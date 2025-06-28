<?php
/**
 * Fix WordPress URLs for BlackCnote subdirectory
 * Run this script to update database URLs
 */

// Load WordPress (correct path for container)
require_once(__DIR__ . '/wp-config.php');
require_once(__DIR__ . '/wp-load.php');

// Check if we're running from command line or web
$is_cli = (php_sapi_name() === 'cli');

if ($is_cli) {
    echo "Fixing WordPress URLs for BlackCnote subdirectory...\n";
} else {
    echo "<h1>Fixing WordPress URLs for BlackCnote subdirectory</h1>";
}

// Current and new URLs (use external URL that users will access)
$old_urls = [
    'http://wordpress:8888',
    'http://wordpress:80',
    'http://localhost:8888',
    'http://localhost'
];
$new_url = 'http://localhost:8888/blackcnote';

// Update options
$options_to_update = [
    'home' => $new_url,
    'siteurl' => $new_url
];

$updated_count = 0;

foreach ($options_to_update as $option_name => $new_value) {
    $current_value = get_option($option_name);
    
    if ($current_value !== $new_value) {
        update_option($option_name, $new_value);
        if ($is_cli) {
            echo "Updated option '{$option_name}': {$current_value} -> {$new_value}\n";
        } else {
            echo "<p>Updated option '{$option_name}': {$current_value} -> {$new_value}</p>";
        }
        $updated_count++;
    } else {
        if ($is_cli) {
            echo "Option '{$option_name}' already correct: {$current_value}\n";
        } else {
            echo "<p>Option '{$option_name}' already correct: {$current_value}</p>";
        }
    }
}

// Update posts content (replace old URLs in content)
global $wpdb;

$posts_updated = 0;
foreach ($old_urls as $old_url) {
    $posts_updated += $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
            $old_url,
            $new_url,
            '%' . $wpdb->esc_like($old_url) . '%'
        )
    );
}

if ($is_cli) {
    echo "Updated {$posts_updated} posts with new URL\n";
} else {
    echo "<p>Updated {$posts_updated} posts with new URL</p>";
}

// Update postmeta
$postmeta_updated = 0;
foreach ($old_urls as $old_url) {
    $postmeta_updated += $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
            $old_url,
            $new_url,
            '%' . $wpdb->esc_like($old_url) . '%'
        )
    );
}

if ($is_cli) {
    echo "Updated {$postmeta_updated} postmeta entries with new URL\n";
} else {
    echo "<p>Updated {$postmeta_updated} postmeta entries with new URL</p>";
}

// Update options that might contain URLs
$options_updated = 0;
foreach ($old_urls as $old_url) {
    $options_updated += $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->options} SET option_value = REPLACE(option_value, %s, %s) WHERE option_value LIKE %s",
            $old_url,
            $new_url,
            '%' . $wpdb->esc_like($old_url) . '%'
        )
    );
}

if ($is_cli) {
    echo "Updated {$options_updated} option values with new URL\n";
} else {
    echo "<p>Updated {$options_updated} option values with new URL</p>";
}

// Clear any caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

if (function_exists('w3tc_flush_all')) {
    w3tc_flush_all();
}

if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
}

if ($is_cli) {
    echo "\nURL fix completed! Total updates: " . ($updated_count + $posts_updated + $postmeta_updated + $options_updated) . "\n";
    echo "You can now access your site at: {$new_url}\n";
} else {
    echo "<h2>URL fix completed!</h2>";
    echo "<p>Total updates: " . ($updated_count + $posts_updated + $postmeta_updated + $options_updated) . "</p>";
    echo "<p>You can now access your site at: <a href='{$new_url}'>{$new_url}</a></p>";
}
?> 