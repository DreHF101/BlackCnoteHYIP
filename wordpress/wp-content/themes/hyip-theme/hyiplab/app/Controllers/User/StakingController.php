<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Staking;
use Hyiplab\Models\StakingInvest;
use Hyiplab\Models\Transaction;

class StakingController extends Controller
{
    public function staking(){
        
        global $user_ID;
        if (!get_option('hyiplab_staking', true)) {
            hyiplab_abort(404);
        }

        $this->pageTitle  = 'My Staking';
        $stakings   = Staking::orderBy('id', 'desc')->where('status', 1)->get();
        $myStakings = StakingInvest::where('user_id', $user_ID)->orderBy('id', 'desc')->paginate(hyiplab_paginate());

        $this->view('user/staking/index', compact('stakings', 'myStakings'));
    }

    public function stakingSave(){
        $request = new Request();
        $amount = $request->amount;

        if (!get_option('hyiplab_staking')) {
            hyiplab_abort(404);
        }

        $min = get_option('hyiplab_staking_min_amount');
        $max = get_option('hyiplab_staking_max_amount');

        $request->validate([
            'duration' => 'required|integer|min:1',
            'amount'   => "required|numeric",
            'wallet'   => 'required|in:deposit_wallet,interest_wallet',
        ]);

        if(($amount < $min || $amount > $max)){
            $notify[] = ['error', 'Amount must be between '. hyiplab_currency('sym') . $min . ' and '. hyiplab_currency('sym') . $max];
            hyiplab_back($notify);
        }

        $user   = hyiplab_auth()->user;
        $wallet = $request->wallet;

        $deposit_wallet_amount = get_user_meta($user->ID, 'hyiplab_deposit_wallet', true);
        $interest_wallet_amount = get_user_meta($user->ID, 'hyiplab_interest_wallet', true);
        
        if (($wallet == 'deposit_wallet' && $deposit_wallet_amount < $amount) || ($wallet == 'interest_wallet' && $interest_wallet_amount < $amount)) {
            $notify[] = ['error', 'You\'ve no sufficient balance'];
            hyiplab_back($notify);
        }
        
        $staking  = Staking::where('status', 1)->findOrFail($request->duration);
        $interest = $amount * $staking->interest_percent / 100;
        
        $stakingInvest                = new StakingInvest();
        $stakingInvest->user_id       = $user->ID;
        $stakingInvest->staking_id    = $staking->id;
        $stakingInvest->invest_amount = $request->amount;
        $stakingInvest->interest      = $interest;
        $stakingInvest->end_at        = hyiplab_date()->addDays($staking->days)->toDateTime();
        $stakingInvest->status        = 1;
        $stakingInvest->created_at    = current_time('mysql');
        $stakingInvest->updated_at    = current_time('mysql');
        $stakingInvest->save();
        

        $afterBalance = hyiplab_balance($user->ID, $wallet) - $request->amount;
        update_user_meta($user->ID,"hyiplab_$wallet",$afterBalance);

        $trx                       = hyiplab_trx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user->ID;
        $transaction->amount       = $amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = esc_html__('Staking investment on ', HYIPLAB_PLUGIN_NAME) . $staking->days . ' days' . ' ' . $staking->interest_percent . '%';
        $transaction->trx          = $trx;
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'staking_invest';
        $transaction->created_at   = current_time('mysql');
        $transaction->save();

        $notify[] = ['success', 'Staking investment added successfully'];
        hyiplab_back($notify);
    }

}