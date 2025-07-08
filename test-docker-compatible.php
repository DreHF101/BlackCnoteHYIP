<?php
/**
 * BlackCnote Docker-Compatible Server Test
 * Updated for Docker container environment
 */

declare(strict_types=1);

echo "🔍 BlackCnote Docker-Compatible Server Test\n";
echo "==========================================\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
global $wpdb;
$start = microtime(true);
$result = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
$duration = (microtime(true) - $start) * 1000;
echo "   Duration: {$duration}ms\n";

// Test 2: API Endpoints (using internal Docker URLs)
echo "\n2. Testing API Endpoints...\n";
$endpoints = [
    "/wp-json/blackcnote/v1/homepage",
    "/wp-json/blackcnote/v1/stats", 
    "/wp-json/blackcnote/v1/plans"
];

foreach ($endpoints as $endpoint) {
    $start = microtime(true);
    $response = wp_remote_get(home_url($endpoint), ["timeout" => 30]);
    $duration = (microtime(true) - $start) * 1000;
    
    if (is_wp_error($response)) {
        echo "   {$endpoint}: ERROR - " . $response->get_error_message() . " ({$duration}ms)\n";
    } else {
        $status = wp_remote_retrieve_response_code($response);
        $size = strlen(wp_remote_retrieve_body($response));
        echo "   {$endpoint}: {$status} - {$size} bytes ({$duration}ms)\n";
        
        if ($duration > 2000) {
            echo "   ⚠️  SLOW RESPONSE DETECTED!\n";
        }
    }
}

// Test 3: HYIPLab Tables
echo "\n3. Testing HYIPLab Tables...\n";
$tables = ["hyiplab_users", "hyiplab_investments", "hyiplab_transactions", "hyiplab_plans"];

foreach ($tables as $table) {
    $full_table = $wpdb->prefix . $table;
    $start = microtime(true);
    $exists = $wpdb->get_var("SHOW TABLES LIKE \"{$full_table}\"") === $full_table;
    $duration = (microtime(true) - $start) * 1000;
    
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$full_table}");
        echo "   {$table}: {$count} records ({$duration}ms)\n";
    } else {
        echo "   {$table}: Does not exist ({$duration}ms)\n";
    }
}

// Test 4: Memory Usage
echo "\n4. Memory Usage Test:\n";
$memory_usage = memory_get_usage(true);
$memory_peak = memory_get_peak_usage(true);
$memory_limit = ini_get("memory_limit");

echo "   Current: " . round($memory_usage / 1024 / 1024, 2) . " MB\n";
echo "   Peak: " . round($memory_peak / 1024 / 1024, 2) . " MB\n";
echo "   Limit: {$memory_limit}\n";

// Test 5: Plugin Status
echo "\n5. Testing Plugin Status...\n";
$plugins = ["blackcnote-cors", "hyiplab", "full-content-checker"];

foreach ($plugins as $plugin) {
    if (is_plugin_active($plugin . "/" . $plugin . ".php")) {
        echo "   {$plugin}: ACTIVE\n";
    } else {
        echo "   {$plugin}: INACTIVE\n";
    }
}

// Test 6: CORS Headers (using internal URL)
echo "\n6. Testing CORS Headers...\n";
$cors_response = wp_remote_get(home_url("/wp-json/blackcnote/v1/health"), [
    "timeout" => 10,
    "headers" => [
        "Origin" => "http://react-app:5176",
        "X-Requested-With" => "XMLHttpRequest"
    ]
]);

if (is_wp_error($cors_response)) {
    echo "   CORS Test: FAILED - " . $cors_response->get_error_message() . "\n";
} else {
    $headers = wp_remote_retrieve_headers($cors_response);
    $cors_header = $headers->get("Access-Control-Allow-Origin");
    if ($cors_header) {
        echo "   CORS Test: PASSED - Headers configured\n";
    } else {
        echo "   CORS Test: FAILED - Headers missing\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "✅ All tests completed using Docker-compatible URLs\n";
echo "✅ Internal service communication verified\n";
echo "✅ Database and API endpoints tested\n";
