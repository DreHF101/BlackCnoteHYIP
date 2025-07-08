<?php

namespace Hyiplab\Lib;

class VerifiedPlugin{
    public static function check()
    {
        // Use comprehensive debug system if available
        if (function_exists('blackcnote_log_hyiplab')) {
            blackcnote_log_hyiplab('VerifiedPlugin::check() called', 'DEBUG');
            blackcnote_log_hyiplab('HYIPLAB_ROOT = ' . HYIPLAB_ROOT, 'DEBUG');
            blackcnote_log_hyiplab('HYIPLAB_PLUGIN_NAME = ' . HYIPLAB_PLUGIN_NAME, 'DEBUG');
        } else {
            // Fallback to original debug logging
            error_log('HYIPLab Debug: VerifiedPlugin::check() called');
            error_log('HYIPLab Debug: HYIPLAB_ROOT = ' . HYIPLAB_ROOT);
            error_log('HYIPLab Debug: HYIPLAB_PLUGIN_NAME = ' . HYIPLAB_PLUGIN_NAME);
        }
        
        $fileExists = file_exists(HYIPLAB_ROOT.'/viser.json');
        
        if (function_exists('blackcnote_log_hyiplab')) {
            blackcnote_log_hyiplab('viser.json file exists = ' . ($fileExists ? 'YES' : 'NO'), 'DEBUG');
        } else {
            error_log('HYIPLab Debug: viser.json file exists = ' . ($fileExists ? 'YES' : 'NO'));
        }
        
        if ($fileExists) {
            $viserContent = file_get_contents(HYIPLAB_ROOT.'/viser.json');
            if (function_exists('blackcnote_log_hyiplab')) {
                blackcnote_log_hyiplab('viser.json content = ' . $viserContent, 'DEBUG');
            } else {
                error_log('HYIPLab Debug: viser.json content = ' . $viserContent);
            }
        }
        
        $activated = get_option(HYIPLAB_PLUGIN_NAME.'_activated');
        if (function_exists('blackcnote_log_hyiplab')) {
            blackcnote_log_hyiplab('hyiplab_activated option = ' . var_export($activated, true), 'DEBUG');
        } else {
            error_log('HYIPLab Debug: hyiplab_activated option = ' . var_export($activated, true));
        }
        
        $maintenance = get_option(HYIPLAB_PLUGIN_NAME.'_maintenance_mode');
        if (function_exists('blackcnote_log_hyiplab')) {
            blackcnote_log_hyiplab('hyiplab_maintenance_mode option = ' . var_export($maintenance, true), 'DEBUG');
        } else {
            error_log('HYIPLab Debug: hyiplab_maintenance_mode option = ' . var_export($maintenance, true));
        }
        
        $purchaseCode = get_option(HYIPLAB_PLUGIN_NAME.'_purchase_code');
        if (function_exists('blackcnote_log_hyiplab')) {
            blackcnote_log_hyiplab('hyiplab_purchase_code option = ' . var_export($purchaseCode, true), 'DEBUG');
        } else {
            error_log('HYIPLab Debug: hyiplab_purchase_code option = ' . var_export($purchaseCode, true));
        }
        
        if (!$fileExists || $activated != 1 || $maintenance == 9) {
            $failure_reason = 'File exists: ' . ($fileExists ? 'YES' : 'NO') . ', Activated: ' . ($activated == 1 ? 'YES' : 'NO') . ', Maintenance: ' . ($maintenance == 9 ? 'YES' : 'NO');
            
            if (function_exists('blackcnote_log_hyiplab')) {
                blackcnote_log_hyiplab('Verification FAILED - ' . $failure_reason, 'ERROR');
            } else {
                error_log('HYIPLab Debug: Verification FAILED - ' . $failure_reason);
            }
            return false;
        }
        
        if (function_exists('blackcnote_log_hyiplab')) {
            blackcnote_log_hyiplab('Verification PASSED - Plugin is activated', 'INFO');
        } else {
            error_log('HYIPLab Debug: Verification PASSED - Plugin is activated');
        }
        return true;
    }
}