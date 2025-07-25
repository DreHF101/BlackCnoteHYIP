<?php

namespace Hyiplab\Controllers\Gateway\PaypalSdk\Core;

use Hyiplab\Controllers\Gateway\PaypalSdk\PayPalHttp\Injector;

class FPTIInstrumentationInjector implements Injector
{
    public function inject($request)
    {
        $request->headers["sdk_name"] = "Checkout SDK";
        $request->headers["sdk_version"] = "1.0.1";
        $request->headers["sdk_tech_stack"] = "PHP " . PHP_VERSION;
        $request->headers["api_integration_type"] = "PAYPALSDK";
    }
}
