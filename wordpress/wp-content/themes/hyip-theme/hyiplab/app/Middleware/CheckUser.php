<?php

namespace Hyiplab\Middleware;

class CheckUser{
    public function filterRequest()
    {
        global $user_ID;
        if (get_user_meta($user_ID, 'hyiplab_user_active', true) == 0) {
            hyiplab_redirect(hyiplab_route_link('user.inactive'));
        }
    }
}