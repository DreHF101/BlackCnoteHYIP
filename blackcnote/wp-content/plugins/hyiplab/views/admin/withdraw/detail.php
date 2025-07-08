<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row mb-none-30">
    <div class="col-lg-4 col-md-5 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted"><?php esc_html_e('Withdraw Via', HYIPLAB_PLUGIN_NAME); ?> <?php echo esc_html($method->name); ?></h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('Date', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="fw-bold"><?php echo hyiplab_show_date_time($withdrawal->created_at); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('Trx Number', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="fw-bold"><?php echo esc_html($withdrawal->trx); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('Username', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="fw-bold">
                            <a href="<?php echo hyiplab_route_link('admin.users.detail');?>&amp;id=<?php echo intval($user->ID); ?>">
                                <?php echo esc_html($user->user_login); ?>
                            </a>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('Method', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="fw-bold"><?php echo esc_html($method->name); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="fw-bold"><?php echo hyiplab_show_amount($withdrawal->amount); ?> <?php echo hyiplab_currency('text'); ?></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="fw-bold"><?php echo hyiplab_show_amount($withdrawal->charge); ?> <?php echo hyiplab_currency('text'); ?></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('After Charge', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="fw-bold"><?php echo hyiplab_show_amount($withdrawal->after_charge); ?> <?php echo hyiplab_currency('text'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('Rate', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="fw-bold">1 <?php echo hyiplab_currency('text'); ?> = <?php echo hyiplab_show_amount($withdrawal->rate); ?> <?php echo hyiplab_currency('text'); ?></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('Payable', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="fw-bold"><?php echo hyiplab_show_amount($withdrawal->final_amount); ?> <?php echo hyiplab_currency('text'); ?></span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?>
                        <?php
                        $html = '';
                        if ($withdrawal->status == 2) {
                            $html = '<span class="badge badge--warning">' . esc_html__('Pending', HYIPLAB_PLUGIN_NAME) . '</span>';
                        } elseif ($withdrawal->status == 1) {
                            $html = '<span><span class="badge badge--success">' . esc_html__('Approved', HYIPLAB_PLUGIN_NAME) . '</span><br>' . hyiplab_diff_for_humans($withdrawal->updated_at) . '</span>';
                        } elseif ($withdrawal->status == 3) {
                            $html = '<span><span class="badge badge--danger">' . esc_html__('Rejected', HYIPLAB_PLUGIN_NAME) . '</span><br>' . hyiplab_diff_for_humans($withdrawal->updated_at) . '</span>';
                        }
                        echo wp_kses($html, hyiplab_allowed_html());
                        ?>
                    </li>

                    <?php if ($withdrawal->admin_feedback) { ?>
                        <li class="list-group-item">
                            <strong><?php esc_html_e('Admin Response', HYIPLAB_PLUGIN_NAME); ?></strong>
                            <br/>
                            <p><?php echo esc_html($withdrawal->admin_feedback); ?></p>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-8 col-md-7 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2"><?php esc_html_e('User Withdraw Information', HYIPLAB_PLUGIN_NAME); ?></h5>
                <?php
                $details = json_decode(json_encode(maybe_unserialize($withdrawal->withdraw_information)));
                if (!hyiplab_check_empty($details)) {
                    foreach ($details as $val) { ?>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6><?php echo esc_html($val->name); ?></h6>
                                <?php if ($val->type == 'checkbox') {
                                    echo implode(',', $val->value);
                                } elseif ($val->type == 'file') {
                                    if ($val->value) { ?>
                                        <a href="<?php echo hyiplab_route_link('admin.download.attachment') ?>&amp;file_path=<?php echo hyiplab_encrypt(hyiplab_file_path('verify') . '/' . $val->value) ?>" class="me-3"><i class="fa fa-file"></i> <?php esc_html_e('Attachment', HYIPLAB_PLUGIN_NAME); ?> </a>
                                    <?php } else {
                                        echo esc_html__('No File', HYIPLAB_PLUGIN_NAME);
                                    }
                                } else { ?>
                                    <?php echo esc_html($val->value); ?></p>
                                <?php }
                                ?>
                            </div>
                        </div>
                <?php }
                } ?>

                <?php if ($withdrawal->status == 2) { ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button class="btn btn-outline--success ms-1 approveBtn" data-id="<?php echo esc_attr($withdrawal->id); ?>" data-amount="<?php echo hyiplab_show_amount($withdrawal->final_amount); ?> <?php echo esc_attr($withdrawal->currency); ?>">
                                <i class="fas la-check"></i> <?php esc_html_e('Approve', HYIPLAB_PLUGIN_NAME); ?>
                            </button>

                            <button class="btn btn-outline--danger ms-1 rejectBtn" data-id="<?php echo esc_attr($withdrawal->id); ?>">
                                <i class="fas fa-ban"></i> <?php esc_html_e('Reject', HYIPLAB_PLUGIN_NAME); ?>
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Approve Withdrawal Confirmation', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.withdraw.approve'); ?>" method="POST">
                <?php hyiplab_nonce_field('admin.withdraw.approve'); ?>
                <input type="hidden" name="id">
                <div class="modal-body">
                    <p><?php esc_html_e('Have you sent', HYIPLAB_PLUGIN_NAME); ?> <span class="fw-bold withdraw-amount text-success"></span>?</p>
                    <p class="withdraw-detail"></p>
                    <textarea name="details" class="form-control pt-3" rows="3" placeholder="<?php echo esc_attr(esc_html__('Provide the details. eg: transaction number', HYIPLAB_PLUGIN_NAME)); ?>" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Reject Withdrawal Confirmation', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.withdraw.reject'); ?>" method="POST">
                <?php hyiplab_nonce_field('admin.withdraw.reject'); ?>
                <input type="hidden" name="id">
                <div class="modal-body">
                    <strong><?php esc_html_e('Reason of Rejection', HYIPLAB_PLUGIN_NAME); ?></strong>
                    <textarea name="details" class="form-control pt-3" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        $('.approveBtn').on('click', function() {
            var modal = $('#approveModal');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.find('.withdraw-amount').text($(this).data('amount'));
            modal.modal('show');
        });

        $('.rejectBtn').on('click', function() {
            var modal = $('#rejectModal');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.modal('show');
        });
    })(jQuery);
</script>