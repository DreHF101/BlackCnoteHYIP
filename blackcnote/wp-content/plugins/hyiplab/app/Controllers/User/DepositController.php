<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\FormProcessor;
use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Form;
use Hyiplab\Models\Gateway;
use Hyiplab\Models\GatewayCurrency;
use Hyiplab\Models\Transaction;
use Hyiplab\Services\DepositService;
use Hyiplab\Controllers\Gateway\GatewayProcessor;

class DepositController extends Controller
{
    protected $depositService;

    public function __construct()
    {
        parent::__construct();
        $this->depositService = new DepositService();
    }

    public function index()
    {
        $this->pageTitle = "Deposit";
        $gatewayCurrency = GatewayCurrency::whereHas('gateway', function ($query) {
            $query->where('status', 1);
        })->orderBy('name', 'asc')->get();
        
        return $this->view('user/payment/deposit', compact('gatewayCurrency'));
    }

    public function history()
    {
        global $user_ID;
        $this->pageTitle = "Deposit History";
        $deposits = Deposit::where('user_id', $user_ID)->where('status', '!=', 0)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('user/deposit_history', compact('deposits'));
    }

    public function insert()
    {
        $request = new Request();
        $request->validate([
            'amount'      => 'required|numeric|gt:0',
            'method_code' => 'required|integer',
            'currency'    => 'required'
        ]);

        $methodCode = sanitize_text_field($request->method_code);
        $currency = sanitize_text_field($request->currency);

        $gatewayCurrency = GatewayCurrency::where('method_code', $methodCode)->where('currency', $currency)->first();
        if (!$gatewayCurrency) {
            $notify[] = ['error', 'Gateway currency not found'];
            return hyiplab_back($notify);
        }

        if ($gatewayCurrency->min_amount > $request->amount || $gatewayCurrency->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return hyiplab_back($notify);
        }

        $data = $this->insertDeposit($gatewayCurrency, $request->amount);
        hyiplab_session()->put('trx', $data->trx);
        return hyiplab_redirect(hyiplab_route_link('user.deposit.confirm'));
    }

    private function insertDeposit($gateway, $amount)
    {
        $charge = $gateway->fixed_charge + ($amount * $gateway->percent_charge / 100);
        $payable = $amount + $charge;
        $final_amo = $payable * $gateway->rate;

        $deposit = new Deposit();
        $deposit->user_id = hyiplab_auth()->user->ID;
        $deposit->method_code = $gateway->method_code;
        $deposit->method_currency = strtoupper($gateway->currency);
        $deposit->amount = $amount;
        $deposit->charge = $charge;
        $deposit->rate = $gateway->rate;
        $deposit->final_amo = $final_amo;
        $deposit->trx = hyiplab_trx();
        $deposit->status = 0;
        $deposit->save();
        return $deposit;
    }

    public function confirm()
    {
        $this->pageTitle = "Payment Confirm";
        $trx = hyiplab_session()->get('trx');

        $deposit = Deposit::where('trx', $trx)->where('status', 0)->orderBy('id', 'DESC')->firstOrFail();
        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->firstOrFail();

        if ($gateway->method_code >= 1000) {
            return hyiplab_redirect(hyiplab_route_link('user.deposit.manual'));
        }
        
        $processor = new GatewayProcessor();
        $result = $processor->process($deposit, $gateway);

        if (isset($result->error)) {
            $notify[] = ['error', $result->message];
            hyiplab_set_notify($notify);
            return hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        if (isset($result->redirect)) {
            return hyiplab_redirect($result->redirect_url);
        }
        
        $this->view($result->view, ['data' => $result, 'deposit' => $deposit]);
    }

    public function manual()
    {
        $this->pageTitle = "Payment Confirm";
        $trx     = hyiplab_session()->get('trx');
        $deposit = Deposit::where('trx', $trx)->where('status', 0)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Invalid request'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
        if (!$gateway) {
            $notify[] = ['error', 'Invalid gateway'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $method = Gateway::where('code', $deposit->method_code)->first();

        if ($deposit->method_code > 999) {
            return $this->view('user/payment/manual', compact('deposit', 'method', 'gateway'));
        }
        hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
    }

    public function manualUpdate()
    {
        $request = new Request();
        $trx     = hyiplab_session()->get('trx');
        $deposit = Deposit::where('trx', $trx)->where('status', 0)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Invalid request'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
        if (!$gateway) {
            $notify[] = ['error', 'Invalid gateway'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $method = Gateway::where('code', $deposit->method_code)->first();

        $form          = Form::find($method->form_id);
        $formData      = json_decode(json_encode(maybe_unserialize($form->form_data)));
        $formProcessor = new FormProcessor();
        $userData      = $formProcessor->processFormData($request, $formData);

        $data['detail'] = maybe_serialize($userData);
        $data['status'] = 2;

        Deposit::where('id', $deposit->id)->update($data);

        $user = get_userdata($deposit->user_id);

        hyiplab_notify($user, 'DEPOSIT_REQUEST', [
            'method_name'     => $gateway->name,
            'method_currency' => $deposit->method_currency,
            'method_amount'   => hyiplab_show_amount($deposit->final_amo),
            'amount'          => hyiplab_show_amount($deposit->amount),
            'charge'          => hyiplab_show_amount($deposit->charge),
            'rate'            => hyiplab_show_amount($deposit->rate),
            'trx'             => $deposit->trx
        ]);

        $notify[] = ['success', 'Your deposit request has been taken'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
    }
}
