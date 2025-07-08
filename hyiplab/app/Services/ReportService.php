<?php

namespace Hyiplab\Services;

use Hyiplab\Lib\CurlRequest;
use Hyiplab\Models\Invest;
use Hyiplab\Models\Transaction;
use Hyiplab\Cache\CacheManager;
use Hyiplab\Log\Logger;

class ReportService
{
    protected $apiUrl = 'https://license.viserlab.com/issue';
    private CacheManager $cache;
    private const CACHE_TTL = 3600; // 1 hour for reports

    public function __construct()
    {
        $this->cache = CacheManager::getInstance();
    }

    public function getReports()
    {
        $cacheKey = 'available_reports';
        return $this->cache->remember($cacheKey, function () {
            return [
                'transaction_report' => 'Transaction Report',
                'investment_history' => 'Investment History Report',
                'user_activity' => 'User Activity Report'
            ];
        }, 7200); // Cache for 2 hours as this rarely changes
    }

    public function submitReport(string $type, string $message)
    {
        Logger::info('Report submitted', ['type' => $type, 'message_length' => strlen($message)]);
        
        // Clear relevant cache when new report is submitted
        $this->cache->forget('available_reports');
        
        $payload = array_merge($this->getApiPayload(), [
            'req_type' => $type,
            'message'  => $message,
        ]);

        $response = CurlRequest::curlPostContent($this->apiUrl . '/add', $payload);
        $response = json_decode($response);

        if ($response->status === 'error') {
            return new \WP_Error('api_error', $response->message ?? 'Something went wrong.');
        }

        return $response->message;
    }

    protected function getApiPayload(): array
    {
        return [
            'app_name'      => hyiplab_system_details()['name'],
            'app_url'       => home_url(),
            'purchase_code' => get_option(HYIPLAB_PLUGIN_NAME . '_purchase_code', HYIPLAB_PLUGIN_NAME),
        ];
    }

    public function getTransactionReport(array $filters = [], int $paginate = 20)
    {
        $cacheKey = 'transaction_report_' . md5(serialize($filters));
        
        return $this->cache->remember($cacheKey, function () use ($filters, $paginate) {
            $query = Transaction::query();
            
            if (!empty($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }
            
            if (!empty($filters['remark'])) {
                $query->where('remark', $filters['remark']);
            }
            
            if (!empty($filters['date_from'])) {
                $query->where('created_at', '>=', $filters['date_from']);
            }
            
            if (!empty($filters['date_to'])) {
                $query->where('created_at', '<=', $filters['date_to']);
            }
            
            return $query->orderBy('id', 'desc')->paginate($paginate);
        }, self::CACHE_TTL);
    }

    public function getInvestmentHistoryReport(int $paginate = 20): array
    {
        $cacheKey = 'investment_history_report';
        
        return $this->cache->remember($cacheKey, function () use ($paginate) {
            $totalInvestments = Invest::count();
            $activeInvestments = Invest::where('status', 1)->count();
            $completedInvestments = Invest::where('status', 0)->count();
            $totalAmount = Invest::sum('amount');
            
            return [
                'total_investments' => $totalInvestments,
                'active_investments' => $activeInvestments,
                'completed_investments' => $completedInvestments,
                'total_amount' => $totalAmount,
                'recent_investments' => Invest::orderBy('id', 'desc')->limit(10)->get()
            ];
        }, self::CACHE_TTL);
    }

    public function getTransactionReportByUsername(string $username, int $paginate = 20)
    {
        $cacheKey = 'transaction_report_username_' . md5($username);
        
        return $this->cache->remember($cacheKey, function () use ($username, $paginate) {
            $user = get_user_by('login', $username);
            if (!$user) {
                return collect();
            }
            
            return Transaction::where('user_id', $user->ID)
                ->orderBy('id', 'desc')
                ->paginate($paginate);
        }, self::CACHE_TTL);
    }

    /**
     * Clear all report cache
     */
    public function clearReportCache(): void
    {
        $this->cache->forget('available_reports');
        $this->cache->forget('investment_history_report');
        Logger::info('Report cache cleared');
    }
} 