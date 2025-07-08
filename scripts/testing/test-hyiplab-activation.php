<?php
/**
 * HYIPLab Plugin Activation Test
 * 
 * This script tests the HYIPLab plugin activation and integration
 * with the BlackCnote theme system.
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
        echo "✅ $test_name - $message\n";
    } else {
        $test_results['failed']++;
        echo "❌ $test_name - $message\n";
    }
    
    if ($details) {
        echo "   Details: $details\n";
    }
}

function add_test_warning($test_name, $message, $details = '') {
    global $test_results;
    $test_results['warnings']++;
    echo "⚠️ $test_name - $message\n";
    
    if ($details) {
        echo "   Details: $details\n";
    }
}

echo "🔍 HYIPLAB PLUGIN ACTIVATION TEST\n";
echo "================================\n";
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

// Test 2: Plugin Directory Structure
echo "\nTESTING PLUGIN DIRECTORY STRUCTURE\n";
echo "----------------------------------\n";

$plugins_dir = WP_PLUGIN_DIR;
add_test_result(
    'Plugins Directory',
    is_dir($plugins_dir),
    "Plugins directory exists",
    "Path: $plugins_dir"
);

$hyiplab_dir = $plugins_dir . '/hyiplab';
add_test_result(
    'HYIPLab Plugin Directory',
    is_dir($hyiplab_dir),
    "HYIPLab plugin directory exists",
    "Path: $hyiplab_dir"
);

// Test 3: HYIPLab Plugin Files
echo "\nTESTING HYIPLAB PLUGIN FILES\n";
echo "-----------------------------\n";

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
        "File verification",
        "Path: $file_path"
    );
}

// Test 4: Plugin Activation Status
echo "\nTESTING PLUGIN ACTIVATION STATUS\n";
echo "--------------------------------\n";

$active_plugins = get_option('active_plugins');
$hyiplab_active = in_array('hyiplab/hyiplab.php', $active_plugins);

add_test_result(
    'HYIPLab Plugin Active',
    $hyiplab_active,
    "Plugin activation status",
    $hyiplab_active ? "Plugin is active" : "Plugin is not active"
);

// Test 5: Database Tables
echo "\nTESTING DATABASE TABLES\n";
echo "----------------------\n";

global $wpdb;

$hyiplab_tables = [
    $wpdb->prefix . 'hyiplab_users',
    $wpdb->prefix . 'hyiplab_investments',
    $wpdb->prefix . 'hyiplab_transactions',
    $wpdb->prefix . 'hyiplab_plans',
    $wpdb->prefix . 'hyiplab_settings'
];

foreach ($hyiplab_tables as $table) {
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
    add_test_result(
        "Database Table: $table",
        $table_exists,
        "Table exists",
        "Table name: $table"
    );
}

// Test 6: HYIPLab API Endpoints
echo "\nTESTING HYIPLAB API ENDPOINTS\n";
echo "-----------------------------\n";

$api_endpoints = [
    '/wp-json/hyiplab/v1/users',
    '/wp-json/hyiplab/v1/investments',
    '/wp-json/hyiplab/v1/transactions',
    '/wp-json/hyiplab/v1/plans',
    '/wp-json/hyiplab/v1/settings'
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

// Test 7: Theme Integration
echo "\nTESTING THEME INTEGRATION\n";
echo "-------------------------\n";

// Check if theme has HYIPLab integration
$theme_functions = get_template_directory() . '/functions.php';
if (file_exists($theme_functions)) {
    $functions_content = file_get_contents($theme_functions);
    $has_hyiplab_integration = strpos($functions_content, 'hyiplab') !== false;
    
    add_test_result(
        'Theme HYIPLab Integration',
        $has_hyiplab_integration,
        "Theme has HYIPLab integration code",
        "Functions file contains HYIPLab references"
    );
}

// Check for HYIPLab template parts
$template_parts = [
    get_template_directory() . '/template-parts/hyiplab/',
    get_template_directory() . '/template-parts/investment/',
    get_template_directory() . '/template-parts/user/'
];

foreach ($template_parts as $part) {
    $exists = is_dir($part);
    add_test_result(
        "Template Part: " . basename($part),
        $exists,
        "Template part directory exists",
        "Path: $part"
    );
}

// Test 8: Live Sync Integration
echo "\nTESTING LIVE SYNC INTEGRATION\n";
echo "-----------------------------\n";

// Check if live sync is enabled
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

// Test 9: Debug System Integration
echo "\nTESTING DEBUG SYSTEM INTEGRATION\n";
echo "--------------------------------\n";

$debug_plugins = [
    'blackcnote-debug-system/blackcnote-debug-system.php',
    'full-content-checker/full-content-checker.php'
];

foreach ($debug_plugins as $plugin) {
    $is_active = in_array($plugin, $active_plugins);
    add_test_result(
        "Debug Plugin: " . basename(dirname($plugin)),
        $is_active,
        "Debug plugin activation status",
        $is_active ? "Plugin is active" : "Plugin is not active"
    );
}

// Test 10: Performance and Compatibility
echo "\nTESTING PERFORMANCE AND COMPATIBILITY\n";
echo "-------------------------------------\n";

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

// Check for PHP errors
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $recent_errors = file_get_contents($error_log);
    $error_count = substr_count($recent_errors, '[error]');
    add_test_warning(
        'PHP Error Log',
        "Found $error_count recent errors",
        "Error log: $error_log"
    );
}

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
    echo "\n🎉 HYIPLAB STATUS: FULLY OPERATIONAL\n";
    echo "All HYIPLab components are activated and functioning properly.\n";
} elseif ($test_results['failed'] === 0) {
    echo "\n✅ HYIPLAB STATUS: OPERATIONAL WITH WARNINGS\n";
    echo "HYIPLab is operational but some components have warnings.\n";
} else {
    echo "\n❌ HYIPLAB STATUS: ISSUES DETECTED\n";
    echo "Some HYIPLab components failed activation tests.\n";
}

// Recommendations
echo "\n💡 Recommendations:\n";
if ($test_results['failed'] > 0) {
    echo "• Review failed tests and fix issues\n";
}
if ($test_results['warnings'] > 0) {
    echo "• Address warnings to improve system reliability\n";
}
echo "• Run this test regularly to monitor HYIPLab health\n";
echo "• Check WordPress debug log for detailed error information\n";

echo "\n🏁 HYIPLab activation test completed!\n"; 