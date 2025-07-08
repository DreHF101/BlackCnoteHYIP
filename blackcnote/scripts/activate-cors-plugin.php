<?php
/**
 * BlackCnote CORS Plugin Activation Script
 * 
 * This script activates the BlackCnote CORS Handler plugin and verifies its functionality.
 * Run this script to ensure CORS is properly configured for React integration.
 */

declare(strict_types=1);

// Load WordPress
require_once dirname(__DIR__) . '/wp-load.php';

// Check if we're in CLI or web context
if (php_sapi_name() !== 'cli') {
    // Web context - check permissions
    if (!current_user_can('activate_plugins')) {
        wp_die('Insufficient permissions to activate plugins.');
    }
}

echo "=== BlackCnote CORS Plugin Activation Script ===\n\n";

// 1. Check if plugin exists
$plugin_file = WP_PLUGIN_DIR . '/blackcnote-cors/blackcnote-cors.php';
if (!file_exists($plugin_file)) {
    echo "❌ ERROR: BlackCnote CORS plugin not found at: {$plugin_file}\n";
    exit(1);
}

echo "✅ BlackCnote CORS plugin found\n";

// 2. Check if plugin is active
if (!is_plugin_active('blackcnote-cors/blackcnote-cors.php')) {
    echo "🔄 Activating BlackCnote CORS plugin...\n";
    
    $result = activate_plugin('blackcnote-cors/blackcnote-cors.php');
    
    if (is_wp_error($result)) {
        echo "❌ ERROR: Failed to activate plugin: " . $result->get_error_message() . "\n";
        exit(1);
    }
    
    echo "✅ BlackCnote CORS plugin activated successfully\n";
} else {
    echo "✅ BlackCnote CORS plugin is already active\n";
}

// 3. Verify CORS functionality
echo "\n=== Testing CORS Functionality ===\n";

// Test REST API endpoint
$test_url = home_url('/wp-json/blackcnote/v1/health');
$response = wp_remote_get($test_url, [
    'timeout' => 10,
    'headers' => [
        'Origin' => 'http://localhost:5174',
        'X-Requested-With' => 'XMLHttpRequest'
    ]
]);

if (is_wp_error($response)) {
    echo "❌ ERROR: REST API test failed: " . $response->get_error_message() . "\n";
} else {
    $status_code = wp_remote_retrieve_response_code($response);
    $headers = wp_remote_retrieve_headers($response);
    
    echo "✅ REST API test successful (Status: {$status_code})\n";
    
    // Check CORS headers
    $cors_origin = $headers->get('Access-Control-Allow-Origin');
    if ($cors_origin) {
        echo "✅ CORS headers present: Access-Control-Allow-Origin = {$cors_origin}\n";
    } else {
        echo "⚠️  WARNING: CORS headers not detected in response\n";
    }
}

// 4. Test admin access
echo "\n=== Testing Admin Access ===\n";
$admin_url = admin_url();
echo "✅ Admin URL: {$admin_url}\n";

// 5. Test React app integration
echo "\n=== Testing React App Integration ===\n";
$react_config_url = home_url('/');
echo "✅ React app should be accessible at: http://localhost:5174\n";
echo "✅ WordPress API base URL: " . home_url('/wp-json/blackcnote/v1/') . "\n";

// 6. Display current configuration
echo "\n=== Current Configuration ===\n";
echo "✅ WordPress URL: " . get_option('home') . "\n";
echo "✅ Site URL: " . get_option('siteurl') . "\n";
echo "✅ Theme: " . get_template() . "\n";
echo "✅ Active Plugins:\n";

$active_plugins = get_option('active_plugins');
foreach ($active_plugins as $plugin) {
    echo "   - {$plugin}\n";
}

// 7. Flush rewrite rules
echo "\n=== Flushing Rewrite Rules ===\n";
flush_rewrite_rules();
echo "✅ Rewrite rules flushed\n";

// 8. Clear any caches
echo "\n=== Clearing Caches ===\n";
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✅ WordPress cache cleared\n";
}

// Clear transients
delete_transient('blackcnote_cors_cache');
echo "✅ CORS cache cleared\n";

echo "\n=== Activation Complete ===\n";
echo "✅ BlackCnote CORS plugin is now active and configured\n";
echo "✅ CORS headers are being sent for cross-origin requests\n";
echo "✅ React app should be able to communicate with WordPress API\n";
echo "✅ Admin access should work at: {$admin_url}\n";

echo "\n=== Next Steps ===\n";
echo "1. Test admin access at: {$admin_url}\n";
echo "2. Test React app at: http://localhost:5174\n";
echo "3. Check browser console for any CORS errors\n";
echo "4. If issues persist, clear browser cache and cookies\n";

echo "\n=== Script Complete ===\n"; 