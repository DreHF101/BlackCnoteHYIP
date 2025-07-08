<?php

namespace Hyiplab\Controllers\Gateway\StripeJs;

use Hyiplab\Controllers\Controller;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;
use Hyiplab\BackOffice\Request;
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
        $StripeJSAcc        = json_decode($gateway->gateway_parameter);
        $user               = get_userdata($deposit->user_id);
        $val['key']         = $StripeJSAcc->publishable_key;
        $val['name']        = $user->user_login;
        $val['description'] = esc_html__("Payment with Stripe", HYIPLAB_PLUGIN_NAME);
        $val['amount']      = $deposit->final_amo * 100;
        $val['currency']    = $deposit->method_currency;
        $send['val']        = $val;
        $send['src']        = "https://checkout.stripe.com/checkout.js";
        $send['view']       = 'user/payment/StripeJs';
        $send['method']     = 'post';
        $send['url']        = hyiplab_route_link('ipn.stripejs');
        $send['deposit']    = $deposit;
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request();
        $trx     = hyiplab_session()->get('trx');
        $deposit = Deposit::where('status', 0)->where('trx', $trx)->orderBy('id', 'DESC')->first();
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

        $StripeJSAcc = json_decode($gateway->gateway_parameter);

        Stripe::setApiKey($StripeJSAcc->secret_key);
        Stripe::setApiVersion("2020-03-02");

        try {
            $customer =  Customer::create([
                'email'  => $request->stripeEmail,
                'source' => $request->stripeToken,
            ]);
        } catch (\Exception $e) {
            $notify[] = ['error', esc_html($e->getMessage())];
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        try {
            $charge = Charge::create([
                'customer'    => $customer->id,
                'description' => esc_html__('Payment with Stripe', HYIPLAB_PLUGIN_NAME),
                'amount'      => $deposit->final_amo * 100,
                'currency'    => $deposit->method_currency,
            ]);
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        if ($charge['status'] == 'succeeded') {
            $this->depositService->updateUserData($deposit);
            $notify[] = ['success', 'Payment captured successfully'];
            hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
        } else {
            $notify[] = ['error', 'Failed to process'];
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }
    }
}
