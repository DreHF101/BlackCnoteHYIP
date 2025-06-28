<?php
/**
 * Fix WordPress URLs for Docker Environment
 * This script updates the WordPress database to use the correct URLs
 */

// Load WordPress
require_once __DIR__ . '/../blackcnote/wp-config.php';
require_once __DIR__ . '/../blackcnote/wp-load.php';

// Ensure we're in CLI mode
if (!defined('WP_CLI') && php_sapi_name() !== 'cli') {
    die('This script should be run from command line');
}

echo "🔧 Fixing WordPress URLs for Docker Environment\n";
echo "================================================\n\n";

// Define the correct URLs
$correct_home = 'http://localhost:8888';
$correct_siteurl = 'http://localhost:8888';

// Get current URLs
$current_home = get_option('home');
$current_siteurl = get_option('siteurl');

echo "Current URLs:\n";
echo "  Home: $current_home\n";
echo "  Site URL: $current_siteurl\n\n";

echo "Target URLs:\n";
echo "  Home: $correct_home\n";
echo "  Site URL: $correct_siteurl\n\n";

// Update URLs if they're different
$updated = false;

if ($current_home !== $correct_home) {
    update_option('home', $correct_home);
    echo "✅ Updated home URL: $current_home → $correct_home\n";
    $updated = true;
}

if ($current_siteurl !== $correct_siteurl) {
    update_option('siteurl', $correct_siteurl);
    echo "✅ Updated site URL: $current_siteurl → $correct_siteurl\n";
    $updated = true;
}

// Search and replace in content
if ($current_home !== $correct_home || $current_siteurl !== $correct_siteurl) {
    echo "\n🔄 Updating content URLs...\n";
    
    // Update posts
    $posts_updated = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
        $current_home,
        $correct_home
    ));
    
    // Update post meta
    $meta_updated = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s)",
        $current_home,
        $correct_home
    ));
    
    // Update options
    $options_updated = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->options} SET option_value = REPLACE(option_value, %s, %s) WHERE option_name NOT IN ('home', 'siteurl')",
        $current_home,
        $correct_home
    ));
    
    echo "✅ Updated $posts_updated posts\n";
    echo "✅ Updated $meta_updated post meta entries\n";
    echo "✅ Updated $options_updated options\n";
    $updated = true;
}

if (!$updated) {
    echo "✅ URLs are already correct!\n";
} else {
    echo "\n🎉 URL fix completed successfully!\n";
    echo "\nYou can now access WordPress at:\n";
    echo "  Main Site: $correct_home\n";
    echo "  Admin Panel: $correct_home/wp-admin/\n";
}

echo "\n"; 