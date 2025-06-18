<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row mb-none-30">
    <div class="col-lg-12 col-md-12 mb-30">
        <div class="card">
            <form action="<?php echo hyiplab_route_link('admin.setting.store'); ?>" method="POST">
                <?php hyiplab_nonce_field('admin.setting.store') ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="hyiplab_cur_text"><?php esc_html_e('Currency', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input class="form-control" type="text" name="hyiplab_cur_text" value="<?php echo get_option('hyiplab_cur_text'); ?>" id="hyiplab_cur_text">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="hyiplab_cur_sym"><?php esc_html_e('Currency Symbol', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input class="form-control" type="text" name="hyiplab_cur_sym" value="<?php echo get_option('hyiplab_cur_sym'); ?>" id="hyiplab_cur_sym">
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Registration Bonus', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input class="form-control bal-charge" type="text" name="hyiplab_registration_bonus_amount" required value="<?php echo hyiplab_get_amount(get_option('hyiplab_registration_bonus_amount') ?? 0); ?>" <?php if (!get_option('hyiplab_registration_bonus')) echo 'readonly'; ?>>
                                    <div class="input-group-text"><?php echo hyiplab_currency('text'); ?></div>
                                    <?php if (!get_option('hyiplab_registration_bonus')) { ?>
                                        <small class="text--small text-muted"><i><i class="las la-info-circle"></i> <?php esc_html_e('To give the registration bonus, please enable the module from the', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo hyiplab_route_link('admin.setting.system.configuration'); ?>" class="text--small"><?php esc_html_e('System Configuration', HYIPLAB_PLUGIN_NAME); ?></a></i></small>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Balance Transfer Fixed Charge', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input class="form-control bal-charge" type="text" name="hyiplab_balance_transfer_fixed_charge" required value="<?php echo hyiplab_get_amount(get_option('hyiplab_balance_transfer_fixed_charge')); ?>" <?php if (!get_option('hyiplab_balance_transfer')) echo 'readonly'; ?>>
                                    <div class="input-group-text"><?php echo hyiplab_currency('text'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Balance Transfer Percent Charge', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input class="form-control bal-charge" type="text" name="hyiplab_balance_transfer_percent_charge" required value="<?php echo hyiplab_get_amount(get_option('hyiplab_balance_transfer_percent_charge')); ?>" <?php if (!get_option('hyiplab_balance_transfer')) echo 'readonly'; ?>>
                                    <div class="input-group-text">%</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Staking Min Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input class="form-control bal-charge" type="text" name="hyiplab_staking_min_amount" required value="<?php echo hyiplab_get_amount(get_option('hyiplab_staking_min_amount')); ?>" <?php if (!get_option('hyiplab_staking')) echo 'readonly'; ?>>
                                    <div class="input-group-text"><?php echo hyiplab_currency('text'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Staking Max Amount', HYIPLAB_PLUGIN_NAME); ?></label>
                                <div class="input-group">
                                    <input class="form-control bal-charge" type="text" name="hyiplab_staking_max_amount" required value="<?php echo hyiplab_get_amount(get_option('hyiplab_staking_max_amount')); ?>" <?php if (!get_option('hyiplab_staking')) echo 'readonly'; ?>>
                                    <div class="input-group-text"><?php echo hyiplab_currency('text'); ?></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn--primary h-45 w-100"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>