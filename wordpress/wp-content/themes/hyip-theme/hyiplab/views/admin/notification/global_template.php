<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive table-responsive--sm">
                    <table class="table align-items-center table--light">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Short Code', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Description', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <tr>
                                <td><span class="short-codes">{{fullname}}</span></td>
                                <td><?php esc_html_e('Full Name of User', HYIPLAB_PLUGIN_NAME); ?></td>
                            </tr>
                            <tr>
                                <td><span class="short-codes">{{username}}</span></td>
                                <td><?php esc_html_e('Username of User', HYIPLAB_PLUGIN_NAME); ?></td>
                            </tr>
                            <tr>
                                <td><span class="short-codes">{{message}}</span></td>
                                <td><?php esc_html_e('Message', HYIPLAB_PLUGIN_NAME); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <h6 class="mt-4 mb-2"><?php esc_html_e('Global Short Codes', HYIPLAB_PLUGIN_NAME); ?></h6>
        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive table-responsive--sm">
                    <table class=" table align-items-center table--light">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Short Code', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Description', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <?php foreach (hyiplab_global_notify_short_codes() as $shortCode => $codeDetails) { ?>
                                <tr>
                                    <td><span class="short-codes">{{<?php echo esc_html($shortCode); ?>}}</span></td>
                                    <td><?php echo esc_html($codeDetails); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card mt-5">
            <div class="card-body">
                <form action="<?php echo hyiplab_route_link('admin.setting.notification.global.update'); ?>" method="POST">
                    <?php hyiplab_nonce_field('admin.setting.notification.global.update'); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php esc_html_e('Email Sent From', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control " placeholder="<?php esc_attr_e('Email address'); ?>" name="email_from" value="<?php echo esc_attr(get_option('hyiplab_email_from')); ?>" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php esc_html_e('Email Body', HYIPLAB_PLUGIN_NAME); ?></label>
                                <textarea name="email_template" rows="10" class="form-control nicEdit" placeholder="<?php esc_attr_e('Your email template'); ?>"><?php echo html_entity_decode(stripslashes(get_option('hyiplab_email_template'))); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php esc_html_e('SMS Sent From', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input class="form-control" placeholder="<?php esc_attr_e('SMS Sent From'); ?>" name="sms_from" value="<?php echo esc_attr(get_option('hyiplab_sms_from')); ?>" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php esc_html_e('SMS Body', HYIPLAB_PLUGIN_NAME); ?></label>
                                <textarea class="form-control" rows="4" placeholder="<?php esc_attr_e('SMS Body'); ?>" name="sms_body" required><?php echo html_entity_decode(stripslashes(get_option('hyiplab_sms_body'))); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn w-100 btn--primary h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </form>
            </div>
        </div><!-- card end -->
    </div>
</div>