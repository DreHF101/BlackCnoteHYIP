<?php
/**
 * BlackCnote Server Performance Test
 * Quick test to identify performance issues
 */

require_once dirname(__FILE__) . '/wp-load.php';

echo "=== BlackCnote Server Performance Test ===\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
$start = microtime(true);
global $wpdb;
$result = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
$duration = (microtime(true) - $start) * 1000;
echo "   Duration: {$duration}ms\n";

// Test 2: API Endpoints
echo "\n2. Testing API Endpoints...\n";
$endpoints = ['/wp-json/blackcnote/v1/homepage', '/wp-json/blackcnote/v1/stats', '/wp-json/blackcnote/v1/plans'];

foreach ($endpoints as $endpoint) {
    $start = microtime(true);
    $response = wp_remote_get(home_url($endpoint), ['timeout' => 30]);
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
$tables = ['hyiplab_users', 'hyiplab_investments', 'hyiplab_transactions', 'hyiplab_plans'];

foreach ($tables as $table) {
    $full_table = $wpdb->prefix . $table;
    $start = microtime(true);
    $exists = $wpdb->get_var("SHOW TABLES LIKE '{$full_table}'") === $full_table;
    $duration = (microtime(true) - $start) * 1000;
    
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$full_table}");
        echo "   {$table}: {$count} records ({$duration}ms)\n";
    } else {
        echo "   {$table}: Does not exist ({$duration}ms)\n";
    }
}

// Test 4: Memory Usage
echo "\n4. Testing Memory Usage...\n";
$memory_usage = memory_get_usage(true);
$memory_peak = memory_get_peak_usage(true);
$memory_limit = ini_get('memory_limit');

echo "   Current: " . round($memory_usage / 1024 / 1024, 2) . " MB\n";
echo "   Peak: " . round($memory_peak / 1024 / 1024, 2) . " MB\n";
echo "   Limit: {$memory_limit}\n";

// Test 5: Plugin Status
echo "\n5. Testing Plugin Status...\n";
$plugins = ['blackcnote-cors', 'hyiplab', 'full-content-checker'];

foreach ($plugins as $plugin) {
    $active = is_plugin_active($plugin . '/' . $plugin . '.php');
    echo "   {$plugin}: " . ($active ? 'ACTIVE' : 'INACTIVE') . "\n";
}

// Test 6: CORS Headers
echo "\n6. Testing CORS Headers...\n";
$response = wp_remote_get(home_url('/wp-json/blackcnote/v1/health'), [
    'headers' => ['Origin' => 'http://localhost:5174']
]);

if (!is_wp_error($response)) {
    $headers = wp_remote_retrieve_headers($response);
    $cors_header = $headers->get('Access-Control-Allow-Origin');
    echo "   CORS Header: " . ($cors_header ?: 'NOT SET') . "\n";
} else {
    echo "   CORS Test: FAILED - " . $response->get_error_message() . "\n";
}

echo "\n=== Test Complete ===\n";

// Check for specific issues
echo "\n🔍 ISSUE ANALYSIS:\n";

// Check if stats endpoint is slow
$stats_response = wp_remote_get(home_url('/wp-json/blackcnote/v1/stats'));
if (!is_wp_error($stats_response)) {
    $body = wp_remote_retrieve_body($stats_response);
    $data = json_decode($body, true);
    
    if ($data && isset($data['totalUsers']) && $data['totalUsers'] > 10000) {
        echo "⚠️  DEMO DATA DETECTED: High user count ({$data['totalUsers']}) suggests demo data\n";
    }
}

// Check for empty tables
foreach ($tables as $table) {
    $full_table = $wpdb->prefix . $table;
    if ($wpdb->get_var("SHOW TABLES LIKE '{$full_table}'") === $full_table) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$full_table}");
        if ($count == 0) {
            echo "⚠️  EMPTY TABLE: {$table} has no data\n";
        }
    }
}

echo "\n💡 RECOMMENDATIONS:\n";
echo "1. If API endpoints are slow (>2s), implement caching\n";
echo "2. If demo data detected, replace with realistic data\n";
echo "3. If tables are empty, create sample data\n";
echo "4. Monitor memory usage and increase limit if needed\n";
echo "5. Check error logs for specific issues\n"; 