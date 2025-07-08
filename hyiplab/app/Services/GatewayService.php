<?php

namespace Hyiplab\Services;

use Hyiplab\Models\Gateway;
use Hyiplab\Models\GatewayCurrency;
use Hyiplab\Lib\FormProcessor;

class GatewayService
{
    public function updateGateway(int $gatewayId, array $data): bool
    {
        $gateway = Gateway::findOrFail($gatewayId);
        $parameters = json_decode($gateway->gateway_parameters, true);
        $credentials = [];

        foreach ($parameters as $key => $value) {
            $credentials[$key] = $value; // Preserve existing values
            if (isset($data['global'][$key])) {
                $credentials[$key]['value'] = sanitize_text_field($data['global'][$key]);
            }
        }

        $gateway->gateway_parameters = json_encode($credentials);
        $gateway->alias = sanitize_text_field($data['alias']);
        $gateway->save();

        if (isset($data['currency'])) {
            $this->syncGatewayCurrencies($gateway, $data['currency']);
        }

        return true;
    }

    protected function syncGatewayCurrencies(Gateway $gateway, array $currenciesData): void
    {
        $existingCurrencies = GatewayCurrency::where('method_code', $gateway->code)->get()->keyBy('id');
        $updatedCurrencyIds = [];

        foreach ($currenciesData as $currencyData) {
            $currencyId = $currencyData['id'] ?? null;
            $updatedCurrencyIds[] = $currencyId;

            $gatewayParameters = json_decode($gateway->gateway_parameters, true);
            $param = [];
            foreach ($gatewayParameters as $pkey => $pram) {
                if ($pram['global']) {
                    $param[$pkey] = $pram['value'];
                } else {
                    $param[$pkey] = $currencyData['param'][$pkey] ?? null;
                }
            }
            
            $payload = [
                'name'              => sanitize_text_field($currencyData['name']),
                'gateway_alias'     => $gateway->alias,
                'currency'          => sanitize_text_field($currencyData['currency']),
                'min_amount'        => (float) $currencyData['min_amount'],
                'max_amount'        => (float) $currencyData['max_amount'],
                'fixed_charge'      => (float) $currencyData['fixed_charge'],
                'percent_charge'    => (float) $currencyData['percent_charge'],
                'rate'              => (float) $currencyData['rate'],
                'symbol'            => sanitize_text_field($currencyData['symbol']),
                'method_code'       => $gateway->code,
                'gateway_parameter' => json_encode($param),
            ];

            GatewayCurrency::updateOrCreate(['id' => $currencyId], $payload);
        }

        // Delete currencies that were not in the request
        $currenciesToDelete = $existingCurrencies->whereNotIn('id', array_filter($updatedCurrencyIds));
        foreach ($currenciesToDelete as $currency) {
            $currency->delete();
        }
    }

    public function createManualGateway(array $data): Gateway
    {
        $methodCode = $this->generateManualMethodCode();
        $form = (new FormProcessor())->generate('manual_deposit');
        
        $gateway = new Gateway();
        $this->saveManualGatewayData($gateway, $data, $methodCode, $form->id ?? 0);

        return $gateway;
    }

    public function updateManualGateway(int $gatewayId, array $data): Gateway
    {
        $gateway = Gateway::findOrFail($gatewayId);
        $formProcessor = new FormProcessor();
        
        if ($gateway->form_id) {
            $form = $formProcessor->generate('manual_deposit', true, 'id', $gateway->form_id);
        } else {
            $form = $formProcessor->generate('manual_deposit');
        }
        
        $this->saveManualGatewayData($gateway, $data, $gateway->code, $form->id ?? 0);
        
        return $gateway;
    }

    protected function saveManualGatewayData(Gateway $gateway, array $data, int $methodCode, int $formId): void
    {
        $gateway->code = $methodCode;
        $gateway->form_id = $formId;
        $gateway->name = sanitize_text_field($data['name']);
        $gateway->alias = strtolower(trim(str_replace(' ', '_', $data['name'])));
        $gateway->gateway_parameters = json_encode([]);
        $gateway->supported_currencies = json_encode([]);
        $gateway->crypto = 0;
        $gateway->description = balanceTags(wp_kses($data['instruction'], hyiplab_allowed_html()));
        $gateway->status = 1;
        $gateway->save();

        GatewayCurrency::updateOrCreate(
            ['method_code' => $methodCode],
            [
                'name'           => sanitize_text_field($data['name']),
                'gateway_alias'  => $gateway->alias,
                'currency'       => sanitize_text_field($data['currency']),
                'min_amount'     => (float) $data['min_limit'],
                'max_amount'     => (float) $data['max_limit'],
                'fixed_charge'   => (float) $data['fixed_charge'],
                'percent_charge' => (float) $data['percent_charge'],
                'rate'           => (float) $data['rate'],
                'symbol'         => '',
            ]
        );
    }

    protected function generateManualMethodCode(): int
    {
        $lastMethod = Gateway::where('code', '>=', 1000)->orderBy('id', 'desc')->first();
        return $lastMethod ? $lastMethod->code + 1 : 1000;
    }
} 