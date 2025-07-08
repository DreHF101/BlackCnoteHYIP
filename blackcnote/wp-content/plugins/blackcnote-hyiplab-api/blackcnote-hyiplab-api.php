<?php
declare(strict_types=1);

/**
 * Plugin Name: BlackCnote HYIPLab API
 * Plugin URI: https://github.com/DreHF101/BlackCnoteHYIP.git
 * Description: REST API endpoints for HYIPLab integration with BlackCnote theme
 * Version: 1.0.0
 * Author: BlackCnote Development Team
 * License: GPL v2 or later
 * Text Domain: blackcnote-hyiplab-api
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote HYIPLab API Plugin Class
 */
class BlackCnote_HYIPLab_API {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('blackcnote-hyiplab-api', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Create database tables if they don't exist
        $this->create_tables();
        
        // Insert sample data if needed
        $this->insert_sample_data();
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        // HYIPLab Status endpoint
        register_rest_route('hyiplab/v1', '/status', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_status'),
            'permission_callback' => '__return_true',
        ));
        
        // HYIPLab Plans endpoint
        register_rest_route('hyiplab/v1', '/plans', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_plans'),
            'permission_callback' => '__return_true',
        ));
        
        // HYIPLab Statistics endpoint
        register_rest_route('hyiplab/v1', '/stats', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_stats'),
            'permission_callback' => '__return_true',
        ));
        
        // HYIPLab Health Check endpoint
        register_rest_route('hyiplab/v1', '/health', array(
            'methods' => 'GET',
            'callback' => array($this, 'health_check'),
            'permission_callback' => '__return_true',
        ));
    }
    
    /**
     * Get HYIPLab status
     */
    public function get_status() {
        return rest_ensure_response(array(
            'status' => 'active',
            'version' => '3.0',
            'license' => 'activated',
            'plugin_active' => function_exists('hyiplab_system_instance'),
            'timestamp' => current_time('mysql'),
            'wordpress_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION
        ));
    }
    
    /**
     * Get investment plans
     */
    public function get_plans() {
        global $wpdb;
        
        $plans = $wpdb->get_results("
            SELECT * FROM {$wpdb->prefix}hyiplab_plans 
            WHERE status = 'active' 
            ORDER BY min_investment ASC
        ");
        
        return rest_ensure_response($plans ?: array());
    }
    
    /**
     * Get platform statistics
     */
    public function get_stats() {
        global $wpdb;
        
        $stats = array(
            'total_users' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_users WHERE status = 'active'") ?: 0,
            'total_investments' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_investments WHERE status = 'active'") ?: 0,
            'total_invested' => $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_investments WHERE status = 'active'") ?: 0,
            'active_plans' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_plans WHERE status = 'active'") ?: 0,
            'last_updated' => current_time('mysql')
        );
        
        return rest_ensure_response($stats);
    }
    
    /**
     * Health check endpoint
     */
    public function health_check() {
        global $wpdb;
        
        $health = array(
            'status' => 'healthy',
            'database' => $this->check_database_connection(),
            'tables' => $this->check_database_tables(),
            'plugins' => $this->check_required_plugins(),
            'timestamp' => current_time('mysql')
        );
        
        return rest_ensure_response($health);
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $tables = array(
            'hyiplab_plans' => "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}hyiplab_plans` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `min_investment` decimal(10,2) NOT NULL,
                `max_investment` decimal(10,2) NOT NULL,
                `return_rate` decimal(5,2) NOT NULL,
                `duration_days` int(11) NOT NULL,
                `status` enum('active','inactive') NOT NULL DEFAULT 'active',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) $charset_collate",
            
            'hyiplab_users' => "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}hyiplab_users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wp_user_id` bigint(20) unsigned NOT NULL,
                `username` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
                `total_invested` decimal(10,2) NOT NULL DEFAULT 0.00,
                `total_earned` decimal(10,2) NOT NULL DEFAULT 0.00,
                `status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) $charset_collate",
            
            'hyiplab_investments' => "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}hyiplab_investments` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `plan_id` int(11) NOT NULL,
                `amount` decimal(10,2) NOT NULL,
                `return_rate` decimal(5,2) NOT NULL,
                `expected_return` decimal(10,2) NOT NULL,
                `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
                `start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) $charset_collate"
        );
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        foreach ($tables as $table_name => $sql) {
            dbDelta($sql);
        }
    }
    
    /**
     * Insert sample data
     */
    private function insert_sample_data() {
        global $wpdb;
        
        // Check if sample data already exists
        $existing_plans = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_plans");
        
        if ($existing_plans == 0) {
            $sample_plans = array(
                array(
                    'name' => 'Starter Plan',
                    'min_investment' => 100.00,
                    'max_investment' => 1000.00,
                    'return_rate' => 2.5,
                    'duration_days' => 30
                ),
                array(
                    'name' => 'Premium Plan',
                    'min_investment' => 1000.00,
                    'max_investment' => 10000.00,
                    'return_rate' => 3.5,
                    'duration_days' => 60
                ),
                array(
                    'name' => 'VIP Plan',
                    'min_investment' => 10000.00,
                    'max_investment' => 100000.00,
                    'return_rate' => 5.0,
                    'duration_days' => 90
                )
            );
            
            foreach ($sample_plans as $plan) {
                $wpdb->insert("{$wpdb->prefix}hyiplab_plans", $plan);
            }
        }
    }
    
    /**
     * Check database connection
     */
    private function check_database_connection() {
        global $wpdb;
        $result = $wpdb->get_var("SELECT 1");
        return $result === '1' ? 'connected' : 'disconnected';
    }
    
    /**
     * Check database tables
     */
    private function check_database_tables() {
        global $wpdb;
        
        $tables = array(
            'hyiplab_plans',
            'hyiplab_users',
            'hyiplab_investments'
        );
        
        $status = array();
        foreach ($tables as $table) {
            $table_name = $wpdb->prefix . $table;
            $exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
            $status[$table] = $exists ? 'exists' : 'missing';
        }
        
        return $status;
    }
    
    /**
     * Check required plugins
     */
    private function check_required_plugins() {
        $plugins = array(
            'hyiplab/hyiplab.php' => 'HYIPLab',
            'blackcnote-debug-system/blackcnote-debug-system.php' => 'BlackCnote Debug System',
            'full-content-checker/full-content-checker.php' => 'Full Content Checker'
        );
        
        $status = array();
        foreach ($plugins as $plugin => $name) {
            $status[$name] = is_plugin_active($plugin) ? 'active' : 'inactive';
        }
        
        return $status;
    }
    
    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'blackcnote-hyiplab-api',
            plugin_dir_url(__FILE__) . 'assets/js/hyiplab-api.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('blackcnote-hyiplab-api', 'blackcnoteHYIPLabAPI', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('hyiplab/v1/'),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_script(
            'blackcnote-hyiplab-api-admin',
            plugin_dir_url(__FILE__) . 'assets/js/hyiplab-api-admin.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}

// Initialize the plugin
new BlackCnote_HYIPLab_API();

// Activation hook
register_activation_hook(__FILE__, function() {
    // Flush rewrite rules
    flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Flush rewrite rules
    flush_rewrite_rules();
}); 