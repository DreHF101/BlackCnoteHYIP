<?php

namespace Hyiplab\Controllers\Gateway\Coingate;

use Hyiplab\Controllers\Controller;
use CoinGate\CoinGate;
use CoinGate\Merchant\Order;
use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Lib\CurlRequest;
use Hyiplab\Models\Deposit;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $coingateAcc = json_decode($gateway->gateway_parameter);
        try {
            CoinGate::config(array(
                'environment' => 'live',                // sandbox OR live
                'auth_token'  => $coingateAcc->api_key
            ));
        } catch (\Exception $e) {
            $send['error']   = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $post_params = array(
            'order_id'         => $deposit->trx,
            'price_amount'     => round($deposit->final_amo, 2),
            'price_currency'   => $deposit->method_currency,
            'receive_currency' => $deposit->method_currency,
            'callback_url'     => hyiplab_route_link('ipn.coingate'),
            'cancel_url'       => hyiplab_route_link('user.deposit.index'),
            'success_url'      => hyiplab_route_link('user.deposit.history'),
            'title'            => esc_html__('Payment to ', HYIPLAB_PLUGIN_NAME) . get_bloginfo('name'),
            'token'            => $deposit->trx
        );

        try {
            $order = Order::create($post_params);
        } catch (\Exception $e) {
            $send['error']   = true;
            $send['message'] = esc_html($e->getMessage());
            return json_encode($send);
        }
        if ($order) {
            $send['redirect']     = true;
            $send['redirect_url'] = esc_url($order->payment_url);
        } else {
            $send['error']   = true;
            $send['message'] = esc_html__('Unexpected Error! Please Try Again', HYIPLAB_PLUGIN_NAME);
        }
        $send['view'] = '';
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request();
        $ip       = $_SERVER['REMOTE_ADDR'];
        $url      = 'https://api.coingate.com/v2/ips-v4';
        $response = CurlRequest::curlContent($url);
        if (strpos($response, $ip) !== false) {
            $deposit = Deposit::where('trx', sanitize_text_field($request->token))->orderBy('id', 'DESC')->first();
            if ($request->status == 'paid' && $request->price_amount == $deposit->final_amo && $deposit->status == '0') {
                DepositController::userDataUpdate($deposit);
            }
        }
    }
}
