<?php
/**
 * Plugin Name: BlackCnote Debug System
 * Plugin URI: https://blackcnote.com
 * Description: Advanced debugging and monitoring system for BlackCnote WordPress platform. Provides 24/7 monitoring, file change detection, system health checks, and Prometheus metrics export.
 * Version: 1.0.0
 * Author: BlackCnote Team
 * Author URI: https://blackcnote.com
 * Text Domain: blackcnote-debug
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BLACKCNOTE_DEBUG_VERSION', '1.0.0');
define('BLACKCNOTE_DEBUG_PLUGIN_FILE', __FILE__);
define('BLACKCNOTE_DEBUG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BLACKCNOTE_DEBUG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BLACKCNOTE_DEBUG_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main BlackCnote Debug System Plugin Class
 */
final class BlackCnoteDebugSystem {
    
    /**
     * Single instance of the plugin
     */
    private static $instance = null;
    
    /**
     * Debug system instance
     */
    private $debug_system = null;
    
    /**
     * Cursor AI Monitor instance
     */
    private $cursor_ai_monitor = null;
    
    /**
     * Get single instance
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
        // Initialize Cursor AI Monitor
        $this->cursor_ai_monitor = new BlackCnoteCursorAIMonitor($this->get_debug_system());
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', [$this, 'init']);
        add_action('admin_init', [$this, 'admin_init']);
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('wp_ajax_blackcnote_debug_metrics', [$this, 'ajax_metrics']);
        add_action('wp_ajax_blackcnote_debug_status', [$this, 'ajax_status']);
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        
        // Add settings link
        add_filter('plugin_action_links_' . BLACKCNOTE_DEBUG_PLUGIN_BASENAME, [$this, 'plugin_action_links']);
    }
    
    /**
     * Load dependencies
     */
    private function load_dependencies() {
        require_once BLACKCNOTE_DEBUG_PLUGIN_DIR . 'includes/class-blackcnote-debug-system.php';
        require_once BLACKCNOTE_DEBUG_PLUGIN_DIR . 'includes/class-blackcnote-debug-admin.php';
        require_once BLACKCNOTE_DEBUG_PLUGIN_DIR . 'includes/class-blackcnote-debug-metrics.php';
        require_once BLACKCNOTE_DEBUG_PLUGIN_DIR . 'includes/class-blackcnote-debug-health.php';
        require_once BLACKCNOTE_DEBUG_PLUGIN_DIR . 'includes/class-blackcnote-debug-rest.php';
        require_once BLACKCNOTE_DEBUG_PLUGIN_DIR . 'includes/class-blackcnote-cursor-ai-monitor.php';
        require_once BLACKCNOTE_DEBUG_PLUGIN_DIR . 'includes/class-blackcnote-debug-startup-monitor.php';
        add_action('rest_api_init', ['BlackCnote_Debug_REST', 'register_routes']);
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize debug system
        $this->debug_system = new BlackCnoteDebugSystemCore([
            'log_file' => WP_CONTENT_DIR . '/logs/blackcnote-debug.log',
            'debug_enabled' => true,
            'log_level' => 'ALL'
        ]);
        
        // Log initialization
        $this->debug_system->log('BlackCnote Debug System WordPress plugin initialized', 'SYSTEM', [
            'wordpress_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'plugin_version' => BLACKCNOTE_DEBUG_VERSION
        ]);
    }
    
    /**
     * Admin initialization
     */
    public function admin_init() {
        // Initialize admin interface
        new BlackCnoteDebugAdmin($this->debug_system);
        
        // Initialize startup monitor
        new BlackCnoteDebugStartupMonitor($this->debug_system);
    }
    
    /**
     * Add admin menu
     */
    public function admin_menu() {
        add_menu_page(
            'BlackCnote Debug',
            'BlackCnote Debug',
            'manage_options',
            'blackcnote-debug',
            [$this, 'admin_page'],
            'dashicons-chart-area',
            30
        );
        
        add_submenu_page(
            'blackcnote-debug',
            'System Status',
            'System Status',
            'manage_options',
            'blackcnote-debug-status',
            [$this, 'status_page']
        );
        
        add_submenu_page(
            'blackcnote-debug',
            'Metrics',
            'Metrics',
            'manage_options',
            'blackcnote-debug-metrics',
            [$this, 'metrics_page']
        );
        
        add_submenu_page(
            'blackcnote-debug',
            'Script Checker',
            'Script Checker',
            'manage_options',
            'blackcnote-debug-scripts',
            [$this, 'scripts_page']
        );
    }
    
    /**
     * Main admin page
     */
    public function admin_page() {
        include BLACKCNOTE_DEBUG_PLUGIN_DIR . 'admin/views/main-page.php';
    }
    
    /**
     * Status page
     */
    public function status_page() {
        include BLACKCNOTE_DEBUG_PLUGIN_DIR . 'admin/views/status-page.php';
    }
    
    /**
     * Metrics page
     */
    public function metrics_page() {
        include BLACKCNOTE_DEBUG_PLUGIN_DIR . 'admin/views/metrics-page.php';
    }
    
    /**
     * Scripts page
     */
    public function scripts_page() {
        include BLACKCNOTE_DEBUG_PLUGIN_DIR . 'admin/views/scripts-page.php';
    }
    
    /**
     * AJAX metrics endpoint
     */
    public function ajax_metrics() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $metrics = new BlackCnoteDebugMetrics($this->debug_system);
        $data = $metrics->get_current_metrics();
        
        wp_send_json_success($data);
    }
    
    /**
     * AJAX status endpoint
     */
    public function ajax_status() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $status = $this->get_system_status();
        wp_send_json_success($status);
    }
    
    /**
     * Get system status
     */
    private function get_system_status() {
        return [
            'wordpress' => [
                'version' => get_bloginfo('version'),
                'memory_limit' => WP_MEMORY_LIMIT,
                'max_memory_limit' => WP_MAX_MEMORY_LIMIT,
                'debug_mode' => WP_DEBUG,
                'debug_log' => WP_DEBUG_LOG,
                'debug_display' => WP_DEBUG_DISPLAY,
            ],
            'system' => [
                'php_version' => PHP_VERSION,
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
                'disk_free' => disk_free_space(ABSPATH),
                'disk_total' => disk_total_space(ABSPATH),
                'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : null,
            ],
            'plugins' => [
                'active' => get_option('active_plugins'),
                'total' => count(get_plugins()),
            ],
            'themes' => [
                'active' => get_stylesheet(),
                'parent' => get_template(),
            ],
            'database' => [
                'prefix' => $GLOBALS['wpdb']->prefix,
                'charset' => $GLOBALS['wpdb']->charset,
                'collate' => $GLOBALS['wpdb']->collate,
            ]
        ];
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create logs directory
        $logs_dir = WP_CONTENT_DIR . '/logs';
        if (!is_dir($logs_dir)) {
            wp_mkdir_p($logs_dir);
        }
        
        // Create default configuration
        $config = [
            'debug_enabled' => true,
            'log_level' => 'ALL',
            'file_monitoring' => true,
            'system_monitoring' => true,
            'metrics_export' => true,
        ];
        
        update_option('blackcnote_debug_config', $config);
        
        // Log activation
        if ($this->debug_system) {
            $this->debug_system->log('BlackCnote Debug System plugin activated', 'SYSTEM');
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Log deactivation
        if ($this->debug_system) {
            $this->debug_system->log('BlackCnote Debug System plugin deactivated', 'SYSTEM');
        }
    }
    
    /**
     * Add settings link to plugin page
     */
    public function plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=blackcnote-debug') . '">' . __('Settings', 'blackcnote-debug') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    /**
     * Get debug system instance
     */
    public function get_debug_system() {
        return $this->debug_system;
    }
}

/**
 * Initialize the plugin
 */
function blackcnote_debug_system() {
    return BlackCnoteDebugSystem::instance();
}

// Start the plugin
blackcnote_debug_system(); 