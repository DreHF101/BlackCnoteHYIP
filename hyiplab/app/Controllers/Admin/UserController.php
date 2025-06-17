<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\SupportTicket;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\User;
use Hyiplab\Models\Withdrawal;
use Hyiplab\Models\ScheduleInvest;
use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Plan;

class UserController extends Controller
{
    public function allUsers()
    {
        $request   = new Request();
        $pageTitle = "All Users";
        if ($request->search) {
            $users = User::where('user_login', urldecode($request->search))->orWhere('user_email', '=', urldecode($request->search))->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        } else {
            $users = User::orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        }
        $this->view('admin/users/list', compact('pageTitle', 'users'));
    }
    public function activeUsers()
    {
        global $wpdb;
        $request   = new Request();
        $pageTitle = "Active Users";
        if ($request->search) {
            $users = User::where('user_login', urldecode($request->search))->orWhere('user_email', '=', urldecode($request->search))->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        } else {
            $query = $wpdb->prepare(
                "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
                'hyiplab_user_active',
                '1'
            );
            $user_ids = $wpdb->get_col($query);
            if(empty($user_ids)){
                $user_ids = [0];
            }
            $users = User::whereIn('ID', $user_ids)->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        }
        $this->view('admin/users/list', compact('pageTitle', 'users'));
    }
    public function bannedUsers()
    {
        global $wpdb;
        $request   = new Request();
        $pageTitle = "Banned Users";
        if ($request->search) {
            $users = User::where('user_login', urldecode($request->search))->orWhere('user_email', '=', urldecode($request->search))->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        } else {
            $query = $wpdb->prepare(
                "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
                'hyiplab_user_active',
                '0'
            );
            $user_ids = $wpdb->get_col($query);
            if(empty($user_ids)){
                $user_ids = [0];
            }
            $users = User::whereIn('ID', $user_ids)->orderBy('id', 'DESC')->paginate(hyiplab_paginate());

        }
        $this->view('admin/users/list', compact('pageTitle', 'users'));
    }
    public function kycPendingUsers()
    {
        global $wpdb;
        $request   = new Request();
        $pageTitle = "KYC Pending Users";
        if ($request->search) {
            $users = User::where('user_login', urldecode($request->search))->orWhere('user_email', '=', urldecode($request->search))->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        } else {
            $query = $wpdb->prepare(
                "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
                'hyiplab_kyc',
                '2'
            );
            $user_ids = $wpdb->get_col($query);
            if(empty($user_ids)){
                $user_ids = [0];
            }
            $users = User::whereIn('ID', $user_ids)->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        }
        $this->view('admin/users/list', compact('pageTitle', 'users'));
    }

    public function kycUnverifiedUsers()
    {
        global $wpdb;
        $request   = new Request();
        $pageTitle = "KYC Unverified Users";
        if ($request->search) {
            $users = User::where('user_login', urldecode($request->search))->orWhere('user_email', '=', urldecode($request->search))->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        } else {
            $query = $wpdb->prepare(
                "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value != %s",
                'hyiplab_kyc',
                '1'
            );
            $user_ids = $wpdb->get_col($query);
            if(empty($user_ids)){
                $user_ids = [0];
            }
            $users = User::whereIn('ID', $user_ids)->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        }
        $this->view('admin/users/list', compact('pageTitle', 'users'));
    }

    public function userDetail()
    {
        $request = new Request();
        $user    = User::find($request->id);
        if (!$user) {
            hyiplab_abort(404);
        }
        $pageTitle        = "User Detail - " . $user->user_login;
        $countries        = json_decode(file_get_contents(HYIPLAB_ROOT . 'views/partials/country.json'));
        $totalDeposit     = Deposit::where('user_id', $user->ID)->where('status', 1)->sum('amount');
        $totalWithdrawals = Withdrawal::where('user_id', $user->ID)->where('status', 1)->sum('amount');
        $totalTransaction = Transaction::where('user_id', $user->ID)->count();
        $pendingTicket    = SupportTicket::whereIn('status', [0, 2])->where('user_id', $user->ID)->count();
        $kyc              = get_user_meta($user->ID, 'hyiplab_kyc', true);
        $isBan            = get_user_meta($user->ID, 'hyiplab_user_active', true) == '' || get_user_meta($user->ID, 'hyiplab_user_active', true) == 1 ? 1 : 0;
        $this->view('admin/users/detail', compact('pageTitle', 'user', 'countries', 'totalDeposit', 'totalWithdrawals', 'totalTransaction', 'pendingTicket', 'kyc', 'isBan'));
    }

    public function userUpdate()
    {
        $request = new Request();
        $request->validate([
            'display_name' => 'required',
            'email'        => 'required|email',
            'mobile'       => 'required',
            'country'      => 'required'
        ]);
        $user = User::find($request->id);
        if (!$user) {
            hyiplab_abort(404);
        }

        $countryData  = json_decode(file_get_contents(HYIPLAB_ROOT . 'views/partials/country.json'));
        $countryCode  = $request->country;
        $country      = $countryData->$countryCode->country;
        $dialCode     = $countryData->$countryCode->dial_code;

        $userData = [
            'ID'           => intval($user->ID),
            'display_name' => sanitize_text_field($request->display_name)
        ];

        $user_id = wp_update_user($userData);

        update_user_meta($user_id, 'hyiplab_mobile', sanitize_text_field($dialCode . $request->mobile));
        update_user_meta($user_id, 'hyiplab_country_code', sanitize_text_field($countryCode));
        update_user_meta($user_id, 'hyiplab_country', sanitize_text_field($country));
        update_user_meta($user_id, 'hyiplab_address', sanitize_text_field($request->address));
        update_user_meta($user_id, 'hyiplab_city', sanitize_text_field($request->city));
        update_user_meta($user_id, 'hyiplab_state', sanitize_text_field($request->state));
        update_user_meta($user_id, 'hyiplab_zip', sanitize_text_field($request->zip));

        update_user_meta($user->ID, 'hyiplab_kyc', $request->kyc_verify == 'on' ? 1 : 0);

        if ($request->email_verify) {
            delete_user_meta($user->ID, 'hyiplab_email_verify');
        } else {
            if (!get_user_meta($user->ID, 'hyiplab_email_verify', true)) {
                $verify_email = wp_generate_password(20, false);
                update_user_meta($user->ID, 'hyiplab_email_verify', $verify_email);
                $verify_link = sprintf('%s?email=verify&login=%s&key=%s', hyiplab_route_link('user.login'), rawurlencode($user->user_login), $verify_email);
                hyiplab_notify($user, 'REGISTER', [
                    'verify_link' => esc_url($verify_link)
                ]);
            }
        }

        $notify[] = ['success', 'User details updated successfully'];
        hyiplab_back($notify);
    }

    public function userBan(){
        $request = new Request();
        $user = User::find($request->id);
        if (!$user) {
            hyiplab_abort(404);
        }

        $userStatus = get_user_meta($user->ID, 'hyiplab_user_active', true) == '' || get_user_meta($user->ID, 'hyiplab_user_active', true) == 1 ? 1 : 0;

        if ($userStatus == 1) {
            $request->validate([
                'reason' => 'required|max:255',
            ]);
            update_user_meta($user->ID, 'hyiplab_user_active', 0);
            update_user_meta($user->ID, 'hyiplab_user_ban_reason', $request->reason);
            $notify[]         = ['success', 'User banned successfully'];
            
        } else {
            update_user_meta($user->ID, 'hyiplab_user_active', 1);
            update_user_meta($user->ID, 'hyiplab_user_ban_reason', null);
            $notify[]         = ['success', 'User unbanned successfully'];
        }

        return hyiplab_back($notify);

    }

    public function KycData(){
        $request = new Request();

        $pageTitle = "KYC Data";

        $kyc = get_user_meta($request->id, 'hyiplab_kyc', true);
        if (!$kyc) {
            $notify[] = ['warning', 'KYC data not submitted yet'];
            hyiplab_back($notify);
        }
        $kycData = get_user_meta($request->id, 'hyiplab_kyc_data', true);
        $user = get_userdata($request->id);
        $rejectReason = get_user_meta($request->id, 'hyiplab_kyc_reject_reason', true) ?? '';

        $this->view('admin/users/kyc-data', compact('pageTitle','kycData', 'kyc', 'user', 'rejectReason'));
    }

    public function KycApprove(){
        $request = new Request();
        $kyc = get_user_meta($request->id, 'hyiplab_kyc', true);
        if (!$kyc) {
            hyiplab_abort(404);
        }
        update_user_meta($request->id, 'hyiplab_kyc', 1);

        $notify[] = ['success', 'KYC approved successfully'];
        hyiplab_back($notify);
    }

    public function KycReject(){
        $request = new Request();
        $kyc = get_user_meta($request->id, 'hyiplab_kyc', true);
        $kycData = get_user_meta($request->id, 'hyiplab_kyc_data', true);
        if (!$kyc) {
            hyiplab_abort(404);
        }
        foreach ($kycData as $data) {
            if ($data['type'] == 'file') {
                hyiplab_file_manager()->removeFile(hyiplab_file_path('verify') . '/' . $kycData->value);
            }
        }

        update_user_meta($request->id, 'hyiplab_kyc', 3);
        update_user_meta($request->id, 'hyiplab_kyc_reject_reason', $request->reject_reason);

        $notify[] = ['success', 'KYC rejected successfully'];
        hyiplab_back($notify);
    }

    public function userAddSubBalance()
    {
        $request = new Request();
        $request->validate([
            'amount'      => 'required|numeric',
            'act'         => 'required|in:add,sub',
            'wallet_type' => 'required|in:deposit_wallet,interest_wallet',
            'remark'      => 'required|max:255',
        ]);

        $user   = User::findOrFail($request->id);
        $amount = $request->amount;
        $wallet = $request->wallet_type;
        $trx    = hyiplab_trx();

        $transaction = new Transaction();

        if ($request->act == 'add') {

            $afterBalance = hyiplab_balance($user->ID, $wallet) + $amount;
            update_user_meta($user->ID,"hyiplab_$wallet", $afterBalance);

            $transaction->trx_type = '+';
            $transaction->remark   = 'balance_add';

            $notifyTemplate = 'BAL_ADD';

            $notify[] = ['success', hyiplab_currency('sym') . $amount . ' added successfully'];

        } else {
            $interest_wallet = get_user_meta($user->ID, 'hyiplab_interest_wallet', true);
            $deposit_wallet  = get_user_meta($user->ID, 'hyiplab_deposit_wallet', true);

            if (($wallet == 'interest_wallet' && $amount > $interest_wallet) || ($wallet == 'deposit_wallet' && $amount > $deposit_wallet)) {
                $notify[] = ['error', $user->username . ' doesn\'t have sufficient balance.'];
                return hyiplab_back($notify);
            }

            $afterBalance = hyiplab_balance($user->ID, $wallet) - $amount;
            update_user_meta($user->ID,"hyiplab_$wallet", $afterBalance);

            $transaction->trx_type = '-';
            $transaction->remark   = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $notify[]       = ['success', hyiplab_currency('sym') . $amount . ' subtracted successfully'];
        }

        $transaction->user_id      = $user->ID;
        $transaction->amount       = $amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge       = 0;
        $transaction->trx          = $trx;
        $transaction->details      = $request->remark;
        $transaction->wallet_type  = $wallet;
        $transaction->created_at   = current_time('mysql');
        $transaction->save();

        hyiplab_notify($user, $notifyTemplate, [
            'trx'          => $trx,
            'amount'       => hyiplab_show_amount($amount),
            'remark'       => $request->remark,
            'post_balance' => hyiplab_show_amount($afterBalance),
        ]);

        return hyiplab_back($notify);
    }




}
