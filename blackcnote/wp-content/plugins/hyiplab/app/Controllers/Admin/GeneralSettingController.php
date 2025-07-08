<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Services\SettingService;

class GeneralSettingController extends Controller
{
    protected $settingService;

    public function __construct()
    {
        parent::__construct();
        $this->settingService = new SettingService();
    }

    public function index()
    {
        $this->pageTitle = "General Setting";
        $this->view('admin/setting/index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'hyiplab_cur_text' => 'required',
            'hyiplab_cur_sym'  => 'required',
        ]);

        $this->settingService->updateGeneralSettings($request);

        $notify[] = ['success', 'General settings updated successfully.'];
        return hyiplab_back($notify);
    }

    public function systemConfiguration()
    {
        $this->pageTitle = "System Configuration";
        $this->view('admin/setting/configuration');
    }

    public function systemConfigurationStore(Request $request)
    {
        $this->settingService->updateSystemConfiguration($request);

        $notify[] = ['success', 'System configuration updated successfully.'];
        return hyiplab_back($notify);
    }

    public function logoIcon()
    {
        $this->pageTitle = 'Logo & Favicon';
        $this->view('admin/setting/logo_icon');
    }

    public function logoIconSubmit(Request $request)
    {
        $request->validate([
            'logo'      => 'nullable|image|mimes:jpg,jpeg,png',
            'logo_dark' => 'nullable|image|mimes:jpg,jpeg,png',
            'favicon'   => 'nullable|image|mimes:png',
        ]);

        $errors = $this->settingService->updateLogoAndFavicon($request);

        if (!empty($errors)) {
            $notify[] = ['error', implode(' ', $errors)];
            return hyiplab_back($notify);
        }

        $notify[] = ['success', 'Logo & favicon updated successfully.'];
        return hyiplab_back($notify);
    }
}
