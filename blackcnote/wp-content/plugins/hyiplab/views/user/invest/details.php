<?php hyiplab_layout('user/layouts/master'); ?>

<div class="mb-4">
    <p><?php esc_html_e('Investment', HYIPLAB_PLUGIN_NAME); ?></p>
    <h3><?php esc_html_e('Investment Details', HYIPLAB_PLUGIN_NAME); ?></h3>
</div>
<div class="row gy-3">
    <div class="col-xl-4">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="title"><?php esc_html_e('Plan & User Information', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Plan Name', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo esc_html($plan->name); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Investable Amount ', HYIPLAB_PLUGIN_NAME); ?>
                        <span>
                            <?php if ($plan->fixed_amount > 0) { ?>
                                <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($plan->fixed_amount); ?>
                            <?php } else { ?>
                                <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($plan->minimum); ?> - <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($plan->maximum); ?>
                            <?php } ?>
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Full Name', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo esc_html($user->display_name); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Username', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo esc_html($user->user_login); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Mobile', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo esc_html(get_user_meta($user->ID, 'hyiplab_mobile', true)); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Email', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo esc_html($user->user_email); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="title"><?php esc_html_e('Basic Information', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Invest Amount', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($invest->amount); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Invested', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo hyiplab_show_date_time($invest->created_at); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Interest Amount', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($invest->interest); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Total Payable', HYIPLAB_PLUGIN_NAME); ?>
                        <span>
                            <?php if ($invest->period != -1) { ?>
                                <?php echo esc_html($invest->period); ?> <?php esc_html_e('times', HYIPLAB_PLUGIN_NAME); ?>
                            <?php } else { ?>
                                <?php esc_html_e('Lifetime', HYIPLAB_PLUGIN_NAME); ?>
                            <?php } ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Interest Interval', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php esc_html_e('Every', HYIPLAB_PLUGIN_NAME); ?> <?php echo esc_html($invest->time_name); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?>
                        <span>
                            <?php if ($invest->status) { ?>
                                <span class="badge badge--success"><?php esc_html_e('Running', HYIPLAB_PLUGIN_NAME); ?></span>
                            <?php } else { ?>
                                <span class="badge badge--dark"><?php esc_html_e('Closed', HYIPLAB_PLUGIN_NAME); ?></span>
                            <?php } ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="title"><?php esc_html_e('Other Information', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Total Paid', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($invest->paid); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Total Paid Amount', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo esc_html($invest->return_rec_time); ?> <?php esc_html_e('times', HYIPLAB_PLUGIN_NAME); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Should Pay', HYIPLAB_PLUGIN_NAME); ?>
                        <span>
                            <?php if ($invest->should_pay != -1) { ?>
                                <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($invest->interest); ?>
                            <?php } else { ?>
                                **
                            <?php } ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Last Paid Time', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo esc_html($invest->last_time) ? hyiplab_show_date_time($invest->last_time) : esc_html__('No', HYIPLAB_PLUGIN_NAME); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Next Pay Time', HYIPLAB_PLUGIN_NAME); ?>
                        <span><?php echo hyiplab_show_date_time($invest->next_time ?? ''); ?></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Capital Back', HYIPLAB_PLUGIN_NAME); ?>
                        <span>
                            <?php if ($invest->capital_status) { ?>
                                <?php esc_html_e('Yes', HYIPLAB_PLUGIN_NAME); ?>
                            <?php } else { ?>
                                <?php esc_html_e('No', HYIPLAB_PLUGIN_NAME); ?>
                            <?php } ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<h4 class="mb-2 mt-4"><?php esc_html_e('All Interests', HYIPLAB_PLUGIN_NAME);?></h4>
<div class="accordion table--acordion" id="transactionAccordion">
    <?php foreach ($transactions->data as $key => $transaction) { ?>
        <div class="accordion-item transaction-item">
            <h2 class="accordion-header" id="h-<?php echo esc_attr($key); ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c-<?php echo esc_attr($key); ?>">
                    <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                        <div class="left">
                            <div class="icon tr-icon <?php if ($transaction->trx_type  == '+') {
                                                            echo 'icon-success';
                                                        } else {
                                                            echo 'icon-danger';
                                                        } ?>">
                                <i class="las la-long-arrow-alt-right"></i>
                            </div>
                            <div class="content">
                                <h6 class="trans-title"><?php echo vlKeyToTitle($transaction->remark); ?> - <?php echo vlKeyToTitle($transaction->wallet_type); ?></h6>
                                <span class="text-muted font-size--14px mt-2"><?php echo hyiplab_show_date_time($transaction->created_at, 'M d Y @g:i:a'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper mt-sm-0 mt-3">
                        <p class="text-muted font-size--14px"><b>#<?php echo esc_html($transaction->trx); ?></b></p>
                    </div>
                    <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                        <p>
                            <b><?php echo hyiplab_show_amount($transaction->amount); ?> <?php echo hyiplab_currency('text'); ?></b><br>
                            <small class="fw-bold text-muted"><?php esc_html_e('Balance', HYIPLAB_PLUGIN_NAME); ?>: <?php echo hyiplab_show_amount($transaction->post_balance); ?> <?php echo hyiplab_currency('text'); ?></small>
                        </p>

                    </div>
                </button>
            </h2>
            <div id="c-<?php echo esc_attr($key); ?>" class="accordion-collapse collapse" aria-labelledby="h-1" data-bs-parent="#transactionAccordion">
                <div class="accordion-body">
                    <ul class="caption-list">
                        <li>
                            <span class="caption"><?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?></span>
                            <span class="value"><?php echo hyiplab_show_amount($transaction->charge); ?> <?php echo hyiplab_currency('text'); ?></span>
                        </li>
                        <li>
                            <span class="caption"><?php esc_html_e('Post Balance', HYIPLAB_PLUGIN_NAME); ?></span>
                            <span class="value"><?php echo hyiplab_show_amount($transaction->post_balance); ?> <?php echo hyiplab_currency('text'); ?></span>
                        </li>
                        <li>
                            <span class="caption"><?php esc_html_e('Details', HYIPLAB_PLUGIN_NAME); ?></span>
                            <span class="value"><?php echo esc_html($transaction->details); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- transaction-item end -->

    <?php } ?>

    <?php if (hyiplab_check_empty($transactions->data)) { ?>
        <div class="accordion-body text-center">
            <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
        </div>
    <?php } ?>
</div>

<?php if ($transactions->links) { ?>
    <div class="mt-4">
        <?php echo wp_kses($transactions->links, hyiplab_allowed_html()); ?>
    </div>
<?php } ?>