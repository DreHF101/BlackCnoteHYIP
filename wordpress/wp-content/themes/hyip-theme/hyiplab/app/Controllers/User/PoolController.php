<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Pool;
use Hyiplab\Models\PoolInvest;
use Hyiplab\Models\Transaction;

class PoolController extends Controller
{
    public function pool(){

        if(!get_option('hyiplab_pool')){
            hyiplab_abort(404);
        }

        $this->pageTitle = "Pool Plan";
        $pools     = Pool::orderBy('id', 'asc')->where('status', 1)->where('share_interest', 0)->get();

        $this->view('user/pool/index', compact('pools'));
    }

    public function poolInvest(){

        if (!get_option('hyiplab_pool')) {
            hyiplab_abort(404);
        }

        $this->pageTitle = 'My Pool Invests';
        $poolInvests = PoolInvest::where('user_id', hyiplab_auth()->user->ID)->orderBy('id', 'desc')->paginate(hyiplab_paginate());

        $this->view('user/pool/invest', compact('poolInvests'));
    }
    public function poolInvestStore(){
        if (!get_option('hyiplab_pool')) {
            hyiplab_abort(404);
        }
        $request = new Request();
        $request->validate([
            'pool_id'     => 'required|integer',
            'wallet_type' => 'required|in:deposit_wallet,interest_wallet',
            'amount'      => 'required|numeric',
        ]);
        
        $pool   = Pool::where('status', 1)->findOrFail($request->pool_id);
        $user   = hyiplab_auth()->user;
        $wallet = $request->wallet_type;

        if ($pool->start_date <= hyiplab_date()->now()) {
            $notify[] = ['error', 'The investment period for this pool has ended.'];
            hyiplab_back($notify);
        }

        if ($request->amount > $pool->amount - $pool->invested_amount) {
            $notify[] = ['error', 'Pool invest over limit!'];
            hyiplab_back($notify);
        }

        $deposit_wallet = get_user_meta($user->ID, 'hyiplab_deposit_wallet', true);
        $interest_wallet = get_user_meta($user->ID, 'hyiplab_interest_wallet', true);
        
        if($wallet == 'deposit_wallet' && $deposit_wallet < $request->amount || $wallet == 'interest_wallet' && $interest_wallet < $request->amount){
            $notify[] = ['error', 'Insufficient balance!'];
            hyiplab_back($notify);
        }
            
        $poolInvest = PoolInvest::where('user_id', $user->ID)->where('pool_id', $pool->id)->where('status', 1)->first();
        
        if (!$poolInvest) {
            $poolInvest          = new PoolInvest();
            $poolInvest->user_id = $user->id;
            $poolInvest->pool_id = $pool->id;
        }

        $poolInvest->invest_amount += $request->amount;
        $poolInvest->created_at = hyiplab_date()->now();
        $poolInvest->updated_at = hyiplab_date()->now();
        $poolInvest->save();

        $pool->invested_amount += $request->amount;
        $pool->save();

        $afterBalance = hyiplab_balance($user->ID, $wallet) - $request->amount;
        update_user_meta($user->ID,"hyiplab_$wallet",$afterBalance);

        $trx                       = hyiplab_trx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user->ID;
        $transaction->amount       = $request->amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = esc_html__('Pool investment on ', HYIPLAB_PLUGIN_NAME) . $pool->name;
        $transaction->trx          = $trx;
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'pool_invest';
        $transaction->created_at   = current_time('mysql');
        $transaction->save();

        $notify[] = ['success', 'Pool investment added successfully'];
        hyiplab_back($notify);

    }
}