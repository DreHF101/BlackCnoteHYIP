<?php
/**
 * Complete Integration Example
 * 
 * This file demonstrates how to use all the new utilities together:
 * - Caching
 * - CSRF Protection
 * - Rate Limiting
 * - Logging
 * - Performance Monitoring
 * - Query Optimization
 * - Dependency Injection
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Hyiplab\Container\Application;
use Hyiplab\Cache\CacheManager;
use Hyiplab\Log\Logger;
use Hyiplab\Helpers\Csrf;
use Hyiplab\Helpers\RateLimiter;
use Hyiplab\Database\QueryOptimizer;
use Hyiplab\Services\InvestmentService;
use Hyiplab\Services\DashboardService;

// Initialize the application
$app = Application::getInstance();

// Example 1: Using Caching with Performance Monitoring
function getInvestmentStatsWithCache(): array
{
    $startTime = microtime(true);
    $cache = CacheManager::getInstance();
    
    $cacheKey = 'investment_stats_dashboard';
    $stats = $cache->remember($cacheKey, function () {
        // Simulate expensive database query
        sleep(1);
        return [
            'total_investments' => 1500,
            'total_amount' => 2500000,
            'active_investments' => 1200,
            'average_return' => 15.5
        ];
    }, 1800); // Cache for 30 minutes
    
    $executionTime = (microtime(true) - $startTime) * 1000;
    Logger::info('Investment stats retrieved', [
        'execution_time_ms' => round($executionTime, 2),
        'cache_hit' => $cache->has($cacheKey)
    ]);
    
    return $stats;
}

// Example 2: Using CSRF Protection in Forms
function renderInvestmentForm(): string
{
    $csrfToken = csrf_token();
    
    return "
    <form action='/user/invest' method='POST' class='investment-form'>
        <input type='hidden' name='csrf_token' value='{$csrfToken}'>
        
        <div class='form-group'>
            <label for='amount'>Investment Amount</label>
            <input type='number' name='amount' id='amount' required min='10' step='0.01'>
        </div>
        
        <div class='form-group'>
            <label for='plan_id'>Investment Plan</label>
            <select name='plan_id' id='plan_id' required>
                <option value=''>Select Plan</option>
                <option value='1'>Basic Plan (10% ROI)</option>
                <option value='2'>Premium Plan (15% ROI)</option>
                <option value='3'>VIP Plan (20% ROI)</option>
            </select>
        </div>
        
        <button type='submit' class='btn btn-primary'>Create Investment</button>
    </form>";
}

// Example 3: Using Rate Limiting in API Endpoints
function processInvestmentRequest(array $data): array
{
    $userId = $data['user_id'] ?? 0;
    $rateLimitKey = "investment_creation_{$userId}";
    
    // Rate limiting: max 5 investments per 5 minutes
    if (rate_limit($rateLimitKey, 5, 300)) {
        Logger::warning('Rate limit exceeded for investment creation', ['user_id' => $userId]);
        return ['success' => false, 'message' => 'Too many investment attempts. Please wait.'];
    }
    
    // CSRF validation
    if (!csrf_check($data['csrf_token'] ?? '')) {
        Logger::warning('CSRF validation failed', ['user_id' => $userId, 'ip' => $_SERVER['REMOTE_ADDR']]);
        return ['success' => false, 'message' => 'Invalid security token'];
    }
    
    try {
        // Use dependency injection to get service
        $investmentService = app(InvestmentService::class);
        
        $investment = $investmentService->createInvestment(
            $userId,
            $data['plan_id'],
            $data['amount']
        );
        
        Logger::info('Investment created successfully', [
            'user_id' => $userId,
            'investment_id' => $investment->id,
            'amount' => $data['amount']
        ]);
        
        return ['success' => true, 'investment_id' => $investment->id];
        
    } catch (\Exception $e) {
        Logger::error('Investment creation failed', [
            'user_id' => $userId,
            'error' => $e->getMessage()
        ]);
        
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Example 4: Using Query Optimization
function getOptimizedUserData(int $userId): array
{
    $queryOptimizer = new QueryOptimizer();
    
    // Optimize user query with specific fields
    $query = \Hyiplab\Models\User::query();
    $query = $queryOptimizer->selectFields($query, [
        'id', 'user_login', 'user_email', 'user_registered', 'meta_value as balance'
    ]);
    
    $user = $query->where('id', $userId)->first();
    
    if (!$user) {
        Logger::warning('User not found', ['user_id' => $userId]);
        return [];
    }
    
    return $user->toArray();
}

// Example 5: Using Caching with Cache Invalidation
function getUserDashboardData(int $userId): array
{
    $cache = CacheManager::getInstance();
    $cacheKey = "user_dashboard_{$userId}";
    
    return $cache->remember($cacheKey, function () use ($userId) {
        $dashboardService = app(DashboardService::class);
        return $dashboardService->getUserDashboardData($userId);
    }, 900); // Cache for 15 minutes
}

function updateUserProfile(int $userId, array $data): bool
{
    try {
        // Update user profile logic here
        $user = \Hyiplab\Models\User::find($userId);
        $user->update($data);
        
        // Clear user-specific cache
        $cache = CacheManager::getInstance();
        $cache->forget("user_dashboard_{$userId}");
        
        Logger::info('User profile updated', [
            'user_id' => $userId,
            'updated_fields' => array_keys($data)
        ]);
        
        return true;
        
    } catch (\Exception $e) {
        Logger::error('User profile update failed', [
            'user_id' => $userId,
            'error' => $e->getMessage()
        ]);
        
        return false;
    }
}

// Example 6: Using Performance Monitoring in Batch Operations
function processBatchInvestments(array $investments): array
{
    $startTime = microtime(true);
    $results = ['success' => 0, 'failed' => 0, 'errors' => []];
    
    foreach ($investments as $investment) {
        $itemStartTime = microtime(true);
        
        try {
            $result = processInvestmentRequest($investment);
            
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = $result['message'];
            }
            
            $itemExecutionTime = (microtime(true) - $itemStartTime) * 1000;
            Logger::debug('Batch investment processed', [
                'investment_data' => $investment,
                'execution_time_ms' => round($itemExecutionTime, 2)
            ]);
            
        } catch (\Exception $e) {
            $results['failed']++;
            $results['errors'][] = $e->getMessage();
            
            Logger::error('Batch investment processing failed', [
                'investment_data' => $investment,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    $totalExecutionTime = (microtime(true) - $startTime) * 1000;
    Logger::info('Batch investment processing completed', [
        'total_count' => count($investments),
        'success_count' => $results['success'],
        'failed_count' => $results['failed'],
        'total_execution_time_ms' => round($totalExecutionTime, 2)
    ]);
    
    return $results;
}

// Example 7: Using All Utilities in a Controller Method
class ExampleController
{
    private CacheManager $cache;
    private QueryOptimizer $queryOptimizer;
    
    public function __construct()
    {
        $this->cache = CacheManager::getInstance();
        $this->queryOptimizer = new QueryOptimizer();
    }
    
    public function getUserInvestmentSummary(int $userId): array
    {
        $startTime = microtime(true);
        
        // Rate limiting
        $rateLimitKey = "user_summary_{$userId}";
        if (rate_limit($rateLimitKey, 10, 60)) {
            Logger::warning('Rate limit exceeded for user summary', ['user_id' => $userId]);
            throw new \Exception('Too many requests. Please wait.');
        }
        
        // CSRF validation (if this is a POST request)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_check($_POST['csrf_token'] ?? '')) {
            Logger::warning('CSRF validation failed for user summary', ['user_id' => $userId]);
            throw new \Exception('Invalid security token');
        }
        
        // Use caching
        $cacheKey = "user_investment_summary_{$userId}";
        $summary = $this->cache->remember($cacheKey, function () use ($userId) {
            // Use dependency injection
            $investmentService = app(InvestmentService::class);
            
            // Use query optimization
            $query = \Hyiplab\Models\Invest::query();
            $query = $this->queryOptimizer->selectFields($query, [
                'id', 'user_id', 'amount', 'status', 'created_at'
            ]);
            
            return [
                'total_investments' => $investmentService->getUserTotalInvestments($userId),
                'active_investments' => $investmentService->getUserActiveInvestments($userId),
                'recent_investments' => $query->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->toArray()
            ];
        }, 1800); // Cache for 30 minutes
        
        $executionTime = (microtime(true) - $startTime) * 1000;
        Logger::info('User investment summary retrieved', [
            'user_id' => $userId,
            'execution_time_ms' => round($executionTime, 2),
            'cache_hit' => $this->cache->has($cacheKey)
        ]);
        
        return $summary;
    }
}

// Example 8: Using Utilities in Views
function renderUserDashboard(int $userId): string
{
    // Include CSRF meta tag
    $csrfMeta = csrf_meta();
    
    // Get cached data
    $dashboardData = getUserDashboardData($userId);
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <title>User Dashboard</title>
        {$csrfMeta}
    </head>
    <body>
        <h1>Welcome, User {$userId}</h1>
        
        <div class='dashboard-stats'>
            <h2>Investment Summary</h2>
            <p>Total Investments: {$dashboardData['total_investments']['count']}</p>
            <p>Total Amount: \${$dashboardData['total_investments']['amount']}</p>
            <p>Active Investments: {$dashboardData['active_investments']}</p>
        </div>
        
        <div class='investment-form'>
            <h2>Create New Investment</h2>
            " . renderInvestmentForm() . "
        </div>
        
        <script>
        // AJAX example with CSRF protection
        function createInvestmentAjax(formData) {
            fetch('/api/invest', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Investment created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the investment.');
            });
        }
        </script>
    </body>
    </html>";
}

// Example 9: Error Handling and Logging
function handleInvestmentError(\Exception $e, array $context = []): array
{
    $errorId = uniqid('ERR_');
    
    Logger::error('Investment error occurred', [
        'error_id' => $errorId,
        'error_message' => $e->getMessage(),
        'error_code' => $e->getCode(),
        'error_file' => $e->getFile(),
        'error_line' => $e->getLine(),
        'context' => $context,
        'user_id' => $context['user_id'] ?? null,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
    
    return [
        'success' => false,
        'error_id' => $errorId,
        'message' => 'An error occurred while processing your request. Please try again later.',
        'debug_message' => $e->getMessage() // Only in development
    ];
}

// Example 10: Cache Management
function clearUserCache(int $userId): void
{
    $cache = CacheManager::getInstance();
    
    // Clear all user-related cache
    $cache->forget("user_dashboard_{$userId}");
    $cache->forget("user_investment_summary_{$userId}");
    $cache->forget("user_investments_{$userId}");
    
    Logger::info('User cache cleared', ['user_id' => $userId]);
}

// Usage Examples
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    echo "<h1>Integration Examples</h1>";
    
    // Example 1: Get cached investment stats
    $stats = getInvestmentStatsWithCache();
    echo "<h2>Investment Stats (Cached)</h2>";
    echo "<pre>" . print_r($stats, true) . "</pre>";
    
    // Example 2: Render investment form
    echo "<h2>Investment Form (CSRF Protected)</h2>";
    echo renderInvestmentForm();
    
    // Example 3: Process investment request
    echo "<h2>Investment Request Processing</h2>";
    $result = processInvestmentRequest([
        'user_id' => 1,
        'plan_id' => 1,
        'amount' => 100,
        'csrf_token' => csrf_token()
    ]);
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    // Example 4: Get optimized user data
    echo "<h2>Optimized User Data</h2>";
    $userData = getOptimizedUserData(1);
    echo "<pre>" . print_r($userData, true) . "</pre>";
    
    // Example 5: Controller example
    echo "<h2>Controller Example</h2>";
    $controller = new ExampleController();
    try {
        $summary = $controller->getUserInvestmentSummary(1);
        echo "<pre>" . print_r($summary, true) . "</pre>";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?> 