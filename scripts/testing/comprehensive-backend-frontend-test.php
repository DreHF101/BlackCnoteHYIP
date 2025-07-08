<?php
/**
 * Comprehensive Backend & Frontend Test Script
 * Tests all BlackCnote theme enhancements and functionality
 *
 * @package BlackCnote
 * @version 2.0
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once dirname(__FILE__) . '/../../blackcnote/wp-config.php';
}

// Test results storage
$test_results = array();
$errors = array();
$warnings = array();

/**
 * Test function to log results
 */
function log_test_result($test_name, $status, $message = '', $details = array()) {
    global $test_results;
    $test_results[] = array(
        'test' => $test_name,
        'status' => $status,
        'message' => $message,
        'details' => $details,
        'timestamp' => current_time('mysql')
    );
}

/**
 * Test function to log errors
 */
function log_error($message, $details = array()) {
    global $errors;
    $errors[] = array(
        'message' => $message,
        'details' => $details,
        'timestamp' => current_time('mysql')
    );
}

/**
 * Test function to log warnings
 */
function log_warning($message, $details = array()) {
    global $warnings;
    $warnings[] = array(
        'message' => $message,
        'details' => $details,
        'timestamp' => current_time('mysql')
    );
}

echo "🚀 BlackCnote Comprehensive Backend & Frontend Test\n";
echo "==================================================\n\n";

// Test 1: WordPress Environment
echo "1. Testing WordPress Environment...\n";
if (defined('ABSPATH')) {
    log_test_result('WordPress Environment', 'PASS', 'WordPress is properly loaded');
} else {
    log_test_result('WordPress Environment', 'FAIL', 'WordPress is not loaded');
    log_error('WordPress environment not available');
}

// Test 2: Theme Activation
echo "2. Testing Theme Activation...\n";
$current_theme = wp_get_theme();
if ($current_theme->get('Name') === 'BlackCnote') {
    log_test_result('Theme Activation', 'PASS', 'BlackCnote theme is active');
} else {
    log_test_result('Theme Activation', 'FAIL', 'BlackCnote theme is not active');
    log_error('BlackCnote theme not activated', array('current_theme' => $current_theme->get('Name')));
}

// Test 3: Required Files
echo "3. Testing Required Files...\n";
$required_files = array(
    'functions.php' => get_template_directory() . '/functions.php',
    'backend-settings-manager.php' => get_template_directory() . '/inc/backend-settings-manager.php',
    'admin-functions.php' => get_template_directory() . '/admin/admin-functions.php',
    'backend-settings.css' => get_template_directory() . '/inc/backend-settings.css',
    'backend-settings.js' => get_template_directory() . '/inc/backend-settings.js',
    'style.css' => get_template_directory() . '/style.css',
    'header.php' => get_template_directory() . '/header.php',
    'footer.php' => get_template_directory() . '/footer.php',
    'index.php' => get_template_directory() . '/index.php',
    'front-page.php' => get_template_directory() . '/front-page.php',
    'page-home.php' => get_template_directory() . '/page-home.php',
    'page-about.php' => get_template_directory() . '/page-about.php',
    'page-services.php' => get_template_directory() . '/page-services.php',
    'page-contact.php' => get_template_directory() . '/page-contact.php',
    'page-privacy.php' => get_template_directory() . '/page-privacy.php',
    'page-terms.php' => get_template_directory() . '/page-terms.php',
    'page-dashboard.php' => get_template_directory() . '/page-dashboard.php',
    'page-plans.php' => get_template_directory() . '/page-plans.php'
);

foreach ($required_files as $file_name => $file_path) {
    if (file_exists($file_path)) {
        log_test_result('File: ' . $file_name, 'PASS', 'File exists');
    } else {
        log_test_result('File: ' . $file_name, 'FAIL', 'File missing');
        log_error('Required file missing', array('file' => $file_name, 'path' => $file_path));
    }
}

// Test 4: Backend Settings Manager
echo "4. Testing Backend Settings Manager...\n";
if (class_exists('BlackCnote_Backend_Settings_Manager')) {
    log_test_result('Backend Settings Manager Class', 'PASS', 'Class exists');
    
    // Test settings registration
    $settings = get_option('blackcnote_theme_settings', array());
    if (!empty($settings)) {
        log_test_result('Settings Registration', 'PASS', 'Settings are registered');
    } else {
        log_test_result('Settings Registration', 'WARNING', 'No settings found');
        log_warning('No theme settings found', array('settings' => $settings));
    }
} else {
    log_test_result('Backend Settings Manager Class', 'FAIL', 'Class not found');
    log_error('Backend Settings Manager class not loaded');
}

// Test 5: Admin Functions
echo "5. Testing Admin Functions...\n";
$admin_functions = array(
    'blackcnote_admin_menu',
    'blackcnote_admin_page',
    'blackcnote_live_editing_page',
    'blackcnote_dev_tools_page',
    'blackcnote_system_status_page'
);

foreach ($admin_functions as $function_name) {
    if (function_exists($function_name)) {
        log_test_result('Admin Function: ' . $function_name, 'PASS', 'Function exists');
    } else {
        log_test_result('Admin Function: ' . $function_name, 'FAIL', 'Function missing');
        log_error('Admin function missing', array('function' => $function_name));
    }
}

// Test 6: Page Templates
echo "6. Testing Page Templates...\n";
$page_templates = array(
    'Home Page' => 'page-home.php',
    'About Page' => 'page-about.php',
    'Services Page' => 'page-services.php',
    'Contact Page' => 'page-contact.php',
    'Privacy Policy Page' => 'page-privacy.php',
    'Terms of Service Page' => 'page-terms.php',
    'Investment Dashboard Page' => 'page-dashboard.php',
    'Investment Plans Page' => 'page-plans.php'
);

foreach ($page_templates as $template_name => $template_file) {
    $template_path = get_template_directory() . '/' . $template_file;
    if (file_exists($template_path)) {
        $content = file_get_contents($template_path);
        if (strpos($content, 'Template Name:') !== false) {
            log_test_result('Page Template: ' . $template_name, 'PASS', 'Template exists and properly formatted');
        } else {
            log_test_result('Page Template: ' . $template_name, 'WARNING', 'Template exists but missing Template Name comment');
            log_warning('Template missing proper header', array('template' => $template_name));
        }
    } else {
        log_test_result('Page Template: ' . $template_name, 'FAIL', 'Template file missing');
        log_error('Page template missing', array('template' => $template_name, 'file' => $template_file));
    }
}

// Test 7: Required Pages
echo "7. Testing Required Pages...\n";
$required_pages = array(
    'home' => 'Home',
    'about' => 'About',
    'services' => 'Investment Services',
    'contact' => 'Contact Us',
    'privacy-policy' => 'Privacy Policy',
    'terms-of-service' => 'Terms of Service',
    'investment-dashboard' => 'Investment Dashboard',
    'investment-plans' => 'Investment Plans'
);

foreach ($required_pages as $slug => $title) {
    $page = get_page_by_path($slug);
    if ($page) {
        log_test_result('Page: ' . $title, 'PASS', 'Page exists');
    } else {
        log_test_result('Page: ' . $title, 'WARNING', 'Page not found - will be created on theme activation');
        log_warning('Required page not found', array('slug' => $slug, 'title' => $title));
    }
}

// Test 8: Script and Style Enqueuing
echo "8. Testing Script and Style Enqueuing...\n";
if (function_exists('blackcnote_enhanced_scripts')) {
    log_test_result('Enhanced Scripts Function', 'PASS', 'Function exists');
} else {
    log_test_result('Enhanced Scripts Function', 'FAIL', 'Function missing');
    log_error('Enhanced scripts function not found');
}

// Test 9: Menu Registration
echo "9. Testing Menu Registration...\n";
if (function_exists('blackcnote_fallback_menu')) {
    log_test_result('Fallback Menu Function', 'PASS', 'Function exists');
} else {
    log_test_result('Fallback Menu Function', 'FAIL', 'Function missing');
    log_error('Fallback menu function not found');
}

// Test 10: CSS and JS Files
echo "10. Testing CSS and JS Files...\n";
$asset_files = array(
    'backend-settings.css' => get_template_directory() . '/inc/backend-settings.css',
    'backend-settings.js' => get_template_directory() . '/inc/backend-settings.js',
    'live-styles.css' => get_template_directory() . '/assets/css/live-styles.css',
    'widgets.css' => get_template_directory() . '/css/widgets.css',
    'blackcnote-theme.js' => get_template_directory() . '/js/blackcnote-theme.js'
);

foreach ($asset_files as $file_name => $file_path) {
    if (file_exists($file_path)) {
        $file_size = filesize($file_path);
        if ($file_size > 0) {
            log_test_result('Asset File: ' . $file_name, 'PASS', 'File exists and has content', array('size' => $file_size));
        } else {
            log_test_result('Asset File: ' . $file_name, 'WARNING', 'File exists but is empty');
            log_warning('Asset file is empty', array('file' => $file_name));
        }
    } else {
        log_test_result('Asset File: ' . $file_name, 'FAIL', 'File missing');
        log_error('Asset file missing', array('file' => $file_name, 'path' => $file_path));
    }
}

// Test 11: Theme Options
echo "11. Testing Theme Options...\n";
$theme_options = array(
    'blackcnote_logo_url',
    'blackcnote_primary_color',
    'blackcnote_footer_text',
    'blackcnote_analytics_code',
    'blackcnote_custom_css',
    'blackcnote_custom_js',
    'blackcnote_live_editing_enabled'
);

foreach ($theme_options as $option_name) {
    $option_value = get_option($option_name);
    if ($option_value !== false) {
        log_test_result('Theme Option: ' . $option_name, 'PASS', 'Option exists');
    } else {
        log_test_result('Theme Option: ' . $option_name, 'WARNING', 'Option not set');
        log_warning('Theme option not set', array('option' => $option_name));
    }
}

// Test 12: REST API Endpoints
echo "12. Testing REST API Endpoints...\n";
$rest_endpoints = array(
    'blackcnote/v1/health',
    'blackcnote/v1/content',
    'blackcnote/v1/styles',
    'blackcnote/v1/components'
);

foreach ($rest_endpoints as $endpoint) {
    $response = wp_remote_get(rest_url($endpoint));
    if (!is_wp_error($response) && $response['response']['code'] !== 404) {
        log_test_result('REST API: ' . $endpoint, 'PASS', 'Endpoint accessible');
    } else {
        log_test_result('REST API: ' . $endpoint, 'WARNING', 'Endpoint not accessible');
        log_warning('REST API endpoint not accessible', array('endpoint' => $endpoint));
    }
}

// Test 13: Database Tables
echo "13. Testing Database Tables...\n";
global $wpdb;
$tables = array(
    $wpdb->prefix . 'options',
    $wpdb->prefix . 'posts',
    $wpdb->prefix . 'postmeta'
);

foreach ($tables as $table) {
    $result = $wpdb->get_var("SHOW TABLES LIKE '$table'");
    if ($result) {
        log_test_result('Database Table: ' . $table, 'PASS', 'Table exists');
    } else {
        log_test_result('Database Table: ' . $table, 'FAIL', 'Table missing');
        log_error('Database table missing', array('table' => $table));
    }
}

// Test 14: File Permissions
echo "14. Testing File Permissions...\n";
$critical_files = array(
    get_template_directory() . '/functions.php',
    get_template_directory() . '/style.css',
    get_template_directory() . '/index.php'
);

foreach ($critical_files as $file_path) {
    if (is_readable($file_path)) {
        log_test_result('File Permissions: ' . basename($file_path), 'PASS', 'File is readable');
    } else {
        log_test_result('File Permissions: ' . basename($file_path), 'FAIL', 'File not readable');
        log_error('File not readable', array('file' => $file_path));
    }
}

// Test 15: WordPress Hooks
echo "15. Testing WordPress Hooks...\n";
$required_hooks = array(
    'wp_enqueue_scripts',
    'admin_enqueue_scripts',
    'admin_menu',
    'widgets_init',
    'after_switch_theme'
);

foreach ($required_hooks as $hook) {
    if (has_action($hook)) {
        log_test_result('WordPress Hook: ' . $hook, 'PASS', 'Hook has actions');
    } else {
        log_test_result('WordPress Hook: ' . $hook, 'WARNING', 'Hook has no actions');
        log_warning('WordPress hook has no actions', array('hook' => $hook));
    }
}

// Generate Test Report
echo "\n📊 Test Results Summary\n";
echo "======================\n";

$pass_count = 0;
$fail_count = 0;
$warning_count = 0;

foreach ($test_results as $result) {
    switch ($result['status']) {
        case 'PASS':
            $pass_count++;
            echo "✅ " . $result['test'] . " - " . $result['message'] . "\n";
            break;
        case 'FAIL':
            $fail_count++;
            echo "❌ " . $result['test'] . " - " . $result['message'] . "\n";
            break;
        case 'WARNING':
            $warning_count++;
            echo "⚠️  " . $result['test'] . " - " . $result['message'] . "\n";
            break;
    }
}

echo "\n📈 Test Statistics:\n";
echo "Passed: " . $pass_count . "\n";
echo "Failed: " . $fail_count . "\n";
echo "Warnings: " . $warning_count . "\n";
echo "Total: " . count($test_results) . "\n";

if ($fail_count === 0) {
    echo "\n🎉 All critical tests passed! BlackCnote theme is fully functional.\n";
} else {
    echo "\n⚠️  Some tests failed. Please review the errors above.\n";
}

if (!empty($errors)) {
    echo "\n❌ Errors Found:\n";
    foreach ($errors as $error) {
        echo "- " . $error['message'] . "\n";
    }
}

if (!empty($warnings)) {
    echo "\n⚠️  Warnings Found:\n";
    foreach ($warnings as $warning) {
        echo "- " . $warning['message'] . "\n";
    }
}

// Save test results to file
$test_report = array(
    'timestamp' => current_time('mysql'),
    'results' => $test_results,
    'errors' => $errors,
    'warnings' => $warnings,
    'summary' => array(
        'total' => count($test_results),
        'passed' => $pass_count,
        'failed' => $fail_count,
        'warnings' => $warning_count
    )
);

$report_file = dirname(__FILE__) . '/test-report-' . date('Y-m-d-H-i-s') . '.json';
file_put_contents($report_file, json_encode($test_report, JSON_PRETTY_PRINT));

echo "\n📄 Test report saved to: " . $report_file . "\n";
echo "\n✅ BlackCnote Comprehensive Test Complete!\n"; 