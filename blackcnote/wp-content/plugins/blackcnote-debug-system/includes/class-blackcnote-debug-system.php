<?php
/**
 * BlackCnote Debug System Core Class
 * WordPress integration for the BlackCnote Debug System
 *
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/class-blackcnote-debug-health.php';
require_once __DIR__ . '/class-blackcnote-debug-rest.php';
add_action('rest_api_init', ['BlackCnote_Debug_REST', 'register_routes']);

/**
 * Core Debug System Class
 */
class BlackCnoteDebugSystemCore {
    
    private $log_file;
    private $debug_enabled = true;
    private $log_level = 'ALL';
    private $environment_info = null;
    private $base_path;
    
    /**
     * Constructor
     */
    public function __construct(array $config = []) {
        $this->base_path = $config['base_path'] ?? dirname(__DIR__, 3);
        $this->log_file = $config['log_file'] ?? $this->base_path . '/logs/blackcnote-debug.log';
        $this->debug_enabled = $config['debug_enabled'] ?? true;
        $this->log_level = $config['log_level'] ?? 'ALL';
        
        $this->detectEnvironment();
        $this->setupErrorHandling();
        $this->setupExceptionHandling();
        $this->setupShutdownHandling();
        
        $this->log('BlackCnote Debug System (WordPress) initialized', 'SYSTEM', [
            'wordpress_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'plugin_version' => BLACKCNOTE_DEBUG_VERSION
        ]);
    }
    
    /**
     * Detect environment
     */
    private function detectEnvironment() {
        $this->environment_info = [
            'php_version' => PHP_VERSION,
            'os' => PHP_OS,
            'base_path' => $this->base_path,
            'log_file' => $this->log_file,
            'debug_enabled' => $this->debug_enabled,
            'log_level' => $this->log_level,
            'wordpress_version' => get_bloginfo('version'),
            'wordpress_url' => get_bloginfo('url'),
            'wordpress_admin_url' => admin_url(),
            'timestamp' => date('c'),
        ];
    }
    
    /**
     * Setup error handling
     */
    private function setupErrorHandling() {
        set_error_handler([$this, 'handleError']);
    }
    
    /**
     * Setup exception handling
     */
    private function setupExceptionHandling() {
        set_exception_handler([$this, 'handleException']);
    }
    
    /**
     * Setup shutdown handling
     */
    private function setupShutdownHandling() {
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    /**
     * Handle PHP errors
     */
    public function handleError($errno, $errstr, $errfile, $errline, $errcontext = null) {
        $this->log("PHP Error [$errno]: $errstr in $errfile on line $errline", 'ERROR', [
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline
        ]);
    }
    
    /**
     * Handle exceptions
     */
    public function handleException($exception) {
        $this->log('Uncaught Exception: ' . $exception->getMessage(), 'ERROR', [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
    
    /**
     * Handle shutdown
     */
    public function handleShutdown() {
        $error = error_get_last();
        if ($error !== null) {
            $this->log('Shutdown Error: ' . print_r($error, true), 'ERROR');
        }
    }
    
    /**
     * Log message
     */
    public function log($message, $level = 'INFO', $context = []) {
        if (!$this->debug_enabled) {
            return;
        }
        
        // Ensure logs directory exists
        $logs_dir = dirname($this->log_file);
        if (!is_dir($logs_dir)) {
            wp_mkdir_p($logs_dir);
        }
        
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => array_merge($context, [
                'wordpress_user_id' => get_current_user_id(),
                'wordpress_user_login' => wp_get_current_user()->user_login ?? 'guest',
                'wordpress_request_uri' => $_SERVER['REQUEST_URI'] ?? '',
                'wordpress_request_method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'wordpress_is_admin' => is_admin(),
                'wordpress_is_ajax' => wp_doing_ajax(),
                'wordpress_is_cron' => wp_doing_cron(),
            ])
        ];
        
        file_put_contents($this->log_file, json_encode($entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get log file path
     */
    public function getLogFilePath() {
        return $this->log_file;
    }
    
    /**
     * Clear log
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
    
    /**
     * Get environment info
     */
    public function getEnvironmentInfo() {
        return $this->environment_info;
    }
    
    /**
     * Get WordPress specific info
     */
    public function getWordPressInfo() {
        return [
            'version' => get_bloginfo('version'),
            'url' => get_bloginfo('url'),
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'admin_email' => get_bloginfo('admin_email'),
            'charset' => get_bloginfo('charset'),
            'language' => get_bloginfo('language'),
            'timezone' => get_option('timezone_string'),
            'date_format' => get_option('date_format'),
            'time_format' => get_option('time_format'),
            'active_theme' => get_stylesheet(),
            'parent_theme' => get_template(),
            'active_plugins' => get_option('active_plugins'),
            'total_plugins' => count(get_plugins()),
            'memory_limit' => WP_MEMORY_LIMIT,
            'max_memory_limit' => WP_MAX_MEMORY_LIMIT,
            'debug_mode' => WP_DEBUG,
            'debug_log' => WP_DEBUG_LOG,
            'debug_display' => WP_DEBUG_DISPLAY,
        ];
    }
    
    /**
     * Get system info
     */
    public function getSystemInfo() {
        return [
            'php_version' => PHP_VERSION,
            'php_os' => PHP_OS,
            'php_sapi' => php_sapi_name(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'disk_free' => disk_free_space(ABSPATH),
            'disk_total' => disk_total_space(ABSPATH),
            'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : null,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
        ];
    }
    
    /**
     * Get database info
     */
    public function getDatabaseInfo() {
        global $wpdb;
        
        return [
            'prefix' => $wpdb->prefix,
            'charset' => $wpdb->charset,
            'collate' => $wpdb->collate,
            'version' => $wpdb->db_version(),
            'client_version' => $wpdb->db_client_version(),
            'num_queries' => $wpdb->num_queries,
            'last_query' => $wpdb->last_query,
            'last_error' => $wpdb->last_error,
        ];
    }
    
    /**
     * Check if debug is enabled
     */
    public function isDebugEnabled() {
        return $this->debug_enabled;
    }
    
    /**
     * Enable debug
     */
    public function enableDebug() {
        $this->debug_enabled = true;
        $this->log('Debug system enabled', 'SYSTEM');
    }
    
    /**
     * Disable debug
     */
    public function disableDebug() {
        $this->debug_enabled = false;
        $this->log('Debug system disabled', 'SYSTEM');
    }
} 