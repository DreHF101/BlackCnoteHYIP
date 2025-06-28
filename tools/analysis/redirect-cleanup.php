<?php
/**
 * Deep Redirect Cleanup Script for WordPress
 * Access via: http://localhost:8888/deep-redirect-cleanup.php
 * DELETE THIS FILE AFTER USE!
 */
require_once('wp-config.php');
require_once('wp-load.php');
global $wpdb;

$expected_url = 'http://localhost:8888';
$search_patterns = [
    '/blackcnote',
    'redirect',
    'url',
    'https://localhost'
];

$fix = isset($_POST['fix']) && $_POST['fix'] === '1';
$fixed_count = 0;

$options = $wpdb->get_results("SELECT option_id, option_name, option_value FROM {$wpdb->options}");
echo "<h2>Deep Redirect Cleanup</h2>";
echo "<form method='post'><input type='hidden' name='fix' value='1'><button type='submit'>Fix All Detected Redirects/URLs</button></form>";
echo "<ul>";
foreach ($options as $opt) {
    foreach ($search_patterns as $pattern) {
        if (stripos($opt->option_value, $pattern) !== false) {
            echo "<li><strong>{$opt->option_name}</strong>: " . htmlspecialchars($opt->option_value) . "</li>";
            if ($fix) {
                $new_value = str_replace([
                    'http://localhost/blackcnote',
                    '/blackcnote',
                    'https://localhost'
                ], $expected_url, $opt->option_value);
                // For redirect options, blank them
                if (stripos($opt->option_name, 'redirect') !== false) {
                    $new_value = '';
                }
                $wpdb->update($wpdb->options, ['option_value' => $new_value], ['option_id' => $opt->option_id]);
                $fixed_count++;
            }
            break;
        }
    }
}
echo "</ul>";
if ($fix) {
    echo "<p style='color:green'>✅ Fixed $fixed_count options. Please reload your site and delete this file for security.</p>";
} else {
    echo "<p style='color:blue'>Review the above options. Click the button to fix all detected issues.</p>";
} 