<?php

namespace Hyiplab\Middleware;

use Hyiplab\Lib\VerifiedPlugin;

class RedirectIfNotPluginVerified
{
    public function filterRequest()
    {
        if (!VerifiedPlugin::check()) {
            hyiplab_redirect(home_url(HYIPLAB_PLUGIN_NAME.'-activation'));
        }
    }
}