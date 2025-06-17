<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\FormProcessor;
use Hyiplab\Models\Form;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\Withdrawal;
use Hyiplab\Models\WithdrawMethod;
use Hyiplab\Lib\HyipLab;
use Carbon\Carbon;

class WithdrawController extends Controller
{
    public function index()
    {
        $user = hyiplab_auth()->user;
        $isKycEnable = get_option('hyiplab_kyc');
        if(
            ($isKycEnable && (get_user_meta($user->ID, 'hyiplab_kyc', true) == 0 )) ||
            ($isKycEnable && (get_user_meta($user->ID, 'hyiplab_kyc', true) == '')) ||
            ($isKycEnable && (get_user_meta($user->ID, 'hyiplab_kyc', true) == 3))
            ) {
            $notify[] = ['error', 'Please verify your KYC first.'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.kyc.form'));
        }
        if($isKycEnable && get_user_meta($user->ID, 'hyiplab_kyc', true) == 2) {
            $notify[] = ['error', 'Please wait for your KYC approval to withdraw.'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.kyc.form'));
        }

        $this->pageTitle = 'Withdraw';
        $methods         = WithdrawMethod::where('status', 1)->get();

        $isHoliday = HyipLab::isHoliDay(hyiplab_date()->now(), get_option('hyiplab_off_days'));

        $nextWorkingDay = hyiplab_date()->toDateTime();

        if ($isHoliday && !get_option('hyiplab_withdrawal_on_holiday')) {
            $nextWorkingDay = HyipLab::nextWorkingDay(24);
            $nextWorkingDay = Carbon::parse($nextWorkingDay)->toDateString();
        }

        $this->view('user/withdraw/methods', compact('methods', 'isHoliday', 'nextWorkingDay'));
    }

    public function insert()
    {
        $request = new Request();

        $isHoliday = HyipLab::isHoliDay(hyiplab_date()->now(), get_option('hyiplab_off_days'));
        if ($isHoliday && !get_option('hyiplab_withdrawal_on_holiday', true)) {
            $notify[] = ['error', 'Today is holiday. You\'re unable to withdraw today'];
            return hyiplab_back($notify);
        }

        $request->validate([
            'method_code' => 'required',
            'amount'      => 'required|numeric'
        ]);

        $amount = sanitize_text_field($request->amount);
        $method = WithdrawMethod::where('id', sanitize_text_field($request->method_code))->where('status', 1)->first();

        $user = hyiplab_auth()->user;

        if ($amount < $method->min_limit) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount.'];
            hyiplab_back($notify);
        }

        if ($amount > $method->max_limit) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount.'];
            hyiplab_back($notify);
        }

        if ($amount > hyiplab_balance($user->ID, 'interest_wallet')) {
            $notify[] = ['error', 'You do not have sufficient balance for withdraw.'];
            hyiplab_back($notify);
        }

        $charge      = $method->fixed_charge + ($amount * $method->percent_charge / 100);
        $afterCharge = $amount - $charge;
        $finalAmount = $afterCharge * $method->rate;

        $trx = hyiplab_trx();
        $withdraw               = new Withdrawal();
        $withdraw->method_id    = $method->id;            // wallet method ID
        $withdraw->user_id      = $user->ID;
        $withdraw->amount       = $amount;
        $withdraw->currency     = $method->currency;
        $withdraw->rate         = $method->rate;
        $withdraw->charge       = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx          = $trx;
        $withdraw->created_at   = current_time('mysql');
        $withdraw->updated_at   = current_time('mysql');
        $withdraw->save();

        hyiplab_session()->put('trx', $trx);
        hyiplab_redirect(hyiplab_route_link('user.withdraw.preview'));
    }

    public function preview()
    {
        $this->pageTitle = 'Withdraw Preview';
        $trx             = hyiplab_session()->get('trx');
        $withdraw        = Withdrawal::where('trx', $trx)->where('status', 0)->orderBy('id', 'desc')->first();
        if (!$withdraw) {
            $notify[] = ['error', 'Withdraw not found'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.withdraw.preview'));
        }
        $method = WithdrawMethod::find($withdraw->method_id);
        $this->view('user/withdraw/preview', compact('withdraw', 'method'));
    }

    public function submit()
    {
        $request  = new Request();
        $trx      = hyiplab_session()->get('trx');
        $withdraw = Withdrawal::where('trx', $trx)->where('status', 0)->orderBy('id', 'desc')->first();
        if (!$withdraw) {
            $notify[] = ['error', 'Withdraw not found'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.withdraw.index'));
        }

        $method        = WithdrawMethod::where('id', $withdraw->method_id)->first();
        $form          = Form::find($method->form_id);
        $formData      = json_decode(json_encode(maybe_unserialize($form->form_data)));
        $formProcessor = new FormProcessor();
        $userData      = $formProcessor->processFormData($request, $formData);

        $user = get_userdata($withdraw->user_id);

        if ($withdraw->amount > hyiplab_balance($user->ID, 'interest_wallet')) {
            $notify[] = ['error', 'Your request amount is larger then your current balance.'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.withdraw.index'));
        }

        $data['status']               = 2;
        $data['withdraw_information'] = maybe_serialize($userData);
        Withdrawal::where('id', $withdraw->id)->update($data);

        $afterBalance = hyiplab_balance($withdraw->user_id, 'interest_wallet') - $withdraw->amount;
        update_user_meta($withdraw->user_id, "hyiplab_interest_wallet", $afterBalance);

        $transaction               = new Transaction();
        $transaction->user_id      = $withdraw->user_id;
        $transaction->amount       = $withdraw->amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge       = $withdraw->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = hyiplab_show_amount($withdraw->final_amount) . ' ' . $withdraw->currency . esc_html__(' Withdraw Via ', HYIPLAB_PLUGIN_NAME) . $method->name;
        $transaction->trx          = $withdraw->trx;
        $transaction->wallet_type  = "interest_wallet";
        $transaction->remark       = 'withdraw';
        $transaction->created_at   = current_time('mysql');
        $transaction->save();

        hyiplab_notify($user, 'WITHDRAW_REQUEST', [
            'method_name'     => $method->name,
            'method_currency' => $withdraw->currency,
            'method_amount'   => hyiplab_show_amount($withdraw->final_amount),
            'amount'          => hyiplab_show_amount($withdraw->amount),
            'charge'          => hyiplab_show_amount($withdraw->charge),
            'rate'            => hyiplab_show_amount($withdraw->rate),
            'trx'             => $withdraw->trx,
            'post_balance'    => hyiplab_show_amount($afterBalance),
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.withdraw.history'));
    }

    public function history()
    {
        global $user_ID;
        $this->pageTitle = 'Withdraw History';
        $withdraws       = Withdrawal::where('user_id', $user_ID)->where('status', '!=', 0)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('user/withdraw/history', compact('withdraws'));
    }
}
