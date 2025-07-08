<?php

namespace Hyiplab\Controllers\Gateway\StripeV3;

use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Gateway;
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
        $credentials = json_decode($gateway->gateway_parameter);
        \Stripe\Stripe::setApiKey("$credentials->secret_key");
        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => [[
                    'name'        => get_bloginfo('name'),
                    'description' => esc_html__('Deposit with Stripe', HYIPLAB_PLUGIN_NAME),
                    'images'      => [],
                    'amount'      => round($deposit->final_amo, 2) * 100,
                    'currency'    => "$deposit->method_currency",
                    'quantity'    => 1,
                ]],
                'cancel_url'  => hyiplab_route_link('user.deposit.index'),
                'success_url' => hyiplab_route_link('user.deposit.history'),
            ]);
        } catch (\Exception $e) {
            $send['error']   = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }
        $send['view']        = 'user/payment/' . $gateway->gateway_alias;
        $send['session']     = $session;
        $send['StripeJSAcc'] = $credentials;
        Deposit::where('id', $deposit->id)->update(['btc_wallet' => json_decode(json_encode($session))->id]);
        return json_encode($send);
    }

    public function ipn()
    {
        $gateway = Gateway::where('code', 114)->first();
        if (!$gateway) {
            http_response_code(404);
            die;
        }
        $credentials = json_decode($gateway->gateway_parameters);

        \Stripe\Stripe::setApiKey($credentials->secret_key->value);
                                                            // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $credentials->end_point->value;  // main
        $payload    = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event      = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
              // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
              // Invalid signature
            http_response_code(400);
            exit();
        }
          // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            $deposit = Deposit::where('btc_wallet', $session->id)->orderBy('id', 'DESC')->first();

            if ($deposit->status == 0) {
                $this->depositService->updateUserData($deposit);
            }
        }
        http_response_code(200);
    }
}
