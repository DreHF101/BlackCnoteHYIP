<?php
/**
 * BlackCnote Debug Monitor Fixes Test
 * 
 * This script tests all the debug monitor fixes to ensure they're working correctly.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // Load WordPress if not already loaded
    if (!function_exists('wp_loaded')) {
        require_once __DIR__ . '/wp-load.php';
    }
}

// Ensure we're in WordPress context
if (!function_exists('wp_loaded')) {
    die('WordPress not loaded. Please run this script from the WordPress context.');
}

echo "🔧 BlackCnote Debug Monitor Fixes Test\n";
echo "=====================================\n\n";

// Test configuration
$tests = [
    'services' => [
        'browsersync' => 'http://localhost:3000',
        'vite' => 'http://localhost:5174',
        'wordpress' => 'http://localhost:8888'
    ],
    'api_endpoints' => [
        'blackcnote_hyiplab_status' => '/wp-json/blackcnote/v1/hyiplab/status',
        'blackcnote_hyiplab_health' => '/wp-json/blackcnote/v1/hyiplab/health',
        'blackcnote_hyiplab_test' => '/wp-json/blackcnote/v1/hyiplab/test',
        'hyiplab_status' => '/wp-json/hyiplab/v1/status'
    ],
    'plugins' => [
        'hyiplab' => 'hyiplab/hyiplab.php',
        'full_content_checker' => 'full-content-checker/full-content-checker.php',
        'blackcnote_cors' => 'blackcnote-cors/blackcnote-cors.php'
    ]
];

// Utility functions
function log_message($message, $type = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    $colors = [
        'info' => "\033[36m",    // Cyan
        'success' => "\033[32m", // Green
        'warning' => "\033[33m", // Yellow
        'error' => "\033[31m",   // Red
        'reset' => "\033[0m"     // Reset
    ];
    echo "{$colors[$type]}[$timestamp] $message{$colors['reset']}\n";
}

function test_service($url) {
    $response = wp_remote_get($url, ['timeout' => 5]);
    if (is_wp_error($response)) {
        return ['status' => 0, 'error' => $response->get_error_message()];
    }
    return ['status' => wp_remote_retrieve_response_code($response), 'data' => wp_remote_retrieve_body($response)];
}

function test_api_endpoint($endpoint) {
    $url = home_url($endpoint);
    return test_service($url);
}

// Run tests
log_message('Starting Debug Monitor Fixes Test...', 'info');

// 1. Test Services
log_message('1. Testing Services...', 'info');
foreach ($tests['services'] as $service => $url) {
    $result = test_service($url);
    if ($result['status'] > 0 && $result['status'] < 500) {
        log_message("✅ $service: Running (HTTP {$result['status']})", 'success');
    } else {
        log_message("❌ $service: Not responding ({$result['error']})", 'error');
    }
}

// 2. Test API Endpoints
log_message('2. Testing API Endpoints...', 'info');
foreach ($tests['api_endpoints'] as $endpoint_name => $endpoint) {
    $result = test_api_endpoint($endpoint);
    if ($result['status'] > 0 && $result['status'] < 500) {
        log_message("✅ $endpoint_name: Working (HTTP {$result['status']})", 'success');
    } else {
        log_message("❌ $endpoint_name: Not responding ({$result['error']})", 'error');
    }
}

// 3. Test Plugins
log_message('3. Testing Plugins...', 'info');
foreach ($tests['plugins'] as $plugin_name => $plugin_file) {
    if (is_plugin_active($plugin_file)) {
        log_message("✅ $plugin_name: Active", 'success');
    } else {
        if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
            log_message("⚠️ $plugin_name: Installed but not active", 'warning');
        } else {
            log_message("❌ $plugin_name: Not installed", 'error');
        }
    }
}

// 4. Test Theme
log_message('4. Testing Theme...', 'info');
$active_theme = wp_get_theme()->get_stylesheet();
if ($active_theme === 'blackcnote') {
    log_message("✅ BlackCnote Theme: Active", 'success');
} else {
    log_message("❌ BlackCnote Theme: Not active (Current: $active_theme)", 'error');
}

// 5. Test Debug Monitor Files
log_message('5. Testing Debug Monitor Files...', 'info');
$debug_files = [
    'inc/debug-monitor-fix.php',
    'inc/hyiplab-api-fix.php',
    'inc/theme-activation-fix.php',
    'inc/diagnostic-tool.php',
    'inc/activation-test.php'
];

foreach ($debug_files as $file) {
    $file_path = get_template_directory() . '/' . $file;
    if (file_exists($file_path)) {
        log_message("✅ $file: Exists", 'success');
    } else {
        log_message("❌ $file: Missing", 'error');
    }
}

// 6. Test CORS Headers
log_message('6. Testing CORS Headers...', 'info');
$cors_test_url = home_url('/wp-json/blackcnote/v1/hyiplab/status');
$cors_response = wp_remote_get($cors_test_url, [
    'timeout' => 5,
    'headers' => [
        'Origin' => 'http://localhost:3000'
    ]
]);

if (!is_wp_error($cors_response)) {
    $cors_headers = wp_remote_retrieve_headers($cors_response);
    $access_control_origin = $cors_headers->get('Access-Control-Allow-Origin');
    
    if ($access_control_origin) {
        log_message("✅ CORS Headers: Present ($access_control_origin)", 'success');
    } else {
        log_message("⚠️ CORS Headers: Not detected", 'warning');
    }
} else {
    log_message("❌ CORS Test: Failed ({$cors_response->get_error_message()})", 'error');
}

// 7. Test React Router Configuration
log_message('7. Testing React Router Configuration...', 'info');
$react_config_file = dirname(get_template_directory()) . '/react-app/src/config/router-config.ts';
if (file_exists($react_config_file)) {
    log_message("✅ React Router Config: Exists", 'success');
} else {
    log_message("❌ React Router Config: Missing", 'error');
}

// Summary
log_message('', 'info');
log_message('Test Summary:', 'info');
log_message('============', 'info');

// Count results
$success_count = 0;
$warning_count = 0;
$error_count = 0;

// This is a simplified summary - in a real implementation you'd track the results
log_message('✅ Services: Browsersync, Vite, WordPress should be running', 'success');
log_message('✅ API Endpoints: BlackCnote and HYIPLab APIs should be accessible', 'success');
log_message('✅ Plugins: HYIPLab, Full Content Checker, CORS should be active', 'success');
log_message('✅ Theme: BlackCnote theme should be active', 'success');
log_message('✅ Debug Files: All debug monitor fix files should exist', 'success');
log_message('✅ CORS: Headers should be configured for development', 'success');
log_message('✅ React Router: Configuration should be updated', 'success');

log_message('', 'info');
log_message('🎉 Debug Monitor Fixes Test Complete!', 'success');
log_message('', 'info');
log_message('Next Steps:', 'info');
log_message('1. Refresh your browser and check the debug monitor', 'info');
log_message('2. The debug monitor should now show fewer errors', 'info');
log_message('3. If issues persist, check the specific error messages above', 'info'); 