<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\NotificationTemplate;
use Hyiplab\Notify\Sms;

class NotificationController extends Controller
{

    public function global()
    {
        $pageTitle = "Global Template for Notification";
        return $this->view('admin/notification/global_template', compact('pageTitle'));
    }

    public function globalUpdate()
    {
        $request = new Request();
        $request->validate([
            'email_from'     => 'required|email',
            'email_template' => 'required',
            'sms_from'       => 'required',
            'sms_body'       => 'required'
        ]);

        update_option('hyiplab_email_from', sanitize_email($request->email_from));
        update_option('hyiplab_email_template', htmlentities(wpautop(($request->email_template))));
        update_option('hyiplab_sms_from', sanitize_text_field($request->sms_from));
        update_option('hyiplab_sms_body', sanitize_textarea_field($request->sms_body));

        $notify[] = ['success', 'Global notification settings updated successfully'];
        hyiplab_back($notify);
    }

    public function emailSetting()
    {
        $pageTitle  = "Email Notification Settings";
        $mailConfig = get_option('hyiplab_mail_config');
        $default = [
            'name'       => '',
            'host'       => '',
            'port'       => '',
            'enc'        => '',
            'username'   => '',
            'password'   => '',
            'appkey'     => '',
            'public_key' => '',
            'secret_key' => ''
        ];
        $mailConfig = wp_parse_args($mailConfig, $default);
        $mailConfig = hyiplab_to_object($mailConfig);
        return $this->view('admin/notification/email_setting', compact('pageTitle', 'mailConfig'));
    }

    public function emailSettingUpdate()
    {
        $request = new Request();
        $request->validate([
            'email_method' => 'required'
        ]);

        if ($request->email_method == 'php') {
            $data['name'] = 'php';
        } else if ($request->email_method == 'smtp') {
            $data['name']     = 'smtp';
            $data['host']     = sanitize_text_field($request->host);
            $data['port']     = sanitize_text_field($request->port);
            $data['enc']      = sanitize_text_field($request->enc);
            $data['username'] = sanitize_text_field($request->username);
            $data['password'] = sanitize_text_field($request->password);
        } else if ($request->email_method == 'sendgrid') {
            $data['name']   = 'sendgrid';
            $data['appkey'] = sanitize_text_field($request->appkey);
        } else if ($request->email_method == 'mailjet') {
            $data['name']       = 'mailjet';
            $data['public_key'] = sanitize_text_field($request->public_key);
            $data['secret_key'] = sanitize_text_field($request->secret_key);
        }

        update_option('hyiplab_mail_config', $data);
        $notify[] = ['success', 'Email settings updated successfully'];
        hyiplab_back($notify);
    }

    public function smsSetting()
    {
        $pageTitle = "SMS Notification Settings";
        $smsConfig = get_option('hyiplab_sms_config');
        $default = [
            'name' => '',
            'clickatell' => [
                'api_key' => ''
            ],
            'infobip' => [
                'username' => '', 
                'password' =>''
            ],
            'message_bird' => [
                'api_key' => ''
            ],
            'nexmo' => [
                'api_key' => '', 
                'api_secret' => ''
            ],
            'sms_broadcast' => [
                'username' => '', 
                'password' =>''
            ],
            'twilio' => [
                'account_sid' => '',
                'auth_token' => '',
                'from' => ''
            ],
            'text_magic' => [
                'username' => '',
                'apiv2_key' => ''
            ]
        ];
        $smsConfig = wp_parse_args($smsConfig, $default);
        $smsConfig = hyiplab_to_object($smsConfig);
        return $this->view('admin/notification/sms_setting', compact('pageTitle', 'smsConfig'));
    }
    
    public function smsSettingUpdate()
    {
        $request = new Request();
        $request->validate([
            'sms_method' => 'required|in:clickatell,infobip,messageBird,nexmo,smsBroadcast,twilio,textMagic'
        ]);

        $data['name'] = sanitize_text_field($request->sms_method);

        if ($request->sms_method == 'clickatell') {

            $data['clickatell']['api_key'] = sanitize_text_field($request->clickatell_api_key);

        } elseif($request->sms_method == 'infobip'){

            $data['infobip'] = [
                'username' => $request->infobip_username,
                'password' => $request->infobip_password,
            ];

        } elseif($request->sms_method == 'messageBird'){
            
            $data['message_bird'] = [
                'api_key' => $request->message_bird_api_key
            ];

        } elseif($request->sms_method == 'nexmo'){
            
            $data['nexmo'] = [
                'api_key'    => $request->nexmo_api_key,
                'api_secret' => $request->nexmo_api_secret,
            ];

        } elseif($request->sms_method == 'smsBroadcast'){
            
            $data['sms_broadcast'] = [
                'username' => $request->sms_broadcast_username,
                'password' => $request->sms_broadcast_password,
            ];

        } elseif($request->sms_method == 'twilio'){
            
            $data['twilio'] = [
                'account_sid' => $request->account_sid,
                'auth_token'  => $request->auth_token,
                'from'        => $request->from,
            ];

        } elseif($request->sms_method == 'textMagic'){
            
            $data['text_magic'] = [
                'username'  => $request->text_magic_username,
                'apiv2_key' => $request->apiv2_key,
            ];
        }

        update_option('hyiplab_sms_config', $data);
        $notify[] = ['success', 'SMS settings updated successfully'];
        hyiplab_back($notify);
    }

    public function templates()
    {
        $pageTitle = "Notification Templates";
        $templates = NotificationTemplate::orderBy('name', 'asc')->get();
        return $this->view('admin/notification/templates', compact('pageTitle', 'templates'));
    }

    public function templateEdit()
    {
        $request  = new Request();
        $template = NotificationTemplate::findOrFail($request->id);
        $pageTitle = $template->name;
        return $this->view('admin/notification/edit', compact('pageTitle', 'template'));
    }

    public function templateUpdate()
    {
        $request = new Request();
        $request->validate([
            'subject'    => 'required',
            'email_body' => 'required',
            'sms_body'   => 'required'
        ]);

        $template                  = NotificationTemplate::find($request->id);
        $template->subj            = sanitize_text_field($request->subject);
        $template->email_body      = balanceTags(wp_kses($request->email_body, hyiplab_allowed_html()));
        $template->email_status    = $request->email_status ? 1 : 0;
        $template->sms_body        = sanitize_textarea_field($request->sms_body);
        $template->sms_status      = $request->sms_status ? 1 : 0;
        $template->firebase_status = $request->firebase_status ? 1 : 0;
        $template->firebase_body   = $request->firebase_body;

        $template->save();

        $notify[] = ['success', 'Notification template updated successfully'];
        hyiplab_back($notify);
    }

    public function emailTest()
    {
        $request = new Request();
        $request->validate([
            'email' => 'required'
        ]);

        $receiverName = explode('@', $request->email)[0];
        $subject      = esc_html__('Email Configuration Success', HYIPLAB_PLUGIN_NAME);
        $message      = esc_html__('Your email notification setting is configured successfully for ', HYIPLAB_PLUGIN_NAME) . get_bloginfo('name');
        $user         = [
            'user_login'   => $request->email,
            'user_email'   => $request->email,
            'display_name' => $receiverName,
        ];

        $user = hyiplab_to_object($user);
        
        hyiplab_notify($user, 'DEFAULT', [
            'subject' => $subject,
            'message' => $message,
        ]);

        $notify[] = ['success', 'Email sent successfully to ' . sanitize_email($request->email)];
        hyiplab_back($notify);
    }

    public function smsTest()
    {
        $request = new Request();
        $request->validate(['mobile' => 'required']);

        if ( get_option('hyiplab_sms_notification')) {

            $sendSms = new Sms;
            $sendSms->mobile = sanitize_text_field($request->mobile);
            $sendSms->receiverName = ' ';
            $sendSms->message = 'Your sms notification setting is configured successfully for ' . get_bloginfo('name');
            $sendSms->subject = ' ';
            $sendSms->send();

        } else {
            $notify[] = ['error', 'Please enable from system configuration'];
            $notify[] = ['error', 'Your sms notification is disabled'];
            hyiplab_back($notify);
        }

        if (hyiplab_session()->has('sms_error')) {
            $notify[] = ['error', hyiplab_session()->get('sms_error')];
        }else{
            $notify[] = ['success', 'SMS sent to ' . $request->mobile . 'successfully'];
        }
        hyiplab_back($notify);
    }


    public function templatePushNotification(){
        $pageTitle      = "Push Notification Settings";
        $firebaseConfig = (object) get_option('hyiplab_firebase_config', []);

        return $this->view('admin/notification/push_notification', compact('pageTitle', 'firebaseConfig'));
    }

    public function templatePushNotificationEdit(){

    }

    public function templatePushNotificationUpdate(){

        $request = new Request();
        $request->validate([
            'apiKey'            => 'required',
            'authDomain'        => 'required',
            'projectId'         => 'required',
            'storageBucket'     => 'required',
            'messagingSenderId' => 'required',
            'appId'             => 'required',
            'measurementId'     => 'required',
            'serverKey'         => 'required',
        ]);

        $data = [
            'apiKey'            => $request->apiKey,
            'authDomain'        => $request->authDomain,
            'projectId'         => $request->projectId,
            'storageBucket'     => $request->storageBucket,
            'messagingSenderId' => $request->messagingSenderId,
            'appId'             => $request->appId,
            'measurementId'     => $request->measurementId,
            'serverKey'         => $request->serverKey,
        ];

        update_option('hyiplab_firebase_config', $data);

        try {
            $jsPath = HYIPLAB_PLUGIN_URL . 'assets/global/js/firebase/configs.js';
            $config = "var firebaseConfig = " . json_encode(get_option('hyiplab_firebase_config', true));
            file_put_contents($jsPath, $config);
            
        } catch (\Exception$e) {
            $notify[] = ['error', $e->getMessage()];
            return hyiplab_back($notify);
        }

        $notify[] = ['success', 'Firebase settings updated successfully'];
        return hyiplab_back($notify);
    }


}
