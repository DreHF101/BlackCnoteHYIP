<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Services\UserService;

class ProfileController extends Controller
{
    protected $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    public function changePassword()
    {
        $this->pageTitle = "Change Password";
        $this->view('user/change_password');
    }

    public function changePasswordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:6',
        ]);

        $result = $this->userService->changeUserPassword(hyiplab_auth()->user->ID, $request);

        if (is_wp_error($result)) {
            $notify[] = ['error', $result->get_error_message()];
            return hyiplab_back($notify);
        }

        if ($result) {
            $notify[] = ['success', 'Password changed successfully.'];
            return hyiplab_back($notify);
        }

        $notify[] = ['error', 'An unexpected error occurred.'];
        return hyiplab_back($notify);
    }

    public function profileSetting()
    {
        $this->pageTitle = "Profile Setting";
        $user = hyiplab_auth()->user;
        $this->view('user/profile_setting', compact('user'));
    }

    public function profileSettingUpdate(Request $request)
    {
        $request->validate([
            'display_name' => 'required',
            'address'      => 'sometimes|required',
            'zip'          => 'sometimes|required',
            'city'         => 'sometimes|required',
            'state'        => 'sometimes|required',
        ]);

        $result = $this->userService->updateUserProfile(hyiplab_auth()->user->ID, $request->all());

        if ($result) {
            $notify[] = ['success', 'Profile updated successfully.'];
            return hyiplab_back($notify);
        }

        $notify[] = ['error', 'An unexpected error occurred.'];
        return hyiplab_back($notify);
    }
}
