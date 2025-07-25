<?php

namespace Hyiplab\Services;

use Hyiplab\Models\NotificationTemplate;

class NotificationService
{
    public function updateGlobalSettings(array $data): void
    {
        update_option('hyiplab_email_from', sanitize_email($data['email_from']));
        update_option('hyiplab_email_template', htmlentities(wpautop($data['email_template'])));
        update_option('hyiplab_sms_from', sanitize_text_field($data['sms_from']));
        update_option('hyiplab_sms_body', sanitize_textarea_field($data['sms_body']));
    }

    public function updateEmailSettings(array $data): void
    {
        $emailConfig = [];

        switch ($data['email_method']) {
            case 'php':
                $emailConfig['name'] = 'php';
                break;
            case 'smtp':
                $emailConfig = [
                    'name' => 'smtp',
                    'host' => sanitize_text_field($data['host']),
                    'port' => sanitize_text_field($data['port']),
                    'enc' => sanitize_text_field($data['enc']),
                    'username' => sanitize_text_field($data['username']),
                    'password' => sanitize_text_field($data['password'])
                ];
                break;
            case 'sendgrid':
                $emailConfig = [
                    'name' => 'sendgrid',
                    'appkey' => sanitize_text_field($data['appkey'])
                ];
                break;
            case 'mailjet':
                $emailConfig = [
                    'name' => 'mailjet',
                    'public_key' => sanitize_text_field($data['public_key']),
                    'secret_key' => sanitize_text_field($data['secret_key'])
                ];
                break;
        }

        update_option('hyiplab_mail_config', $emailConfig);
    }

    public function updateSmsSettings(array $data): void
    {
        $smsConfig = ['name' => sanitize_text_field($data['sms_method'])];

        switch ($data['sms_method']) {
            case 'clickatell':
                $smsConfig['clickatell'] = ['api_key' => sanitize_text_field($data['clickatell_api_key'])];
                break;
            case 'infobip':
                $smsConfig['infobip'] = [
                    'username' => sanitize_text_field($data['infobip_username']),
                    'password' => sanitize_text_field($data['infobip_password'])
                ];
                break;
            case 'messageBird':
                $smsConfig['message_bird'] = ['api_key' => sanitize_text_field($data['message_bird_api_key'])];
                break;
            case 'nexmo':
                $smsConfig['nexmo'] = [
                    'api_key' => sanitize_text_field($data['nexmo_api_key']),
                    'api_secret' => sanitize_text_field($data['nexmo_api_secret'])
                ];
                break;
            case 'smsBroadcast':
                $smsConfig['sms_broadcast'] = [
                    'username' => sanitize_text_field($data['sms_broadcast_username']),
                    'password' => sanitize_text_field($data['sms_broadcast_password'])
                ];
                break;
            case 'twilio':
                $smsConfig['twilio'] = [
                    'account_sid' => sanitize_text_field($data['account_sid']),
                    'auth_token' => sanitize_text_field($data['auth_token']),
                    'from' => sanitize_text_field($data['from'])
                ];
                break;
            case 'textMagic':
                $smsConfig['text_magic'] = [
                    'username' => sanitize_text_field($data['text_magic_username']),
                    'apiv2_key' => sanitize_text_field($data['apiv2_key'])
                ];
                break;
        }

        update_option('hyiplab_sms_config', $smsConfig);
    }

    public function updateTemplate(int $templateId, array $data): NotificationTemplate
    {
        $template = NotificationTemplate::findOrFail($templateId);
        $template->subj = sanitize_text_field($data['subject']);
        $template->email_body = balanceTags(wp_kses($data['email_body'], hyiplab_allowed_html()));
        $template->email_status = !empty($data['email_status']) ? 1 : 0;
        $template->sms_body = sanitize_textarea_field($data['sms_body']);
        $template->sms_status = !empty($data['sms_status']) ? 1 : 0;
        $template->firebase_status = !empty($data['firebase_status']) ? 1 : 0;
        $template->firebase_body = $data['firebase_body'] ?? '';
        $template->save();

        return $template;
    }

    public function testEmail(string $email): void
    {
        $username = explode('@', $email)[0];
        $subject = esc_html__('Email Configuration Success', HYIPLAB_PLUGIN_NAME);
        $message = esc_html__('Your email notification setting is configured successfully for ', HYIPLAB_PLUGIN_NAME) . get_bloginfo('name');
        
        $user = hyiplab_to_object([
            'user_login' => $username,
            'user_email' => $email,
            'display_name' => $username,
        ]);

        hyiplab_notify($user, 'DEFAULT', [
            'subject' => $subject,
            'message' => $message,
        ]);
    }

    public function testSms(string $mobile): void
    {
        $message = esc_html__('Your SMS notification setting is configured successfully for ', HYIPLAB_PLUGIN_NAME) . get_bloginfo('name');
        
        $user = hyiplab_to_object([
            'mobile' => $mobile
        ]);

        hyiplab_notify($user, 'DEFAULT', [
            'message' => $message,
        ]);
    }

    public function getEmailConfig(): object
    {
        $config = get_option('hyiplab_mail_config');
        $defaults = [
            'name' => '',
            'host' => '',
            'port' => '',
            'enc' => '',
            'username' => '',
            'password' => '',
            'appkey' => '',
            'public_key' => '',
            'secret_key' => ''
        ];
        
        return hyiplab_to_object(wp_parse_args($config, $defaults));
    }

    public function getSmsConfig(): object
    {
        $config = get_option('hyiplab_sms_config');
        $defaults = [
            'name' => '',
            'clickatell' => ['api_key' => ''],
            'infobip' => ['username' => '', 'password' => ''],
            'message_bird' => ['api_key' => ''],
            'nexmo' => ['api_key' => '', 'api_secret' => ''],
            'sms_broadcast' => ['username' => '', 'password' => ''],
            'twilio' => ['account_sid' => '', 'auth_token' => '', 'from' => ''],
            'text_magic' => ['username' => '', 'apiv2_key' => '']
        ];
        
        return hyiplab_to_object(wp_parse_args($config, $defaults));
    }
}
