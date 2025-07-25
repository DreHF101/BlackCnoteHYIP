<?php

namespace Hyiplab\Services;

use Hyiplab\Models\Plan;

class PlanService
{
    public function createPlan(array $data): Plan
    {
        $this->validatePlanData($data);

        $plan = new Plan();
        $this->fillPlanData($plan, $data);
        $plan->save();

        return $plan;
    }

    public function updatePlan(int $planId, array $data): Plan
    {
        $this->validatePlanData($data);

        $plan = Plan::findOrFail($planId);
        $this->fillPlanData($plan, $data);
        $plan->save();

        return $plan;
    }

    public function togglePlanStatus(int $planId): Plan
    {
        $plan = Plan::findOrFail($planId);
        $plan->status = !$plan->status;
        $plan->save();

        return $plan;
    }

    protected function validatePlanData(array $data): void
    {
        if (!empty($data['compound_interest']) && 
            ((empty($data['capital_back']) && empty($data['return_type'])) || $data['interest_type'] == 2)) {
            throw new \InvalidArgumentException('When compound interest is enabled, capital back and return type are required.');
        }

        if (!empty($data['hold_capital']) && empty($data['capital_back'])) {
            throw new \InvalidArgumentException('When hold capital is enabled, capital back is required.');
        }
    }

    protected function fillPlanData(Plan $plan, array $data): void
    {
        $plan->name = sanitize_text_field($data['name']);
        $plan->minimum = !empty($data['minimum']) ? floatval($data['minimum']) : 0;
        $plan->maximum = !empty($data['maximum']) ? floatval($data['maximum']) : 0;
        $plan->fixed_amount = !empty($data['amount']) ? floatval($data['amount']) : 0;
        $plan->interest = floatval($data['interest']);
        $plan->interest_type = $data['interest_type'] == 1 ? 1 : 0;
        $plan->time_setting_id = intval($data['time']);
        $plan->capital_back = !empty($data['capital_back']) ? intval($data['capital_back']) : 0;
        $plan->lifetime = !empty($data['return_type']) && $data['return_type'] == 1 ? 1 : 0;
        $plan->compound_interest = !empty($data['compound_interest']) ? 1 : 0;
        $plan->hold_capital = !empty($data['hold_capital']) ? 1 : 0;
        $plan->featured = !empty($data['featured']) ? 1 : 0;
        $plan->repeat_time = !empty($data['repeat_time']) ? intval($data['repeat_time']) : 0;
    }
}
