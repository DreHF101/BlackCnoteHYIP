<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Invest;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\User;
use Hyiplab\Models\Withdrawal;
use Hyiplab\Models\PromotionalTool;
use Hyiplab\Models\Form;
use Hyiplab\Lib\FormProcessor;
use Hyiplab\Lib\HyipLab;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
        global $user_ID, $wpdb;

        $this->pageTitle = 'Dashboard';

        $user         = get_userdata($user_ID);
        $data['user'] = $user;

        $data['submittedDeposits']  = Deposit::where('status', '!=', 0)->where('user_id', $user->ID)->sum('amount');
        $data['successfulDeposits'] = Deposit::where('status', 1)->where('user_id', $user->ID)->sum('amount');
        $data['requestedDeposits']  = Deposit::where('user_id', $user->ID)->sum('amount');
        $data['initiatedDeposits']  = Deposit::where('status', 0)->where('user_id', $user->ID)->sum('amount');
        $data['pendingDeposits']    = Deposit::where('status', 2)->where('user_id', $user->ID)->sum('amount');
        $data['rejectedDeposits']   = Deposit::where('status', 3)->where('user_id', $user->ID)->sum('amount');

        $data['submittedWithdraws']  = Withdrawal::where('status', '!=', 0)->where('user_id', $user->ID)->sum('amount');
        $data['successfulWithdraws'] = Withdrawal::where('status', 1)->where('user_id', $user->ID)->sum('amount');
        $data['requestedWithdraws']  = Withdrawal::where('user_id', $user->ID)->sum('amount');
        $data['initiatedWithdraws']  = Withdrawal::where('status', 0)->where('user_id', $user->ID)->sum('amount');
        $data['pendingWithdraws']    = Withdrawal::where('status', 2)->where('user_id', $user->ID)->sum('amount');
        $data['rejectedWithdraws']   = Withdrawal::where('status', 3)->where('user_id', $user->ID)->sum('amount');

        $data['invests']               = Invest::where('user_id', $user->ID)->sum('amount');
        $data['completedInvests']      = Invest::where('user_id', $user->ID)->where('status', 0)->sum('amount');
        $data['runningInvests']        = Invest::where('user_id', $user->ID)->where('status', 1)->sum('amount');
        $data['interests']             = Transaction::where('remark', 'interest')->where('user_id', $user->ID)->sum('amount');
        $data['depositWalletInvests']  = Invest::where('user_id', $user->ID)->where('wallet_type', 'deposit_wallet')->where('status', 1)->sum('amount');
        $data['interestWalletInvests'] = Invest::where('user_id', $user->ID)->where('wallet_type', 'interest_wallet')->where('status', 1)->sum('amount');
        $data['deposit_wallet']        = get_user_meta($user_ID, 'hyiplab_deposit_wallet', true);
        $data['interest_wallet']       = get_user_meta($user_ID, 'hyiplab_interest_wallet', true);

        $kv                            = get_user_meta($user->ID, 'hyiplab_kyc', true);
        $kycData                       = get_user_meta($user->ID, 'hyiplab_kyc_data', true);
        $rejectReason                  = get_user_meta($user->ID, 'hyiplab_kyc_reject_reason', true) ?? '';

        $kyc['kycData']                = $kycData;
        $kyc['rejectReason']           = $rejectReason;
        $kyc['kv']                     = $kv;

        $data['kyc']                   = (Object) $kyc;
        $data['isHoliday']             = HyipLab::isHoliDay(hyiplab_date()->toDateTime(), get_option('hyiplab_off_days'));
        $data['nextWorkingDay']        = hyiplab_date()->toDateTime();
        if ($data['isHoliday']) {
            $data['nextWorkingDay']    = HyipLab::nextWorkingDay(24);
            $data['nextWorkingDay']    = Carbon::parse($data['nextWorkingDay'])->toDateString();
        }

        $table_prefix  = $wpdb->base_prefix;
        $date          = hyiplab_date()->subDays(30)->toDate();
        $data['chart'] = Transaction::selectRaw("select SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date from " . $table_prefix . "hyiplab_transactions where `remark` = 'interest' and `created_at` >= '" . $date . "' and `user_id` = " . $user->ID . " group by `date` order by `created_at` asc");
        return $this->view('user/dashboard', compact('data'));
    }

    public function inactive()
    {
        global $user_ID;
        $this->pageTitle = 'Inactive';
        if (get_user_meta($user_ID, 'hyiplab_user_active', true) != 0) {
            hyiplab_redirect(hyiplab_route_link('user.home'));
        }
        return $this->view('user/inactive');
    }

    public function transferBalance()
    {
        global $user_ID;
        if (!get_option('hyiplab_balance_transfer')) {
            hyiplab_abort(404);
        }
        $isKycEnable = get_option('hyiplab_kyc');
        if(
            $isKycEnable && (get_user_meta($user_ID, 'hyiplab_kyc', true) == 0 ) ||
            $isKycEnable &&(get_user_meta($user_ID, 'hyiplab_kyc', true) == '') ||
            $isKycEnable && (get_user_meta($user_ID, 'hyiplab_kyc', true) == 3)
            ) {
            $notify[] = ['error', 'Please verify your KYC first.'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.kyc.form'));
        }
        if(get_user_meta($user_ID, 'hyiplab_kyc', true) == 2) {
            $notify[] = ['warning', 'Please wait for your KYC approval to withdraw.'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.kyc.form'));
        }


        $this->pageTitle = "Balance Transfer";
        $user = get_userdata($user_ID);
        $this->view('user/balance_transfer', compact('user'));
    }

    public function transferBalanceSubmit()
    {
        global $user_ID;
        if (!get_option('hyiplab_balance_transfer')) {
            hyiplab_abort(404);
        }

        $request = new Request();
        $request->validate([
            'username' => 'required',
            'amount'   => 'required|numeric',
            'wallet'   => 'required|in:deposit_wallet,interest_wallet',
        ]);

        $user = get_userdata($user_ID);

        if ($user->user_login == $request->username) {
            $notify[] = ['error', 'You cannot transfer balance to your own account'];
            hyiplab_back($notify);
        }

        $receiver = User::where('user_login', $request->username)->first();
        if (!$receiver) {
            $notify[] = ['error', 'Oops! Receiver not found'];
            hyiplab_back($notify);
        }

        $charge      = get_option('hyiplab_balance_transfer_fixed_charge') + ($request->amount * get_option('hyiplab_balance_transfer_percent_charge')) / 100;
        $afterCharge = $request->amount + $charge;
        $wallet      = $request->wallet;

        $userBalance = hyiplab_balance($user->ID, $wallet);

        if ($userBalance < $afterCharge) {
            $notify[] = ['error', 'You have no sufficient balance to this wallet'];
            hyiplab_back($notify);
        }

        $userAfterBalance = $userBalance - $afterCharge;
        update_user_meta($user->ID, 'hyiplab_'.$wallet, $userAfterBalance);

        $transaction               = new Transaction();
        $transaction->user_id      = $user->ID;
        $transaction->amount       = hyiplab_get_amount($afterCharge);
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->trx          = hyiplab_trx();
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'balance_transfer';
        $transaction->details      = 'Balance transfer to ' . $receiver->user_login;
        $transaction->post_balance = hyiplab_get_amount($userAfterBalance);
        $transaction->created_at   = current_time('mysql');
        $transaction->save();

        $receiverBalance = hyiplab_balance($receiver->ID, 'deposit_wallet');
        $receiverAfterBalance = $userBalance + $request->amount;
        update_user_meta($receiver->ID, 'hyiplab_deposit_wallet', $receiverAfterBalance);

        $transaction               = new Transaction();
        $transaction->user_id      = $receiver->ID;
        $transaction->amount       = hyiplab_get_amount($request->amount);
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->trx          = hyiplab_trx();
        $transaction->wallet_type  = 'deposit_wallet';
        $transaction->remark       = 'balance_transfer';
        $transaction->details      = 'Balance received from ' . $user->user_login;
        $transaction->post_balance = hyiplab_get_amount($receiverAfterBalance);
        $transaction->created_at   = current_time('mysql');
        $transaction->save();

        $notify[] = ['success', 'Balance transferred successfully'];
        hyiplab_back($notify);
    }

    public function promotionalBanner(){

        $this->pageTitle = 'Promotional Banner';
        $banners = PromotionalTool::orderBy('id', 'ASC')->get();

        $this->view('user/promotional_banner', compact('banners'));
    }

    public function kycForm()
    {
        if(!get_option('hyiplab_kyc')) {
            hyiplab_abort(404);
        }
        $user = hyiplab_auth()->user;
        $kyc  = get_user_meta($user->ID, 'hyiplab_kyc', true);

        if (@$kyc == 2) {
            $notify[] = ['error', 'Your KYC is under review'];
            hyiplab_back($notify);
        }
        if (@$kyc == 1) {
            $notify[] = ['success', 'You are already KYC verified'];
            hyiplab_back($notify);
        }
        $pageTitle = 'KYC Form';
        $this->pageTitle = $pageTitle;
        $form      = Form::where('act', 'kyc_form')->first();
        if (!$form) {
            $notify[] = ['error', 'KYC form not found'];
            hyiplab_back($notify);
        }
        $this->view('user/kyc/form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        if(!get_option('hyiplab_kyc')) {
            hyiplab_abort(404);
        }
        $this->pageTitle = 'KYC Data';
        $user      = hyiplab_auth()->user;
        $kyc       = get_user_meta($user->ID, 'hyiplab_kyc', true);
        $kycData   = get_user_meta($user->ID, 'hyiplab_kyc_data', true);
        $rejectReason = get_user_meta($user->ID, 'hyiplab_kyc_reject_reason', true) ?? '';
        $this->view('user/kyc/index', compact('kycData', 'kyc', 'user', 'rejectReason'));
    }

    public function kycSubmit()
    {
        $request        = new Request();
        $form           = Form::where('act', 'kyc_form')->first();
        $formData       = json_decode(json_encode(maybe_unserialize($form->form_data)));

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);

        $userData       = $formProcessor->processFormData($request, $formData);
        $user           = hyiplab_auth()->user;

        update_user_meta($user->ID, 'hyiplab_kyc', 2);
        update_user_meta($user->ID, 'hyiplab_kyc_data', $userData);

        $notify[] = ['success', 'KYC data submitted successfully'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.kyc.data'));

    }
    
    public function downloadKycAttachment()
    {
        $request   = new Request();
        $user      = hyiplab_auth()->user;
        $file      = hyiplab_decrypt($request->file);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $fullpath = hyiplab_file_path('verify').'/' . $file;
        $title     = hyiplab_title_to_key(get_bloginfo('name')) . '_' . $user->user_login . '_kyc_attachment.' . $extension;
        $mimetype  = mime_content_type($file);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        ob_clean();
        flush();
        return readfile($fullpath);
    }

}
