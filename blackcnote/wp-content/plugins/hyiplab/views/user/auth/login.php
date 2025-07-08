<?php
define("DONOTCACHEPAGE", true);
if (isset($_GET['redirect_to']) && $_GET['redirect_to']) {
    $redirect = $_GET['redirect_to'];
} else {
    $redirect = esc_url(home_url());
}
hyiplab_layout('user/layouts/auth')
?>

<form class="account-form verify-gcaptcha" name="loginform" id="loginform" action="<?php echo site_url('wp-login.php', 'login_post'); ?>" method="post">

    <div class="mb-4">
        <h4 class="mb-2"><?php esc_html_e('Login to your account', HYIPLAB_PLUGIN_NAME); ?></h4>
        <p><?php esc_html_e('You can sign in to your account using email or username', HYIPLAB_PLUGIN_NAME); ?></p>
    </div>

    <div class="row">

        <div class="col-12">
            <div class="form-group">
                <label class="form-label" for="log"><?php esc_html_e('Username or Email', HYIPLAB_PLUGIN_NAME); ?></label>
                <input class="form-control form--control" type="text" name="log" id="log" value="" required />
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                <label class="form-label" for="pwd"><?php esc_html_e('Password', HYIPLAB_PLUGIN_NAME); ?></label>
                <input class="form-control form--control" type="password" name="pwd" id="pwd" value="" required />
            </div>
        </div>

        <?php hyiplab_include('partials/captcha') ?>

        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 justify-content-between">
                <div class="form-group custom--checkbox">
                    <input type="checkbox" name="rememberme" id="rememberme" value="forever" class="form-check-input">
                    <label for="rememberme"><?php esc_html_e('Keep me Logged in', HYIPLAB_PLUGIN_NAME); ?></label>
                </div>
                <a href="<?php echo esc_url(home_url('/forgot')); ?>" class="text-primary fw-bold"><?php esc_html_e('Forgot Password?', HYIPLAB_PLUGIN_NAME); ?></a>
            </div>
        </div>
        <div class="col-12">
            <input type="submit" class="btn btn--base w-100" name="wp-submit" id="wp-submit" value="<?php esc_html_e('Login', HYIPLAB_PLUGIN_NAME); ?>" />
        </div>
    </div>

    <input type="hidden" name="redirect_to" id="redirect_to" value="<?php echo esc_attr($redirect); ?>" />
    <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('login'); ?> " />
    <input type="hidden" name="formname" id="formname" value="loginform" />

    <div class="col-12 mt-4">
        <p class="text-center"><?php esc_html_e('Don\'t have any account?', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo esc_url(hyiplab_route_link('user.register')); ?>" class="fw-bold text-primary"><?php esc_html_e('Create Account', HYIPLAB_PLUGIN_NAME); ?></a></p>
    </div>

    <p class="text-center mt-3">
        <?php esc_html_e('Activation account', HYIPLAB_PLUGIN_NAME); ?> <a class="fw-bold text-primary" href="<?php echo esc_url(hyiplab_route_link('user.register')); ?>?action=resend">
            <?php esc_html_e('Resend Email', HYIPLAB_PLUGIN_NAME); ?>
        </a>
    </p>

</form>