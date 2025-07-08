<?php
/**
 * BlackCnote Main Settings Template
 * Displays general site settings.
 * @var array $settings
 */
?>
<div class="wrap">
    <h1><?php esc_html_e('BlackCnote General Settings', 'blackcnote'); ?></h1>
    <form method="post" id="blackcnote-main-settings-form">
        <?php wp_nonce_field('blackcnote_backend_nonce', 'blackcnote_backend_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="blackcnote_site_title"><?php esc_html_e('Site Title', 'blackcnote'); ?></label></th>
                <td><input type="text" id="blackcnote_site_title" name="settings[blackcnote_site_title]" value="<?php echo esc_attr($settings['blackcnote_site_title'] ?? ''); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="blackcnote_site_description"><?php esc_html_e('Site Description', 'blackcnote'); ?></label></th>
                <td><textarea id="blackcnote_site_description" name="settings[blackcnote_site_description]" class="large-text"><?php echo esc_textarea($settings['blackcnote_site_description'] ?? ''); ?></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="blackcnote_logo_url"><?php esc_html_e('Logo URL', 'blackcnote'); ?></label></th>
                <td><input type="url" id="blackcnote_logo_url" name="settings[blackcnote_logo_url]" value="<?php echo esc_url($settings['blackcnote_logo_url'] ?? ''); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="blackcnote_favicon_url"><?php esc_html_e('Favicon URL', 'blackcnote'); ?></label></th>
                <td><input type="url" id="blackcnote_favicon_url" name="settings[blackcnote_favicon_url]" value="<?php echo esc_url($settings['blackcnote_favicon_url'] ?? ''); ?>" class="regular-text" /></td>
            </tr>
        </table>
        <p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e('Save Changes', 'blackcnote'); ?></button></p>
    </form>
</div> 