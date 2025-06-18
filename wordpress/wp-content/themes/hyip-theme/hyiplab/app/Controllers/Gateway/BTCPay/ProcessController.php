<?php

namespace Hyiplab\Controllers\Gateway\BTCPay;

use Hyiplab\Controllers\Controller;
use BTCPayServer\Client\Invoice;
use BTCPayServer\Util\PreciseNumber;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Gateway;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $btcPay = json_decode($gateway->gateway_parameter);
        $client = new Invoice($btcPay->server_name, $btcPay->api_key);

        try {
            $amount  = PreciseNumber::parseFloat($deposit->amount);
            $invoice = $client->createInvoice(
                $btcPay->store_id,
                hyiplab_currency('text'),
                $amount,
                $deposit->trx
            );

            $deposit = Deposit::find($deposit->id);
            $deposit->btc_wallet = $invoice->getData()['id'];
            $deposit->detail = json_encode($invoice->getData());
            $deposit->save();

            $send['redirect']     = true;
            $send['redirect_url'] = $invoice['checkoutLink'];
        } catch (\Throwable $e) {
            $send['error']     = true;
            $send['message'] = $e->getMessage();;
        }
        return json_encode($send);
    }

    public function ipn()
    {
        $rawPostData = file_get_contents("php://input");

        if ($rawPostData) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'btcpay-sig') {
                    $signature = $value;
                }
            }

            $gateway = Gateway::where('alias', 'BTCPay')->first();
            $gatewayParameters = json_decode($gateway->gateway_parameters);

            try {
                $postData = json_decode($rawPostData, false, 512, JSON_THROW_ON_ERROR);
                $deposit = Deposit::where('btc_wallet', $postData->invoiceId)->where('status', 0)->first();
                if ($deposit) {
                    DepositController::userDataUpdate($deposit);
                }
            } catch (\Throwable $e) {
            }
        }
    }
}
