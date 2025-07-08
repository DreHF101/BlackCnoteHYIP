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

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * The main plugin class.
 */
final class Hyiplab_Plugin {

	/**
	 * The single instance of the class.
	 *
	 * @var Hyiplab_Plugin
	 */
	private static $instance = null;

	/**
	 * Main Hyiplab_Plugin Instance.
	 *
	 * Ensures only one instance of Hyiplab_Plugin is loaded or can be loaded.
	 *
	 * @static
	 * @return Hyiplab_Plugin - Main instance.
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong(__FUNCTION__, __('Cloning is forbidden.', 'hyiplab'), '1.0.0');
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong(__FUNCTION__, __('Unserializing instances of this class is forbidden.', 'hyiplab'), '1.0.0');
	}

	/**
	 * Hyiplab_Plugin constructor.
	 */
	public function __construct() {
		try {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		} catch (Exception $e) {
			error_log('HYIPLab Plugin Constructor Error: ' . $e->getMessage());
		}
	}

	/**
	 * Define constants.
	 */
	private function define_constants() {
		try {
			require_once __DIR__ . '/app/Helpers/helpers.php';
			define('HYIPLAB_PLUGIN_VERSION', hyiplab_system_details()['version']);
			define('HYIPLAB_PLUGIN_NAME', hyiplab_system_details()['real_name']);
			define('HYIPLAB_ROOT', plugin_dir_path(__FILE__));
			define('HYIPLAB_PLUGIN_URL', plugin_dir_url(__FILE__));
		} catch (Exception $e) {
			error_log('HYIPLab Plugin Constants Error: ' . $e->getMessage());
		}
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		try {
			require_once __DIR__ . '/vendor/autoload.php';
			include_once ABSPATH . 'wp-includes/pluggable.php';
		} catch (Exception $e) {
			error_log('HYIPLab Plugin Includes Error: ' . $e->getMessage());
		}
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		try {
			$system = hyiplab_system_instance();
			$system->bootMiddleware();
			$system->handleRequestThroughRouter();

			$hook = new Hook();
			$hook->init();

			$activator = new Activator();
			register_activation_hook(__FILE__, [$activator, 'activate']);
			register_deactivation_hook(__FILE__, [$activator, 'deactivate']);
		} catch (Exception $e) {
			error_log('HYIPLab Plugin Error: ' . $e->getMessage());
		}
	}
}

/**
 * Begins execution of the plugin.
 *
 * @return Hyiplab_Plugin
 */
function hyiplab_plugin() {
	return Hyiplab_Plugin::instance();
}

// Get the plugin running.
hyiplab_plugin();
