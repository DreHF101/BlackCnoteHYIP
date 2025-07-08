<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Services\TransferService;

class TransferController extends Controller
{
    protected $transferService;

    public function __construct()
    {
        parent::__construct();
        $this->transferService = new TransferService();
    }

    public function transferBalance()
    {
        $this->pageTitle = 'Transfer Balance';
        $this->view('user/balance_transfer');
    }

    public function transferBalanceSubmit(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,user_login',
            'amount'   => 'required|numeric|gt:0',
            'wallet'   => 'required|in:deposit_wallet,interest_wallet',
        ]);

        $user = hyiplab_auth()->user;
        $result = $this->transferService->transfer($user, $request);

        if (is_wp_error($result)) {
            $notify[] = ['error', $result->get_error_message()];
            return hyiplab_back($notify);
        }

        $notify[] = ['success', 'Balance transferred successfully.'];
        return hyiplab_back($notify);
    }
} 