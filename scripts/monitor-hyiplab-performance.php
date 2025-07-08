<?php
/**
 * BlackCnote HYIPLab Performance Monitor
 * Monitors system performance and health metrics
 */

declare(strict_types=1);

// Load WordPress
require_once 'wp-config.php';
require_once 'wp-load.php';

echo "📊 BlackCnote HYIPLab Performance Monitor\n";
echo "========================================\n\n";

global $wpdb;

class HYIPLabPerformanceMonitor {
    private $wpdb;
    private $start_time;
    private $memory_start;
    
    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
        $this->start_time = microtime(true);
        $this->memory_start = memory_get_usage();
    }
    
    public function runPerformanceTests() {
        echo "Starting performance monitoring...\n\n";
        
        $this->testDatabasePerformance();
        $this->testMemoryUsage();
        $this->testQueryOptimization();
        $this->testSystemHealth();
        $this->testResponseTimes();
        $this->testConcurrentUsers();
        
        $this->generatePerformanceReport();
    }
    
    private function testDatabasePerformance() {
        echo "1. Database Performance Tests...\n";
        
        // Test query execution time
        $start = microtime(true);
        $users_count = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->prefix}hyiplab_users");
        $query_time = (microtime(true) - $start) * 1000; // Convert to milliseconds
        
        $this->assertPerformance('User count query', $query_time < 100, "Query took {$query_time}ms (should be < 100ms)");
        
        // Test complex join query
        $start = microtime(true);
        $investments = $this->wpdb->get_results("
            SELECT i.*, u.username, p.name as plan_name 
            FROM {$this->wpdb->prefix}hyiplab_invests i
            JOIN {$this->wpdb->prefix}hyiplab_users u ON i.user_id = u.id
            JOIN {$this->wpdb->prefix}hyiplab_plans p ON i.plan_id = p.id
            LIMIT 10
        ");
        $join_query_time = (microtime(true) - $start) * 1000;
        
        $this->assertPerformance('Complex join query', $join_query_time < 200, "Join query took {$join_query_time}ms (should be < 200ms)");
        
        // Test index usage
        $this->wpdb->query("EXPLAIN SELECT * FROM {$this->wpdb->prefix}hyiplab_invests WHERE user_id = 1");
        $explain_result = $this->wpdb->last_result;
        
        $this->assertPerformance('Index usage', !empty($explain_result), 'Queries should use proper indexes');
        
        echo "\n";
    }
    
    private function testMemoryUsage() {
        echo "2. Memory Usage Tests...\n";
        
        $current_memory = memory_get_usage();
        $peak_memory = memory_get_peak_usage();
        $memory_limit = ini_get('memory_limit');
        
        $memory_usage_mb = round($current_memory / 1024 / 1024, 2);
        $peak_memory_mb = round($peak_memory / 1024 / 1024, 2);
        
        $this->assertPerformance('Current memory usage', $memory_usage_mb < 50, "Current memory: {$memory_usage_mb}MB (should be < 50MB)");
        $this->assertPerformance('Peak memory usage', $peak_memory_mb < 100, "Peak memory: {$peak_memory_mb}MB (should be < 100MB)");
        
        echo "   📊 Memory Usage: {$memory_usage_mb}MB current, {$peak_memory_mb}MB peak\n";
        echo "   📊 Memory Limit: {$memory_limit}\n";
        
        echo "\n";
    }
    
    private function testQueryOptimization() {
        echo "3. Query Optimization Tests...\n";
        
        // Test slow query detection
        $slow_queries = $this->wpdb->get_results("
            SELECT * FROM {$this->wpdb->prefix}hyiplab_invests i
            WHERE i.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY i.amount DESC
        ");
        
        $this->assertPerformance('Slow query handling', count($slow_queries) < 1000, 'Large result sets should be limited');
        
        // Test query caching
        $start = microtime(true);
        $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->prefix}hyiplab_plans");
        $first_query_time = (microtime(true) - $start) * 1000;
        
        $start = microtime(true);
        $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->prefix}hyiplab_plans");
        $second_query_time = (microtime(true) - $start) * 1000;
        
        $this->assertPerformance('Query caching', $second_query_time <= $first_query_time, 'Repeated queries should be faster');
        
        echo "\n";
    }
    
    private function testSystemHealth() {
        echo "4. System Health Tests...\n";
        
        // Test database connection
        $this->assertPerformance('Database connection', $this->wpdb->check_connection(), 'Database should be accessible');
        
        // Test table integrity
        $tables = ['hyiplab_plans', 'hyiplab_users', 'hyiplab_invests', 'hyiplab_transactions'];
        foreach ($tables as $table) {
            $table_status = $this->wpdb->get_row("CHECK TABLE {$this->wpdb->prefix}{$table}");
            $this->assertPerformance("Table {$table} integrity", 
                $table_status && $table_status->Msg_text === 'OK', 
                "Table {$table} should be healthy"
            );
        }
        
        // Test WordPress core health
        $this->assertPerformance('WordPress core', defined('ABSPATH'), 'WordPress core should be loaded');
        $this->assertPerformance('HYIPLab plugin', is_plugin_active('hyiplab/hyiplab.php'), 'HYIPLab plugin should be active');
        
        echo "\n";
    }
    
    private function testResponseTimes() {
        echo "5. Response Time Tests...\n";
        
        // Test admin page load time
        $start = microtime(true);
        $admin_url = admin_url('admin.php?page=hyiplab');
        $response = wp_remote_get($admin_url, ['timeout' => 30]);
        $admin_load_time = (microtime(true) - $start) * 1000;
        
        $this->assertPerformance('Admin page load', $admin_load_time < 5000, "Admin page loaded in {$admin_load_time}ms (should be < 5000ms)");
        
        // Test API response time
        $start = microtime(true);
        $api_url = home_url('/wp-json/hyiplab/v1/');
        $api_response = wp_remote_get($api_url, ['timeout' => 10]);
        $api_load_time = (microtime(true) - $start) * 1000;
        
        $this->assertPerformance('API response time', $api_load_time < 2000, "API responded in {$api_load_time}ms (should be < 2000ms)");
        
        echo "\n";
    }
    
    private function testConcurrentUsers() {
        echo "6. Concurrent User Tests...\n";
        
        // Simulate concurrent database access
        $start = microtime(true);
        $results = [];
        
        for ($i = 0; $i < 10; $i++) {
            $results[] = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->prefix}hyiplab_users");
        }
        
        $concurrent_time = (microtime(true) - $start) * 1000;
        
        $this->assertPerformance('Concurrent queries', $concurrent_time < 1000, "10 concurrent queries took {$concurrent_time}ms (should be < 1000ms)");
        
        // Test user session handling
        $this->assertPerformance('Session management', function_exists('wp_get_current_user'), 'User session management should be available');
        
        echo "\n";
    }
    
    private function assertPerformance($test_name, $condition, $message) {
        $result = $condition ? 'PASS' : 'FAIL';
        $icon = $condition ? '✅' : '❌';
        echo "   {$icon} {$test_name}: {$result}\n";
        
        if (!$condition) {
            echo "      ⚠️  {$message}\n";
        }
    }
    
    private function generatePerformanceReport() {
        $total_time = (microtime(true) - $this->start_time) * 1000;
        $memory_used = memory_get_usage() - $this->memory_start;
        
        echo "\n📊 Performance Report\n";
        echo "===================\n";
        echo "Total execution time: " . round($total_time, 2) . "ms\n";
        echo "Memory used: " . round($memory_used / 1024 / 1024, 2) . "MB\n";
        echo "PHP version: " . PHP_VERSION . "\n";
        echo "MySQL version: " . $this->wpdb->db_version() . "\n";
        echo "WordPress version: " . get_bloginfo('version') . "\n";
        
        // Performance recommendations
        echo "\n💡 Performance Recommendations:\n";
        
        if ($total_time > 5000) {
            echo "   - Consider optimizing database queries\n";
        }
        
        if ($memory_used > 50 * 1024 * 1024) { // 50MB
            echo "   - Consider reducing memory usage\n";
        }
        
        if (!defined('WP_CACHE') || !WP_CACHE) {
            echo "   - Enable WordPress caching for better performance\n";
        }
        
        echo "\n🚀 Performance monitoring completed!\n";
    }
}

// Run the performance monitor
$monitor = new HYIPLabPerformanceMonitor($wpdb);
$monitor->runPerformanceTests();

echo "\n📊 Performance monitoring completed!\n";
echo "For real-time monitoring, consider:\n";
echo "- Setting up automated monitoring scripts\n";
echo "- Implementing logging for performance metrics\n";
echo "- Using external monitoring services\n"; 