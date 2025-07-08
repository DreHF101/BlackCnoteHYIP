<?php

namespace Hyiplab\Services;

use Hyiplab\Lib\FormProcessor;
use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Form;
use Hyiplab\Models\Invest;
use Hyiplab\Models\PromotionalTool;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\Withdrawal;
use Carbon\Carbon;
use Hyiplab\Cache\CacheManager;
use Hyiplab\Models\User;
use Hyiplab\Models\Kyc;
use Hyiplab\Log\Logger;

class DashboardService
{
    private CacheManager $cache;
    private const CACHE_TTL = 1800; // 30 minutes

    public function __construct()
    {
        $this->cache = CacheManager::getInstance();
    }

    public function getDashboardData(): array
    {
        $startTime = microtime(true);
        
        $cacheKey = 'dashboard_data';
        $data = $this->cache->remember($cacheKey, function () {
            return [
                'total_users' => $this->getTotalUsers(),
                'total_investments' => $this->getTotalInvestments(),
                'total_deposits' => $this->getTotalDeposits(),
                'total_withdrawals' => $this->getTotalWithdrawals(),
                'recent_transactions' => $this->getRecentTransactions(),
                'investment_stats' => $this->getInvestmentStats(),
                'user_stats' => $this->getUserStats()
            ];
        }, self::CACHE_TTL);

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('Dashboard data retrieved', [
            'execution_time_ms' => round($executionTime, 2),
            'cache_hit' => $this->cache->has($cacheKey)
        ]);

        return $data;
    }

    public function getUserDashboardData(int $userId): array
    {
        $startTime = microtime(true);
        
        $cacheKey = "user_dashboard_{$userId}";
        $data = $this->cache->remember($cacheKey, function () use ($userId) {
            return [
                'total_investments' => $this->getUserTotalInvestments($userId),
                'active_investments' => $this->getUserActiveInvestments($userId),
                'total_deposits' => $this->getUserTotalDeposits($userId),
                'total_withdrawals' => $this->getUserTotalWithdrawals($userId),
                'recent_transactions' => $this->getUserRecentTransactions($userId),
                'investment_history' => $this->getUserInvestmentHistory($userId),
                'kyc_status' => $this->getUserKycStatus($userId)
            ];
        }, self::CACHE_TTL);

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('User dashboard data retrieved', [
            'user_id' => $userId,
            'execution_time_ms' => round($executionTime, 2),
            'cache_hit' => $this->cache->has($cacheKey)
        ]);

        return $data;
    }

    public function getKycData(int $userId): array
    {
        $startTime = microtime(true);
        
        $kyc = Kyc::where('user_id', $userId)->first();
        
        $data = [
            'kyc' => $kyc,
            'status' => $kyc ? $kyc->status : 0,
            'submitted' => (bool) $kyc
        ];

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('KYC data retrieved', [
            'user_id' => $userId,
            'kyc_exists' => (bool) $kyc,
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $data;
    }

    public function submitKyc(int $userId, array $data): Kyc
    {
        $startTime = microtime(true);
        
        // Rate limiting for KYC submission
        $rateLimitKey = "kyc_submission_{$userId}";
        if (rate_limit($rateLimitKey, 3, 3600)) { // Max 3 submissions per hour
            throw new \InvalidArgumentException('Too many KYC submission attempts. Please wait before trying again.');
        }

        $kyc = Kyc::where('user_id', $userId)->first();
        if (!$kyc) {
            $kyc = new Kyc();
            $kyc->user_id = $userId;
        }

        $kyc->first_name = sanitize_text_field($data['first_name']);
        $kyc->last_name = sanitize_text_field($data['last_name']);
        $kyc->email = sanitize_email($data['email']);
        $kyc->phone = sanitize_text_field($data['phone']);
        $kyc->country = sanitize_text_field($data['country']);
        $kyc->city = sanitize_text_field($data['city']);
        $kyc->zip = sanitize_text_field($data['zip']);
        $kyc->address = sanitize_textarea_field($data['address']);
        $kyc->status = 0; // Pending
        $kyc->save();

        // Clear user dashboard cache
        $this->cache->forget("user_dashboard_{$userId}");

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('KYC submitted', [
            'user_id' => $userId,
            'kyc_id' => $kyc->id,
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $kyc;
    }

    public function getPromotionalBanners(): array
    {
        $startTime = microtime(true);
        
        $cacheKey = 'promotional_banners';
        $banners = $this->cache->remember($cacheKey, function () {
            return PromotionalTool::where('status', 1)
                ->where('type', 'banner')
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();
        }, 3600); // Cache for 1 hour

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('Promotional banners retrieved', [
            'count' => count($banners),
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $banners;
    }

    public function downloadFile(string $filePath): array
    {
        $startTime = microtime(true);
        
        if (!file_exists($filePath)) {
            Logger::warning('File download attempted for non-existent file', ['file_path' => $filePath]);
            throw new \InvalidArgumentException('File not found');
        }

        $fileInfo = [
            'path' => $filePath,
            'size' => filesize($filePath),
            'mime_type' => mime_content_type($filePath),
            'filename' => basename($filePath)
        ];

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('File download prepared', [
            'file_path' => $filePath,
            'file_size' => $fileInfo['size'],
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $fileInfo;
    }

    // Private helper methods with performance monitoring
    private function getTotalUsers(): int
    {
        $startTime = microtime(true);
        $count = User::count();
        $executionTime = (microtime(true) - $startTime) * 1000;
        
        Logger::debug('Total users count retrieved', [
            'count' => $count,
            'execution_time_ms' => round($executionTime, 2)
        ]);
        
        return $count;
    }

    private function getTotalInvestments(): array
    {
        $startTime = microtime(true);
        
        $data = [
            'count' => Invest::count(),
            'amount' => Invest::sum('amount'),
            'active' => Invest::where('status', 1)->count()
        ];

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::debug('Total investments retrieved', [
            'count' => $data['count'],
            'amount' => $data['amount'],
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $data;
    }

    private function getTotalDeposits(): array
    {
        $startTime = microtime(true);
        
        $data = [
            'count' => Deposit::count(),
            'amount' => Deposit::sum('amount'),
            'pending' => Deposit::where('status', 2)->count()
        ];

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::debug('Total deposits retrieved', [
            'count' => $data['count'],
            'amount' => $data['amount'],
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $data;
    }

    private function getTotalWithdrawals(): array
    {
        $startTime = microtime(true);
        
        $data = [
            'count' => Withdrawal::count(),
            'amount' => Withdrawal::sum('amount'),
            'pending' => Withdrawal::where('status', 2)->count()
        ];

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::debug('Total withdrawals retrieved', [
            'count' => $data['count'],
            'amount' => $data['amount'],
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $data;
    }

    private function getRecentTransactions(int $limit = 10): array
    {
        $startTime = microtime(true);
        
        $transactions = Transaction::orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::debug('Recent transactions retrieved', [
            'count' => count($transactions),
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $transactions;
    }

    private function getInvestmentStats(): array
    {
        $startTime = microtime(true);
        
        $stats = [
            'total_invested' => Invest::sum('amount'),
            'total_paid' => Invest::sum('paid'),
            'active_investments' => Invest::where('status', 1)->count(),
            'completed_investments' => Invest::where('status', 0)->count()
        ];

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::debug('Investment stats retrieved', [
            'total_invested' => $stats['total_invested'],
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $stats;
    }

    private function getUserStats(): array
    {
        $startTime = microtime(true);
        
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::where('email_verified', 1)->count(),
            'kyc_submitted' => Kyc::count(),
            'kyc_approved' => Kyc::where('status', 1)->count()
        ];

        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::debug('User stats retrieved', [
            'total_users' => $stats['total_users'],
            'execution_time_ms' => round($executionTime, 2)
        ]);

        return $stats;
    }

    // User-specific methods
    private function getUserTotalInvestments(int $userId): array
    {
        return [
            'count' => Invest::where('user_id', $userId)->count(),
            'amount' => Invest::where('user_id', $userId)->sum('amount')
        ];
    }

    private function getUserActiveInvestments(int $userId): int
    {
        return Invest::where('user_id', $userId)->where('status', 1)->count();
    }

    private function getUserTotalDeposits(int $userId): array
    {
        return [
            'count' => Deposit::where('user_id', $userId)->count(),
            'amount' => Deposit::where('user_id', $userId)->sum('amount')
        ];
    }

    private function getUserTotalWithdrawals(int $userId): array
    {
        return [
            'count' => Withdrawal::where('user_id', $userId)->count(),
            'amount' => Withdrawal::where('user_id', $userId)->sum('amount')
        ];
    }

    private function getUserRecentTransactions(int $userId, int $limit = 10): array
    {
        return Transaction::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getUserInvestmentHistory(int $userId, int $limit = 10): array
    {
        return Invest::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getUserKycStatus(int $userId): array
    {
        $kyc = Kyc::where('user_id', $userId)->first();
        return [
            'submitted' => (bool) $kyc,
            'status' => $kyc ? $kyc->status : 0
        ];
    }

    /**
     * Clear all dashboard cache
     */
    public function clearDashboardCache(): void
    {
        $this->cache->forget('dashboard_data');
        $this->cache->forget('promotional_banners');
        Logger::info('Dashboard cache cleared');
    }

    /**
     * Clear user-specific cache
     */
    public function clearUserCache(int $userId): void
    {
        $this->cache->forget("user_dashboard_{$userId}");
        Logger::info('User dashboard cache cleared', ['user_id' => $userId]);
    }
} 