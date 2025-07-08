<?php foreach ($pools as $pool) { ?>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="plan-item style--two text-center mw-100 w-100 h-100 pool-plan">
            <div class="plan-item__header pool">
                <h4 class="mb-4 plan-title text--base"><?php echo esc_html($pool->name) ?></h4>
                <p class="item">
                   <?php echo esc_html('Total Amount', HYIPLAB_PLUGIN_NAME) . ' ' . hyiplab_currency('sym') . hyiplab_show_amount($pool->amount)  ?>
                </p>
                <p class="item">
                   <?php echo esc_html('Invest till', HYIPLAB_PLUGIN_NAME) . ' ' . hyiplab_show_date_time($pool->start_date)  ?>
                </p>
                <p class="item">
                   <?php echo esc_html('Return Date', HYIPLAB_PLUGIN_NAME) . ' ' . hyiplab_show_date_time($pool->end_date)  ?>
                </p>
                <div class="plan-rate mt-2">
                    <h3 class="rate fs-6">
                        <?php echo esc_html__('Invested Amount', HYIPLAB_PLUGIN_NAME); ?>
                    </h3>
                    <span class="remaining-amount"><?php echo hyiplab_currency('sym') . esc_html(hyiplab_show_amount($pool->invested_amount), 2) ?>/<?php echo hyiplab_currency('sym') . esc_html(hyiplab_show_amount($pool->amount), 2); ?></span>
                    <div class="progress">
                        <?php
                        $percent = round(($pool->invested_amount / $pool->amount) * 100, 2);
                        ?>
                        <div class="progress-bar customWidth bg--base" data-invested="<?php echo esc_attr($percent); ?>" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo esc_attr($percent); ?>%;"></div>
                    </div>
                </div>
            </div>
            <div class="plan-item__body my-4">
                <div class="return-percent">
                    <div class="package-card__range mt-2 base--color text--base fs--18px fw-bold">
                        <?php echo esc_html($pool->interest_range); ?>
                    </div>
                    <span><?php echo esc_html__('Interest Rate', HYIPLAB_PLUGIN_NAME); ?></span>
                </div>
            </div>
            <button class="btn btn--base investModal" data-bs-toggle="modal" data-pool_id="<?php echo esc_attr($pool->id); ?>" data-pool_name="<?php echo esc_attr($pool->name); ?>" data-bs-target="#investModal" type="button"><?php esc_html_e('Invest Now', HYIPLAB_PLUGIN_NAME); ?></button>
        </div>
    </div>
<?php } ?>



<div class="modal fade" id="investModal">
    <div class="modal-dialog modal-content-bg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php if (hyiplab_auth()) { ?>
                        <?php esc_html_e('Confirm to invest on', HYIPLAB_PLUGIN_NAME); ?> <span class="poolName"></span>
                    <?php } else { ?>
                        <?php esc_html_e('At first sign in your account', HYIPLAB_PLUGIN_NAME); ?>
                    <?php } ?>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('user.pool.store'); ?>" method="post">
                <?php hyiplab_nonce_field('user.pool.store');?>
                <input type="hidden" name="pool_id">
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
                        </div>
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
            var poolId = $(this).data('pool_id');
            var pool_name = $(this).data('pool_name');
            modal.find('[name=pool_id]').val(poolId);
            modal.find('.poolName').text(pool_name);s
        });

    });


</script>