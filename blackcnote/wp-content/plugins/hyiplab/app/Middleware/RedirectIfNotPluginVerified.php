<?php

namespace Hyiplab\Middleware;

use Hyiplab\Lib\VerifiedPlugin;

class RedirectIfNotPluginVerified
{
    public function filterRequest()
    {
        // Use comprehensive debug system if available
        if (function_exists('blackcnote_log_hyiplab')) {
            blackcnote_log_hyiplab('RedirectIfNotPluginVerified::filterRequest() called', 'DEBUG');
            blackcnote_log_hyiplab('Current URL = ' . $_SERVER['REQUEST_URI'], 'DEBUG');
            blackcnote_log_hyiplab('Home URL = ' . home_url(), 'DEBUG');
        } else {
            // Fallback to original debug logging
            error_log('HYIPLab Debug: RedirectIfNotPluginVerified::filterRequest() called');
            error_log('HYIPLab Debug: Current URL = ' . $_SERVER['REQUEST_URI']);
            error_log('HYIPLab Debug: Home URL = ' . home_url());
        }
        
        if (!VerifiedPlugin::check()) {
            if (function_exists('blackcnote_log_hyiplab')) {
                blackcnote_log_hyiplab('Plugin verification failed, redirecting to activation page', 'WARNING');
                blackcnote_log_hyiplab('Redirect URL = ' . home_url(HYIPLAB_PLUGIN_NAME.'-activation'), 'DEBUG');
            } else {
                error_log('HYIPLab Debug: Plugin verification failed, redirecting to activation page');
                error_log('HYIPLab Debug: Redirect URL = ' . home_url(HYIPLAB_PLUGIN_NAME.'-activation'));
            }
            hyiplab_redirect(home_url(HYIPLAB_PLUGIN_NAME.'-activation'));
        } else {
            if (function_exists('blackcnote_log_hyiplab')) {
                blackcnote_log_hyiplab('Plugin verification passed, continuing with request', 'DEBUG');
            } else {
                error_log('HYIPLab Debug: Plugin verification passed, continuing with request');
            }
        }
    }
}