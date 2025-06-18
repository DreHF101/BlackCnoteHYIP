<?php
/**
 * BlackCnote Theme Test Suite
 *
 * @package BlackCnote
 */

class BlackCnote_Theme_Test extends WP_UnitTestCase {
    private $required_files = [
        'template-parts/dashboard.php',
        'template-parts/plans.php',
        'template-parts/transactions.php',
        'blackcnote/dashboard.php',
        'assets/css/blackcnote-theme.css',
        'assets/js/blackcnote-theme.js',
        'languages/blackcnote.pot'
    ];

    private $required_functions = [
        'blackcnote_theme_setup',
        'blackcnote_theme_scripts',
        'blackcnote_theme_register_post_type',
        'blackcnote_theme_add_settings_page',
        'blackcnote_theme_calculate_return',
        'blackcnote_theme_filter_transactions'
    ];

    private $required_hooks = [
        'blackcnote_before_dashboard',
        'blackcnote_after_dashboard',
        'blackcnote_before_plans',
        'blackcnote_after_plans',
        'blackcnote_before_transactions',
        'blackcnote_after_transactions'
    ];

    private $required_tables = [
        'wp_blackcnote_plans',
        'wp_blackcnote_transactions'
    ];

    public function test_required_files_exist() {
        foreach ($this->required_files as $file) {
            $this->assertFileExists(get_template_directory() . '/' . $file);
        }
    }

    public function test_required_functions_exist() {
        foreach ($this->required_functions as $function) {
            $this->assertTrue(function_exists($function));
        }
    }

    public function test_required_hooks_exist() {
        foreach ($this->required_hooks as $hook) {
            $this->assertTrue(has_action($hook));
        }
    }

    public function test_required_tables_exist() {
        global $wpdb;
        foreach ($this->required_tables as $table) {
            $this->assertTrue($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table);
        }
    }

    public function test_blackcnote_integration() {
        // Test BlackCnote integration
        $this->assertTrue(function_exists('blackcnote_system_instance'));
        
        if (!is_plugin_active('blackcnote/blackcnote.php')) {
            error_log('BlackCnote plugin not active');
            return;
        }

        // Test database tables
        global $wpdb;
        $this->assertTrue($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}blackcnote_plans'") === $wpdb->prefix . 'blackcnote_plans');
        $this->assertTrue($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}blackcnote_transactions'") === $wpdb->prefix . 'blackcnote_transactions');
    }

    public function test_theme_activation() {
        $theme = wp_get_theme();
        $this->assertEquals('BlackCnote', $theme->get('Name'));
        $this->assertEquals('blackcnote', $theme->get('TextDomain'));
    }

    public function test_theme_assets() {
        $this->assertFileExists(get_template_directory() . '/assets/css/blackcnote-theme.css');
        $this->assertFileExists(get_template_directory() . '/assets/js/blackcnote-theme.js');
    }

    public function test_theme_languages() {
        $this->assertFileExists(get_template_directory() . '/languages/blackcnote.pot');
    }

    public function test_theme_plugins() {
        $this->assertTrue(function_exists('blackcnote_system_instance'));
    }

    public function test_theme_capabilities() {
        $this->assertTrue(current_user_can('manage_options'));
    }

    public function test_theme_menus() {
        $this->assertTrue(has_nav_menu('primary'));
        $this->assertTrue(has_nav_menu('footer'));
    }

    public function test_theme_widgets() {
        $this->assertTrue(is_active_sidebar('sidebar-1'));
    }

    public function test_theme_options() {
        $this->assertTrue(get_option('blackcnote_theme_options'));
    }

    public function test_theme_post_types() {
        $this->assertTrue(post_type_exists('blackcnote_plan'));
        $this->assertTrue(post_type_exists('blackcnote_transaction'));
    }

    public function test_theme_taxonomies() {
        $this->assertTrue(taxonomy_exists('blackcnote_plan_category'));
    }

    public function test_theme_shortcodes() {
        $this->assertTrue(shortcode_exists('blackcnote_dashboard'));
        $this->assertTrue(shortcode_exists('blackcnote_plans'));
        $this->assertTrue(shortcode_exists('blackcnote_transactions'));
    }

    public function test_theme_templates() {
        $this->assertFileExists(get_template_directory() . '/template-parts/dashboard.php');
        $this->assertFileExists(get_template_directory() . '/template-parts/plans.php');
        $this->assertFileExists(get_template_directory() . '/template-parts/transactions.php');
    }

    public function test_theme_shortcode_output() {
        $output = do_shortcode('[blackcnote_dashboard]');
        $this->assertNotEmpty($output);
    }

    public function test_theme_template_output() {
        ob_start();
        include get_template_directory() . '/template-parts/dashboard.php';
        $output = ob_get_clean();
        $this->assertNotEmpty($output);
    }

    public function test_theme_asset_output() {
        $this->assertTrue(wp_style_is('blackcnote-theme', 'registered'));
        $this->assertTrue(wp_script_is('blackcnote-theme', 'registered'));
    }

    public function test_theme_language_output() {
        $this->assertTrue(function_exists('load_theme_textdomain'));
    }
} 