<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Pool;
use Hyiplab\Models\PoolInvest;
use Hyiplab\Models\Transaction;
use Hyiplab\Services\PoolService;

class PoolController extends Controller
{
    protected $poolService;

    public function __construct()
    {
        parent::__construct();
        $this->poolService = new PoolService();
    }

    public function pool()
    {
        if (!$this->poolService->isPoolEnabled()) {
            hyiplab_abort(404);
        }

        $this->pageTitle = "Pool Plan";
        $pools = $this->poolService->getActivePools();
        $this->view('user/pool/index', compact('pools'));
    }

    public function poolInvest()
    {
        if (!$this->poolService->isPoolEnabled()) {
            hyiplab_abort(404);
        }

        $this->pageTitle = 'My Pool Invests';
        $poolInvests = $this->poolService->getUserPoolInvestments(
            hyiplab_auth()->user->ID,
            hyiplab_paginate()
        );

        $this->view('user/pool/invest', compact('poolInvests'));
    }

    public function poolInvestStore(Request $request)
    {
        if (!$this->poolService->isPoolEnabled()) {
            hyiplab_abort(404);
        }

        $request->validate([
            'pool_id' => 'required|integer',
            'wallet_type' => 'required|in:deposit_wallet,interest_wallet',
            'amount' => 'required|numeric',
        ]);

        try {
            $this->poolService->processPoolInvestment(hyiplab_auth()->user->ID, $request->all());
            
            $notify[] = ['success', 'Pool investment added successfully'];
        } catch (\InvalidArgumentException $e) {
            $notify[] = ['error', $e->getMessage()];
        }

        return hyiplab_back($notify);
    }
}