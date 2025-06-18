<?php

namespace Hyiplab\Controllers\Gateway\Flutterwave;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $flutterAcc             = json_decode($gateway->gateway_parameter);
        $user                   = get_userdata($deposit->user_id);
        $send['API_publicKey']  = $flutterAcc->public_key;
        $send['encryption_key'] = $flutterAcc->encryption_key;
        $send['customer_email'] = $user->user_email;
        $send['amount']         = round($deposit->final_amo, 2);
        $send['customer_phone'] = get_user_meta($user->ID, 'hyiplab_mobile', true) ?? '';
        $send['currency']       = $deposit->method_currency;
        $send['txref']          = $deposit->trx;
        $send['notify_url']     = hyiplab_route_link('ipn.flutterwave');
        $alias                  = $gateway->gateway_alias;
        $send['view']           = 'user/payment/' . $alias;
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request;
        $track   = $request->trx;
        $type    = $request->status;

        if ($type == 'error') {
            $message  = 'Transaction failed, Ref: ' . $track;
            $notify[] = ['error', $message];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        if (!isset($track)) {
            $notify[] = ['error', 'Unable to process'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
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

        $flutterAcc = json_decode($gateway->gateway_parameter);

        $query = array(
            "SECKEY" => $flutterAcc->secret_key,
            "txref"  => $track
        );

        $dataString = json_encode($query);
        $ch         = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);
        $response        = json_decode($response);
        $deposit->detail = $response->data;
        $deposit->save();

        if ($response->status == 'error') {
            $message  = $response->message;
            $notify[] = ['error', $message];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }

        if ($response->data->status == "successful" && $response->data->chargecode == "00" && $deposit->final_amo == $response->data->amount && $deposit->method_currency == $response->data->currency && $deposit->status == '0') {
            DepositController::userDataUpdate($deposit);
            $message  = 'Transaction was successful, Ref: ' . $track;
            $notify[] = ['success', $message];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
        }

        $notify[] = ['error', 'Unable to process'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
    }
}
