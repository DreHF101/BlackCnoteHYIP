<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form action="<?php echo hyiplab_route_link('admin.setting.system.configuration.store') ?>" method="post">
                <?php hyiplab_nonce_field('admin.setting.system.configuration.store') ?>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Email Notification', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small><?php esc_html_e('If you enable this module, the system will send emails to users where needed. Otherwise, no email will be sent.', HYIPLAB_PLUGIN_NAME); ?> <code><?php esc_html_e('So be sure before disabling this module that, the system doesn\'t need to send any emails.', HYIPLAB_PLUGIN_NAME); ?></code></small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_email_notification" <?php if (get_option('hyiplab_email_notification')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('SMS Notification', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small><?php esc_html_e('If you enable this module, the system will send SMS to users where needed. Otherwise, no SMS will be sent.', HYIPLAB_PLUGIN_NAME); ?> <code><?php esc_html_e('So be sure before disabling this module that, the system doesn\'t need to send any SMS.', HYIPLAB_PLUGIN_NAME); ?></code></small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_sms_notification" <?php if (get_option('hyiplab_sms_notification')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Email Verification', HYIPLAB_PLUGIN_NAME);?></p>
                                <p class="mb-0">
                                    <small>
                                        <?php esc_html_e('If you enable', HYIPLAB_PLUGIN_NAME);?> <span class="fw-bold"><?php esc_html_e('Email Verification', HYIPLAB_PLUGIN_NAME);?></span>, <?php esc_html_e('users have to verify their email to access the dashboard. A verification link will be sent to their email to be verified.', HYIPLAB_PLUGIN_NAME);?>
                                        <br>
                                        <span class="fw-bold"><i><?php esc_html_e('Note', HYIPLAB_PLUGIN_NAME);?>:</i></span> <i><?php esc_html_e('Make sure that the', HYIPLAB_PLUGIN_NAME);?> <span class="fw-bold"><?php esc_html_e('Email Notification', HYIPLAB_PLUGIN_NAME);?> </span> <?php esc_html_e('module is enabled', HYIPLAB_PLUGIN_NAME);?></i>
                                    </small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_email_verification" <?php if (get_option('hyiplab_email_verification')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Registration Bonus', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small><?php esc_html_e('If you enable this module, users will get an amount to their deposit wallet after completing registration, according to the Registration Bonus value set from the', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo hyiplab_route_link('admin.setting.index');?>"><?php esc_html_e('General Setting', HYIPLAB_PLUGIN_NAME);?></a>.</small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_registration_bonus" <?php if (get_option('hyiplab_registration_bonus')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('User Ranking', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small><?php esc_html_e('If you enable this module, users will get a defined bonus for investment that you can configure from', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo hyiplab_route_link('admin.ranking.index');?>"><?php esc_html_e('here', HYIPLAB_PLUGIN_NAME);?></a>.</small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_user_ranking" <?php if (get_option('hyiplab_user_ranking')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Balance Transfer', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small><?php esc_html_e('If you enable this module, users will be able to transfer the balance to each other. A fixed and a percent charge can be configured for this module from the', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo hyiplab_route_link('admin.setting.index');?>"><?php esc_html_e('General Setting', HYIPLAB_PLUGIN_NAME);?></a>.</small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_balance_transfer" <?php if (get_option('hyiplab_balance_transfer')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Promotional Tool', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small>
                                        <?php esc_html_e(' If you enable this module, users will be able to copy some HTML code which contain his/her referral link and an image added by you. Click here to add promotional tool. ', HYIPLAB_PLUGIN_NAME); ?> 
                                        <a href="<?php echo hyiplab_route_link('admin.promotion.index');?>">
                                            <?php esc_html_e('Promtional Tool', HYIPLAB_PLUGIN_NAME);?>
                                        </a>
                                    </small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_promotional_tool" <?php if (get_option('hyiplab_promotional_tool')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Withdrawal on Holiday', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small>
                                        <?php esc_html_e(' If you enable it, that means the system\'s users will be able to make withdrawal requests on holiday. Otherwise, they have to wait for the next working days ', HYIPLAB_PLUGIN_NAME); ?> 
                                    </small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_withdrawal_on_holiday" <?php if (get_option('hyiplab_withdrawal_on_holiday')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('KYC Verification', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small><?php esc_html_e('If you enable ', HYIPLAB_PLUGIN_NAME) ?><span class="fw-bold"><?php echo esc_html__('KYC (Know Your Client) ', HYIPLAB_PLUGIN_NAME); ?></span><?php esc_html_e('module, users must have to submit', HYIPLAB_PLUGIN_NAME) ?> <a href="<?php echo hyiplab_route_link('admin.kyc.index'); ?>"><?php esc_html_e('the required data', HYIPLAB_PLUGIN_NAME); ?></a>.<?php esc_html_e(' Otherwise, any money out transaction will be prevented by this system.', HYIPLAB_PLUGIN_NAME) ?></small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_kyc" <?php if (get_option('hyiplab_kyc')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Push Notification', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small>
                                        <?php esc_html_e(' If you enable this module, the system will send push notifications to users. Otherwise, no push notification will be sent. ', HYIPLAB_PLUGIN_NAME); ?> 
                                        <a href="<?php echo hyiplab_route_link('admin.setting.notification.template.push');?>">
                                            <?php esc_html_e('Setting here', HYIPLAB_PLUGIN_NAME);?>
                                        </a>
                                    </small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_push_notify" <?php if (get_option('hyiplab_push_notify')) echo 'checked'; ?>>
                            </div>
                        </li>
                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Schedule Invest', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small>
                                        <?php esc_html_e(' Enabling this module allows users to set up automated investment schedules. Without enabling it, users are unable to ments.', HYIPLAB_PLUGIN_NAME); ?>
                                    </small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_schedule_invest" <?php if (get_option('hyiplab_schedule_invest')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Staking', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small>
                                        <?php esc_html_e('Enabling this module allows users to stake their investments. Without enabling it, users will be unable to participate in staking. ', HYIPLAB_PLUGIN_NAME); ?>
                                    </small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_staking" <?php if (get_option('hyiplab_staking')) echo 'checked'; ?>>
                            </div>
                        </li>

                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0"><?php esc_html_e('Pool', HYIPLAB_PLUGIN_NAME); ?></p>
                                <p class="mb-0">
                                    <small>
                                        <?php esc_html_e('Enabling this module allows users to invest in the pool. Without enabling it, users will not have the option to invest in the pool.', HYIPLAB_PLUGIN_NAME); ?>
                                    </small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="<?php esc_attr_e('Enable', HYIPLAB_PLUGIN_NAME); ?>" data-off="<?php esc_attr_e('Disable', HYIPLAB_PLUGIN_NAME); ?>" name="hyiplab_pool" <?php if (get_option('hyiplab_pool')) echo 'checked'; ?>>
                            </div>
                        </li>

                    </ul>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>