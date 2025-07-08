<?php

namespace Hyiplab\Services;

use Hyiplab\Models\Staking;
use Hyiplab\Models\StakingInvest;
use Hyiplab\Models\Transaction;

class StakingService
{
    public function createStakingInvestment(int $userId, int $stakingId, float $amount, string $wallet): StakingInvest
    {
        $staking = Staking::where('status', 1)->findOrFail($stakingId);
        $interest = $amount * $staking->interest_percent / 100;

        $stakingInvest = new StakingInvest();
        $stakingInvest->user_id = $userId;
        $stakingInvest->staking_id = $staking->id;
        $stakingInvest->invest_amount = $amount;
        $stakingInvest->interest = $interest;
        $stakingInvest->end_at = now()->addDays($staking->days);
        $stakingInvest->status = 1; // Active
        $stakingInvest->save();

        $this->recordTransaction($userId, $staking, $amount, $wallet);

        return $stakingInvest;
    }

    protected function recordTransaction(int $userId, Staking $staking, float $amount, string $wallet): void
    {
        $afterBalance = hyiplab_balance_update($userId, -$amount, $wallet);

        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->amount = $amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge = 0;
        $transaction->trx_type = '-';
        $transaction->details = sprintf(
            '%s %d days %d%%',
            __('Staking investment on', HYIPLAB_PLUGIN_NAME),
            $staking->days,
            $staking->interest_percent
        );
        $transaction->trx = hyiplab_trx();
        $transaction->wallet_type = $wallet;
        $transaction->remark = 'staking_invest';
        $transaction->save();
    }
} 