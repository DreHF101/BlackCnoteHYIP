<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <form action="<?php echo hyiplab_route_link('admin.setting.notification.email.update'); ?>" method="POST">
                <?php hyiplab_nonce_field('admin.setting.notification.email.update'); ?>
                <div class="card-body">
                    <div class="form-group">
                        <label class="mb-4"><?php esc_html_e('Email Send Method', HYIPLAB_PLUGIN_NAME); ?></label>
                        <select name="email_method" class="form-control">
                            <option value="php" <?php selected($mailConfig->name, 'php'); ?>><?php esc_html_e('PHP Mail', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="smtp" <?php selected($mailConfig->name, 'smtp'); ?>><?php esc_html_e('SMTP', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="sendgrid" <?php selected($mailConfig->name, 'sendgrid'); ?>><?php esc_html_e('SendGrid API', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="mailjet" <?php selected($mailConfig->name, 'mailjet'); ?>><?php esc_html_e('Mailjet API', HYIPLAB_PLUGIN_NAME); ?></option>
                        </select>
                    </div>
                    <div class="row mt-4 d-none configForm" id="smtp">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('SMTP Configuration', HYIPLAB_PLUGIN_NAME); ?></h6>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php esc_html_e('Host', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr(esc_html__('e.g. smtp.googlemail.com')); ?>" name="host" value="<?php echo esc_attr($mailConfig->host); ?>" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php esc_html_e('Port', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr(esc_html__("Available port", HYIPLAB_PLUGIN_NAME)); ?>" name="port" value="<?php echo esc_attr($mailConfig->port); ?>" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php esc_html_e('Encryption', HYIPLAB_PLUGIN_NAME); ?></label>
                                <select class="form-control" name="enc">
                                    <option value="ssl" <?php selected($mailConfig->enc, 'ssl'); ?>><?php esc_html_e('SSL', HYIPLAB_PLUGIN_NAME); ?></option>
                                    <option value="tls" <?php selected($mailConfig->enc, 'tls'); ?>><?php esc_html_e('TLS', HYIPLAB_PLUGIN_NAME); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Username', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr(esc_html__('Normally your email address', HYIPLAB_PLUGIN_NAME)); ?>" name="username" value="<?php echo esc_attr($mailConfig->username); ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Password', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr(esc_html__('Normally your email password', HYIPLAB_PLUGIN_NAME)); ?>" name="password" value="<?php echo esc_attr($mailConfig->password); ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 d-none configForm" id="sendgrid">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('SendGrid API Configuration', HYIPLAB_PLUGIN_NAME); ?></h6>
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('App Key', HYIPLAB_PLUGIN_NAME); ?></label>
                            <input type="text" class="form-control" placeholder="<?php echo esc_attr(esc_html__('SendGrid App key', HYIPLAB_PLUGIN_NAME)); ?>" name="appkey" value="<?php echo esc_attr($mailConfig->appkey); ?>" />
                        </div>
                    </div>
                    <div class="row mt-4 d-none configForm" id="mailjet">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('Mailjet API Configuration', HYIPLAB_PLUGIN_NAME); ?></h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Api Public Key', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr(esc_html__('Mailjet Api Public Key', HYIPLAB_PLUGIN_NAME)); ?>" name="public_key" value="<?php echo esc_attr($mailConfig->public_key); ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Api Secret Key', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr(esc_html__('Mailjet Api Secret Key', HYIPLAB_PLUGIN_NAME)); ?>" name="secret_key" value="<?php echo esc_attr($mailConfig->secret_key); ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div><!-- card end -->
    </div>
</div>

<div id="testMailModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Test Mail Setup', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.setting.notification.email.test'); ?>" method="POST">
                <input type="hidden" name="id">
                <?php hyiplab_nonce_field('admin.setting.notification.email.test'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php esc_html_e('Sent to', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" name="email" class="form-control" placeholder="<?php echo esc_attr(esc_html__('Email Address', HYIPLAB_PLUGIN_NAME)); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

$html = '<button type="button" data-bs-target="#testMailModal" data-bs-toggle="modal" class="btn btn-sm btn-outline--primary"><i class="las la-paper-plane"></i>' . esc_html__('Send Test Mail', HYIPLAB_PLUGIN_NAME) . '</button>';

hyiplab_push_breadcrumb($html);

?>

<script>
    (function($) {
        "use strict";
        var method = '<?php echo esc_attr($mailConfig->name); ?>';
        emailMethod(method);
        $('select[name=email_method]').on('change', function() {
            var method = $(this).val();
            emailMethod(method);
        });

        function emailMethod(method) {
            $('.configForm').addClass('d-none');
            if (method != 'php') {
                $(`#${method}`).removeClass('d-none');
            }
        }
    })(jQuery);
</script>