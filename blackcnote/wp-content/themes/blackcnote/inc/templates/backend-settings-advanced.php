<?php
/**
 * BlackCnote Advanced Settings Template
 * Displays advanced and appearance settings.
 * @var array $settings
 */
?>
<div class="wrap">
    <h1><?php esc_html_e('BlackCnote Advanced Settings', 'blackcnote'); ?></h1>
    <form method="post" id="blackcnote-advanced-settings-form">
        <?php wp_nonce_field('blackcnote_backend_nonce', 'blackcnote_backend_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="blackcnote_primary_color"><?php esc_html_e('Primary Color', 'blackcnote'); ?></label></th>
                <td><input type="color" id="blackcnote_primary_color" name="settings[blackcnote_primary_color]" value="<?php echo esc_attr($settings['blackcnote_primary_color'] ?? '#007cba'); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="blackcnote_secondary_color"><?php esc_html_e('Secondary Color', 'blackcnote'); ?></label></th>
                <td><input type="color" id="blackcnote_secondary_color" name="settings[blackcnote_secondary_color]" value="<?php echo esc_attr($settings['blackcnote_secondary_color'] ?? '#6c757d'); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="blackcnote_accent_color"><?php esc_html_e('Accent Color', 'blackcnote'); ?></label></th>
                <td><input type="color" id="blackcnote_accent_color" name="settings[blackcnote_accent_color]" value="<?php echo esc_attr($settings['blackcnote_accent_color'] ?? '#28a745'); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Enable Live Editing', 'blackcnote'); ?></th>
                <td><input type="checkbox" id="blackcnote_live_editing_enabled" name="settings[blackcnote_live_editing_enabled]" value="1" <?php checked($settings['blackcnote_live_editing_enabled'] ?? true); ?> />
                <label for="blackcnote_live_editing_enabled"><?php esc_html_e('Enable real-time live editing features', 'blackcnote'); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Enable React Integration', 'blackcnote'); ?></th>
                <td><input type="checkbox" id="blackcnote_react_integration_enabled" name="settings[blackcnote_react_integration_enabled]" value="1" <?php checked($settings['blackcnote_react_integration_enabled'] ?? true); ?> />
                <label for="blackcnote_react_integration_enabled"><?php esc_html_e('Enable React app integration', 'blackcnote'); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Enable Debug Mode', 'blackcnote'); ?></th>
                <td><input type="checkbox" id="blackcnote_debug_mode_enabled" name="settings[blackcnote_debug_mode_enabled]" value="1" <?php checked($settings['blackcnote_debug_mode_enabled'] ?? false); ?> />
                <label for="blackcnote_debug_mode_enabled"><?php esc_html_e('Enable debug mode for troubleshooting', 'blackcnote'); ?></label></td>
            </tr>
        </table>
        <p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e('Save Changes', 'blackcnote'); ?></button></p>
    </form>
</div> 