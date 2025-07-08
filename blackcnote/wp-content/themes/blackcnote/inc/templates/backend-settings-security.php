<?php
/**
 * BlackCnote Security Settings Template
 * Displays security settings.
 * @var array $settings
 */
?>
<div class="wrap">
    <h1><?php esc_html_e('BlackCnote Security Settings', 'blackcnote'); ?></h1>
    <form method="post" id="blackcnote-security-settings-form">
        <?php wp_nonce_field('blackcnote_backend_nonce', 'blackcnote_backend_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Security Level', 'blackcnote'); ?></th>
                <td>
                    <select name="settings[blackcnote_security_level]">
                        <option value="low" <?php selected($settings['blackcnote_security_level'] ?? '', 'low'); ?>><?php esc_html_e('Low', 'blackcnote'); ?></option>
                        <option value="medium" <?php selected($settings['blackcnote_security_level'] ?? '', 'medium'); ?>><?php esc_html_e('Medium', 'blackcnote'); ?></option>
                        <option value="high" <?php selected($settings['blackcnote_security_level'] ?? '', 'high'); ?>><?php esc_html_e('High', 'blackcnote'); ?></option>
                        <option value="maximum" <?php selected($settings['blackcnote_security_level'] ?? '', 'maximum'); ?>><?php esc_html_e('Maximum', 'blackcnote'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Enable Two-Factor Authentication', 'blackcnote'); ?></th>
                <td><input type="checkbox" id="blackcnote_two_factor_enabled" name="settings[blackcnote_two_factor_enabled]" value="1" <?php checked($settings['blackcnote_two_factor_enabled'] ?? false); ?> />
                <label for="blackcnote_two_factor_enabled"><?php esc_html_e('Enable two-factor authentication for admin users', 'blackcnote'); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><label for="blackcnote_session_timeout"><?php esc_html_e('Session Timeout (minutes)', 'blackcnote'); ?></label></th>
                <td><input type="number" id="blackcnote_session_timeout" name="settings[blackcnote_session_timeout]" value="<?php echo esc_attr($settings['blackcnote_session_timeout'] ?? 30); ?>" min="5" max="240" /></td>
            </tr>
        </table>
        <p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e('Save Changes', 'blackcnote'); ?></button></p>
    </form>
</div> 