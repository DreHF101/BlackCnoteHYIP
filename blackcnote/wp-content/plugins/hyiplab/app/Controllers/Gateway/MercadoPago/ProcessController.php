<?php

namespace Hyiplab\Controllers\Gateway\MercadoPago;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
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
        $gatewayCurrency = $gateway;
        $gatewayAcc      = json_decode($gatewayCurrency->gateway_parameter);
        $user            = get_userdata($deposit->user_id);
        $curl            = curl_init();

        $preferenceData = [
            'items' => [
                [
                    'id'          => $deposit->trx,
                    'title'       => esc_html__('Deposit', HYIPLAB_PLUGIN_NAME),
                    'description' => esc_html__('Deposit from ', HYIPLAB_PLUGIN_NAME) . $user->user_login,
                    'quantity'    => 1,
                    'currency_id' => $gatewayCurrency->currency,
                    'unit_price'  => $deposit->final_amo
                ]
            ],
            'payer' => [
                'email' => $user->email,
            ],
            'back_urls' => [
                'success' => hyiplab_route_link('user.deposit.history'),
                'pending' => '',
                'failure' => hyiplab_route_link('user.deposit.index'),
            ],
            'notification_url' => hyiplab_route_link('ipn.mercadopago'),
            'auto_return'      => 'approved',
        ];

        $httpHeader = [
            "Content-Type: application/json",
        ];

        $url  = "https://api.mercadopago.com/checkout/preferences?access_token=" . $gatewayAcc->access_token;
        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode($preferenceData, true),
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => $httpHeader
        ];
        curl_setopt_array($curl, $opts);
        $response = curl_exec($curl);
        $result   = json_decode($response, true);
        $err      = curl_error($curl);
        curl_close($curl);

        if ($result['init_point']) {
            $send['redirect']     = true;
            $send['redirect_url'] = $result['init_point'];
        } else {
            $send['error']   = true;
            $send['message'] = esc_html__('Some problem ocurred with api.', HYIPLAB_PLUGIN_NAME);
        }
        $send['view'] = '';
        return json_encode($send);
    }

    public function ipn()
    {
        $request   = new Request();
        $paymentId = json_decode(json_encode($request->all()))->data->id;
        $gateway   = Gateway::where('alias', 'MercadoPago')->first();
        if (!$gateway) {
            $notify[] = ['error', 'Invalid gateway'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }
        $param      = json_decode($gateway->gateway_parameters);
        $paymentUrl = "https://api.mercadopago.com/v1/payments/" . $paymentId . "?access_token=" . $param->access_token->value;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paymentUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $paymentData = curl_exec($ch);
        curl_close($ch);

        $payment = json_decode($paymentData, true);
        $trx     = $payment['additional_info']['items'][0]['id'];
        $deposit = Deposit::where('trx', $trx)->where('status', 0)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }
        if ($payment['status'] == 'approved' && $deposit) {
            $this->depositService->updateUserData($deposit);
            $notify[] = ['success', 'Payment captured successfully.'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
        }
        $notify[] = ['success', 'Unable to process'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
    }
}
