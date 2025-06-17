<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Gateway;
use Hyiplab\Models\GatewayCurrency;

class AutomaticGatewayController extends Controller
{
    public function index()
    {
        $pageTitle = "Automatic Gateways";
        $gateways  = Gateway::where('code', '<', 1000)->orderBy('name', 'asc')->get();
        $this->view('admin/gateway/automatic/list', compact('pageTitle', 'gateways'));
    }

    public function status()
    {
        $request         = new Request();
        $gateway         = Gateway::findOrFail($request->id);
        $gateway->status = $gateway->status ? 0 : 1;
        $gateway->save();
        $notify[] = ['success', 'Status changed successfully'];
        hyiplab_back($notify);
    }

    public function edit()
    {
        $request             = new Request();
        $pageTitle           = 'Edit Gateway Method';
        $gateway             = Gateway::findOrFail($request->id);
        $supportedCurrencies = json_decode($gateway->supported_currencies);
        $parameters          = json_decode($gateway->gateway_parameters);
        $globalCredentials   = [];
        $privateCredentials  = [];
        foreach ($parameters as $key => $value) {
            if ($value->global) {
                $globalCredentials[$key] = $value;
            } else {
                $privateCredentials[$key] = $value;
            }
        }
        $globalParameters    = null;
        $hasCurrencies       = false;
        $currencyIndex       = 1;
        $activatedCurrencies = GatewayCurrency::where('method_code', $gateway->code)->get();
        $this->view('admin/gateway/automatic/edit', compact('pageTitle', 'gateway', 'supportedCurrencies', 'parameters', 'hasCurrencies', 'currencyIndex', 'globalParameters', 'globalCredentials', 'privateCredentials', 'activatedCurrencies'));
    }

    public function update()
    {
        $request = new Request();
        $request->validate([
            'alias' => 'required',
        ]);
        $gateway = Gateway::where('id', intval($request->id))->first();
        if (!$gateway) {
            $notify[] = ['error', 'Gateway not found'];
            hyiplab_back($notify);
        }
        $parameters  = json_decode($gateway->gateway_parameters);
        $credentials = [];
        foreach ($parameters as $key => $value) {
            if ($value->global) {
                $credentials[$key]['value']  = sanitize_text_field($request->global[$key]);
                $credentials[$key]['title']  = $value->title;
                $credentials[$key]['global'] = true;
            } else {
                $credentials[$key]['value']  = $value->value;
                $credentials[$key]['title']  = $value->title;
                $credentials[$key]['global'] = false;
            }
        }

        $credentials = json_encode($credentials);
        Gateway::where('id', intval($request->id))->update([
            'gateway_parameters' => $credentials
        ]);

        if ($request->currency) {
            GatewayCurrency::where('method_code', $gateway->code)->delete();
            foreach ($request->currency as $key => $currency) {
                $param = [];
                foreach ($parameters as $pkey => $pram) {
                    if ($value->global) {
                        $param[$pkey] = $pram->value;
                    } else {
                        $param[$pkey] = $currency['param'][$pkey];
                    }
                }
                $currencyData = [
                    'name'              => sanitize_text_field($currency['name']),
                    'gateway_alias'     => sanitize_text_field($gateway->alias),
                    'currency'          => sanitize_text_field($currency['currency']),
                    'min_amount'        => sanitize_text_field($currency['min_amount']),
                    'max_amount'        => sanitize_text_field($currency['max_amount']),
                    'fixed_charge'      => sanitize_text_field($currency['fixed_charge']),
                    'percent_charge'    => sanitize_text_field($currency['percent_charge']),
                    'rate'              => sanitize_text_field($currency['rate']),
                    'symbol'            => sanitize_text_field($currency['symbol']),
                    'method_code'       => sanitize_text_field($gateway->code),
                    'gateway_parameter' => json_encode($param),
                ];

                GatewayCurrency::insert($currencyData);
            }
        }

        $notify[] = ['success', 'Gateway updated successfully'];
        hyiplab_back($notify);
    }

    public function currencyRemove()
    {
        $request = new Request();
        GatewayCurrency::where('id', $request->id)->delete();
        $notify[] = ['success', 'Gateway currency deleted successfully'];
        hyiplab_back($notify);
    }
}
