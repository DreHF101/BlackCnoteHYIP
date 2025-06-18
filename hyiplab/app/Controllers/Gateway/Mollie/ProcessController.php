<?php

namespace Hyiplab\Controllers\Gateway\Mollie;

use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $mollieAcc = json_decode($gateway->gateway_parameter);
        $mollie    = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($mollieAcc->api_key);

        try {
            $siteName = get_bloginfo('name');
            $payment  = $mollie->payments->create([
                "amount" => [
                    'currency' => "$deposit->method_currency",
                    'value'    => '' . sprintf('%0.2f', round($deposit->final_amo, 2)) . '',
                ],
                "description" => esc_html__("Pay To", HYIPLAB_PLUGIN_NAME) . ' ' . $siteName . esc_html__(' Account', HYIPLAB_PLUGIN_NAME),
                "redirectUrl" => hyiplab_route_link('ipn.mollie'),
            ]);
        } catch (\Exception $e) {
            $send['error']   = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        hyiplab_session()->put('payment_id', $payment->id);

        $send['redirect']     = true;
        $send['redirect_url'] = $payment->getCheckoutUrl();
        return json_encode($send);
    }

    public function ipn()
    {
        $trx        = hyiplab_session()->get('trx');
        $payment_id = hyiplab_session()->get('payment_id');
        $deposit = Deposit::where('trx', $trx)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
        if (!$gateway) {
            $notify[] = ['error', 'Gateway not found'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $mollieAcc = json_decode($gateway->gateway_parameter);
        $mollie    = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($mollieAcc->api_key);
        $payment = $mollie->payments->get($payment_id);

        $data['detail'] = json_encode($payment->details);
        Deposit::where('id', $deposit->id)->update($data);

        if ($payment->status == "paid") {
            DepositController::userDataUpdate($deposit);
            $notify[] = ['success', 'Transaction was successful'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
        }
        $notify[] = ['error', 'Invalid request'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
    }
}
