<?php hyiplab_layout('user/layouts/master'); ?>
<script>
    "use strict"
    function createCountDown(elementId, sec) {
        var tms = sec;
        var x = setInterval(function () {
            var distance = tms * 1000;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            var days = `<span>${days}d</span>`;
            var hours = `<span>${hours}h</span>`;
            var minutes = `<span>${minutes}m</span>`;
            var seconds = `<span>${seconds}s</span>`;
            document.getElementById(elementId).innerHTML = days +' '+ hours + " " + minutes + " " + seconds;
            if (distance < 0) {
                clearInterval(x);
                document.getElementById(elementId).innerHTML = "COMPLETE";
            }
            tms--;
        }, 1000);
    }
</script>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="mb-4">
            <h3 class="mb-2"><?php esc_html_e('Withdraw Funds', HYIPLAB_PLUGIN_NAME); ?></h3>
            <p><?php esc_html_e('The fund will be withdrawal only from Interest Wallet. So make sure that you\'ve sufficient balance to the interest wallet. ', HYIPLAB_PLUGIN_NAME); ?></p>
        </div>
        <div class="text-end mb-4">
            <a href="<?php echo hyiplab_route_link('user.withdraw.history'); ?>" class="btn btn--secondary btn--smd">
                <i class="las la-long-arrow-alt-left"></i> <?php esc_html_e('Withdraw History', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
        <div class="card custom--card <?php if($isHoliday && !get_option('hyiplab_withdrawal_on_holiday', true)) 'countdown-card'; ?>">
            <div class="card-body">
                <?php if($isHoliday && !get_option('hyiplab_withdrawal_on_holiday', true)){ ?>
                    <div class="text-center">
                        <h4 class="mb-3"><?php esc_html_e('Withdrawal request is disable for today. Please wait for next working day.', HYIPLAB_PLUGIN_NAME); ?></h4>
                        <h2 class="text--base mb-3"><?php esc_html_e('Next Working Day', HYIPLAB_PLUGIN_NAME); ?></h2>
                        <div id="counter" class="countdown-wrapper fs-3 fw-bold text--secondary"></div>
                        <script>createCountDown('counter', <?php echo \Carbon\Carbon::parse($nextWorkingDay)->diffInSeconds(); ?>);</script>
                    </div>
                <?php }else{ ?>
                <form action="<?php echo hyiplab_route_link('user.withdraw.insert'); ?>" method="post">
                    <?php hyiplab_nonce_field('user.withdraw.insert'); ?>
                    <div class="form-group">
                        <label class="form-label"><?php esc_html_e('Method', HYIPLAB_PLUGIN_NAME); ?></label>
                        <select class="form-select form-control form--control" name="method_code" required>
                            <option value=""><?php esc_html_e('Select Gateway', HYIPLAB_PLUGIN_NAME); ?></option>
                            <?php dump($methods); foreach ($methods as $data) {
                                $data->description = '';
                            ?>
                                <option value="<?php echo esc_attr($data->id); ?>" data-resource='<?php echo wp_json_encode($data); ?>'>
                                    <?php echo esc_html($data->name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                        <div class="input-group">
                            <input type="number" step="any" name="amount" class="form-control form--control" required>
                            <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                        </div>
                    </div>

                    <div class="mt-3 preview-details d-none">
                        <ul class="list-group text-center">
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php esc_html_e('Limit', HYIPLAB_PLUGIN_NAME); ?></span>
                                <span><span class="min fw-bold">0</span> <?php echo hyiplab_currency('text'); ?> - <span class="max fw-bold">0</span> <?php echo hyiplab_currency('text'); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?></span>
                                <span><span class="charge fw-bold">0</span> <?php echo hyiplab_currency('text'); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php esc_html_e('Receivable', HYIPLAB_PLUGIN_NAME); ?></span> <span><span class="receivable fw-bold"> 0</span> <?php echo hyiplab_currency('text'); ?> </span>
                            </li>
                            <li class="list-group-item d-none justify-content-between rate-element">

                            </li>
                            <li class="list-group-item d-none justify-content-between in-site-cur">
                                <span><?php esc_html_e('In', HYIPLAB_PLUGIN_NAME); ?> <span class="base-currency"></span></span>
                                <strong class="final_amo">0</strong>
                            </li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn--base w-100 mt-3"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </form>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        $('select[name=method_code]').on('change', function() {
            if (!$('select[name=method_code]').val()) {
                $('.preview-details').addClass('d-none');
                return false;
            }
            var resource = $('select[name=method_code] option:selected').data('resource');
            var fixed_charge = parseFloat(resource.fixed_charge);
            var percent_charge = parseFloat(resource.percent_charge);
            var rate = parseFloat(resource.rate)
            var toFixedDigit = 2;
            $('.min').text(parseFloat(resource.min_limit).toFixed(2));
            $('.max').text(parseFloat(resource.max_limit).toFixed(2));
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
            if (resource.currency != '<?php echo hyiplab_currency('text'); ?>') {
                var rateElement = `<span><?php esc_html_e('Conversion Rate', HYIPLAB_PLUGIN_NAME) ?></span> <span class="fw-bold">1 <?php echo hyiplab_currency('text'); ?> = <span class="rate">${rate}</span>  <span class="base-currency">${resource.currency}</span></span>`;
                $('.rate-element').html(rateElement);
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
            var receivable = parseFloat((parseFloat(amount) - parseFloat(charge))).toFixed(2);
            $('.receivable').text(receivable);
            var final_amo = parseFloat(parseFloat(receivable) * rate).toFixed(toFixedDigit);
            $('.final_amo').text(final_amo);
            $('.base-currency').text(resource.currency);
            $('.method_currency').text(resource.currency);
            $('input[name=amount]').on('input');
        });
        $('input[name=amount]').on('input', function() {
            var data = $('select[name=method_code]').change();
            $('.amount').text(parseFloat($(this).val()).toFixed(2));
        });
    })
</script>
