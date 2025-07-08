<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Referral;

class ReferralController extends Controller
{
    public function index()
    {
        $this->pageTitle = "Referrals";
        $user = hyiplab_auth()->user;

        $maxLevel = cache()->remember('referral.max_level', 1440, function () {
            return Referral::max('level') ?? 0;
        });

        $this->view('user/referral', compact('user', 'maxLevel'));
    }
}
