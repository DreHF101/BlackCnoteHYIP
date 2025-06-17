<?php

namespace Hyiplab\Controllers\Gateway\NMI;

use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Lib\CurlRequest;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $credentials              = json_decode($gateway->gateway_parameter);
        $xmlRequest               = new \DOMDocument('1.0', 'UTF-8');
        $xmlRequest->formatOutput = true;
        $xmlSale                  = $xmlRequest->createElement('sale');

        self::appendXmlNode($xmlRequest, $xmlSale, 'api-key', $credentials->api_key);
        self::appendXmlNode($xmlRequest, $xmlSale, 'redirect-url', hyiplab_route_link('ipn.nmi'));
        self::appendXmlNode($xmlRequest, $xmlSale, 'amount', $deposit->amount);
        self::appendXmlNode($xmlRequest, $xmlSale, 'currency', $deposit->method_currency);
        self::appendXmlNode($xmlRequest, $xmlSale, 'order-id', $deposit->trx);
        $xmlRequest->appendChild($xmlSale);

        $data = CurlRequest::curlPostContent('https://secure.nmi.com/api/v2/three-step', $xmlRequest->saveXML(), ["Content-type: text/xml"]);

        $gwResponse = new \SimpleXMLElement($data);
        if ((string)$gwResponse->result == 1) {
            $formURL = $gwResponse->{'form-url'};
        } else {
            $send['error']   = true;
            $send['message'] = esc_html__('Something went wrong', HYIPLAB_PLUGIN_NAME);
            return json_encode($send);
        }
        $formURL        = (array)$formURL;
        $formURL        = $formURL[0];
        $send['url']    = $formURL;
        $send['view']   = 'user/payment/' . $gateway->gateway_alias;
        $send['method'] = 'POST';
        return json_encode($send);
    }

    public function ipn()
    {
        $tokenId = $_GET['token-id'];
        $trx     = hyiplab_session()->get('trx');
        $deposit = Deposit::where('trx', $trx)->orderBy('id', 'DESC')->first();
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

        $credentials = json_decode($gateway->gateway_parameter);

        $xmlRequest               = new \DOMDocument('1.0', 'UTF-8');
        $xmlRequest->formatOutput = true;
        $xmlCompleteTransaction   = $xmlRequest->createElement('complete-action');
        self::appendXmlNode($xmlRequest, $xmlCompleteTransaction, 'api-key', $credentials->api_key);
        self::appendXmlNode($xmlRequest, $xmlCompleteTransaction, 'token-id', $tokenId);
        $xmlRequest->appendChild($xmlCompleteTransaction);

        $data       = CurlRequest::curlPostContent('https://secure.nmi.com/api/v2/three-step', $xmlRequest->saveXML(), ["Content-type: text/xml"]);
        $gwResponse = @new \SimpleXMLElement((string)$data);
        if ($gwResponse->result == 1) {
            $deData['detail'] = $gwResponse;
            Deposit::where('id', $deposit->id)->update($deData);
            DepositController::userDataUpdate($deposit);
            $notify[] = ['success', 'Payment captured successfully'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.deposit.history'));
        }
        $notify[] = ['error', 'Something went wrong'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.deposit.index'));
    }

    public static function appendXmlNode($domDocument, $parentNode, $name, $value)
    {
        $childNode      = $domDocument->createElement($name);
        $childNodeValue = $domDocument->createTextNode($value);
        $childNode->appendChild($childNodeValue);
        $parentNode->appendChild($childNode);
    }
}
