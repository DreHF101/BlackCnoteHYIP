<?php
/**
 * BlackCnote Comprehensive Debug System
 * Captures all errors, warnings, issues, and problems across the entire project
 * Enhanced for WordPress/React integration, localhost, live editing, Browsersync, and XAMPP
 */

// Prevent direct access
if (!defined('ABSPATH') && !defined('BLACKCNOTE_DEBUG')) {
    define('BLACKCNOTE_DEBUG', true);
}

class BlackCnoteDebugSystem {
    
    private static $instance = null;
    private $log_file;
    private $debug_enabled = true;
    private $log_level = 'ALL'; // ALL, ERROR, WARNING, INFO, DEBUG
    private $environment_info = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->log_file = WP_CONTENT_DIR . '/blackcnote-debug.log';
        $this->detectEnvironment();
        $this->setupErrorHandling();
        $this->setupExceptionHandling();
        $this->setupShutdownHandling();
        $this->setupReactIntegration();
        $this->log('BlackCnote Debug System initialized', 'SYSTEM');
    }
    
    /**
     * Detect and analyze the current environment
     */
    private function detectEnvironment() {
        $this->environment_info = [
            'environment' => $this->detectEnvironmentType(),
            'server' => $this->detectServerEnvironment(),
            'development_tools' => $this->detectDevelopmentTools(),
            'react_integration' => $this->detectReactIntegration(),
            'live_editing' => $this->detectLiveEditing(),
            'browsersync' => $this->detectBrowsersync(),
            'localhost' => $this->detectLocalhost(),
            'file_watching' => $this->detectFileWatching(),
            'hot_reload' => $this->detectHotReload()
        ];
    }
    
    /**
     * Detect environment type (development, staging, production)
     */
    private function detectEnvironmentType() {
        $env = 'unknown';
        
        // Check WordPress environment
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $env = 'development';
        } elseif (defined('WP_ENVIRONMENT_TYPE')) {
            $env = WP_ENVIRONMENT_TYPE;
        } else {
            // Detect by domain
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                $env = 'development';
            } elseif (strpos($host, 'staging') !== false || strpos($host, 'test') !== false) {
                $env = 'staging';
            } else {
                $env = 'production';
            }
        }
        
        return $env;
    }
    
    /**
     * Detect server environment
     */
    private function detectServerEnvironment() {
        $server_info = [
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'php_version' => PHP_VERSION,
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'unknown',
            'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
            'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown'
        ];
        
        return $server_info;
    }
    
    /**
     * Detect development tools
     */
    private function detectDevelopmentTools() {
        $tools = [];
        
        // Check for Node.js/npm
        if (file_exists(ABSPATH . 'package.json')) {
            $tools['nodejs'] = true;
            $package_json = json_decode(file_get_contents(ABSPATH . 'package.json'), true);
            $tools['package_name'] = $package_json['name'] ?? 'unknown';
            $tools['package_version'] = $package_json['version'] ?? 'unknown';
        }
        
        // Check for Vite
        if (file_exists(ABSPATH . 'vite.config.ts') || file_exists(ABSPATH . 'vite.config.js')) {
            $tools['vite'] = true;
        }
        
        // Check for React
        if (file_exists(ABSPATH . 'src/App.tsx') || file_exists(ABSPATH . 'src/App.jsx')) {
            $tools['react'] = true;
        }
        
        // Check for TypeScript
        if (file_exists(ABSPATH . 'tsconfig.json')) {
            $tools['typescript'] = true;
        }
        
        // Check for build tools
        if (file_exists(ABSPATH . 'webpack.config.js')) {
            $tools['webpack'] = true;
        }
        
        return $tools;
    }
    
    /**
     * Detect React integration
     */
    private function detectReactIntegration() {
        $react_info = [
            'enabled' => false,
            'build_system' => 'none',
            'dev_server' => false,
            'api_endpoints' => []
        ];
        
        // Check if React is being used
        if (file_exists(ABSPATH . 'src/App.tsx') || file_exists(ABSPATH . 'src/App.jsx')) {
            $react_info['enabled'] = true;
            
            // Detect build system
            if (file_exists(ABSPATH . 'vite.config.ts')) {
                $react_info['build_system'] = 'vite';
            } elseif (file_exists(ABSPATH . 'webpack.config.js')) {
                $react_info['build_system'] = 'webpack';
            }
            
            // Check for dev server
            $dev_server_ports = [3000, 3001, 5173, 8080, 8000];
            foreach ($dev_server_ports as $port) {
                if ($this->isPortOpen('localhost', $port)) {
                    $react_info['dev_server'] = true;
                    $react_info['dev_server_port'] = $port;
                    break;
                }
            }
            
            // Check for API endpoints
            $api_files = [
                ABSPATH . 'src/api/',
                ABSPATH . 'api/',
                ABSPATH . 'wp-content/plugins/hyiplab/api/'
            ];
            
            foreach ($api_files as $api_path) {
                if (is_dir($api_path)) {
                    $react_info['api_endpoints'][] = $api_path;
                }
            }
        }
        
        return $react_info;
    }
    
    /**
     * Detect live editing capabilities
     */
    private function detectLiveEditing() {
        $live_editing = [
            'enabled' => false,
            'tools' => [],
            'file_watching' => false,
            'hot_reload' => false
        ];
        
        // Check for file watchers
        if (file_exists(ABSPATH . 'package.json')) {
            $package_json = json_decode(file_get_contents(ABSPATH . 'package.json'), true);
            $scripts = $package_json['scripts'] ?? [];
            
            foreach ($scripts as $script_name => $script_command) {
                if (strpos($script_command, 'watch') !== false) {
                    $live_editing['enabled'] = true;
                    $live_editing['tools'][] = 'npm-watch';
                }
                if (strpos($script_command, 'dev') !== false) {
                    $live_editing['enabled'] = true;
                    $live_editing['tools'][] = 'dev-server';
                }
            }
        }
        
        // Check for hot reload
        if (file_exists(ABSPATH . 'vite.config.ts')) {
            $vite_config = file_get_contents(ABSPATH . 'vite.config.ts');
            if (strpos($vite_config, 'hmr') !== false || strpos($vite_config, 'hot') !== false) {
                $live_editing['hot_reload'] = true;
            }
        }
        
        return $live_editing;
    }
    
    /**
     * Detect Browsersync
     */
    private function detectBrowsersync() {
        $browsersync = [
            'enabled' => false,
            'port' => null,
            'config_file' => null
        ];
        
        // Check for Browsersync config
        $bs_config_files = [
            'bs-config.js',
            'bs-config.cjs',
            'browsersync.config.js',
            '.browsersyncrc'
        ];
        
        foreach ($bs_config_files as $config_file) {
            if (file_exists(ABSPATH . $config_file)) {
                $browsersync['enabled'] = true;
                $browsersync['config_file'] = $config_file;
                break;
            }
        }
        
        // Check for Browsersync in package.json
        if (file_exists(ABSPATH . 'package.json')) {
            $package_json = json_decode(file_get_contents(ABSPATH . 'package.json'), true);
            $dependencies = array_merge(
                $package_json['dependencies'] ?? [],
                $package_json['devDependencies'] ?? []
            );
            
            if (isset($dependencies['browser-sync'])) {
                $browsersync['enabled'] = true;
                $browsersync['version'] = $dependencies['browser-sync'];
            }
        }
        
        // Check for Browsersync server
        $bs_ports = [3000, 3001, 3002, 3003, 3004, 3005];
        foreach ($bs_ports as $port) {
            if ($this->isPortOpen('localhost', $port)) {
                $browsersync['port'] = $port;
                break;
            }
        }
        
        return $browsersync;
    }
    
    /**
     * Detect localhost environment
     */
    private function detectLocalhost() {
        $localhost = [
            'enabled' => false,
            'type' => 'none',
            'ports' => []
        ];
        
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $server_addr = $_SERVER['SERVER_ADDR'] ?? '';
        
        if (strpos($host, 'localhost') !== false || 
            strpos($host, '127.0.0.1') !== false ||
            $server_addr === '127.0.0.1' ||
            $server_addr === '::1') {
            
            $localhost['enabled'] = true;
            
            // Determine localhost type
            if (strpos($host, 'localhost') !== false) {
                $localhost['type'] = 'localhost';
            } elseif (strpos($host, '127.0.0.1') !== false) {
                $localhost['type'] = 'ip';
            } else {
                $localhost['type'] = 'local';
            }
            
            // Check common development ports
            $dev_ports = [80, 443, 3000, 3001, 5173, 8080, 8000, 9000];
            foreach ($dev_ports as $port) {
                if ($this->isPortOpen('localhost', $port)) {
                    $localhost['ports'][] = $port;
                }
            }
        }
        
        return $localhost;
    }
    
    /**
     * Detect file watching capabilities
     */
    private function detectFileWatching() {
        $file_watching = [
            'enabled' => false,
            'tools' => [],
            'watched_directories' => []
        ];
        
        // Check for file watchers in package.json
        if (file_exists(ABSPATH . 'package.json')) {
            $package_json = json_decode(file_get_contents(ABSPATH . 'package.json'), true);
            $dependencies = array_merge(
                $package_json['dependencies'] ?? [],
                $package_json['devDependencies'] ?? []
            );
            
            $watcher_packages = ['chokidar', 'nodemon', 'watch', 'gaze', 'fs-extra'];
            foreach ($watcher_packages as $package) {
                if (isset($dependencies[$package])) {
                    $file_watching['enabled'] = true;
                    $file_watching['tools'][] = $package;
                }
            }
        }
        
        // Check for watched directories
        $potential_watched_dirs = ['src', 'public', 'wp-content', 'hyiplab', 'blackcnote'];
        foreach ($potential_watched_dirs as $dir) {
            if (is_dir(ABSPATH . $dir)) {
                $file_watching['watched_directories'][] = $dir;
            }
        }
        
        return $file_watching;
    }
    
    /**
     * Detect hot reload capabilities
     */
    private function detectHotReload() {
        $hot_reload = [
            'enabled' => false,
            'tools' => [],
            'config_files' => []
        ];
        
        // Check for hot reload in build tools
        $config_files = [
            'vite.config.ts',
            'vite.config.js',
            'webpack.config.js',
            'webpack.dev.js',
            'rollup.config.js'
        ];
        
        foreach ($config_files as $config_file) {
            if (file_exists(ABSPATH . $config_file)) {
                $content = file_get_contents(ABSPATH . $config_file);
                if (strpos($content, 'hmr') !== false || 
                    strpos($content, 'hot') !== false ||
                    strpos($content, 'liveReload') !== false) {
                    $hot_reload['enabled'] = true;
                    $hot_reload['config_files'][] = $config_file;
                }
            }
        }
        
        return $hot_reload;
    }
    
    /**
     * Check if a port is open
     */
    private function isPortOpen($host, $port) {
        $connection = @fsockopen($host, $port, $errno, $errstr, 1);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }
    
    /**
     * Setup React integration debugging
     */
    private function setupReactIntegration() {
        if ($this->environment_info['react_integration']['enabled']) {
            // Add React-specific error handling
            add_action('wp_head', [$this, 'injectReactDebugScript']);
            add_action('wp_footer', [$this, 'injectReactDebugFooter']);
        }
    }
    
    /**
     * Inject React debug script
     */
    public function injectReactDebugScript() {
        if ($this->environment_info['react_integration']['enabled']) {
            echo '<script>
                window.blackcnoteDebug = {
                    enabled: true,
                    environment: ' . json_encode($this->environment_info) . ',
                    logger: {
                        debug: function(message, data) { 
                            if (window.parent && window.parent.postMessage) {
                                window.parent.postMessage({
                                    type: "blackcnote-debug",
                                    level: "debug",
                                    message: message,
                                    data: data
                                }, "*");
                            }
                        },
                        info: function(message, data) {
                            if (window.parent && window.parent.postMessage) {
                                window.parent.postMessage({
                                    type: "blackcnote-debug",
                                    level: "info",
                                    message: message,
                                    data: data
                                }, "*");
                            }
                        },
                        warn: function(message, data) {
                            if (window.parent && window.parent.postMessage) {
                                window.parent.postMessage({
                                    type: "blackcnote-debug",
                                    level: "warn",
                                    message: message,
                                    data: data
                                }, "*");
                            }
                        },
                        error: function(message, data) {
                            if (window.parent && window.parent.postMessage) {
                                window.parent.postMessage({
                                    type: "blackcnote-debug",
                                    level: "error",
                                    message: message,
                                    data: data
                                }, "*");
                            }
                        }
                    }
                };
            </script>';
        }
    }
    
    /**
     * Inject React debug footer
     */
    public function injectReactDebugFooter() {
        if ($this->environment_info['react_integration']['enabled']) {
            echo '<script>
                // Listen for React debug messages
                window.addEventListener("message", function(event) {
                    if (event.data && event.data.type === "blackcnote-debug") {
                        // Send to PHP debug system
                        fetch("' . admin_url('admin-ajax.php') . '", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: "action=blackcnote_react_debug&level=" + event.data.level + "&message=" + encodeURIComponent(event.data.message) + "&data=" + encodeURIComponent(JSON.stringify(event.data.data))
                        });
                    }
                });
            </script>';
        }
    }
    
    /**
     * Setup comprehensive error handling
     */
    private function setupErrorHandling() {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
        
        // Enable all error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        ini_set('error_log', $this->log_file);
    }
    
    /**
     * Setup exception handling
     */
    private function setupExceptionHandling() {
        // Custom exception handler for uncaught exceptions
        set_exception_handler([$this, 'handleException']);
    }
    
    /**
     * Setup shutdown handling for fatal errors
     */
    private function setupShutdownHandling() {
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    /**
     * Handle PHP errors
     */
    public function handleError($errno, $errstr, $errfile, $errline, $errcontext = null) {
        $error_type = $this->getErrorType($errno);
        $message = sprintf(
            '[%s] %s: %s in %s on line %d',
            $error_type,
            $errstr,
            $errfile,
            $errline
        );
        
        $this->log($message, 'ERROR', [
            'error_number' => $errno,
            'error_string' => $errstr,
            'error_file' => $errfile,
            'error_line' => $errline,
            'error_context' => $errcontext,
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ]);
        
        // Don't suppress the error if it's fatal
        if ($errno === E_ERROR || $errno === E_PARSE || $errno === E_CORE_ERROR || 
            $errno === E_COMPILE_ERROR || $errno === E_USER_ERROR) {
            return false; // Let PHP handle fatal errors
        }
        
        return true; // Suppress non-fatal errors
    }
    
    /**
     * Handle exceptions
     */
    public function handleException($exception) {
        $message = sprintf(
            '[EXCEPTION] %s: %s in %s on line %d',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        
        $this->log($message, 'EXCEPTION', [
            'exception_class' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'exception_file' => $exception->getFile(),
            'exception_line' => $exception->getLine(),
            'exception_code' => $exception->getCode(),
            'exception_trace' => $exception->getTraceAsString(),
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ]);
    }
    
    /**
     * Handle shutdown (fatal errors)
     */
    public function handleShutdown() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $message = sprintf(
                '[FATAL] %s: %s in %s on line %d',
                $this->getErrorType($error['type']),
                $error['message'],
                $error['file'],
                $error['line']
            );
            
            $this->log($message, 'FATAL', [
                'error_type' => $error['type'],
                'error_message' => $error['message'],
                'error_file' => $error['file'],
                'error_line' => $error['line']
            ]);
        }
    }
    
    /**
     * Main logging function
     */
    public function log($message, $level = 'INFO', $context = []) {
        if (!$this->debug_enabled) {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $memory_usage = memory_get_usage(true);
        $peak_memory = memory_get_peak_usage(true);
        
        $log_entry = sprintf(
            "[%s] [%s] [Memory: %s/%s] %s",
            $timestamp,
            strtoupper($level),
            $this->formatBytes($memory_usage),
            $this->formatBytes($peak_memory),
            $message
        );
        
        // Add context information
        if (!empty($context)) {
            $log_entry .= "\nContext: " . json_encode($context, JSON_PRETTY_PRINT);
        }
        
        // Add environment information
        $log_entry .= "\nEnvironment: " . json_encode($this->environment_info, JSON_PRETTY_PRINT);
        
        // Add request information
        $log_entry .= "\nRequest: " . json_encode([
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'UNKNOWN',
            'host' => $_SERVER['HTTP_HOST'] ?? 'UNKNOWN',
            'port' => $_SERVER['SERVER_PORT'] ?? 'UNKNOWN',
            'protocol' => $_SERVER['REQUEST_SCHEME'] ?? 'UNKNOWN'
        ], JSON_PRETTY_PRINT);
        
        // Add WordPress information
        if (defined('ABSPATH')) {
            $log_entry .= "\nWordPress: " . json_encode([
                'version' => get_bloginfo('version'),
                'site_url' => get_site_url(),
                'home_url' => get_home_url(),
                'admin_url' => admin_url(),
                'current_user' => wp_get_current_user()->user_login ?? 'NOT_LOGGED_IN',
                'current_theme' => get_template(),
                'active_plugins' => get_option('active_plugins', []),
                'wp_debug' => defined('WP_DEBUG') ? WP_DEBUG : false,
                'wp_debug_log' => defined('WP_DEBUG_LOG') ? WP_DEBUG_LOG : false,
                'wp_debug_display' => defined('WP_DEBUG_DISPLAY') ? WP_DEBUG_DISPLAY : false
            ], JSON_PRETTY_PRINT);
        }
        
        $log_entry .= "\n" . str_repeat('-', 80) . "\n";
        
        // Write to log file
        file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);
        
        // Also log to WordPress debug log if available
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log($log_entry);
        }
    }
    
    /**
     * Log React-specific events
     */
    public function logReact($message, $level = 'INFO', $context = []) {
        $context['component'] = 'React';
        $context['react_integration'] = $this->environment_info['react_integration'];
        $this->log($message, $level, $context);
    }
    
    /**
     * Log development environment events
     */
    public function logDevelopment($message, $level = 'INFO', $context = []) {
        $context['component'] = 'Development';
        $context['environment'] = $this->environment_info['environment'];
        $context['development_tools'] = $this->environment_info['development_tools'];
        $this->log($message, $level, $context);
    }
    
    /**
     * Log live editing events
     */
    public function logLiveEditing($message, $level = 'INFO', $context = []) {
        $context['component'] = 'Live_Editing';
        $context['live_editing'] = $this->environment_info['live_editing'];
        $this->log($message, $level, $context);
    }
    
    /**
     * Log Browsersync events
     */
    public function logBrowsersync($message, $level = 'INFO', $context = []) {
        $context['component'] = 'Browsersync';
        $context['browsersync'] = $this->environment_info['browsersync'];
        $this->log($message, $level, $context);
    }
    
    /**
     * Log localhost events
     */
    public function logLocalhost($message, $level = 'INFO', $context = []) {
        $context['component'] = 'Localhost';
        $context['localhost'] = $this->environment_info['localhost'];
        $this->log($message, $level, $context);
    }
    
    /**
     * Log file watching events
     */
    public function logFileWatching($message, $level = 'INFO', $context = []) {
        $context['component'] = 'File_Watching';
        $context['file_watching'] = $this->environment_info['file_watching'];
        $this->log($message, $level, $context);
    }
    
    /**
     * Log hot reload events
     */
    public function logHotReload($message, $level = 'INFO', $context = []) {
        $context['component'] = 'Hot_Reload';
        $context['hot_reload'] = $this->environment_info['hot_reload'];
        $this->log($message, $level, $context);
    }
    
    /**
     * Log environment changes
     */
    public function logEnvironmentChange($old_env, $new_env, $context = []) {
        $context['component'] = 'Environment_Change';
        $context['old_environment'] = $old_env;
        $context['new_environment'] = $new_env;
        $this->log("Environment changed from {$old_env} to {$new_env}", 'INFO', $context);
    }
    
    /**
     * Log development server events
     */
    public function logDevServer($message, $level = 'INFO', $context = []) {
        $context['component'] = 'Dev_Server';
        $context['dev_server'] = $this->environment_info['react_integration']['dev_server'];
        $this->log($message, $level, $context);
    }
    
    /**
     * Log API integration events
     */
    public function logApiIntegration($endpoint, $method, $data = [], $response = null, $context = []) {
        $context['component'] = 'API_Integration';
        $context['endpoint'] = $endpoint;
        $context['method'] = $method;
        $context['request_data'] = $data;
        $context['response'] = $response;
        $context['api_endpoints'] = $this->environment_info['react_integration']['api_endpoints'];
        $this->log("API Integration: {$method} {$endpoint}", 'INFO', $context);
    }
    
    /**
     * Get environment information
     */
    public function getEnvironmentInfo() {
        return $this->environment_info;
    }
    
    /**
     * Check if running in development mode
     */
    public function isDevelopment() {
        return $this->environment_info['environment'] === 'development';
    }
    
    /**
     * Check if React integration is enabled
     */
    public function isReactEnabled() {
        return $this->environment_info['react_integration']['enabled'];
    }
    
    /**
     * Check if live editing is enabled
     */
    public function isLiveEditingEnabled() {
        return $this->environment_info['live_editing']['enabled'];
    }
    
    /**
     * Check if Browsersync is enabled
     */
    public function isBrowsersyncEnabled() {
        return $this->environment_info['browsersync']['enabled'];
    }
    
    /**
     * Check if running on localhost
     */
    public function isLocalhost() {
        return $this->environment_info['localhost']['enabled'];
    }
    
    /**
     * Log specific to HYIPLab plugin
     */
    public function logHyipLab($message, $level = 'INFO', $context = []) {
        $context['component'] = 'HYIPLab';
        $this->log($message, $level, $context);
    }
    
    /**
     * Log specific to BlackCnote theme
     */
    public function logTheme($message, $level = 'INFO', $context = []) {
        $context['component'] = 'BlackCnote_Theme';
        $this->log($message, $level, $context);
    }
    
    /**
     * Log database queries
     */
    public function logDatabase($query, $context = []) {
        $context['component'] = 'Database';
        $this->log("Database Query: " . $query, 'DEBUG', $context);
    }
    
    /**
     * Log API calls
     */
    public function logApi($endpoint, $method, $data = [], $response = null, $context = []) {
        $context['component'] = 'API';
        $context['endpoint'] = $endpoint;
        $context['method'] = $method;
        $context['request_data'] = $data;
        $context['response'] = $response;
        
        $this->log("API Call: {$method} {$endpoint}", 'INFO', $context);
    }
    
    /**
     * Log file operations
     */
    public function logFileOperation($operation, $file_path, $result = null, $context = []) {
        $context['component'] = 'File_System';
        $context['operation'] = $operation;
        $context['file_path'] = $file_path;
        $context['result'] = $result;
        
        $this->log("File Operation: {$operation} on {$file_path}", 'INFO', $context);
    }
    
    /**
     * Log plugin activation/deactivation
     */
    public function logPluginLifecycle($plugin, $action, $context = []) {
        $context['component'] = 'Plugin_Lifecycle';
        $context['plugin'] = $plugin;
        $context['action'] = $action;
        
        $this->log("Plugin {$action}: {$plugin}", 'INFO', $context);
    }
    
    /**
     * Log user actions
     */
    public function logUserAction($user_id, $action, $details = [], $context = []) {
        $context['component'] = 'User_Action';
        $context['user_id'] = $user_id;
        $context['action'] = $action;
        $context['details'] = $details;
        
        $this->log("User Action: {$action} by user {$user_id}", 'INFO', $context);
    }
    
    /**
     * Log performance metrics
     */
    public function logPerformance($operation, $start_time, $end_time = null, $context = []) {
        if ($end_time === null) {
            $end_time = microtime(true);
        }
        
        $duration = ($end_time - $start_time) * 1000; // Convert to milliseconds
        
        $context['component'] = 'Performance';
        $context['operation'] = $operation;
        $context['duration_ms'] = round($duration, 2);
        $context['start_time'] = $start_time;
        $context['end_time'] = $end_time;
        
        $this->log("Performance: {$operation} took {$duration}ms", 'DEBUG', $context);
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
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Enable/disable debugging
     */
    public function setDebugEnabled($enabled) {
        $this->debug_enabled = $enabled;
    }
    
    /**
     * Set log level
     */
    public function setLogLevel($level) {
        $this->log_level = strtoupper($level);
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
        file_put_contents($this->log_file, '');
        $this->log('Log file cleared', 'SYSTEM');
    }
    
    /**
     * Get log file size
     */
    public function getLogFileSize() {
        return file_exists($this->log_file) ? filesize($this->log_file) : 0;
    }
}

// Initialize the debug system
$blackcnote_debug = BlackCnoteDebugSystem::getInstance();

// Helper functions for easy access
function blackcnote_log($message, $level = 'INFO', $context = []) {
    global $blackcnote_debug;
    $blackcnote_debug->log($message, $level, $context);
}

function blackcnote_log_error($message, $context = []) {
    blackcnote_log($message, 'ERROR', $context);
}

function blackcnote_log_warning($message, $context = []) {
    blackcnote_log($message, 'WARNING', $context);
}

function blackcnote_log_info($message, $context = []) {
    blackcnote_log($message, 'INFO', $context);
}

function blackcnote_log_debug($message, $context = []) {
    blackcnote_log($message, 'DEBUG', $context);
}

function blackcnote_log_hyiplab($message, $level = 'INFO', $context = []) {
    global $blackcnote_debug;
    $blackcnote_debug->logHyipLab($message, $level, $context);
}

function blackcnote_log_theme($message, $level = 'INFO', $context = []) {
    global $blackcnote_debug;
    $blackcnote_debug->logTheme($message, $level, $context);
}

function blackcnote_log_performance($operation, $start_time, $end_time = null, $context = []) {
    global $blackcnote_debug;
    $blackcnote_debug->logPerformance($operation, $start_time, $end_time, $context);
}

// Log system initialization
blackcnote_log('BlackCnote Debug System loaded and ready', 'SYSTEM');
?> 