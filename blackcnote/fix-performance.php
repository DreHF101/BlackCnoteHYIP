<?php
/**
 * BlackCnote Performance Fix
 * Simple script to fix server performance issues
 */

// Load WordPress
require_once dirname(__FILE__) . '/wp-load.php';

echo "=== BlackCnote Server Performance Fix ===\n\n";

// Test database connection
global $wpdb;
if ($wpdb->db_connect()) {
    echo "✅ Database connection: OK\n";
    
    // Add database indexes (check if they exist first)
    $indexes = [
        "idx_posts_status_date" => "CREATE INDEX idx_posts_status_date ON {$wpdb->posts}(post_status, post_date)",
        "idx_posts_type_status" => "CREATE INDEX idx_posts_type_status ON {$wpdb->posts}(post_type, post_status)",
        "idx_postmeta_post_key" => "CREATE INDEX idx_postmeta_post_key ON {$wpdb->postmeta}(post_id, meta_key)"
    ];
    
    foreach ($indexes as $index_name => $index_sql) {
        try {
            // Check if index exists
            $exists = $wpdb->get_var("SHOW INDEX FROM {$wpdb->posts} WHERE Key_name = '{$index_name}'");
            if (!$exists) {
                $wpdb->query($index_sql);
                echo "✅ Database index created: {$index_name}\n";
            } else {
                echo "✅ Database index already exists: {$index_name}\n";
            }
        } catch (Exception $e) {
            echo "⚠️ Index creation failed: " . $e->getMessage() . "\n";
        }
    }
    
    // Clean up expired transients
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%' AND option_value < " . time());
    echo "✅ Expired transients cleaned up\n";
    
} else {
    echo "❌ Database connection failed\n";
}

// Test memory usage
$memory_usage = memory_get_usage(true);
$memory_limit = ini_get('memory_limit');
echo "📊 Memory usage: " . round($memory_usage / 1024 / 1024, 2) . "MB / {$memory_limit}\n";

// Test response time
$start_time = microtime(true);
$test_query = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
$end_time = microtime(true);
$response_time = ($end_time - $start_time) * 1000;

echo "📊 Database response time: " . round($response_time, 2) . "ms\n";

if ($response_time > 1000) {
    echo "⚠️ WARNING: Slow database response detected!\n";
} else {
    echo "✅ Database performance: OK\n";
}

// Test API endpoints
echo "\n🔧 Testing API Endpoints...\n";
$endpoints = ['/wp-json/blackcnote/v1/homepage', '/wp-json/blackcnote/v1/stats', '/wp-json/blackcnote/v1/plans'];

foreach ($endpoints as $endpoint) {
    $start = microtime(true);
    // Use internal URL instead of external
    $response = wp_remote_get('http://wordpress' . $endpoint, ['timeout' => 30]);
    $duration = (microtime(true) - $start) * 1000;
    
    if (is_wp_error($response)) {
        echo "  {$endpoint}: ERROR - " . $response->get_error_message() . " ({$duration}ms)\n";
    } else {
        $status = wp_remote_retrieve_response_code($response);
        $size = strlen(wp_remote_retrieve_body($response));
        echo "  {$endpoint}: {$status} - {$size} bytes ({$duration}ms)\n";
        
        if ($duration > 2000) {
            echo "  ⚠️ SLOW RESPONSE DETECTED!\n";
        }
    }
}

echo "\n✅ Server performance fix completed!\n"; 