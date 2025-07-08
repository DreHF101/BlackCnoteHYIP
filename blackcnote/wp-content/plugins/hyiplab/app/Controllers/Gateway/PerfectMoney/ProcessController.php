<?php

namespace Hyiplab\Controllers\Gateway\PerfectMoney;

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
        $perfectAcc            = json_decode($gateway->gateway_parameter);
        $user                  = get_userdata($deposit->user_id);
        $val['PAYEE_ACCOUNT']  = trim($perfectAcc->wallet_id);
        $val['PAYEE_NAME']     = get_bloginfo('name');
        $val['PAYMENT_ID']     = "$deposit->trx";
        $val['PAYMENT_AMOUNT'] = round($deposit->final_amo, 2);
        $val['PAYMENT_UNITS']  = "$deposit->method_currency";

        $val['STATUS_URL']           = hyiplab_route_link('ipn.perfectmoney');
        $val['PAYMENT_URL']          = hyiplab_route_link('user.deposit.history');
        $val['PAYMENT_URL_METHOD']   = 'POST';
        $val['NOPAYMENT_URL']        = hyiplab_route_link('user.deposit.index');
        $val['NOPAYMENT_URL_METHOD'] = 'POST';
        $val['SUGGESTED_MEMO']       = $user->user_login;
        $val['BAGGAGE_FIELDS']       = 'IDENT';

        $send['val']    = $val;
        $send['view']   = 'user/payment/redirect';
        $send['method'] = 'post';
        $send['url']    = 'https://perfectmoney.is/api/step1.asp';
        return json_encode($send);
    }

    public function ipn()
    {
        $request    = new Request();
        $deposit    = Deposit::where('trx', $request->PAYMENT_ID)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }
        $gateway    = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
        if (!$gateway) {
            $notify[] = ['error', 'Invalid gateway'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
        }
        $pmAcc      = json_decode($gateway->gateway_parameter);
        $passphrase = strtoupper(md5($pmAcc->passphrase));

        define('ALTERNATE_PHRASE_HASH', $passphrase);
        define('PATH_TO_LOG', '/somewhere/out/of/document_root/');
        $string = 
            $request->PAYMENT_ID . ':' . $request->PAYEE_ACCOUNT . ':' .
            $request->PAYMENT_AMOUNT . ':' . $request->PAYMENT_UNITS . ':' .
            $request->PAYMENT_BATCH_NUM . ':' .
            $request->PAYER_ACCOUNT . ':' . ALTERNATE_PHRASE_HASH . ':' .
            $request->TIMESTAMPGMT;

        $hash  = strtoupper(md5($string));
        $hash2 = $request->V2_HASH;

        if ($hash == $hash2) {

            $details = [];

            foreach ($_POST as $key => $value) {
                $details[$key] = $value;
            }
            $data['detail'] = json_encode($details);
            Deposit::where('id', $deposit->id)->update($data);

            $amo   = $request->PAYMENT_AMOUNT;
            $unit  = $request->PAYMENT_UNITS;
            $track = $request->PAYMENT_ID;
            if ($request->PAYEE_ACCOUNT == $pmAcc->wallet_id && $unit == $deposit->method_currency && $amo == round($deposit->final_amo, 2) && $deposit->status == '0') {
                $this->depositService->updateUserData($deposit);
            }
        }
    }
}
