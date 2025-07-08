<?php
/**
 * WordPress URL Fix Script for BlackCnote
 * 
 * This script fixes common WordPress URL configuration issues that cause
 * admin redirect loops and 404 errors.
 */

// Load WordPress (container path)
require_once '/var/www/html/wp-config.php';
require_once '/var/www/html/wp-load.php';

echo "🔧 BlackCnote WordPress URL Fix Script\n";
echo "=====================================\n\n";

// Check if WordPress is loaded
if (!defined('ABSPATH')) {
    echo "❌ Error: WordPress not loaded properly\n";
    exit(1);
}

echo "✅ WordPress loaded successfully\n";

// Get current database values
$current_home = get_option('home');
$current_siteurl = get_option('siteurl');
$current_rewrite_rules = get_option('rewrite_rules');

echo "\n📊 Current Database Values:\n";
echo "Home URL: " . ($current_home ?: 'NOT SET') . "\n";
echo "Site URL: " . ($current_siteurl ?: 'NOT SET') . "\n";
echo "Rewrite Rules: " . ($current_rewrite_rules ? 'SET' : 'NOT SET') . "\n";

// Define correct URLs
$correct_home = 'http://localhost:8888';
$correct_siteurl = 'http://localhost:8888';

echo "\n🎯 Target URLs:\n";
echo "Home URL: {$correct_home}\n";
echo "Site URL: {$correct_siteurl}\n";

// Fix URLs if they're incorrect
$changes_made = false;

if ($current_home !== $correct_home) {
    echo "\n🔄 Updating Home URL from '{$current_home}' to '{$correct_home}'...\n";
    update_option('home', $correct_home);
    $changes_made = true;
}

if ($current_siteurl !== $correct_siteurl) {
    echo "🔄 Updating Site URL from '{$current_siteurl}' to '{$correct_siteurl}'...\n";
    update_option('siteurl', $correct_siteurl);
    $changes_made = true;
}

// Clear rewrite rules to force regeneration
if ($current_rewrite_rules) {
    echo "🔄 Clearing rewrite rules...\n";
    delete_option('rewrite_rules');
    $changes_made = true;
}

// Flush rewrite rules
if ($changes_made) {
    echo "🔄 Flushing rewrite rules...\n";
    flush_rewrite_rules();
}

// Verify the changes
$new_home = get_option('home');
$new_siteurl = get_option('siteurl');

echo "\n✅ Verification:\n";
echo "New Home URL: {$new_home}\n";
echo "New Site URL: {$new_siteurl}\n";

if ($new_home === $correct_home && $new_siteurl === $correct_siteurl) {
    echo "\n🎉 URL configuration fixed successfully!\n";
} else {
    echo "\n⚠️ Some URLs may still be incorrect. Please check manually.\n";
}

// Test admin access
echo "\n🧪 Testing Admin Access...\n";
$admin_url = admin_url();
echo "Admin URL: {$admin_url}\n";

// Test REST API
echo "\n🧪 Testing REST API...\n";
$rest_url = rest_url();
echo "REST API URL: {$rest_url}\n";

// Test if we can access the admin
$admin_response = wp_remote_get($admin_url, ['timeout' => 10]);
if (!is_wp_error($admin_response)) {
    $admin_status = wp_remote_retrieve_response_code($admin_response);
    echo "Admin page status: {$admin_status}\n";
    
    if ($admin_status === 200) {
        echo "✅ Admin page accessible\n";
    } else {
        echo "⚠️ Admin page returned status {$admin_status}\n";
    }
} else {
    echo "❌ Error accessing admin: " . $admin_response->get_error_message() . "\n";
}

// Test REST API endpoint
$rest_response = wp_remote_get($rest_url . 'blackcnote/v1/health', ['timeout' => 10]);
if (!is_wp_error($rest_response)) {
    $rest_status = wp_remote_retrieve_response_code($rest_response);
    echo "REST API status: {$rest_status}\n";
    
    if ($rest_status === 200) {
        echo "✅ REST API accessible\n";
    } else {
        echo "⚠️ REST API returned status {$rest_status}\n";
    }
} else {
    echo "❌ Error accessing REST API: " . $rest_response->get_error_message() . "\n";
}

echo "\n🔧 Additional Fixes:\n";

// Check and fix .htaccess
$htaccess_file = ABSPATH . '.htaccess';
if (file_exists($htaccess_file)) {
    echo "✅ .htaccess file exists\n";
    
    $htaccess_content = file_get_contents($htaccess_file);
    if (strpos($htaccess_content, 'RewriteEngine On') === false) {
        echo "⚠️ .htaccess may need rewrite rules\n";
    } else {
        echo "✅ .htaccess has rewrite rules\n";
    }
} else {
    echo "⚠️ .htaccess file not found\n";
}

// Check if plugins directory is accessible
$plugins_dir = WP_PLUGIN_DIR;
if (is_dir($plugins_dir)) {
    echo "✅ Plugins directory accessible\n";
    
    // Check if our CORS plugin exists
    $cors_plugin = $plugins_dir . '/blackcnote-cors/blackcnote-cors.php';
    if (file_exists($cors_plugin)) {
        echo "✅ BlackCnote CORS plugin found\n";
    } else {
        echo "⚠️ BlackCnote CORS plugin not found\n";
    }
} else {
    echo "❌ Plugins directory not accessible\n";
}

// Check if themes directory is accessible
$themes_dir = get_template_directory();
if (is_dir($themes_dir)) {
    echo "✅ Themes directory accessible\n";
    
    // Check if BlackCnote theme exists
    $blackcnote_theme = $themes_dir . '/blackcnote';
    if (is_dir($blackcnote_theme)) {
        echo "✅ BlackCnote theme found\n";
    } else {
        echo "⚠️ BlackCnote theme not found\n";
    }
} else {
    echo "❌ Themes directory not accessible\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. Try accessing http://localhost:8888/wp-admin/\n";
echo "2. If still having issues, check Docker container logs\n";
echo "3. Verify all services are running: docker ps\n";
echo "4. Test REST API: curl http://localhost:8888/wp-json/blackcnote/v1/health\n";

echo "\n✨ Script completed!\n"; 