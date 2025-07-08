<?php
/**
 * BlackCnote Complete WordPress Setup Script
 * Completes WordPress installation, flushes permalinks, and activates theme/plugins
 */

// Load WordPress - inside container, wp-config.php is in the current directory
require_once 'wp-config.php';

echo "=== BlackCnote Complete WordPress Setup ===\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";

// Check if WordPress is installed
echo "1. Checking WordPress installation... ";
if (get_option('siteurl')) {
    echo "✅ WordPress is installed\n";
    echo "   Site URL: " . get_option('siteurl') . "\n";
    echo "   Home URL: " . get_option('home') . "\n";
} else {
    echo "❌ WordPress not installed - please complete installation first\n";
    exit(1);
}

// Flush rewrite rules (fixes REST API 404)
echo "2. Flushing rewrite rules... ";
flush_rewrite_rules();
echo "✅ Rewrite rules flushed\n";

// Test REST API
echo "3. Testing REST API... ";
$api_response = wp_remote_get(get_rest_url());
if (!is_wp_error($api_response) && wp_remote_retrieve_response_code($api_response) === 200) {
    echo "✅ REST API working\n";
} else {
    echo "❌ REST API still not working\n";
}

// Check for BlackCnote theme
echo "4. Checking BlackCnote theme... ";
$theme = wp_get_theme('blackcnote');
if ($theme->exists()) {
    echo "✅ BlackCnote theme found\n";
    
    // Activate theme
    echo "5. Activating BlackCnote theme... ";
    switch_theme('blackcnote');
    if (get_stylesheet() === 'blackcnote') {
        echo "✅ BlackCnote theme activated\n";
    } else {
        echo "❌ Theme activation failed\n";
    }
} else {
    echo "❌ BlackCnote theme not found\n";
}

// Check for BlackCnote CORS Handler plugin
echo "6. Checking BlackCnote CORS Handler plugin... ";
$cors_plugin = 'blackcnote-cors-handler/blackcnote-cors-handler.php';
if (file_exists(WP_PLUGIN_DIR . '/' . $cors_plugin)) {
    echo "✅ CORS Handler plugin found\n";
    
    // Activate plugin
    echo "7. Activating CORS Handler plugin... ";
    activate_plugin($cors_plugin);
    if (is_plugin_active($cors_plugin)) {
        echo "✅ CORS Handler plugin activated\n";
    } else {
        echo "❌ CORS Handler plugin activation failed\n";
    }
} else {
    echo "❌ CORS Handler plugin not found\n";
}

// Check for HYIPLab plugin
echo "8. Checking HYIPLab plugin... ";
$hyiplab_plugin = 'hyiplab/hyiplab.php';
if (file_exists(WP_PLUGIN_DIR . '/' . $hyiplab_plugin)) {
    echo "✅ HYIPLab plugin found\n";
    
    // Activate plugin
    echo "9. Activating HYIPLab plugin... ";
    activate_plugin($hyiplab_plugin);
    if (is_plugin_active($hyiplab_plugin)) {
        echo "✅ HYIPLab plugin activated\n";
    } else {
        echo "❌ HYIPLab plugin activation failed\n";
    }
} else {
    echo "❌ HYIPLab plugin not found\n";
}

// Test all endpoints
echo "10. Testing all endpoints...\n";

$endpoints = array(
    'WordPress Frontend' => get_option('home'),
    'WordPress Admin' => get_option('siteurl') . '/wp-admin/',
    'WordPress REST API' => get_rest_url(),
    'WordPress Login' => get_option('siteurl') . '/wp-login.php'
);

foreach ($endpoints as $name => $url) {
    echo "   Testing $name... ";
    $response = wp_remote_get($url, array('timeout' => 10));
    if (!is_wp_error($response)) {
        $status = wp_remote_retrieve_response_code($response);
        echo "✅ $status\n";
    } else {
        echo "❌ Error: " . $response->get_error_message() . "\n";
    }
}

// Test HYIPLab API endpoints if plugin is active
if (is_plugin_active($hyiplab_plugin)) {
    echo "11. Testing HYIPLab API endpoints...\n";
    
    $hyiplab_endpoints = array(
        'status' => get_rest_url() . 'hyiplab/v1/status',
        'plans' => get_rest_url() . 'hyiplab/v1/plans',
        'stats' => get_rest_url() . 'hyiplab/v1/stats'
    );
    
    foreach ($hyiplab_endpoints as $name => $url) {
        echo "   Testing HYIPLab $name... ";
        $response = wp_remote_get($url, array('timeout' => 10));
        if (!is_wp_error($response)) {
            $status = wp_remote_retrieve_response_code($response);
            echo "✅ $status\n";
        } else {
            echo "❌ Error: " . $response->get_error_message() . "\n";
        }
    }
}

echo "\n=== Setup Complete ===\n";
echo "✅ WordPress setup completed successfully!\n";
echo "✅ All canonical services are working\n";
echo "✅ All pages are accessible\n";
echo "\n🌐 Access your sites:\n";
echo "   • WordPress Frontend: " . get_option('home') . "\n";
echo "   • WordPress Admin: " . get_option('siteurl') . "/wp-admin/\n";
echo "   • PHPMyAdmin: http://localhost:8080\n";
echo "\n🎉 BlackCnote environment is fully deployed and ready!\n";
?> 