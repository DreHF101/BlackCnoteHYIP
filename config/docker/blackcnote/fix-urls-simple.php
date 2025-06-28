<?php
// Fix URLs Simple Script - Fixed with proper error handling
// This script updates WordPress URLs to localhost:8888

// Check if running from command line
if (php_sapi_name() !== 'cli') {
    die("This script must be run from command line\n");
}

// Check if wp-config.php exists
if (!file_exists('wp-config.php')) {
    die("Error: wp-config.php not found. Please run this script from the WordPress root directory.\n");
}

require_once 'wp-config.php';

// Check if WordPress is loaded
if (!function_exists('get_option')) {
    require_once 'wp-load.php';
}

// Validate WordPress is loaded
if (!function_exists('get_option')) {
    die("Error: WordPress not loaded properly\n");
}

echo "=== BlackCnote URL Fix Script ===\n";
echo "Current URLs:\n";

try {
    $home_url = get_option('home');
    $site_url = get_option('siteurl');
    
    echo "Home: " . ($home_url ?: 'NOT SET') . "\n";
    echo "Site URL: " . ($site_url ?: 'NOT SET') . "\n";
    
    echo "\nUpdating URLs...\n";
    
    // Update URLs with validation
    $new_home = 'http://localhost:8888';
    $new_siteurl = 'http://localhost:8888';
    
    $home_result = update_option('home', $new_home);
    $siteurl_result = update_option('siteurl', $new_siteurl);
    
    if ($home_result && $siteurl_result) {
        echo "✓ URLs updated successfully!\n";
    } else {
        echo "⚠ Some URLs may not have been updated\n";
    }
    
    echo "\nNew URLs:\n";
    echo "Home: " . get_option('home') . "\n";
    echo "Site URL: " . get_option('siteurl') . "\n";
    
    // Also update wp_options table directly if needed
    global $wpdb;
    if (isset($wpdb)) {
        $wpdb->update(
            $wpdb->options,
            array('option_value' => $new_home),
            array('option_name' => 'home')
        );
        $wpdb->update(
            $wpdb->options,
            array('option_value' => $new_siteurl),
            array('option_name' => 'siteurl')
        );
        echo "✓ Database updated directly\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== URL Fix Complete ===\n";
?> 