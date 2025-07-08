<?php

namespace Hyiplab\Controllers\Gateway\Stripe;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;
use Hyiplab\Controllers\User\DepositController;
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
        $send['track']  = $deposit->trx;
        $send['view']   = 'user/payment/' . $gateway->gateway_alias;
        $send['method'] = 'post';
        $send['url']    = hyiplab_route_link('ipn.stripe');
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request();
        $request->validate([
            'cardNumber' => 'required',
            'cardExpiry' => 'required',
            'cardCVC'    => 'required',
        ]);
        $trx     = hyiplab_session()->get('trx');
        $deposit = Deposit::where('status', 0)->where('trx', $trx)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found'];
            hyiplab_set_notify($notify);
            hyiplab_back();
        }

        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
        if (!$gateway) {
            $notify[] = ['error', 'Invalid gateway'];
            hyiplab_set_notify($notify);
            hyiplab_back();
        }

        $credentials = json_decode($gateway->gateway_parameter);
        $cc          = sanitize_text_field($request->cardNumber);
        $exp         = sanitize_text_field($request->cardExpiry);
        $cvc         = sanitize_text_field($request->cardCVC);

        $exp  = explode("/", $request->cardExpiry);
        $emo  = trim($exp[0]);
        $eyr  = trim($exp[1]);
        $cnts = round($deposit->final_amo, 2) * 100;

        Stripe::setApiKey($credentials->secret_key);
        Stripe::setApiVersion("2020-03-02");
        
        try {
            $token = Token::create(array(
                "card" => array(
                    "number"    => "$cc",
                    "exp_month" => $emo,
                    "exp_year"  => $eyr,
                    "cvc"       => "$cvc"
                )
            ));
            try {
                $charge = Charge::create(array(
                    'card'        => $token['id'],
                    'currency'    => $deposit->method_currency,
                    'amount'      => $cnts,
                    'description' => 'item',
                ));

                if ($charge['status'] == 'succeeded') {
                    $this->depositService->updateUserData($deposit);
                    $notify[] = ['success', 'Payment captured successfully'];
                    hyiplab_set_notify($notify);
                    hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
                }
            } catch (\Exception $e) {
                $notify[] = ['error', esc_html($e->getMessage())];
                hyiplab_set_notify($notify);
            }
        } catch (\Exception $e) {
            $notify[] = ['error', esc_html($e->getMessage())];
            hyiplab_set_notify($notify);
        }
        hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
    }
}
