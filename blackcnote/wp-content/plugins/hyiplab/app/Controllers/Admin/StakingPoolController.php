<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Staking;
use Hyiplab\Models\StakingInvest;
use Hyiplab\Models\User;
use Hyiplab\Models\Pool;
use Hyiplab\Models\PoolInvest;
use Hyiplab\Models\Transaction;

class StakingPoolController extends Controller
{
    public function staking()
    {
        $pageTitle = 'Manage Staking';
        $stakings  = Staking::orderBy('days')->paginate(hyiplab_paginate());
        $this->view('admin/staking/time', compact('pageTitle', 'stakings'));
    }

    public function store()
    {
        $request = new Request();
        $request->validate([
            'duration'        => 'required|integer',
            'interest_amount' => 'required|numeric',
        ]);
        
        $id = $request->id;

        if ($id) {
            $staking      = Staking::findOrFail($id);
            $notification = 'updated';
        } else {
            $staking      = new Staking();
            $notification = 'added';
        }
        
        $staking->days             = $request->duration;
        $staking->interest_percent = $request->interest_amount;
        $staking->created_at       = hyiplab_date()->now();
        $staking->updated_at       = hyiplab_date()->now();
        $staking->save();

        $notify[] = ['success', 'Staking ' . $notification . ' successfully'];
        hyiplab_back($notify);
    }

    public function status()
    {
        $request         = new Request();
        $staking         = Staking::findOrFail($request->id);
        $staking->status = $staking->status ? 0 : 1;
        $staking->save();

        $notify[] = ['success', "Staking status changed successfully"];
        hyiplab_back($notify);
    }

    public function stakingInvest()
    {
        $request = new Request();
        $pageTitle      = 'All Staking Invest';

        if ($request->search) {
            $users = User::where('user_login', urldecode($request->search))->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        } else {
            $users = User::orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        }
        $stakingInvests = StakingInvest::orderBy('id', 'desc')->paginate(hyiplab_paginate());

        $this->view('admin/staking/invest', compact('pageTitle', 'stakingInvests'));
    }


    public function pool()
    {
        $pageTitle = 'Manage Pool';
        $pools     = Pool::orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('admin/pool/index', compact('pageTitle', 'pools'));
    }

    public function savePool()
    {
        $request = new Request;
        $request->validate([
            'name'           => 'required',
            'amount'         => 'required|numeric',
            'interest_range' => 'required',
            'start_date'     => 'required',
            'end_date'       => 'required',
        ]);

        $id = $request->id;
        if ($id) {
            $pool         = Pool::findOrFail($id);
            $notification = 'updated';
            if($pool->share_interest){
                $notify[] = ['error','Pool interest already dispatched! Unable to update.'];
                hyiplab_back($notify);
            }
        } else {
            $pool         = new Pool();
            $notification = 'added';
        }

        $pool->name           = $request->name;
        $pool->amount         = $request->amount;
        $pool->interest_range = $request->interest_range;
        $pool->start_date     = $request->start_date;
        $pool->end_date       = $request->end_date;
        $pool->created_at     = hyiplab_date()->now();
        $pool->updated_at     = hyiplab_date()->now();
        $pool->save();

        $notify[] = ['success', "Pool $notification successfully"];
        hyiplab_back($notify);
    }

    public function poolStatus()
    {
        $request      = new Request();
        $pool         = Pool::findOrFail($request->id);
        $pool->status = $pool->status ? 0 : 1;
        $pool->save();

        $notify[] = ['success', "Pool status changed successfully"];
        hyiplab_back($notify);
    }

    public function dispatchPool()
    {
        $request = new Request();
        $request->validate([
            'pool_id' => 'required|integer',
            'amount'  => 'required|numeric',
        ]);

        $pool = Pool::findOrFail($request->pool_id);
        $endDate = hyiplab_date()->parse($pool->end_date)->toDateTime('Y-m-d H:i:s');
        if ($endDate > hyiplab_date()->now()) {
            $notify[] = ['error', 'You can dispatch interest after end date'];
            hyiplab_back($notify);
        }

        if ($pool->share_interest == 1) {
            $notify[] = ['error', 'Interest already dispatched for this pool'];
            hyiplab_back($notify);
        }

        $pool->share_interest = 1;
        $pool->interest       = $request->amount;
        $pool->save();

        $poolInvest = PoolInvest::where('pool_id', $pool->id)->where('status', 1)->get();

        foreach ($poolInvest as $invest) {
            $investAmount  = $invest->invest_amount;
            $interest      = $investAmount * $request->amount / 100;
            $totalInterest = $investAmount + $interest;

            $userID = $invest->user_id;
            $afterBalance = hyiplab_balance($userID, 'interest_wallet') + $totalInterest;
            update_user_meta($userID, "hyiplab_interest_wallet", $afterBalance);

            PoolInvest::where('id', $invest->id)->update(['status' => 2]);

            $trx          = hyiplab_trx();
            $transactions = [
                'user_id'      => $userID,
                'invest_id'    => $invest->id,
                'amount'       => $totalInterest,
                'post_balance' => $afterBalance,
                'charge'       => 0,
                'trx_type'     => '+',
                'details'      => hyiplab_show_amount($totalInterest) . ' ' . hyiplab_currency('text') . esc_html__(' interest from Pool investment', HYIPLAB_PLUGIN_NAME),
                'trx'          => $trx,
                'wallet_type'  => 'interest_wallet',
                'remark'       => 'pool_invest_return',
                'created_at'   => current_time('mysql')
            ];
            Transaction::insert($transactions);

            hyiplab_notify($userID, 'INTEREST', [
                'trx'          => $trx,
                'amount'       => hyiplab_show_amount($totalInterest),
                'plan_name'    => esc_html($pool->name),
                'post_balance' => hyiplab_show_amount($afterBalance),
            ]);

        }

        $notify[] = ['success', 'Pool dispatched successfully'];
        hyiplab_back($notify);
    }

    public function poolInvest()
    {
        $pageTitle   = 'All Pool Invest';
        $poolInvests = PoolInvest::orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('admin/pool/invest', compact('pageTitle', 'poolInvests'));
    }


}
