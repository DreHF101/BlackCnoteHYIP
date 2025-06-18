<?php

namespace Hyiplab\Controllers\Gateway\Paypal;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Lib\CurlRequest;
use Hyiplab\Models\Deposit;

class ProcessController extends Controller
{
    public static function process($deposit, $gateway)
    {
        $credentials          = json_decode($gateway->gateway_parameter);
        $siteName             = get_bloginfo('name');
        $val['cmd']           = '_xclick';
        $val['business']      = trim($credentials->paypal_email);
        $val['cbt']           = $siteName;
        $val['currency_code'] = "$deposit->method_currency";
        $val['quantity']      = 1;
        $val['item_name']     = esc_html__("Payment To ", HYIPLAB_PLUGIN_NAME) . $siteName . esc_html__(" Account", HYIPLAB_PLUGIN_NAME);
        $val['custom']        = "$deposit->trx";
        $val['amount']        = round($deposit->final_amo, 2);
        $val['return']        = hyiplab_route_link('user.deposit.history');
        $val['cancel_return'] = hyiplab_route_link('user.deposit.index');
        $val['notify_url']    = hyiplab_route_link('ipn.paypal');
        $send['val']          = $val;
        $send['view']         = 'user/payment/redirect';
        $send['method']       = 'post';
          // $send['url'] = 'https://www.sandbox.paypal.com/';
        $send['url'] = 'https://www.paypal.com/cgi-bin/webscr';
        return json_encode($send);
    }

    public function ipn()
    {
        $request = new Request();
        $raw_post_data  = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost         = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        $req = 'cmd=_notify-validate';
        foreach ($myPost as $key => $value) {
            $value          = urlencode(stripslashes($value));
            $req           .= "&$key=$value";
            $details[$key]  = $value;
        }

          // $paypalURL = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr?"; // use for sandbox text
        $paypalURL = "https://ipnpb.paypal.com/cgi-bin/webscr?";
        $url       = $paypalURL . $req;
        $response  = CurlRequest::curlContent($url);

        if ($response == "VERIFIED") {
            $deposit        = Deposit::where('trx', sanitize_text_field($request->custom))->orderBy('id', 'DESC')->first();
            $data['detail'] = json_encode($details);
            Deposit::where('id', $deposit->id)->update($data);
            if ($request->mc_gross == $deposit->final_amo && $deposit->status == '0') {
                DepositController::userDataUpdate($deposit);
            }
        }
    }
}
