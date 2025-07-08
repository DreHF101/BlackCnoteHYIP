<?php

namespace Hyiplab\Controllers\Gateway\NowPaymentsHosted;

use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Lib\CurlRequest;
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
        $nowPaymentsAcc = json_decode($gateway->gateway_parameter);
        $response       = CurlRequest::curlPostContent('https://api.nowpayments.io/v1/payment', json_encode([
            'price_amount'     => $deposit->final_amo,
            'price_currency'   => hyiplab_currency('text'),
            'pay_currency'     => $deposit->method_currency,
            'ipn_callback_url' => hyiplab_route_link('ipn.nowpaymentshosted'),
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

        if ($deposit->btc_amo == 0 || $deposit->btc_wallet == "") {
            $sendto              = $response->pay_address;
            $deposit = Deposit::find($deposit->id);
            $deposit->btc_wallet = $sendto;
            $deposit->btc_amo    = $response->pay_amount;
            $deposit->save();
        }

        $send['amount']   = $deposit->btc_amo;
        $send['sendto']   = $deposit->btc_wallet;
        $send['img']      = hyiplab_crypto_qr($deposit->btc_wallet);
        $send['currency'] = $deposit->method_currency;
        $send['view']     = 'user/payment/crypto';
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
                $gateway    = Gateway::where('alias', 'NowPaymentsHosted')->first();
                $gatewayAcc = json_decode($gateway->gateway_parameters);
                $hmac       = hash_hmac("sha512", $sorted_request_json, trim($gatewayAcc->secret_key->value));
                if ($hmac == $recived_hmac) {
                    if ($request_data['payment_status'] == 'confirmed' || $request_data['payment_status'] == 'finished') {
                        if ($request_data['actually_paid'] == $request_data['pay_amount']) {
                            $deposit = Deposit::where('status', 0)->where('trx', $request_data['order_id'])->first();
                            if ($deposit) {
                                $this->depositService->updateUserData($deposit);
                            }
                        }
                    }
                }
            }
        }
    }
}
