<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="text-center"><?php esc_html_e('Stripe Payment', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <div class="card-body">
                <div class="card-wrapper mb-3"></div>
                <form role="form" id="payment-form" method="<?php echo esc_attr($data->method) ?>" action="<?php echo esc_url($data->url) ?>">
                    <?php hyiplab_nonce_field('ipn.stripe'); ?>
                    <input type="hidden" value="<?php echo esc_attr($data->track); ?>" name="track">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label"><?php esc_html_e('Name on Card', HYIPLAB_PLUGIN_NAME); ?></label>
                            <div class="input-group">
                                <input type="text" class="form-control form--control" name="name" required autocomplete="off" autofocus />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php esc_html_e('Card Number', HYIPLAB_PLUGIN_NAME); ?></label>
                            <div class="input-group">
                                <input type="tel" class="form-control form--control" name="cardNumber" autocomplete="off" required autofocus />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label class="form-label"><?php esc_html_e('Expiration Date', HYIPLAB_PLUGIN_NAME); ?></label>
                            <input type="tel" class="form-control form--control" name="cardExpiry" autocomplete="off" required />
                        </div>
                        <div class="col-md-6 ">
                            <label class="form-label"><?php esc_html_e('CVC Code', HYIPLAB_PLUGIN_NAME); ?></label>
                            <input type="tel" class="form-control form--control" name="cardCVC" autocomplete="off" required />
                        </div>
                    </div>
                    <br>
                    <button class="btn btn--base w-100" type="submit"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
wp_enqueue_script('card', hyiplab_asset('public/js/card.js'), array('jquery'), null, true);
wp_enqueue_script('card-init', hyiplab_asset('public/js/card-init.js'), array('jquery'), null, true);
?>