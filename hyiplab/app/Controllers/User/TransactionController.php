<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        global $user_ID;
        $this->pageTitle = "Transactions";
        $transactions = Transaction::where('user_id', $user_ID)->orderBy('id', 'DESC')->paginate(hyiplab_paginate());
        $this->view('user/transactions', compact('transactions'));
    }
}
