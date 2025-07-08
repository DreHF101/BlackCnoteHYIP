<?php

namespace Hyiplab\Controllers\Gateway\Instamojo;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;
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
        $instaMojoAcc = json_decode($gateway->gateway_parameter);
        $user = get_userdata($deposit->user_id);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "X-Api-Key:$instaMojoAcc->api_key",
                "X-Auth-Token:$instaMojoAcc->auth_token"
            )
        );
        $payload = array(
            'purpose'                 => esc_html__('Payment to ', HYIPLAB_PLUGIN_NAME) . get_bloginfo('name'),
            'amount'                  => round($deposit->final_amo, 2),
            'buyer_name'              => $user->user_login,
            'redirect_url'            => hyiplab_route_link('user.deposit.index'),
            'webhook'                 => hyiplab_route_link('ipn.instamojo'),
            'email'                   => sanitize_email($user->user_email),
            'send_email'              => true,
            'allow_repeated_payments' => false
        );

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($response);
        if (isset($res->success) && $res->success) {
            if (!$res->payment_request->id) {
                $send['error']   = true;
                $send['message'] = esc_html__("Response not given from API. Please re-check the API credentials.", HYIPLAB_PLUGIN_NAME);
            } else {
                $data['btc_wallet'] = $res->payment_request->id;
                Deposit::where('id', $deposit->id)->update($data);
                $send['redirect']     = true;
                $send['redirect_url'] = $res->payment_request->longurl;
            }
        } else {
            $send['error']   = true;
            $send['message'] = esc_html__("Credentials mismatch. Please contact with admin", HYIPLAB_PLUGIN_NAME);
        }
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request();
        $deposit = Deposit::where('btc_wallet', sanitize_text_field($request->payment_request_id))->orderBy('id', 'DESC')->first();
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
        $instaMojoAcc = json_decode($gateway->gateway_parameter);
        $imData       = $_POST;
        $macSent      = $imData['mac'];
        unset($imData['mac']);
        ksort($imData, SORT_STRING | SORT_FLAG_CASE);
        $mac = hash_hmac("sha1", implode("|", $imData), $instaMojoAcc->salt);
        if ($macSent == $mac && $imData['status'] == "Credit" && $deposit->status == '0') {
            $this->depositService->updateUserData($deposit);
        }
    }
}
