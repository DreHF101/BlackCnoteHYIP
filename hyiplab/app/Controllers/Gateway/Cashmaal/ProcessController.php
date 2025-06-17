<?php

namespace Hyiplab\Controllers\Gateway\Cashmaal;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $user                = get_userdata($deposit->user_id);
        $param               = json_decode($gateway->gateway_parameter);
        $val['pay_method']   = " ";
        $val['amount']       = hyiplab_get_amount($deposit->final_amo);
        $val['currency']     = $gateway->currency;
        $val['succes_url']   = hyiplab_route_link('ipn.cashmaal');
        $val['cancel_url']   = hyiplab_route_link('user.deposit.index');
        $val['client_email'] = $user->user_email;
        $val['web_id']       = $param->web_id;
        $val['order_id']     = $deposit->trx;
        $val['addi_info']    = esc_html__("Deposit", HYIPLAB_PLUGIN_NAME);
        $send['url']         = 'https://www.cashmaal.com/Pay/';
        $send['method']      = 'post';
        $send['view']        = 'user/payment/redirect';
        $send['val']         = $val;
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request();
        $gateway = GatewayCurrency::where('gateway_alias', 'Cashmaal')->where('currency', sanitize_text_field($request->currency))->first();
        if (!$gateway) {
            $notify[] = ['error', 'Invalid gateway'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $IPN_key = json_decode($gateway->gateway_parameter)->ipn_key;
        $web_id  = json_decode($gateway->gateway_parameter)->web_id;

        $deposit = Deposit::where('trx', sanitize_text_field($request->order_id))->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        if (sanitize_text_field($request->ipn_key) != $IPN_key && $web_id != sanitize_text_field($request->web_id)) {
            $notify[] = ['error', 'Data invalid'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        if ($request->status == 2) {
            $notify[] = ['info', 'Payment in pending'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        if ($request->status != 1) {
            $notify[] = ['error', 'Data invalid'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        if ($request->status == 1 && $deposit->status == 0 && $request->currency == $deposit->method_currency) {
            DepositController::userDataUpdate($deposit);
            $notify[] = ['success', 'Transaction is successful'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
        } else {
            $notify[] = ['error', 'Payment failed'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }
        hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
    }
}
