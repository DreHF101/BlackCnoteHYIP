<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Staking;
use Hyiplab\Models\StakingInvest;
use Hyiplab\Services\StakingService;

class StakingController extends Controller
{
    protected $stakingService;

    public function __construct()
    {
        parent::__construct();
        if (!get_option('hyiplab_staking')) {
            hyiplab_abort(404);
        }
        $this->stakingService = new StakingService();
    }

    public function staking()
    {
        $this->pageTitle = 'My Staking';
        $stakings = Staking::where('status', 1)->orderBy('id', 'desc')->get();
        $myStakings = StakingInvest::where('user_id', hyiplab_auth()->user->ID)
            ->orderBy('id', 'desc')
            ->paginate(hyiplab_paginate());

        $this->view('user/staking/index', compact('stakings', 'myStakings'));
    }

    public function stakingSave(Request $request)
    {
        $min = get_option('hyiplab_staking_min_amount', 0);
        $max = get_option('hyiplab_staking_max_amount', 1000000);

        $request->validate([
            'duration' => 'required|integer|exists:hyiplab_stakings,id',
            'amount'   => "required|numeric|min:$min|max:$max",
            'wallet'   => 'required|in:deposit_wallet,interest_wallet',
        ]);

        $user = hyiplab_auth()->user;
        $wallet = $request->wallet;
        $amount = $request->amount;

        if ($amount > hyiplab_balance($user->ID, $wallet)) {
            $notify[] = ['error', 'You do not have sufficient balance.'];
            return hyiplab_back($notify);
        }

        $this->stakingService->createStakingInvestment($user->ID, $request->duration, $amount, $wallet);

        $notify[] = ['success', 'Staking investment successful.'];
        return hyiplab_back($notify);
    }
}