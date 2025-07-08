<?php
/**
 * Admin Functions for BlackCnote Theme
 * Handles all admin-related functionality
 *
 * @package BlackCnote
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize admin functionality
 */
function blackcnote_admin_init(): void {
    // Add admin menu
    add_action('admin_menu', 'blackcnote_admin_menu');
    
    // Add admin scripts and styles
    add_action('admin_enqueue_scripts', 'blackcnote_admin_enqueue_scripts');
    
    // Add admin AJAX handlers
    add_action('wp_ajax_blackcnote_save_settings', 'blackcnote_save_settings_ajax');
    add_action('wp_ajax_blackcnote_test_connection', 'blackcnote_test_connection_ajax');
    add_action('wp_ajax_blackcnote_export_data', 'blackcnote_export_data_ajax');
    add_action('wp_ajax_blackcnote_import_data', 'blackcnote_import_data_ajax');
    
    // Add admin notices
    add_action('admin_notices', 'blackcnote_admin_notices');
    
    // Add admin footer text
    add_filter('admin_footer_text', 'blackcnote_admin_footer_text');
}

/**
 * Add admin menu pages
 */
function blackcnote_admin_menu(): void {
    // Main settings page
    add_menu_page(
        'BlackCnote Settings',
        'BlackCnote',
        'manage_options',
        'blackcnote-settings',
        'blackcnote_settings_page',
        'dashicons-chart-line',
        30
    );
    
    // Settings submenu
    add_submenu_page(
        'blackcnote-settings',
        'General Settings',
        'General Settings',
        'manage_options',
        'blackcnote-settings',
        'blackcnote_settings_page'
    );
    
    // Live Editing submenu
    add_submenu_page(
        'blackcnote-settings',
        'Live Editing',
        'Live Editing',
        'manage_options',
        'blackcnote-live-editing',
        'blackcnote_live_editing_page'
    );
    
    // Development Tools submenu
    add_submenu_page(
        'blackcnote-settings',
        'Development Tools',
        'Dev Tools',
        'manage_options',
        'blackcnote-dev-tools',
        'blackcnote_dev_tools_page'
    );
    
    // System Status submenu
    add_submenu_page(
        'blackcnote-settings',
        'System Status',
        'System Status',
        'manage_options',
        'blackcnote-system-status',
        'blackcnote_system_status_page'
    );
}

/**
 * Enqueue admin scripts and styles
 */
function blackcnote_admin_enqueue_scripts($hook): void {
    // Only load on BlackCnote admin pages
    if (strpos($hook, 'blackcnote') === false) {
        return;
    }
    
    // Admin CSS
    wp_enqueue_style(
        'blackcnote-admin',
        get_template_directory_uri() . '/admin/admin.css',
        [],
        BLACKCNOTE_VERSION
    );
    
    // Admin JavaScript
    wp_enqueue_script(
        'blackcnote-admin',
        get_template_directory_uri() . '/admin/admin.js',
        ['jquery'],
        BLACKCNOTE_VERSION,
        true
    );
    
    // Localize script
    wp_localize_script('blackcnote-admin', 'blackcnoteAdmin', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('blackcnote_admin_nonce'),
        'strings' => [
            'saving' => __('Saving...', 'blackcnote'),
            'saved' => __('Settings saved!', 'blackcnote'),
            'error' => __('Error occurred', 'blackcnote'),
            'confirmDelete' => __('Are you sure you want to delete this?', 'blackcnote'),
            'confirmReset' => __('Are you sure you want to reset all settings?', 'blackcnote')
        ]
    ]);
}

/**
 * Main settings page
 */
function blackcnote_settings_page(): void {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Save settings if form submitted
    if (isset($_POST['blackcnote_save_settings']) && wp_verify_nonce($_POST['blackcnote_nonce'], 'blackcnote_settings')) {
        blackcnote_save_settings($_POST);
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }
    
    // Get current settings
    $settings = blackcnote_get_settings();
    
    ?>
    <div class="wrap blackcnote-admin">
        <h1>BlackCnote Settings</h1>
        
        <div class="blackcnote-admin-tabs">
            <nav class="nav-tab-wrapper">
                <a href="#general" class="nav-tab nav-tab-active">General Settings</a>
                <a href="#investment" class="nav-tab">Investment Settings</a>
                <a href="#security" class="nav-tab">Security Settings</a>
                <a href="#notifications" class="nav-tab">Notifications</a>
                <a href="#advanced" class="nav-tab">Advanced</a>
            </nav>
            
            <form method="post" action="">
                <?php wp_nonce_field('blackcnote_settings', 'blackcnote_nonce'); ?>
                
                <!-- General Settings Tab -->
                <div id="general" class="tab-content active">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Site Title</th>
                            <td>
                                <input type="text" name="site_title" value="<?php echo esc_attr($settings['site_title'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Site Description</th>
                            <td>
                                <textarea name="site_description" rows="3" class="large-text"><?php echo esc_textarea($settings['site_description'] ?? ''); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Contact Email</th>
                            <td>
                                <input type="email" name="contact_email" value="<?php echo esc_attr($settings['contact_email'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Phone Number</th>
                            <td>
                                <input type="text" name="phone_number" value="<?php echo esc_attr($settings['phone_number'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Investment Settings Tab -->
                <div id="investment" class="tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Minimum Investment</th>
                            <td>
                                <input type="number" name="min_investment" value="<?php echo esc_attr($settings['min_investment'] ?? 100); ?>" class="small-text">
                                <span class="description">Minimum investment amount in USD</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Maximum Investment</th>
                            <td>
                                <input type="number" name="max_investment" value="<?php echo esc_attr($settings['max_investment'] ?? 50000); ?>" class="small-text">
                                <span class="description">Maximum investment amount in USD</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Default Daily Return (%)</th>
                            <td>
                                <input type="number" name="default_daily_return" value="<?php echo esc_attr($settings['default_daily_return'] ?? 1.8); ?>" class="small-text" step="0.1">
                                <span class="description">Default daily return percentage</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Investment Duration (days)</th>
                            <td>
                                <input type="number" name="investment_duration" value="<?php echo esc_attr($settings['investment_duration'] ?? 30); ?>" class="small-text">
                                <span class="description">Default investment duration in days</span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Security Settings Tab -->
                <div id="security" class="tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Two-Factor Authentication</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="enable_2fa" value="1" <?php checked($settings['enable_2fa'] ?? false); ?>>
                                    Enable two-factor authentication for user accounts
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Session Timeout (minutes)</th>
                            <td>
                                <input type="number" name="session_timeout" value="<?php echo esc_attr($settings['session_timeout'] ?? 30); ?>" class="small-text">
                                <span class="description">User session timeout in minutes</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Login Attempts Limit</th>
                            <td>
                                <input type="number" name="login_attempts_limit" value="<?php echo esc_attr($settings['login_attempts_limit'] ?? 5); ?>" class="small-text">
                                <span class="description">Maximum failed login attempts before lockout</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Lockout Duration (minutes)</th>
                            <td>
                                <input type="number" name="lockout_duration" value="<?php echo esc_attr($settings['lockout_duration'] ?? 15); ?>" class="small-text">
                                <span class="description">Account lockout duration in minutes</span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Notifications Tab -->
                <div id="notifications" class="tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Email Notifications</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="email_notifications" value="1" <?php checked($settings['email_notifications'] ?? true); ?>>
                                    Enable email notifications
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">SMS Notifications</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="sms_notifications" value="1" <?php checked($settings['sms_notifications'] ?? false); ?>>
                                    Enable SMS notifications
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Push Notifications</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="push_notifications" value="1" <?php checked($settings['push_notifications'] ?? false); ?>>
                                    Enable push notifications
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Advanced Tab -->
                <div id="advanced" class="tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Debug Mode</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="debug_mode" value="1" <?php checked($settings['debug_mode'] ?? false); ?>>
                                    Enable debug mode
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Cache Duration (seconds)</th>
                            <td>
                                <input type="number" name="cache_duration" value="<?php echo esc_attr($settings['cache_duration'] ?? 3600); ?>" class="small-text">
                                <span class="description">Cache duration in seconds</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">API Rate Limit</th>
                            <td>
                                <input type="number" name="api_rate_limit" value="<?php echo esc_attr($settings['api_rate_limit'] ?? 100); ?>" class="small-text">
                                <span class="description">API requests per minute</span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <p class="submit">
                    <input type="submit" name="blackcnote_save_settings" class="button-primary" value="Save Settings">
                    <button type="button" class="button" id="reset-settings">Reset to Defaults</button>
                </p>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Live editing page
 */
function blackcnote_live_editing_page(): void {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <div class="wrap blackcnote-admin">
        <h1>Live Editing Settings</h1>
        
        <div class="blackcnote-live-editing">
            <div class="live-editing-status">
                <h3>Live Editing Status</h3>
                <div class="status-indicators">
                    <div class="status-item">
                        <span class="status-label">WordPress API:</span>
                        <span class="status-value" id="wp-api-status">Checking...</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">React Service:</span>
                        <span class="status-value" id="react-status">Checking...</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">File Watching:</span>
                        <span class="status-value" id="file-watch-status">Checking...</span>
                    </div>
                </div>
            </div>
            
            <div class="live-editing-controls">
                <h3>Live Editing Controls</h3>
                <button class="button" id="start-live-editing">Start Live Editing</button>
                <button class="button" id="stop-live-editing">Stop Live Editing</button>
                <button class="button" id="test-connection">Test Connection</button>
            </div>
            
            <div class="live-editing-logs">
                <h3>Live Editing Logs</h3>
                <div id="live-editing-logs" class="log-container">
                    <p>No logs available</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Development tools page
 */
function blackcnote_dev_tools_page(): void {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <div class="wrap blackcnote-admin">
        <h1>Development Tools</h1>
        
        <div class="blackcnote-dev-tools">
            <div class="tool-section">
                <h3>Database Tools</h3>
                <button class="button" id="backup-database">Backup Database</button>
                <button class="button" id="optimize-database">Optimize Database</button>
                <button class="button" id="repair-database">Repair Database</button>
            </div>
            
            <div class="tool-section">
                <h3>Cache Management</h3>
                <button class="button" id="clear-cache">Clear All Cache</button>
                <button class="button" id="clear-transients">Clear Transients</button>
                <button class="button" id="clear-object-cache">Clear Object Cache</button>
            </div>
            
            <div class="tool-section">
                <h3>File Management</h3>
                <button class="button" id="regenerate-thumbnails">Regenerate Thumbnails</button>
                <button class="button" id="clean-uploads">Clean Uploads</button>
                <button class="button" id="export-theme">Export Theme</button>
            </div>
            
            <div class="tool-section">
                <h3>System Information</h3>
                <div id="system-info" class="system-info">
                    <p>Loading system information...</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * System status page
 */
function blackcnote_system_status_page(): void {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $system_info = blackcnote_get_system_info();
    
    ?>
    <div class="wrap blackcnote-admin">
        <h1>System Status</h1>
        
        <div class="blackcnote-system-status">
            <div class="status-section">
                <h3>WordPress Environment</h3>
                <table class="widefat">
                    <tr>
                        <td>WordPress Version</td>
                        <td><?php echo esc_html($system_info['wp_version']); ?></td>
                    </tr>
                    <tr>
                        <td>PHP Version</td>
                        <td><?php echo esc_html($system_info['php_version']); ?></td>
                    </tr>
                    <tr>
                        <td>MySQL Version</td>
                        <td><?php echo esc_html($system_info['mysql_version']); ?></td>
                    </tr>
                    <tr>
                        <td>Memory Limit</td>
                        <td><?php echo esc_html($system_info['memory_limit']); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="status-section">
                <h3>Server Environment</h3>
                <table class="widefat">
                    <tr>
                        <td>Server Software</td>
                        <td><?php echo esc_html($system_info['server_software']); ?></td>
                    </tr>
                    <tr>
                        <td>Server Protocol</td>
                        <td><?php echo esc_html($system_info['server_protocol']); ?></td>
                    </tr>
                    <tr>
                        <td>Server Name</td>
                        <td><?php echo esc_html($system_info['server_name']); ?></td>
                    </tr>
                    <tr>
                        <td>Document Root</td>
                        <td><?php echo esc_html($system_info['document_root']); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="status-section">
                <h3>BlackCnote Status</h3>
                <table class="widefat">
                    <tr>
                        <td>Theme Version</td>
                        <td><?php echo esc_html($system_info['theme_version']); ?></td>
                    </tr>
                    <tr>
                        <td>Database Size</td>
                        <td><?php echo esc_html($system_info['db_size']); ?></td>
                    </tr>
                    <tr>
                        <td>Upload Directory</td>
                        <td><?php echo esc_html($system_info['upload_dir']); ?></td>
                    </tr>
                    <tr>
                        <td>Cache Directory</td>
                        <td><?php echo esc_html($system_info['cache_dir']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Save settings
 */
function blackcnote_save_settings($data): void {
    $settings = [
        'site_title' => sanitize_text_field($data['site_title'] ?? ''),
        'site_description' => sanitize_textarea_field($data['site_description'] ?? ''),
        'contact_email' => sanitize_email($data['contact_email'] ?? ''),
        'phone_number' => sanitize_text_field($data['phone_number'] ?? ''),
        'min_investment' => intval($data['min_investment'] ?? 100),
        'max_investment' => intval($data['max_investment'] ?? 50000),
        'default_daily_return' => floatval($data['default_daily_return'] ?? 1.8),
        'investment_duration' => intval($data['investment_duration'] ?? 30),
        'enable_2fa' => isset($data['enable_2fa']),
        'session_timeout' => intval($data['session_timeout'] ?? 30),
        'login_attempts_limit' => intval($data['login_attempts_limit'] ?? 5),
        'lockout_duration' => intval($data['lockout_duration'] ?? 15),
        'email_notifications' => isset($data['email_notifications']),
        'sms_notifications' => isset($data['sms_notifications']),
        'push_notifications' => isset($data['push_notifications']),
        'debug_mode' => isset($data['debug_mode']),
        'cache_duration' => intval($data['cache_duration'] ?? 3600),
        'api_rate_limit' => intval($data['api_rate_limit'] ?? 100)
    ];
    
    update_option('blackcnote_settings', $settings);
}

/**
 * Get settings
 */
function blackcnote_get_settings(): array {
    $defaults = [
        'site_title' => get_bloginfo('name'),
        'site_description' => get_bloginfo('description'),
        'contact_email' => get_option('admin_email'),
        'phone_number' => '',
        'min_investment' => 100,
        'max_investment' => 50000,
        'default_daily_return' => 1.8,
        'investment_duration' => 30,
        'enable_2fa' => false,
        'session_timeout' => 30,
        'login_attempts_limit' => 5,
        'lockout_duration' => 15,
        'email_notifications' => true,
        'sms_notifications' => false,
        'push_notifications' => false,
        'debug_mode' => false,
        'cache_duration' => 3600,
        'api_rate_limit' => 100
    ];
    
    $saved = get_option('blackcnote_settings', []);
    return wp_parse_args($saved, $defaults);
}

/**
 * Get system information
 */
function blackcnote_get_system_info(): array {
    global $wpdb;
    
    return [
        'wp_version' => get_bloginfo('version'),
        'php_version' => PHP_VERSION,
        'mysql_version' => $wpdb->db_version(),
        'memory_limit' => WP_MEMORY_LIMIT,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'server_protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'theme_version' => BLACKCNOTE_VERSION,
        'db_size' => blackcnote_get_database_size(),
        'upload_dir' => wp_upload_dir()['basedir'],
        'cache_dir' => WP_CONTENT_DIR . '/cache'
    ];
}

/**
 * Get database size
 */
function blackcnote_get_database_size(): string {
    global $wpdb;
    
    $result = $wpdb->get_row("
        SELECT 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
        FROM information_schema.tables 
        WHERE table_schema = DATABASE()
    ");
    
    return $result ? $result->size_mb . ' MB' : 'Unknown';
}

/**
 * AJAX handlers
 */
function blackcnote_save_settings_ajax(): void {
    check_ajax_referer('blackcnote_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $data = $_POST['settings'] ?? [];
    blackcnote_save_settings($data);
    
    wp_send_json_success('Settings saved successfully');
}

function blackcnote_test_connection_ajax(): void {
    check_ajax_referer('blackcnote_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $url = sanitize_url($_POST['url'] ?? '');
    $response = wp_remote_get($url, ['timeout' => 10]);
    
    if (is_wp_error($response)) {
        wp_send_json_error('Connection failed: ' . $response->get_error_message());
    }
    
    wp_send_json_success('Connection successful');
}

function blackcnote_export_data_ajax(): void {
    check_ajax_referer('blackcnote_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $data = [
        'settings' => blackcnote_get_settings(),
        'system_info' => blackcnote_get_system_info(),
        'export_date' => current_time('mysql')
    ];
    
    wp_send_json_success($data);
}

function blackcnote_import_data_ajax(): void {
    check_ajax_referer('blackcnote_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $data = $_POST['data'] ?? [];
    
    if (isset($data['settings'])) {
        update_option('blackcnote_settings', $data['settings']);
    }
    
    wp_send_json_success('Data imported successfully');
}

/**
 * Admin notices
 */
function blackcnote_admin_notices(): void {
    $settings = blackcnote_get_settings();
    
    if ($settings['debug_mode']) {
        echo '<div class="notice notice-warning"><p><strong>Debug Mode:</strong> Debug mode is enabled. Disable it in production.</p></div>';
    }
    
    if (empty($settings['contact_email'])) {
        echo '<div class="notice notice-info"><p><strong>BlackCnote:</strong> Please set your contact email in <a href="' . admin_url('admin.php?page=blackcnote-settings') . '">BlackCnote Settings</a>.</p></div>';
    }
}

/**
 * Admin footer text
 */
function blackcnote_admin_footer_text($text): string {
    if (isset($_GET['page']) && strpos($_GET['page'], 'blackcnote') !== false) {
        return 'Thank you for using <strong>BlackCnote</strong> - Your Investment Platform';
    }
    
    return $text;
}

// Initialize admin functionality
add_action('init', 'blackcnote_admin_init'); 