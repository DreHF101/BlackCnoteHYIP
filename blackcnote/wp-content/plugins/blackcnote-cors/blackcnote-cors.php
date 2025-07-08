<?php
declare(strict_types=1);

/**
 * Plugin Name: BlackCnote CORS Fix
 * Plugin URI: https://blackcnote.com
 * Description: Fixes CORS issues for BlackCnote React app development
 * Version: 1.0
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * Author: BlackCnote Team
 * Author URI: https://blackcnote.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: blackcnote-cors
 * Domain Path: /languages
 * Network: false
 * 
 * @package BlackCnote_CORS
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote CORS Fix Plugin
 */
class BlackCnote_CORS_Fix {
    
    public function __construct() {
        add_action('init', [$this, 'add_cors_headers']);
        add_action('rest_api_init', [$this, 'add_rest_cors']);
        add_action('wp_ajax_blackcnote_cors_test', [$this, 'cors_test']);
        add_action('wp_ajax_nopriv_blackcnote_cors_test', [$this, 'cors_test']);
    }
    
    /**
     * Add CORS headers for all requests
     */
    public function add_cors_headers() {
        // Only add CORS headers in development
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        // Allow specific origins for development
        $allowed_origins = [
            'http://localhost:3000',  // Browsersync
            'http://localhost:5174',  // Vite dev server
            'http://localhost:5175',  // Vite alternative port
            'http://127.0.0.1:3000',
            'http://127.0.0.1:5174',
            'http://127.0.0.1:5175',
            'http://wordpress:80',    // Docker WordPress
            'http://localhost:8888'   // Local WordPress
        ];
        
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        
        if (in_array($origin, $allowed_origins)) {
            header("Access-Control-Allow-Origin: $origin");
        } else {
            header('Access-Control-Allow-Origin: *');
        }
        
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-WP-Nonce, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // 24 hours
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
    
    /**
     * Add CORS headers for REST API
     */
    public function add_rest_cors() {
        add_filter('rest_pre_serve_request', function($served, $result, $request, $server) {
            // Add CORS headers for REST API
            $this->add_cors_headers();
            return $served;
        }, 10, 4);
    }
    
    /**
     * CORS test endpoint
     */
    public function cors_test() {
        wp_send_json_success([
            'message' => 'CORS is working correctly',
            'timestamp' => current_time('mysql'),
            'origin' => isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'none',
            'method' => $_SERVER['REQUEST_METHOD']
        ]);
    }
}

// Initialize the CORS fix
new BlackCnote_CORS_Fix();

// Add activation hook
register_activation_hook(__FILE__, function() {
    error_log('BlackCnote CORS Fix Plugin Activated');
});

// Add deactivation hook
register_deactivation_hook(__FILE__, function() {
    error_log('BlackCnote CORS Fix Plugin Deactivated');
}); 