<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\WithdrawMethod;
use Hyiplab\Services\WithdrawalService;

class WithdrawController extends Controller
{
    protected $withdrawalService;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('kyc');
        $this->withdrawalService = new WithdrawalService();
    }

    public function index()
    {
        $this->pageTitle = 'Withdraw';
        $methods = $this->withdrawalService->getWithdrawalMethods();
        $holidayStatus = $this->withdrawalService->checkHolidayStatus();
        
        $this->view('user/withdraw/methods', [
            'methods' => $methods,
            'isHoliday' => $holidayStatus['is_holiday'],
            'nextWorkingDay' => $holidayStatus['next_working_day']
        ]);
    }

    public function insert(Request $request)
    {
        if (!$this->withdrawalService->canWithdrawToday()) {
            $notify[] = ['error', 'Today is a holiday. You are unable to withdraw today.'];
            return hyiplab_back($notify);
        }

        $request->validate([
            'method_code' => 'required|integer',
            'amount'      => 'required|numeric|gt:0'
        ]);

        try {
            $withdrawal = $this->withdrawalService->createWithdrawal(
                $request->method_code,
                $request->amount,
                hyiplab_auth()->user->ID
            );
            
            hyiplab_session()->put('trx', $withdrawal->trx);
            return hyiplab_redirect(hyiplab_route_link('user.withdraw.preview'));
        } catch (\InvalidArgumentException $e) {
            $notify[] = ['error', $e->getMessage()];
            return hyiplab_back($notify);
        }
    }

    public function preview()
    {
        $this->pageTitle = 'Withdraw Preview';
        $trx = hyiplab_session()->get('trx');
        $withdraw = $this->withdrawalService->getWithdrawalByTrx($trx);
        
        if (!$withdraw) {
            $notify[] = ['error', 'Invalid withdrawal request'];
            hyiplab_set_notify($notify);
            return hyiplab_redirect(hyiplab_route_link('user.withdraw.index'));
        }
        
        $method = WithdrawMethod::find($withdraw->method_id);
        $this->view('user/withdraw/preview', compact('withdraw', 'method'));
    }

    public function submit(Request $request)
    {
        $trx = hyiplab_session()->get('trx');
        
        try {
            $this->withdrawalService->submitWithdrawal($trx, $request->all());
            
            $notify[] = ['success', 'Withdraw request sent successfully'];
            hyiplab_set_notify($notify);
            return hyiplab_redirect(hyiplab_route_link('user.withdraw.history'));
        } catch (\InvalidArgumentException $e) {
            $notify[] = ['error', $e->getMessage()];
            hyiplab_set_notify($notify);
            return hyiplab_redirect(hyiplab_route_link('user.withdraw.index'));
        }
    }

    public function history()
    {
        $this->pageTitle = 'Withdraw History';
        $withdraws = $this->withdrawalService->getUserWithdrawals(
            hyiplab_auth()->user->ID,
            hyiplab_paginate()
        );
        
        $this->view('user/withdraw/history', compact('withdraws'));
    }
}
