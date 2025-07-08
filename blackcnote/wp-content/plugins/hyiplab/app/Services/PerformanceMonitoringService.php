<?php

namespace BlackCnote\Services;

use PDO;
use Exception;
use DateTime;
use DateTimeZone;

/**
 * Advanced Performance Monitoring Service
 * 
 * Provides comprehensive application performance monitoring (APM)
 * with automated alerting and performance analytics
 */
class PerformanceMonitoringService
{
    private PDO $db;
    private array $metrics = [];
    private array $alerts = [];
    private float $startTime;
    private array $config;
    private static ?self $instance = null;

    /**
     * Private constructor for singleton pattern
     */
    private function __construct()
    {
        global $wpdb;
        $this->db = $wpdb->dbh;
        $this->startTime = microtime(true);
        $this->loadConfig();
        $this->initializeMetrics();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load monitoring configuration
     */
    private function loadConfig(): void
    {
        $this->config = [
            'slow_query_threshold' => 1.0, // seconds
            'memory_threshold' => 80, // percentage
            'cpu_threshold' => 70, // percentage
            'error_rate_threshold' => 5, // percentage
            'response_time_threshold' => 2.0, // seconds
            'alert_email' => get_option('admin_email'),
            'enable_real_time_monitoring' => true,
            'enable_alerting' => true,
            'retention_days' => 30
        ];
    }

    /**
     * Initialize metrics collection
     */
    private function initializeMetrics(): void
    {
        $this->metrics = [
            'request_count' => 0,
            'error_count' => 0,
            'slow_queries' => 0,
            'memory_usage' => [],
            'response_times' => [],
            'database_queries' => [],
            'cache_hits' => 0,
            'cache_misses' => 0,
            'active_users' => 0,
            'peak_memory' => 0
        ];
    }

    /**
     * Start monitoring a request
     */
    public function startRequest(): void
    {
        $this->metrics['request_count']++;
        $this->startTime = microtime(true);
        
        // Record initial memory usage
        $this->metrics['memory_usage'][] = [
            'time' => microtime(true),
            'usage' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true)
        ];
    }

    /**
     * End monitoring a request
     */
    public function endRequest(): array
    {
        $endTime = microtime(true);
        $responseTime = $endTime - $this->startTime;
        
        $this->metrics['response_times'][] = $responseTime;
        
        // Record final memory usage
        $this->metrics['memory_usage'][] = [
            'time' => $endTime,
            'usage' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true)
        ];
        
        // Update peak memory
        $currentPeak = memory_get_peak_usage(true);
        if ($currentPeak > $this->metrics['peak_memory']) {
            $this->metrics['peak_memory'] = $currentPeak;
        }
        
        // Check for performance issues
        $this->checkPerformanceThresholds($responseTime);
        
        return [
            'response_time' => $responseTime,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => $currentPeak
        ];
    }

    /**
     * Monitor database query performance
     */
    public function monitorQuery(string $sql, float $executionTime): void
    {
        $this->metrics['database_queries'][] = [
            'sql' => $sql,
            'execution_time' => $executionTime,
            'timestamp' => microtime(true)
        ];
        
        // Check for slow queries
        if ($executionTime > $this->config['slow_query_threshold']) {
            $this->metrics['slow_queries']++;
            $this->logSlowQuery($sql, $executionTime);
        }
    }

    /**
     * Monitor cache performance
     */
    public function monitorCache(bool $hit): void
    {
        if ($hit) {
            $this->metrics['cache_hits']++;
        } else {
            $this->metrics['cache_misses']++;
        }
    }

    /**
     * Record an error
     */
    public function recordError(string $error, string $context = ''): void
    {
        $this->metrics['error_count']++;
        
        $errorData = [
            'error' => $error,
            'context' => $context,
            'timestamp' => microtime(true),
            'memory_usage' => memory_get_usage(true),
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
        ];
        
        $this->logError($errorData);
        $this->checkErrorThresholds();
    }

    /**
     * Check performance thresholds and trigger alerts
     */
    private function checkPerformanceThresholds(float $responseTime): void
    {
        $alerts = [];
        
        // Response time threshold
        if ($responseTime > $this->config['response_time_threshold']) {
            $alerts[] = [
                'type' => 'slow_response',
                'message' => "Response time exceeded threshold: {$responseTime}s",
                'severity' => 'warning',
                'value' => $responseTime,
                'threshold' => $this->config['response_time_threshold']
            ];
        }
        
        // Memory usage threshold
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        $memoryPercentage = $this->getMemoryPercentage($memoryUsage, $memoryLimit);
        
        if ($memoryPercentage > $this->config['memory_threshold']) {
            $alerts[] = [
                'type' => 'high_memory',
                'message' => "Memory usage exceeded threshold: {$memoryPercentage}%",
                'severity' => 'critical',
                'value' => $memoryPercentage,
                'threshold' => $this->config['memory_threshold']
            ];
        }
        
        // CPU usage check (if available)
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            if ($load[0] > $this->config['cpu_threshold']) {
                $alerts[] = [
                    'type' => 'high_cpu',
                    'message' => "CPU load exceeded threshold: {$load[0]}",
                    'severity' => 'warning',
                    'value' => $load[0],
                    'threshold' => $this->config['cpu_threshold']
                ];
            }
        }
        
        // Trigger alerts
        foreach ($alerts as $alert) {
            $this->triggerAlert($alert);
        }
    }

    /**
     * Check error rate thresholds
     */
    private function checkErrorThresholds(): void
    {
        if ($this->metrics['request_count'] > 0) {
            $errorRate = ($this->metrics['error_count'] / $this->metrics['request_count']) * 100;
            
            if ($errorRate > $this->config['error_rate_threshold']) {
                $this->triggerAlert([
                    'type' => 'high_error_rate',
                    'message' => "Error rate exceeded threshold: {$errorRate}%",
                    'severity' => 'critical',
                    'value' => $errorRate,
                    'threshold' => $this->config['error_rate_threshold']
                ]);
            }
        }
    }

    /**
     * Trigger an alert
     */
    private function triggerAlert(array $alert): void
    {
        $alert['timestamp'] = microtime(true);
        $alert['request_id'] = uniqid();
        
        $this->alerts[] = $alert;
        
        // Log alert
        $this->logAlert($alert);
        
        // Send notification if enabled
        if ($this->config['enable_alerting']) {
            $this->sendAlertNotification($alert);
        }
    }

    /**
     * Send alert notification
     */
    private function sendAlertNotification(array $alert): void
    {
        $subject = "[BlackCnote Alert] {$alert['type']} - {$alert['severity']}";
        $message = $this->formatAlertMessage($alert);
        
        // Send email
        wp_mail($this->config['alert_email'], $subject, $message);
        
        // Log to file
        error_log("Performance Alert: " . json_encode($alert));
    }

    /**
     * Format alert message
     */
    private function formatAlertMessage(array $alert): string
    {
        $message = "Performance Alert Detected\n\n";
        $message .= "Type: {$alert['type']}\n";
        $message .= "Severity: {$alert['severity']}\n";
        $message .= "Message: {$alert['message']}\n";
        $message .= "Value: {$alert['value']}\n";
        $message .= "Threshold: {$alert['threshold']}\n";
        $message .= "Timestamp: " . date('Y-m-d H:i:s', $alert['timestamp']) . "\n";
        $message .= "Request ID: {$alert['request_id']}\n\n";
        $message .= "Current Metrics:\n";
        $message .= "- Request Count: {$this->metrics['request_count']}\n";
        $message .= "- Error Count: {$this->metrics['error_count']}\n";
        $message .= "- Peak Memory: " . $this->formatBytes($this->metrics['peak_memory']) . "\n";
        
        return $message;
    }

    /**
     * Get comprehensive performance report
     */
    public function getPerformanceReport(): array
    {
        $responseTimes = $this->metrics['response_times'];
        $memoryUsage = $this->metrics['memory_usage'];
        
        $report = [
            'summary' => [
                'total_requests' => $this->metrics['request_count'],
                'total_errors' => $this->metrics['error_count'],
                'error_rate' => $this->metrics['request_count'] > 0 ? 
                    ($this->metrics['error_count'] / $this->metrics['request_count']) * 100 : 0,
                'slow_queries' => $this->metrics['slow_queries'],
                'cache_hit_rate' => $this->getCacheHitRate(),
                'peak_memory' => $this->formatBytes($this->metrics['peak_memory'])
            ],
            'response_times' => [
                'average' => !empty($responseTimes) ? array_sum($responseTimes) / count($responseTimes) : 0,
                'median' => $this->calculateMedian($responseTimes),
                'p95' => $this->calculatePercentile($responseTimes, 95),
                'p99' => $this->calculatePercentile($responseTimes, 99),
                'min' => !empty($responseTimes) ? min($responseTimes) : 0,
                'max' => !empty($responseTimes) ? max($responseTimes) : 0
            ],
            'memory_usage' => [
                'current' => $this->formatBytes(memory_get_usage(true)),
                'peak' => $this->formatBytes($this->metrics['peak_memory']),
                'limit' => ini_get('memory_limit'),
                'percentage' => $this->getMemoryPercentage(memory_get_usage(true), ini_get('memory_limit'))
            ],
            'database' => [
                'total_queries' => count($this->metrics['database_queries']),
                'slow_queries' => $this->metrics['slow_queries'],
                'average_query_time' => $this->calculateAverageQueryTime()
            ],
            'cache' => [
                'hits' => $this->metrics['cache_hits'],
                'misses' => $this->metrics['cache_misses'],
                'hit_rate' => $this->getCacheHitRate()
            ],
            'alerts' => $this->alerts,
            'timestamp' => microtime(true)
        ];
        
        return $report;
    }

    /**
     * Get real-time metrics
     */
    public function getRealTimeMetrics(): array
    {
        return [
            'current_memory' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'request_count' => $this->metrics['request_count'],
            'error_count' => $this->metrics['error_count'],
            'active_alerts' => count($this->alerts),
            'uptime' => microtime(true) - $this->startTime
        ];
    }

    /**
     * Calculate cache hit rate
     */
    private function getCacheHitRate(): float
    {
        $total = $this->metrics['cache_hits'] + $this->metrics['cache_misses'];
        return $total > 0 ? ($this->metrics['cache_hits'] / $total) * 100 : 0;
    }

    /**
     * Calculate median
     */
    private function calculateMedian(array $values): float
    {
        if (empty($values)) return 0;
        
        sort($values);
        $count = count($values);
        $middle = floor($count / 2);
        
        if ($count % 2 == 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        } else {
            return $values[$middle];
        }
    }

    /**
     * Calculate percentile
     */
    private function calculatePercentile(array $values, int $percentile): float
    {
        if (empty($values)) return 0;
        
        sort($values);
        $index = ceil(count($values) * $percentile / 100) - 1;
        return $values[$index] ?? 0;
    }

    /**
     * Calculate average query time
     */
    private function calculateAverageQueryTime(): float
    {
        if (empty($this->metrics['database_queries'])) return 0;
        
        $totalTime = array_sum(array_column($this->metrics['database_queries'], 'execution_time'));
        return $totalTime / count($this->metrics['database_queries']);
    }

    /**
     * Get memory percentage
     */
    private function getMemoryPercentage(int $usage, string $limit): float
    {
        $limitBytes = $this->parseMemoryLimit($limit);
        return $limitBytes > 0 ? ($usage / $limitBytes) * 100 : 0;
    }

    /**
     * Parse memory limit string
     */
    private function parseMemoryLimit(string $limit): int
    {
        $unit = strtolower(substr($limit, -1));
        $value = (int)substr($limit, 0, -1);
        
        switch ($unit) {
            case 'k': return $value * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'g': return $value * 1024 * 1024 * 1024;
            default: return $value;
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Log slow query
     */
    private function logSlowQuery(string $sql, float $executionTime): void
    {
        $logData = [
            'type' => 'slow_query',
            'sql' => $sql,
            'execution_time' => $executionTime,
            'timestamp' => date('Y-m-d H:i:s'),
            'memory_usage' => memory_get_usage(true)
        ];
        
        error_log("Slow Query: " . json_encode($logData));
    }

    /**
     * Log error
     */
    private function logError(array $errorData): void
    {
        error_log("Performance Error: " . json_encode($errorData));
    }

    /**
     * Log alert
     */
    private function logAlert(array $alert): void
    {
        error_log("Performance Alert: " . json_encode($alert));
    }

    /**
     * Clean up old metrics data
     */
    public function cleanupOldData(): void
    {
        $cutoffTime = microtime(true) - ($this->config['retention_days'] * 24 * 60 * 60);
        
        // Clean up old response times
        $this->metrics['response_times'] = array_filter(
            $this->metrics['response_times'],
            function($time) use ($cutoffTime) {
                return $time > $cutoffTime;
            }
        );
        
        // Clean up old memory usage data
        $this->metrics['memory_usage'] = array_filter(
            $this->metrics['memory_usage'],
            function($usage) use ($cutoffTime) {
                return $usage['time'] > $cutoffTime;
            }
        );
        
        // Clean up old database queries
        $this->metrics['database_queries'] = array_filter(
            $this->metrics['database_queries'],
            function($query) use ($cutoffTime) {
                return $query['timestamp'] > $cutoffTime;
            }
        );
    }

    /**
     * Reset metrics
     */
    public function resetMetrics(): void
    {
        $this->initializeMetrics();
        $this->alerts = [];
    }
} 