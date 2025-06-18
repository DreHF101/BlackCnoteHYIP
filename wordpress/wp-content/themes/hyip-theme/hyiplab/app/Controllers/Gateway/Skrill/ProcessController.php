<?php

namespace Hyiplab\Controllers\Gateway\Skrill;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $credentials                = json_decode($gateway->gateway_parameter);
        $val['pay_to_email']        = trim($credentials->pay_to_email);
        $val['transaction_id']      = "$deposit->trx";
        $siteName                   = get_bloginfo('name');
        $val['return_url']          = hyiplab_route_link('user.deposit.history');
        $val['return_url_text']     = esc_html__("Return ", HYIPLAB_PLUGIN_NAME) . $siteName;
        $val['cancel_url']          = hyiplab_route_link('user.deposit.index');
        $val['status_url']          = hyiplab_route_link('ipn.skrill');
        $val['language']            = 'EN';
        $val['amount']              = round($deposit->final_amo, 2);
        $val['currency']            = "$deposit->method_currency";
        $val['detail1_description'] = "$siteName";
        $val['detail1_text']        = esc_html__("Pay To ", HYIPLAB_PLUGIN_NAME) . $siteName;
        $val['logo_url']            = [];
        $send['val']                = $val;
        $send['view']               = 'user/payment/redirect';
        $send['method']             = 'post';
        $send['url']                = 'https://www.moneybookers.com/app/payment.pl';
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request();
        $deposit = Deposit::where('trx', sanitize_text_field($request->transaction_id))->orderBy('id', 'DESC')->first();
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
        $SkrillrAcc   = json_decode($gateway->gateway_parameter);
        $concatFields = $request->merchant_id
            . $request->transaction_id
            . strtoupper(md5($SkrillrAcc->secret_key))
            . $request->mb_amount
            . $request->mb_currency
            . $request->status;

        if (strtoupper(md5($concatFields)) == $request->md5sig && $request->status == 2 && $request->pay_to_email == $SkrillrAcc->pay_to_email && $deposit->status = '0') {
            DepositController::userDataUpdate($deposit);
        }
    }
}
