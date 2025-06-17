<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="text-center"><?php esc_html_e('Voguepay', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <div class="card-body p-5">
                <ul class="list-group text-center">
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('You have to pay', HYIPLAB_PLUGIN_NAME); ?>
                        <strong><?php echo hyiplab_show_amount($deposit->final_amo) ?> <?php echo esc_html($deposit->method_currency) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('You will get', HYIPLAB_PLUGIN_NAME); ?>
                        <strong><?php echo hyiplab_show_amount($deposit->amount) ?> <?php echo hyiplab_currency('text') ?></strong>
                    </li>
                </ul>
                <button type="button" class="btn btn--base w-100 mt-3" id="btn-confirm"><?php esc_html_e('Pay Now', HYIPLAB_PLUGIN_NAME); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        var closedFunction = function() {}
        var successFunction = function(transaction_id) {
            window.location.href = '<?php echo hyiplab_route_link('ipn.voguepay') ?>';
        }
        var failedFunction = function(transaction_id) {
            window.location.href = '<?php echo hyiplab_route_link('user.deposit.index') ?>';
        }

        function pay(item, price) {
            //Initiate voguepay inline payment
            Voguepay.init({
                v_merchant_id: "<?php echo esc_html($data->v_merchant_id) ?>",
                total: price,
                notify_url: "<?php echo esc_url($data->notify_url) ?>",
                cur: "<?php echo esc_html($data->cur) ?>",
                merchant_ref: "<?php echo esc_html($data->merchant_ref) ?>",
                memo: "<?php echo esc_html($data->memo) ?>",
                recurrent: true,
                frequency: 10,
                developer_code: '60a4ecd9bbc77',
                custom: "<?php echo esc_html($data->custom) ?>",
                customer: {
                    name: '<?php esc_html_e('Customer name', HYIPLAB_PLUGIN_NAME) ?>',
                    country: '<?php esc_html_e('text', HYIPLAB_PLUGIN_NAME); ?>',
                    address: '<?php esc_html_e('Customer address', HYIPLAB_PLUGIN_NAME); ?>',
                    city: '<?php esc_html_e('Customer city', HYIPLAB_PLUGIN_NAME); ?>',
                    state: '<?php esc_html_e('Customer state', HYIPLAB_PLUGIN_NAME); ?>',
                    zipcode: '<?php esc_html_e('Customer zip/post code', HYIPLAB_PLUGIN_NAME); ?>',
                    email: '<?php esc_html_e('example@example.com', HYIPLAB_PLUGIN_NAME); ?>',
                    phone: '<?php esc_html_e('Customer phone', HYIPLAB_PLUGIN_NAME); ?>'
                },
                closed: closedFunction,
                success: successFunction,
                failed: failedFunction
            });
        }
        $('#btn-confirm').on('click', function(e) {
            e.preventDefault();
            pay('<?php esc_html_e('Buy', HYIPLAB_PLUGIN_NAME); ?>', <?php echo esc_html($data->Buy) ?>);
        });
    });
</script>

<?php
wp_enqueue_script('vougepay', '//pay.voguepay.com/js/voguepay.js', array('jquery'), null, true);
?>