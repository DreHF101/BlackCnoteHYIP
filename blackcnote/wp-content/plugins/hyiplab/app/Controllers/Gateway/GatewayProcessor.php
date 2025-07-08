<?php

namespace Hyiplab\Controllers\Gateway;

use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;

class GatewayProcessor
{
    public function process(Deposit $deposit, GatewayCurrency $gateway)
    {
        $alias = $gateway->gateway_alias;

        $processorClass = __NAMESPACE__ . '\\' . $alias . '\\ProcessController';

        if (!class_exists($processorClass)) {
            return (object) [
                'error' => true,
                'message' => 'Payment processor not found.',
            ];
        }

        $processor = new $processorClass();
        $result = $processor->process($deposit, $gateway);

        return json_decode($result);
    }
} 