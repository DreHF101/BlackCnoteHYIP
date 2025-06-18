<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://viserlab.com
 * @since             1.0.0
 * @package           Hyiplab
 *
 * @wordpress-plugin
 * Plugin Name:       Hyiplab
 * Plugin URI:        https://viserlab.com/products/wordpress
 * Description:       Premium hyip investment plugin by ViserLab
 * Version:           3.0
 * Author:            Hyiplab
 * Author URI:        https://viserlab.com
 * Text Domain:       hyiplab
 * Domain Path:       /languages
 */

use Hyiplab\Hook\Hook;
use Hyiplab\Includes\Activator;

require_once __DIR__ . '/vendor/autoload.php';

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

define('HYIPLAB_PLUGIN_VERSION', hyiplab_system_details()['version']);
define('HYIPLAB_PLUGIN_NAME', hyiplab_system_details()['real_name']);
define('HYIPLAB_ROOT', plugin_dir_path(__FILE__));
define('HYIPLAB_PLUGIN_URL', plugin_dir_url(__FILE__));

include_once(ABSPATH . 'wp-includes/pluggable.php');

$system = hyiplab_system_instance();
$system->bootMiddleware();
$system->handleRequestThroughRouter();

$hook = new Hook;
$hook->init();

$activator = new Activator();
register_activation_hook(__FILE__, [$activator, 'activate']);
register_deactivation_hook(__FILE__, [$activator, 'deactivate']);
