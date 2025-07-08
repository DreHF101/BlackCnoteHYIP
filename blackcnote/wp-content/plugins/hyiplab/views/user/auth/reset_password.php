<form class="account-form" action="<?php echo hyiplab_route_link('user.forget.password'); ?>" method="post">
    <div class="mb-4">
        <h4 class="mb-2"><?php esc_html_e('Account Recovery', HYIPLAB_PLUGIN_NAME); ?></h4>
        <p><?php esc_html_e('To recover your account please provide your email or username to find your account.', HYIPLAB_PLUGIN_NAME); ?></p>
    </div>
    <div class="form-group">
        <label class="form-label" for="user_login"><?php esc_html_e('Email or Username', HYIPLAB_PLUGIN_NAME); ?></label>
        <input class="form-control form--control" type="text" name="user_login" id="user_login" value="" required>
    </div>
    <input type="hidden" name="action" value="pwreset" />
    <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('forgot'); ?>" />
    <input type="submit" class="btn btn--base w-100" name="wp-submit" id="wp-submit" value="<?php esc_html_e('Get New Password', HYIPLAB_PLUGIN_NAME); ?>" />
    <p class="mt-3"><?php esc_html_e('You will receive a link to create a new password via email.', HYIPLAB_PLUGIN_NAME); ?></p>
    <p class="text-center mt-4">
        <?php esc_html_e('Already have an account?', HYIPLAB_PLUGIN_NAME); ?> <a class="fw-bold text-primary" href="<?php echo esc_url( hyiplab_route_link('user.login') );?>?redirect_to=/">
            <?php esc_html_e('Login now', HYIPLAB_PLUGIN_NAME); ?>
        </a>
    </p>
</form>