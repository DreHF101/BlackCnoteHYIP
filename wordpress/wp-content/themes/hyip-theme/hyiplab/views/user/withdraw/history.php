<?php hyiplab_layout('user/layouts/master'); ?>
<div class="mb-4">
    <div class="d-flex justify-content-between">
        <h3 class="mb-2"><?php esc_html_e('Withdraw History', HYIPLAB_PLUGIN_NAME); ?></h3>
        <span>
            <a href="<?php echo hyiplab_route_link('user.withdraw.index'); ?>" class="btn btn--secondary btn--smd"><?php esc_html_e('Withdraw Now', HYIPLAB_PLUGIN_NAME); ?> <i class="las la-long-arrow-alt-right"></i></a>
        </span>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">

        <div class="accordion table--acordion" id="transactionAccordion">
            <?php foreach ($withdraws->data as $key => $withdraw) {
                $method = hyiplab_withdraw_methods($withdraw->method_id);
            ?>
                <div class="accordion-item transaction-item">
                    <h2 class="accordion-header" id="h-<?php echo esc_attr($key); ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c-<?php echo esc_attr($key); ?>" aria-expanded="false" aria-controls="c-1">
                            <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                                <div class="left">
                                    <?php if ($withdraw->status == 1) { ?>
                                        <div class="icon icon-success">
                                            <i class="las la-check"></i>
                                        </div>
                                    <?php } elseif ($withdraw->status == 2) { ?>
                                        <div class="icon icon-warning">
                                            <i class="las la-spinner fa-spin"></i>
                                        </div>
                                    <?php } elseif ($withdraw->status == 3) { ?>
                                        <div class="icon icon-danger">
                                            <i class="las la-ban"></i>
                                        </div>
                                    <?php } ?>
                                    <div class="content">
                                        <h6 class="trans-title"><?php echo esc_html($method->name); ?></h6>
                                        <span class="text-muted font-size--14px mt-2"><?php echo hyiplab_show_date_time($withdraw->created_at, 'M d Y @g:i:a'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper mt-sm-0 mt-3">
                                <p class="text-muted font-size--14px"><b>#<?php echo esc_html($withdraw->trx); ?></b></p>
                            </div>
                            <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                <p><b><?php echo hyiplab_show_amount($withdraw->amount); ?> <?php echo hyiplab_currency('text'); ?></b></p>
                            </div>
                        </button>
                    </h2>
                    <div id="c-<?php echo esc_attr($key); ?>" class="accordion-collapse collapse" aria-labelledby="h-1" data-bs-parent="#transactionAccordion">
                        <div class="accordion-body">
                            <ul class="caption-list">
                                <li>
                                    <span class="caption"><?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <span class="value"><?php echo hyiplab_show_amount($withdraw->charge); ?> <?php echo hyiplab_currency('text'); ?></span>
                                </li>
                                <li>
                                    <span class="caption"><?php esc_html_e('After Charge', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <span class="value"><?php echo hyiplab_show_amount($withdraw->amount - $withdraw->charge); ?> <?php echo hyiplab_currency('text'); ?></span>
                                </li>
                                <li>
                                    <span class="caption"><?php esc_html_e('Conversion', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <span class="value"><?php echo hyiplab_show_amount($withdraw->amount - $withdraw->charge); ?> <?php echo hyiplab_currency('text'); ?> x <?php echo hyiplab_show_amount($withdraw->rate); ?> <?php echo esc_html($withdraw->currency); ?> = <?php echo hyiplab_show_amount($withdraw->final_amount); ?> <?php echo esc_html($withdraw->currency); ?></span>
                                </li>
                                <li>
                                    <span class="caption"><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <span class="value">
                                        <?php
                                        $html = '';
                                        if ($withdraw->status == 2) {
                                            $html = '<span class="badge badge--warning">' . esc_html__('Pending', HYIPLAB_PLUGIN_NAME) . '</span>';
                                        } elseif ($withdraw->status == 1) {
                                            $html = '<span><span class="badge badge--success">' . esc_html__('Approved', HYIPLAB_PLUGIN_NAME) . '</span><br>' . hyiplab_diff_for_humans($withdraw->updated_at) . '</span>';
                                        } elseif ($withdraw->status == 3) {
                                            $html = '<span><span class="badge badge--danger">' . esc_html__('Rejected', HYIPLAB_PLUGIN_NAME) . '</span><br>' . hyiplab_diff_for_humans($withdraw->updated_at) . '</span>';
                                        }
                                        echo wp_kses($html, hyiplab_allowed_html());

                                        $withdraw_information = maybe_unserialize($withdraw->withdraw_information);

                                        ?>
                                        <a href="javascript:void(0)"><i class="las la-info-circle detailBtn" data-user_data='<?php echo wp_json_encode($withdraw_information); ?>' <?php if ($withdraw->status == 3) { ?> data-admin_feedback="<?php echo esc_attr($withdraw->admin_feedback); ?>" <?php } ?>></i></a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div><!-- transaction-item end -->
            <?php } ?>
            <?php if (hyiplab_check_empty($withdraws->data)) { ?>
                <div class="accordion-body text-center">
                    <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
                </div>
            <?php } ?>
        </div>

        <?php if ($withdraws->links) { ?>
            <div class="mt-4">
                <?php echo wp_kses($withdraws->links, hyiplab_allowed_html()); ?>
            </div>
        <?php } ?>

    </div>
</div>

<div id="detailModal" class="modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Details', HYIPLAB_PLUGIN_NAME); ?></h5>
                <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </span>
            </div>
            <div class="modal-body">
                <ul class="list-group userData">

                </ul>
                <div class="feedback"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal"><?php esc_html_e('Close', HYIPLAB_PLUGIN_NAME); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        "use strict";
        $('.detailBtn').on('click', function() {
            var modal = $('#detailModal');
            var userData = $(this).data('user_data');
            var html = ``;
            userData.forEach(element => {
                if (element.type != 'file') {
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                }
            });
            modal.find('.userData').html(html);
            if ($(this).data('admin_feedback') != undefined) {
                var adminFeedback = `
                        <div class="my-3">
                            <strong><?php esc_html_e('Admin Feedback',HYIPLAB_PLUGIN_NAME) ?></strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
            } else {
                var adminFeedback = '';
            }
            modal.find('.feedback').html(adminFeedback);
            modal.modal('show');
        });
    })
</script>