<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Deposit;
use Hyiplab\Services\DepositService;

class DepositController extends Controller
{
    protected $depositService;

    public function __construct()
    {
        parent::__construct();
        $this->depositService = new DepositService();
    }

    public function index($scope = 'all')
    {
        $request = new Request();
        $filters = [];
        $pageTitle = 'Deposit History';

        if ($request->username) {
            $user = get_user_by('login', $request->username);
            if ($user) {
                $filters['user_id'] = $user->ID;
                $pageTitle .= ' - ' . $request->username;
            }
        } else {
            switch ($scope) {
                case 'pending':
                    $pageTitle = 'Pending Deposits';
                    $filters['status'] = 2;
                    break;
                case 'approved':
                    $pageTitle = 'Approved Deposits';
                    $filters['status'] = 1;
                    $filters['method_code'] = 1000;
                    break;
                case 'successful':
                    $pageTitle = 'Successful Deposits';
                    $filters['status'] = 1;
                    break;
                case 'rejected':
                    $pageTitle = 'Rejected Deposits';
                    $filters['status'] = 3;
                    break;
                case 'initiated':
                    $pageTitle = 'Initiated Deposits';
                    $filters['status'] = 0;
                    break;
            }
        }
        
        $deposits = $this->depositService->getDeposits($filters, hyiplab_paginate());
        $this->view('admin/deposit/log', compact('pageTitle', 'deposits'));
    }

    public function detail(Request $request)
    {
        $deposit = Deposit::findOrFail($request->id);
        $gateway = hyiplab_gateway($deposit->method_code);
        $user = get_userdata($deposit->user_id);
        $pageTitle = $user->user_login . ' requested ' . hyiplab_show_amount($deposit->amount) . ' ' . hyiplab_currency('text');
        
        $this->view('admin/deposit/detail', compact('pageTitle', 'deposit', 'gateway', 'user'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $this->depositService->approveDeposit($request->id);
        
        $notify[] = ['success', 'Deposit request approved successfully'];
        return hyiplab_back($notify);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id'      => 'required|integer',
            'message' => 'required|string',
        ]);
        
        $this->depositService->rejectDeposit($request->id, $request->message);

        $notify[] = ['success', 'Deposit request rejected successfully'];
        return hyiplab_back($notify);
    }
}
