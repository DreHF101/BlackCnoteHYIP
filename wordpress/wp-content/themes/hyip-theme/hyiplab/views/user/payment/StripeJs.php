<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="text-center"><?php esc_html_e('Stripe Payment', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <div class="card-body p-5">
                <form action="<?php echo esc_url($data->url); ?>" method="<?php echo esc_attr($data->method); ?>">
                    <?php hyiplab_nonce_field('ipn.stripejs'); ?>
                    <ul class="list-group text-center">
                        <li class="list-group-item d-flex justify-content-between">
                            <?php esc_html_e('You have to pay ', HYIPLAB_PLUGIN_NAME); ?>
                            <strong><?php echo hyiplab_show_amount($deposit->final_amo); ?> <?php echo esc_html($deposit->method_currency); ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <?php esc_html_e('You will get ', HYIPLAB_PLUGIN_NAME); ?>
                            <strong><?php echo hyiplab_show_amount($deposit->amount); ?> <?php echo hyiplab_currency('text'); ?></strong>
                        </li>
                    </ul>
                    <script src="<?php echo esc_url($data->src); ?>" class="stripe-button" <?php foreach ($data->val as $key => $value) { ?> data-<?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>" <?php } ?>>
                    </script>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        $('button[type="submit"]').addClass("btn btn--base w-100 mt-3");
        $('button[type="submit"]').text("Pay Now");
        $('button[type="submit"]').removeClass("stripe-button-el");
    })(jQuery);
</script>

<?php wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', array('jquery'), null, true); ?>