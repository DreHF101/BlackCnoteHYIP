<?php

namespace Hyiplab\Controllers\Gateway\NowPaymentsCheckout;

use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Lib\CurlRequest;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Gateway;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $nowPaymentsAcc = json_decode($gateway->gateway_parameter);

        $response       = CurlRequest::curlPostContent('https://api.nowpayments.io/v1/invoice', json_encode([
            'price_amount'     => $deposit->final_amo,
            'price_currency'   => hyiplab_currency('text'),
            'ipn_callback_url' => hyiplab_route_link('ipn.nowpaymentscheckout'),
            'success_url'      => hyiplab_route_link('user.deposit.history'),
            'cancel_url'       => hyiplab_route_link('user.deposit.index'),
            'order_id'         => $deposit->trx,

        ]), [
            "x-api-key: $nowPaymentsAcc->api_key",
            'Content-Type: application/json',
        ]);
        $response = json_decode($response);

        if (!$response) {
            $send['error']   = true;
            $send['message'] = 'Some problem ocurred with api.';
            return json_encode($send);
        }

        if ($response->status == '' && $response->statusCode == 403) {
            $send['error']   = true;
            $send['message'] = $response->message;
            return json_encode($send);
        }

        if ($response->status && $response->status === false) {
            $send['error']   = true;
            $send['message'] = 'Invalid api key';
            return json_encode($send);
        }

        $send['redirect'] = true;
        $send['redirect_url'] = $response->invoice_url ?? '';

        return json_encode($send);
    }

    public function ipn()
    {
        if (isset($_SERVER['HTTP_X_NOWPAYMENTS_SIG']) && !empty($_SERVER['HTTP_X_NOWPAYMENTS_SIG'])) {
            $recived_hmac = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'];
            $request_json = file_get_contents('php://input');
            $request_data = json_decode($request_json, true);
            ksort($request_data);
            $sorted_request_json = json_encode($request_data, JSON_UNESCAPED_SLASHES);
            if ($request_json !== false && !empty($request_json)) {
                $gateway    = Gateway::where('alias', 'NowPaymentsCheckout')->first();
                $gatewayAcc = json_decode($gateway->gateway_parameters);
                $hmac       = hash_hmac("sha512", $sorted_request_json, trim($gatewayAcc->secret_key->value));
                if ($hmac == $recived_hmac) {
                    if ($request_data['payment_status'] == 'confirmed' || $request_data['payment_status'] == 'finished') {
                        if ($request_data['actually_paid'] == $request_data['pay_amount']) {
                            $deposit = Deposit::where('status', 0)->where('trx', $request_data['order_id'])->first();
                            if ($deposit) {
                                DepositController::userDataUpdate($deposit);
                            }
                        }
                    }
                }
            }
        }
    }
}
