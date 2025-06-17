<?php
/**
 * BlackCnote Theme Validation Script
 *
 * @package BlackCnote
 */

if (!defined('ABSPATH')) {
    exit;
}

class BlackCnote_Theme_Validator {
    private $errors = [];

    public function validate() {
        $this->check_required_files();
        $this->check_required_functions();
        $this->check_required_shortcodes();
        $this->check_required_hooks();
        $this->check_required_tables();
        $this->check_blackcnote_integration();

        return empty($this->errors);
    }

    private function check_required_files() {
        $required_files = [
            'template-parts/dashboard.php',
            'template-parts/plans.php',
            'template-parts/transactions.php',
            'blackcnote/dashboard.php',
            'assets/css/blackcnote-theme.css',
            'assets/js/blackcnote-theme.js',
            'languages/blackcnote.pot'
        ];

        foreach ($required_files as $file) {
            if (!file_exists(get_template_directory() . '/' . $file)) {
                $this->errors[] = sprintf(
                    'Required file missing: %s',
                    $file
                );
            }
        }
    }

    private function check_required_functions() {
        $required_functions = [
            'blackcnote_get_plan_details',
            'blackcnote_calculate_return',
            'blackcnote_process_investment',
            'blackcnote_process_withdrawal'
        ];

        foreach ($required_functions as $function) {
            if (!function_exists($function)) {
                $this->errors[] = sprintf(
                    'Required function missing: %s',
                    $function
                );
            }
        }
    }

    private function check_required_shortcodes() {
        $required_shortcodes = [
            'blackcnote_dashboard',
            'blackcnote_plans',
            'blackcnote_transactions'
        ];

        foreach ($required_shortcodes as $shortcode) {
            if (!shortcode_exists($shortcode)) {
                $this->errors[] = sprintf(
                    'Required shortcode missing: %s',
                    $shortcode
                );
            }
        }
    }

    private function check_required_hooks() {
        $required_hooks = [
            'blackcnote_before_dashboard',
            'blackcnote_after_dashboard',
            'blackcnote_before_plans',
            'blackcnote_after_plans',
            'blackcnote_before_transactions',
            'blackcnote_after_transactions'
        ];

        foreach ($required_hooks as $hook) {
            if (!has_action($hook)) {
                $this->errors[] = sprintf(
                    'Required hook missing: %s',
                    $hook
                );
            }
        }
    }

    private function check_required_tables() {
        global $wpdb;

        $required_tables = [
            $wpdb->prefix . 'blackcnote_plans',
            $wpdb->prefix . 'blackcnote_transactions'
        ];

        foreach ($required_tables as $table) {
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
                $this->errors[] = sprintf(
                    'Required table missing: %s',
                    $table
                );
            }
        }
    }

    private function check_blackcnote_integration() {
        // Test BlackCnote integration
        if (!function_exists('blackcnote_system_instance')) {
            $this->errors[] = 'BlackCnote plugin not active';
            return;
        }

        // Check if required tables exist
        global $wpdb;
        $required_tables = [
            $wpdb->prefix . 'blackcnote_plans',
            $wpdb->prefix . 'blackcnote_transactions'
        ];

        foreach ($required_tables as $table) {
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
                $this->errors[] = sprintf(
                    'Required BlackCnote table missing: %s',
                    $table
                );
            }
        }
    }

    public function get_errors() {
        return $this->errors;
    }
}

// Run validation
$validator = new BlackCnote_Theme_Validator();
$is_valid = $validator->validate();

if (!$is_valid) {
    $errors = $validator->get_errors();
    foreach ($errors as $error) {
        error_log('BlackCnote Theme Validation Error: ' . $error);
    }
} 