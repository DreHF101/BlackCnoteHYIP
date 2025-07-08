<?php

namespace Hyiplab\Services;

use Hyiplab\BackOffice\Request;
use WP_Error;

class UserService
{
    public function updateUserProfile(int $userId, array $data): bool
    {
        $userData = [
            'ID'           => $userId,
            'display_name' => sanitize_text_field($data['display_name']),
        ];

        $result = wp_update_user($userData);

        if (is_wp_error($result)) {
            return false;
        }

        update_user_meta($userId, 'hyiplab_address', sanitize_text_field($data['address']));
        update_user_meta($userId, 'hyiplab_zip', sanitize_text_field($data['zip']));
        update_user_meta($userId, 'hyiplab_city', sanitize_text_field($data['city']));
        update_user_meta($userId, 'hyiplab_state', sanitize_text_field($data['state']));

        return true;
    }

    public function changeUserPassword(int $userId, Request $request): bool|WP_Error
    {
        $user = get_userdata($userId);

        if (!wp_check_password($request->current_password, $user->user_pass, $user->ID)) {
            return new WP_Error('password_mismatch', 'Current password does not match.');
        }

        $userData = [
            'ID'        => $userId,
            'user_pass' => $request->password,
        ];

        $result = wp_update_user($userData);

        if (is_wp_error($result)) {
            return $result;
        }

        return true;
    }
} 