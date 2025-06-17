<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Referral;

class ReferralController extends Controller
{
    public function index()
    {
        $this->pageTitle = "Referrals";
        global $user_ID;
        $user     = get_userdata($user_ID);
        $maxLevel = Referral::max('level');
        $this->view('user/referral', compact('user', 'maxLevel'));
    }
}
