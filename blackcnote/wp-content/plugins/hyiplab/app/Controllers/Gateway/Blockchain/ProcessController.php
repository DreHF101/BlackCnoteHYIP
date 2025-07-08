<?php

namespace Hyiplab\Controllers\Gateway\Blockchain;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Lib\CurlRequest;
use Hyiplab\Models\Deposit;
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
        $blockchainAcc = json_decode($gateway->gateway_parameter);

        $url       = "https://blockchain.info/ticker";
        $response  = CurlRequest::curlContent($url);
        $res       = json_decode($response);
        $btcrate   = $res->USD->last ?? 0;
        $usd       = $deposit->final_amo;
        $btcamount = $usd / $btcrate;
        $btc       = round($btcamount, 8);

        if ($deposit->btc_amo == 0 || $deposit->btc_wallet == "") {

            $blockchain_receive_root = "https://api.blockchain.info/";
            $secret                  = "MySecret";
            $my_xpub                 = trim($blockchainAcc->xpub_code);
            $my_api_key              = trim($blockchainAcc->api_key);
            $invoice_id              = $deposit->trx;
            $callback_url            = hyiplab_route_link('ipn.blockchain') . "?invoice_id=" . $invoice_id . "&secret=" . $secret;
            $url                     = $blockchain_receive_root . "v2/receive?key=" . $my_api_key . "&callback=" . urlencode($callback_url) . "&xpub=" . $my_xpub;
            $response                = CurlRequest::curlContent($url);
            $response                = json_decode($response);

            if (isset($response->address) && $response->address == '') {
                $send['error']   = true;
                $send['message'] = esc_html__('BLOCKCHAIN API HAVING ISSUE. PLEASE TRY LATER. ' . $response->message, HYIPLAB_PLUGIN_NAME);
            } else {
                $sendto             = $response->address;
                $data['btc_wallet'] = $sendto;
                $data['btc_amo']    = $btc;
                Deposit::where('id', $deposit->id)->update($data);
            }

            $deposit          = Deposit::where('trx', $deposit->trx)->orderBy('id', 'DESC')->first();
            $send['amount']   = $deposit->btc_amo;
            $send['sendto']   = $deposit->btc_wallet;
            $send['img']      = hyiplab_crypto_qr($deposit->btc_wallet);
            $send['currency'] = "BTC";
            $send['view']     = 'user/payment/crypto';
            return json_encode($send);
        }
    }

    public function ipn()
    {
        $request      = new Request();
        $track        = sanitize_text_field($request->invoice_id);
        $value_in_btc = sanitize_text_field($request->value) / 100000000;
        $deposit      = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        foreach ($_GET as $key => $value) {
            $details[$key] = $value;
        }

        $data['details'] = json_encode($details);
        Deposit::where('id', $deposit->id)->update($data);

        if ($deposit->btc_amo == $value_in_btc && $request->address == $deposit->btc_wallet && $request->secret == "MySecret" && $request->confirmations > 2 && $deposit->status == 0) {
            $this->depositService->updateUserData($deposit);
        }
    }
}
