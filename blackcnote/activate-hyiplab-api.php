<?php
/**
 * BlackCnote HYIPLab API Activation and Test Script
 * Run this script to activate the plugin and test all endpoints
 */

// Load WordPress - inside container, wp-config.php is in the current directory
require_once 'wp-config.php';

echo "=== BlackCnote HYIPLab API Activation and Test ===\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check if plugin file exists
echo "1. Checking plugin file... ";
$plugin_file = 'wp-content/plugins/blackcnote-hyiplab-api/blackcnote-hyiplab-api.php';
if (file_exists($plugin_file)) {
    echo "✅ Plugin file found\n";
} else {
    echo "❌ Plugin file not found\n";
    exit(1);
}

// Test 2: Activate plugin
echo "2. Activating plugin... ";
$plugin = 'blackcnote-hyiplab-api/blackcnote-hyiplab-api.php';
activate_plugin($plugin);
if (is_plugin_active($plugin)) {
    echo "✅ Plugin activated successfully\n";
} else {
    echo "❌ Plugin activation failed\n";
    exit(1);
}

// Test 3: Check database tables
echo "3. Checking database tables... ";
global $wpdb;
$tables = array('hyiplab_plans', 'hyiplab_users', 'hyiplab_investments');
$all_tables_exist = true;

foreach ($tables as $table) {
    $table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$exists) {
        echo "❌ Table $table_name does not exist\n";
        $all_tables_exist = false;
    }
}

if ($all_tables_exist) {
    echo "✅ All database tables exist\n";
} else {
    echo "❌ Some database tables are missing\n";
}

// Test 4: Test REST API endpoints
echo "4. Testing REST API endpoints...\n";

$endpoints = array(
    'status' => 'http://localhost:8889/wp-json/hyiplab/v1/status',
    'plans' => 'http://localhost:8889/wp-json/hyiplab/v1/plans',
    'stats' => 'http://localhost:8889/wp-json/hyiplab/v1/stats',
    'health' => 'http://localhost:8889/wp-json/hyiplab/v1/health'
);

foreach ($endpoints as $name => $url) {
    echo "   Testing $name endpoint... ";
    
    $response = wp_remote_get($url, array(
        'timeout' => 10,
        'headers' => array(
            'X-WP-Nonce' => wp_create_nonce('wp_rest')
        )
    ));
    
    if (is_wp_error($response)) {
        echo "❌ Error: " . $response->get_error_message() . "\n";
    } else {
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($status_code === 200) {
            $data = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "✅ Success (Status: $status_code)\n";
            } else {
                echo "❌ Invalid JSON response\n";
            }
        } else {
            echo "❌ Failed (Status: $status_code)\n";
        }
    }
}

// Test 5: Check plugin integration
echo "5. Checking plugin integration... ";
if (class_exists('BlackCnote_HYIPLab_API')) {
    echo "✅ Plugin class loaded\n";
} else {
    echo "❌ Plugin class not found\n";
}

// Test 6: Check CORS headers
echo "6. Checking CORS headers... ";
$cors_response = wp_remote_get('http://localhost:8889/wp-json/hyiplab/v1/status', array(
    'timeout' => 10,
    'headers' => array(
        'X-WP-Nonce' => wp_create_nonce('wp_rest')
    )
));

if (!is_wp_error($cors_response)) {
    $headers = wp_remote_retrieve_headers($cors_response);
    $cors_header = $headers->get('Access-Control-Allow-Origin');
    if ($cors_header) {
        echo "✅ CORS headers configured\n";
    } else {
        echo "❌ CORS headers missing\n";
    }
} else {
    echo "❌ Could not test CORS headers\n";
}

echo "\n=== Test Summary ===\n";
echo "WordPress URL: http://localhost:8889\n";
echo "Admin URL: http://localhost:8889/wp-admin/\n";
echo "API Base URL: http://localhost:8889/wp-json/hyiplab/v1/\n";
echo "PHPMyAdmin: http://localhost:8090\n";
echo "\nPlugin activation and testing completed!\n";
echo "You can now test the API endpoints in your browser or Postman.\n";
?>
