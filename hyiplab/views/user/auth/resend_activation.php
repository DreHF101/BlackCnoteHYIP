<form class="account-form" action="<?php echo esc_url(home_url('/register/?action=resend')); ?>" method="post">

    <div class="mb-4">
        <h4 class="mb-2"><?php esc_html_e('Resend Email', HYIPLAB_PLUGIN_NAME); ?></h4>
        <p><?php esc_html_e('Check your junk/spam folder if you did not receive the activation email', HYIPLAB_PLUGIN_NAME); ?></p>
    </div>

    <div class="form-group">
        <label class="form-label" for="user_email"><?php esc_html_e('Email or Username', HYIPLAB_PLUGIN_NAME); ?></label>
        <input class="form-control form--control" type="text" name="user_email" id="user_email" value="" required>
    </div>
    <input type="hidden" name="action" value="resend" />
    <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('register'); ?>" />
    <input type="submit" class="btn btn--base w-100" value="<?php esc_html_e('Resend', HYIPLAB_PLUGIN_NAME); ?>" />

    <div class="col-12 mt-4">
        <p class="text-center"><?php esc_html_e('Already have an account?', HYIPLAB_PLUGIN_NAME); ?>
            <a href="<?php echo esc_url(home_url('login?redirect_to=/')); ?>" class="fw-bold text-primary"><?php esc_html_e('Login Now', HYIPLAB_PLUGIN_NAME); ?></a>
        </p>
    </div>
</form>