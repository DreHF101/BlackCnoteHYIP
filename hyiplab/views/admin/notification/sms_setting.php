<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <form action="<?php echo hyiplab_route_link('admin.setting.notification.sms.update'); ?>" method="POST">
                <?php hyiplab_nonce_field('admin.setting.notification.sms.update'); ?>
                <div class="card-body">
                    <div class="form-group">
                        <label><?php esc_html_e('Sms Send Method', HYIPLAB_PLUGIN_NAME); ?></label>
                        <select name="sms_method" class="form-control">
                            <option value="clickatell" <?php selected($smsConfig->name, 'clickatell'); ?>><?php esc_html_e('Clickatell', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="infobip" <?php selected($smsConfig->name, 'infobip'); ?>><?php esc_html_e('Infobip', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="messageBird" <?php selected($smsConfig->name, 'messageBird'); ?>><?php esc_html_e('Message Bird', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="nexmo" <?php selected($smsConfig->name, 'nexmo'); ?>><?php esc_html_e('Nexmo', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="smsBroadcast" <?php selected($smsConfig->name, 'smsBroadcast'); ?>><?php esc_html_e('Sms Broadcast', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="twilio" <?php selected($smsConfig->name, 'twilio'); ?>><?php esc_html_e('Twilio', HYIPLAB_PLUGIN_NAME); ?></option>
                            <option value="textMagic" <?php selected($smsConfig->name, 'textMagic'); ?>><?php esc_html_e('Text Magic', HYIPLAB_PLUGIN_NAME); ?></option>
                        </select>
                    </div>
                    <div class="row mt-4 d-none configForm" id="clickatell">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('Clickatell Configuration', HYIPLAB_PLUGIN_NAME);?></h6>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php esc_html_e('API Key', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('API Key', HYIPLAB_PLUGIN_NAME);?>" name="clickatell_api_key" value="<?php echo esc_attr($smsConfig->clickatell->api_key);?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 d-none configForm" id="infobip">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('Infobip Configuration', HYIPLAB_PLUGIN_NAME);?></h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Username', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('Username', HYIPLAB_PLUGIN_NAME);?>" name="infobip_username" value="<?php echo esc_attr($smsConfig->infobip->username);?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Password', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('Password', HYIPLAB_PLUGIN_NAME);?>" name="infobip_password" value="<?php echo esc_attr($smsConfig->infobip->password);?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 d-none configForm" id="messageBird">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('Message Bird Configuration', HYIPLAB_PLUGIN_NAME);?></h6>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php esc_html_e('API Key', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('API Key', HYIPLAB_PLUGIN_NAME);?>" name="message_bird_api_key" value="<?php echo esc_attr($smsConfig->message_bird->api_key);?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 d-none configForm" id="nexmo">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('Nexmo Configuration', HYIPLAB_PLUGIN_NAME);?></h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('API Key', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('API Key', HYIPLAB_PLUGIN_NAME);?>" name="nexmo_api_key" value="<?php echo esc_attr($smsConfig->nexmo->api_key);?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('API Secret', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('API Secret', HYIPLAB_PLUGIN_NAME);?>" name="nexmo_api_secret" value="<?php echo esc_attr($smsConfig->nexmo->api_secret);?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 d-none configForm" id="smsBroadcast">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('Sms Broadcast Configuration', HYIPLAB_PLUGIN_NAME);?></h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Username', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('Username', HYIPLAB_PLUGIN_NAME);?>" name="sms_broadcast_username" value="<?php echo esc_attr($smsConfig->sms_broadcast->username);?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Password', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('Password', HYIPLAB_PLUGIN_NAME);?>" name="sms_broadcast_password" value="<?php echo esc_attr($smsConfig->sms_broadcast->password);?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 d-none configForm" id="twilio">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('Twilio Configuration', HYIPLAB_PLUGIN_NAME);?></h6>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php esc_html_e('Account SID', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('Account SID', HYIPLAB_PLUGIN_NAME);?>" name="account_sid" value="<?php echo esc_attr($smsConfig->twilio->account_sid);?>" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php esc_html_e('Auth Token', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('Auth Token', HYIPLAB_PLUGIN_NAME);?>" name="auth_token" value="<?php echo esc_attr($smsConfig->twilio->auth_token);?>" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php esc_html_e('From Number', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('From Number', HYIPLAB_PLUGIN_NAME);?>" name="from" value="<?php echo esc_attr($smsConfig->twilio->from);?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 d-none configForm" id="textMagic">
                        <div class="col-md-12">
                            <h6 class="mb-2"><?php esc_html_e('Text Magic Configuration', HYIPLAB_PLUGIN_NAME);?></h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Username', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('Username', HYIPLAB_PLUGIN_NAME);?>" name="text_magic_username" value="<?php echo esc_attr($smsConfig->text_magic->username);?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php esc_html_e('Apiv2 Key', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" class="form-control" placeholder="<?php esc_attr_e('Apiv2 Key');?>" name="apiv2_key" value="<?php echo esc_attr($smsConfig->text_magic->apiv2_key);?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn w-100 h-45 btn--primary"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME);?></button>
                </div>
            </form>
        </div><!-- card end -->
    </div>
</div>


<div id="testSMSModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Test SMS Setup', HYIPLAB_PLUGIN_NAME);?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('admin.setting.notification.sms.test');?>" method="POST">
                <?php hyiplab_nonce_field('admin.setting.notification.sms.test');?>
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php esc_html_e('Sent to', HYIPLAB_PLUGIN_NAME);?></label>
                                <input type="text" name="mobile" class="form-control" placeholder="<?php esc_attr_e('Mobile', HYIPLAB_PLUGIN_NAME);?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME);?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

$html = '<button type="button" data-bs-target="#testSMSModal" data-bs-toggle="modal" class="btn btn-sm btn-outline--primary"><i class="las la-paper-plane"></i>' . esc_html__('Send Test SMS', HYIPLAB_PLUGIN_NAME) . '</button>';

hyiplab_push_breadcrumb($html);

?>


<script>
    jQuery(document).ready(function($) {
        "use strict";
        var method = '<?php echo esc_html($smsConfig->name); ?>';

        if (!method) {
            method = 'clickatell';
        }

        smsMethod(method);
        $('select[name=sms_method]').on('change', function() {
            var method = $(this).val();
            smsMethod(method);
        });

        function smsMethod(method) {
            $('.configForm').addClass('d-none');
            if (method != 'php') {
                $(`#${method}`).removeClass('d-none');
            }
        }

    });
</script>