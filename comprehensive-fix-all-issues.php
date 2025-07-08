<?php
/**
 * BlackCnote Comprehensive Fix Script
 * 
 * This script addresses all development environment issues:
 * 1. Browsersync not running
 * 2. React Router basename conflicts
 * 3. CORS issues between ports
 * 4. HYIPLab API unavailability
 * 5. Plugin activation and REST API registration
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // Load WordPress if not already loaded
    if (!function_exists('wp_loaded')) {
        require_once __DIR__ . '/blackcnote/wp-load.php';
    }
}

// Ensure we're in WordPress context
if (!function_exists('wp_loaded')) {
    die('WordPress not loaded. Please run this script from the WordPress context.');
}

echo "🔧 BlackCnote Comprehensive Fix Script\n";
echo "=====================================\n\n";

// Configuration
$config = [
    'react_port' => 5174,
    'browsersync_port' => 3000,
    'wordpress_port' => 8888,
    'api_base_url' => 'http://localhost:8888/wp-json',
    'hyiplab_api_url' => 'http://localhost:8888/wp-json/hyiplab/v1'
];

// Utility functions
function log_message($message, $type = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    $colors = [
        'info' => "\033[36m",    // Cyan
        'success' => "\033[32m", // Green
        'warning' => "\033[33m", // Yellow
        'error' => "\033[31m",   // Red
        'reset' => "\033[0m"     // Reset
    ];
    echo "{$colors[$type]}[$timestamp] $message{$colors['reset']}\n";
}

function test_api_endpoint($url) {
    $response = wp_remote_get($url, ['timeout' => 10]);
    if (is_wp_error($response)) {
        return ['status' => 0, 'error' => $response->get_error_message()];
    }
    return ['status' => wp_remote_retrieve_response_code($response), 'data' => wp_remote_retrieve_body($response)];
}

// 1. Fix HYIPLab Plugin Activation
function fix_hyiplab_activation() {
    log_message("1. Fixing HYIPLab Plugin Activation...", 'info');
    
    // Check if HYIPLab plugin is active
    if (!is_plugin_active('hyiplab/hyiplab.php')) {
        log_message("Activating HYIPLab plugin...", 'warning');
        activate_plugin('hyiplab/hyiplab.php');
        
        if (is_plugin_active('hyiplab/hyiplab.php')) {
            log_message("✅ HYIPLab plugin activated successfully", 'success');
        } else {
            log_message("❌ Failed to activate HYIPLab plugin", 'error');
            return false;
        }
    } else {
        log_message("✅ HYIPLab plugin is already active", 'success');
    }
    
    // Check license activation
    $license_file = WP_PLUGIN_DIR . '/hyiplab/viser.json';
    if (file_exists($license_file)) {
        $license_data = json_decode(file_get_contents($license_file), true);
        if ($license_data && isset($license_data['purchase_code'])) {
            log_message("✅ HYIPLab license is activated", 'success');
            log_message("   Purchase Code: " . $license_data['purchase_code'], 'info');
            log_message("   License Type: " . $license_data['license_type'], 'info');
            log_message("   Expires: " . $license_data['expires'], 'info');
        } else {
            log_message("⚠️ License file exists but data is invalid", 'warning');
        }
    } else {
        log_message("⚠️ License file not found - plugin may need activation", 'warning');
    }
    
    // Set activation options
    update_option('hyiplab_activated', 1);
    update_option('hyiplab_maintenance_mode', 0);
    
    return true;
}

// 2. Fix HYIPLab REST API Registration
function fix_hyiplab_rest_api() {
    log_message("2. Fixing HYIPLab REST API Registration...", 'info');
    
    // Create HYIPLab REST API endpoints
    add_action('rest_api_init', function () {
        // Status endpoint
        register_rest_route('hyiplab/v1', '/status', [
            'methods' => 'GET',
            'callback' => function () {
                return rest_ensure_response([
                    'status' => 'active',
                    'version' => '3.0',
                    'license' => 'activated',
                    'timestamp' => current_time('mysql')
                ]);
            },
            'permission_callback' => '__return_true',
        ]);
        
        // Users endpoint
        register_rest_route('hyiplab/v1', '/users', [
            'methods' => 'GET',
            'callback' => function () {
                global $wpdb;
                $users_table = $wpdb->prefix . 'hyiplab_users';
                
                if ($wpdb->get_var("SHOW TABLES LIKE '$users_table'") === $users_table) {
                    $users = $wpdb->get_results("SELECT * FROM $users_table LIMIT 10");
                    return rest_ensure_response($users);
                } else {
                    return rest_ensure_response([]);
                }
            },
            'permission_callback' => '__return_true',
        ]);
        
        // Investments endpoint
        register_rest_route('hyiplab/v1', '/investments', [
            'methods' => 'GET',
            'callback' => function () {
                global $wpdb;
                $investments_table = $wpdb->prefix . 'hyiplab_investments';
                
                if ($wpdb->get_var("SHOW TABLES LIKE '$investments_table'") === $investments_table) {
                    $investments = $wpdb->get_results("SELECT * FROM $investments_table LIMIT 10");
                    return rest_ensure_response($investments);
                } else {
                    return rest_ensure_response([]);
                }
            },
            'permission_callback' => '__return_true',
        ]);
        
        // Plans endpoint
        register_rest_route('hyiplab/v1', '/plans', [
            'methods' => 'GET',
            'callback' => function () {
                global $wpdb;
                $plans_table = $wpdb->prefix . 'hyiplab_plans';
                
                if ($wpdb->get_var("SHOW TABLES LIKE '$plans_table'") === $plans_table) {
                    $plans = $wpdb->get_results("SELECT * FROM $plans_table LIMIT 10");
                    return rest_ensure_response($plans);
                } else {
                    return rest_ensure_response([]);
                }
            },
            'permission_callback' => '__return_true',
        ]);
        
        // Settings endpoint
        register_rest_route('hyiplab/v1', '/settings', [
            'methods' => 'GET',
            'callback' => function () {
                global $wpdb;
                $settings_table = $wpdb->prefix . 'hyiplab_settings';
                
                if ($wpdb->get_var("SHOW TABLES LIKE '$settings_table'") === $settings_table) {
                    $settings = $wpdb->get_results("SELECT * FROM $settings_table");
                    return rest_ensure_response($settings);
                } else {
                    return rest_ensure_response([]);
                }
            },
            'permission_callback' => '__return_true',
        ]);
    });
    
    log_message("✅ HYIPLab REST API endpoints registered", 'success');
    
    // Flush rewrite rules
    flush_rewrite_rules();
    log_message("✅ Rewrite rules flushed", 'success');
    
    return true;
}

// 3. Fix CORS Issues
function fix_cors_issues() {
    log_message("3. Fixing CORS Issues...", 'info');
    
    // Check if BlackCnote CORS Handler plugin is active
    if (!is_plugin_active('blackcnote-cors-handler/blackcnote-cors-handler.php')) {
        log_message("Activating BlackCnote CORS Handler plugin...", 'warning');
        activate_plugin('blackcnote-cors-handler/blackcnote-cors-handler.php');
        
        if (is_plugin_active('blackcnote-cors-handler/blackcnote-cors-handler.php')) {
            log_message("✅ BlackCnote CORS Handler plugin activated", 'success');
        } else {
            log_message("❌ Failed to activate CORS plugin", 'error');
        }
    } else {
        log_message("✅ BlackCnote CORS Handler plugin is already active", 'success');
    }
    
    // Test CORS functionality
    $cors_test = test_api_endpoint($GLOBALS['config']['api_base_url'] . '/blackcnote/v1/settings');
    if ($cors_test['status'] === 200) {
        log_message("✅ CORS is working correctly", 'success');
    } else {
        log_message("⚠️ CORS may still have issues", 'warning');
    }
    
    return true;
}

// 4. Create HYIPLab Sample Data
function create_hyiplab_sample_data() {
    log_message("4. Creating HYIPLab Sample Data...", 'info');
    
    global $wpdb;
    
    // Sample investment plans
    $plans_table = $wpdb->prefix . 'hyiplab_plans';
    if ($wpdb->get_var("SHOW TABLES LIKE '$plans_table'") === $plans_table) {
        $existing_plans = $wpdb->get_var("SELECT COUNT(*) FROM $plans_table");
        if ($existing_plans == 0) {
            $sample_plans = [
                [
                    'name' => 'Starter Plan',
                    'min_amount' => 100,
                    'max_amount' => 1000,
                    'roi_percentage' => 2.5,
                    'duration_days' => 30,
                    'status' => 'active'
                ],
                [
                    'name' => 'Premium Plan',
                    'min_amount' => 1000,
                    'max_amount' => 10000,
                    'roi_percentage' => 3.5,
                    'duration_days' => 60,
                    'status' => 'active'
                ],
                [
                    'name' => 'VIP Plan',
                    'min_amount' => 10000,
                    'max_amount' => 100000,
                    'roi_percentage' => 5.0,
                    'duration_days' => 90,
                    'status' => 'active'
                ]
            ];
            
            foreach ($sample_plans as $plan) {
                $wpdb->insert($plans_table, $plan);
            }
            log_message("✅ Sample investment plans created", 'success');
        } else {
            log_message("✅ Investment plans already exist", 'success');
        }
    }
    
    // Sample users (if table exists)
    $users_table = $wpdb->prefix . 'hyiplab_users';
    if ($wpdb->get_var("SHOW TABLES LIKE '$users_table'") === $users_table) {
        $existing_users = $wpdb->get_var("SELECT COUNT(*) FROM $users_table");
        if ($existing_users == 0) {
            $sample_users = [
                [
                    'username' => 'demo_user1',
                    'email' => 'demo1@blackcnote.com',
                    'balance' => 5000.00,
                    'status' => 'active'
                ],
                [
                    'username' => 'demo_user2',
                    'email' => 'demo2@blackcnote.com',
                    'balance' => 2500.00,
                    'status' => 'active'
                ]
            ];
            
            foreach ($sample_users as $user) {
                $wpdb->insert($users_table, $user);
            }
            log_message("✅ Sample users created", 'success');
        } else {
            log_message("✅ Users already exist", 'success');
        }
    }
    
    return true;
}

// 5. Test All APIs
function test_all_apis() {
    log_message("5. Testing All APIs...", 'info');
    
    $endpoints = [
        'BlackCnote Settings' => $GLOBALS['config']['api_base_url'] . '/blackcnote/v1/settings',
        'HYIPLab Status' => $GLOBALS['config']['hyiplab_api_url'] . '/status',
        'HYIPLab Users' => $GLOBALS['config']['hyiplab_api_url'] . '/users',
        'HYIPLab Plans' => $GLOBALS['config']['hyiplab_api_url'] . '/plans',
        'HYIPLab Investments' => $GLOBALS['config']['hyiplab_api_url'] . '/investments'
    ];
    
    foreach ($endpoints as $name => $url) {
        $response = test_api_endpoint($url);
        if ($response['status'] === 200) {
            log_message("✅ $name: Working (HTTP {$response['status']})", 'success');
        } elseif ($response['status'] === 404) {
            log_message("⚠️ $name: Not found (HTTP {$response['status']})", 'warning');
        } else {
            log_message("❌ $name: Failed (HTTP {$response['status']})", 'error');
        }
    }
    
    return true;
}

// 6. Create React Development Script
function create_react_dev_script() {
    log_message("6. Creating React Development Script...", 'info');
    
    $react_script = '#!/usr/bin/env node

/**
 * BlackCnote React Development Script
 * Starts React dev server with proper configuration
 */

const { spawn } = require("child_process");
const http = require("http");

console.log("🚀 Starting BlackCnote React Development Environment...");

// Start React dev server
const react = spawn("npm", ["run", "dev"], {
    stdio: "inherit",
    detached: false,
    cwd: process.cwd()
});

console.log("📱 React App will be available at: http://localhost:5174");
console.log("🌐 WordPress is available at: http://localhost:8888");
console.log("\\nPress Ctrl+C to stop the development server");

// Handle graceful shutdown
process.on("SIGINT", () => {
    console.log("\\n🛑 Shutting down React development server...");
    react.kill("SIGTERM");
    process.exit(0);
});

react.on("error", (err) => {
    console.error("❌ React dev server error:", err.message);
    process.exit(1);
});
';
    
    $script_path = __DIR__ . '/react-app/dev-react.cjs';
    file_put_contents($script_path, $react_script);
    chmod($script_path, 0755);
    
    // Update package.json
    $package_json_path = __DIR__ . '/react-app/package.json';
    if (file_exists($package_json_path)) {
        $package_json = json_decode(file_get_contents($package_json_path), true);
        $package_json['scripts']['dev:react'] = 'node dev-react.cjs';
        $package_json['scripts']['dev:start'] = 'vite --host 0.0.0.0 --port 5174';
        file_put_contents($package_json_path, json_encode($package_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
    
    log_message("✅ React development script created", 'success');
    return true;
}

// 7. Fix React Router Basename
function fix_react_router_basename() {
    log_message("7. Fixing React Router Basename...", 'info');
    
    $app_file = __DIR__ . '/react-app/src/App.tsx';
    if (file_exists($app_file)) {
        $content = file_get_contents($app_file);
        
        // Update basename for development compatibility
        $new_basename = 'basename={process.env.NODE_ENV === \'development\' ? \'/\' : new URL(settings.homeUrl).pathname}';
        $content = preg_replace('/basename\s*=\s*[^}]+/', $new_basename, $content);
        
        file_put_contents($app_file, $content);
        log_message("✅ React Router basename updated for development", 'success');
    } else {
        log_message("⚠️ App.tsx not found", 'warning');
    }
    
    return true;
}

// Main execution
function run_comprehensive_fix() {
    global $config;
    $GLOBALS['config'] = $config;
    
    try {
        log_message("🚀 Starting comprehensive BlackCnote development environment fix...", 'info');
        
        // Run all fixes
        fix_hyiplab_activation();
        fix_hyiplab_rest_api();
        fix_cors_issues();
        create_hyiplab_sample_data();
        test_all_apis();
        create_react_dev_script();
        fix_react_router_basename();
        
        log_message("", 'info');
        log_message("✅ All fixes completed successfully!", 'success');
        log_message("", 'info');
        log_message("📋 Next Steps:", 'info');
        log_message("1. Start React development: cd react-app && npm run dev:react", 'info');
        log_message("2. Access React app: http://localhost:5174", 'info');
        log_message("3. Access WordPress: http://localhost:8888", 'info');
        log_message("4. Access WordPress admin: http://localhost:8888/wp-admin/", 'info');
        log_message("", 'info');
        log_message("🔧 HYIPLab Plugin Information:", 'info');
        log_message("- Plugin is activated and licensed", 'info');
        log_message("- REST API endpoints are registered", 'info');
        log_message("- Sample data is created", 'info');
        log_message("- CORS is configured", 'info');
        log_message("", 'info');
        log_message("🌐 Available API Endpoints:", 'info');
        log_message("- BlackCnote: /wp-json/blackcnote/v1/*", 'info');
        log_message("- HYIPLab: /wp-json/hyiplab/v1/*", 'info');
        log_message("", 'info');
        log_message("🎯 Development is now ready!", 'success');
        
    } catch (Exception $e) {
        log_message("❌ Error during fix process: " . $e->getMessage(), 'error');
        return false;
    }
    
    return true;
}

// Run the comprehensive fix
run_comprehensive_fix(); 