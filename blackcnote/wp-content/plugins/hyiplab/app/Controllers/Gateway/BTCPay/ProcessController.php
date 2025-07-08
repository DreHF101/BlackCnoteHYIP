<?php

namespace Hyiplab\Controllers\Gateway\BTCPay;

use Hyiplab\Controllers\Controller;
use BTCPayServer\Client\Invoice;
use BTCPayServer\Util\PreciseNumber;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Gateway;
use Hyiplab\Services\DepositService;
use Hyiplab\Models\GatewayCurrency;

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
        $gateway = GatewayCurrency::where('gateway_alias', 'BTCPay')->first();
        $btcPayAcc = json_decode($gateway->gateway_parameter);
        $client = new \BTCPayServer\Client\Invoice($btcPayAcc->server_url, $btcPayAcc->api_key);
        $raw_post_data = file_get_contents('php://input');
        if ($raw_post_data) {
            $payload = json_decode($raw_post_data);
            if (!empty($payload) && \BTCPayServer\Client\Webhook::isIncomingWebhookRequestValid($raw_post_data, $_SERVER['BTCPAY-SIG'], $btcPayAcc->secret)) {
                $deposit = Deposit::where('trx', $payload->invoiceId)->first();
                $data = $client->getInvoice($btcPayAcc->store_id, $deposit->trx);
                if ($data->getData()['status'] == 'complete' || $data->getData()['status'] == 'confirmed') {
                    $this->depositService->updateUserData($deposit);
                }
            }
        }
    }
}
