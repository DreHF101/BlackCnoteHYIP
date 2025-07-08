<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Invest;
use Hyiplab\Models\Plan;
use Hyiplab\Services\InvestmentService;
use Hyiplab\Container\Application;
use Hyiplab\Helpers\Csrf;
use Hyiplab\Helpers\RateLimiter;
use Hyiplab\Log\Logger;
use Hyiplab\Cache\CacheManager;

class InvestController extends Controller
{
    protected InvestmentService $investmentService;
    private CacheManager $cache;

    public function __construct()
    {
        parent::__construct();
        $this->investmentService = app(InvestmentService::class);
        $this->cache = CacheManager::getInstance();
    }

    public function index()
    {
        $startTime = microtime(true);
        
        $userId = get_current_user_id();
        $cacheKey = "user_investments_{$userId}";
        
        $data = $this->cache->remember($cacheKey, function () use ($userId) {
            return [
                'investments' => $this->investmentService->getUserInvestments($userId),
                'plans' => $this->investmentService->getAvailablePlans(),
                'stats' => $this->investmentService->getUserInvestmentStats($userId)
            ];
        }, 900); // Cache for 15 minutes

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('User investments page loaded', [
            'user_id' => $userId,
            'execution_time_ms' => round($executionTime, 2),
            'cache_hit' => $this->cache->has($cacheKey)
        ]);

        $pageTitle = 'My Investments';
        return $this->view('user/invest/index', compact('pageTitle', 'data'));
    }

    public function invest(Request $request)
    {
        $startTime = microtime(true);
        $userId = get_current_user_id();

        // CSRF Protection
        if (!csrf_check($request->csrf_token)) {
            Logger::warning('CSRF token validation failed for investment', [
                'user_id' => $userId,
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            $notify[] = ['error', 'Invalid security token'];
            return hyiplab_back($notify);
        }

        // Rate Limiting
        $rateLimitKey = "investment_creation_{$userId}";
        if (rate_limit($rateLimitKey, 5, 300)) { // Max 5 investments per 5 minutes
            Logger::warning('Rate limit exceeded for investment creation', ['user_id' => $userId]);
            $notify[] = ['error', 'Too many investment attempts. Please wait before trying again.'];
            return hyiplab_back($notify);
        }

        $request->validate([
            'plan_id' => 'required|integer',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $investment = $this->investmentService->createInvestment(
                $userId,
                $request->plan_id,
                $request->amount
            );

            // Clear user investment cache
            $this->cache->forget("user_investments_{$userId}");

            $executionTime = (microtime(true) - $startTime) * 1000;
            Logger::info('Investment created successfully', [
                'user_id' => $userId,
                'investment_id' => $investment->id,
                'plan_id' => $request->plan_id,
                'amount' => $request->amount,
                'execution_time_ms' => round($executionTime, 2)
            ]);

            $notify[] = ['success', 'Investment created successfully'];
        } catch (\Exception $e) {
            $executionTime = (microtime(true) - $startTime) * 1000;
            Logger::error('Investment creation failed', [
                'user_id' => $userId,
                'plan_id' => $request->plan_id,
                'amount' => $request->amount,
                'error' => $e->getMessage(),
                'execution_time_ms' => round($executionTime, 2)
            ]);

            $notify[] = ['error', 'Investment creation failed: ' . $e->getMessage()];
        }

        return hyiplab_back($notify);
    }

    public function details(Request $request)
    {
        $startTime = microtime(true);
        $userId = get_current_user_id();

        $request->validate([
            'id' => 'required|integer'
        ]);

        try {
            $cacheKey = "investment_details_{$request->id}_{$userId}";
            $investment = $this->cache->remember($cacheKey, function () use ($request, $userId) {
                return $this->investmentService->getInvestmentDetails($request->id, $userId);
            }, 1800); // Cache for 30 minutes

            $pageTitle = 'Investment Details';
            
            $executionTime = (microtime(true) - $startTime) * 1000;
            Logger::info('Investment details retrieved', [
                'user_id' => $userId,
                'investment_id' => $request->id,
                'execution_time_ms' => round($executionTime, 2)
            ]);

            return $this->view('user/invest/details', compact('pageTitle', 'investment'));
        } catch (\Exception $e) {
            Logger::error('Investment details retrieval failed', [
                'user_id' => $userId,
                'investment_id' => $request->id,
                'error' => $e->getMessage()
            ]);

            $notify[] = ['error', 'Investment not found'];
            return hyiplab_back($notify);
        }
    }

    public function history(Request $request)
    {
        $startTime = microtime(true);
        $userId = get_current_user_id();

        $filters = [];
        if ($request->status) {
            $filters['status'] = $request->status;
        }

        $cacheKey = "investment_history_{$userId}_" . md5(serialize($filters));
        $history = $this->cache->remember($cacheKey, function () use ($userId, $filters) {
            return $this->investmentService->getUserInvestmentHistory($userId, $filters, hyiplab_paginate());
        }, 900); // Cache for 15 minutes

        $pageTitle = 'Investment History';
        
        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('Investment history retrieved', [
            'user_id' => $userId,
            'filters' => $filters,
            'count' => $history->count(),
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $this->view('user/invest/history', compact('pageTitle', 'history'));
    }

    public function ranking()
    {
        $startTime = microtime(true);
        
        $cacheKey = 'investment_rankings';
        $rankings = $this->cache->remember($cacheKey, function () {
            return $this->investmentService->getInvestmentRankings();
        }, 3600); // Cache for 1 hour

        $pageTitle = 'Investment Rankings';
        
        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('Investment rankings retrieved', [
            'count' => count($rankings),
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $this->view('user/invest/ranking', compact('pageTitle', 'rankings'));
    }

    public function cancel(Request $request)
    {
        $startTime = microtime(true);
        $userId = get_current_user_id();

        // CSRF Protection
        if (!csrf_check($request->csrf_token)) {
            Logger::warning('CSRF token validation failed for investment cancellation', [
                'user_id' => $userId,
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            $notify[] = ['error', 'Invalid security token'];
            return hyiplab_back($notify);
        }

        // Rate Limiting
        $rateLimitKey = "investment_cancellation_{$userId}";
        if (rate_limit($rateLimitKey, 3, 300)) { // Max 3 cancellations per 5 minutes
            Logger::warning('Rate limit exceeded for investment cancellation', ['user_id' => $userId]);
            $notify[] = ['error', 'Too many cancellation attempts. Please wait before trying again.'];
            return hyiplab_back($notify);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        try {
            $this->investmentService->cancelInvestment($request->id, $userId);

            // Clear user investment cache
            $this->cache->forget("user_investments_{$userId}");
            $this->cache->forget("investment_details_{$request->id}_{$userId}");

            $executionTime = (microtime(true) - $startTime) * 1000;
            Logger::info('Investment cancelled successfully', [
                'user_id' => $userId,
                'investment_id' => $request->id,
                'execution_time_ms' => round($executionTime, 2)
            ]);

            $notify[] = ['success', 'Investment cancelled successfully'];
        } catch (\Exception $e) {
            $executionTime = (microtime(true) - $startTime) * 1000;
            Logger::error('Investment cancellation failed', [
                'user_id' => $userId,
                'investment_id' => $request->id,
                'error' => $e->getMessage(),
                'execution_time_ms' => round($executionTime, 2)
            ]);

            $notify[] = ['error', 'Investment cancellation failed: ' . $e->getMessage()];
        }

        return hyiplab_back($notify);
    }

    /**
     * Clear user investment cache
     */
    public function clearCache(int $userId): void
    {
        $this->cache->forget("user_investments_{$userId}");
        $this->cache->forget("investment_history_{$userId}_*");
        Logger::info('User investment cache cleared', ['user_id' => $userId]);
    }
}
