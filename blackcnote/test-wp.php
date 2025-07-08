<?php
// Test WordPress functionality
echo "<h1>WordPress Test</h1>";

// Load WordPress
require_once('wp-config.php');
require_once('wp-settings.php');

echo "<h2>Database Connection Test</h2>";
global $wpdb;

if ($wpdb->check_connection()) {
    echo "✅ Database connection successful!<br>";
    
    // Test a simple query
    $result = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
    echo "✅ Database query successful. Posts count: " . $result . "<br>";
    
    // Test WordPress functions
    echo "<h2>WordPress Functions Test</h2>";
    echo "Site URL: " . get_site_url() . "<br>";
    echo "Home URL: " . get_home_url() . "<br>";
    echo "WordPress Version: " . get_bloginfo('version') . "<br>";
    
} else {
    echo "❌ Database connection failed!<br>";
    echo "Error: " . $wpdb->last_error . "<br>";
}

echo "<h2>Test Complete</h2>";
?> 