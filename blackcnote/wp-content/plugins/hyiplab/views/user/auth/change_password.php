<form class="account-form" action="<?php echo hyiplab_route_link('user.forget.password'); ?>?action=rp" method="post">
    <div class="mb-4">
        <h4 class="mb-2"><?php esc_html_e('Reset Password', HYIPLAB_PLUGIN_NAME); ?></h4>
        <p><?php esc_html_e('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.', HYIPLAB_PLUGIN_NAME); ?></p>
    </div>
    <div class="form-group">
        <label class="form-label" for="pass1"><?php esc_html_e('New password', HYIPLAB_PLUGIN_NAME) ?></label>
        <input id="pass1" class="form-control form--control" type="password" name="pass1" value="" autocomplete="off" required>
    </div>
    <div class="form-group">
        <label class="form-label" for="pass2"><?php esc_html_e('Confirm new password', HYIPLAB_PLUGIN_NAME) ?></label>
        <input id="pass2" class="form-control form--control" type="password" name="pass2" value="" autocomplete="off" required>
    </div>
    <input type="hidden" name="user_login" id="user_login" value="<?php echo esc_attr( isset($_GET['login']) ? $_GET['login'] : @$_POST['user_login']); ?>" />
    <input type="hidden" name="action" id="action" value="rp" />
    <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('forgot'); ?>" />
    <input id="wp-submit" class="btn btn--base w-100" type="submit" name="wp-submit" value="<?php esc_attr_e('Reset Password', HYIPLAB_PLUGIN_NAME); ?>" />
    <p class="text-center mt-4">
        <?php esc_html_e('Already have an account?', HYIPLAB_PLUGIN_NAME); ?> <a class="fw-bold text-primary" href="<?php echo esc_url( hyiplab_route_link('user.login') );?>?redirect_to=/">
            <?php esc_html_e('Login now', HYIPLAB_PLUGIN_NAME); ?>
        </a>
    </p>
</form>