<?php

namespace Hyiplab\Services;

use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Plan;

class InvestmentPlanService
{
    public function getActivePlans()
    {
        return Plan::where('status', 1)->orderBy('id', 'DESC')->get();
    }

    public function validateInvestmentRequest(array $data): array
    {
        $validationRule = [
            'amount' => 'required|numeric',
            'plan_id' => 'required|integer',
            'wallet_type' => 'required',
            'invest_time' => 'required',
            'compound_interest' => 'numeric',
        ];
        
        $scheduleInvest = get_option('hyiplab_schedule_invest', true);
        
        if ($scheduleInvest) {
            $validationRule['invest_time'] = 'required|in:invest_now,schedule';
        }

        if ($data['invest_time'] == 'schedule') {
            $validationRule['wallet_type'] = 'required|in:deposit_wallet,interest_wallet';
            $validationRule['schedule_times'] = 'required|integer|min:1';
            $validationRule['hours'] = 'required|integer|min:1';
        }

        return $validationRule;
    }

    public function processInvestment(int $userId, array $data): array
    {
        $user = get_userdata($userId);
        $plan = Plan::where('id', $data['plan_id'])->where('status', 1)->firstOrFail();

        // Validate compound interest
        if (!empty($data['compound_interest'])) {
            if (!$plan->compound_interest) {
                throw new \InvalidArgumentException('Compound interest is not available for this plan.');
            }

            if ($plan->repeat_time && $plan->repeat_time <= $data['compound_interest']) {
                throw new \InvalidArgumentException('Compound interest times must be fewer than repeat times.');
            }
        }

        $amount = floatval($data['amount']);

        // Validate amount limits
        if ($plan->fixed_amount > 0) {
            if ($amount != $plan->fixed_amount) {
                throw new \InvalidArgumentException('Please check the investment limit');
            }
        } else {
            if ($amount < $plan->minimum || $amount > $plan->maximum) {
                throw new \InvalidArgumentException('Please check the investment limit');
            }
        }

        // Check user balance
        $wallet = sanitize_text_field($data['wallet_type']);
        if ($amount > hyiplab_balance($userId, $wallet)) {
            throw new \InvalidArgumentException('Your balance is not sufficient');
        }

        // Handle scheduled investment
        if ($data['invest_time'] == 'schedule' && get_option('hyiplab_schedule_invest', true)) {
            HyipLab::saveScheduleInvest($data, $userId);
            return ['type' => 'scheduled', 'message' => 'Invest scheduled successfully'];
        }

        // Process immediate investment
        $hyip = new HyipLab($user, $plan);
        $hyip->invest($amount, $wallet, $data['compound_interest'] ?? null);
        
        return ['type' => 'immediate', 'message' => 'Invested to plan successfully'];
    }

    public function getPlanById(int $planId): ?Plan
    {
        return Plan::where('id', $planId)->where('status', 1)->first();
    }

    public function validatePlanForInvestment(Plan $plan, array $data): void
    {
        // Validate compound interest settings
        if (!empty($data['compound_interest'])) {
            if (!$plan->compound_interest) {
                throw new \InvalidArgumentException('Compound interest is not available for this plan.');
            }

            if ($plan->repeat_time && $plan->repeat_time <= $data['compound_interest']) {
                throw new \InvalidArgumentException('Compound interest times must be fewer than repeat times.');
            }
        }

        // Validate amount
        $amount = floatval($data['amount']);
        
        if ($plan->fixed_amount > 0) {
            if ($amount != $plan->fixed_amount) {
                throw new \InvalidArgumentException('Please check the investment limit');
            }
        } else {
            if ($amount < $plan->minimum || $amount > $plan->maximum) {
                throw new \InvalidArgumentException('Please check the investment limit');
            }
        }
    }

    public function checkUserBalance(int $userId, float $amount, string $walletType): bool
    {
        return $amount <= hyiplab_balance($userId, $walletType);
    }

    public function isScheduleInvestEnabled(): bool
    {
        return (bool) get_option('hyiplab_schedule_invest', true);
    }
} 