<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;

class ProfileController extends Controller
{
    public function changePassword()
    {
        $this->pageTitle = "Change Password";
        $this->view('user/change_password');
    }

    public function changePasswordUpdate()
    {
        global $user_ID;
        $request = new Request();
        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required',
            'password_confirmation' => 'required',
        ]);

        $user = get_userdata($user_ID);

        if (!wp_check_password(sanitize_text_field($request->current_password), $user->user_pass, $user->ID)) {
            $notify[] = ['error', 'Current password doesn\'t match'];
            hyiplab_back($notify);
        }

        if (strlen(sanitize_text_field($request->password)) < 6) {
            $notify[] = ['error', 'Passwords must be at least 6 characters long'];
            hyiplab_back($notify);
        }

        if (sanitize_text_field($request->password) != sanitize_text_field($request->password_confirmation)) {
            $notify[] = ['error', 'Please enter the same password in the two password fields.'];
            hyiplab_back($notify);
        }

        $userData = [
            'ID'        => intval($user->ID),
            'user_pass' => sanitize_text_field($request->password)
        ];

        $user_id = wp_update_user($userData);

        if ($user_id) {
            $notify[] = ['success', 'Password changes successfully'];
            hyiplab_back($notify);
        }

        $notify[] = ['error', 'Something went wrong'];
        hyiplab_back($notify);
    }

    public function profileSetting()
    {
        global $user_ID;
        $this->pageTitle = "Profile Setting";
        $user = get_userdata($user_ID);
        $this->view('user/profile_setting', compact('user'));
    }

    public function profileSettingUpdate()
    {
        global $user_ID;
        $request = new Request();
        $request->validate([
            'display_name' => 'required'
        ]);

        $userData = [
            'ID'           => intval($user_ID),
            'display_name' => sanitize_text_field($request->display_name)
        ];

        $user_id = wp_update_user($userData);

        if ($user_id) {
            update_user_meta($user_id, 'hyiplab_address', sanitize_text_field($request->address));
            update_user_meta($user_id, 'hyiplab_zip', sanitize_text_field($request->zip));
            update_user_meta($user_id, 'hyiplab_city', sanitize_text_field($request->city));
            update_user_meta($user_id, 'hyiplab_state', sanitize_text_field($request->state));

            $notify[] = ['success', 'Profile updated successfully'];
            hyiplab_back($notify);
        }

        $notify[] = ['error', 'Something went wrong'];
        hyiplab_back($notify);
    }
}
