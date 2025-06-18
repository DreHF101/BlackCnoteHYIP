<?php

namespace Hyiplab\Controllers\Gateway\CoinbaseCommerce;

use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $coinbaseAcc = json_decode($gateway->gateway_parameter);
        $user        = get_userdata($deposit->user_id);
        $url         = 'https://api.commerce.coinbase.com/charges';
        $array       = [
            'name'        => esc_html($user->user_login),
            'description' => esc_html__("Pay to ", HYIPLAB_PLUGIN_NAME) . get_bloginfo('name'),
            'local_price' => [
                'amount'   => $deposit->final_amo,
                'currency' => $deposit->method_currency
            ],
            'metadata' => [
                'trx' => $deposit->trx
            ],
            'pricing_type' => "fixed_price",
            'redirect_url' => hyiplab_route_link('user.deposit.history'),
            'cancel_url'   => hyiplab_route_link('user.deposit.index')
        ];

        $jsonData = json_encode($array);
        $ch       = curl_init();
        $apiKey   = $coinbaseAcc->api_key;
        $header   = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'X-CC-Api-Key: ' . "$apiKey";
        $header[] = 'X-CC-Version: 2018-03-22';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);
        if ($result->error == '') {
            $send['redirect']     = true;
            $send['redirect_url'] = esc_url($result->data->hosted_url);
        } else {
            $send['error']   = true;
            $send['message'] = esc_html__('Some problem ocurred with api.', HYIPLAB_PLUGIN_NAME);
        }

        $send['view'] = '';
        return json_encode($send);
    }

    public function ipn()
    {
        $postdata = file_get_contents("php://input");
        $res      = json_decode($postdata);
        $deposit  = Deposit::where('trx', $res->event->data->metadata->trx)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found'];
            hyiplab_back($notify);
        }
        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
        if (!$gateway) {
            $notify[] = ['error', 'Invalid gateway'];
            hyiplab_back($notify);
        }
        $coinbaseAcc = json_decode($gateway->gateway_parameter);
        $headers     = apache_request_headers();
        $headers     = json_decode(json_encode($headers), true);
        $sentSign    = $headers['X-Cc-Webhook-Signature'];
        $sig         = hash_hmac('sha256', $postdata, $coinbaseAcc->secret);
        if ($sentSign == $sig) {
            if ($res->event->type == 'charge:confirmed' && $deposit->status == 0) {
                DepositController::userDataUpdate($deposit);
            }
        }
    }
}
