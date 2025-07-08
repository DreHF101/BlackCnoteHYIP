<?php

namespace Hyiplab\BackOffice\Database;

class Database
{
    public $table_prefix;
    public $wpdb;

    public function __construct()
    {
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;
    }

    public function tablePrefix()
    {
        return $this->table_prefix;
    }

    public function wpdb()
    {
        return $this->wpdb;
    }

    /**
     * Execute a prepared SQL query with parameters
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return array|object|null Query results
     */
    public function execute($sql, $params = [])
    {
        $sql = $this->setPrefix($sql);
        
        // Use prepared statements for security
        if (!empty($params)) {
            $result = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        } else {
            $result = $this->wpdb->get_results($sql);
        }
        
        $this->throwException();
        return $result;
    }

    /**
     * Get a single row with prepared statements
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return object|null Single row result
     */
    public function getRow($sql, $params = [])
    {
        $sql = $this->setPrefix($sql);
        
        if (!empty($params)) {
            $result = $this->wpdb->get_row($this->wpdb->prepare($sql, $params));
        } else {
            $result = $this->wpdb->get_row($sql);
        }
        
        $this->throwException();
        return $result;
    }

    /**
     * Get a single value with prepared statements
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return mixed Single value result
     */
    public function getVar($sql, $params = [])
    {
        $sql = $this->setPrefix($sql);
        
        if (!empty($params)) {
            $result = $this->wpdb->get_var($this->wpdb->prepare($sql, $params));
        } else {
            $result = $this->wpdb->get_var($sql);
        }
        
        $this->throwException();
        return $result;
    }

    /**
     * Execute a query with prepared statements
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return int|false Insert ID or false on failure
     */
    public function query($sql, $params = [])
    {
        $sql = $this->setPrefix($sql);
        
        if (!empty($params)) {
            $this->wpdb->query($this->wpdb->prepare($sql, $params));
        } else {
            $this->wpdb->query($sql);
        }
        
        $this->throwException();
        return $this->wpdb->insert_id;
    }

    /**
     * Safely set table prefix with validation
     * @param string $sql SQL query
     * @return string SQL query with prefix
     */
    private function setPrefix($sql)
    {
        // Validate table prefix
        if (!is_string($this->table_prefix) || empty($this->table_prefix)) {
            throw new \Exception('Invalid table prefix');
        }
        
        // Use str_replace with validation
        $replaced = str_replace("{{table_prefix}}", $this->table_prefix, $sql);
        
        // Validate the replacement didn't create SQL injection
        if (strpos($replaced, '{{table_prefix}}') !== false) {
            throw new \Exception('Table prefix replacement failed');
        }
        
        return $replaced;
    }

    /**
     * Enhanced error handling with detailed information
     */
    private function throwException()
    {
        if ($this->wpdb->last_error) {
            $error_message = sprintf(
                'Database error: %s. Last query: %s',
                $this->wpdb->last_error,
                $this->wpdb->last_query
            );
            throw new \Exception($error_message);
        }
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        $this->wpdb->query('START TRANSACTION');
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        $this->wpdb->query('COMMIT');
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        $this->wpdb->query('ROLLBACK');
    }

    /**
     * Check if database connection is healthy
     * @return bool
     */
    public function isHealthy()
    {
        try {
            $this->wpdb->get_var('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}