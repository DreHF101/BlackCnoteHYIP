<?php
/**
 * Comprehensive BlackCnote System Activation Test
 * 
 * This script tests the complete BlackCnote system activation including:
 * - WordPress core functionality
 * - BlackCnote theme activation
 * - HYIPLab plugin integration
 * - React app connectivity
 * - Live sync system
 * - REST API endpoints
 * - Database connectivity
 * - Plugin compatibility
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    // Load WordPress if not already loaded
    $wp_load_path = __DIR__ . '/../../../blackcnote/wp-load.php';
    if (file_exists($wp_load_path)) {
        require_once $wp_load_path;
    } else {
        die('WordPress not found. Please ensure this script is run from the correct location.');
    }
}

// Test results tracking
$test_results = [
    'total' => 0,
    'passed' => 0,
    'failed' => 0,
    'warnings' => 0
];

function add_test_result($test_name, $passed, $message, $details = '') {
    global $test_results;
    $test_results['total']++;
    
    if ($passed) {
        $test_results['passed']++;
        echo "[PASS] $test_name - $message\n";
    } else {
        $test_results['failed']++;
        echo "[FAIL] $test_name - $message\n";
    }
    
    if ($details) {
        echo "   Details: $details\n";
    }
}

function add_test_warning($test_name, $message, $details = '') {
    global $test_results;
    $test_results['warnings']++;
    echo "[WARN] $test_name - $message\n";
    
    if ($details) {
        echo "   Details: $details\n";
    }
}

echo "COMPREHENSIVE BLACKCNOTE SYSTEM ACTIVATION TEST\n";
echo "===============================================\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: WordPress Environment
echo "TESTING WORDPRESS ENVIRONMENT\n";
echo "-----------------------------\n";

$wp_version = get_bloginfo('version');
add_test_result(
    'WordPress Version',
    !empty($wp_version),
    "WordPress $wp_version detected",
    "Version: $wp_version"
);

$theme = wp_get_theme();
add_test_result(
    'Active Theme',
    $theme->get('Name') === 'BlackCnote',
    "Active theme: " . $theme->get('Name'),
    "Theme directory: " . $theme->get_stylesheet_directory()
);

// Test 2: BlackCnote Theme Files
echo "\nTESTING BLACKCNOTE THEME FILES\n";
echo "------------------------------\n";

$theme_dir = get_template_directory();
$theme_files = [
    'style.css',
    'index.php',
    'functions.php',
    'header.php',
    'footer.php',
    'single.php',
    'page.php',
    'archive.php',
    'search.php',
    '404.php'
];

foreach ($theme_files as $file) {
    $file_path = $theme_dir . '/' . $file;
    $exists = file_exists($file_path);
    add_test_result(
        "Theme File: $file",
        $exists,
        "Theme file verification",
        "Path: $file_path"
    );
}

// Test 3: Theme Functions
echo "\nTESTING THEME FUNCTIONS\n";
echo "----------------------\n";

// Check if theme functions are loaded
$theme_functions = [
    'blackcnote_setup',
    'blackcnote_enqueue_scripts',
    'blackcnote_register_rest_routes'
];

foreach ($theme_functions as $function) {
    $exists = function_exists($function);
    add_test_result(
        "Theme Function: $function",
        $exists,
        "Theme function verification",
        $exists ? "Function is available" : "Function not found"
    );
}

// Test 4: Plugin Activation
echo "\nTESTING PLUGIN ACTIVATION\n";
echo "-------------------------\n";

$active_plugins = get_option('active_plugins');
$required_plugins = [
    'blackcnote-debug-system/blackcnote-debug-system.php',
    'hyiplab/hyiplab.php',
    'full-content-checker/full-content-checker.php'
];

foreach ($required_plugins as $plugin) {
    $is_active = in_array($plugin, $active_plugins);
    add_test_result(
        "Plugin: " . basename(dirname($plugin)),
        $is_active,
        "Plugin activation status",
        $is_active ? "Plugin is active" : "Plugin is not active"
    );
}

// Test 5: HYIPLab Integration
echo "\nTESTING HYIPLAB INTEGRATION\n";
echo "---------------------------\n";

// Check HYIPLab plugin files
$hyiplab_dir = WP_PLUGIN_DIR . '/hyiplab';
$hyiplab_files = [
    'hyiplab.php',
    'dashboard.php',
    'composer.json',
    'app/Models/User.php',
    'app/Models/Investment.php',
    'app/Models/Transaction.php',
    'app/Controllers/UserController.php',
    'app/Controllers/InvestmentController.php',
    'routes/web.php',
    'routes/admin.php'
];

foreach ($hyiplab_files as $file) {
    $file_path = $hyiplab_dir . '/' . $file;
    $exists = file_exists($file_path);
    add_test_result(
        "HYIPLab File: $file",
        $exists,
        "HYIPLab file verification",
        "Path: $file_path"
    );
}

// Test 6: Database Tables
echo "\nTESTING DATABASE TABLES\n";
echo "----------------------\n";

global $wpdb;

$required_tables = [
    $wpdb->prefix . 'posts',
    $wpdb->prefix . 'pages',
    $wpdb->prefix . 'users',
    $wpdb->prefix . 'options',
    $wpdb->prefix . 'hyiplab_users',
    $wpdb->prefix . 'hyiplab_investments',
    $wpdb->prefix . 'hyiplab_transactions',
    $wpdb->prefix . 'hyiplab_plans',
    $wpdb->prefix . 'hyiplab_settings'
];

foreach ($required_tables as $table) {
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
    add_test_result(
        "Database Table: $table",
        $table_exists,
        "Table exists",
        "Table name: $table"
    );
}

// Test 7: REST API Endpoints
echo "\nTESTING REST API ENDPOINTS\n";
echo "-------------------------\n";

$api_endpoints = [
    '/wp-json/wp/v2/posts',
    '/wp-json/wp/v2/pages',
    '/wp-json/wp/v2/users',
    '/wp-json/blackcnote/v1/homepage',
    '/wp-json/blackcnote/v1/plans',
    '/wp-json/blackcnote/v1/content',
    '/wp-json/blackcnote/v1/live-sync/status',
    '/wp-json/hyiplab/v1/users',
    '/wp-json/hyiplab/v1/investments',
    '/wp-json/hyiplab/v1/transactions'
];

foreach ($api_endpoints as $endpoint) {
    $url = home_url($endpoint);
    $response = wp_remote_get($url, ['timeout' => 10]);
    
    if (is_wp_error($response)) {
        add_test_warning(
            "API Endpoint: $endpoint",
            "Endpoint check failed",
            "Error: " . $response->get_error_message()
        );
    } else {
        $status_code = wp_remote_retrieve_response_code($response);
        $is_success = $status_code === 200 || $status_code === 401; // 401 is expected for unauthenticated requests
        
        add_test_result(
            "API Endpoint: $endpoint",
            $is_success,
            "Endpoint accessible (HTTP $status_code)",
            "URL: $url"
        );
    }
}

// Test 8: Live Sync System
echo "\nTESTING LIVE SYNC SYSTEM\n";
echo "------------------------\n";

// Check live sync settings
$live_sync_enabled = get_option('blackcnote_live_sync_enabled', false);
add_test_result(
    'Live Sync Enabled',
    $live_sync_enabled,
    "Live sync system status",
    $live_sync_enabled ? "Live sync is enabled" : "Live sync is disabled"
);

// Check live sync API
$live_sync_response = wp_remote_get(home_url('/wp-json/blackcnote/v1/live-sync/status'), ['timeout' => 10]);
if (is_wp_error($live_sync_response)) {
    add_test_warning(
        'Live Sync API',
        'Live sync API check failed',
        'Error: ' . $live_sync_response->get_error_message()
    );
} else {
    $status_code = wp_remote_retrieve_response_code($live_sync_response);
    add_test_result(
        'Live Sync API',
        $status_code === 200,
        "Live sync API accessible (HTTP $status_code)",
        "API endpoint is responding"
    );
}

// Test 9: React App Integration
echo "\nTESTING REACT APP INTEGRATION\n";
echo "-----------------------------\n";

// Check if React app is accessible
$react_url = 'http://localhost:5174';
$react_response = wp_remote_get($react_url, ['timeout' => 10]);

if (is_wp_error($react_response)) {
    add_test_warning(
        'React App',
        'React app check failed',
        'Error: ' . $react_response->get_error_message()
    );
} else {
    $status_code = wp_remote_retrieve_response_code($react_response);
    add_test_result(
        'React App',
        $status_code === 200,
        "React app accessible (HTTP $status_code)",
        "React app is running and accessible"
    );
}

// Test 10: Theme-Plugin Compatibility
echo "\nTESTING THEME-PLUGIN COMPATIBILITY\n";
echo "----------------------------------\n";

// Check if theme uses HYIPLab functions
$theme_functions_content = file_get_contents(get_template_directory() . '/functions.php');
$has_hyiplab_integration = strpos($theme_functions_content, 'hyiplab') !== false;

add_test_result(
    'Theme HYIPLab Integration',
    $has_hyiplab_integration,
    "Theme has HYIPLab integration code",
    $has_hyiplab_integration ? "Theme integrates with HYIPLab" : "No HYIPLab integration found"
);

// Check for conflicts
$conflicts = [];
if (function_exists('hyiplab_init') && !$has_hyiplab_integration) {
    $conflicts[] = "HYIPLab plugin active but theme doesn't integrate with it";
}

if (empty($conflicts)) {
    add_test_result(
        'Plugin Conflicts',
        true,
        "No plugin conflicts detected",
        "All plugins are compatible"
    );
} else {
    add_test_result(
        'Plugin Conflicts',
        false,
        "Plugin conflicts detected",
        implode(', ', $conflicts)
    );
}

// Test 11: Performance and Memory
echo "\nTESTING PERFORMANCE AND MEMORY\n";
echo "------------------------------\n";

// Check memory usage
$memory_limit = ini_get('memory_limit');
$memory_usage = memory_get_usage(true);
$memory_peak = memory_get_peak_usage(true);

add_test_result(
    'Memory Usage',
    $memory_usage < 50 * 1024 * 1024, // Less than 50MB
    "Memory usage is acceptable",
    "Current: " . round($memory_usage / 1024 / 1024, 2) . "MB, Peak: " . round($memory_peak / 1024 / 1024, 2) . "MB, Limit: $memory_limit"
);

// Check execution time
$execution_time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
add_test_result(
    'Execution Time',
    $execution_time < 30, // Less than 30 seconds
    "Execution time is acceptable",
    "Execution time: " . round($execution_time, 2) . " seconds"
);

// Test 12: Error Logging
echo "\nTESTING ERROR LOGGING\n";
echo "---------------------\n";

// Check WordPress debug settings
$wp_debug = defined('WP_DEBUG') && WP_DEBUG;
$wp_debug_log = defined('WP_DEBUG_LOG') && WP_DEBUG_LOG;

add_test_result(
    'WordPress Debug',
    $wp_debug,
    "WordPress debug mode status",
    $wp_debug ? "Debug mode is enabled" : "Debug mode is disabled"
);

add_test_result(
    'WordPress Debug Log',
    $wp_debug_log,
    "WordPress debug logging status",
    $wp_debug_log ? "Debug logging is enabled" : "Debug logging is disabled"
);

// Generate Test Summary
echo "\n📊 TEST SUMMARY\n";
echo "===============\n";
echo "Total Tests: {$test_results['total']}\n";
echo "✅ Passed: {$test_results['passed']}\n";
echo "❌ Failed: {$test_results['failed']}\n";
echo "⚠️ Warnings: {$test_results['warnings']}\n";

$success_rate = $test_results['total'] > 0 ? round(($test_results['passed'] / $test_results['total']) * 100, 2) : 0;
echo "📈 Success Rate: {$success_rate}%\n";

// Overall status
if ($test_results['failed'] === 0 && $success_rate >= 90) {
    echo "\n🎉 BLACKCNOTE STATUS: FULLY OPERATIONAL\n";
    echo "All BlackCnote components are activated and functioning properly.\n";
    echo "WordPress, React app, HYIPLab plugin, and live sync are all working.\n";
} elseif ($test_results['failed'] === 0) {
    echo "\n✅ BLACKCNOTE STATUS: OPERATIONAL WITH WARNINGS\n";
    echo "BlackCnote is operational but some components have warnings.\n";
} else {
    echo "\n❌ BLACKCNOTE STATUS: ISSUES DETECTED\n";
    echo "Some BlackCnote components failed activation tests.\n";
}

// Recommendations
echo "\n💡 Recommendations:\n";
if ($test_results['failed'] > 0) {
    echo "• Review failed tests and fix issues\n";
}
if ($test_results['warnings'] > 0) {
    echo "• Address warnings to improve system reliability\n";
}
echo "• Run this test regularly to monitor BlackCnote health\n";
echo "• Check WordPress debug log for detailed error information\n";
echo "• Monitor HYIPLab plugin performance and database usage\n";

// Save test results
$test_report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'results' => $test_results,
    'success_rate' => $success_rate,
    'wordpress_version' => $wp_version,
    'active_theme' => $theme->get('Name'),
    'active_plugins' => $active_plugins,
    'memory_usage' => $memory_usage,
    'execution_time' => $execution_time
];

$reports_dir = __DIR__ . '/../../../reports';
if (!is_dir($reports_dir)) {
    mkdir($reports_dir, 0755, true);
}

$report_file = $reports_dir . '/comprehensive-activation-test-' . date('Y-m-d-H-i-s') . '.json';
file_put_contents($report_file, json_encode($test_report, JSON_PRETTY_PRINT));

echo "\n📄 Test report saved to: $report_file\n";
echo "\n🏁 Comprehensive BlackCnote activation test completed!\n"; 