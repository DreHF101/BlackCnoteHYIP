<?php
/**
 * HYIP Theme Validation Script
 *
 * @package HYIP_Theme
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Validation Class
 */
class HYIP_Theme_Validator {
    /**
     * Required files
     *
     * @var array
     */
    private array $required_files = [
        'style.css',
        'functions.php',
        'index.php',
        'header.php',
        'footer.php',
        'template-hyip-dashboard.php',
        'template-hyip-plans.php',
        'template-hyip-transactions.php',
        'hyiplab/dashboard.php',
        'assets/css/hyip-theme.css',
        'assets/js/hyip-theme.js',
        'languages/hyip-theme.pot',
        'tests/test-hyip-theme.php',
        'README.md',
        'CHANGELOG.md',
        'LICENSE.txt',
        'screenshot.png',
        'screenshot.txt',
        'validate.php',
        'package.ps1'
    ];

    /**
     * Required functions
     *
     * @var array
     */
    private array $required_functions = [
        'hyip_theme_setup',
        'hyip_theme_scripts',
        'hyip_theme_register_post_type',
        'hyip_theme_add_settings_page',
        'hyip_theme_calculate_return',
        'hyip_theme_filter_transactions'
    ];

    /**
     * Required shortcodes
     *
     * @var array
     */
    private array $required_shortcodes = [
        'hyiplab_dashboard',
        'hyiplab_plans',
        'hyiplab_transactions'
    ];

    /**
     * Required hooks
     *
     * @var array
     */
    private array $required_hooks = [
        'hyiplab_before_dashboard',
        'hyiplab_after_dashboard',
        'hyiplab_before_plans',
        'hyiplab_after_plans',
        'hyiplab_before_transactions',
        'hyiplab_after_transactions'
    ];

    /**
     * Required database tables
     *
     * @var array
     */
    private array $required_tables = [
        'wp_hyiplab_plans',
        'wp_hyiplab_transactions'
    ];

    /**
     * Theme directory
     *
     * @var string
     */
    private string $theme_dir;

    /**
     * Constructor
     */
    public function __construct() {
        $this->theme_dir = get_template_directory();
    }

    /**
     * Run all validation checks
     */
    public function validate(): void {
        $this->check_required_files();
        $this->check_required_functions();
        $this->check_required_shortcodes();
        $this->check_required_hooks();
        $this->check_required_tables();
        $this->check_security_measures();
        $this->check_performance_optimizations();
        $this->check_theme_review_guidelines();
        $this->check_hyiplab_integration();
    }

    /**
     * Check required files
     */
    private function check_required_files(): void {
        foreach ($this->required_files as $file) {
            $file_path = $this->theme_dir . '/' . $file;
            if (!file_exists($file_path)) {
                error_log("Missing required file: {$file}");
            }
        }
    }

    /**
     * Check required functions
     */
    private function check_required_functions(): void {
        foreach ($this->required_functions as $function) {
            if (!function_exists($function)) {
                error_log("Missing required function: {$function}");
            }
        }
    }

    /**
     * Check required shortcodes
     */
    private function check_required_shortcodes(): void {
        global $shortcode_tags;
        foreach ($this->required_shortcodes as $shortcode) {
            if (!isset($shortcode_tags[$shortcode])) {
                error_log("Missing required shortcode: {$shortcode}");
            }
        }
    }

    /**
     * Check required hooks
     */
    private function check_required_hooks(): void {
        global $wp_filter;
        foreach ($this->required_hooks as $hook) {
            if (!isset($wp_filter[$hook])) {
                error_log("Missing required hook: {$hook}");
            }
        }
    }

    /**
     * Check required database tables
     */
    private function check_required_tables(): void {
        global $wpdb;
        foreach ($this->required_tables as $table) {
            $table_name = $wpdb->prefix . str_replace('wp_', '', $table);
            if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
                error_log("Missing required database table: {$table}");
            }
        }
    }

    /**
     * Check security measures
     */
    private function check_security_measures(): void {
        // Check nonce implementation
        $functions_file = file_get_contents($this->theme_dir . '/functions.php');
        if (!preg_match('/wp_create_nonce|wp_verify_nonce/', $functions_file)) {
            error_log('Missing nonce implementation in functions.php');
        }

        // Check capability checks
        if (!preg_match('/current_user_can/', $functions_file)) {
            error_log('Missing capability checks in functions.php');
        }

        // Check data sanitization
        if (!preg_match('/sanitize_text_field|sanitize_email|sanitize_title/', $functions_file)) {
            error_log('Missing data sanitization in functions.php');
        }

        // Check output escaping
        if (!preg_match('/esc_html|esc_attr|esc_url/', $functions_file)) {
            error_log('Missing output escaping in functions.php');
        }
    }

    /**
     * Check performance optimizations
     */
    private function check_performance_optimizations(): void {
        $functions_file = file_get_contents($this->theme_dir . '/functions.php');

        // Check transient API usage
        if (!preg_match('/set_transient|get_transient|delete_transient/', $functions_file)) {
            error_log('Missing transient API usage in functions.php');
        }

        // Check object cache usage
        if (!preg_match('/wp_cache_set|wp_cache_get|wp_cache_delete/', $functions_file)) {
            error_log('Missing object cache usage in functions.php');
        }

        // Check database optimization
        if (!preg_match('/prepare|get_var|get_row|get_col|get_results/', $functions_file)) {
            error_log('Missing database optimization in functions.php');
        }

        // Check WP_Cron usage
        if (!preg_match('/wp_schedule_event|wp_next_scheduled/', $functions_file)) {
            error_log('Missing WP_Cron usage in functions.php');
        }
    }

    /**
     * Check theme review guidelines
     */
    private function check_theme_review_guidelines(): void {
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            error_log('PHP version must be 7.4 or higher');
        }

        // Check WordPress version
        if (version_compare($GLOBALS['wp_version'], '5.0', '<')) {
            error_log('WordPress version must be 5.0 or higher');
        }

        // Check file permissions
        $files = glob($this->theme_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file) && substr(sprintf('%o', fileperms($file)), -4) !== '0644') {
                error_log("Incorrect file permissions for: {$file}");
            }
        }

        // Check for direct file access
        $php_files = glob($this->theme_dir . '/*.php');
        foreach ($php_files as $file) {
            $content = file_get_contents($file);
            if (strpos($content, 'ABSPATH') === false) {
                error_log("Missing ABSPATH check in: {$file}");
            }
        }
    }

    /**
     * Check HYIPLab integration
     */
    private function check_hyiplab_integration(): void {
        // Check if HYIPLab plugin is active
        if (!is_plugin_active('hyiplab/hyiplab.php')) {
            error_log('HYIPLab plugin is not active');
            return;
        }

        // Check for required HYIPLab functions
        $required_hyiplab_functions = [
            'hyiplab_get_plans',
            'hyiplab_get_transactions',
            'hyiplab_calculate_return'
        ];

        foreach ($required_hyiplab_functions as $function) {
            if (!function_exists($function)) {
                error_log("Missing required HYIPLab function: {$function}");
            }
        }

        // Check for required HYIPLab hooks
        $required_hyiplab_hooks = [
            'hyiplab_before_dashboard',
            'hyiplab_after_dashboard',
            'hyiplab_before_plans',
            'hyiplab_after_plans',
            'hyiplab_before_transactions',
            'hyiplab_after_transactions'
        ];

        foreach ($required_hyiplab_hooks as $hook) {
            if (!has_action($hook)) {
                error_log("Missing required HYIPLab hook: {$hook}");
            }
        }
    }
}

// Run validation if script is accessed directly
if (defined('WP_DEBUG') && WP_DEBUG) {
    $validator = new HYIP_Theme_Validator();
    $validator->validate();
} 