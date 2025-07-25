<?php

namespace Hyiplab\Controllers\Gateway\Paystack;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;
use Hyiplab\Services\DepositService;

class ProcessController extends Controller
{
    protected $depositService;

    public function __construct()
    {
        parent::__construct();
        $this->depositService = new DepositService();
    }

    public static function process($deposit, $gateway)
    {
        $user             = get_userdata($deposit->user_id);
        $paystackAcc      = json_decode($gateway->gateway_parameter);
        $alias            = $gateway->gateway_alias;
        $send['key']      = $paystackAcc->public_key;
        $send['email']    = $user->user_email;
        $send['amount']   = $deposit->final_amo * 100;
        $send['currency'] = $deposit->method_currency;
        $send['ref']      = $deposit->trx;
        $send['view']     = 'user/payment/' . $alias;
        $send['deposit']  = $deposit;
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request();
        $request->validate([
            'reference'       => 'required',
            'paystack-trxref' => 'required'
        ]);
        $track = sanitize_text_field($request->reference);

        $deposit = Deposit::where('trx', $track)->where('status', 0)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
        if (!$gateway) {
            $notify[] = ['error', 'Invalid gateway'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $paystackAcc = json_decode($gateway->gateway_parameter);
        $secret_key  = $paystackAcc->secret_key;

        $result = array();
          //The parameter after verify/ is the transaction reference to be verified
        $url = 'https://api.paystack.co/transaction/verify/' . $track;
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $secret_key]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);

            if ($result) {
                if ($result['data']) {
                    $data['detail'] = json_encode($result['data']);
                    Deposit::where('id', $deposit->id)->update($data);
                    if ($result['data']['status'] == 'success') {
                        $am  = $result['data']['amount'] / 100;
                        $sam = round($deposit->final_amo, 2);
                        if ($am == $sam && $result['data']['currency'] == $deposit->method_currency  && $deposit->status == '0') {
                            $this->depositService->updateUserData($deposit);
                            $notify[] = ['success', 'Payment captured successfully'];
                            hyiplab_set_notify($notify);
                            hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
                        } else {
                            $notify[] = ['error', 'Less amount paid. Please contact with admin.'];
                        }
                    } else {
                        $notify[] = ['error', $result['data']['gateway_response']];
                    }
                } else {
                    $notify[] = ['error', $result['message']];
                }
            } else {
                $notify[] = ['error', 'Something went wrong while executing'];
            }
        } else {
            $notify[] = ['error', 'Something went wrong while executing'];
        }
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
    }
}
