<?php
/**
 * Check for internal container URLs in WordPress database
 */

// Load WordPress
require_once(__DIR__ . '/wp-config.php');
require_once(__DIR__ . '/wp-load.php');

echo "Checking for internal container URLs in WordPress database...\n";

global $wpdb;

// Check options table
$options = $wpdb->get_results("SELECT option_name, option_value FROM wp_options WHERE option_value LIKE '%wordpress:%' OR option_value LIKE '%172.%' OR option_value LIKE '%localhost:8888'");

if ($options) {
    echo "Found internal URLs in options:\n";
    foreach ($options as $option) {
        echo "  {$option->option_name}: {$option->option_value}\n";
    }
} else {
    echo "No internal URLs found in options table.\n";
}

// Check posts table
$posts = $wpdb->get_results("SELECT ID, post_title FROM wp_posts WHERE post_content LIKE '%wordpress:%' OR post_content LIKE '%172.%' OR post_content LIKE '%localhost:8888'");

if ($posts) {
    echo "Found internal URLs in posts:\n";
    foreach ($posts as $post) {
        echo "  Post ID {$post->ID}: {$post->post_title}\n";
    }
} else {
    echo "No internal URLs found in posts table.\n";
}

// Check postmeta table
$postmeta = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM wp_postmeta WHERE meta_value LIKE '%wordpress:%' OR meta_value LIKE '%172.%' OR meta_value LIKE '%localhost:8888'");

if ($postmeta) {
    echo "Found internal URLs in postmeta:\n";
    foreach ($postmeta as $meta) {
        echo "  Post ID {$meta->post_id}, {$meta->meta_key}: {$meta->meta_value}\n";
    }
} else {
    echo "No internal URLs found in postmeta table.\n";
}

echo "Check completed.\n";
?> 