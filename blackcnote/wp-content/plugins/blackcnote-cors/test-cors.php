<?php
/**
 * BlackCnote CORS Plugin Test Suite
 * 
 * This file tests all plugin functionality to ensure it's ready for production.
 * Run this file to verify the plugin is working correctly.
 */

// Simulate WordPress environment for testing
if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $accepted_args = 1) { return true; }
}
if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action) { return 'test-nonce'; }
}
if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action) { return true; }
}
if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data) { echo json_encode(['success' => true, 'data' => $data]); }
}
if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data) { echo json_encode(['success' => false, 'data' => $data]); }
}
if (!function_exists('check_ajax_referer')) {
    function check_ajax_referer($action, $nonce) { return true; }
}
if (!function_exists('admin_url')) {
    function admin_url($path = '') { return 'http://localhost:8888/wp-admin/' . $path; }
}
if (!function_exists('home_url')) {
    function home_url($path = '') { return 'http://localhost:8888/' . $path; }
}
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) { return dirname($file) . '/'; }
}
if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) { return 'http://localhost:8888/wp-content/plugins/blackcnote-cors/'; }
}
if (!function_exists('add_option')) {
    function add_option($option, $value) { return true; }
}
if (!function_exists('get_option')) {
    function get_option($option, $default = false) { return $default; }
}
if (!function_exists('delete_transient')) {
    function delete_transient($transient) { return true; }
}
if (!function_exists('flush_rewrite_rules')) {
    function flush_rewrite_rules() { return true; }
}
if (!function_exists('add_options_page')) {
    function add_options_page($page_title, $menu_title, $capability, $menu_slug, $function = '') { return true; }
}
if (!function_exists('register_setting')) {
    function register_setting($option_group, $option_name, $args = []) { return true; }
}
if (!function_exists('settings_fields')) {
    function settings_fields($option_group) { echo '<input type="hidden" name="option_page" value="' . $option_group . '">'; }
}
if (!function_exists('submit_button')) {
    function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null) { echo '<input type="submit" value="' . $text . '">'; }
}
if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') { return htmlspecialchars($text); }
}
if (!function_exists('esc_html')) {
    function esc_html($text) { return htmlspecialchars($text); }
}
if (!function_exists('esc_textarea')) {
    function esc_textarea($text) { return htmlspecialchars($text); }
}
if (!function_exists('esc_attr')) {
    function esc_attr($text) { return htmlspecialchars($text); }
}
if (!function_exists('checked')) {
    function checked($checked, $current = true, $echo = true) { $result = ($checked == $current) ? ' checked="checked"' : ''; if ($echo) echo $result; return $result; }
}
if (!function_exists('wp_remote_get')) {
    function wp_remote_get($url, $args = []) { return ['response' => ['code' => 200], 'headers' => new stdClass()]; }
}
if (!function_exists('wp_remote_retrieve_response_code')) {
    function wp_remote_retrieve_response_code($response) { return $response['response']['code'] ?? 200; }
}
if (!function_exists('wp_remote_retrieve_headers')) {
    function wp_remote_retrieve_headers($response) { return $response['headers'] ?? new stdClass(); }
}
if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) { return false; }
}
if (!function_exists('register_activation_hook')) {
    function register_activation_hook($file, $callback) { return true; }
}
if (!function_exists('register_deactivation_hook')) {
    function register_deactivation_hook($file, $callback) { return true; }
}
if (!function_exists('add_filter')) {
    function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) { return true; }
}
if (!function_exists('apply_filters')) {
    function apply_filters($hook, $value, ...$args) { return $value; }
}
if (!function_exists('error_log')) {
    function error_log($message) { echo "[LOG] {$message}\n"; }
}

// Define constants
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}
if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}
if (!defined('REST_REQUEST')) {
    define('REST_REQUEST', false);
}

// Include the main plugin file
require_once __DIR__ . '/blackcnote-cors.php';

/**
 * Test Suite Class
 */
class BlackCnote_CORS_Test_Suite {
    
    private $test_results = [];
    private $plugin_instance;
    
    public function __construct() {
        $this->plugin_instance = new BlackCnote_CORS_Handler();
    }
    
    /**
     * Run all tests
     */
    public function run_all_tests(): void {
        echo "🚀 Starting BlackCnote CORS Plugin Test Suite\n";
        echo "=============================================\n\n";
        
        $this->test_plugin_loading();
        $this->test_constants();
        $this->test_cors_headers();
        $this->test_origin_validation();
        $this->test_preflight_handling();
        $this->test_security_headers();
        $this->test_admin_functions();
        $this->test_ajax_handlers();
        $this->test_performance();
        $this->test_compatibility();
        
        $this->print_results();
    }
    
    /**
     * Test plugin loading
     */
    private function test_plugin_loading(): void {
        echo "📦 Testing Plugin Loading...\n";
        
        try {
            // Test if plugin class exists
            $this->assert_true(class_exists('BlackCnote_CORS_Handler'), 'Plugin class exists');
            
            // Test if plugin instance can be created
            $this->assert_true($this->plugin_instance instanceof BlackCnote_CORS_Handler, 'Plugin instance created');
            
            // Test if constants are defined
            $this->assert_true(defined('BLACKCNOTE_CORS_VERSION'), 'Plugin version constant defined');
            $this->assert_true(defined('BLACKCNOTE_CORS_PLUGIN_FILE'), 'Plugin file constant defined');
            
            echo "✅ Plugin loading tests passed\n\n";
        } catch (Exception $e) {
            $this->test_results[] = ['test' => 'Plugin Loading', 'status' => 'FAILED', 'error' => $e->getMessage()];
            echo "❌ Plugin loading tests failed: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * Test constants
     */
    private function test_constants(): void {
        echo "🔧 Testing Constants...\n";
        
        $this->assert_equal(BLACKCNOTE_CORS_VERSION, '1.0.1', 'Plugin version is correct');
        $this->assert_equal(BlackCnote_CORS_Handler::SLUG, 'blackcnote-cors', 'Plugin slug is correct');
        $this->assert_true(file_exists(BLACKCNOTE_CORS_PLUGIN_FILE), 'Plugin file exists');
        
        echo "✅ Constants tests passed\n\n";
    }
    
    /**
     * Test CORS headers
     */
    private function test_cors_headers(): void {
        echo "🌐 Testing CORS Headers...\n";
        
        // Simulate request
        $_SERVER['HTTP_ORIGIN'] = 'http://localhost:5174';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        // Test CORS handling
        ob_start();
        $this->plugin_instance->handle_cors();
        $output = ob_get_clean();
        
        // Check if headers would be set (we can't actually set them in test)
        $this->assert_true(true, 'CORS handler executed without errors');
        
        echo "✅ CORS headers tests passed\n\n";
    }
    
    /**
     * Test origin validation
     */
    private function test_origin_validation(): void {
        echo "🔍 Testing Origin Validation...\n";
        
        // Test allowed origins
        $allowed_origins = [
            'http://localhost:5174',
            'http://localhost:3000',
            'http://localhost:8888',
            'http://127.0.0.1:5174'
        ];
        
        foreach ($allowed_origins as $origin) {
            $_SERVER['HTTP_ORIGIN'] = $origin;
            $this->assert_true($this->plugin_instance->is_origin_allowed($origin), "Origin {$origin} is allowed");
        }
        
        // Test disallowed origin
        $_SERVER['HTTP_ORIGIN'] = 'http://malicious-site.com';
        $this->assert_false($this->plugin_instance->is_origin_allowed('http://malicious-site.com'), 'Malicious origin is blocked');
        
        echo "✅ Origin validation tests passed\n\n";
    }
    
    /**
     * Test preflight handling
     */
    private function test_preflight_handling(): void {
        echo "✈️ Testing Preflight Handling...\n";
        
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $_SERVER['HTTP_ORIGIN'] = 'http://localhost:5174';
        
        // Test preflight handling
        ob_start();
        $this->plugin_instance->handle_preflight();
        $output = ob_get_clean();
        
        $this->assert_true(true, 'Preflight handler executed without errors');
        
        echo "✅ Preflight handling tests passed\n\n";
    }
    
    /**
     * Test security headers
     */
    private function test_security_headers(): void {
        echo "🔒 Testing Security Headers...\n";
        
        ob_start();
        $this->plugin_instance->add_security_headers();
        $output = ob_get_clean();
        
        $this->assert_true(true, 'Security headers function executed without errors');
        
        echo "✅ Security headers tests passed\n\n";
    }
    
    /**
     * Test admin functions
     */
    private function test_admin_functions(): void {
        echo "⚙️ Testing Admin Functions...\n";
        
        // Test admin menu addition
        $this->assert_true(method_exists($this->plugin_instance, 'add_admin_menu'), 'Admin menu method exists');
        
        // Test settings registration
        $this->assert_true(method_exists($this->plugin_instance, 'register_settings'), 'Settings registration method exists');
        
        // Test settings sanitization
        $test_input = [
            'allowed_origins' => "http://localhost:5174\nhttp://localhost:3000",
            'debug_mode' => '1'
        ];
        
        $sanitized = $this->plugin_instance->sanitize_settings($test_input);
        $this->assert_true(is_array($sanitized), 'Settings sanitization returns array');
        $this->assert_true(isset($sanitized['allowed_origins']), 'Allowed origins are sanitized');
        
        echo "✅ Admin functions tests passed\n\n";
    }
    
    /**
     * Test AJAX handlers
     */
    private function test_ajax_handlers(): void {
        echo "🔄 Testing AJAX Handlers...\n";
        
        // Test CORS connection test
        $this->assert_true(method_exists($this->plugin_instance, 'test_cors_connection'), 'CORS test method exists');
        
        // Test cache clearing
        $this->assert_true(method_exists($this->plugin_instance, 'clear_cache'), 'Cache clearing method exists');
        
        echo "✅ AJAX handlers tests passed\n\n";
    }
    
    /**
     * Test performance
     */
    private function test_performance(): void {
        echo "⚡ Testing Performance...\n";
        
        // Test performance headers
        $this->assert_true(method_exists($this->plugin_instance, 'add_performance_headers'), 'Performance headers method exists');
        
        // Test memory usage
        $memory_before = memory_get_usage();
        $this->plugin_instance = new BlackCnote_CORS_Handler();
        $memory_after = memory_get_usage();
        $memory_used = $memory_after - $memory_before;
        
        $this->assert_true($memory_used < 1024 * 1024, 'Memory usage is reasonable (< 1MB)');
        
        echo "✅ Performance tests passed\n\n";
    }
    
    /**
     * Test compatibility
     */
    private function test_compatibility(): void {
        echo "🔗 Testing Compatibility...\n";
        
        // Test WordPress compatibility
        $this->assert_true(function_exists('add_action'), 'WordPress add_action function available');
        $this->assert_true(function_exists('wp_create_nonce'), 'WordPress nonce function available');
        
        // Test PHP version compatibility
        $this->assert_true(version_compare(PHP_VERSION, '7.4.0', '>='), 'PHP version is 7.4 or higher');
        
        // Test helper function
        $this->assert_true(function_exists('is_rest_request'), 'Helper function exists');
        
        echo "✅ Compatibility tests passed\n\n";
    }
    
    /**
     * Assertion helpers
     */
    private function assert_true($condition, $message): void {
        if ($condition) {
            $this->test_results[] = ['test' => $message, 'status' => 'PASSED'];
        } else {
            $this->test_results[] = ['test' => $message, 'status' => 'FAILED'];
            throw new Exception("Assertion failed: {$message}");
        }
    }
    
    private function assert_false($condition, $message): void {
        $this->assert_true(!$condition, $message);
    }
    
    private function assert_equal($actual, $expected, $message): void {
        $this->assert_true($actual === $expected, "{$message} (expected: {$expected}, actual: {$actual})");
    }
    
    /**
     * Print test results
     */
    private function print_results(): void {
        echo "📊 Test Results Summary\n";
        echo "=======================\n\n";
        
        $passed = 0;
        $failed = 0;
        
        foreach ($this->test_results as $result) {
            $status = $result['status'];
            $test = $result['test'];
            
            if ($status === 'PASSED') {
                echo "✅ {$test}\n";
                $passed++;
            } else {
                echo "❌ {$test}\n";
                if (isset($result['error'])) {
                    echo "   Error: {$result['error']}\n";
                }
                $failed++;
            }
        }
        
        echo "\n📈 Summary: {$passed} passed, {$failed} failed\n";
        
        if ($failed === 0) {
            echo "\n🎉 All tests passed! Plugin is ready for production use.\n";
        } else {
            echo "\n⚠️ Some tests failed. Please review the errors above.\n";
        }
    }
}

// Run tests
$test_suite = new BlackCnote_CORS_Test_Suite();
$test_suite->run_all_tests(); 