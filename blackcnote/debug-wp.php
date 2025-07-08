<?php
// Debug WordPress loading
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>WordPress Debug</h1>";

// Test 1: Load wp-config.php
echo "<h2>Test 1: Loading wp-config.php</h2>";
try {
    require_once('wp-config.php');
    echo "✅ wp-config.php loaded successfully<br>";
    echo "DB_NAME: " . DB_NAME . "<br>";
    echo "DB_HOST: " . DB_HOST . "<br>";
} catch (Exception $e) {
    echo "❌ Error loading wp-config.php: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Load wp-settings.php
echo "<h2>Test 2: Loading wp-settings.php</h2>";
try {
    require_once('wp-settings.php');
    echo "✅ wp-settings.php loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Error loading wp-settings.php: " . $e->getMessage() . "<br>";
    exit;
}

// Test 3: Check WordPress globals
echo "<h2>Test 3: WordPress Globals</h2>";
global $wpdb;
if (isset($wpdb)) {
    echo "✅ \$wpdb is set<br>";
    echo "Database connected: " . ($wpdb->check_connection() ? 'Yes' : 'No') . "<br>";
} else {
    echo "❌ \$wpdb is not set<br>";
}

// Test 4: Check WordPress functions
echo "<h2>Test 4: WordPress Functions</h2>";
if (function_exists('get_site_url')) {
    echo "✅ get_site_url() function exists<br>";
    echo "Site URL: " . get_site_url() . "<br>";
} else {
    echo "❌ get_site_url() function not found<br>";
}

if (function_exists('get_home_url')) {
    echo "✅ get_home_url() function exists<br>";
    echo "Home URL: " . get_home_url() . "<br>";
} else {
    echo "❌ get_home_url() function not found<br>";
}

// Test 5: Check if WordPress is installed
echo "<h2>Test 5: WordPress Installation</h2>";
if (defined('WP_INSTALLING') && WP_INSTALLING) {
    echo "⚠️ WordPress is in installation mode<br>";
} else {
    echo "✅ WordPress is not in installation mode<br>";
}

// Test 6: Check for any errors
echo "<h2>Test 6: Error Check</h2>";
$errors = error_get_last();
if ($errors) {
    echo "⚠️ Last error: " . $errors['message'] . "<br>";
} else {
    echo "✅ No errors detected<br>";
}

echo "<h2>Debug Complete</h2>";
?> 