<?php

namespace Hyiplab\Controllers\Gateway\NowPaymentsCheckout;

use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Gateway;
use Hyiplab\Lib\CurlRequest;
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

        $send['val1'] = $deposit->trx;
        $send['val2'] = $nowPaymentsAcc->api_key;
        $send['val3'] = hyiplab_route('ipn.now_payments_checkout');
        $send['view'] = 'user/payment/redirect';
        return json_encode($send);
    }

    public function ipn()
    {
        $request    = file_get_contents('php://input');
        $request_data = json_decode($request, true);

        if ($request_data['payment_status'] == 'finished') {
            $deposit = Deposit::where('status', 0)->where('trx', $request_data['order_id'])->first();
            if ($deposit) {
                $this->depositService->updateUserData($deposit);
            }
        }
    }
}
