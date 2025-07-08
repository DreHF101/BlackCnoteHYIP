<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Plan;
use Hyiplab\Models\TimeSetting;
use Hyiplab\Services\PlanService;

class PlanController extends Controller
{
    protected $planService;

    public function __construct()
    {
        parent::__construct();
        $this->planService = new PlanService();
    }

    public function index()
    {
        $pageTitle = "Plans";
        $plans     = Plan::orderBy('id', 'desc')->get();
        $times     = TimeSetting::where('status', 1)->get();
        $this->view('admin/plan/index', compact('pageTitle', 'plans', 'times'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'interest_type' => 'required|integer',
            'interest'      => 'required|numeric',
            'time'          => 'required|integer'
        ]);

        try {
            $this->planService->createPlan($request->all());
            $notify[] = ['success', 'Plan added successfully'];
        } catch (\InvalidArgumentException $e) {
            $notify[] = ['error', $e->getMessage()];
        }

        return hyiplab_back($notify);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'            => 'required|integer',
            'name'          => 'required',
            'interest_type' => 'required|integer',
            'interest'      => 'required|numeric',
            'time'          => 'required|integer'
        ]);

        try {
            $this->planService->updatePlan($request->id, $request->all());
            $notify[] = ['success', 'Plan updated successfully'];
        } catch (\InvalidArgumentException $e) {
            $notify[] = ['error', $e->getMessage()];
        }

        return hyiplab_back($notify);
    }

    public function status(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        
        $this->planService->togglePlanStatus($request->id);
        $notify[] = ['success', "Plan status changed successfully"];
        
        return hyiplab_back($notify);
    }
}
