<?php

namespace Hyiplab\Middleware;

class RedirectIfNotLogin
{
    public function filterRequest()
    {
        if (!is_user_logged_in()) {
            hyiplab_redirect(home_url('/login'));
            exit;
        }
    }
}