<?php
/**
 * Fix Database URLs to Stop Redirect Loop
 * Access via: http://localhost:8888/fix-db-urls.php
 */

require_once('wp-config.php');
require_once('wp-load.php');
global $wpdb;

echo "<h2>Fix Database URLs</h2>";

// Get current values
$current_home = get_option('home');
$current_siteurl = get_option('siteurl');

echo "<p><strong>Current home:</strong> $current_home</p>";
echo "<p><strong>Current siteurl:</strong> $current_siteurl</p>";

// Fix the URLs
$new_url = 'http://localhost:8888';

// Update home URL
if ($current_home !== $new_url) {
    update_option('home', $new_url);
    echo "<p style='color:green'>✅ Updated home to $new_url</p>";
} else {
    echo "<p style='color:blue'>ℹ️ Home already correct</p>";
}

// Update site URL
if ($current_siteurl !== $new_url) {
    update_option('siteurl', $new_url);
    echo "<p style='color:green'>✅ Updated siteurl to $new_url</p>";
} else {
    echo "<p style='color:blue'>ℹ️ Site URL already correct</p>";
}

// Clear rewrite rules
delete_option('rewrite_rules');
echo "<p style='color:green'>✅ Cleared rewrite rules</p>";

// Clear any cached options
wp_cache_flush();
echo "<p style='color:green'>✅ Cleared cache</p>";

// Verify the changes
$new_home = get_option('home');
$new_siteurl = get_option('siteurl');

echo "<h3>Updated Values:</h3>";
echo "<p><strong>Home:</strong> $new_home</p>";
echo "<p><strong>Site URL:</strong> $new_siteurl</p>";

echo "<h3>Next Steps:</h3>";
echo "<p>1. <a href='http://localhost:8888/'>Test your site</a></p>";
echo "<p>2. <a href='http://localhost:8888/wp-admin/'>Access admin</a></p>";
echo "<p>3. Delete this file for security</p>";

?> 