<?php

namespace Hyiplab\Controllers\Gateway\Authorize;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\controller\CreateTransactionController;
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
        $alias          = $gateway->gateway_alias;
        $send['track']  = $deposit->trx;
        $send['view']   = 'user/payment/' . $alias;
        $send['method'] = 'post';
        $send['url']    = hyiplab_route_link('ipn.authorize');
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

        $trx = hyiplab_session()->get('trx');
        $deposit = Deposit::where('status', 0)->where('trx', $trx)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            $notify[] = ['error', 'Deposit not found'];
            hyiplab_back($notify);
        }

        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)->where('currency', $deposit->method_currency)->first();
        if (!$gateway) {
            $notify[] = ['error', 'Invalid gateway'];
            hyiplab_back($notify);
        }

        $cardNumber  = str_replace(' ', '', sanitize_text_field($request->cardNumber));
        $exp         = str_replace(' ', '', sanitize_text_field($request->cardExpiry));
        $credentials = json_decode($gateway->gateway_parameter);

        $merchantAuthentication = new MerchantAuthenticationType();
        $merchantAuthentication->setName($credentials->login_id);
        $merchantAuthentication->setTransactionKey($credentials->transaction_key);

          // Create the payment data for a credit card
        $creditCard = new CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($exp);

        $paymentOne = new PaymentType();
        $paymentOne->setCreditCard($creditCard);

          // Create a transaction
        $transactionRequestType = new TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($deposit->final_amo);
        $transactionRequestType->setPayment($paymentOne);

        $transactionRequest = new CreateTransactionRequest();
        $transactionRequest->setMerchantAuthentication($merchantAuthentication);
        $transactionRequest->setRefId($deposit->trx);
        $transactionRequest->setTransactionRequest($transactionRequestType);

        $controller = new CreateTransactionController($transactionRequest);
        $response   = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
        $response   = $response->getTransactionResponse();

        if (($response != null) && ($response->getResponseCode() == "1")) {
            $this->depositService->updateUserData($deposit);
            $notify[] = ['success', 'Payment captured successfully'];
            hyiplab_set_notify($notify);
            return hyiplab_redirect(hyiplab_route('user.deposit.history', false));
        }

        $notify[] = ['error', 'Something went wrong'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
    }
}
