<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Services\ReportService;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct()
    {
        parent::__construct();
        $this->reportService = new ReportService();
    }

    public function transaction(Request $request)
    {
        if ($request->username) {
            $transactions = $this->reportService->getTransactionReportByUsername($request->username, hyiplab_paginate());
            if (!$transactions) {
                hyiplab_abort(404);
            }
            $user = get_user_by('login', $request->username);
            $pageTitle = 'Transaction Logs - ' . $user->user_login;
        } else {
            $transactions = $this->reportService->getTransactionReport([], hyiplab_paginate());
            $pageTitle = 'Transaction Logs';
        }

        $this->view('admin/report/transactions', compact('pageTitle', 'transactions'));
    }

    public function investHistory()
    {
        $pageTitle = "Invest History";
        $reportData = $this->reportService->getInvestmentHistoryReport(hyiplab_paginate());
        
        $this->view('admin/report/invest_history', [
            'pageTitle' => $pageTitle,
            'invests' => $reportData['invests'],
            'totalInvestCount' => $reportData['summary']['total_count'],
            'totalInvestAmount' => $reportData['summary']['total_amount'],
            'totalPaid' => $reportData['summary']['total_paid'],
            'shouldPay' => $reportData['summary']['should_pay']
        ]);
    }
}
