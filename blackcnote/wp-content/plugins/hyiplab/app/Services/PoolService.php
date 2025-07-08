<?php

namespace Hyiplab\Services;

use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Pool;
use Hyiplab\Models\PoolInvest;
use Hyiplab\Models\Transaction;

class PoolService
{
    public function isPoolEnabled(): bool
    {
        return (bool) get_option('hyiplab_pool');
    }

    public function getActivePools()
    {
        return Pool::orderBy('id', 'asc')
            ->where('status', 1)
            ->where('share_interest', 0)
            ->get();
    }

    public function getUserPoolInvestments(int $userId, int $paginate = 20)
    {
        return PoolInvest::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate($paginate);
    }

    public function processPoolInvestment(int $userId, array $data): PoolInvest
    {
        $pool = Pool::where('status', 1)->findOrFail($data['pool_id']);
        $user = get_userdata($userId);
        $wallet = $data['wallet_type'];
        $amount = floatval($data['amount']);

        // Validate pool availability
        if ($pool->start_date <= hyiplab_date()->now()) {
            throw new \InvalidArgumentException('The investment period for this pool has ended.');
        }

        // Validate investment limit
        if ($amount > $pool->amount - $pool->invested_amount) {
            throw new \InvalidArgumentException('Pool invest over limit!');
        }

        // Check user balance
        $depositWallet = get_user_meta($userId, 'hyiplab_deposit_wallet', true);
        $interestWallet = get_user_meta($userId, 'hyiplab_interest_wallet', true);
        
        if (($wallet == 'deposit_wallet' && $depositWallet < $amount) || 
            ($wallet == 'interest_wallet' && $interestWallet < $amount)) {
            throw new \InvalidArgumentException('Insufficient balance!');
        }

        // Get or create pool investment
        $poolInvest = PoolInvest::where('user_id', $userId)
            ->where('pool_id', $pool->id)
            ->where('status', 1)
            ->first();

        if (!$poolInvest) {
            $poolInvest = new PoolInvest();
            $poolInvest->user_id = $userId;
            $poolInvest->pool_id = $pool->id;
        }

        // Update pool investment
        $poolInvest->invest_amount += $amount;
        $poolInvest->created_at = hyiplab_date()->now();
        $poolInvest->updated_at = hyiplab_date()->now();
        $poolInvest->save();

        // Update pool invested amount
        $pool->invested_amount += $amount;
        $pool->save();

        // Update user balance
        $afterBalance = hyiplab_balance($userId, $wallet) - $amount;
        update_user_meta($userId, "hyiplab_$wallet", $afterBalance);

        // Create transaction record
        $this->createPoolTransaction($userId, $amount, $afterBalance, $pool, $wallet);

        return $poolInvest;
    }

    protected function createPoolTransaction(int $userId, float $amount, float $afterBalance, Pool $pool, string $wallet): Transaction
    {
        $trx = hyiplab_trx();
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->amount = $amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge = 0;
        $transaction->trx_type = '-';
        $transaction->details = esc_html__('Pool investment on ', HYIPLAB_PLUGIN_NAME) . $pool->name;
        $transaction->trx = $trx;
        $transaction->wallet_type = $wallet;
        $transaction->remark = 'pool_invest';
        $transaction->created_at = current_time('mysql');
        $transaction->save();

        return $transaction;
    }

    public function getPoolById(int $poolId): ?Pool
    {
        return Pool::where('status', 1)->find($poolId);
    }

    public function validatePoolInvestment(Pool $pool, float $amount): void
    {
        if ($pool->start_date <= hyiplab_date()->now()) {
            throw new \InvalidArgumentException('The investment period for this pool has ended.');
        }

        if ($amount > $pool->amount - $pool->invested_amount) {
            throw new \InvalidArgumentException('Pool invest over limit!');
        }
    }

    public function checkUserBalanceForPool(int $userId, float $amount, string $walletType): bool
    {
        $balance = get_user_meta($userId, "hyiplab_$walletType", true);
        return $balance >= $amount;
    }
} 