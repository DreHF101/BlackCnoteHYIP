<?php
/**
 * Debug WordPress Redirect Loop
 * Access via: http://localhost:8888/debug-redirect.php
 */

require_once('wp-config.php');
require_once('wp-load.php');
global $wpdb;

echo "<h2>WordPress Redirect Debug</h2>";

// Check if WordPress is loaded
if (!defined('ABSPATH')) {
    echo "<p style='color:red'>❌ WordPress not loaded properly</p>";
    exit;
}

echo "<p style='color:green'>✅ WordPress loaded successfully</p>";

// Check database connection
if ($wpdb->db_connect()) {
    echo "<p style='color:green'>✅ Database connected</p>";
} else {
    echo "<p style='color:red'>❌ Database connection failed</p>";
    exit;
}

// Check if tables exist
$tables = $wpdb->get_col("SHOW TABLES");
echo "<p><strong>Database tables:</strong> " . count($tables) . " found</p>";

if (count($tables) == 0) {
    echo "<p style='color:orange'>⚠️ No tables found - fresh database</p>";
} else {
    echo "<p style='color:green'>✅ Database has tables</p>";
}

// Check WordPress options
$options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name IN ('home', 'siteurl', 'rewrite_rules')");

echo "<h3>WordPress Options:</h3><ul>";
foreach ($options as $opt) {
    echo "<li><strong>{$opt->option_name}:</strong> " . htmlspecialchars($opt->option_value) . "</li>";
}
echo "</ul>";

// Check if WordPress is installed
$installed = get_option('siteurl');
if (empty($installed)) {
    echo "<p style='color:orange'>⚠️ WordPress not installed yet</p>";
    echo "<p><a href='http://localhost:8888/wp-admin/install.php'>Click here to install WordPress</a></p>";
} else {
    echo "<p style='color:green'>✅ WordPress appears to be installed</p>";
}

// Check for any redirect-related options
$redirect_options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE '%redirect%' OR option_name LIKE '%url%'");

if (!empty($redirect_options)) {
    echo "<h3>Redirect-related options:</h3><ul>";
    foreach ($redirect_options as $opt) {
        echo "<li><strong>{$opt->option_name}:</strong> " . htmlspecialchars($opt->option_value) . "</li>";
    }
    echo "</ul>";
}

echo "<h3>Configuration Check:</h3>";
echo "<ul>";
echo "<li><strong>WP_HOME:</strong> " . (defined('WP_HOME') ? WP_HOME : 'Not defined') . "</li>";
echo "<li><strong>WP_SITEURL:</strong> " . (defined('WP_SITEURL') ? WP_SITEURL : 'Not defined') . "</li>";
echo "<li><strong>ABSPATH:</strong> " . ABSPATH . "</li>";
echo "</ul>";
?> 