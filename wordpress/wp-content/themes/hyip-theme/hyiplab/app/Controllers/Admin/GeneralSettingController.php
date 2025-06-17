<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Intervention\Image\ImageManager as Image;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $pageTitle = "General Setting";
        $this->view('admin/setting/index', compact('pageTitle'));
    }

    public function store()
    {
        $request = new Request();
        $request->validate(
            [
                'hyiplab_cur_text' => 'required',
                'hyiplab_cur_sym'  => 'required',
            ],
            [
                'hyiplab_cur_text.required' => 'Currency field is required',
                'hyiplab_cur_sym.required'  => 'Currency symbol field is required'
            ]
        );

        update_option('hyiplab_cur_text', sanitize_text_field($request->hyiplab_cur_text));
        update_option('hyiplab_cur_sym', sanitize_text_field($request->hyiplab_cur_sym));
        update_option('hyiplab_registration_bonus_amount', sanitize_text_field($request->hyiplab_registration_bonus_amount));
        update_option('hyiplab_balance_transfer_fixed_charge', sanitize_text_field($request->hyiplab_balance_transfer_fixed_charge));
        update_option('hyiplab_balance_transfer_percent_charge', sanitize_text_field($request->hyiplab_balance_transfer_percent_charge));
        update_option('hyiplab_staking_min_amount', sanitize_text_field($request->hyiplab_staking_min_amount));
        update_option('hyiplab_staking_max_amount', sanitize_text_field($request->hyiplab_staking_max_amount));

        $notify[] = ['success', 'General setting updated successfully'];
        hyiplab_back($notify);
    }

    public function systemConfiguration()
    {
        $pageTitle = "System Configuration";
        return $this->view('admin/setting/configuration', compact('pageTitle'));
    }

    public function systemConfigurationStore()
    {
        $request = hyiplab_request();

        update_option('hyiplab_email_notification', $request->hyiplab_email_notification ? 1 : 0);
        update_option('hyiplab_sms_notification', $request->hyiplab_sms_notification ? 1 : 0);
        update_option('hyiplab_email_verification', $request->hyiplab_email_verification ? 1 : 0);
        update_option('hyiplab_user_ranking', $request->hyiplab_user_ranking ? 1 : 0);
        update_option('hyiplab_registration_bonus', $request->hyiplab_registration_bonus ? 1 : 0);
        update_option('hyiplab_balance_transfer', $request->hyiplab_balance_transfer ? 1 : 0);
        update_option('hyiplab_promotional_tool', $request->hyiplab_promotional_tool ? 1 : 0);
        update_option('hyiplab_kyc', $request->hyiplab_kyc ? 1 : 0);
        update_option('hyiplab_withdrawal_on_holiday', $request->hyiplab_withdrawal_on_holiday ? 1 : 0);
        update_option('hyiplab_push_notify', $request->hyiplab_push_notify ? 1 : 0);
        update_option('hyiplab_schedule_invest', $request->hyiplab_schedule_invest ? 1 : 0);
        update_option('hyiplab_staking', $request->hyiplab_staking ? 1 : 0);
        update_option('hyiplab_pool', $request->hyiplab_pool ? 1 : 0);

        $notify[] = ['success', 'System configuration update successfully'];
        hyiplab_back($notify);
    }

    public function logoIcon()
    {
        $pageTitle = 'Logo & Favicon';
        $this->view('admin/setting/logo_icon', compact('pageTitle'));
    }

    public function logoIconSubmit()
    {
        $request = new Request();
        $request->validate([
            'logo'    => 'image|mimes:jpg,jpeg,png',
            'favicon' => 'image|mimes:png',
        ]);

        $path  = HYIPLAB_ROOT . 'assets/global/images';
        $image = new Image();

        if ($request->hasFile('logo')) {
            try {
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $image->make($request->logo['tmp_name'])->save($path . '/logo_light.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                hyiplab_back($notify);
            }
        }

        if ($request->hasFile('logo_dark')) {
            try {
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $image->make($request->logo_dark['tmp_name'])->save($path . '/logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the dark logo'];
                hyiplab_back($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $size = explode('x', hyiplab_file_size('favicon'));
                $image->make($request->favicon['tmp_name'])->resize($size[0], $size[1])->save($path . '/favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the favicon'];
                hyiplab_back($notify);
            }
        }

        $notify[] = ['success', 'Logo & favicon updated successfully'];
        hyiplab_back($notify);
    }
}
