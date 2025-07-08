<?php

namespace Hyiplab\Database;

use Hyiplab\Cache\CacheManager;

class QueryOptimizer
{
    private CacheManager $cache;
    private array $queryStats = [];
    private bool $enabled = true;

    public function __construct()
    {
        $this->cache = CacheManager::getInstance();
    }

    /**
     * Enable or disable query optimization
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Check if query optimization is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Optimize a query with caching and eager loading
     */
    public function optimize($query, string $cacheKey = null, int $ttl = 1800)
    {
        if (!$this->enabled) {
            return $query;
        }

        // Add query statistics
        $this->addQueryStat($cacheKey ?? 'unknown');

        // If cache key is provided, try to get from cache
        if ($cacheKey && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Execute query and cache result
        $result = $query->get();
        
        if ($cacheKey) {
            $this->cache->put($cacheKey, $result, $ttl);
        }

        return $result;
    }

    /**
     * Optimize paginated query
     */
    public function optimizePaginated($query, int $perPage = 20, string $cacheKey = null)
    {
        if (!$this->enabled) {
            return $query->paginate($perPage);
        }

        // Don't cache paginated results as they change frequently
        $this->addQueryStat($cacheKey ?? 'paginated');
        
        return $query->paginate($perPage);
    }

    /**
     * Optimize with eager loading
     */
    public function withRelations($query, array $relations)
    {
        if (!$this->enabled) {
            return $query->with($relations);
        }

        return $query->with($relations);
    }

    /**
     * Optimize select fields
     */
    public function selectFields($query, array $fields)
    {
        if (!$this->enabled) {
            return $query->select($fields);
        }

        return $query->select($fields);
    }

    /**
     * Add database indexes recommendations
     */
    public function getIndexRecommendations(): array
    {
        return [
            'users' => [
                'user_login' => 'INDEX',
                'user_email' => 'UNIQUE INDEX',
                'user_registered' => 'INDEX'
            ],
            'invests' => [
                'user_id' => 'INDEX',
                'plan_id' => 'INDEX',
                'status' => 'INDEX',
                'user_id,status' => 'COMPOSITE INDEX',
                'created_at' => 'INDEX'
            ],
            'transactions' => [
                'user_id' => 'INDEX',
                'trx' => 'UNIQUE INDEX',
                'remark' => 'INDEX',
                'user_id,remark' => 'COMPOSITE INDEX',
                'created_at' => 'INDEX'
            ],
            'withdrawals' => [
                'user_id' => 'INDEX',
                'method_id' => 'INDEX',
                'status' => 'INDEX',
                'trx' => 'UNIQUE INDEX',
                'user_id,status' => 'COMPOSITE INDEX',
                'created_at' => 'INDEX'
            ],
            'deposits' => [
                'user_id' => 'INDEX',
                'method_code' => 'INDEX',
                'status' => 'INDEX',
                'trx' => 'UNIQUE INDEX',
                'user_id,status' => 'COMPOSITE INDEX',
                'created_at' => 'INDEX'
            ],
            'support_tickets' => [
                'user_id' => 'INDEX',
                'status' => 'INDEX',
                'priority' => 'INDEX',
                'ticket_number' => 'UNIQUE INDEX',
                'user_id,status' => 'COMPOSITE INDEX',
                'created_at' => 'INDEX'
            ]
        ];
    }

    /**
     * Get query performance statistics
     */
    public function getQueryStats(): array
    {
        return $this->queryStats;
    }

    /**
     * Clear query statistics
     */
    public function clearQueryStats(): void
    {
        $this->queryStats = [];
    }

    /**
     * Add query statistic
     */
    private function addQueryStat(string $queryType): void
    {
        if (!isset($this->queryStats[$queryType])) {
            $this->queryStats[$queryType] = 0;
        }
        $this->queryStats[$queryType]++;
    }

    /**
     * Generate SQL for recommended indexes
     */
    public function generateIndexSQL(): array
    {
        $recommendations = $this->getIndexRecommendations();
        $sql = [];

        foreach ($recommendations as $table => $indexes) {
            foreach ($indexes as $columns => $type) {
                $columnList = is_array($columns) ? implode(',', $columns) : $columns;
                $indexName = "idx_{$table}_{$columnList}";
                $indexName = str_replace(',', '_', $indexName);
                
                if ($type === 'UNIQUE INDEX') {
                    $sql[] = "CREATE UNIQUE INDEX {$indexName} ON {$table} ({$columnList});";
                } else {
                    $sql[] = "CREATE INDEX {$indexName} ON {$table} ({$columnList});";
                }
            }
        }

        return $sql;
    }

    /**
     * Optimize table structure
     */
    public function optimizeTableStructure(): array
    {
        return [
            'recommendations' => [
                'Use appropriate data types (INT, VARCHAR, TEXT, etc.)',
                'Set proper field lengths for VARCHAR fields',
                'Use NOT NULL constraints where appropriate',
                'Add foreign key constraints for referential integrity',
                'Use ENUM for status fields with limited values',
                'Consider partitioning for large tables',
                'Use appropriate storage engines (InnoDB for transactions)'
            ],
            'data_types' => [
                'user_id' => 'INT UNSIGNED',
                'amount' => 'DECIMAL(15,2)',
                'status' => 'TINYINT',
                'email' => 'VARCHAR(255)',
                'phone' => 'VARCHAR(20)',
                'address' => 'TEXT',
                'created_at' => 'TIMESTAMP',
                'updated_at' => 'TIMESTAMP'
            ]
        ];
    }

    /**
     * Get slow query recommendations
     */
    public function getSlowQueryRecommendations(): array
    {
        return [
            'Avoid SELECT *' => 'Only select the columns you need',
            'Use LIMIT' => 'Limit the number of rows returned',
            'Use proper WHERE clauses' => 'Add conditions to reduce result set',
            'Use indexes' => 'Ensure queries use indexed columns',
            'Avoid subqueries' => 'Use JOINs instead when possible',
            'Use EXPLAIN' => 'Analyze query execution plans',
            'Consider pagination' => 'For large result sets',
            'Use caching' => 'Cache frequently accessed data',
            'Optimize JOINs' => 'Use appropriate JOIN types',
            'Avoid ORDER BY on large datasets' => 'Consider sorting in application'
        ];
    }

    /**
     * Monitor query performance
     */
    public function monitorQuery($query, string $queryName = 'unknown'): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $result = $query->get();

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;

        return [
            'query_name' => $queryName,
            'execution_time_ms' => round($executionTime, 2),
            'memory_usage_bytes' => $memoryUsage,
            'result_count' => count($result),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

/**
 * Global helper function for query optimization
 */
if (!function_exists('optimize_query')) {
    function optimize_query($query, string $cacheKey = null, int $ttl = 1800)
    {
        $optimizer = new QueryOptimizer();
        return $optimizer->optimize($query, $cacheKey, $ttl);
    }
}

if (!function_exists('optimize_paginated')) {
    function optimize_paginated($query, int $perPage = 20, string $cacheKey = null)
    {
        $optimizer = new QueryOptimizer();
        return $optimizer->optimizePaginated($query, $perPage, $cacheKey);
    }
} 