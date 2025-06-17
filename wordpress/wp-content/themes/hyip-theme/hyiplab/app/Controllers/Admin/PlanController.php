<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Plan;
use Hyiplab\Models\TimeSetting;

class PlanController extends Controller
{
    public function index()
    {
        $pageTitle = "Plans";
        $plans     = Plan::orderBy('id', 'desc')->get();
        $times     = TimeSetting::where('status', 1)->get();
        $this->view('admin/plan/index', compact('pageTitle', 'plans', 'times'));
    }

    public function store()
    {
        $request = new Request();
        $request->validate([
            'name'          => 'required',
            'interest_type' => 'required|integer',
            'interest'      => 'required|numeric',
            'time'          => 'required|integer'
        ]);

        if ($request->compound_interest && ((!$request->capital_back && !$request->return_type) || $request->interest_type == 2)) {
            $notify[] = ['error', 'When compound interest is enabled, capital back and return type are required.'];

            hyiplab_back($notify);
        }

        if ($request->hold_capital && !$request->capital_back) {
            $notify[] = ['error' => 'When hold capital is enabled, capital back is required.'];
            
            hyiplab_back($notify);
        }

        $plan                    = new Plan();
        $plan->name              = sanitize_text_field($request->name);
        $plan->minimum           = $request->minimum ? floatval($request->minimum) : 0;
        $plan->maximum           = $request->maximum ? floatval($request->maximum) : 0;
        $plan->fixed_amount      = $request->amount ? floatval($request->amount) : 0;
        $plan->interest          = floatval($request->interest);
        $plan->interest_type     = $request->interest_type == 1 ? 1 : 0;
        $plan->time_setting_id   = intval($request->time);
        $plan->capital_back      = $request->capital_back ? intval($request->capital_back) : 0;
        $plan->lifetime          = $request->return_type   == 1 ? 1 : 0;
        $plan->compound_interest = $request->compound_interest ? 1 : 0;
        $plan->hold_capital      = $request->hold_capital ? 1 : 0;
        $plan->featured          = $request->featured ? 1 : 0;
        $plan->repeat_time       = $request->repeat_time ? intval($request->repeat_time) : 0;
        $plan->save();

        $notify[] = ['success', 'Plan added successfully'];
        hyiplab_back($notify);
    }

    public function update()
    {
        $request = new Request();
        $request->validate([
            'name'          => 'required',
            'interest_type' => 'required|integer',
            'interest'      => 'required|numeric',
            'time'          => 'required|integer'
        ]);

        $plan                    = Plan::findOrFail($request->id);
        $plan->name              = sanitize_text_field($request->name);
        $plan->minimum           = $request->minimum ? floatval($request->minimum) : 0;
        $plan->maximum           = $request->maximum ? floatval($request->maximum) : 0;
        $plan->fixed_amount      = $request->amount ? floatval($request->amount) : 0;
        $plan->interest          = floatval($request->interest);
        $plan->interest_type     = $request->interest_type == 1 ? 1 : 0;
        $plan->time_setting_id   = intval($request->time);
        $plan->capital_back      = $request->capital_back ? intval($request->capital_back) : 0;
        $plan->lifetime          = $request->return_type   == 1 ? 1 : 0;
        $plan->repeat_time       = $request->repeat_time ? intval($request->repeat_time) : 0;
        $plan->compound_interest = $request->compound_interest ? 1 : 0;
        $plan->hold_capital      = $request->hold_capital ? 1 : 0;
        $plan->featured          = $request->featured ? 1 : 0;
        $plan->save();

        $notify[] = ['success', 'Plan updated successfully'];
        hyiplab_back($notify);
    }

    public function status()
    {
        $request      = new Request();
        $plan         = Plan::findOrFail($request->id);
        $plan->status = $plan->status ? 0 : 1;
        $plan->save();
        $notify[] = ['success', "Plan status changed successfully"];
        hyiplab_back($notify);
    }
}
