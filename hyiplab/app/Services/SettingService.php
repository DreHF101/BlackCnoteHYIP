<?php

namespace Hyiplab\Services;

use Hyiplab\BackOffice\Request;
use Intervention\Image\ImageManager;

class SettingService
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager();
    }

    public function updateGeneralSettings(Request $request): void
    {
        $settings = [
            'hyiplab_cur_text' => 'required',
            'hyiplab_cur_sym'  => 'required',
            'hyiplab_registration_bonus_amount' => 'nullable|numeric',
            'hyiplab_balance_transfer_fixed_charge' => 'nullable|numeric',
            'hyiplab_balance_transfer_percent_charge' => 'nullable|numeric',
            'hyiplab_staking_min_amount' => 'nullable|numeric',
            'hyiplab_staking_max_amount' => 'nullable|numeric',
        ];

        foreach ($settings as $key => $rule) {
            if ($request->has($key)) {
                update_option($key, sanitize_text_field($request->$key));
            }
        }
    }

    public function updateSystemConfiguration(Request $request): void
    {
        $configs = [
            'hyiplab_email_notification', 'hyiplab_sms_notification', 'hyiplab_email_verification',
            'hyiplab_user_ranking', 'hyiplab_registration_bonus', 'hyiplab_balance_transfer',
            'hyiplab_promotional_tool', 'hyiplab_kyc', 'hyiplab_withdrawal_on_holiday',
            'hyiplab_push_notify', 'hyiplab_schedule_invest', 'hyiplab_staking', 'hyiplab_pool'
        ];

        foreach ($configs as $key) {
            update_option($key, $request->$key ? 1 : 0);
        }
    }

    public function updateLogoAndFavicon(Request $request): array
    {
        $errors = [];
        $path = HYIPLAB_ROOT . 'assets/global/images';

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        if ($request->hasFile('logo')) {
            if (!$this->saveImage($request->file('logo'), $path . '/logo_light.png')) {
                $errors[] = 'Could not upload the logo.';
            }
        }

        if ($request->hasFile('logo_dark')) {
            if (!$this->saveImage($request->file('logo_dark'), $path . '/logo.png')) {
                $errors[] = 'Could not upload the dark logo.';
            }
        }

        if ($request->hasFile('favicon')) {
            $size = explode('x', hyiplab_file_size('favicon'));
            if (!$this->saveImage($request->file('favicon'), $path . '/favicon.png', ['width' => $size[0], 'height' => $size[1]])) {
                $errors[] = 'Could not upload the favicon.';
            }
        }

        return $errors;
    }

    protected function saveImage($file, $path, $size = null): bool
    {
        try {
            $image = $this->imageManager->make($file['tmp_name']);
            if ($size) {
                $image->resize($size['width'], $size['height']);
            }
            $image->save($path);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
} 