<?php
/**
 * BlackCnote HYIPLab API Complete Test Script
 * Tests plugin activation, database setup, and all API endpoints
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // Load WordPress
    require_once dirname(__FILE__) . '/../../blackcnote/wp-config.php';
    require_once dirname(__FILE__) . '/../../blackcnote/wp-load.php';
}

// Test results storage
$test_results = array();
$start_time = microtime(true);

/**
 * Test Functions
 */

function run_test($test_name, $test_function) {
    global $test_results;
    
    echo "Running test: $test_name... ";
    
    try {
        $result = $test_function();
        $test_results[$test_name] = array(
            'status' => 'PASS',
            'result' => $result,
            'time' => microtime(true)
        );
        echo "PASS\n";
        return $result;
    } catch (Exception $e) {
        $test_results[$test_name] = array(
            'status' => 'FAIL',
            'error' => $e->getMessage(),
            'time' => microtime(true)
        );
        echo "FAIL: " . $e->getMessage() . "\n";
        return false;
    }
}

function test_plugin_activation() {
    // Check if plugin file exists
    $plugin_file = WP_CONTENT_DIR . '/plugins/blackcnote-hyiplab-api/blackcnote-hyiplab-api.php';
    
    if (!file_exists($plugin_file)) {
        throw new Exception("Plugin file not found: $plugin_file");
    }
    
    // Activate plugin
    $plugin = plugin_basename($plugin_file);
    activate_plugin($plugin);
    
    // Check if plugin is active
    if (!is_plugin_active($plugin)) {
        throw new Exception("Plugin activation failed");
    }
    
    return "Plugin activated successfully";
}

function test_database_tables() {
    global $wpdb;
    
    $required_tables = array(
        'hyiplab_plans',
        'hyiplab_users',
        'hyiplab_investments'
    );
    
    $results = array();
    
    foreach ($required_tables as $table) {
        $table_name = $wpdb->prefix . $table;
        $exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
        
        if (!$exists) {
            throw new Exception("Table $table_name does not exist");
        }
        
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $results[$table] = $count;
    }
    
    return $results;
}

function test_sample_data() {
    global $wpdb;
    
    // Check if sample plans exist
    $plans_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_plans");
    
    if ($plans_count == 0) {
        // Insert sample data
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
        
        return "Inserted " . count($sample_plans) . " sample plans";
    }
    
    return "Sample data already exists ($plans_count plans)";
}

function test_rest_api_endpoints() {
    $endpoints = array(
        'hyiplab/v1/status',
        'hyiplab/v1/plans',
        'hyiplab/v1/stats',
        'hyiplab/v1/health'
    );
    
    $results = array();
    
    foreach ($endpoints as $endpoint) {
        $url = rest_url($endpoint);
        $response = wp_remote_get($url, array(
            'timeout' => 10,
            'headers' => array(
                'X-WP-Nonce' => wp_create_nonce('wp_rest')
            )
        ));
        
        if (is_wp_error($response)) {
            throw new Exception("Failed to test endpoint $endpoint: " . $response->get_error_message());
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($status_code !== 200) {
            throw new Exception("Endpoint $endpoint returned status $status_code");
        }
        
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Endpoint $endpoint returned invalid JSON");
        }
        
        $results[$endpoint] = array(
            'status_code' => $status_code,
            'data' => $data
        );
    }
    
    return $results;
}

function test_cors_headers() {
    $url = rest_url('hyiplab/v1/status');
    $response = wp_remote_get($url, array(
        'timeout' => 10,
        'headers' => array(
            'X-WP-Nonce' => wp_create_nonce('wp_rest')
        )
    ));
    
    if (is_wp_error($response)) {
        throw new Exception("Failed to test CORS headers: " . $response->get_error_message());
    }
    
    $headers = wp_remote_retrieve_headers($response);
    $cors_headers = array(
        'Access-Control-Allow-Origin',
        'Access-Control-Allow-Methods',
        'Access-Control-Allow-Headers'
    );
    
    $results = array();
    foreach ($cors_headers as $header) {
        $value = $headers->get($header);
        $results[$header] = $value ?: 'Not set';
    }
    
    return $results;
}

function test_plugin_integration() {
    // Test if plugin class exists
    if (!class_exists('BlackCnote_HYIPLab_API')) {
        throw new Exception("Plugin class BlackCnote_HYIPLab_API not found");
    }
    
    // Test if hooks are registered
    $has_rest_hook = has_action('rest_api_init');
    if (!$has_rest_hook) {
        throw new Exception("REST API hooks not registered");
    }
    
    return "Plugin integration successful";
}

function test_performance() {
    global $wpdb;
    
    $start_time = microtime(true);
    
    // Test database query performance
    $plans = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hyiplab_plans");
    $db_time = microtime(true) - $start_time;
    
    // Test API response time
    $start_time = microtime(true);
    $url = rest_url('hyiplab/v1/stats');
    $response = wp_remote_get($url, array(
        'timeout' => 10,
        'headers' => array(
            'X-WP-Nonce' => wp_create_nonce('wp_rest')
        )
    ));
    $api_time = microtime(true) - $start_time;
    
    return array(
        'database_query_time' => round($db_time * 1000, 2) . 'ms',
        'api_response_time' => round($api_time * 1000, 2) . 'ms',
        'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB'
    );
}

function test_error_handling() {
    // Test invalid endpoint
    $url = rest_url('hyiplab/v1/invalid');
    $response = wp_remote_get($url, array(
        'timeout' => 10,
        'headers' => array(
            'X-WP-Nonce' => wp_create_nonce('wp_rest')
        )
    ));
    
    if (is_wp_error($response)) {
        throw new Exception("Error handling test failed: " . $response->get_error_message());
    }
    
    $status_code = wp_remote_retrieve_response_code($response);
    
    // Should return 404 for invalid endpoint
    if ($status_code !== 404) {
        throw new Exception("Invalid endpoint should return 404, got $status_code");
    }
    
    return "Error handling working correctly";
}

/**
 * Main Test Execution
 */

echo "=== BlackCnote HYIPLab API Complete Test Suite ===\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";

// Run all tests
run_test('Plugin Activation', 'test_plugin_activation');
run_test('Database Tables', 'test_database_tables');
run_test('Sample Data', 'test_sample_data');
run_test('REST API Endpoints', 'test_rest_api_endpoints');
run_test('CORS Headers', 'test_cors_headers');
run_test('Plugin Integration', 'test_plugin_integration');
run_test('Performance', 'test_performance');
run_test('Error Handling', 'test_error_handling');

// Calculate total time
$total_time = microtime(true) - $start_time;

// Generate report
echo "\n=== Test Results Summary ===\n";
echo "Total execution time: " . round($total_time, 2) . " seconds\n\n";

$passed = 0;
$failed = 0;

foreach ($test_results as $test_name => $result) {
    $status = $result['status'];
    if ($status === 'PASS') {
        $passed++;
    } else {
        $failed++;
    }
    
    echo "$test_name: $status\n";
    if ($status === 'FAIL') {
        echo "  Error: " . $result['error'] . "\n";
    }
}

echo "\n=== Summary ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Total: " . count($test_results) . "\n";

if ($failed === 0) {
    echo "\n✅ ALL TESTS PASSED! HYIPLab API is working correctly.\n";
} else {
    echo "\n❌ Some tests failed. Please review the errors above.\n";
}

// Save detailed results to file
$report_file = dirname(__FILE__) . '/hyiplab-api-test-results.json';
file_put_contents($report_file, json_encode($test_results, JSON_PRETTY_PRINT));

echo "\nDetailed results saved to: $report_file\n";
echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";
?> 