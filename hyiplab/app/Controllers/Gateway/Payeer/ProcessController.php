<?php

namespace Hyiplab\Controllers\Gateway\Payeer;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $siteName         = get_bloginfo('name');
        $payeerAcc        = json_decode($gateway->gateway_parameter);
        $val['m_shop']    = trim($payeerAcc->merchant_id);
        $val['m_orderid'] = $deposit->trx;
        $val['m_amount']  = number_format($deposit->final_amo, 2, '.', '');
        $val['m_curr']    = $deposit->method_currency;
        $val['m_desc']    = base64_encode("Pay To $siteName");
        $arHash           = [$val['m_shop'], $val['m_orderid'], $val['m_amount'], $val['m_curr'], $val['m_desc']];
        $arHash[]         = $payeerAcc->secret_key;
        $val['m_sign']    = strtoupper(hash('sha256', implode(":", $arHash)));
        $send['val']      = $val;
        $send['view']     = 'user/payment/redirect';
        $send['method']   = 'get';
        $send['url']      = 'https://payeer.com/merchant';
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request();

        if (isset($request->m_operation_id) && isset($request->m_sign)) {
            $deposit = Deposit::where('trx', $request->m_orderid)->orderBy('id', 'DESC')->first();
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

            $payeerAcc = json_decode($gateway->gateway_parameter);
            $sign_hash = strtoupper(hash('sha256', implode(":", array(
                $request->m_operation_id,
                $request->m_operation_ps,
                $request->m_operation_date,
                $request->m_operation_pay_date,
                $request->m_shop,
                $request->m_orderid,
                $request->m_amount,
                $request->m_curr,
                $request->m_desc,
                $request->m_status,
                $payeerAcc->secret_key
            ))));

            if ($request->m_sign != $sign_hash) {
                $notify[] = ['error', 'The digital signature did not matched'];
            } else {

                if ($request->m_amount == hyiplab_get_amount($deposit->final_amo) && $request->m_curr == $deposit->method_currency && $request->m_status == 'success' && $deposit->status == '0') {
                    DepositController::userDataUpdate($deposit);
                    $notify[] = ['success', 'Transaction is successful'];
                    hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
                } else {
                    $notify[] = ['error', 'Payment failed'];
                }
            }
        } else {
            $notify[] = ['error', 'Payment failed'];
        }
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
    }
}
