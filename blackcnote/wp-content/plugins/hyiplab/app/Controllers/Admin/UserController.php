<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\User;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Withdrawal;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\SupportTicket;
use Hyiplab\Services\AdminUserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new AdminUserService();
    }

    public function index($scope = 'all')
    {
        $request = new Request();
        $filters = ['search' => $request->search];
        $pageTitle = 'All Users';

        switch ($scope) {
            case 'active':
                $pageTitle = 'Active Users';
                $filters['status'] = '1';
                break;
            case 'banned':
                $pageTitle = 'Banned Users';
                $filters['status'] = '0';
                break;
            case 'kyc_pending':
                $pageTitle = 'KYC Pending Users';
                $filters['kyc_status'] = '2';
                break;
            case 'kyc_unverified':
                $pageTitle = 'KYC Unverified Users';
                $filters['kyc_unverified'] = true;
                break;
        }

        $users = $this->userService->getUsers($filters, hyiplab_paginate());
        $this->view('admin/users/list', compact('pageTitle', 'users'));
    }

    public function userDetail(Request $request)
    {
        $user = User::findOrFail($request->id);
        $pageTitle = "User Detail - " . $user->user_login;
        
        $widget['total_deposit'] = Deposit::where('user_id', $user->ID)->where('status', 1)->sum('amount');
        $widget['total_withdrawals'] = Withdrawal::where('user_id', $user->ID)->where('status', 1)->sum('amount');
        $widget['total_transaction'] = Transaction::where('user_id', $user->ID)->count();
        $widget['pending_ticket'] = SupportTicket::whereIn('status', [0, 2])->where('user_id', $user->ID)->count();

        $countries = $this->userService->getCountryData();
        
        $this->view('admin/users/detail', compact('pageTitle', 'user', 'countries', 'widget'));
    }

    public function userUpdate(Request $request)
    {
        $request->validate([
            'id'           => 'required|integer',
            'display_name' => 'required',
            'email'        => 'required|email',
        ]);

        $this->userService->updateUser($request->id, $request->all());

        // Handle KYC and Email Verification
        update_user_meta($request->id, 'hyiplab_kyc', $request->kyc_verify ? 1 : 0);
        if ($request->email_verify) {
            delete_user_meta($request->id, 'hyiplab_email_verify');
        } else {
            // Logic for sending verification email
        }

        $notify[] = ['success', 'User details updated successfully'];
        return hyiplab_back($notify);
    }
    
    public function userBan(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $user = User::findOrFail($request->id);
        $isBanned = get_user_meta($user->ID, 'hyiplab_user_active', true) === '0';

        if ($isBanned) {
            update_user_meta($user->ID, 'hyiplab_user_active', 1);
            update_user_meta($user->ID, 'hyiplab_user_ban_reason', null);
            $notify[] = ['success', 'User unbanned successfully'];
        } else {
            $request->validate(['reason' => 'required|string|max:255']);
            update_user_meta($user->ID, 'hyiplab_user_active', 0);
            update_user_meta($user->ID, 'hyiplab_user_ban_reason', $request->reason);
            $notify[] = ['success', 'User banned successfully'];
        }
        
        return hyiplab_back($notify);
    }


    public function userAddSubBalance(Request $request)
    {
        $request->validate([
            'id'        => 'required|integer',
            'amount'    => 'required|numeric|gt:0',
            'wallet'    => 'required|in:deposit_wallet,interest_wallet',
            'operation' => 'required|in:add,sub',
            'remark'    => 'required|string|max:255',
        ]);

        $this->userService->addSubBalance(
            $request->id,
            $request->amount,
            $request->wallet,
            $request->operation,
            $request->remark
        );

        $notify[] = ['success', 'Balance updated successfully.'];
        return hyiplab_back($notify);
    }
    
    // KYC methods would also be refactored to use the service
}
