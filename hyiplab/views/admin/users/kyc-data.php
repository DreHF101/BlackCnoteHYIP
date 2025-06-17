<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-6 offset-3">
        <div class="card mt-30">
            <div class="card-body">
                <?php if(!hyiplab_check_empty($kycData)){ ?>
                <ul class="list-group">
                <?php foreach($kycData as $val){ ?>
                <?php if (!$val['value']) continue;?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo __($val['name']); ?>
                    <span>
                        <?php if($val['type'] == 'checkbox'){
                            implode(',',$val['value']); 
                        } else if ($val['type'] == 'file') { ?>
                            <a href="<?php echo hyiplab_route_link('user.attachment.download');?>?file=<?php echo esc_attr(hyiplab_encrypt($val['value'])); ?>" class="me-3"><i class="fa fa-file"></i> <?php esc_html_e('Attachment'); ?> </a>
                        <?php } else { ?>
                        <p><?php echo __($val['value']); ?></p>
                        <?php } ?>
                    </span>
                </li>
                <?php } ?>
                </ul>
                <?php } else { ?>
                <h5 class="text-center"><?php esc_html_e('KYC data not found', HYIPLAB_PLUGIN_NAME); ?></h5>
                <?php } ?>
            </div>
            <?php if(@$kyc != 1){ ?>
                <div class="row">
                    <div class="col-md-12 text-end px-4 mb-2">
                        <button class="btn btn-outline--success btn-sm ms-1 confirmationBtn" data-action="<?php echo hyiplab_route_link('admin.users.kyc.approve');?>&amp;id=<?php echo intval($user->ID); ?>" data-question="Are you sure to approve this KYC data?" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.users.kyc.approve'));?>"><i class="las la-check-double"></i><?php echo esc_html__('Approve', HYIPLAB_PLUGIN_NAME); ?></button>

                        <button class="btn btn-outline--danger ms-1 rejectBtn" data-id="<?php echo intval($user->ID); ?>" data-info="" data-username="<?php echo esc_attr($user->display_name); ?>"><i class="las la-ban"></i><?php echo esc_html__('Reject', HYIPLAB_PLUGIN_NAME); ?></button>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if(@$kyc == 3) { ?>
        <div class="card">
            <div class="card-body">
                <h5 class="text-center text--muted"><i class="far fa-frown"></i> <?php esc_html_e('KYC rejected', HYIPLAB_PLUGIN_NAME); ?></h5>
                <p class="text-center mt-2"><strong><?php echo 'Reason: '; ?></strong><?php echo esc_html($rejectReason); ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
</div>


<?php hyiplab_include('partials/confirmation'); ?>

<div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Reject KYC Confirmation', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.users.kyc.reject'); ?>" method="POST">
                <?php hyiplab_nonce_field('admin.users.kyc.reject'); ?>
                <input type="hidden" name="id">
                <div class="modal-body">
                    <p><?php esc_html_e('Are you sure to reject kyc data of', HYIPLAB_PLUGIN_NAME); ?> <span class="fw-bold withdraw-user"></span>?</p>

                    <div class="form-group">
                        <label class="fw-bold mt-2"><?php esc_html_e('Reason for Rejection', HYIPLAB_PLUGIN_NAME); ?></label>
                        <textarea name="reject_reason" maxlength="255" class="form-control" rows="5" required><?php echo esc_html($rejectReason); ?></textarea>
                    </div>

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
        $('.rejectBtn').on('click', function() {
            var modal = $('#rejectModal');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.find('.withdraw-user').text($(this).data('username'));
            modal.modal('show');
        });
    })(jQuery);
</script>