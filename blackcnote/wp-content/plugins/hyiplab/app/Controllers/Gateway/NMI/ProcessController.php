<?php

namespace Hyiplab\Controllers\Gateway\NMI;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
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
        $nmiAcc = json_decode($gateway->gateway_parameter);
        $val['key'] = $nmiAcc->public_key;
        $val['amount'] = round($deposit->final_amo, 2);
        $val['currency'] = $deposit->method_currency;
        $val['track'] = $deposit->trx;
        $send['view'] = 'user/payment/nmi';
        $send['val'] = $val;
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track = hyiplab_session()->get('track');
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        if ($deposit->status == 1) {
            $notify[] = ['error', 'Invalid request'];
            hyiplab_redirect(hyiplab_route('user.deposit.history', false), $notify);
        }
        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
        $nmiAcc = json_decode($gateway->gateway_parameter);

        $request_temp = "security_key=" . $nmiAcc->private_key . "&" . "amount=" . round($deposit->final_amo, 2) . "&" . "type=sale" . "&" . "payment_token=" . $request->payment_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://secure.networkmerchants.com/api/transact.php");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_temp);
        curl_setopt($ch, CURLOPT_POST, 1);
        $result = curl_exec($ch);
        $result = explode("&", $result);
        $result = explode("=", $result[0]);

        if ($result[1] == 1) {
            $this->depositService->updateUserData($deposit);
            $notify[] = ['success', 'Payment captured successfully'];
            hyiplab_redirect(hyiplab_route('user.deposit.history', false), $notify);
        }
        $notify[] = ['error', 'Payment failed'];
        hyiplab_redirect(hyiplab_route('user.deposit.index', false), $notify);
    }
}
