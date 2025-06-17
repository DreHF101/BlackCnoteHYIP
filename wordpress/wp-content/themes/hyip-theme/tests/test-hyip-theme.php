<?php
/**
 * HYIP Theme Tests
 *
 * @package HYIP_Theme
 */

declare(strict_types=1);

class HYIP_Theme_Test extends WP_UnitTestCase {
    /**
     * Test theme activation and auto-setup
     */
    public function test_theme_activation(): void {
        // Activate theme
        switch_theme('hyip-theme');
        
        // Check if pages were created
        $this->assertNotNull(get_page_by_path('home'));
        $this->assertNotNull(get_page_by_path('dashboard'));
        $this->assertNotNull(get_page_by_path('plans'));
        $this->assertNotNull(get_page_by_path('transactions'));

        // Check if menu was created
        $menu = wp_get_nav_menu_object('Primary Menu');
        $this->assertNotNull($menu);

        // Check if front page was set
        $this->assertEquals('page', get_option('show_on_front'));
        $home_page = get_page_by_path('home');
        $this->assertEquals($home_page->ID, get_option('page_on_front'));
    }

    /**
     * Test theme settings
     */
    public function test_theme_settings(): void {
        // Test settings registration
        $this->assertTrue(get_option('hyip_theme_auto_setup', false));
        
        // Test settings update
        update_option('hyip_theme_auto_setup', false);
        $this->assertFalse(get_option('hyip_theme_auto_setup'));
        
        // Test settings page capability check
        $this->assertFalse(current_user_can('manage_options'));

        // Test logo setting
        $default_logo = get_template_directory_uri() . '/assets/images/BlackCnote Logo.png';
        $this->assertEquals($default_logo, get_option('hyip_theme_logo', $default_logo));

        // Test cache duration setting
        $this->assertEquals(HOUR_IN_SECONDS, get_option('hyip_theme_cache_duration', HOUR_IN_SECONDS));
    }

    /**
     * Test HYIPLab integration
     */
    public function test_hyiplab_integration(): void {
        // Check if HYIPLab plugin is active
        $this->assertTrue(is_plugin_active('hyiplab/hyiplab.php'));

        // Check if shortcodes are registered
        $this->assertTrue(shortcode_exists('hyiplab_dashboard'));
        $this->assertTrue(shortcode_exists('hyiplab_plans'));
        $this->assertTrue(shortcode_exists('hyiplab_transactions'));

        // Test shortcode output
        $dashboard_output = do_shortcode('[hyiplab_dashboard]');
        $this->assertNotEmpty($dashboard_output);
        $this->assertStringContainsString('dashboard', $dashboard_output);
    }

    /**
     * Test template files
     */
    public function test_template_files(): void {
        $template_files = [
            'template-hyip-dashboard.php',
            'template-hyip-plans.php',
            'template-hyip-transactions.php',
        ];

        foreach ($template_files as $file) {
            $this->assertFileExists(get_template_directory() . '/' . $file);
        }
    }

    /**
     * Test custom post type
     */
    public function test_custom_post_type(): void {
        $post_type = get_post_type_object('hyip_plan');
        $this->assertNotNull($post_type);
        $this->assertEquals('Investment Plans', $post_type->labels->name);
    }

    /**
     * Test theme assets
     */
    public function test_theme_assets(): void {
        // Check if styles are enqueued
        $this->assertTrue(wp_style_is('hyip-theme-style', 'enqueued'));
        $this->assertTrue(wp_style_is('bootstrap', 'enqueued'));

        // Check if scripts are enqueued
        $this->assertTrue(wp_script_is('hyip-theme-script', 'enqueued'));
        $this->assertTrue(wp_script_is('bootstrap', 'enqueued'));
    }

    /**
     * Test security measures
     */
    public function test_security_measures(): void {
        // Check if nonce field is present in settings page
        $this->assertTrue(has_action('admin_init', 'hyip_theme_register_settings'));

        // Check if capabilities are properly checked
        $this->assertFalse(current_user_can('manage_options'));

        // Test data sanitization
        $unsafe_data = '<script>alert("XSS")</script>';
        $safe_data = sanitize_text_field($unsafe_data);
        $this->assertNotEquals($unsafe_data, $safe_data);
    }

    /**
     * Test RTL support
     */
    public function test_rtl_support(): void {
        // Check if RTL stylesheet exists
        $this->assertFileExists(get_template_directory() . '/assets/css/rtl.css');
    }

    /**
     * Test theme validation
     */
    public function test_theme_validation(): void {
        $validation = hyip_theme_validate();
        $this->assertEmpty($validation['errors']);
    }

    /**
     * Test transient caching
     */
    public function test_transient_caching(): void {
        // Test plan caching
        $plans = hyip_get_cached_plans();
        $this->assertIsArray($plans);
        
        // Verify transient was set
        $cache_key = 'hyip_plans_' . md5(serialize([]));
        $this->assertNotFalse(get_transient($cache_key));
        
        // Test cache expiration
        delete_transient($cache_key);
        $this->assertFalse(get_transient($cache_key));
    }

    /**
     * Test auto-setup filter
     */
    public function test_auto_setup_filter(): void {
        // Add test filter
        add_filter('hyip_theme_setup_pages', function($pages) {
            $pages[] = [
                'title' => 'Test Page',
                'slug' => 'test-page',
                'template' => '',
                'content' => 'Test content',
                'menu_order' => 5,
            ];
            return $pages;
        });

        // Run auto-setup
        do_action('after_switch_theme');
        
        // Check if test page was created
        $this->assertNotNull(get_page_by_path('test-page'));
    }

    /**
     * Test custom taxonomy
     */
    public function test_plan_taxonomy(): void {
        // Check if taxonomy is registered
        $taxonomy = get_taxonomy('plan_category');
        $this->assertNotNull($taxonomy);
        $this->assertEquals('Plan Categories', $taxonomy->labels->name);

        // Test taxonomy creation
        $term = wp_insert_term('Test Category', 'plan_category');
        $this->assertNotWPError($term);
        $this->assertIsArray($term);

        // Test term retrieval
        $retrieved_term = get_term($term['term_id'], 'plan_category');
        $this->assertEquals('Test Category', $retrieved_term->name);
    }

    /**
     * Test plan category taxonomy registration and functionality
     */
    public function test_plan_category_taxonomy(): void {
        $this->assertTrue(taxonomy_exists('plan_category'));
        
        // Test term creation
        $term = wp_insert_term('Test Category', 'plan_category');
        $this->assertNotWPError($term);
        
        // Test term retrieval
        $terms = get_terms(['taxonomy' => 'plan_category', 'hide_empty' => false]);
        $this->assertNotEmpty($terms);
        
        // Test term deletion
        $deleted = wp_delete_term($term['term_id'], 'plan_category');
        $this->assertNotWPError($deleted);
    }

    /**
     * Test REST API endpoints for plans
     */
    public function test_rest_api_plans(): void {
        // Test plans endpoint
        $request = new WP_REST_Request('GET', '/hyip/v1/plans');
        $response = rest_do_request($request);
        $this->assertEquals(200, $response->get_status());
        
        // Test single plan endpoint
        $request = new WP_REST_Request('GET', '/hyip/v1/plans/1');
        $response = rest_do_request($request);
        $this->assertEquals(200, $response->get_status());
        
        // Test category filter
        $request = new WP_REST_Request('GET', '/hyip/v1/plans');
        $request->set_param('category', 'test-category');
        $response = rest_do_request($request);
        $this->assertEquals(200, $response->get_status());
    }

    /**
     * Test transient invalidation
     */
    public function test_transient_invalidation(): void {
        // Set a test transient
        set_transient('hyip_plans_test', ['test' => 'data'], HOUR_IN_SECONDS);
        
        // Trigger the invalidation hook
        do_action('hyiplab_plan_updated');
        
        // Verify transient is deleted
        $this->assertFalse(get_transient('hyip_plans_test'));
    }

    /**
     * Test legal disclaimer
     */
    public function test_legal_disclaimer(): void {
        ob_start();
        get_footer();
        $footer_content = ob_get_clean();

        $this->assertStringContainsString('HYIPs involve high risks', $footer_content);
        $this->assertStringContainsString('Past performance is not indicative of future results', $footer_content);
        $this->assertStringContainsString('Ensure compliance with local regulations', $footer_content);
    }

    /**
     * Test 2FA functionality
     */
    public function test_2fa_check(): void {
        // Test 2FA function exists
        $this->assertTrue(function_exists('hyip_theme_check_2fa'));

        // Test without 2FA plugin
        $this->assertTrue(hyip_theme_check_2fa());

        // Test with 2FA plugin (mock)
        if (!function_exists('two_factor_enabled')) {
            function two_factor_enabled($user_id) {
                return false;
            }
        }
        $this->assertFalse(hyip_theme_check_2fa());
    }

    /**
     * Test activity log functionality
     */
    public function test_activity_log(): void {
        // Test logging
        $user_id = get_current_user_id();
        hyip_theme_log_activity($user_id, 'test_action', 'Test details');
        
        global $wpdb;
        $log = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}hyip_activity_log WHERE user_id = %d AND action = %s",
            $user_id,
            'test_action'
        ));
        
        $this->assertNotNull($log);
        $this->assertEquals('test_action', $log->action);
        $this->assertEquals('Test details', $log->details);

        // Test log retrieval
        $logs = hyip_theme_get_activity_log($user_id, 1);
        $this->assertCount(1, $logs);
        $this->assertEquals('test_action', $logs[0]->action);
    }

    /**
     * Test plan comparison shortcode
     */
    public function test_plan_comparison_shortcode(): void {
        // Test shortcode registration
        $this->assertTrue(shortcode_exists('hyip_plan_comparison'));

        // Test shortcode output
        $output = do_shortcode('[hyip_plan_comparison]');
        $this->assertStringContainsString('table-responsive', $output);
        $this->assertStringContainsString('table-bordered', $output);
        $this->assertStringContainsString('table-hover', $output);
        $this->assertStringContainsString('table-striped', $output);

        // Test with attributes
        $output = do_shortcode('[hyip_plan_comparison category="premium" limit="2"]');
        $this->assertStringContainsString('table-responsive', $output);
    }

    /**
     * Test activity log cleanup
     */
    public function test_activity_log_cleanup(): void {
        // Add test log entry
        $user_id = get_current_user_id();
        hyip_theme_log_activity($user_id, 'old_action', 'Old test details');
        
        // Set created_at to 31 days ago
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'hyip_activity_log',
            ['created_at' => date('Y-m-d H:i:s', strtotime('-31 days'))],
            ['action' => 'old_action']
        );

        // Run cleanup
        hyip_theme_cleanup_activity_log();

        // Verify old log is removed
        $log = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}hyip_activity_log WHERE action = %s",
            'old_action'
        ));
        $this->assertNull($log);
    }

    /**
     * Test investment approval workflow
     */
    public function test_investment_approval_workflow(): void {
        // Test initial transaction status
        $transaction = $this->create_test_transaction();
        $this->assertEquals('pending', $transaction->status);

        // Test approval action
        $_GET['action'] = 'approve_investment';
        $_GET['transaction_id'] = $transaction->id;
        $_GET['_wpnonce'] = wp_create_nonce('approve_investment_' . $transaction->id);
        
        $this->login_as_admin();
        do_action('admin_post_approve_investment');
        
        $updated_transaction = $this->get_transaction($transaction->id);
        $this->assertEquals('active', $updated_transaction->status);

        // Test rejection action
        $transaction = $this->create_test_transaction();
        $_GET['action'] = 'reject_investment';
        $_GET['transaction_id'] = $transaction->id;
        $_GET['_wpnonce'] = wp_create_nonce('reject_investment_' . $transaction->id);
        
        do_action('admin_post_reject_investment');
        
        $updated_transaction = $this->get_transaction($transaction->id);
        $this->assertEquals('rejected', $updated_transaction->status);
    }

    /**
     * Test backup functionality
     */
    public function test_backup_functionality(): void {
        // Test backup settings registration
        $this->assertTrue(get_option('hyip_theme_backup_enabled') !== false);
        $this->assertTrue(get_option('hyip_theme_backup_frequency') !== false);
        $this->assertTrue(get_option('hyip_theme_backup_retention') !== false);
        $this->assertTrue(get_option('hyip_theme_backup_email') !== false);

        // Test backup scheduling
        $this->assertTrue(wp_next_scheduled('hyip_theme_backup_event') !== false);

        // Test backup creation
        do_action('hyip_theme_backup_event');
        
        $backup_dir = WP_CONTENT_DIR . '/hyip-backups';
        $files = glob($backup_dir . '/backup-*.sql');
        $this->assertNotEmpty($files);

        // Test backup retention
        $old_file = $backup_dir . '/backup-old.sql';
        file_put_contents($old_file, 'test');
        touch($old_file, time() - (31 * DAY_IN_SECONDS));

        do_action('hyip_theme_backup_event');
        
        $this->assertFalse(file_exists($old_file));
    }

    /**
     * Helper function to create a test transaction
     */
    private function create_test_transaction() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hyiplab_transactions';
        
        $data = [
            'user_id' => 1,
            'plan_id' => 1,
            'amount' => 100,
            'status' => 'pending',
            'created_at' => current_time('mysql'),
        ];
        
        $wpdb->insert($table_name, $data);
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $wpdb->insert_id
        ));
    }

    /**
     * Helper function to get a transaction
     */
    private function get_transaction($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hyiplab_transactions';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }
} 