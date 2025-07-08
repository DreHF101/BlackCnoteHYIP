<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Plan;
use Hyiplab\Models\ScheduleInvest;

class ScheduleController extends Controller
{
    public function index(){

        global $user_ID;

        $this->pageTitle = "Schedule Investments";
        $request = new Request();

        if (!get_option('hyiplab_schedule_invest', true)) {
            hyiplab_abort(404);
        }

        $pageTitle       = 'Schedule Invests';
        $scheduleInvests = ScheduleInvest::where('user_id', $user_ID)->orderBy('id', 'desc')->paginate(hyiplab_paginate());

        $this->view('user/schedule/index', compact( 'scheduleInvests'));
    }


    public function scheduleInvestStatus()
    {
        global $user_ID;
        $request = new Request();

        if (!get_option('hyiplab_schedule_invest', true)) {
            hyiplab_abort(404);
        }
        
        $scheduleInvest         = ScheduleInvest::where('user_id', $user_ID)->where('rem_schedule_times', '>', 0)->findOrFail($request->id);
        $scheduleInvest->status = !$scheduleInvest->status;
        $scheduleInvest->save();

        $notification = $scheduleInvest->status ? 'enabled' : 'disabled';
        $notify[]     = ['success', "Schedule invest $notification successfully"];

        $notify[] = ['success' => 'Your schedule status has been updated.'];
        hyiplab_back($notify);

    }




}