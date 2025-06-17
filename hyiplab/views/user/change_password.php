<?php hyiplab_layout('user/layouts/master'); ?>
<div class="mb-4">
    <h3 class="mb-2"><?php esc_html_e('Change Password', HYIPLAB_PLUGIN_NAME); ?></h3>
</div>
<div class="card">
    <div class="card-body">
        <form action="<?php echo hyiplab_route_link('user.change.password.update'); ?>" method="post">
            <?php hyiplab_nonce_field('user.change.password.update'); ?>
            <div class="form-group">
                <label class="form-label" for="current_password"><?php esc_html_e('Current Password', HYIPLAB_PLUGIN_NAME); ?></label>
                <input type="password" class="form-control form--control" name="current_password" autocomplete="current-password" id="current_password" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="password"><?php esc_html_e('Password', HYIPLAB_PLUGIN_NAME); ?></label>
                <input type="password" class="form-control form--control" name="password" autocomplete="current-password" id="password" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="password_confirmation"><?php esc_html_e('Confirm Password', HYIPLAB_PLUGIN_NAME); ?></label>
                <input type="password" class="form-control form--control" name="password_confirmation" autocomplete="current-password" id="password_confirmation" required>
            </div>
            <div class="form-group mt-3">
                <button type="submit" class="btn btn--base w-100"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
            </div>
        </form>
    </div>
</div>