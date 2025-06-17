<?php
/**
 * Unit Tests for HYIP Theme
 *
 * @package HYIP_Theme
 * @since 1.0.0
 */

declare(strict_types=1);

class HYIPThemeTest extends WP_UnitTestCase {
    /**
     * Set up test environment
     */
    public function setUp(): void {
        parent::setUp();
        
        // Activate HYIPLab plugin
        activate_plugin('hyiplab/hyiplab.php');
        
        // Create test user
        $this->user_id = $this->factory->user->create([
            'role' => 'administrator'
        ]);
        wp_set_current_user($this->user_id);
        
        // Create test investment plan
        $this->plan_id = $this->factory->post->create([
            'post_type' => 'hyip_plan',
            'post_title' => 'Test Plan',
            'post_content' => 'Test plan description',
            'post_status' => 'publish'
        ]);
        
        // Add plan meta
        update_post_meta($this->plan_id, 'hyiplab_plan_min', 100);
        update_post_meta($this->plan_id, 'hyiplab_plan_max', 1000);
        update_post_meta($this->plan_id, 'hyiplab_plan_roi', 10);
        update_post_meta($this->plan_id, 'hyiplab_plan_duration', 30);
    }
    
    /**
     * Test shortcode output
     */
    public function test_hyiplab_dashboard_shortcode(): void {
        $output = do_shortcode('[hyiplab_dashboard]');
        $this->assertStringContainsString('hyip-dashboard', $output);
        $this->assertStringContainsString('investment-form', $output);
    }
    
    public function test_hyiplab_plans_shortcode(): void {
        $output = do_shortcode('[hyiplab_plans]');
        $this->assertStringContainsString('hyip-plans', $output);
        $this->assertStringContainsString('Test Plan', $output);
    }
    
    public function test_hyiplab_transactions_shortcode(): void {
        $output = do_shortcode('[hyiplab_transactions]');
        $this->assertStringContainsString('hyip-transactions', $output);
        $this->assertStringContainsString('transaction-filters', $output);
    }
    
    /**
     * Test AJAX handlers
     */
    public function test_hyiplab_calculate_ajax(): void {
        $_POST['nonce'] = wp_create_nonce('hyiplab_nonce');
        $_POST['plan_id'] = $this->plan_id;
        $_POST['amount'] = 500;
        
        $response = $this->do_ajax('hyiplab_calculate');
        $this->assertTrue($response['success']);
        $this->assertEquals(550, $response['data']['return_amount']);
    }
    
    public function test_hyiplab_filter_transactions_ajax(): void {
        $_POST['nonce'] = wp_create_nonce('hyiplab_nonce');
        $_POST['type'] = 'deposit';
        $_POST['date_from'] = '2024-01-01';
        $_POST['date_to'] = '2024-12-31';
        
        $response = $this->do_ajax('hyiplab_filter_transactions');
        $this->assertTrue($response['success']);
        $this->assertStringContainsString('transaction-table', $response['data']['html']);
    }
    
    /**
     * Test template rendering
     */
    public function test_dashboard_template(): void {
        $template = locate_template('template-hyip-dashboard.php');
        $this->assertNotEmpty($template);
        
        ob_start();
        include $template;
        $output = ob_get_clean();
        
        $this->assertStringContainsString('hyip-dashboard', $output);
        $this->assertStringContainsString('investment-form', $output);
    }
    
    public function test_plans_template(): void {
        $template = locate_template('template-hyip-plans.php');
        $this->assertNotEmpty($template);
        
        ob_start();
        include $template;
        $output = ob_get_clean();
        
        $this->assertStringContainsString('hyip-plans', $output);
        $this->assertStringContainsString('Test Plan', $output);
    }
    
    public function test_transactions_template(): void {
        $template = locate_template('template-hyip-transactions.php');
        $this->assertNotEmpty($template);
        
        ob_start();
        include $template;
        $output = ob_get_clean();
        
        $this->assertStringContainsString('hyip-transactions', $output);
        $this->assertStringContainsString('transaction-filters', $output);
    }
    
    /**
     * Test custom post type
     */
    public function test_hyip_plan_post_type(): void {
        $post_types = get_post_types();
        $this->assertArrayHasKey('hyip_plan', $post_types);
        
        $post_type = get_post_type_object('hyip_plan');
        $this->assertEquals('Investment Plans', $post_type->labels->name);
        $this->assertTrue($post_type->public);
    }
    
    /**
     * Test theme settings
     */
    public function test_theme_settings_page(): void {
        $this->assertTrue(current_user_can('manage_options'));
        
        $menu_items = get_option('hyip_theme_options');
        $this->assertIsArray($menu_items);
    }
    
    /**
     * Test security measures
     */
    public function test_nonce_verification(): void {
        $_POST['nonce'] = wp_create_nonce('hyiplab_nonce');
        $this->assertTrue(wp_verify_nonce($_POST['nonce'], 'hyiplab_nonce'));
        
        $_POST['nonce'] = 'invalid_nonce';
        $this->assertFalse(wp_verify_nonce($_POST['nonce'], 'hyiplab_nonce'));
    }
    
    public function test_input_sanitization(): void {
        $input = '<script>alert("XSS")</script>';
        $sanitized = sanitize_text_field($input);
        $this->assertNotEquals($input, $sanitized);
        $this->assertStringNotContainsString('<script>', $sanitized);
    }
    
    public function test_output_escaping(): void {
        $output = '<div>' . esc_html('<script>alert("XSS")</script>') . '</div>';
        $this->assertStringNotContainsString('<script>', $output);
    }
    
    /**
     * Test performance features
     */
    public function test_transient_caching(): void {
        $plans = get_transient('hyip_plans');
        $this->assertFalse($plans);
        
        // Cache plans
        set_transient('hyip_plans', [$this->plan_id], HOUR_IN_SECONDS);
        
        $plans = get_transient('hyip_plans');
        $this->assertIsArray($plans);
        $this->assertContains($this->plan_id, $plans);
    }
    
    public function test_wp_cron_schedule(): void {
        $timestamp = wp_next_scheduled('hyip_calculate_interest');
        $this->assertNotFalse($timestamp);
    }
    
    /**
     * Test RTL support
     */
    public function test_rtl_styles(): void {
        $this->assertTrue(is_rtl());
        
        $styles = wp_styles();
        $this->assertArrayHasKey('hyip-theme-rtl', $styles->registered);
    }
    
    /**
     * Clean up test environment
     */
    public function tearDown(): void {
        parent::tearDown();
        
        // Delete test data
        wp_delete_post($this->plan_id, true);
        wp_delete_user($this->user_id);
        
        // Deactivate HYIPLab plugin
        deactivate_plugins('hyiplab/hyiplab.php');
    }
} 