<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Invest;
use Hyiplab\Models\Transaction;

class ReportController extends Controller
{

    public function transaction()
    {
        $request = new Request();
        if ($request->username) {
            $user = get_user_by('login', $request->username);
            if (!$user) {
                hyiplab_abort(404);
            }
            $pageTitle    = 'Transaction Logs - ' . $user->user_login;
            $transactions = Transaction::where('user_id', $user->ID)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        } else {
            $pageTitle    = 'Transaction Logs';
            $transactions = Transaction::orderBy('id', 'desc')->paginate(hyiplab_paginate());
        }
        $this->view('admin/report/transactions', compact('pageTitle', 'transactions'));
    }

    public function investHistory()
    {
        $pageTitle         = "Invest History";
        $invests           = Invest::orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $totalInvestCount  = Invest::count();
        $totalInvestAmount = Invest::sum('amount');
        $totalPaid         = Invest::sum('paid');
        $shouldPay         = Invest::where('period', '!=', -1)->sum('should_pay');
        $this->view('admin/report/invest_history', compact('pageTitle', 'invests', 'totalInvestCount', 'totalInvestAmount', 'totalPaid', 'shouldPay'));
    }
}
