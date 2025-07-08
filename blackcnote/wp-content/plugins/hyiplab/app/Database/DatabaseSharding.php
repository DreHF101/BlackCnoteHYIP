<?php

namespace BlackCnote\Database;

use PDO;
use PDOException;
use Exception;

/**
 * Database Sharding Manager
 * 
 * Handles horizontal database scaling through sharding
 * Distributes data across multiple database instances
 */
class DatabaseSharding
{
    private array $shards = [];
    private array $shardConfigs = [];
    private string $currentShard = 'default';
    private static ?self $instance = null;

    /**
     * Private constructor for singleton pattern
     */
    private function __construct()
    {
        $this->initializeShards();
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
     * Initialize shard configurations
     */
    private function initializeShards(): void
    {
        $this->shardConfigs = [
            'shard_0' => [
                'host' => 'localhost',
                'port' => 3306,
                'database' => 'blackcnote_shard_0',
                'username' => 'root',
                'password' => '',
                'weight' => 1
            ],
            'shard_1' => [
                'host' => 'localhost',
                'port' => 3307,
                'database' => 'blackcnote_shard_1',
                'username' => 'root',
                'password' => '',
                'weight' => 1
            ],
            'shard_2' => [
                'host' => 'localhost',
                'port' => 3308,
                'database' => 'blackcnote_shard_2',
                'username' => 'root',
                'password' => '',
                'weight' => 1
            ]
        ];
    }

    /**
     * Get shard based on user ID (consistent hashing)
     */
    public function getShardForUser(int $userId): string
    {
        $hash = crc32($userId);
        $shardCount = count($this->shardConfigs);
        $shardIndex = $hash % $shardCount;
        
        return 'shard_' . $shardIndex;
    }

    /**
     * Get shard based on table and key
     */
    public function getShardForTable(string $table, $key = null): string
    {
        // User-related tables
        if (in_array($table, ['users', 'user_meta', 'user_sessions', 'user_activity'])) {
            if ($key) {
                return $this->getShardForUser((int)$key);
            }
            return 'shard_0'; // Default for user tables
        }
        
        // Transaction-related tables
        if (in_array($table, ['deposits', 'withdrawals', 'transactions'])) {
            if ($key) {
                return $this->getShardForUser((int)$key);
            }
            return 'shard_1'; // Default for transaction tables
        }
        
        // Content-related tables
        if (in_array($table, ['posts', 'pages', 'comments', 'options'])) {
            return 'shard_2'; // Default for content tables
        }
        
        return 'shard_0'; // Default fallback
    }

    /**
     * Get database connection for specific shard
     */
    public function getConnection(string $shardName = null): PDO
    {
        if ($shardName === null) {
            $shardName = $this->currentShard;
        }

        if (!isset($this->shards[$shardName])) {
            $this->connectToShard($shardName);
        }

        return $this->shards[$shardName];
    }

    /**
     * Connect to a specific shard
     */
    private function connectToShard(string $shardName): void
    {
        if (!isset($this->shardConfigs[$shardName])) {
            throw new Exception("Shard configuration not found: {$shardName}");
        }

        $config = $this->shardConfigs[$shardName];
        
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
            
            $this->shards[$shardName] = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            error_log("Failed to connect to shard {$shardName}: " . $e->getMessage());
            throw new Exception("Database connection failed for shard: {$shardName}");
        }
    }

    /**
     * Execute query on specific shard
     */
    public function executeOnShard(string $shardName, string $sql, array $params = []): mixed
    {
        $connection = $this->getConnection($shardName);
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        
        return $stmt;
    }

    /**
     * Execute query across all shards
     */
    public function executeOnAllShards(string $sql, array $params = []): array
    {
        $results = [];
        
        foreach (array_keys($this->shardConfigs) as $shardName) {
            try {
                $stmt = $this->executeOnShard($shardName, $sql, $params);
                $results[$shardName] = $stmt->fetchAll();
            } catch (Exception $e) {
                error_log("Error executing on shard {$shardName}: " . $e->getMessage());
                $results[$shardName] = [];
            }
        }
        
        return $results;
    }

    /**
     * Insert data into appropriate shard
     */
    public function insert(string $table, array $data, $userId = null): int
    {
        $shardName = $this->getShardForTable($table, $userId);
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $connection = $this->getConnection($shardName);
        $stmt = $connection->prepare($sql);
        $stmt->execute($data);
        
        return (int)$connection->lastInsertId();
    }

    /**
     * Select data from appropriate shard
     */
    public function select(string $table, array $conditions = [], $userId = null): array
    {
        $shardName = $this->getShardForTable($table, $userId);
        
        $sql = "SELECT * FROM {$table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $stmt = $this->executeOnShard($shardName, $sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Update data in appropriate shard
     */
    public function update(string $table, array $data, array $conditions, $userId = null): int
    {
        $shardName = $this->getShardForTable($table, $userId);
        
        $setClause = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClause[] = "{$column} = :set_{$column}";
            $params["set_{$column}"] = $value;
        }
        
        $whereClause = [];
        foreach ($conditions as $column => $value) {
            $whereClause[] = "{$column} = :where_{$column}";
            $params["where_{$column}"] = $value;
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $setClause) . " WHERE " . implode(' AND ', $whereClause);
        
        $stmt = $this->executeOnShard($shardName, $sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Delete data from appropriate shard
     */
    public function delete(string $table, array $conditions, $userId = null): int
    {
        $shardName = $this->getShardForTable($table, $userId);
        
        $sql = "DELETE FROM {$table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $stmt = $this->executeOnShard($shardName, $sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Get shard health status
     */
    public function getShardHealth(): array
    {
        $health = [];
        
        foreach (array_keys($this->shardConfigs) as $shardName) {
            try {
                $connection = $this->getConnection($shardName);
                $stmt = $connection->query("SELECT 1");
                $health[$shardName] = [
                    'status' => 'healthy',
                    'response_time' => microtime(true)
                ];
            } catch (Exception $e) {
                $health[$shardName] = [
                    'status' => 'unhealthy',
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $health;
    }

    /**
     * Get shard statistics
     */
    public function getShardStats(): array
    {
        $stats = [];
        
        foreach (array_keys($this->shardConfigs) as $shardName) {
            try {
                $connection = $this->getConnection($shardName);
                
                // Get database size
                $stmt = $connection->query("
                    SELECT 
                        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                    FROM information_schema.tables 
                    WHERE table_schema = '{$this->shardConfigs[$shardName]['database']}'
                ");
                $size = $stmt->fetchColumn();
                
                // Get table counts
                $stmt = $connection->query("
                    SELECT COUNT(*) as table_count 
                    FROM information_schema.tables 
                    WHERE table_schema = '{$this->shardConfigs[$shardName]['database']}'
                ");
                $tableCount = $stmt->fetchColumn();
                
                $stats[$shardName] = [
                    'size_mb' => $size,
                    'table_count' => $tableCount,
                    'config' => $this->shardConfigs[$shardName]
                ];
            } catch (Exception $e) {
                $stats[$shardName] = [
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $stats;
    }

    /**
     * Migrate data between shards
     */
    public function migrateData(string $fromShard, string $toShard, string $table, array $conditions = []): bool
    {
        try {
            // Get data from source shard
            $sql = "SELECT * FROM {$table}";
            $params = [];
            
            if (!empty($conditions)) {
                $whereClause = [];
                foreach ($conditions as $column => $value) {
                    $whereClause[] = "{$column} = :{$column}";
                    $params[$column] = $value;
                }
                $sql .= " WHERE " . implode(' AND ', $whereClause);
            }
            
            $stmt = $this->executeOnShard($fromShard, $sql, $params);
            $data = $stmt->fetchAll();
            
            if (empty($data)) {
                return true; // No data to migrate
            }
            
            // Insert data into target shard
            $connection = $this->getConnection($toShard);
            $connection->beginTransaction();
            
            try {
                foreach ($data as $row) {
                    $columns = implode(', ', array_keys($row));
                    $placeholders = ':' . implode(', :', array_keys($row));
                    
                    $insertSql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
                    $stmt = $connection->prepare($insertSql);
                    $stmt->execute($row);
                }
                
                $connection->commit();
                return true;
            } catch (Exception $e) {
                $connection->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            error_log("Data migration failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Close all shard connections
     */
    public function closeConnections(): void
    {
        foreach ($this->shards as $shardName => $connection) {
            $this->shards[$shardName] = null;
        }
        $this->shards = [];
    }
} 