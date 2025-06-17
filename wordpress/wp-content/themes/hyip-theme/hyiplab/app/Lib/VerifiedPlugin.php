<?php

namespace Hyiplab\Lib;

class VerifiedPlugin{
    public static function check()
    {
        $fileExists = file_exists(HYIPLAB_ROOT.'/viser.json');
        if (!$fileExists || get_option(HYIPLAB_PLUGIN_NAME.'_activated') != 1 || get_option(HYIPLAB_PLUGIN_NAME.'_maintenance_mode') == 9) {
            return false;
        }
        return true;
    }
}