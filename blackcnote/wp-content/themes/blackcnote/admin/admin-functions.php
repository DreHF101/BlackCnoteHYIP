<?php
/**
 * Admin Functions for BlackCnote Theme
 * Unique admin features not handled by the backend settings manager
 *
 * @package BlackCnote
 * @version 2.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add unique admin menu items (not handled by backend settings manager)
 */
function blackcnote_admin_menu() {
    // Live Editing Management (unique feature)
    add_theme_page(
        __('Live Editing', 'blackcnote'),
        __('Live Editing', 'blackcnote'),
        'manage_options',
        'blackcnote-live-editing',
        'blackcnote_live_editing_page'
    );
    
    // Development Tools (unique feature)
    add_theme_page(
        __('Development Tools', 'blackcnote'),
        __('Dev Tools', 'blackcnote'),
        'manage_options',
        'blackcnote-dev-tools',
        'blackcnote_dev_tools_page'
    );
    
    // System Status (unique feature)
    add_theme_page(
        __('System Status', 'blackcnote'),
        __('System Status', 'blackcnote'),
        'manage_options',
        'blackcnote-system-status',
        'blackcnote_system_status_page'
    );
}
add_action('admin_menu', 'blackcnote_admin_menu');

/**
 * Live Editing Management Page
 */
function blackcnote_live_editing_page() {
    $settings = get_option('blackcnote_theme_settings', array());
    $live_editing_enabled = $settings['blackcnote_live_editing_enabled'] ?? true;
    $react_integration_enabled = $settings['blackcnote_react_integration_enabled'] ?? true;
    
    // Handle live editing toggle
    if (isset($_POST['toggle_live_editing']) && check_admin_referer('blackcnote_live_editing')) {
        $settings['blackcnote_live_editing_enabled'] = !$live_editing_enabled;
        update_option('blackcnote_theme_settings', $settings);
        $live_editing_enabled = $settings['blackcnote_live_editing_enabled'];
        echo '<div class="notice notice-success"><p>' . esc_html__('Live editing setting updated!', 'blackcnote') . '</p></div>';
    }
    
    // Handle React integration toggle
    if (isset($_POST['toggle_react_integration']) && check_admin_referer('blackcnote_live_editing')) {
        $settings['blackcnote_react_integration_enabled'] = !$react_integration_enabled;
        update_option('blackcnote_theme_settings', $settings);
        $react_integration_enabled = $settings['blackcnote_react_integration_enabled'];
        echo '<div class="notice notice-success"><p>' . esc_html__('React integration setting updated!', 'blackcnote') . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Live Editing Management', 'blackcnote'); ?></h1>
        
        <div class="card">
            <h2><?php esc_html_e('Live Editing Status', 'blackcnote'); ?></h2>
            <p><strong><?php esc_html_e('Status:', 'blackcnote'); ?></strong> 
                <?php echo $live_editing_enabled ? 
                    '<span style="color: green;">' . esc_html__('Enabled', 'blackcnote') . '</span>' : 
                    '<span style="color: red;">' . esc_html__('Disabled', 'blackcnote') . '</span>'; ?>
            </p>
            
            <form method="post">
                <?php wp_nonce_field('blackcnote_live_editing'); ?>
                <input type="submit" name="toggle_live_editing" class="button button-primary" 
                       value="<?php echo $live_editing_enabled ? 
                           esc_attr__('Disable Live Editing', 'blackcnote') : 
                           esc_attr__('Enable Live Editing', 'blackcnote'); ?>" />
            </form>
        </div>
        
        <div class="card">
            <h2><?php esc_html_e('React Integration Status', 'blackcnote'); ?></h2>
            <p><strong><?php esc_html_e('Status:', 'blackcnote'); ?></strong> 
                <?php echo $react_integration_enabled ? 
                    '<span style="color: green;">' . esc_html__('Enabled', 'blackcnote') . '</span>' : 
                    '<span style="color: red;">' . esc_html__('Disabled', 'blackcnote') . '</span>'; ?>
            </p>
            
            <form method="post">
                <?php wp_nonce_field('blackcnote_live_editing'); ?>
                <input type="submit" name="toggle_react_integration" class="button button-primary" 
                       value="<?php echo $react_integration_enabled ? 
                           esc_attr__('Disable React Integration', 'blackcnote') : 
                           esc_attr__('Enable React Integration', 'blackcnote'); ?>" />
            </form>
        </div>
        
        <div class="card">
            <h2><?php esc_html_e('Live Editing Information', 'blackcnote'); ?></h2>
            <p><?php esc_html_e('Live editing allows real-time updates to your theme content and styles. When enabled, changes made in the WordPress admin or React app will be reflected immediately across all environments.', 'blackcnote'); ?></p>
            
            <h3><?php esc_html_e('Features:', 'blackcnote'); ?></h3>
            <ul>
                <li><?php esc_html_e('Real-time content synchronization', 'blackcnote'); ?></li>
                <li><?php esc_html_e('Live style updates', 'blackcnote'); ?></li>
                <li><?php esc_html_e('Cross-platform editing', 'blackcnote'); ?></li>
                <li><?php esc_html_e('Automatic save and backup', 'blackcnote'); ?></li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * Development Tools Page
 */
function blackcnote_dev_tools_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Development Tools', 'blackcnote'); ?></h1>
        
        <div class="card">
            <h2><?php esc_html_e('Available Development Tools', 'blackcnote'); ?></h2>
            
            <h3><?php esc_html_e('React Development Server', 'blackcnote'); ?></h3>
            <p><strong><?php esc_html_e('URL:', 'blackcnote'); ?></strong> <a href="http://localhost:5174" target="_blank">http://localhost:5174</a></p>
            <p><?php esc_html_e('Access the React development environment for frontend development and testing.', 'blackcnote'); ?></p>
            
            <h3><?php esc_html_e('Dev Tools Dashboard', 'blackcnote'); ?></h3>
            <p><strong><?php esc_html_e('URL:', 'blackcnote'); ?></strong> <a href="http://localhost:9229" target="_blank">http://localhost:9229</a></p>
            <p><?php esc_html_e('Development tools dashboard for debugging and monitoring.', 'blackcnote'); ?></p>
            
            <h3><?php esc_html_e('Database Management', 'blackcnote'); ?></h3>
            <p><strong><?php esc_html_e('URL:', 'blackcnote'); ?></strong> <a href="http://localhost:8080" target="_blank">http://localhost:8080</a></p>
            <p><?php esc_html_e('phpMyAdmin for database management and queries.', 'blackcnote'); ?></p>
            
            <h3><?php esc_html_e('Cache Management', 'blackcnote'); ?></h3>
            <p><strong><?php esc_html_e('URL:', 'blackcnote'); ?></strong> <a href="http://localhost:8081" target="_blank">http://localhost:8081</a></p>
            <p><?php esc_html_e('Redis Commander for cache management and monitoring.', 'blackcnote'); ?></p>
            
            <h3><?php esc_html_e('Email Testing', 'blackcnote'); ?></h3>
            <p><strong><?php esc_html_e('URL:', 'blackcnote'); ?></strong> <a href="http://localhost:8025" target="_blank">http://localhost:8025</a></p>
            <p><?php esc_html_e('MailHog for email testing and debugging.', 'blackcnote'); ?></p>
        </div>
        
        <div class="card">
            <h2><?php esc_html_e('Development Commands', 'blackcnote'); ?></h2>
            <p><?php esc_html_e('Use these commands in your terminal for development tasks:', 'blackcnote'); ?></p>
            
            <h3><?php esc_html_e('Start Development Environment', 'blackcnote'); ?></h3>
            <code>.\automate-docker-startup.bat</code>
            
            <h3><?php esc_html_e('Stop Development Environment', 'blackcnote'); ?></h3>
            <code>docker compose down</code>
            
            <h3><?php esc_html_e('View Logs', 'blackcnote'); ?></h3>
            <code>docker compose logs -f</code>
            
            <h3><?php esc_html_e('Restart Services', 'blackcnote'); ?></h3>
            <code>docker compose restart</code>
        </div>
    </div>
    <?php
}

/**
 * System Status Page
 */
function blackcnote_system_status_page() {
    $settings = get_option('blackcnote_theme_settings', array());
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('System Status', 'blackcnote'); ?></h1>
        
        <div class="card">
            <h2><?php esc_html_e('WordPress Environment', 'blackcnote'); ?></h2>
            <table class="widefat">
                <tr>
                    <td><strong><?php esc_html_e('WordPress Version:', 'blackcnote'); ?></strong></td>
                    <td><?php echo esc_html(get_bloginfo('version')); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('PHP Version:', 'blackcnote'); ?></strong></td>
                    <td><?php echo esc_html(PHP_VERSION); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('MySQL Version:', 'blackcnote'); ?></strong></td>
                    <td><?php echo esc_html(mysqli_get_server_info(mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('Active Theme:', 'blackcnote'); ?></strong></td>
                    <td><?php echo esc_html(get_template()); ?></td>
                </tr>
            </table>
        </div>
        
        <div class="card">
            <h2><?php esc_html_e('BlackCnote Settings Status', 'blackcnote'); ?></h2>
            <table class="widefat">
                <tr>
                    <td><strong><?php esc_html_e('Live Editing:', 'blackcnote'); ?></strong></td>
                    <td><?php echo ($settings['blackcnote_live_editing_enabled'] ?? true) ? 
                        '<span style="color: green;">' . esc_html__('Enabled', 'blackcnote') . '</span>' : 
                        '<span style="color: red;">' . esc_html__('Disabled', 'blackcnote') . '</span>'; ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('React Integration:', 'blackcnote'); ?></strong></td>
                    <td><?php echo ($settings['blackcnote_react_integration_enabled'] ?? true) ? 
                        '<span style="color: green;">' . esc_html__('Enabled', 'blackcnote') . '</span>' : 
                        '<span style="color: red;">' . esc_html__('Disabled', 'blackcnote') . '</span>'; ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('Debug Mode:', 'blackcnote'); ?></strong></td>
                    <td><?php echo ($settings['blackcnote_debug_mode_enabled'] ?? false) ? 
                        '<span style="color: orange;">' . esc_html__('Enabled', 'blackcnote') . '</span>' : 
                        '<span style="color: green;">' . esc_html__('Disabled', 'blackcnote') . '</span>'; ?></td>
                </tr>
            </table>
        </div>
        
        <div class="card">
            <h2><?php esc_html_e('Service Status', 'blackcnote'); ?></h2>
            <p><?php esc_html_e('Check if all development services are running:', 'blackcnote'); ?></p>
            
            <h3><?php esc_html_e('WordPress:', 'blackcnote'); ?></h3>
            <p><a href="http://localhost:8888" target="_blank">http://localhost:8888</a></p>
            
            <h3><?php esc_html_e('React App:', 'blackcnote'); ?></h3>
            <p><a href="http://localhost:5174" target="_blank">http://localhost:5174</a></p>
            
            <h3><?php esc_html_e('phpMyAdmin:', 'blackcnote'); ?></h3>
            <p><a href="http://localhost:8080" target="_blank">http://localhost:8080</a></p>
            
            <h3><?php esc_html_e('Redis Commander:', 'blackcnote'); ?></h3>
            <p><a href="http://localhost:8081" target="_blank">http://localhost:8081</a></p>
            
            <h3><?php esc_html_e('MailHog:', 'blackcnote'); ?></h3>
            <p><a href="http://localhost:8025" target="_blank">http://localhost:8025</a></p>
            
            <h3><?php esc_html_e('Dev Tools:', 'blackcnote'); ?></h3>
            <p><a href="http://localhost:9229" target="_blank">http://localhost:9229</a></p>
        </div>
    </div>
    <?php
}

/**
 * Enqueue admin scripts and styles
 */
function blackcnote_admin_scripts($hook) {
    // Only load on BlackCnote admin pages
    if (strpos($hook, 'blackcnote') === false) {
        return;
    }
    
    wp_enqueue_style(
        'blackcnote-admin', 
        get_template_directory_uri() . '/admin/admin-styles.css', 
        array(), 
        '2.0'
    );
    
    wp_enqueue_script(
        'blackcnote-admin', 
        get_template_directory_uri() . '/admin/admin-script.js', 
        array('jquery'), 
        '2.0', 
        true
    );
}
add_action('admin_enqueue_scripts', 'blackcnote_admin_scripts');

/**
 * Get theme settings (helper function)
 */
function blackcnote_get_theme_settings() {
    return get_option('blackcnote_theme_settings', array(
        'blackcnote_live_editing_enabled' => true,
        'blackcnote_react_integration_enabled' => true,
        'blackcnote_debug_mode_enabled' => false,
    ));
} 