<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Services\InvestmentPlanService;

class PlanController extends Controller
{
    protected $investmentPlanService;

    public function __construct()
    {
        parent::__construct();
        $this->investmentPlanService = new InvestmentPlanService();
    }

    public function index()
    {
        $this->pageTitle = "Plans";
        $plans = $this->investmentPlanService->getActivePlans();
        $this->view('user/plan', compact('plans'));
    }

    public function invest(Request $request)
    {
        global $user_ID;
        
        $validationRules = $this->investmentPlanService->validateInvestmentRequest($request->all());
        $request->validate($validationRules);

        try {
            $result = $this->investmentPlanService->processInvestment($user_ID, $request->all());
            
            $notify[] = ['success', $result['message']];
            
            if ($result['type'] === 'scheduled') {
                return hyiplab_back($notify);
            } else {
                hyiplab_set_notify($notify);
                return hyiplab_redirect(hyiplab_route_link('user.invest.index'));
            }
        } catch (\InvalidArgumentException $e) {
            $notify[] = ['error', $e->getMessage()];
            hyiplab_set_notify($notify);
            return hyiplab_redirect(hyiplab_route_link('user.plan.index'));
        }
    }

    public function test()
    {
        return 'test content goes here.';
    }
}
