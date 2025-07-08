<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\Withdrawal;
use Hyiplab\Models\WithdrawMethod;
use Hyiplab\Services\WithdrawalService;
use Hyiplab\Container\Application;
use Hyiplab\Helpers\Csrf;
use Hyiplab\Helpers\RateLimiter;
use Hyiplab\Log\Logger;

class WithdrawalController extends Controller
{
    protected WithdrawalService $withdrawalService;

    public function __construct()
    {
        parent::__construct();
        $this->withdrawalService = app(WithdrawalService::class);
    }

    public function index($scope = 'all')
    {
        $request = new Request();
        $filters = [];
        $pageTitle = 'Withdrawals Log';

        if ($request->username) {
            $user = get_user_by('login', $request->username);
            if ($user) {
                $filters['user_id'] = $user->ID;
                $pageTitle .= ' - ' . $user->user_login;
            }
        } else {
            switch ($scope) {
                case 'pending':
                    $pageTitle = 'Pending Withdrawals';
                    $filters['status'] = 2;
                    break;
                case 'approved':
                    $pageTitle = 'Approved Withdrawals';
                    $filters['status'] = 1;
                    break;
                case 'rejected':
                    $pageTitle = 'Rejected Withdrawals';
                    $filters['status'] = 3;
                    break;
                default:
                    $filters['exclude_status'] = 0;
                    break;
            }
        }

        $withdrawals = $this->withdrawalService->getWithdrawals($filters, hyiplab_paginate());
        return $this->view('admin/withdraw/list', compact('pageTitle', 'withdrawals'));
    }

    public function detail(Request $request)
    {
        $withdrawal = Withdrawal::where('id', $request->id)->where('status', '!=', 0)->firstOrFail();
        $user = get_userdata($withdrawal->user_id);
        $method = WithdrawMethod::where('id', $withdrawal->method_id)->first();
        $pageTitle = $user->user_login . ' Withdraw Requested ' . hyiplab_show_amount($withdrawal->amount) . ' ' . hyiplab_currency('text');
        
        $this->view('admin/withdraw/detail', compact('pageTitle', 'withdrawal', 'method', 'user'));
    }

    public function approve(Request $request)
    {
        // CSRF Protection
        if (!csrf_check($request->csrf_token)) {
            Logger::warning('CSRF token validation failed for withdrawal approval', ['ip' => $_SERVER['REMOTE_ADDR']]);
            $notify[] = ['error', 'Invalid security token'];
            return hyiplab_back($notify);
        }

        // Rate Limiting
        $rateLimitKey = 'withdrawal_approval_' . get_current_user_id();
        if (rate_limit($rateLimitKey, 10, 60)) { // Max 10 approvals per minute
            Logger::warning('Rate limit exceeded for withdrawal approval', ['user_id' => get_current_user_id()]);
            $notify[] = ['error', 'Too many approval attempts. Please wait a moment.'];
            return hyiplab_back($notify);
        }

        $request->validate([
            'id'      => 'required|integer',
            'details' => 'required|string'
        ]);

        try {
            $this->withdrawalService->approveWithdrawal($request->id, $request->details);
            Logger::info('Withdrawal approved by admin', [
                'withdrawal_id' => $request->id,
                'admin_id' => get_current_user_id(),
                'details' => $request->details
            ]);

            $notify[] = ['success', 'Withdrawal approved successfully'];
        } catch (\Exception $e) {
            Logger::error('Withdrawal approval failed', [
                'withdrawal_id' => $request->id,
                'error' => $e->getMessage(),
                'admin_id' => get_current_user_id()
            ]);
            $notify[] = ['error', 'Failed to approve withdrawal: ' . $e->getMessage()];
        }

        return hyiplab_back($notify);
    }

    public function reject(Request $request)
    {
        // CSRF Protection
        if (!csrf_check($request->csrf_token)) {
            Logger::warning('CSRF token validation failed for withdrawal rejection', ['ip' => $_SERVER['REMOTE_ADDR']]);
            $notify[] = ['error', 'Invalid security token'];
            return hyiplab_back($notify);
        }

        // Rate Limiting
        $rateLimitKey = 'withdrawal_rejection_' . get_current_user_id();
        if (rate_limit($rateLimitKey, 10, 60)) { // Max 10 rejections per minute
            Logger::warning('Rate limit exceeded for withdrawal rejection', ['user_id' => get_current_user_id()]);
            $notify[] = ['error', 'Too many rejection attempts. Please wait a moment.'];
            return hyiplab_back($notify);
        }

        $request->validate([
            'id'      => 'required|integer',
            'details' => 'required|string'
        ]);

        try {
            $this->withdrawalService->rejectWithdrawal($request->id, $request->details);
            Logger::info('Withdrawal rejected by admin', [
                'withdrawal_id' => $request->id,
                'admin_id' => get_current_user_id(),
                'details' => $request->details
            ]);

            $notify[] = ['success', 'Withdrawal rejected successfully'];
        } catch (\Exception $e) {
            Logger::error('Withdrawal rejection failed', [
                'withdrawal_id' => $request->id,
                'error' => $e->getMessage(),
                'admin_id' => get_current_user_id()
            ]);
            $notify[] = ['error', 'Failed to reject withdrawal: ' . $e->getMessage()];
        }

        return hyiplab_back($notify);
    }
}
