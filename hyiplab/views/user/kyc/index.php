<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row gy-4">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="dashboard-inner">
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="wrapper d-flex justify-between ">
                                    <div class="heading">
                                        <h3><?php esc_html_e('KYC Data', HYIPLAB_PLUGIN_NAME); ?></h3>
                                        <p><?php esc_html_e('Your submitted KYC information is shown below. You couldn\'t change the data that you\'ve submitted. If the admin rejects your information, you\'ll be able to re-submit.', HYIPLAB_PLUGIN_NAME); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php if(@$kyc != 1){  ?>
                                <div class="col-md-4">
                                    <div class="button text-end">
                                        <a href="<?php echo hyiplab_route_link('user.kyc.form'); ?>" class="btn btn--primary"><?php esc_html_e('Submit KYC', HYIPLAB_PLUGIN_NAME); ?></a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card custom--card">
                        <div class="card-body">
                            <?php if(!empty($kycData)){ ?>
                            <ul class="list-group">
                            <?php foreach($kycData as $val){ ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo __($val['name']); ?>
                                <span>
                                    <?php if($val['type'] == 'checkbox'){
                                     implode(',',$val['value']); 
                                    } else if ($val['type'] == 'file') { ?>
                                        <a href="<?php echo hyiplab_route_link('user.attachment.download');?>?file=<?php echo esc_attr(hyiplab_encrypt($val['value'])); ?>" class="text-primary"><i class="fa fa-file"></i> <?php esc_html_e('Attachment'); ?> </a>
                                    <?php } else { ?>
                                    <p><?php echo __($val['value']); ?></p>
                                    <?php } ?>
                                </span>
                            </li>
                            <?php } ?>
                            </ul>
                            <?php } else { ?>
                                <div class="text-center">
                                    <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if(@$kyc == 3){?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center text--muted"><i class="far fa-frown"></i> <?php esc_html_e('KYC rejected', HYIPLAB_PLUGIN_NAME); ?></h5>
                                <p class="text-center mt-2"><strong><?php echo 'Reason: '; ?></strong><?php echo esc_html($rejectReason ?? ''); ?></p>
                                <p class="text-center mt-2 text--danger"><?php esc_html_e('If you want to re-submit your KYC data, please click on the Submit KYC button on top.', HYIPLAB_PLUGIN_NAME); ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>