<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\Withdrawal;
use Hyiplab\Models\WithdrawMethod;

class WithdrawalController extends Controller
{

    public function log()
    {
        $request = new Request();
        if ($request->username) {
            $user = get_user_by('login', $request->username);
            if (!$user) {
                abort(404);
            }
            $pageTitle   = "Withdrawals Log - " . $user->user_login;
            $withdrawals = Withdrawal::where('user_id', $user->ID)->where('status', '!=', 0)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        } else {
            $pageTitle   = "Withdrawals Log";
            $withdrawals = Withdrawal::where('status', '!=', 0)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        }
        return $this->view('admin/withdraw/list', compact('pageTitle', 'withdrawals'));
    }

    public function pending()
    {
        $pageTitle   = "Pending Withdrawals";
        $withdrawals = Withdrawal::where('status', 2)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        return $this->view('admin/withdraw/list', compact('pageTitle', 'withdrawals'));
    }

    public function approved()
    {
        $pageTitle   = "Approved Withdrawals";
        $withdrawals = Withdrawal::where('status', 1)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        return $this->view('admin/withdraw/list', compact('pageTitle', 'withdrawals'));
    }

    public function rejected()
    {
        $pageTitle   = "Rejected Withdrawals";
        $withdrawals = Withdrawal::where('status', 3)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        return $this->view('admin/withdraw/list', compact('pageTitle', 'withdrawals'));
    }

    public function detail()
    {
        $request    = new Request();
        $withdrawal = Withdrawal::where('id', $request->id)->where('status', '!=', 0)->firstOrFail();
        $user      = get_userdata($withdrawal->user_id);
        $method    = WithdrawMethod::where('id', $withdrawal->method_id)->first();
        $pageTitle = $user->user_login . esc_html__(' Withdraw Requested ', HYIPLAB_PLUGIN_NAME) . hyiplab_show_amount($withdrawal->amount) . ' ' . hyiplab_currency('text');
        $this->view('admin/withdraw/detail', compact('pageTitle', 'withdrawal', 'method', 'user'));
    }

    public function reject()
    {
        $request = new Request();
        $request->validate([
            'id'      => 'required|integer',
            'details' => 'required'
        ]);

        $withdraw = Withdrawal::where('id', $request->id)->where('status', 2)->firstOrFail();

        $method                 = WithdrawMethod::where('id', $withdraw->method_id)->first();
        $data['status']         = 3;
        $data['admin_feedback'] = sanitize_text_field($request->details);
        $data['updated_at']     = current_time('mysql');
        Withdrawal::where('id', $request->id)->update($data);

        $afterBalance = hyiplab_balance($withdraw->user_id, 'interest_wallet') + $withdraw->amount;
        update_user_meta($withdraw->user_id, "hyiplab_interest_wallet", $afterBalance);

        $transaction               = new Transaction();
        $transaction->user_id      = $withdraw->user_id;
        $transaction->amount       = $withdraw->amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->remark       = 'withdraw_reject';
        $transaction->details      = hyiplab_show_amount($withdraw->amount) . ' ' . hyiplab_currency('text') . esc_html__(' Refunded from withdrawal rejection', HYIPLAB_PLUGIN_NAME);
        $transaction->trx          = $withdraw->trx;
        $transaction->wallet_type  = 'interest_wallet';
        $transaction->created_at   = current_time('mysql');
        $transaction->save();

        $user = get_userdata($withdraw->user_id);

        hyiplab_notify($user, 'WITHDRAW_REJECT', [
            'method_name'     => $method->name,
            'method_currency' => $withdraw->currency,
            'method_amount'   => hyiplab_show_amount($withdraw->final_amount),
            'amount'          => hyiplab_show_amount($withdraw->amount),
            'charge'          => hyiplab_show_amount($withdraw->charge),
            'rate'            => hyiplab_show_amount($withdraw->rate),
            'trx'             => $withdraw->trx,
            'post_balance'    => hyiplab_show_amount($afterBalance),
            'admin_details'   => sanitize_text_field($request->details),
        ]);
        $notify[] = ['success', 'Withdrawal rejected successfully'];
        hyiplab_back($notify);
    }

    public function approve()
    {
        $request = new Request();
        $request->validate([
            'id'      => 'required|integer',
            'details' => 'required'
        ]);

        $withdraw = Withdrawal::where('id', $request->id)->where('status', 2)->firstOrFail();

        $method = WithdrawMethod::where('id', $withdraw->method_id)->first();

        $data['status']         = 1;
        $data['admin_feedback'] = sanitize_text_field($request->details);
        $data['updated_at']     = current_time('mysql');
        Withdrawal::where('id', $request->id)->update($data);

        $user = get_userdata($withdraw->user_id);

        hyiplab_notify($user, 'WITHDRAW_APPROVE', [
            'method_name'     => $method->name,
            'method_currency' => $withdraw->currency,
            'method_amount'   => hyiplab_show_amount($withdraw->final_amount),
            'amount'          => hyiplab_show_amount($withdraw->amount),
            'charge'          => hyiplab_show_amount($withdraw->charge),
            'rate'            => hyiplab_show_amount($withdraw->rate),
            'trx'             => $withdraw->trx,
            'admin_details'   => sanitize_text_field($request->details),
        ]);

        $notify[] = ['success', 'Withdrawal approved successfully'];
        hyiplab_back($notify);
    }
}
