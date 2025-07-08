#!/usr/bin/env php
<?php
/**
 * BlackCnote Debug Daemon
 * Standalone CLI daemon for 24/7 project-wide monitoring
 * Enhanced with file watching, system monitoring, and Docker integration
 * 
 * ================================================
 * CANONICAL PATHWAYS - DO NOT CHANGE
 * Project Root: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
 * Theme Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote
 * WP-Content Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
 * Theme Files: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote
 * ================================================
 * EXCLUSIVELY monitors: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
 */

declare(strict_types=1);

require_once __DIR__ . '/../hyiplab/app/Log/BlackCnoteDebugSystem.php';

use BlackCnote\Log\BlackCnoteDebugSystem;

// Configuration - EXCLUSIVELY for BlackCnote project
$config = [
    'base_path' => dirname(__DIR__),
    'blackcnote_path' => dirname(__DIR__) . '/blackcnote',
    'wp_content_path' => dirname(__DIR__) . '/blackcnote/wp-content',
    'log_file' => dirname(__DIR__) . '/logs/blackcnote-debug.log',
    'debug_enabled' => true,
    'log_level' => 'ALL',
];

$debug = new BlackCnoteDebugSystem($config);

class BlackCnoteDebugDaemon {
    private $debug;
    private $base_path;
    private $blackcnote_path;
    private $wp_content_path;
    private $running = true;
    private $last_file_check = 0;
    private $last_system_check = 0;
    private $last_docker_check = 0;
    private $file_hashes = [];
    private $start_time = 0;
    
    public function __construct($debug, $base_path) {
        $this->debug = $debug;
        $this->base_path = $base_path;
        $this->blackcnote_path = $base_path . '/blackcnote';
        $this->wp_content_path = $base_path . '/blackcnote/wp-content';
        $this->initializeFileHashes();
    }
    
    private function initializeFileHashes() {
        $this->file_hashes = $this->getFileHashes();
        $this->debug->log('BlackCnote file monitoring initialized', 'SYSTEM', [
            'base_path' => $this->base_path,
            'blackcnote_path' => $this->blackcnote_path,
            'wp_content_path' => $this->wp_content_path,
            'files_monitored' => count($this->file_hashes)
        ]);
    }
    
    private function getFileHashes() {
        $hashes = [];
        $extensions = ['php', 'js', 'css', 'json', 'yml', 'yaml', 'md', 'txt', 'log'];
        
        // EXCLUSIVELY monitor blackcnote directory
        $monitor_paths = [
            $this->blackcnote_path, // Main blackcnote directory
            $this->wp_content_path, // WordPress content directory
            $this->base_path . '/logs', // Logs directory
            $this->base_path . '/config', // Configuration directory
        ];
        
        foreach ($monitor_paths as $path) {
            if (!is_dir($path)) {
                continue;
            }
            
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $ext = pathinfo($file->getPathname(), PATHINFO_EXTENSION);
                    if (in_array($ext, $extensions)) {
                        $relative_path = str_replace($this->base_path . '/', '', $file->getPathname());
                        $hashes[$relative_path] = md5_file($file->getPathname());
                    }
                }
            }
        }
        
        return $hashes;
    }
    
    private function checkFileChanges() {
        $current_hashes = $this->getFileHashes();
        $changes = [];
        
        foreach ($current_hashes as $file => $hash) {
            if (!isset($this->file_hashes[$file])) {
                $changes[] = ['type' => 'created', 'file' => $file];
            } elseif ($this->file_hashes[$file] !== $hash) {
                $changes[] = ['type' => 'modified', 'file' => $file];
            }
        }
        
        foreach ($this->file_hashes as $file => $hash) {
            if (!isset($current_hashes[$file])) {
                $changes[] = ['type' => 'deleted', 'file' => $file];
            }
        }
        
        if (!empty($changes)) {
            $this->debug->log('BlackCnote file changes detected', 'INFO', [
                'changes' => $changes,
                'total_files' => count($current_hashes),
                'wp_content_path' => $this->wp_content_path
            ]);
            $this->file_hashes = $current_hashes;
        }
    }
    
    private function checkSystemStatus() {
        $system_info = [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'disk_free' => disk_free_space($this->base_path),
            'disk_total' => disk_total_space($this->base_path),
            'blackcnote_path_exists' => is_dir($this->blackcnote_path),
            'wp_content_path_exists' => is_dir($this->wp_content_path),
            'load_average' => $this->getLoadAverage(),
            'php_version' => PHP_VERSION,
            'uptime' => time() - $this->start_time
        ];
        
        $this->debug->log('BlackCnote system status check', 'DEBUG', $system_info);
        
        // Check for critical conditions
        $memory_limit = 100 * 1024 * 1024; // 100MB
        if ($system_info['memory_usage'] > $memory_limit) {
            $this->debug->log('High memory usage detected', 'WARNING', [
                'memory_usage' => $system_info['memory_usage'],
                'memory_limit' => $memory_limit
            ]);
        }
        
        // Check if blackcnote directory exists
        if (!$system_info['blackcnote_path_exists']) {
            $this->debug->log('BlackCnote directory not found', 'ERROR', [
                'path' => $this->blackcnote_path
            ]);
        }
        
        // Check if wp-content directory exists
        if (!$system_info['wp_content_path_exists']) {
            $this->debug->log('WordPress content directory not found', 'ERROR', [
                'path' => $this->wp_content_path
            ]);
        }
    }
    
    private function getLoadAverage() {
        // Windows-compatible load average
        if (function_exists('sys_getloadavg')) {
            return sys_getloadavg();
        } else {
            // Windows fallback - return current time as simple metric
            return [time() % 100, 0, 0];
        }
    }
    
    private function checkDockerStatus() {
        // Try to check Docker status if available
        $docker_available = false;
        $docker_containers = [];
        
        try {
            $output = shell_exec('docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" 2>&1');
            if ($output && strpos($output, 'CONTAINER ID') === false) {
                $docker_available = true;
                $lines = explode("\n", trim($output));
                foreach ($lines as $line) {
                    if (trim($line) && strpos($line, 'blackcnote') !== false) {
                        $docker_containers[] = trim($line);
                    }
                }
            }
        } catch (Exception $e) {
            // Docker not available or not running
        }
        
        if ($docker_available) {
            $this->debug->log('BlackCnote Docker status check', 'INFO', [
                'containers' => $docker_containers,
                'total_containers' => count($docker_containers),
                'wp_content_path' => $this->wp_content_path
            ]);
        } else {
            $this->debug->log('Docker not available', 'WARNING', [
                'message' => 'Docker engine not running or not accessible',
                'wp_content_path' => $this->wp_content_path
            ]);
        }
    }
    
    private function checkLogFiles() {
        $log_dir = $this->base_path . '/logs';
        if (!is_dir($log_dir)) {
            return;
        }
        
        $log_files = glob($log_dir . '/*.log');
        foreach ($log_files as $log_file) {
            $filename = basename($log_file);
            $size = filesize($log_file);
            $modified = filemtime($log_file);
            
            // Check for new errors in log files
            $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $recent_lines = array_slice($lines, -10); // Last 10 lines
            
            foreach ($recent_lines as $line) {
                if (stripos($line, 'error') !== false || stripos($line, 'exception') !== false) {
                    $this->debug->log('Error detected in log file', 'ERROR', [
                        'file' => $filename,
                        'line' => $line,
                        'size' => $size,
                        'wp_content_path' => $this->wp_content_path
                    ]);
                }
            }
        }
    }
    
    public function run() {
        $this->start_time = time();
        $this->debug->log('BlackCnote Debug Daemon started - EXCLUSIVE WP-CONTENT MONITORING', 'SYSTEM', [
            'base_path' => $this->base_path,
            'blackcnote_path' => $this->blackcnote_path,
            'wp_content_path' => $this->wp_content_path,
            'start_time' => date('Y-m-d H:i:s', $this->start_time)
        ]);
        
        echo "[BlackCnote Debug Daemon] Started. EXCLUSIVE MONITORING...\n";
        echo "Base path: {$this->base_path}\n";
        echo "BlackCnote path: {$this->blackcnote_path}\n";
        echo "WP-Content path: {$this->wp_content_path}\n";
        echo "Log file: " . $this->debug->getLogFilePath() . "\n";
        echo "Press Ctrl+C to stop\n\n";
        
        while ($this->running) {
            try {
                $current_time = time();
                
                // Check file changes every 30 seconds
                if ($current_time - $this->last_file_check >= 30) {
                    $this->checkFileChanges();
                    $this->last_file_check = $current_time;
                }
                
                // Check system status every 60 seconds
                if ($current_time - $this->last_system_check >= 60) {
                    $this->checkSystemStatus();
                    $this->last_system_check = $current_time;
                }
                
                // Check Docker status every 120 seconds
                if ($current_time - $this->last_docker_check >= 120) {
                    $this->checkDockerStatus();
                    $this->last_docker_check = $current_time;
                }
                
                // Check log files every 30 seconds
                $this->checkLogFiles();
                
                // Heartbeat every 60 seconds
                $this->debug->log('BlackCnote Debug Daemon heartbeat', 'DEBUG', [
                    'uptime' => $current_time - $this->start_time,
                    'memory_usage' => memory_get_usage(true),
                    'wp_content_path' => $this->wp_content_path
                ]);
                
                sleep(30); // Check every 30 seconds
                
            } catch (Exception $e) {
                $this->debug->log('Daemon error: ' . $e->getMessage(), 'ERROR', [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'wp_content_path' => $this->wp_content_path
                ]);
                sleep(60); // Wait longer on error
            }
        }
    }
    
    public function stop() {
        $this->running = false;
        $this->debug->log('BlackCnote Debug Daemon stopped', 'SYSTEM', [
            'wp_content_path' => $this->wp_content_path
        ]);
    }
}

// Handle graceful shutdown
$daemon = new BlackCnoteDebugDaemon($debug, $config['base_path']);

// Set up signal handlers for graceful shutdown
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, function() use ($daemon) { $daemon->stop(); });
    pcntl_signal(SIGINT, function() use ($daemon) { $daemon->stop(); });
}

// Run the daemon
$daemon->run(); 