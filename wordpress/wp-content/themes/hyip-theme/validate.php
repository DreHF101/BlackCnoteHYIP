<?php
/**
 * HYIP Theme Validation Script
 *
 * @package HYIP_Theme
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Validate theme files and structure
 */
function hyip_theme_validate(): array {
    $errors = [];
    $warnings = [];
    $success = [];

    // Check required files
    $required_files = [
        'style.css',
        'index.php',
        'functions.php',
        'header.php',
        'footer.php',
        'screenshot.png',
        'README.md',
        'LICENSE.txt',
        'CHANGELOG.md',
    ];

    foreach ($required_files as $file) {
        if (!file_exists(get_template_directory() . '/' . $file)) {
            $errors[] = "Missing required file: $file";
        } else {
            $success[] = "Found required file: $file";
        }
    }

    // Check style.css header
    $theme_data = wp_get_theme();
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
        'Requires at least',
        'Requires PHP',
    ];

    foreach ($required_headers as $header) {
        if (!$theme_data->get($header)) {
            $errors[] = "Missing required header in style.css: $header";
        } else {
            $success[] = "Found header in style.css: $header";
        }
    }

    // Check PHP version requirement
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        $errors[] = 'PHP version 7.4 or higher is required';
    } else {
        $success[] = 'PHP version check passed';
    }

    // Check WordPress version requirement
    if (version_compare($GLOBALS['wp_version'], '5.0', '<')) {
        $errors[] = 'WordPress version 5.0 or higher is required';
    } else {
        $success[] = 'WordPress version check passed';
    }

    // Check required functions
    $required_functions = [
        'hyip_theme_setup',
        'hyip_theme_register_rest_routes',
        'hyip_theme_register_taxonomies',
        'hyip_theme_check_2fa',
        'hyip_theme_log_activity',
        'hyip_theme_perform_backup',
    ];

    foreach ($required_functions as $function) {
        if (!function_exists($function)) {
            $errors[] = "Missing required function: $function";
        } else {
            $success[] = "Found required function: $function";
        }
    }

    // Check required hooks
    $required_hooks = [
        'after_setup_theme',
        'wp_enqueue_scripts',
        'admin_init',
        'rest_api_init',
        'init',
    ];

    foreach ($required_hooks as $hook) {
        if (!has_action($hook)) {
            $warnings[] = "No actions registered for hook: $hook";
        } else {
            $success[] = "Found actions for hook: $hook";
        }
    }

    // Check required shortcodes
    $required_shortcodes = [
        'hyiplab_dashboard',
        'hyiplab_plans',
        'hyiplab_transactions',
        'hyip_plan_comparison',
    ];

    foreach ($required_shortcodes as $shortcode) {
        if (!shortcode_exists($shortcode)) {
            $errors[] = "Missing required shortcode: $shortcode";
        } else {
            $success[] = "Found required shortcode: $shortcode";
        }
    }

    // Check required database tables
    global $wpdb;
    $required_tables = [
        $wpdb->prefix . 'hyiplab_plans',
        $wpdb->prefix . 'hyiplab_transactions',
        $wpdb->prefix . 'hyip_activity_log',
    ];

    foreach ($required_tables as $table) {
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            $errors[] = "Missing required database table: $table";
        } else {
            $success[] = "Found required database table: $table";
        }
    }

    // Check file permissions
    $directories = [
        get_template_directory(),
        get_template_directory() . '/assets',
        get_template_directory() . '/inc',
    ];

    foreach ($directories as $dir) {
        if (!is_writable($dir)) {
            $warnings[] = "Directory not writable: $dir";
        } else {
            $success[] = "Directory is writable: $dir";
        }
    }

    // Check backup directory
    $backup_dir = WP_CONTENT_DIR . '/hyip-backups';
    if (!file_exists($backup_dir)) {
        $warnings[] = "Backup directory does not exist: $backup_dir";
    } elseif (!is_writable($backup_dir)) {
        $warnings[] = "Backup directory is not writable: $backup_dir";
    } else {
        $success[] = "Backup directory is properly configured";
    }

    // Check theme settings
    $required_settings = [
        'hyip_theme_auto_setup',
        'hyip_theme_backup_enabled',
        'hyip_theme_backup_frequency',
        'hyip_theme_backup_retention',
    ];

    foreach ($required_settings as $setting) {
        if (get_option($setting) === false) {
            $warnings[] = "Missing theme setting: $setting";
        } else {
            $success[] = "Found theme setting: $setting";
        }
    }

    return [
        'errors' => $errors,
        'warnings' => $warnings,
        'success' => $success,
    ];
}

// Run validation if script is called directly
if (php_sapi_name() === 'cli') {
    $results = hyip_theme_validate();
    
    echo "Theme Validation Results:\n\n";
    
    if (!empty($results['errors'])) {
        echo "Errors:\n";
        foreach ($results['errors'] as $error) {
            echo "- $error\n";
        }
        echo "\n";
    }
    
    if (!empty($results['warnings'])) {
        echo "Warnings:\n";
        foreach ($results['warnings'] as $warning) {
            echo "- $warning\n";
        }
        echo "\n";
    }
    
    if (!empty($results['success'])) {
        echo "Success:\n";
        foreach ($results['success'] as $success) {
            echo "- $success\n";
        }
        echo "\n";
    }
    
    exit(empty($results['errors']) ? 0 : 1);
} 