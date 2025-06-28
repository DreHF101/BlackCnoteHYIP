<?php
/**
 * Automated WordPress URL Checker & Fixer
 * Access via: http://localhost:8888/check-fix-urls.php
 * DELETE THIS FILE AFTER USE!
 */

require_once('wp-config.php');
require_once('wp-load.php');
global $wpdb;

$expected_url = 'http://localhost:8888';
$fixed = false;

// Get current values
echo "<h2>WordPress URL Checker & Fixer</h2>";
$home = get_option('home');
$siteurl = get_option('siteurl');
echo "<p><strong>Current home:</strong> $home</p>";
echo "<p><strong>Current siteurl:</strong> $siteurl</p>";

if ($home !== $expected_url) {
    update_option('home', $expected_url);
    echo "<p style='color:green'>✅ Updated home to $expected_url</p>";
    $fixed = true;
}
if ($siteurl !== $expected_url) {
    update_option('siteurl', $expected_url);
    echo "<p style='color:green'>✅ Updated siteurl to $expected_url</p>";
    $fixed = true;
}

// Print any suspicious options
$options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE '%redirect%' OR option_name LIKE '%url%'");
echo "<h3>Other URL/Redirect-related options:</h3><ul>";
foreach ($options as $opt) {
    echo "<li><strong>{$opt->option_name}:</strong> " . htmlspecialchars($opt->option_value) . "</li>";
}
echo "</ul>";

if ($fixed) {
    echo "<p style='color:blue'>Reload your site and test again. If the issue persists, delete this file for security.</p>";
} else {
    echo "<p style='color:blue'>No changes needed. If the issue persists, check for .htaccess or server-level redirects.</p>";
} 