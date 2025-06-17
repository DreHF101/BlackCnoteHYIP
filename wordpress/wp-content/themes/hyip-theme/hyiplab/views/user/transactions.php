<?php hyiplab_layout('user/layouts/master'); ?>
<div class="mb-4">
    <p><?php esc_html_e('Transaction', HYIPLAB_PLUGIN_NAME); ?></p>
    <h3><?php esc_html_e('My Transactions History', HYIPLAB_PLUGIN_NAME); ?></h3>
</div>

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