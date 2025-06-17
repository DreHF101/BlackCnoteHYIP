<?php
/**
 * Theme Validation Script
 *
 * @package HYIP_Theme
 */

declare(strict_types=1);

// Enable debugging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Check for required files
$required_files = [
    'style.css',
    'functions.php',
    'index.php',
    'template-hyip-dashboard.php',
    'template-hyip-plans.php',
    'template-hyip-transactions.php',
    'hyiplab/dashboard.php',
    'assets/css/hyip-theme.css',
    'assets/js/hyip-theme.js',
    'languages/hyip-theme.pot',
    'README.md',
    'CHANGELOG.md',
    'LICENSE.txt',
    'screenshot.png'
];

$missing_files = [];
foreach ($required_files as $file) {
    if (!file_exists(__DIR__ . '/../' . $file)) {
        $missing_files[] = $file;
    }
}

if (!empty($missing_files)) {
    error_log('Missing required files: ' . implode(', ', $missing_files));
}

// Test HYIPLab integration
if (!function_exists('is_plugin_active')) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if (!is_plugin_active('hyiplab/hyiplab.php')) {
    error_log('HYIPLab plugin not active');
}

// Check database tables
global $wpdb;
$required_tables = [
    $wpdb->prefix . 'hyiplab_plans',
    $wpdb->prefix . 'hyiplab_transactions'
];

foreach ($required_tables as $table) {
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
        error_log("Missing required table: $table");
    }
}

// Check theme requirements
$theme = wp_get_theme();
$required_headers = [
    'Theme Name',
    'Theme URI',
    'Author',
    'Author URI',
    'Description',
    'Version',
    'License',
    'License URI',
    'Text Domain',
    'Tags'
];

foreach ($required_headers as $header) {
    if (!$theme->get($header)) {
        error_log("Missing required theme header: $header");
    }
}

// Check for common security issues
$security_checks = [
    'nonce' => [
        'pattern' => '/wp_create_nonce/',
        'message' => 'Missing nonce verification'
    ],
    'sanitization' => [
        'pattern' => '/sanitize_/',
        'message' => 'Missing input sanitization'
    ],
    'escaping' => [
        'pattern' => '/esc_/',
        'message' => 'Missing output escaping'
    ]
];

$files_to_check = [
    'functions.php',
    'template-hyip-dashboard.php',
    'template-hyip-plans.php',
    'template-hyip-transactions.php',
    'hyiplab/dashboard.php'
];

foreach ($files_to_check as $file) {
    $content = file_get_contents(__DIR__ . '/../' . $file);
    foreach ($security_checks as $check) {
        if (!preg_match($check['pattern'], $content)) {
            error_log("Security issue in $file: " . $check['message']);
        }
    }
}

// Check for Bootstrap 5 integration
$bootstrap_checks = [
    'css' => 'bootstrap.min.css',
    'js' => 'bootstrap.bundle.min.js'
];

$functions_content = file_get_contents(__DIR__ . '/../functions.php');
foreach ($bootstrap_checks as $type => $file) {
    if (!strpos($functions_content, $file)) {
        error_log("Missing Bootstrap 5 $type file: $file");
    }
}

// Output results
echo "Theme validation complete. Check debug.log for details.\n"; 