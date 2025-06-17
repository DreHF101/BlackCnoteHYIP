<?php

$customCaptcha = hyiplab_custom_captcha();
$googleCaptcha = hyiplab_re_captcha()

?>

<?php if ($googleCaptcha) : ?>
    <div class="mb-3">
        <?php echo  $googleCaptcha ?>
    </div>
<?php endif ?>

<?php if ($customCaptcha) : ?>
    <div class="form-group">
        <div class="mb-2">
            <?php echo  $customCaptcha ?>
        </div>
        <label class="form-label"><?php esc_html_e('Captcha', HYIPLAB_PLUGIN_NAME); ?></label>
        <input type="text" name="captcha" class="form-control form--control" required>
    </div>
<?php endif ?>

<script>
    jQuery(document).ready(function($) {
        "use strict"
        $('.verify-gcaptcha').on('submit', function() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span class="text-danger">Captcha field is required.</span>';
                return false;
            }
            return true;
        });
    });
</script>