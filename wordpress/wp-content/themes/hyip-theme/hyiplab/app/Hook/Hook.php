<?php

namespace Hyiplab\Hook;

use Hyiplab\Controllers\ActivationController;
use Hyiplab\Hook\AdminMenu;
use Hyiplab\Lib\VerifiedPlugin;

class Hook
{

    public function init()
    {
        add_action('admin_menu', [new AdminMenu, 'menuSetting']);
        add_action('init', [new ExecuteRouter, 'execute']);
        add_filter('template_include', [new ExecuteRouter, 'includeTemplate'], 1000, 1);
        add_action('query_vars', [new ExecuteRouter, 'setQueryVar']);

        $loadAssets = new LoadAssets('admin');
        add_action('admin_enqueue_scripts', [$loadAssets, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [$loadAssets, 'enqueueStyles']);

        $loadAssets = new LoadAssets('public');
        add_action('wp_enqueue_scripts', [$loadAssets, 'enqueueScripts']);
        add_action('wp_enqueue_scripts', [$loadAssets, 'enqueueStyles']);

        if (VerifiedPlugin::check()) {
            $this->authHooks();
        }

        add_action('plugin_loaded', function () {
            load_plugin_textdomain(
                HYIPLAB_PLUGIN_NAME,
                false,
                dirname(dirname(dirname(plugin_basename(__FILE__)))) . '/languages'
            );
        });

        add_action('wp_dashboard_setup', function () {
            $widget = new Widget();
            $widget->loadWidget();
        });

        add_filter('admin_body_class', function ($classes) {
            if (isset($_GET['page']) && $_GET['page'] == HYIPLAB_PLUGIN_NAME) {
                $classes .= ' vl-admin';
            }
            return $classes;
        });

        add_action('init', function () {
            ob_start();
        });

        add_filter('redirect_canonical', [$this, 'no_redirect_on_404']);

        add_filter('cron_schedules', [$this,'hyiplab_cron_schedules']);
        if (!wp_next_scheduled('hyiplab_cron_action')) {
            wp_schedule_event(time(), 'hyiplab_prune', 'hyiplab_cron_action');
        }
        add_action('hyiplab_cron_action', [new Cron,'cron']);
        add_action('hyiplab_cron_action', [new Cron,'rank']);
        add_action('hyiplab_cron_action', [new Cron,'investSchedule']);
        add_action('hyiplab_cron_action', [new Cron,'staking']);

        // add_action('admin_init',[new Demo, 'protectPost']);
        // add_filter( 'plugin_action_links',[new Demo, 'disablePluginDeactivation'], 10, 4 );

        add_action('admin_init',function(){
            if (!VerifiedPlugin::check()) {
                add_action('admin_notices', function(){
                    $activationUrl = hyiplab_route_link('plugin.activation');
                    echo "<div class='notice notice-error is-dismissible'><p><strong>".esc_html__('Please',HYIPLAB_PLUGIN_NAME)." <a href='$activationUrl'>".esc_html__('activate',HYIPLAB_PLUGIN_NAME)."</a> ".esc_html__('the '.HYIPLAB_PLUGIN_NAME.' Plugin',HYIPLAB_PLUGIN_NAME)."</strong></p></div>";
                });
            } 
        });

        add_action('wp_ajax_active_plugin',function(){
            $controller = new ActivationController;
            $controller->activationSubmit();
        });

        add_action('admin_enqueue_scripts', function(){
            wp_enqueue_style( 'global_admin', esc_url(plugin_dir_url('/') .HYIPLAB_PLUGIN_NAME.  "/assets/admin/css/global_admin.css"), array(), HYIPLAB_PLUGIN_VERSION, 'all' );
        });

        add_shortcode('hyiplab_plans', 'hyiplabPlansShortCode');

    }

    public function authHooks()
    {
        $authorization = new Authorization;
        add_action('after_setup_theme', [$authorization, 'removeAdminBar']);
        add_action('admin_init', [$authorization, 'redirectHome'], 1);
        add_action('login_init', [$authorization, 'restrictWpLogin'], 1);
        add_filter('login_url', [$authorization, 'redirectLogin'], 10, 2);
        add_action('wp_login_failed', [$authorization, 'authFailed']);
        add_filter('authenticate', [$authorization, 'authenticate'], 20, 3);
        add_filter('wp_authenticate_user', [$authorization, 'verifyUser'], 1);
        add_action('edit_user_profile', [$authorization, 'userProfile']);
        add_action('edit_user_profile_update', [$authorization, 'updateUserProfile']);
    }

    public function no_redirect_on_404($redirect_url)
    {
        if (is_404()) {
            return false;
        }
        return $redirect_url;
    }

    public function hyiplab_cron_schedules($schedules)
    {
        $prune_duration = 240;
        $schedules['hyiplab_prune'] = array(
            'interval' => $prune_duration,
            'display'  => 'Prune Duration'
        );
        return $schedules;
    }
}
