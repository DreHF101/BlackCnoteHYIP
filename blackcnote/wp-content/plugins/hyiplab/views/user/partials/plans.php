<?php foreach ($plans as $plan) { 
    $time = get_hyiplab_time_setting($plan->time_setting_id);
    ?>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="plan-item style--two text-center mw-100 w-100 h-100">
            <div class="plan-item__header">
                <h4 class="mb-1 plan-title"><?php echo esc_html($plan->name) ?></h4>
                <p class="mb-2">
                    <?php if ($plan->lifetime == 0) { ?>
                        <?php esc_html_e('Total', HYIPLAB_PLUGIN_NAME); ?>
                        <?php echo esc_html($plan->interest * $plan->repeat_time) ?><?php echo esc_html($plan->interest_type == 1 ? '%' : ' ' . hyiplab_currency('text')) ?>
                        <?php esc_html_e('ROI', HYIPLAB_PLUGIN_NAME); ?>
                    <?php } else { ?>
                        <?php esc_html_e('Unlimited', HYIPLAB_PLUGIN_NAME); ?>
                    <?php } ?>
                </p>
                <div class="plan-rate">
                    <h3 class="rate">
                        <?php echo esc_html($plan->interest_type != 1 ? hyiplab_currency('symbol') : ''); ?><?php echo hyiplab_show_amount($plan->interest); ?><?php echo esc_html($plan->interest_type == 1 ? '%' : ''); ?>
                    </h3>
                    <p><?php esc_html_e('EVERY', HYIPLAB_PLUGIN_NAME); ?> <?php echo strtoupper($time->name) ?> <?php esc_html_e('FOR', HYIPLAB_PLUGIN_NAME); ?> <?php if ($plan->lifetime == 0) { ?>
                            <?php echo esc_html($plan->repeat_time . ' ' . $time->name) ?>
                        <?php } else { ?>
                            <?php esc_html_e('LIFETIME', HYIPLAB_PLUGIN_NAME); ?>
                        <?php } ?>
                    </p>
                </div>
            </div>
            <div class="plan-item__body my-4">
                <ul class="list list-style-three text-start">
                    <li class="d-flex flex-wrap justify-content-between align-items-center">
                        <span class="label"><?php esc_html_e('Investment', HYIPLAB_PLUGIN_NAME); ?></span>
                        <span class="value">
                            <?php if ($plan->fixed_amount == 0) { ?>
                                <?php echo hyiplab_currency('sym') . hyiplab_show_amount($plan->minimum); ?> -
                                <?php echo hyiplab_currency('sym') . hyiplab_show_amount($plan->maximum); ?>
                            <?php } else { ?>
                                <?php echo hyiplab_currency('sym') . hyiplab_show_amount($plan->fixed_amount); ?>
                            <?php } ?>
                        </span>
                    </li>
                    <li class="d-flex flex-wrap justify-content-between align-items-center">
                        <span class="label"><?php esc_html_e('Max. Earn', HYIPLAB_PLUGIN_NAME); ?></span>
                        <span class="value">
                            <?php
                            if ($plan->fixed_amount == 0) {
                                $investAmo = $plan->maximum;
                            } else {
                                $investAmo = $plan->fixed_amount;
                            }

                            if ($plan->lifetime == 0) {
                                if ($plan->interest_type == 1) {
                                    $interestAmo = (($investAmo * $plan->interest) / 100) * $plan->repeat_time;
                                } else {
                                    $interestAmo = $plan->interest * $plan->repeat_time;
                                }
                            } else {
                                $interestAmo = esc_html__('Unlimited', HYIPLAB_PLUGIN_NAME);
                            }

                            ?>

                            <?php echo esc_html($interestAmo); ?>
                            <?php if ($plan->lifetime == 0)
                                echo hyiplab_currency('text');
                            ?>
                        </span>
                    </li>
                    <li class="d-flex flex-wrap justify-content-between align-items-center">
                        <span class="label"><?php esc_html_e('Total Return', HYIPLAB_PLUGIN_NAME); ?></span>
                        <span class="value">
                            <?php if ($plan->lifetime == 0) { ?>
                                <?php if ($plan->capital_back == 1) { ?>
                                    <?php esc_html_e('capital +', HYIPLAB_PLUGIN_NAME); ?>
                                <?php } ?>
                                <?php echo esc_html($plan->interest * $plan->repeat_time); ?> <?php echo esc_html($plan->interest_type == 1 ? '%' : ' ' . hyiplab_currency('text')); ?>
                            <?php } else { ?>
                                <?php esc_html_e('Unlimited', HYIPLAB_PLUGIN_NAME); ?>
                            <?php } ?>
                        </span>
                    </li>
                    <?php if ($plan->compound_interest) : ?>
                        <li class="d-flex flex-wrap justify-content-between align-items-center">
                            <span class="label"><?php esc_html_e('Compound interest', HYIPLAB_PLUGIN_NAME); ?></span>
                            <span class="value"><?php echo esc_html__('available', HYIPLAB_PLUGIN_NAME); ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if ($plan->hold_capital) : ?>
                        <li class="d-flex flex-wrap justify-content-between align-items-center">
                            <span class="label"><?php esc_html_e('Hold Capital & Reinvest', HYIPLAB_PLUGIN_NAME); ?></span>
                            <span class="value"><?php echo esc_html__('Yes', HYIPLAB_PLUGIN_NAME); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <button class="cmn--btn plan-btn btn investModal" data-bs-toggle="modal" data-plan_id="<?php echo esc_attr($plan->id); ?>" data-plan_name="<?php echo esc_attr($plan->name); ?>" data-fixed_amount="<?php echo esc_attr(hyiplab_get_amount($plan->fixed_amount)); ?>" data-minimum="<?php echo esc_attr(hyiplab_get_amount($plan->minimum)); ?>" data-maximum="<?php echo esc_attr(hyiplab_get_amount($plan->maximum)); ?>" data-time_name="<?php echo esc_attr($time->name); ?>" data-interest="<?php echo esc_attr(hyiplab_get_amount($plan->interest)); ?>" data-interest_type="<?php echo esc_attr($plan->interest_type); ?>" data-lifetime="<?php echo esc_attr($plan->lifetime); ?>" data-repeat_time="<?php echo esc_attr($plan->repeat_time); ?>" data-compound_interest="<?php echo esc_attr($plan->compound_interest); ?>" data-bs-target="#investModal" type="button"><?php esc_html_e('Invest Now', HYIPLAB_PLUGIN_NAME); ?></button>
        </div>
    </div>
<?php } ?>

<div class="modal fade" id="investModal">
    <div class="modal-dialog modal-content-bg modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php if (hyiplab_auth()) { ?>
                        <?php esc_html_e('Confirm to invest on', HYIPLAB_PLUGIN_NAME); ?> <span class="planName"></span>
                    <?php } else { ?>
                        <?php esc_html_e('At first sign in your account', HYIPLAB_PLUGIN_NAME); ?>
                    <?php } ?>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('user.plan.invest'); ?>" method="post">
                <?php hyiplab_nonce_field('user.plan.invest');?>
                <input type="hidden" name="plan_id">
                <?php if (hyiplab_auth()) { ?>
                    <div class="modal-body">
                        <div class="form-group">

                            <h6 class="text-center investAmountRange"></h6>
                            <p class="text-center mt-1 interestDetails"></p>
                            <p class="text-center interestValidity"></p>

                            <label><?php esc_html_e('Select Wallet', HYIPLAB_PLUGIN_NAME); ?></label>
                            <select class="form-select form-control form--control form-select" name="wallet_type" required>
                                <option value=""><?php esc_html_e('Select One', HYIPLAB_PLUGIN_NAME); ?></option>
                                <?php if (hyiplab_balance(hyiplab_auth()->user->ID, 'deposit_wallet') > 0) { ?>
                                    <option value="deposit_wallet"><?php esc_html_e('Deposit Wallet', HYIPLAB_PLUGIN_NAME); ?> - <?php echo hyiplab_currency('sym') . hyiplab_show_amount(hyiplab_balance(hyiplab_auth()->user->ID, 'deposit_wallet')); ?></option>
                                <?php } ?>
                                <?php if (hyiplab_balance(hyiplab_auth()->user->ID, 'interest_wallet') > 0) { ?>
                                    <option value="interest_wallet"><?php esc_html_e('Interest Wallet', HYIPLAB_PLUGIN_NAME); ?> - <?php echo hyiplab_currency('sym') . hyiplab_show_amount(hyiplab_balance(hyiplab_auth()->user->ID, 'interest_wallet')); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php esc_html_e('Invest Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control form--control" name="amount" required>
                                <div class="input-group-text"><?php echo hyiplab_currency('text') ?></div>
                            </div>
                            <code class="gateway-info d-none"><?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?>: <span class="charge"></span> <?php echo hyiplab_currency('text') ?>. <?php esc_html_e('Total amount', HYIPLAB_PLUGIN_NAME); ?>: <span class="total"></span> <?php echo hyiplab_currency('text') ?></code>
                        </div>

                        <div class="row">
                            <div class="col-md-6 compoundInterest">
                                <div class="form-group">
                                    <label for="compound_interest"><?php esc_html_e('Compound Interest (optional)', HYIPLAB_PLUGIN_NAME); ?></label>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control form--control" name="compound_interest" id="compound_interest">
                                        <div class="input-group-text bg--base text-white"><?php echo esc_html_e('Times'); ?></div>
                                    </div>
                                    <small class="fst-italic text--info"><i class="las la-info-circle"></i><?php 
                                        esc_html_e('Your interest will add to the investment capital amount for a specific time that you\'re entering.', HYIPLAB_PLUGIN_NAME);
                                    ?></small>
                                </div>
                            </div>
                            <?php if (get_option('hyiplab_schedule_invest', true)) { ?>
                            <div class="col-sm-12 investTime col-md-6">
                                <div class="form-group has-icon-select">
                                    <label class="required" for="invest_time">
                                        <?php echo esc_html_e('Auto Schedule Invest', HYIPLAB_PLUGIN_NAME); ?>
                                    </label>
                                    <select class="form--control form-select" name="invest_time" required="" id="invest_time">
                                        <option value="invest_now"><?php esc_html_e('Invest Now', HYIPLAB_PLUGIN_NAME); ?></option>
                                        <option value="schedule"><?php esc_html_e('Schedule', HYIPLAB_PLUGIN_NAME) ?></option>
                                    </select>
                                    <small class="fst-italic text--info">
                                        <i class="las la-info-circle"></i>
                                        <?php esc_html_e('You can set your investment as a scheduler or invest instant.', HYIPLAB_PLUGIN_NAME); ?>
                                    </small>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php if (get_option('hyiplab_schedule_invest', true)) { ?>
                        <div class="row schedule">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="schedule_times"><?php esc_html_e('Schedule For', HYIPLAB_PLUGIN_NAME); ?></label>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control form--control" name="schedule_times" id="schedule_times">
                                        <span class="input-group-text bg--base text-white"><?php echo esc_html__('Times', HYIPLAB_PLUGIN_NAME); ?></span>
                                    </div>
                                    <small class="fst-italic text--info"><i class="las la-info-circle"></i>
                                    <?php esc_html_e('Set how many times you want to invest.', HYIPLAB_PLUGIN_NAME); ?></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="hours"><?php esc_html_e('After', HYIPLAB_PLUGIN_NAME); ?></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form--control" name="hours" min="0" id="hours">
                                        <span class="input-group-text bg--base text-white"><?php echo esc_html__('Hours', HYIPLAB_PLUGIN_NAME); ?></span>
                                    </div>
                                    <small class="fst-italic text--info"><i class="las la-info-circle"></i><?php esc_html_e('Set a frequency at which you prefer to make investments.', HYIPLAB_PLUGIN_NAME); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                <?php } ?>

                <div class="modal-footer">
                    <?php if (hyiplab_auth()) { ?>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><?php esc_html_e('No', HYIPLAB_PLUGIN_NAME); ?></button>
                        <button type="submit" class="btn btn--base"><?php esc_html_e('Yes', HYIPLAB_PLUGIN_NAME); ?></button>
                    <?php } else { ?>
                        <a href="<?php echo esc_url(home_url('login?redirect_to=user-dashboard/plan')); ?>" class="btn btn-primary w-100">
                            <?php esc_html_e('At first sign in your account', HYIPLAB_PLUGIN_NAME); ?>
                        </a>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        "use strict"
        $('.investModal').on('click', function() {
            var symbol = '<?php echo hyiplab_currency('sym');?>';
            var currency = '<?php echo hyiplab_currency('text');?>';
            var modal = $('#investModal');
            var planId = $(this).data('plan_id');
            var plan_name = $(this).data('plan_name');
            var fixed_amount = $(this).data('fixed_amount');
            var compound_interest = $(this).data('compound_interest');
            var schedule_invest = <?php echo get_option('hyiplab_schedule_invest', true); ?>;
            var minimum = $(this).data('minimum');
            var maximum = $(this).data('maximum');
            var interest = $(this).data('interest');
            var interest_type = $(this).data('interest_type');
            var time_name = $(this).data('time_name');
            var lifetime = $(this).data('lifetime');
            var repeat_time = $(this).data('repeat_time');
            modal.find('[name=plan_id]').val(planId);
            modal.find('.planName').text(plan_name);
            let fixedAmount = parseFloat(fixed_amount).toFixed(2);
            let minimumAmount = parseFloat(minimum).toFixed(2);
            let maximumAmount = parseFloat(maximum).toFixed(2);
            let interestAmount = parseFloat(interest);

            if (fixed_amount > 0) {
                modal.find('.investAmountRange').text(`Invest: ${symbol}${fixedAmount}`);
                modal.find('[name=amount]').val(parseFloat(fixed_amount).toFixed(2));
                modal.find('[name=amount]').attr('readonly', true);
            } else {
                modal.find('.investAmountRange').text(`Invest: ${symbol}${minimumAmount} - ${symbol}${maximumAmount}`);
                modal.find('[name=amount]').val('');
                modal.find('[name=amount]').removeAttr('readonly');
            }

            if (interest_type == '1') {
                modal.find('.interestDetails').html(`<strong> Interest: ${interestAmount}% </strong>`);
            } else {
                modal.find('.interestDetails').html(`<strong> Interest: ${interestAmount} ${currency}  </strong>`);
            }

            if (lifetime == '0') {
                modal.find('.interestValidity').html(`<strong>  Every ${time_name} for ${repeat_time} times</strong>`);
            } else {
                modal.find('.interestValidity').html(`<strong>  Every ${time_name} for life time </strong>`);
            }

            if (compound_interest == '1') {
                $('.compoundInterest').show();
                $('.investTime').removeClass('col-md-12');
            } else {
                $('.compoundInterest').hide();
                $('.investTime').addClass('col-md-12');
            }
            
            if (!schedule_invest){
                $('.modal-dialog').removeClass('modal-lg');
                $('.modal-dialog').find('.col-md-6').addClass('col-md-12');
            }

        });

        $('[name=invest_time]').on('change', function() {
            let investTime = $(this).find(':selected').val();
            if (investTime == 'invest_now') {
                $('.schedule').hide();
            } else {
                $('.schedule').show();
            }
        }).change();
    });

</script>