<?php

namespace Hyiplab\Controllers\Gateway\PaypalSdk;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Gateway\PaypalSdk\Core\PayPalHttpClient;
use Hyiplab\Controllers\Gateway\PaypalSdk\Core\ProductionEnvironment;
use Hyiplab\Controllers\Gateway\PaypalSdk\Orders\OrdersCaptureRequest;
use Hyiplab\Controllers\Gateway\PaypalSdk\Orders\OrdersCreateRequest;
use Hyiplab\Controllers\Gateway\PaypalSdk\PayPalHttp\HttpException;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;
use Hyiplab\Controllers\Controller;
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
        $paypalAcc = json_decode($gateway->gateway_parameter);
          // Creating an environment
        $clientId     = $paypalAcc->clientId;
        $clientSecret = $paypalAcc->clientSecret;
        $environment  = new ProductionEnvironment($clientId, $clientSecret);
        $client       = new PayPalHttpClient($environment);
        $request      = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent"         => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => $deposit->trx,
                "amount"       => [
                    "value"         => round($deposit->final_amo, 2),
                    "currency_code" => $deposit->method_currency
                ]
            ]],
            "application_context" => [
                "cancel_url" => hyiplab_route_link('user.deposit.index'),
                "return_url" => hyiplab_route_link('ipn.paypalsdk')
            ]
        ];

        try {
            $response            = $client->execute($request);
            $deposit->btc_wallet = $response->result->id;
            $deposit->save();

            $send['redirect']     = true;
            $send['redirect_url'] = $response->result->links[1]->href;
        } catch (HttpException $ex) {
            $send['error']   = true;
            $send['message'] = esc_html__('Failed to process with api', HYIPLAB_PLUGIN_NAME);
        }
        return json_encode($send);
    }

    public function ipn()
    {
        $req     = new Request();
        $request = new OrdersCaptureRequest($req->token);
        $request->prefer('return=representation');
        try {
            $deposit = Deposit::where('btc_wallet', $req->token)->where('status', 0)->first();
            if (!$deposit) {
                $notify[] = ['error', 'Deposit not found'];
                hyiplab_set_notify($notify);
                hyiplab_request(hyiplab_route_link('user.deposit.index'));
            }
            $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
            if (!$gateway) {
                $notify[] = ['error', 'Gateway not found'];
                hyiplab_set_notify($notify);
                hyiplab_request(hyiplab_route_link('user.deposit.index'));
            }
            $paypalAcc    = json_decode($gateway->gateway_parameter);
            $clientId     = $paypalAcc->clientId;
            $clientSecret = $paypalAcc->clientSecret;
            $environment  = new ProductionEnvironment($clientId, $clientSecret);
            $client       = new PayPalHttpClient($environment);

            $response = $client->execute($request);

            if (in_array($response->result->status, ['COMPLETED', 'APPROVED'])) {
                $data['detail'] = json_encode($response->result->payer);
                Deposit::where('id', $deposit->id)->update($data);
                $this->depositService->updateUserData($deposit);
                $notify[] = ['success', 'Payment captured successfully'];
                hyiplab_set_notify($notify);
                hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
            } else {
                $notify[] = ['error', 'Payment captured failed'];
                hyiplab_set_notify($notify);
                hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
            }
        } catch (HttpException $ex) {
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }
    }
}
