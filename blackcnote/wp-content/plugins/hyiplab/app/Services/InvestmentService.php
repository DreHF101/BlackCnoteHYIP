<?php

namespace Hyiplab\Services;

use Hyiplab\Models\Invest;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\UserRanking;
use Hyiplab\Cache\CacheManager;

class InvestmentService
{
    private CacheManager $cache;
    private const CACHE_TTL = 1800; // 30 minutes

    public function __construct()
    {
        $this->cache = CacheManager::getInstance();
    }

    public function getUserInvestmentStats(int $userId): object
    {
        $cacheKey = "user_investment_stats_{$userId}";
        
        return $this->cache->remember($cacheKey, function () use ($userId) {
            return Invest::where('user_id', $userId)
                ->selectRaw("SUM(amount) as totalInvest")
                ->selectRaw("SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as activePlan")
                ->first();
        }, self::CACHE_TTL);
    }

    public function getUserActiveInvestments(int $userId, int $limit = 5)
    {
        $cacheKey = "user_active_investments_{$userId}_{$limit}";
        
        return $this->cache->remember($cacheKey, function () use ($userId, $limit) {
            return Invest::where('user_id', $userId)
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
        }, self::CACHE_TTL);
    }

    public function getUserTotalProfit(int $userId): float
    {
        $cacheKey = "user_total_profit_{$userId}";
        
        return $this->cache->remember($cacheKey, function () use ($userId) {
            return Transaction::where('user_id', $userId)
                ->where('remark', 'interest')
                ->sum('amount');
        }, self::CACHE_TTL);
    }

    public function getUserInvestmentChart(int $userId)
    {
        $cacheKey = "user_investment_chart_{$userId}";
        
        return $this->cache->remember($cacheKey, function () use ($userId) {
            return Invest::where('user_id', $userId)
                ->selectRaw("plan_id, SUM(amount) as investAmount")
                ->groupBy('plan_id')
                ->orderBy('investAmount', 'desc')
                ->get();
        }, self::CACHE_TTL);
    }

    public function getUserInvestmentLogs(int $userId, int $paginate = 20)
    {
        // Don't cache paginated results as they change frequently
        return Invest::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->where('status', 1)
            ->paginate($paginate);
    }

    public function getInvestmentDetails(int $investmentId, int $userId): array
    {
        $cacheKey = "investment_details_{$investmentId}_{$userId}";
        
        return $this->cache->remember($cacheKey, function () use ($investmentId, $userId) {
            $invest = Invest::where('user_id', $userId)->findOrFail($investmentId);
            $plan = get_hyiplab_plan($invest->plan_id);
            $user = get_userdata($invest->user_id);
            $transactions = Transaction::where('invest_id', $invest->id)
                ->orderBy('id', 'desc')
                ->paginate(hyiplab_paginate());

            return [
                'invest' => $invest,
                'plan' => $plan,
                'user' => $user,
                'transactions' => $transactions
            ];
        }, self::CACHE_TTL);
    }

    public function getUserRankingData(int $userId): array
    {
        if (!get_option('hyiplab_user_ranking')) {
            return null;
        }

        $cacheKey = "user_ranking_data_{$userId}";
        
        return $this->cache->remember($cacheKey, function () use ($userId) {
            $userRankingId = get_user_meta($userId, 'hyiplab_ranking_id', true) ?? 0;
            $userRankings = UserRanking::where('status', 1)->get();
            $nextRanking = UserRanking::where('status', 1)
                ->where('id', '>', $userRankingId)
                ->first();

            $totalInvests = get_user_meta($userId, 'hyiplab_total_invest', true) ?? 0;
            $teamInvests = get_user_meta($userId, 'hyiplab_team_invest', true) ?? 0;
            $activeReferrals = getViserAllReferrer($userId, true);

            return [
                'user_rankings' => $userRankings,
                'next_ranking' => $nextRanking,
                'total_invests' => $totalInvests,
                'team_invests' => $teamInvests,
                'active_referrals' => $activeReferrals,
                'user_ranking_id' => $userRankingId
            ];
        }, self::CACHE_TTL);
    }

    public function isUserRankingEnabled(): bool
    {
        $cacheKey = 'user_ranking_enabled';
        
        return $this->cache->remember($cacheKey, function () {
            return (bool) get_option('hyiplab_user_ranking');
        }, 3600); // Cache for 1 hour as this rarely changes
    }

    /**
     * Clear cache for a specific user
     */
    public function clearUserCache(int $userId): void
    {
        $this->cache->forget("user_investment_stats_{$userId}");
        $this->cache->forget("user_active_investments_{$userId}_5");
        $this->cache->forget("user_total_profit_{$userId}");
        $this->cache->forget("user_investment_chart_{$userId}");
        $this->cache->forget("user_ranking_data_{$userId}");
    }

    /**
     * Clear all investment-related cache
     */
    public function clearAllCache(): void
    {
        $this->cache->forget('user_ranking_enabled');
        // Note: User-specific cache should be cleared individually
    }
} 