<?php
/**
 * BlackCnote WordPress/React Integration Fix
 * 
 * This script fixes the integration between WordPress and the React development server.
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "==========================================\n";
echo "BLACKCNOTE WORDPRESS/REACT INTEGRATION FIX\n";
echo "==========================================\n\n";

// Function to write colored output
function write_output($message, $type = 'info') {
    $colors = [
        'info' => '36',    // Cyan
        'success' => '32', // Green
        'warning' => '33', // Yellow
        'error' => '31'    // Red
    ];
    
    $color = $colors[$type] ?? '37';
    echo "\033[{$color}m{$message}\033[0m\n";
}

// Test React development server connectivity
write_output("Testing React development server connectivity...", 'info');
$react_dev_url = 'http://localhost:5174';
$react_response = wp_remote_get($react_dev_url, ['timeout' => 5]);

if (is_wp_error($react_response)) {
    write_output("❌ React development server not accessible: " . $react_response->get_error_message(), 'error');
} else {
    $status_code = wp_remote_retrieve_response_code($react_response);
    if ($status_code === 200) {
        write_output("✅ React development server is accessible at {$react_dev_url}", 'success');
    } else {
        write_output("❌ React development server returned status code: {$status_code}", 'error');
    }
}

// Update the theme's functions.php to properly load React
write_output("\nUpdating WordPress theme React integration...", 'info');

$theme_functions_file = get_template_directory() . '/functions.php';
$backup_file = $theme_functions_file . '.backup.' . date('Y-m-d-H-i-s');

// Create backup
if (copy($theme_functions_file, $backup_file)) {
    write_output("✅ Created backup: " . basename($backup_file), 'success');
} else {
    write_output("❌ Failed to create backup", 'error');
    exit(1);
}

// Read the current functions.php
$functions_content = file_get_contents($theme_functions_file);

// Check if React integration is already fixed
if (strpos($functions_content, 'blackcnote_react_dev_server_url') !== false) {
    write_output("✅ React integration already configured", 'success');
} else {
    // Add React development server configuration
    $react_config = '
/**
 * BlackCnote React Development Server Configuration
 */
function blackcnote_get_react_dev_server_url() {
    $dev_server_url = "http://localhost:5174";
    
    // Test if dev server is accessible
    $response = wp_remote_get($dev_server_url, ["timeout" => 2]);
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        return $dev_server_url;
    }
    
    return false;
}

/**
 * Enhanced React app loading with development server support
 */
function blackcnote_load_react_app() {
    $dev_server_url = blackcnote_get_react_dev_server_url();
    
    if ($dev_server_url) {
        // Development mode - load from React dev server
        wp_enqueue_script(
            "blackcnote-react-dev",
            $dev_server_url . "/@vite/client",
            [],
            "1.0.0",
            false
        );
        
        wp_enqueue_script(
            "blackcnote-react-main",
            $dev_server_url . "/src/main.tsx",
            ["blackcnote-react-dev"],
            "1.0.0",
            true
        );
        
        // Inject development configuration
        $user = wp_get_current_user();
        $config = [
            "homeUrl" => home_url(),
            "isDevelopment" => true,
            "devServerUrl" => $dev_server_url,
            "apiUrl" => home_url("/wp-json/blackcnote/v1/"),
            "nonce" => wp_create_nonce("wp_rest"),
            "isLoggedIn" => is_user_logged_in(),
            "userId" => is_user_logged_in() ? $user->ID : 0,
            "baseUrl" => home_url(),
            "themeUrl" => get_template_directory_uri(),
            "ajaxUrl" => admin_url("admin-ajax.php"),
            "environment" => "development",
            "themeActive" => true,
            "pluginActive" => function_exists("hyiplab_system_instance"),
        ];
        
        wp_add_inline_script(
            "blackcnote-react-main",
            "window.blackCnoteApiSettings = " . wp_json_encode($config) . ";",
            "before"
        );
        
        error_log("BlackCnote: Loading React app from development server at " . $dev_server_url);
        return true;
    }
    
    return false;
}

// Hook into WordPress script loading
add_action("wp_enqueue_scripts", function() {
    if (!is_admin()) {
        blackcnote_load_react_app();
    }
}, 20);

';

    // Insert the React configuration before the closing PHP tag
    if (strpos($functions_content, '?>') !== false) {
        $functions_content = str_replace('?>', $react_config . "\n?>", $functions_content);
    } else {
        $functions_content .= $react_config;
    }
    
    // Write the updated content
    if (file_put_contents($theme_functions_file, $functions_content)) {
        write_output("✅ Updated functions.php with React development server integration", 'success');
    } else {
        write_output("❌ Failed to update functions.php", 'error');
        exit(1);
    }
}

// Test WordPress REST API
write_output("\nTesting WordPress REST API...", 'info');
$rest_url = home_url('/wp-json/');
$rest_response = wp_remote_get($rest_url, ['timeout' => 5]);

if (is_wp_error($rest_response)) {
    write_output("❌ WordPress REST API not accessible: " . $rest_response->get_error_message(), 'error');
} else {
    $status_code = wp_remote_retrieve_response_code($rest_response);
    if ($status_code === 200) {
        write_output("✅ WordPress REST API is accessible at {$rest_url}", 'success');
    } else {
        write_output("❌ WordPress REST API returned status code: {$status_code}", 'error');
    }
}

// Test BlackCnote API endpoint
write_output("\nTesting BlackCnote API endpoint...", 'info');
$blackcnote_api_url = home_url('/wp-json/blackcnote/v1/health');
$api_response = wp_remote_get($blackcnote_api_url, ['timeout' => 5]);

if (is_wp_error($api_response)) {
    write_output("❌ BlackCnote API not accessible: " . $api_response->get_error_message(), 'error');
} else {
    $status_code = wp_remote_retrieve_response_code($api_response);
    if ($status_code === 200) {
        write_output("✅ BlackCnote API is accessible at {$blackcnote_api_url}", 'success');
    } else {
        write_output("❌ BlackCnote API returned status code: {$status_code}", 'error');
    }
}

// Clear WordPress cache
write_output("\nClearing WordPress cache...", 'info');
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    write_output("✅ WordPress cache cleared", 'success');
}

if (function_exists('w3tc_flush_all')) {
    w3tc_flush_all();
    write_output("✅ W3 Total Cache cleared", 'success');
}

// Test the frontend
write_output("\nTesting WordPress frontend...", 'info');
$frontend_url = home_url('/');
$frontend_response = wp_remote_get($frontend_url, ['timeout' => 10]);

if (is_wp_error($frontend_response)) {
    write_output("❌ WordPress frontend not accessible: " . $frontend_response->get_error_message(), 'error');
} else {
    $status_code = wp_remote_retrieve_response_code($frontend_response);
    if ($status_code === 200) {
        write_output("✅ WordPress frontend is accessible at {$frontend_url}", 'success');
        
        $body = wp_remote_retrieve_body($frontend_response);
        if (strpos($body, 'blackcnote-react-app') !== false) {
            write_output("✅ React app container found in frontend", 'success');
        } else {
            write_output("⚠️  React app container not found in frontend", 'warning');
        }
    } else {
        write_output("❌ WordPress frontend returned status code: {$status_code}", 'error');
    }
}

write_output("\n==========================================", 'info');
write_output("INTEGRATION FIX COMPLETED", 'success');
write_output("==========================================", 'info');
write_output("", 'info');
write_output("Next steps:", 'info');
write_output("1. Visit http://localhost:8888 to see the updated frontend", 'info');
write_output("2. Check browser console for any JavaScript errors", 'info');
write_output("3. Verify React app loads properly", 'info');
write_output("", 'info');
write_output("If issues persist:", 'info');
write_output("- Check React development server logs", 'info');
write_output("- Verify WordPress theme is active", 'info');
write_output("- Check browser network tab for failed requests", 'info');
?> 