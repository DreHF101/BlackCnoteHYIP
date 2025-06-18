<?php

namespace Hyiplab\Hook;

use Hyiplab\Includes\RegisterAssets;

class LoadAssets{
    private $scope;

    private $styles;
    private $scripts;

    public function __construct($scope)
    {
        $this->scope = $scope;

        $this->styles = RegisterAssets::$styles;
        $this->scripts = RegisterAssets::$scripts;
    }

    public function enqueueScripts()
    {
        if( is_admin() ){
            if(!isset($_GET['page']) || $_GET['page'] !== HYIPLAB_PLUGIN_NAME) {
                return;
            }
        }
        $globalScripts = RegisterAssets::$scripts['global'];
        foreach ($globalScripts as $script) {
            $name = str_replace('.js', '', $script);
            wp_enqueue_script( $name, esc_url(plugin_dir_url('/') .HYIPLAB_PLUGIN_NAME.  "/assets/global/js/".$script), array('jquery'), HYIPLAB_PLUGIN_VERSION, 'all' );
        }

        $scripts = $this->scripts[$this->scope];
        foreach ($scripts as $script) {
            $name = str_replace('.js', '', $script);
            wp_enqueue_script( $name, esc_url(plugin_dir_url('/') .HYIPLAB_PLUGIN_NAME.  "/assets/$this->scope/js/".$script), array('jquery'), HYIPLAB_PLUGIN_VERSION, 'all' );
        }
    }

    public function enqueueStyles()
    {
        if( is_admin() ){
            if(!isset($_GET['page']) || $_GET['page'] !== HYIPLAB_PLUGIN_NAME) {
                return;
            }
        }
        $globalStyles = RegisterAssets::$styles['global'];
        foreach ($globalStyles as $style) {
            $name = str_replace('.css', '', $style);
            wp_enqueue_style( $name, esc_url(plugin_dir_url('/') .HYIPLAB_PLUGIN_NAME.  "/assets/global/css/".$style), array(), HYIPLAB_PLUGIN_VERSION, 'all' );
        }

        $styles = $this->styles[$this->scope];
        foreach ($styles as $style) {
            $name = str_replace('.css', '', $style);
            wp_enqueue_style( $name, esc_url(plugin_dir_url('/') .HYIPLAB_PLUGIN_NAME.  "/assets/$this->scope/css/".$style), array(), HYIPLAB_PLUGIN_VERSION, 'all' );
        }
    }
}