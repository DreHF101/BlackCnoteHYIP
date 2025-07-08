<?php hyiplab_layout('user/layouts/master');?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="text-center"><?php esc_html_e('Flutterwave Payment', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <div class="card-body p-5">
                <ul class="list-group text-center">
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('You have to pay', HYIPLAB_PLUGIN_NAME); ?>:
                        <strong><?php echo hyiplab_show_amount($data->amount); ?> <?php echo esc_html($data->currency); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('You will get ', HYIPLAB_PLUGIN_NAME); ?>:
                        <strong><?php echo hyiplab_show_amount($data->amount); ?> <?php echo hyiplab_currency('text'); ?></strong>
                    </li>
                </ul>
                <button type="button" class="btn btn--base w-100 flutterClick mt-3" id="btn-confirm"><?php esc_html_e('Pay Now', HYIPLAB_PLUGIN_NAME); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        "use strict"
        var btn = document.querySelector("#btn-confirm");
        btn.setAttribute("type", "button");
        const API_publicKey = "<?php echo esc_html($data->API_publicKey); ?>";
        $('.flutterClick').on('click', function() {
            payWithRave();
        })
        function payWithRave() {
            var x = getpaidSetup({
                PBFPubKey: API_publicKey,
                customer_email: "<?php echo esc_html($data->customer_email); ?>",
                amount: "<?php echo esc_html($data->amount); ?>",
                customer_phone: "<?php echo esc_html($data->customer_phone); ?>",
                currency: "<?php echo esc_html($data->currency); ?>",
                txref: "<?php echo esc_html($data->txref); ?>",
                onclose: function() {},
                callback: function(response) {
                    var txref = response.tx.txRef;
                    var status = response.tx.status;
                    var chargeResponse = response.tx.chargeResponseCode;
                    if (chargeResponse == "00" || chargeResponse == "0") {
                        window.location = '<?php echo hyiplab_route_link('ipn.flutterwave'); ?>?trx=' + txref + '&status=' + status;
                    } else {
                        window.location = '<?php echo hyiplab_route_link('ipn.flutterwave'); ?>?trx=' + txref + '&status=' + status;
                    }
                    // x.close(); // use this to close the modal immediately after payment.
                }
            });
        }
    });
</script>

<?php
wp_enqueue_script('flutterwave', 'https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js', array('jquery'), null, true);
?>