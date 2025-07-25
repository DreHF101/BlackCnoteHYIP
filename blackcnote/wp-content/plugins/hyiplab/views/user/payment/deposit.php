<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="mb-4">
            <h3 class="mb-2"><?php esc_html_e('Deposit Funds', HYIPLAB_PLUGIN_NAME); ?></h3>
            <p><?php esc_html_e('Add funds using our system\'s gateway. The deposited amount will be credited to the deposit wallet. You\'ll just make investments from this wallet.', HYIPLAB_PLUGIN_NAME); ?></p>
        </div>
        <div class="text-end mb-3">
            <a href="<?php echo hyiplab_route_link('user.deposit.history'); ?>" class="btn btn--secondary btn--smd">
                <i class="las la-long-arrow-alt-left"></i> <?php esc_html_e('Deposit History', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
        <form action="<?php echo hyiplab_route_link('user.deposit.insert'); ?>" method="post">
            <?php hyiplab_nonce_field('user.deposit.insert'); ?>
            <input type="hidden" name="method_code">
            <input type="hidden" name="currency">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label"><?php esc_html_e('Select Gateway', HYIPLAB_PLUGIN_NAME); ?></label>
                        <select class="form-control form--control form-select" name="gateway" required>
                            <option value=""><?php esc_html_e('Select One', HYIPLAB_PLUGIN_NAME); ?></option>
                            <?php foreach ($gatewayCurrency as $data) {
                                $method = hyiplab_gateway($data->method_code);
                            ?>
                                <option value="<?php echo esc_attr($data->method_code); ?>" data-gateway='<?php echo wp_json_encode($data); ?>' data-method='<?php echo wp_json_encode($method); ?>'><?php echo esc_html($data->name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                        <div class="input-group">
                            <input type="number" step="any" name="amount" class="form-control form--control" value="<?php echo hyiplab_old('amount'); ?>" autocomplete="off" required />
                            <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                        </div>
                    </div>
                    <div class="mt-3 preview-details d-none">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php esc_html_e('Limit', HYIPLAB_PLUGIN_NAME); ?></span>
                                <span><span class="min fw-bold">0</span> <?php echo hyiplab_currency('text'); ?> - <span class="max fw-bold">0</span> <?php echo hyiplab_currency('text'); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?></span>
                                <span><span class="charge fw-bold">0</span> <?php echo hyiplab_currency('text'); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php esc_html_e('Payable', HYIPLAB_PLUGIN_NAME); ?></span> <span><span class="payable fw-bold"> 0</span> <?php echo hyiplab_currency('text'); ?></span>
                            </li>
                            <li class="list-group-item justify-content-between d-none rate-element">

                            </li>
                            <li class="list-group-item justify-content-between d-none in-site-cur">
                                <span><?php esc_html_e('In', HYIPLAB_PLUGIN_NAME); ?> <span class="base-currency"></span></span>
                                <span class="final_amo fw-bold">0</span>
                            </li>
                            <li class="list-group-item justify-content-center crypto_currency d-none">
                                <span><?php esc_html_e('Conversion with', HYIPLAB_PLUGIN_NAME); ?> <span class="method_currency"></span> <?php esc_html_e('and final value will Show on next step', HYIPLAB_PLUGIN_NAME); ?></span>
                            </li>
                        </ul>
                    </div>
                    <button type="submit" class="btn btn--base w-100 mt-3"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        $('select[name=gateway]').on('change', function() {
            if (!$('select[name=gateway]').val()) {
                $('.preview-details').addClass('d-none');
                return false;
            }
            var resource = $('select[name=gateway] option:selected').data('gateway');
            var method = $('select[name=gateway] option:selected').data('method');
            var fixed_charge = parseFloat(resource.fixed_charge);
            var percent_charge = parseFloat(resource.percent_charge);
            var rate = parseFloat(resource.rate)
            if (method.crypto == 1) {
                var toFixedDigit = 8;
                $('.crypto_currency').removeClass('d-none');
            } else {
                var toFixedDigit = 2;
                $('.crypto_currency').addClass('d-none');
            }
            $('.min').text(parseFloat(resource.min_amount).toFixed(2));
            $('.max').text(parseFloat(resource.max_amount).toFixed(2));
            var amount = parseFloat($('input[name=amount]').val());
            if (!amount) {
                amount = 0;
            }
            if (amount <= 0) {
                $('.preview-details').addClass('d-none');
                return false;
            }
            $('.preview-details').removeClass('d-none');
            var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
            $('.charge').text(charge);
            var payable = parseFloat((parseFloat(amount) + parseFloat(charge))).toFixed(2);
            $('.payable').text(payable);
            var final_amo = (parseFloat((parseFloat(amount) + parseFloat(charge))) * rate).toFixed(toFixedDigit);
            $('.final_amo').text(final_amo);
            if (resource.currency != '<?php echo hyiplab_currency('text'); ?>') {
                var rateElement = `<span class="fw-bold"><?php esc_html_e('Conversion Rate', HYIPLAB_PLUGIN_NAME) ?></span> <span><span  class="fw-bold">1 <?php echo hyiplab_currency('text'); ?> = <span class="rate">${rate}</span>  <span class="base-currency">${resource.currency}</span></span></span>`;
                $('.rate-element').html(rateElement)
                $('.rate-element').removeClass('d-none');
                $('.in-site-cur').removeClass('d-none');
                $('.rate-element').addClass('d-flex');
                $('.in-site-cur').addClass('d-flex');
            } else {
                $('.rate-element').html('')
                $('.rate-element').addClass('d-none');
                $('.in-site-cur').addClass('d-none');
                $('.rate-element').removeClass('d-flex');
                $('.in-site-cur').removeClass('d-flex');
            }
            $('.base-currency').text(resource.currency);
            $('.method_currency').text(resource.currency);
            $('input[name=currency]').val(resource.currency);
            $('input[name=method_code]').val(resource.method_code);
            $('input[name=amount]').on('input');
        });
        $('input[name=amount]').on('input', function() {
            $('select[name=gateway]').change();
            $('.amount').text(parseFloat($(this).val()).toFixed(2));
        });
    });
</script>