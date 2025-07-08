<?php

namespace Hyiplab\Controllers\Gateway\Coingate;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;
use Hyiplab\Services\DepositService;
use CoinGate\CoinGate;
use CoinGate\Merchant\Order;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Lib\CurlRequest;

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
            'token'            => $coingateAcc->app_id
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

    public function ipn(Request $request)
    {
        $deposit = Deposit::where('trx', $request->order_id)->orderBy('id', 'DESC')->first();
        if ($request->status == 'paid' && $request->price_amount == $deposit->final_amo && $deposit->status == '0') {
            $this->depositService->updateUserData($deposit);
        }
    }
}
