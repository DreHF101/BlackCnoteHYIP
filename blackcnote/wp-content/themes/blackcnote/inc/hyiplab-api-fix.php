<?php
/**
 * BlackCnote HYIPLab API Fix
 * Resolves HYIPLab plugin API connection issues
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote HYIPLab API Fix
 */
class BlackCnote_HYIPLab_API_Fix {
    
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_hyiplab_routes']);
        add_action('wp_ajax_blackcnote_hyiplab_status', [$this, 'get_hyiplab_status']);
        add_action('wp_ajax_nopriv_blackcnote_hyiplab_status', [$this, 'get_hyiplab_status']);
        add_action('wp_ajax_blackcnote_hyiplab_test', [$this, 'test_hyiplab_api']);
        add_action('wp_ajax_nopriv_blackcnote_hyiplab_test', [$this, 'test_hyiplab_api']);
    }
    
    /**
     * Register HYIPLab REST API routes
     */
    public function register_hyiplab_routes() {
        // HYIPLab API status endpoint
        register_rest_route('blackcnote/v1', '/hyiplab/status', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_hyiplab_rest_status'],
            'permission_callback' => '__return_true'
        ]);
        
        // HYIPLab API test endpoint
        register_rest_route('blackcnote/v1', '/hyiplab/test', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'test_hyiplab_rest_api'],
            'permission_callback' => '__return_true'
        ]);
        
        // HYIPLab API health check
        register_rest_route('blackcnote/v1', '/hyiplab/health', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'hyiplab_health_check'],
            'permission_callback' => '__return_true'
        ]);
    }
    
    /**
     * Get HYIPLab status via AJAX
     */
    public function get_hyiplab_status() {
        check_ajax_referer('blackcnote_hyiplab_nonce', 'nonce');
        
        $status = $this->check_hyiplab_status();
        wp_send_json_success($status);
    }
    
    /**
     * Test HYIPLab API via AJAX
     */
    public function test_hyiplab_api() {
        check_ajax_referer('blackcnote_hyiplab_nonce', 'nonce');
        
        $result = $this->test_hyiplab_connection();
        wp_send_json_success($result);
    }
    
    /**
     * Get HYIPLab status via REST API
     */
    public function get_hyiplab_rest_status($request) {
        $status = $this->check_hyiplab_status();
        return new WP_REST_Response($status, 200);
    }
    
    /**
     * Test HYIPLab API via REST API
     */
    public function test_hyiplab_rest_api($request) {
        $result = $this->test_hyiplab_connection();
        return new WP_REST_Response($result, 200);
    }
    
    /**
     * HYIPLab health check
     */
    public function hyiplab_health_check($request) {
        $health = [
            'status' => 'healthy',
            'timestamp' => current_time('mysql'),
            'checks' => []
        ];
        
        // Check if plugin is active
        $health['checks']['plugin_active'] = is_plugin_active('hyiplab/hyiplab.php');
        
        // Check if functions exist
        $health['checks']['functions_exist'] = function_exists('hyiplab_system_instance');
        
        // Check if system instance is available
        if ($health['checks']['functions_exist']) {
            try {
                $system = hyiplab_system_instance();
                $health['checks']['system_instance'] = $system !== null;
                $health['checks']['system_class'] = get_class($system);
            } catch (Exception $e) {
                $health['checks']['system_instance'] = false;
                $health['checks']['system_error'] = $e->getMessage();
            }
        }
        
        // Check database tables
        $health['checks']['database_tables'] = $this->check_hyiplab_tables();
        
        // Determine overall health
        $failed_checks = array_filter($health['checks'], function($check) {
            return $check === false;
        });
        
        if (!empty($failed_checks)) {
            $health['status'] = 'unhealthy';
            $health['failed_checks'] = array_keys($failed_checks);
        }
        
        return new WP_REST_Response($health, 200);
    }
    
    /**
     * Check HYIPLab status
     */
    private function check_hyiplab_status() {
        $status = [
            'plugin_active' => false,
            'functions_available' => false,
            'system_instance' => false,
            'database_tables' => false,
            'api_endpoints' => false,
            'errors' => []
        ];
        
        // Check if plugin is active
        $status['plugin_active'] = is_plugin_active('hyiplab/hyiplab.php');
        
        if (!$status['plugin_active']) {
            $status['errors'][] = 'HYIPLab plugin is not active';
            return $status;
        }
        
        // Check if required functions exist
        $required_functions = [
            'hyiplab_system_instance',
            'hyiplab_system_details',
            'hyiplab_request'
        ];
        
        $missing_functions = [];
        foreach ($required_functions as $function) {
            if (!function_exists($function)) {
                $missing_functions[] = $function;
            }
        }
        
        if (!empty($missing_functions)) {
            $status['errors'][] = 'Missing functions: ' . implode(', ', $missing_functions);
            return $status;
        }
        
        $status['functions_available'] = true;
        
        // Check system instance
        try {
            $system = hyiplab_system_instance();
            $status['system_instance'] = $system !== null;
            
            if ($status['system_instance']) {
                $status['system_class'] = get_class($system);
            }
        } catch (Exception $e) {
            $status['errors'][] = 'System instance error: ' . $e->getMessage();
        }
        
        // Check database tables
        $status['database_tables'] = $this->check_hyiplab_tables();
        
        // Check API endpoints
        $status['api_endpoints'] = $this->check_hyiplab_endpoints();
        
        return $status;
    }
    
    /**
     * Test HYIPLab connection
     */
    private function test_hyiplab_connection() {
        $result = [
            'success' => false,
            'message' => '',
            'details' => []
        ];
        
        try {
            // Check if plugin is active
            if (!is_plugin_active('hyiplab/hyiplab.php')) {
                throw new Exception('HYIPLab plugin is not active');
            }
            
            // Check if functions exist
            if (!function_exists('hyiplab_system_instance')) {
                throw new Exception('HYIPLab system functions not available');
            }
            
            // Test system instance
            $system = hyiplab_system_instance();
            if (!$system) {
                throw new Exception('HYIPLab system instance not available');
            }
            
            // Test system details
            $details = hyiplab_system_details();
            if (!$details) {
                throw new Exception('HYIPLab system details not available');
            }
            
            // Test request object
            $request = hyiplab_request();
            if (!$request) {
                throw new Exception('HYIPLab request object not available');
            }
            
            $result['success'] = true;
            $result['message'] = 'HYIPLab API is working correctly';
            $result['details'] = [
                'system_class' => get_class($system),
                'system_version' => $details['version'] ?? 'unknown',
                'system_name' => $details['name'] ?? 'unknown'
            ];
            
        } catch (Exception $e) {
            $result['message'] = 'HYIPLab API error: ' . $e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * Check HYIPLab database tables
     */
    private function check_hyiplab_tables() {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'hyiplab_users',
            $wpdb->prefix . 'hyiplab_plans',
            $wpdb->prefix . 'hyiplab_invests',
            $wpdb->prefix . 'hyiplab_transactions'
        ];
        
        $existing_tables = [];
        foreach ($tables as $table) {
            $result = $wpdb->get_var("SHOW TABLES LIKE '$table'");
            if ($result) {
                $existing_tables[] = $table;
            }
        }
        
        return [
            'expected' => $tables,
            'existing' => $existing_tables,
            'all_exist' => count($existing_tables) === count($tables)
        ];
    }
    
    /**
     * Check HYIPLab API endpoints
     */
    private function check_hyiplab_endpoints() {
        $endpoints = [
            '/wp-json/hyiplab/v1/',
            '/wp-admin/admin-ajax.php'
        ];
        
        $working_endpoints = [];
        foreach ($endpoints as $endpoint) {
            $url = home_url($endpoint);
            $response = wp_remote_get($url, [
                'timeout' => 5,
                'sslverify' => false
            ]);
            
            if (!is_wp_error($response) && $response['response']['code'] < 500) {
                $working_endpoints[] = $endpoint;
            }
        }
        
        return [
            'expected' => $endpoints,
            'working' => $working_endpoints,
            'all_working' => count($working_endpoints) === count($endpoints)
        ];
    }
}

// Initialize the HYIPLab API fix
new BlackCnote_HYIPLab_API_Fix(); 