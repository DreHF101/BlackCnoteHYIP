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

class DepositController extends Controller
{
    public function index()
    {
        global $wpdb;
        $this->pageTitle = "Deposit";
        $table_prefix = $wpdb->base_prefix;
        $gatewayCurrency = GatewayCurrency::selectRaw("select * from `" . $table_prefix . "hyiplab_gateway_currencies` where exists (select * from `" . $table_prefix . "hyiplab_gateways` where `" . $table_prefix . "hyiplab_gateway_currencies`.`method_code` = `" . $table_prefix . "hyiplab_gateways`.`code` and `status` = 1) order by `name` asc");
        return $this->view('user/payment/deposit', compact('gatewayCurrency'));
    }

    public function history()
    {
        global $user_ID;
        $this->pageTitle = "Deposit History";
        $deposits        = Deposit::where('user_id', $user_ID)->where('status', '!=', 0)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('user/deposit_history', compact('deposits'));
    }

    public function insert()
    {
        $request = new Request();
        $request->validate([
            'amount'      => 'required|numeric',
            'method_code' => 'required|integer',
            'currency'    => 'required'
        ]);

        if ($request->amount <= 0) {
            $notify[] = ['error', 'Amount must be greater than zero'];
            hyiplab_back($notify);
        }

        $gatewayCurrency = GatewayCurrency::where('method_code', sanitize_text_field($request->method_code))->where('currency', sanitize_text_field($request->currency))->first();
        if (!$gatewayCurrency) {
            $notify[] = ['error', 'Gateway currency not found'];
            hyiplab_back($notify);
        }

        if ($gatewayCurrency->min_amount > $request->amount || $gatewayCurrency->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            hyiplab_back($notify);
        }

        $data = self::insertDeposit($gatewayCurrency, $request->amount);
        hyiplab_session()->put('trx', $data->trx);
        return hyiplab_redirect(hyiplab_route_link('user.deposit.confirm'));
    }

    public static function insertDeposit($gateway, $amount)
    {
        $charge    = $gateway->fixed_charge + ($amount * $gateway->percent_charge / 100);
        $payable   = $amount + $charge;
        $final_amo = $payable * $gateway->rate;

        $deposit                  = new Deposit();
        $deposit->user_id         = hyiplab_auth()->user->ID;
        $deposit->method_code     = $gateway->method_code;
        $deposit->method_currency = strtoupper($gateway->currency);
        $deposit->amount          = $amount;
        $deposit->charge          = $charge;
        $deposit->rate            = $gateway->rate;
        $deposit->final_amo       = $final_amo;
        $deposit->btc_amo         = 0;
        $deposit->btc_wallet      = "";
        $deposit->trx             = hyiplab_trx();
        $deposit->payment_try     = 0;
        $deposit->status          = 0;
        $deposit->created_at      = current_time('mysql');
        $deposit->updated_at      = current_time('mysql');
        $deposit->save();
        return $deposit;
    }

    public function confirm()
    {
        $this->pageTitle = "Payment Confirm";

        $trx = hyiplab_session()->get('trx');
        if (!$trx) {
            $notify[] = ['error', 'Invalid session'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

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

        if ($deposit->method_code >= 1000) {
            hyiplab_redirect(hyiplab_route_link('user.deposit.manual'));
        }

        $dirName = $gateway->gateway_alias;
        $new     = 'Hyiplab\\Controllers\\Gateway\\' . $dirName . '\\ProcessController';
        $data = $new::process($deposit, $gateway);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        if (isset($data->redirect)) {
            hyiplab_redirect($data->redirect_url);
        }

        if (isset($data->session) && $data->session) {
            $depositData['btc_wallet'] = $data->session->id;
            Deposit::where('id', $deposit->id)->update($depositData);
        }
        $this->view($data->view, compact('data', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit->status == 0 || $deposit->status == 2) {
            $data['status'] = 1;
            Deposit::where('id', $deposit->id)->update($data);
            $afterBalance = hyiplab_balance($deposit->user_id, 'deposit_wallet') + $deposit->amount;
            update_user_meta($deposit->user_id, "hyiplab_deposit_wallet", $afterBalance);

            $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();

            $transactions = [
                'user_id'      => $deposit->user_id,
                'amount'       => $deposit->amount,
                'post_balance' => $afterBalance,
                'charge'       => $deposit->charge,
                'trx_type'     => '+',
                'details'      => __("Deposited via ", HYIPLAB_PLUGIN_NAME) . esc_html($gateway->name),
                'trx'          => $deposit->trx,
                'wallet_type'  => 'deposit_wallet',
                'remark'       => 'deposit',
                'created_at'   => current_time('mysql')
            ];

            $transaction = new Transaction();
            $transaction->insert($transactions);

            $user = get_userdata($deposit->user_id);

            if (get_option('hyiplab_deposit_commission') == 1) {
                $commissionType = 'deposit_commission';
                HyipLab::levelCommission($user, $deposit->amount, $commissionType, $deposit->trx);
            }

            hyiplab_notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name'     => esc_html($gateway->name),
                'method_currency' => esc_html($deposit->method_currency),
                'method_amount'   => hyiplab_show_amount($deposit->final_amo),
                'amount'          => hyiplab_show_amount($deposit->amount),
                'charge'          => hyiplab_show_amount($deposit->charge),
                'rate'            => hyiplab_show_amount($deposit->rate),
                'trx'             => $deposit->trx,
                'post_balance'    => hyiplab_show_amount($afterBalance)
            ]);
        }
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
