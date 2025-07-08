<?php
/**
 * BlackCnote Backend Settings Manager
 * Comprehensive backend settings management and enhancement system
 *
 * @package BlackCnote
 * @version 2.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Backend Settings Manager Class
 */
class BlackCnote_Backend_Settings_Manager {
    
    private $settings_group = 'blackcnote_theme_settings';
    private $settings_page = 'blackcnote-settings';
    private $capability = 'manage_options';
    private $version = '2.0';
    
    public function __construct() {
        add_action('admin_init', array($this, 'init_settings'));
        add_action('admin_menu', array($this, 'add_settings_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_blackcnote_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_blackcnote_reset_settings', array($this, 'ajax_reset_settings'));
        add_action('wp_ajax_blackcnote_export_settings', array($this, 'ajax_export_settings'));
        add_action('wp_ajax_blackcnote_import_settings', array($this, 'ajax_import_settings'));
        add_action('wp_ajax_blackcnote_validate_settings', array($this, 'ajax_validate_settings'));
        add_action('wp_ajax_blackcnote_backup_settings', array($this, 'ajax_backup_settings'));
        add_action('wp_ajax_blackcnote_restore_settings', array($this, 'ajax_restore_settings'));
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        register_setting(
            $this->settings_group,
            'blackcnote_theme_settings',
            array(
                'type' => 'array',
                'description' => 'BlackCnote Theme Settings',
                'sanitize_callback' => array($this, 'sanitize_settings'),
                'default' => $this->get_default_settings()
            )
        );
        
        // Register individual settings for better control
        $this->register_individual_settings();
    }
    
    /**
     * Register individual settings
     */
    private function register_individual_settings() {
        $settings = $this->get_all_settings();
        
        foreach ($settings as $setting_key => $setting_config) {
            register_setting(
                $this->settings_group,
                $setting_key,
                array(
                    'type' => $setting_config['type'],
                    'description' => $setting_config['description'],
                    'sanitize_callback' => $setting_config['sanitize_callback'],
                    'default' => $setting_config['default']
                )
            );
        }
    }
    
    /**
     * Get all settings configuration
     */
    private function get_all_settings() {
        return array(
            // General Settings
            'blackcnote_site_title' => array(
                'type' => 'string',
                'description' => 'Custom site title',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => get_bloginfo('name')
            ),
            'blackcnote_site_description' => array(
                'type' => 'string',
                'description' => 'Custom site description',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => get_bloginfo('description')
            ),
            'blackcnote_logo_url' => array(
                'type' => 'string',
                'description' => 'Logo URL',
                'sanitize_callback' => 'esc_url_raw',
                'default' => ''
            ),
            'blackcnote_favicon_url' => array(
                'type' => 'string',
                'description' => 'Favicon URL',
                'sanitize_callback' => 'esc_url_raw',
                'default' => ''
            ),
            
            // Appearance Settings
            'blackcnote_primary_color' => array(
                'type' => 'string',
                'description' => 'Primary color',
                'sanitize_callback' => 'sanitize_hex_color',
                'default' => '#007cba'
            ),
            'blackcnote_secondary_color' => array(
                'type' => 'string',
                'description' => 'Secondary color',
                'sanitize_callback' => 'sanitize_hex_color',
                'default' => '#6c757d'
            ),
            'blackcnote_accent_color' => array(
                'type' => 'string',
                'description' => 'Accent color',
                'sanitize_callback' => 'sanitize_hex_color',
                'default' => '#28a745'
            ),
            'blackcnote_custom_css' => array(
                'type' => 'string',
                'description' => 'Custom CSS',
                'sanitize_callback' => array($this, 'sanitize_css'),
                'default' => ''
            ),
            'blackcnote_custom_js' => array(
                'type' => 'string',
                'description' => 'Custom JavaScript',
                'sanitize_callback' => array($this, 'sanitize_js'),
                'default' => ''
            ),
            
            // Layout Settings
            'blackcnote_layout_type' => array(
                'type' => 'string',
                'description' => 'Layout type',
                'sanitize_callback' => array($this, 'sanitize_layout_type'),
                'default' => 'wide'
            ),
            'blackcnote_sidebar_position' => array(
                'type' => 'string',
                'description' => 'Sidebar position',
                'sanitize_callback' => array($this, 'sanitize_sidebar_position'),
                'default' => 'right'
            ),
            'blackcnote_container_width' => array(
                'type' => 'integer',
                'description' => 'Container width',
                'sanitize_callback' => 'absint',
                'default' => 1200
            ),
            
            // Typography Settings
            'blackcnote_body_font' => array(
                'type' => 'string',
                'description' => 'Body font family',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'Inter, sans-serif'
            ),
            'blackcnote_heading_font' => array(
                'type' => 'string',
                'description' => 'Heading font family',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'Inter, sans-serif'
            ),
            'blackcnote_font_size_base' => array(
                'type' => 'integer',
                'description' => 'Base font size',
                'sanitize_callback' => 'absint',
                'default' => 16
            ),
            
            // Investment Settings
            'blackcnote_investment_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable investment features',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            'blackcnote_dashboard_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable investment dashboard',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            'blackcnote_market_data_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable market data',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            
            // Statistics Settings
            'blackcnote_stat_total_invested' => array(
                'type' => 'string',
                'description' => 'Total invested amount',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '2500000'
            ),
            'blackcnote_stat_active_investors' => array(
                'type' => 'string',
                'description' => 'Active investors count',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '1200'
            ),
            'blackcnote_stat_success_rate' => array(
                'type' => 'string',
                'description' => 'Success rate percentage',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '98.5'
            ),
            'blackcnote_stat_years_experience' => array(
                'type' => 'string',
                'description' => 'Years of experience',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '5'
            ),
            
            // Advanced Settings
            'blackcnote_live_editing_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable live editing',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            'blackcnote_react_integration_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable React integration',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            'blackcnote_debug_mode_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable debug mode',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false
            ),
            'blackcnote_auto_backup_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable auto backup',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            
            // Security Settings
            'blackcnote_security_level' => array(
                'type' => 'string',
                'description' => 'Security level',
                'sanitize_callback' => array($this, 'sanitize_security_level'),
                'default' => 'high'
            ),
            'blackcnote_two_factor_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable two-factor authentication',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false
            ),
            'blackcnote_session_timeout' => array(
                'type' => 'integer',
                'description' => 'Session timeout in minutes',
                'sanitize_callback' => 'absint',
                'default' => 30
            ),
            
            // Performance Settings
            'blackcnote_cache_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable caching',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            'blackcnote_minify_css' => array(
                'type' => 'boolean',
                'description' => 'Minify CSS',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            'blackcnote_minify_js' => array(
                'type' => 'boolean',
                'description' => 'Minify JavaScript',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            'blackcnote_lazy_loading' => array(
                'type' => 'boolean',
                'description' => 'Enable lazy loading',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            
            // Analytics Settings
            'blackcnote_google_analytics_id' => array(
                'type' => 'string',
                'description' => 'Google Analytics ID',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => ''
            ),
            'blackcnote_facebook_pixel_id' => array(
                'type' => 'string',
                'description' => 'Facebook Pixel ID',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => ''
            ),
            'blackcnote_custom_analytics_code' => array(
                'type' => 'string',
                'description' => 'Custom analytics code',
                'sanitize_callback' => array($this, 'sanitize_html'),
                'default' => ''
            ),
            
            // Social Media Settings
            'blackcnote_social_facebook' => array(
                'type' => 'string',
                'description' => 'Facebook URL',
                'sanitize_callback' => 'esc_url_raw',
                'default' => ''
            ),
            'blackcnote_social_twitter' => array(
                'type' => 'string',
                'description' => 'Twitter URL',
                'sanitize_callback' => 'esc_url_raw',
                'default' => ''
            ),
            'blackcnote_social_instagram' => array(
                'type' => 'string',
                'description' => 'Instagram URL',
                'sanitize_callback' => 'esc_url_raw',
                'default' => ''
            ),
            'blackcnote_social_linkedin' => array(
                'type' => 'string',
                'description' => 'LinkedIn URL',
                'sanitize_callback' => 'esc_url_raw',
                'default' => ''
            ),
            'blackcnote_social_youtube' => array(
                'type' => 'string',
                'description' => 'YouTube URL',
                'sanitize_callback' => 'esc_url_raw',
                'default' => ''
            ),
            
            // Email Settings
            'blackcnote_email_from_name' => array(
                'type' => 'string',
                'description' => 'Email from name',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => get_bloginfo('name')
            ),
            'blackcnote_email_from_address' => array(
                'type' => 'string',
                'description' => 'Email from address',
                'sanitize_callback' => 'sanitize_email',
                'default' => get_option('admin_email')
            ),
            'blackcnote_email_template_header' => array(
                'type' => 'string',
                'description' => 'Email template header',
                'sanitize_callback' => array($this, 'sanitize_html'),
                'default' => ''
            ),
            'blackcnote_email_template_footer' => array(
                'type' => 'string',
                'description' => 'Email template footer',
                'sanitize_callback' => array($this, 'sanitize_html'),
                'default' => ''
            ),
            
            // Notification Settings
            'blackcnote_notifications_enabled' => array(
                'type' => 'boolean',
                'description' => 'Enable notifications',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            'blackcnote_email_notifications' => array(
                'type' => 'boolean',
                'description' => 'Email notifications',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true
            ),
            'blackcnote_push_notifications' => array(
                'type' => 'boolean',
                'description' => 'Push notifications',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false
            ),
            'blackcnote_sms_notifications' => array(
                'type' => 'boolean',
                'description' => 'SMS notifications',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false
            )
        );
    }
    
    /**
     * Get default settings
     */
    private function get_default_settings() {
        $defaults = array();
        $all_settings = $this->get_all_settings();
        
        foreach ($all_settings as $key => $config) {
            $defaults[$key] = $config['default'];
        }
        
        return $defaults;
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        if (!is_array($input)) {
            return $this->get_default_settings();
        }
        
        $sanitized = array();
        $all_settings = $this->get_all_settings();
        
        foreach ($all_settings as $key => $config) {
            if (isset($input[$key])) {
                $sanitized[$key] = call_user_func($config['sanitize_callback'], $input[$key]);
            } else {
                $sanitized[$key] = $config['default'];
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Custom sanitization functions
     */
    public function sanitize_css($input) {
        // Basic CSS sanitization - in production, use a proper CSS parser
        return wp_strip_all_tags($input);
    }
    
    public function sanitize_js($input) {
        // Basic JS sanitization - in production, use a proper JS parser
        return wp_strip_all_tags($input);
    }
    
    public function sanitize_html($input) {
        return wp_kses_post($input);
    }
    
    public function sanitize_layout_type($input) {
        $allowed = array('wide', 'boxed', 'fluid');
        return in_array($input, $allowed) ? $input : 'wide';
    }
    
    public function sanitize_sidebar_position($input) {
        $allowed = array('left', 'right', 'none');
        return in_array($input, $allowed) ? $input : 'right';
    }
    
    public function sanitize_security_level($input) {
        $allowed = array('low', 'medium', 'high', 'maximum');
        return in_array($input, $allowed) ? $input : 'high';
    }
    
    /**
     * Add settings pages
     */
    public function add_settings_pages() {
        // Main settings page
        add_theme_page(
            __('BlackCnote Settings', 'blackcnote'),
            __('BlackCnote Settings', 'blackcnote'),
            $this->capability,
            $this->settings_page,
            array($this, 'render_main_settings_page')
        );
        
        // Advanced settings page
        add_theme_page(
            __('Advanced Settings', 'blackcnote'),
            __('Advanced Settings', 'blackcnote'),
            $this->capability,
            'blackcnote-advanced-settings',
            array($this, 'render_advanced_settings_page')
        );
        
        // Security settings page
        add_theme_page(
            __('Security Settings', 'blackcnote'),
            __('Security Settings', 'blackcnote'),
            $this->capability,
            'blackcnote-security-settings',
            array($this, 'render_security_settings_page')
        );
        
        // Performance settings page
        add_theme_page(
            __('Performance Settings', 'blackcnote'),
            __('Performance Settings', 'blackcnote'),
            $this->capability,
            'blackcnote-performance-settings',
            array($this, 'render_performance_settings_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'blackcnote') !== false) {
            wp_enqueue_style(
                'blackcnote-backend-settings',
                get_template_directory_uri() . '/inc/backend-settings.css',
                array(),
                $this->version
            );
            
            wp_enqueue_script(
                'blackcnote-backend-settings',
                get_template_directory_uri() . '/inc/backend-settings.js',
                array('jquery', 'wp-color-picker'),
                $this->version,
                true
            );
            
            wp_localize_script('blackcnote-backend-settings', 'blackcnoteBackend', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('blackcnote_backend_nonce'),
                'strings' => array(
                    'saving' => __('Saving...', 'blackcnote'),
                    'saved' => __('Settings saved!', 'blackcnote'),
                    'error' => __('Error saving settings', 'blackcnote'),
                    'confirmReset' => __('Are you sure you want to reset all settings?', 'blackcnote'),
                    'confirmImport' => __('Are you sure you want to import these settings?', 'blackcnote')
                )
            ));
        }
    }
    
    /**
     * AJAX handlers
     */
    public function ajax_save_settings() {
        check_ajax_referer('blackcnote_backend_nonce', 'nonce');
        
        if (!current_user_can($this->capability)) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $settings = $_POST['settings'] ?? array();
        $sanitized_settings = $this->sanitize_settings($settings);
        
        $result = update_option('blackcnote_theme_settings', $sanitized_settings);
        
        if ($result) {
            wp_send_json_success('Settings saved successfully');
        } else {
            wp_send_json_error('Failed to save settings');
        }
    }
    
    public function ajax_reset_settings() {
        check_ajax_referer('blackcnote_backend_nonce', 'nonce');
        
        if (!current_user_can($this->capability)) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $default_settings = $this->get_default_settings();
        $result = update_option('blackcnote_theme_settings', $default_settings);
        
        if ($result) {
            wp_send_json_success('Settings reset successfully');
        } else {
            wp_send_json_error('Failed to reset settings');
        }
    }
    
    public function ajax_export_settings() {
        check_ajax_referer('blackcnote_backend_nonce', 'nonce');
        
        if (!current_user_can($this->capability)) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $settings = get_option('blackcnote_theme_settings', array());
        $export_data = array(
            'version' => $this->version,
            'timestamp' => current_time('mysql'),
            'settings' => $settings
        );
        
        wp_send_json_success($export_data);
    }
    
    public function ajax_import_settings() {
        check_ajax_referer('blackcnote_backend_nonce', 'nonce');
        
        if (!current_user_can($this->capability)) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $import_data = $_POST['import_data'] ?? array();
        
        if (empty($import_data) || !isset($import_data['settings'])) {
            wp_send_json_error('Invalid import data');
        }
        
        $sanitized_settings = $this->sanitize_settings($import_data['settings']);
        $result = update_option('blackcnote_theme_settings', $sanitized_settings);
        
        if ($result) {
            wp_send_json_success('Settings imported successfully');
        } else {
            wp_send_json_error('Failed to import settings');
        }
    }
    
    public function ajax_validate_settings() {
        check_ajax_referer('blackcnote_backend_nonce', 'nonce');
        
        if (!current_user_can($this->capability)) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $settings = $_POST['settings'] ?? array();
        $errors = $this->validate_settings($settings);
        
        if (empty($errors)) {
            wp_send_json_success('Settings are valid');
        } else {
            wp_send_json_error($errors);
        }
    }
    
    public function ajax_backup_settings() {
        check_ajax_referer('blackcnote_backend_nonce', 'nonce');
        
        if (!current_user_can($this->capability)) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $settings = get_option('blackcnote_theme_settings', array());
        $backup_data = array(
            'version' => $this->version,
            'timestamp' => current_time('mysql'),
            'settings' => $settings
        );
        
        $backup_id = 'blackcnote_backup_' . time();
        $result = update_option($backup_id, $backup_data);
        
        if ($result) {
            wp_send_json_success(array(
                'backup_id' => $backup_id,
                'message' => 'Settings backed up successfully'
            ));
        } else {
            wp_send_json_error('Failed to backup settings');
        }
    }
    
    public function ajax_restore_settings() {
        check_ajax_referer('blackcnote_backend_nonce', 'nonce');
        
        if (!current_user_can($this->capability)) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $backup_id = $_POST['backup_id'] ?? '';
        
        if (empty($backup_id)) {
            wp_send_json_error('Backup ID is required');
        }
        
        $backup_data = get_option($backup_id, array());
        
        if (empty($backup_data) || !isset($backup_data['settings'])) {
            wp_send_json_error('Invalid backup data');
        }
        
        $sanitized_settings = $this->sanitize_settings($backup_data['settings']);
        $result = update_option('blackcnote_theme_settings', $sanitized_settings);
        
        if ($result) {
            wp_send_json_success('Settings restored successfully');
        } else {
            wp_send_json_error('Failed to restore settings');
        }
    }
    
    /**
     * Validate settings
     */
    private function validate_settings($settings) {
        $errors = array();
        
        // Validate email addresses
        if (!empty($settings['blackcnote_email_from_address']) && !is_email($settings['blackcnote_email_from_address'])) {
            $errors[] = 'Invalid email address';
        }
        
        // Validate URLs
        $url_fields = array(
            'blackcnote_logo_url',
            'blackcnote_favicon_url',
            'blackcnote_social_facebook',
            'blackcnote_social_twitter',
            'blackcnote_social_instagram',
            'blackcnote_social_linkedin',
            'blackcnote_social_youtube'
        );
        
        foreach ($url_fields as $field) {
            if (!empty($settings[$field]) && !filter_var($settings[$field], FILTER_VALIDATE_URL)) {
                $errors[] = "Invalid URL for {$field}";
            }
        }
        
        // Validate colors
        $color_fields = array(
            'blackcnote_primary_color',
            'blackcnote_secondary_color',
            'blackcnote_accent_color'
        );
        
        foreach ($color_fields as $field) {
            if (!empty($settings[$field]) && !preg_match('/^#[a-f0-9]{6}$/i', $settings[$field])) {
                $errors[] = "Invalid color format for {$field}";
            }
        }
        
        // Validate numbers
        $number_fields = array(
            'blackcnote_container_width' => array('min' => 800, 'max' => 2000),
            'blackcnote_font_size_base' => array('min' => 12, 'max' => 24),
            'blackcnote_session_timeout' => array('min' => 5, 'max' => 480)
        );
        
        foreach ($number_fields as $field => $limits) {
            if (isset($settings[$field])) {
                $value = intval($settings[$field]);
                if ($value < $limits['min'] || $value > $limits['max']) {
                    $errors[] = "{$field} must be between {$limits['min']} and {$limits['max']}";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Get current settings
     */
    public function get_current_settings() {
        return get_option('blackcnote_theme_settings', $this->get_default_settings());
    }
    
    /**
     * Get setting value
     */
    public function get_setting($key, $default = null) {
        $settings = $this->get_current_settings();
        return isset($settings[$key]) ? $settings[$key] : $default;
    }
    
    /**
     * Update setting value
     */
    public function update_setting($key, $value) {
        $settings = $this->get_current_settings();
        $settings[$key] = $value;
        return update_option('blackcnote_theme_settings', $settings);
    }
    
    /**
     * Render main settings page
     */
    public function render_main_settings_page() {
        $settings = $this->get_current_settings();
        include get_template_directory() . '/inc/templates/backend-settings-main.php';
    }
    
    /**
     * Render advanced settings page
     */
    public function render_advanced_settings_page() {
        $settings = $this->get_current_settings();
        include get_template_directory() . '/inc/templates/backend-settings-advanced.php';
    }
    
    /**
     * Render security settings page
     */
    public function render_security_settings_page() {
        $settings = $this->get_current_settings();
        include get_template_directory() . '/inc/templates/backend-settings-security.php';
    }
    
    /**
     * Render performance settings page
     */
    public function render_performance_settings_page() {
        $settings = $this->get_current_settings();
        include get_template_directory() . '/inc/templates/backend-settings-performance.php';
    }
}

// Initialize the backend settings manager
new BlackCnote_Backend_Settings_Manager(); 