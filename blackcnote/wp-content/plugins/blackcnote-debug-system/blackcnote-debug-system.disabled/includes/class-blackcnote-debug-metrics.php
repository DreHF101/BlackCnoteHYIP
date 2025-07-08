<?php
/**
 * BlackCnote Debug System Metrics Class
 * Handles metrics collection and export for the debug system
 *
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Metrics Class
 */
class BlackCnoteDebugMetrics {
    
    private $debug_system;
    private $metrics = [];
    
    /**
     * Constructor
     */
    public function __construct($debug_system) {
        $this->debug_system = $debug_system;
    }
    
    /**
     * Get current metrics
     */
    public function get_current_metrics() {
        $this->collect_metrics();
        return $this->metrics;
    }
    
    /**
     * Collect all metrics
     */
    private function collect_metrics() {
        $this->metrics = [
            'blackcnote_debug_log_entries_total' => 0,
            'blackcnote_debug_errors_total' => 0,
            'blackcnote_debug_warnings_total' => 0,
            'blackcnote_debug_info_total' => 0,
            'blackcnote_debug_debug_total' => 0,
            'blackcnote_debug_system_total' => 0,
            'blackcnote_wordpress_version' => 0,
            'blackcnote_php_version' => 0,
            'blackcnote_memory_usage_bytes' => 0,
            'blackcnote_memory_peak_bytes' => 0,
            'blackcnote_disk_free_bytes' => 0,
            'blackcnote_disk_total_bytes' => 0,
            'blackcnote_active_plugins_total' => 0,
            'blackcnote_total_plugins_total' => 0,
            'blackcnote_database_queries_total' => 0,
            'blackcnote_database_errors_total' => 0,
            'blackcnote_uptime_seconds' => 0,
            'blackcnote_file_changes_total' => 0,
            'blackcnote_docker_containers_running' => 0,
        ];
        
        $this->collect_log_metrics();
        $this->collect_system_metrics();
        $this->collect_wordpress_metrics();
        $this->collect_database_metrics();
        $this->collect_docker_metrics();
    }
    
    /**
     * Collect log metrics
     */
    private function collect_log_metrics() {
        $log_file = $this->debug_system->getLogFilePath();
        
        if (!file_exists($log_file)) {
            return;
        }
        
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->metrics['blackcnote_debug_log_entries_total'] = count($lines);
        
        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if (!$entry) continue;
            
            $level = strtolower($entry['level'] ?? 'unknown');
            $context = $entry['context'] ?? [];
            
            // Count by log level
            switch ($level) {
                case 'error':
                    $this->metrics['blackcnote_debug_errors_total']++;
                    break;
                case 'warning':
                    $this->metrics['blackcnote_debug_warnings_total']++;
                    break;
                case 'info':
                    $this->metrics['blackcnote_debug_info_total']++;
                    break;
                case 'debug':
                    $this->metrics['blackcnote_debug_debug_total']++;
                    break;
                case 'system':
                    $this->metrics['blackcnote_debug_system_total']++;
                    break;
            }
            
            // Extract specific metrics from context
            if (isset($context['memory_usage'])) {
                $this->metrics['blackcnote_memory_usage_bytes'] = $context['memory_usage'];
            }
            
            if (isset($context['uptime'])) {
                $this->metrics['blackcnote_uptime_seconds'] = $context['uptime'];
            }
            
            if (isset($context['total_containers'])) {
                $this->metrics['blackcnote_docker_containers_running'] = $context['total_containers'];
            }
            
            if (isset($context['changes'])) {
                $this->metrics['blackcnote_file_changes_total'] += count($context['changes']);
            }
            
            if (isset($context['disk_free'])) {
                $this->metrics['blackcnote_disk_free_bytes'] = $context['disk_free'];
            }
        }
    }
    
    /**
     * Collect system metrics
     */
    private function collect_system_metrics() {
        $system_info = $this->debug_system->getSystemInfo();
        
        $this->metrics['blackcnote_memory_usage_bytes'] = $system_info['memory_usage'];
        $this->metrics['blackcnote_memory_peak_bytes'] = $system_info['memory_peak'];
        $this->metrics['blackcnote_disk_free_bytes'] = $system_info['disk_free'];
        $this->metrics['blackcnote_disk_total_bytes'] = $system_info['disk_total'];
        
        // Convert PHP version to numeric for metrics
        $php_version = $system_info['php_version'];
        $this->metrics['blackcnote_php_version'] = $this->version_to_numeric($php_version);
    }
    
    /**
     * Collect WordPress metrics
     */
    private function collect_wordpress_metrics() {
        $wordpress_info = $this->debug_system->getWordPressInfo();
        
        // Convert WordPress version to numeric for metrics
        $wp_version = $wordpress_info['version'];
        $this->metrics['blackcnote_wordpress_version'] = $this->version_to_numeric($wp_version);
        
        $this->metrics['blackcnote_active_plugins_total'] = count($wordpress_info['active_plugins']);
        $this->metrics['blackcnote_total_plugins_total'] = $wordpress_info['total_plugins'];
    }
    
    /**
     * Collect database metrics
     */
    private function collect_database_metrics() {
        $database_info = $this->debug_system->getDatabaseInfo();
        
        $this->metrics['blackcnote_database_queries_total'] = $database_info['num_queries'];
        $this->metrics['blackcnote_database_errors_total'] = $database_info['last_error'] ? 1 : 0;
    }
    
    /**
     * Collect Docker metrics
     */
    private function collect_docker_metrics() {
        // Try to check Docker status if available
        try {
            $output = shell_exec('docker ps --format "table {{.Names}}" 2>&1');
            if ($output && strpos($output, 'CONTAINER ID') === false) {
                $lines = explode("\n", trim($output));
                $blackcnote_containers = 0;
                
                foreach ($lines as $line) {
                    if (trim($line) && strpos($line, 'blackcnote') !== false) {
                        $blackcnote_containers++;
                    }
                }
                
                $this->metrics['blackcnote_docker_containers_running'] = $blackcnote_containers;
            }
        } catch (Exception $e) {
            // Docker not available or not running
            $this->metrics['blackcnote_docker_containers_running'] = 0;
        }
    }
    
    /**
     * Export Prometheus format
     */
    public function export_prometheus_format() {
        $metrics = $this->get_current_metrics();
        $output = "# BlackCnote Debug System Metrics\n";
        $output .= "# Generated at " . date('c') . "\n\n";
        
        foreach ($metrics as $metric_name => $value) {
            $output .= sprintf("%s %s\n", $metric_name, $value);
        }
        
        return $output;
    }
    
    /**
     * Get metrics as JSON
     */
    public function get_metrics_json() {
        $metrics = $this->get_current_metrics();
        
        return [
            'timestamp' => time(),
            'metrics' => $metrics,
            'wordpress_info' => $this->debug_system->getWordPressInfo(),
            'system_info' => $this->debug_system->getSystemInfo(),
            'database_info' => $this->debug_system->getDatabaseInfo(),
        ];
    }
    
    /**
     * Convert version string to numeric for metrics
     */
    private function version_to_numeric($version) {
        $parts = explode('.', $version);
        $numeric = 0;
        
        foreach ($parts as $i => $part) {
            $numeric += intval($part) * pow(100, 2 - $i);
        }
        
        return $numeric;
    }
    
    /**
     * Get metrics summary
     */
    public function get_metrics_summary() {
        $metrics = $this->get_current_metrics();
        
        return [
            'log_entries' => $metrics['blackcnote_debug_log_entries_total'],
            'errors' => $metrics['blackcnote_debug_errors_total'],
            'warnings' => $metrics['blackcnote_debug_warnings_total'],
            'memory_usage' => $this->format_bytes($metrics['blackcnote_memory_usage_bytes']),
            'disk_free' => $this->format_bytes($metrics['blackcnote_disk_free_bytes']),
            'active_plugins' => $metrics['blackcnote_active_plugins_total'],
            'docker_containers' => $metrics['blackcnote_docker_containers_running'],
            'database_queries' => $metrics['blackcnote_database_queries_total'],
            'database_errors' => $metrics['blackcnote_database_errors_total'],
        ];
    }
    
    /**
     * Format bytes to human readable
     */
    private function format_bytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
} 