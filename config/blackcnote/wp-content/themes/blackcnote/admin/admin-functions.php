<?php
/**
 * Admin Functions for BlackCnote Theme
 *
 * @package BlackCnote
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add theme admin menu
 */
function blackcnote_admin_menu() {
    add_theme_page(
        __('BlackCnote Theme Settings', 'blackcnote'),
        __('BlackCnote Settings', 'blackcnote'),
        'manage_options',
        'blackcnote-settings',
        'blackcnote_admin_page'
    );
}
add_action('admin_menu', 'blackcnote_admin_menu');

/**
 * Admin page callback
 */
function blackcnote_admin_page() {
    // Save settings
    if (isset($_POST['submit']) && check_admin_referer('blackcnote_settings', 'blackcnote_nonce')) {
        update_option('blackcnote_theme_color', sanitize_hex_color($_POST['theme_color']));
        update_option('blackcnote_logo_url', esc_url_raw($_POST['logo_url']));
        update_option('blackcnote_footer_text', sanitize_textarea_field($_POST['footer_text']));
        update_option('blackcnote_analytics_code', wp_kses_post($_POST['analytics_code']));
        
        echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully!', 'blackcnote') . '</p></div>';
    }

    $theme_color = get_option('blackcnote_theme_color', '#007cba');
    $logo_url = get_option('blackcnote_logo_url', '');
    $footer_text = get_option('blackcnote_footer_text', '');
    $analytics_code = get_option('blackcnote_analytics_code', '');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('blackcnote_settings', 'blackcnote_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="theme_color"><?php esc_html_e('Theme Color', 'blackcnote'); ?></label>
                    </th>
                    <td>
                        <input type="color" id="theme_color" name="theme_color" value="<?php echo esc_attr($theme_color); ?>" />
                        <p class="description"><?php esc_html_e('Choose the primary color for your theme.', 'blackcnote'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="logo_url"><?php esc_html_e('Logo URL', 'blackcnote'); ?></label>
                    </th>
                    <td>
                        <input type="url" id="logo_url" name="logo_url" value="<?php echo esc_url($logo_url); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter the URL for your logo image.', 'blackcnote'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="footer_text"><?php esc_html_e('Footer Text', 'blackcnote'); ?></label>
                    </th>
                    <td>
                        <textarea id="footer_text" name="footer_text" rows="3" cols="50" class="large-text"><?php echo esc_textarea($footer_text); ?></textarea>
                        <p class="description"><?php esc_html_e('Custom text to display in the footer.', 'blackcnote'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="analytics_code"><?php esc_html_e('Analytics Code', 'blackcnote'); ?></label>
                    </th>
                    <td>
                        <textarea id="analytics_code" name="analytics_code" rows="5" cols="50" class="large-text code"><?php echo esc_textarea($analytics_code); ?></textarea>
                        <p class="description"><?php esc_html_e('Paste your Google Analytics or other tracking code here.', 'blackcnote'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <h2><?php esc_html_e('System Information', 'blackcnote'); ?></h2>
        <table class="widefat">
            <tr>
                <td><strong><?php esc_html_e('Theme Version:', 'blackcnote'); ?></strong></td>
                <td><?php echo esc_html(BLACKCNOTE_VERSION); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('WordPress Version:', 'blackcnote'); ?></strong></td>
                <td><?php echo esc_html(get_bloginfo('version')); ?></td>
            </tr>
            <tr>
                <td><strong><?php esc_html_e('PHP Version:', 'blackcnote'); ?></strong></td>
                <td><?php echo esc_html(PHP_VERSION); ?></td>
            </tr>
        </table>
    </div>
    <?php
} 