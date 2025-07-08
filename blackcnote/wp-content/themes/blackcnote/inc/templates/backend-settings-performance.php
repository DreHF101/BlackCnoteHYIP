<?php
/**
 * BlackCnote Performance Settings Template
 * Displays performance settings.
 * @var array $settings
 */
?>
<div class="wrap">
    <h1><?php esc_html_e('BlackCnote Performance Settings', 'blackcnote'); ?></h1>
    <form method="post" id="blackcnote-performance-settings-form">
        <?php wp_nonce_field('blackcnote_backend_nonce', 'blackcnote_backend_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Enable Caching', 'blackcnote'); ?></th>
                <td><input type="checkbox" id="blackcnote_cache_enabled" name="settings[blackcnote_cache_enabled]" value="1" <?php checked($settings['blackcnote_cache_enabled'] ?? true); ?> />
                <label for="blackcnote_cache_enabled"><?php esc_html_e('Enable caching for better performance', 'blackcnote'); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Minify CSS', 'blackcnote'); ?></th>
                <td><input type="checkbox" id="blackcnote_minify_css" name="settings[blackcnote_minify_css]" value="1" <?php checked($settings['blackcnote_minify_css'] ?? true); ?> />
                <label for="blackcnote_minify_css"><?php esc_html_e('Minify CSS files', 'blackcnote'); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Minify JavaScript', 'blackcnote'); ?></th>
                <td><input type="checkbox" id="blackcnote_minify_js" name="settings[blackcnote_minify_js]" value="1" <?php checked($settings['blackcnote_minify_js'] ?? true); ?> />
                <label for="blackcnote_minify_js"><?php esc_html_e('Minify JavaScript files', 'blackcnote'); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Enable Lazy Loading', 'blackcnote'); ?></th>
                <td><input type="checkbox" id="blackcnote_lazy_loading" name="settings[blackcnote_lazy_loading]" value="1" <?php checked($settings['blackcnote_lazy_loading'] ?? true); ?> />
                <label for="blackcnote_lazy_loading"><?php esc_html_e('Enable lazy loading for images and assets', 'blackcnote'); ?></label></td>
            </tr>
        </table>
        <p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e('Save Changes', 'blackcnote'); ?></button></p>
    </form>
</div> 