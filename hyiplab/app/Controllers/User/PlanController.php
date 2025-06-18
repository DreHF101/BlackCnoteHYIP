<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $this->pageTitle = "Plans";
        $plans           = Plan::where('status', 1)->orderBy('id', 'DESC')->get();
        $this->view('user/plan', compact('plans'));
    }

    public function invest()
    {
        global $user_ID;
        $request = new Request();
        
        $this->validation($request);

        $user = get_userdata($user_ID);
        $plan = Plan::where('id', $request->plan_id)->where('status', 1)->firstOrFail();

        if ($request->compound_interest) {
            if (!$plan->compound_interest) {
                $notify[] = ['error', 'Compound interest is not available for this plan.'];
                hyiplab_back($notify);
            }

            if ($plan->repeat_time && $plan->repeat_time <= $request->compound_interest) {
                $notify[] = ['error', 'Compound interest times must be fewer than repeat times.'];
                hyiplab_back($notify);
            }
        }

        $amount = floatval($request->amount);

        if ($plan->fixed_amount > 0) {
            if ($amount != $plan->fixed_amount) {
                $notify[] = ['error', 'Please check the investment limit'];
                hyiplab_set_notify($notify);
                hyiplab_redirect(hyiplab_route_link('user.plan.index'));
            }
        } else {
            if ($amount < $plan->minimum || $amount > $plan->maximum) {
                $notify[] = ['error', 'Please check the investment limit'];
                hyiplab_set_notify($notify);
                hyiplab_redirect(hyiplab_route_link('user.plan.index'));
            }
        }

        $wallet = sanitize_text_field($request->wallet_type);
        if ($amount > hyiplab_balance($user_ID, $wallet)) {
            $notify[] = ['error', 'Your balance is not sufficient'];
            hyiplab_set_notify($notify);
            hyiplab_redirect(hyiplab_route_link('user.plan.index'));
        }

        if ($request->invest_time == 'schedule' && get_option('hyiplab_schedule_invest', true)) {
            HyipLab::saveScheduleInvest($request, $user_ID);
            $notify[] = ['success', 'Invest scheduled successfully'];
            hyiplab_back($notify);
        }

        $hyip = new HyipLab($user, $plan);
        $hyip->invest($amount, $wallet, $request->compound_interest);
        $notify[] = ['success', 'Invested to plan successfully'];
        hyiplab_set_notify($notify);
        hyiplab_redirect(hyiplab_route_link('user.invest.index'));
    }

    private function validation($request)
    {
        $validationRule = [
            'amount'            => 'required|numeric',
            'plan_id'           => 'required|integer',
            'wallet_type'       => 'required',
            'invest_time'       => 'required',
            'compound_interest' => 'numeric',
        ];
        
        $schedule_invest = get_option('hyiplab_schedule_invest', true);
        
        if ($schedule_invest) {
            $validationRule['invest_time'] = 'required|in:invest_now,schedule';
        }

        if ($request->invest_time == 'schedule') {
            $validationRule['wallet_type']    = 'required|in:deposit_wallet,interest_wallet';
            $validationRule['schedule_times'] = 'required|integer|min:1';
            $validationRule['hours']          = 'required|integer|min:1';
        }

        $request->validate($validationRule);
    }

    public function test(){

        return 'test content goes here.';
    }


}
