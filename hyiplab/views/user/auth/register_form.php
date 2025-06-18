<?php
$reference = hyiplab_session()->get('reference');
?>
<form class="account-form verify-gcaptcha" action="<?php echo esc_url(hyiplab_route_link('user.register')); ?>" method="post">

    <?php hyiplab_nonce_field('user.register'); ?>

    <div class="mb-4">
        <h4 class="mb-2"><?php esc_html_e('Create an Account', HYIPLAB_PLUGIN_NAME); ?></h4>
        <p><?php esc_html_e('You can create account using email or username and the registration is fully free', HYIPLAB_PLUGIN_NAME); ?></p>
    </div>

    <?php if ($reference) { ?>
        <div class="form-group">
            <label class="form-label" for="referral"><?php esc_html_e('Referral Username', HYIPLAB_PLUGIN_NAME); ?></label>
            <input class="form-control form--control" type="text" name="referral" id="referral" value="<?php echo sanitize_user($reference); ?>" readonly>
        </div>
    <?php } ?>

    <div class="form-group">
        <label class="form-label" for="username"><?php esc_html_e('Username', HYIPLAB_PLUGIN_NAME); ?></label>
        <input class="form-control form--control" type="text" name="username" id="username" value="" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="email"><?php esc_html_e('E-Mail Address', HYIPLAB_PLUGIN_NAME); ?></label>
        <input class="form-control form--control" type="email" name="email" id="email" value="" required>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label"><?php esc_html_e('Country', HYIPLAB_PLUGIN_NAME); ?></label>
                <select name="country" class="form--control form-select">
                    <?php foreach ($countries as $key => $country) { ?>
                        <option data-mobile_code="<?php echo esc_attr($country->dial_code); ?>" value="<?php echo esc_attr($country->country); ?>" data-code="<?php echo esc_attr($key); ?>">
                            <?php echo esc_html($country->country); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label"><?php esc_html_e('Mobile', HYIPLAB_PLUGIN_NAME); ?></label>
                <div class="input-group ">
                    <span class="input-group-text mobile-code">
                    </span>
                    <input type="hidden" name="mobile_code">
                    <input type="hidden" name="country_code">
                    <input type="number" name="mobile" value="" class="form-control form--control" required>
                </div>
            </div>
        </div>

    </div>

    <div class="form-group">
        <label class="form-label" for="password"><?php esc_html_e('Password', HYIPLAB_PLUGIN_NAME) ?></label>
        <input class="form-control form--control" type="password" name="password" id="password" size="20" value="" autocomplete="off" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="password_confirmation"><?php esc_html_e('Confirm Password', HYIPLAB_PLUGIN_NAME) ?></label>
        <input class="form-control form--control" type="password" name="password_confirmation" id="password_confirmation" size="20" value="" autocomplete="off" required>
    </div>

    <?php hyiplab_include('partials/captcha') ?>

    <div class="form-group">
        <input class="btn btn--base w-100" type="submit" value="<?php esc_html_e('Register', HYIPLAB_PLUGIN_NAME); ?>">
    </div>

    <p class="text-center mt-4">
        <?php esc_html_e('Already have an account?', HYIPLAB_PLUGIN_NAME); ?> <a class="fw-bold text-primary" href="<?php echo esc_url(hyiplab_route_link('user.login')); ?>?redirect_to=/">
            <?php esc_html_e('Login now', HYIPLAB_PLUGIN_NAME); ?>
        </a>
    </p>

</form>


<script>
    jQuery(document).ready(function($) {
        "use strict";
        <?php if ($mobileCode) { ?>
            $(`option[data-code=<?php echo esc_attr($mobileCode); ?>]`).attr('selected', '');
        <?php } ?>
        $('select[name=country]').on('change', function() {
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
        });
        $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
        $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
        $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
    });
</script>