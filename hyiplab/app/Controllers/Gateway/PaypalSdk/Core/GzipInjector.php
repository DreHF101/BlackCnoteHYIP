<?php

namespace Hyiplab\Controllers\Gateway\PaypalSdk\Core;


use Hyiplab\Controllers\Gateway\PaypalSdk\PayPalHttp\Injector;

class GzipInjector implements Injector
{
    public function inject($httpRequest)
    {
        $httpRequest->headers["Accept-Encoding"] = "gzip";
    }
}
