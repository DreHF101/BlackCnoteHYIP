<?php
/**
 * BlackCnote Optimized Debug System
 * Addresses performance, security, and code quality issues from the review
 * 
 * Features:
 * - Unified debug system with modular architecture
 * - Performance-optimized logging with buffering
 * - Security-hardened with data sanitization
 * - Configuration management system
 * - Memory usage monitoring
 * - Log rotation and cleanup
 */

// Prevent direct access
if (!defined('ABSPATH') && !defined('BLACKCNOTE_DEBUG')) {
    define('BLACKCNOTE_DEBUG', true);
}

class BlackCnoteOptimizedDebugSystem {
    
    private static $instance = null;
    private $log_file;
    private $debug_enabled = false;
    private $log_level = 'INFO';
    private $log_buffer = [];
    private $buffer_size = 50;
    private $max_log_size = 10 * 1024 * 1024; // 10MB
    private $log_rotation = true;
    private $memory_limit = 50 * 1024 * 1024; // 50MB
    private $flush_interval = 5; // seconds
    private $last_flush = 0;
    private $config = null;
    private $performance_monitor = null;
    private $modules = [];
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->config = new BlackCnoteDebugConfig();
        $this->performance_monitor = new BlackCnotePerformanceMonitor();
        
        $this->initialize();
        $this->loadModules();
        $this->setupHooks();
        
        $this->log('BlackCnote Optimized Debug System initialized', 'SYSTEM');
    }
    
    /**
     * Initialize the debug system
     */
    private function initialize() {
        // Set log file path
        $this->log_file = WP_CONTENT_DIR . '/blackcnote-optimized-debug.log';
        
        // Load configuration
        $this->debug_enabled = $this->config->get('enabled', defined('WP_DEBUG') && WP_DEBUG);
        $this->log_level = $this->config->get('log_level', 'INFO');
        $this->buffer_size = $this->config->get('buffer_size', 50);
        $this->max_log_size = $this->config->get('max_log_size', 10 * 1024 * 1024);
        $this->log_rotation = $this->config->get('log_rotation', true);
        
        // Setup error handling only if debug is enabled
        if ($this->debug_enabled) {
            $this->setupErrorHandling();
        }
    }
    
    /**
     * Load debug modules
     */
    private function loadModules() {
        // Core module - always loaded
        $this->modules['core'] = new BlackCnoteCoreDebugModule($this);
        
        // Conditional modules
        if ($this->config->get('react_debugging', false)) {
            $this->modules['react'] = new BlackCnoteReactDebugModule($this);
        }
        
        if ($this->config->get('theme_debugging', true)) {
            $this->modules['theme'] = new BlackCnoteThemeDebugModule($this);
        }
        
        if ($this->config->get('performance_monitoring', true)) {
            $this->modules['performance'] = new BlackCnotePerformanceDebugModule($this);
        }
        
        if ($this->config->get('security_monitoring', true)) {
            $this->modules['security'] = new BlackCnoteSecurityDebugModule($this);
        }
    }
    
    /**
     * Setup WordPress hooks
     */
    private function setupHooks() {
        // Periodic flush
        add_action('shutdown', [$this, 'flushLogBuffer']);
        
        // Memory monitoring
        add_action('wp_loaded', [$this, 'checkMemoryUsage']);
        
        // Log rotation check
        add_action('init', [$this, 'checkLogRotation'], 20);
        
        // Initialize modules
        add_action('plugins_loaded', [$this, 'initializeModules'], 5);
    }
    
    /**
     * Initialize debug modules
     */
    public function initializeModules() {
        foreach ($this->modules as $module) {
            if (method_exists($module, 'initialize')) {
                $module->initialize();
            }
        }
    }
    
    /**
     * Main logging method with performance optimization
     */
    public function log($message, $level = 'INFO', $context = []) {
        if (!$this->debug_enabled || !$this->shouldLog($level)) {
            return false;
        }
        
        // Check memory usage
        if (memory_get_usage() > $this->memory_limit) {
            $this->flushLogBuffer();
        }
        
        // Sanitize context
        $context = $this->sanitizeContext($context);
        
        // Remove sensitive data
        $message = $this->removeSensitiveData($message);
        
        // Buffer the log entry
        $this->log_buffer[] = [
            'timestamp' => microtime(true),
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'memory_usage' => memory_get_usage(),
            'peak_memory' => memory_get_peak_usage()
        ];
        
        // Flush buffer if full
        if (count($this->log_buffer) >= $this->buffer_size) {
            $this->flushLogBuffer();
        }
        
        // Periodic flush
        if (time() - $this->last_flush > $this->flush_interval) {
            $this->flushLogBuffer();
        }
        
        return true;
    }
    
    /**
     * Flush log buffer to file
     */
    public function flushLogBuffer() {
        if (empty($this->log_buffer)) {
            return;
        }
        
        // Validate log path
        if (!$this->isLogPathSafe()) {
            return;
        }
        
        $log_content = '';
        foreach ($this->log_buffer as $entry) {
            $log_content .= $this->formatLogEntry($entry);
        }
        
        // Atomic write with file locking
        $result = @file_put_contents($this->log_file, $log_content, FILE_APPEND | LOCK_EX);
        
        if ($result !== false) {
            $this->log_buffer = [];
            $this->last_flush = time();
        }
    }
    
    /**
     * Format log entry
     */
    private function formatLogEntry($entry) {
        $timestamp = date('Y-m-d H:i:s', (int)$entry['timestamp']);
        $microseconds = sprintf('%.6f', $entry['timestamp'] - (int)$entry['timestamp']);
        $memory_mb = round($entry['memory_usage'] / 1024 / 1024, 2);
        
        $context_str = '';
        if (!empty($entry['context'])) {
            $context_str = ' | Context: ' . json_encode($entry['context']);
        }
        
        return sprintf(
            "[%s%s] [%s] [Memory: %sMB] %s%s\n",
            $timestamp,
            $microseconds,
            strtoupper($entry['level']),
            $memory_mb,
            $entry['message'],
            $context_str
        );
    }
    
    /**
     * Check if should log based on level
     */
    private function shouldLog($level) {
        $levels = [
            'DEBUG' => 1,
            'INFO' => 2,
            'WARNING' => 3,
            'ERROR' => 4,
            'FATAL' => 5
        ];
        
        $current_level = $levels[$this->log_level] ?? 2;
        $message_level = $levels[$level] ?? 2;
        
        return $message_level >= $current_level;
    }
    
    /**
     * Sanitize context data
     */
    private function sanitizeContext($context) {
        if (!is_array($context)) {
            return [];
        }
        
        $sensitive_keys = ['password', 'token', 'key', 'secret', 'auth', 'credential'];
        $sanitized = [];
        
        foreach ($context as $key => $value) {
            $key_lower = strtolower($key);
            
            // Remove sensitive data
            if (in_array($key_lower, $sensitive_keys)) {
                $sanitized[$key] = '[REDACTED]';
                continue;
            }
            
            // Sanitize file paths
            if (is_string($value) && (strpos($value, '/') !== false || strpos($value, '\\') !== false)) {
                $sanitized[$key] = $this->sanitizePath($value);
                continue;
            }
            
            // Sanitize arrays and objects
            if (is_array($value) || is_object($value)) {
                $sanitized[$key] = $this->sanitizeContext($value);
                continue;
            }
            
            $sanitized[$key] = $value;
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize file paths
     */
    private function sanitizePath($path) {
        // Remove absolute paths
        $path = str_replace(ABSPATH, '[ABSPATH]/', $path);
        $path = str_replace(WP_CONTENT_DIR, '[WP_CONTENT]/', $path);
        
        // Remove potential sensitive information
        $path = preg_replace('/\/home\/[^\/]+\//', '/home/[USER]/', $path);
        $path = preg_replace('/\/Users\/[^\/]+\//', '/Users/[USER]/', $path);
        
        return $path;
    }
    
    /**
     * Remove sensitive data from messages
     */
    private function removeSensitiveData($message) {
        $patterns = [
            '/password\s*[:=]\s*[^\s,}]+/i' => 'password: [REDACTED]',
            '/token\s*[:=]\s*[^\s,}]+/i' => 'token: [REDACTED]',
            '/key\s*[:=]\s*[^\s,}]+/i' => 'key: [REDACTED]',
            '/secret\s*[:=]\s*[^\s,}]+/i' => 'secret: [REDACTED]'
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            $message = preg_replace($pattern, $replacement, $message);
        }
        
        return $message;
    }
    
    /**
     * Check if log path is safe
     */
    private function isLogPathSafe() {
        $log_dir = dirname($this->log_file);
        
        // Check if directory is within WordPress content directory
        if (strpos($log_dir, WP_CONTENT_DIR) !== 0) {
            return false;
        }
        
        // Check if directory is writable
        if (!is_writable($log_dir)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check memory usage
     */
    public function checkMemoryUsage() {
        $memory_usage = memory_get_usage();
        $memory_limit = ini_get('memory_limit');
        
        if ($memory_usage > $this->memory_limit) {
            $this->log('High memory usage detected', 'WARNING', [
                'current_usage' => $this->formatBytes($memory_usage),
                'memory_limit' => $memory_limit,
                'peak_usage' => $this->formatBytes(memory_get_peak_usage())
            ]);
            
            // Force flush to free memory
            $this->flushLogBuffer();
        }
    }
    
    /**
     * Check and rotate log file
     */
    public function checkLogRotation() {
        if (!$this->log_rotation || !file_exists($this->log_file)) {
            return;
        }
        
        $file_size = filesize($this->log_file);
        
        if ($file_size > $this->max_log_size) {
            $this->rotateLog();
        }
    }
    
    /**
     * Rotate log file
     */
    private function rotateLog() {
        $backup_file = $this->log_file . '.' . date('Y-m-d-H-i-s') . '.bak';
        
        if (rename($this->log_file, $backup_file)) {
            $this->log('Log file rotated', 'INFO', [
                'old_file' => $this->log_file,
                'new_file' => $backup_file,
                'size' => $this->formatBytes(filesize($backup_file))
            ]);
        }
        
        // Clean up old backup files (keep last 5)
        $this->cleanupOldLogs();
    }
    
    /**
     * Clean up old log files
     */
    private function cleanupOldLogs() {
        $log_dir = dirname($this->log_file);
        $pattern = $log_dir . '/blackcnote-optimized-debug.log.*.bak';
        $files = glob($pattern);
        
        if (count($files) > 5) {
            // Sort by modification time
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            // Remove old files
            $files_to_remove = array_slice($files, 5);
            foreach ($files_to_remove as $file) {
                unlink($file);
            }
        }
    }
    
    /**
     * Setup error handling
     */
    private function setupErrorHandling() {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    /**
     * Handle PHP errors
     */
    public function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $this->log("PHP Error: [$errno] $errstr", 'ERROR', [
            'file' => $this->sanitizePath($errfile),
            'line' => $errline,
            'error_type' => $this->getErrorType($errno)
        ]);
        
        return false; // Let standard handler run
    }
    
    /**
     * Handle exceptions
     */
    public function handleException($exception) {
        $this->log("Uncaught Exception: " . $exception->getMessage(), 'FATAL', [
            'file' => $this->sanitizePath($exception->getFile()),
            'line' => $exception->getLine(),
            'trace' => $this->sanitizeStackTrace($exception->getTrace())
        ]);
    }
    
    /**
     * Handle shutdown
     */
    public function handleShutdown() {
        $error = error_get_last();
        if ($error && $error['type'] === E_ERROR) {
            $this->log("Fatal Error: " . $error['message'], 'FATAL', [
                'file' => $this->sanitizePath($error['file']),
                'line' => $error['line']
            ]);
        }
        
        // Final flush
        $this->flushLogBuffer();
    }
    
    /**
     * Sanitize stack trace
     */
    private function sanitizeStackTrace($trace) {
        $sanitized = [];
        
        foreach ($trace as $frame) {
            $sanitized[] = [
                'file' => isset($frame['file']) ? $this->sanitizePath($frame['file']) : null,
                'line' => $frame['line'] ?? null,
                'function' => $frame['function'] ?? null,
                'class' => $frame['class'] ?? null
            ];
        }
        
        return $sanitized;
    }
    
    /**
     * Get error type string
     */
    private function getErrorType($errno) {
        switch ($errno) {
            case E_ERROR: return 'E_ERROR';
            case E_WARNING: return 'E_WARNING';
            case E_PARSE: return 'E_PARSE';
            case E_NOTICE: return 'E_NOTICE';
            case E_CORE_ERROR: return 'E_CORE_ERROR';
            case E_CORE_WARNING: return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: return 'E_COMPILE_WARNING';
            case E_USER_ERROR: return 'E_USER_ERROR';
            case E_USER_WARNING: return 'E_USER_WARNING';
            case E_USER_NOTICE: return 'E_USER_NOTICE';
            case E_STRICT: return 'E_STRICT';
            case E_RECOVERABLE_ERROR: return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: return 'E_DEPRECATED';
            case E_USER_DEPRECATED: return 'E_USER_DEPRECATED';
            default: return 'UNKNOWN';
        }
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Get module by name
     */
    public function getModule($name) {
        return $this->modules[$name] ?? null;
    }
    
    /**
     * Get configuration
     */
    public function getConfig() {
        return $this->config;
    }
    
    /**
     * Get performance monitor
     */
    public function getPerformanceMonitor() {
        return $this->performance_monitor;
    }
    
    /**
     * Enable/disable debug system
     */
    public function setDebugEnabled($enabled) {
        $this->debug_enabled = $enabled;
        $this->config->set('enabled', $enabled);
    }
    
    /**
     * Set log level
     */
    public function setLogLevel($level) {
        $this->log_level = $level;
        $this->config->set('log_level', $level);
    }
    
    /**
     * Get log file path
     */
    public function getLogFilePath() {
        return $this->log_file;
    }
    
    /**
     * Clear log file
     */
    public function clearLog() {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }
    }
    
    /**
     * Get log file size
     */
    public function getLogFileSize() {
        return file_exists($this->log_file) ? filesize($this->log_file) : 0;
    }
}

/**
 * Configuration Management Class
 */
class BlackCnoteDebugConfig {
    private $options = [];
    private $option_prefix = 'blackcnote_debug_';
    
    public function __construct() {
        $this->loadOptions();
    }
    
    private function loadOptions() {
        $this->options = [
            'enabled' => get_option($this->option_prefix . 'enabled', defined('WP_DEBUG') && WP_DEBUG),
            'log_level' => get_option($this->option_prefix . 'log_level', 'INFO'),
            'buffer_size' => get_option($this->option_prefix . 'buffer_size', 50),
            'max_log_size' => get_option($this->option_prefix . 'max_log_size', 10 * 1024 * 1024),
            'log_rotation' => get_option($this->option_prefix . 'log_rotation', true),
            'react_debugging' => get_option($this->option_prefix . 'react_debugging', false),
            'theme_debugging' => get_option($this->option_prefix . 'theme_debugging', true),
            'performance_monitoring' => get_option($this->option_prefix . 'performance_monitoring', true),
            'security_monitoring' => get_option($this->option_prefix . 'security_monitoring', true)
        ];
    }
    
    public function get($key, $default = null) {
        return $this->options[$key] ?? $default;
    }
    
    public function set($key, $value) {
        $this->options[$key] = $value;
        update_option($this->option_prefix . $key, $value);
    }
    
    public function getAll() {
        return $this->options;
    }
}

/**
 * Performance Monitor Class
 */
class BlackCnotePerformanceMonitor {
    private $metrics = [];
    private $debug_system = null;
    
    public function __construct($debug_system = null) {
        $this->debug_system = $debug_system;
    }
    
    public function startTimer($operation) {
        $this->metrics[$operation] = [
            'start' => microtime(true),
            'memory_start' => memory_get_usage(),
            'peak_start' => memory_get_peak_usage()
        ];
    }
    
    public function endTimer($operation) {
        if (!isset($this->metrics[$operation])) {
            return;
        }
        
        $end_time = microtime(true);
        $end_memory = memory_get_usage();
        $end_peak = memory_get_peak_usage();
        
        $duration = $end_time - $this->metrics[$operation]['start'];
        $memory_used = $end_memory - $this->metrics[$operation]['memory_start'];
        $peak_increase = $end_peak - $this->metrics[$operation]['peak_start'];
        
        $this->logPerformance($operation, $duration, $memory_used, $peak_increase);
        
        unset($this->metrics[$operation]);
    }
    
    private function logPerformance($operation, $duration, $memory_used, $peak_increase) {
        if ($this->debug_system) {
            $this->debug_system->log("Performance: $operation", 'DEBUG', [
                'duration_ms' => round($duration * 1000, 2),
                'memory_used' => $this->formatBytes($memory_used),
                'peak_increase' => $this->formatBytes($peak_increase)
            ]);
        }
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

/**
 * Base Debug Module Class
 */
abstract class BlackCnoteDebugModule {
    protected $debug_system = null;
    
    public function __construct($debug_system) {
        $this->debug_system = $debug_system;
    }
    
    abstract public function initialize();
    
    protected function log($message, $level = 'INFO', $context = []) {
        if ($this->debug_system) {
            $this->debug_system->log($message, $level, $context);
        }
    }
}

/**
 * Core Debug Module
 */
class BlackCnoteCoreDebugModule extends BlackCnoteDebugModule {
    public function initialize() {
        $this->log('Core debug module initialized', 'INFO');
    }
}

/**
 * React Debug Module
 */
class BlackCnoteReactDebugModule extends BlackCnoteDebugModule {
    public function initialize() {
        $this->log('React debug module initialized', 'INFO');
        // React-specific debugging setup
    }
}

/**
 * Theme Debug Module
 */
class BlackCnoteThemeDebugModule extends BlackCnoteDebugModule {
    public function initialize() {
        $this->log('Theme debug module initialized', 'INFO');
        // Theme-specific debugging setup
    }
}

/**
 * Performance Debug Module
 */
class BlackCnotePerformanceDebugModule extends BlackCnoteDebugModule {
    public function initialize() {
        $this->log('Performance debug module initialized', 'INFO');
        // Performance monitoring setup
    }
}

/**
 * Security Debug Module
 */
class BlackCnoteSecurityDebugModule extends BlackCnoteDebugModule {
    public function initialize() {
        $this->log('Security debug module initialized', 'INFO');
        // Security monitoring setup
    }
}

// Initialize the optimized debug system
$blackcnote_optimized_debug = BlackCnoteOptimizedDebugSystem::getInstance();

// Helper functions for easy access
function blackcnote_opt_log($message, $level = 'INFO', $context = []) {
    global $blackcnote_optimized_debug;
    $blackcnote_optimized_debug->log($message, $level, $context);
}

function blackcnote_opt_log_error($message, $context = []) {
    blackcnote_opt_log($message, 'ERROR', $context);
}

function blackcnote_opt_log_warning($message, $context = []) {
    blackcnote_opt_log($message, 'WARNING', $context);
}

function blackcnote_opt_log_info($message, $context = []) {
    blackcnote_opt_log($message, 'INFO', $context);
}

function blackcnote_opt_log_debug($message, $context = []) {
    blackcnote_opt_log($message, 'DEBUG', $context);
}

function blackcnote_opt_performance($operation, $callback) {
    global $blackcnote_optimized_debug;
    $monitor = $blackcnote_optimized_debug->getPerformanceMonitor();
    
    $monitor->startTimer($operation);
    $result = $callback();
    $monitor->endTimer($operation);
    
    return $result;
}

// Log system initialization
blackcnote_opt_log('BlackCnote Optimized Debug System loaded and ready', 'SYSTEM');
?> 