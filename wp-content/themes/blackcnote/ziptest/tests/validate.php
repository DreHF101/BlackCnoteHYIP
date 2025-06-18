<?php
/**
 * Theme Validation Script
 *
 * @package BlackCnote_Theme
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
    'template-blackcnote-dashboard.php',
    'template-blackcnote-plans.php',
    'template-blackcnote-transactions.php',
    'template-parts/blackcnote/dashboard.php',
    'template-parts/blackcnote/plans.php',
    'template-parts/blackcnote/transactions.php',
    'inc/blackcnote-functions.php',
    'inc/blackcnote-hooks.php',
    'inc/blackcnote-shortcodes.php',
    'inc/blackcnote-ajax.php',
    'inc/blackcnote-cron.php',
    'inc/blackcnote-admin.php',
    'inc/blackcnote-woocommerce.php',
    'inc/blackcnote-security.php',
    'inc/blackcnote-api.php',
    'inc/blackcnote-email.php',
    'inc/blackcnote-notifications.php',
    'inc/blackcnote-reports.php',
    'inc/blackcnote-export.php',
    'inc/blackcnote-import.php',
    'inc/blackcnote-backup.php',
    'inc/blackcnote-restore.php',
    'inc/blackcnote-update.php',
    'inc/blackcnote-debug.php',
    'inc/blackcnote-logger.php',
    'inc/blackcnote-cache.php',
    'inc/blackcnote-optimization.php',
    'inc/blackcnote-maintenance.php',
    'inc/blackcnote-cleanup.php',
    'inc/blackcnote-settings.php',
    'inc/blackcnote-options.php',
    'inc/blackcnote-widgets.php',
    'inc/blackcnote-customizer.php',
    'inc/blackcnote-menu.php',
    'inc/blackcnote-sidebar.php',
    'inc/blackcnote-footer.php',
    'inc/blackcnote-header.php',
    'inc/blackcnote-navigation.php',
    'inc/blackcnote-pagination.php',
    'inc/blackcnote-comments.php',
    'inc/blackcnote-search.php',
    'inc/blackcnote-archive.php',
    'inc/blackcnote-single.php',
    'inc/blackcnote-page.php',
    'inc/blackcnote-attachment.php',
    'inc/blackcnote-404.php',
    'inc/blackcnote-embed.php',
    'inc/blackcnote-content.php',
    'inc/blackcnote-excerpt.php',
    'inc/blackcnote-meta.php',
    'inc/blackcnote-taxonomy.php',
    'inc/blackcnote-author.php',
    'inc/blackcnote-date.php',
    'inc/blackcnote-category.php',
    'inc/blackcnote-tag.php',
    'inc/blackcnote-format.php',
    'inc/blackcnote-post-type.php',
    'inc/blackcnote-template.php',
    'inc/blackcnote-walker.php',
    'inc/blackcnote-widget.php',
    'inc/blackcnote-shortcode.php'
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

// Test BlackCnoteLab integration
if (!function_exists('is_plugin_active')) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if (!is_plugin_active('blackcnote/blackcnote.php')) {
    error_log('BlackCnote plugin not active');
}

// Check database tables
global $wpdb;
$required_tables = [
    $wpdb->prefix . 'blackcnote_plans',
    $wpdb->prefix . 'blackcnote_transactions'
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
    'template-blackcnote-dashboard.php',
    'template-blackcnote-plans.php',
    'template-blackcnote-transactions.php',
    'template-parts/blackcnote/dashboard.php',
    'template-parts/blackcnote/plans.php',
    'template-parts/blackcnote/transactions.php',
    'inc/blackcnote-functions.php',
    'inc/blackcnote-hooks.php',
    'inc/blackcnote-shortcodes.php',
    'inc/blackcnote-ajax.php',
    'inc/blackcnote-cron.php',
    'inc/blackcnote-admin.php',
    'inc/blackcnote-woocommerce.php',
    'inc/blackcnote-security.php',
    'inc/blackcnote-api.php',
    'inc/blackcnote-email.php',
    'inc/blackcnote-notifications.php',
    'inc/blackcnote-reports.php',
    'inc/blackcnote-export.php',
    'inc/blackcnote-import.php',
    'inc/blackcnote-backup.php',
    'inc/blackcnote-restore.php',
    'inc/blackcnote-update.php',
    'inc/blackcnote-debug.php',
    'inc/blackcnote-logger.php',
    'inc/blackcnote-cache.php',
    'inc/blackcnote-optimization.php',
    'inc/blackcnote-maintenance.php',
    'inc/blackcnote-cleanup.php',
    'inc/blackcnote-settings.php',
    'inc/blackcnote-options.php',
    'inc/blackcnote-widgets.php',
    'inc/blackcnote-customizer.php',
    'inc/blackcnote-menu.php',
    'inc/blackcnote-sidebar.php',
    'inc/blackcnote-footer.php',
    'inc/blackcnote-header.php',
    'inc/blackcnote-navigation.php',
    'inc/blackcnote-pagination.php',
    'inc/blackcnote-comments.php',
    'inc/blackcnote-search.php',
    'inc/blackcnote-archive.php',
    'inc/blackcnote-single.php',
    'inc/blackcnote-page.php',
    'inc/blackcnote-attachment.php',
    'inc/blackcnote-404.php',
    'inc/blackcnote-embed.php',
    'inc/blackcnote-content.php',
    'inc/blackcnote-excerpt.php',
    'inc/blackcnote-meta.php',
    'inc/blackcnote-taxonomy.php',
    'inc/blackcnote-author.php',
    'inc/blackcnote-date.php',
    'inc/blackcnote-category.php',
    'inc/blackcnote-tag.php',
    'inc/blackcnote-format.php',
    'inc/blackcnote-post-type.php',
    'inc/blackcnote-template.php',
    'inc/blackcnote-walker.php',
    'inc/blackcnote-widget.php',
    'inc/blackcnote-shortcode.php'
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