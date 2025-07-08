<?php
/**
 * BlackCnote Enhanced Debug System
 * Comprehensive debugging for WordPress/React integration, localhost, live editing, Browsersync, and XAMPP
 */

// Prevent direct access
if (!defined('ABSPATH') && !defined('BLACKCNOTE_DEBUG')) {
    define('BLACKCNOTE_DEBUG', true);
}

class BlackCnoteEnhancedDebugSystem {
    
    private static $instance = null;
    private $log_file;
    private $debug_enabled = true;
    private $log_level = 'ALL';
    private $environment_info = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Set log file path with fallback
        if (defined('WP_CONTENT_DIR')) {
            $this->log_file = WP_CONTENT_DIR . '/blackcnote-enhanced-debug.log';
        } else {
            // Fallback for when WordPress isn't loaded
            $this->log_file = dirname(__FILE__) . '/blackcnote-enhanced-debug.log';
        }
        
        $this->detectEnvironment();
        $this->setupErrorHandling();
        $this->setupExceptionHandling();
        $this->setupShutdownHandling();
        $this->setupReactIntegration();
        $this->setupAjaxHandlers();
        $this->log('BlackCnote Enhanced Debug System initialized', 'SYSTEM');
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
     * Detect environment type
     */
    private function detectEnvironmentType() {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return 'development';
        } elseif (defined('WP_ENVIRONMENT_TYPE')) {
            return WP_ENVIRONMENT_TYPE;
        }
        
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            return 'development';
        } elseif (strpos($host, 'staging') !== false || strpos($host, 'test') !== false) {
            return 'staging';
        }
        
        return 'production';
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
        
        // Get base path with fallback
        $base_path = defined('ABSPATH') ? ABSPATH : dirname(dirname(__FILE__)) . '/';
        
        // Check for Node.js/npm
        if (file_exists($base_path . 'package.json')) {
            $tools['nodejs'] = true;
            $package_json = json_decode(file_get_contents($base_path . 'package.json'), true);
            $tools['package_name'] = $package_json['name'] ?? 'unknown';
            $tools['package_version'] = $package_json['version'] ?? 'unknown';
        }
        
        // Check for Vite
        if (file_exists($base_path . 'vite.config.ts') || file_exists($base_path . 'vite.config.js')) {
            $tools['vite'] = true;
        }
        
        // Check for React
        if (file_exists($base_path . 'src/App.tsx') || file_exists($base_path . 'src/App.jsx')) {
            $tools['react'] = true;
        }
        
        // Check for TypeScript
        if (file_exists($base_path . 'tsconfig.json')) {
            $tools['typescript'] = true;
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
        
        // Get base path with fallback
        $base_path = defined('ABSPATH') ? ABSPATH : dirname(dirname(__FILE__)) . '/';
        
        if (file_exists($base_path . 'src/App.tsx') || file_exists($base_path . 'src/App.jsx')) {
            $react_info['enabled'] = true;
            
            // Detect build system
            if (file_exists($base_path . 'vite.config.ts')) {
                $react_info['build_system'] = 'vite';
            } elseif (file_exists($base_path . 'webpack.config.js')) {
                $react_info['build_system'] = 'webpack';
            }
            
            // Check for API endpoints
            $api_files = [
                $base_path . 'src/api/',
                $base_path . 'api/',
                $base_path . 'wp-content/plugins/hyiplab/api/'
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
        
        // Get base path with fallback
        $base_path = defined('ABSPATH') ? ABSPATH : dirname(dirname(__FILE__)) . '/';
        
        if (file_exists($base_path . 'package.json')) {
            $package_json = json_decode(file_get_contents($base_path . 'package.json'), true);
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
        
        // Get base path with fallback
        $base_path = defined('ABSPATH') ? ABSPATH : dirname(dirname(__FILE__)) . '/';
        
        $bs_config_files = [
            'bs-config.js',
            'bs-config.cjs',
            'browsersync.config.js',
            '.browsersyncrc'
        ];
        
        foreach ($bs_config_files as $config_file) {
            if (file_exists($base_path . $config_file)) {
                $browsersync['enabled'] = true;
                $browsersync['config_file'] = $config_file;
                break;
            }
        }
        
        if (file_exists($base_path . 'package.json')) {
            $package_json = json_decode(file_get_contents($base_path . 'package.json'), true);
            $dependencies = array_merge(
                $package_json['dependencies'] ?? [],
                $package_json['devDependencies'] ?? []
            );
            
            if (isset($dependencies['browser-sync'])) {
                $browsersync['enabled'] = true;
                $browsersync['version'] = $dependencies['browser-sync'];
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
            
            if (strpos($host, 'localhost') !== false) {
                $localhost['type'] = 'localhost';
            } elseif (strpos($host, '127.0.0.1') !== false) {
                $localhost['type'] = 'ip';
            } else {
                $localhost['type'] = 'local';
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
        
        // Get base path with fallback
        $base_path = defined('ABSPATH') ? ABSPATH : dirname(dirname(__FILE__)) . '/';
        
        if (file_exists($base_path . 'package.json')) {
            $package_json = json_decode(file_get_contents($base_path . 'package.json'), true);
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
        
        $potential_watched_dirs = ['src', 'public', 'wp-content', 'hyiplab', 'blackcnote'];
        foreach ($potential_watched_dirs as $dir) {
            if (is_dir($base_path . $dir)) {
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
        
        // Get base path with fallback
        $base_path = defined('ABSPATH') ? ABSPATH : dirname(dirname(__FILE__)) . '/';
        
        $config_files = [
            'vite.config.ts',
            'vite.config.js',
            'webpack.config.js',
            'webpack.dev.js',
            'rollup.config.js'
        ];
        
        foreach ($config_files as $config_file) {
            if (file_exists($base_path . $config_file)) {
                $content = file_get_contents($base_path . $config_file);
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
     * Setup React integration debugging
     */
    private function setupReactIntegration() {
        if ($this->environment_info['react_integration']['enabled'] && function_exists('add_action')) {
            // Only setup WordPress hooks if WordPress is loaded
            add_action('wp_head', [$this, 'injectReactDebugScript']);
            add_action('wp_footer', [$this, 'injectReactDebugFooter']);
        }
    }
    
    /**
     * Setup AJAX handlers for React communication
     */
    private function setupAjaxHandlers() {
        if (function_exists('add_action')) {
            // Only setup WordPress AJAX handlers if WordPress is loaded
            add_action('wp_ajax_blackcnote_react_debug', [$this, 'handleReactDebug']);
            add_action('wp_ajax_nopriv_blackcnote_react_debug', [$this, 'handleReactDebug']);
        }
    }
    
    /**
     * Inject React debug script
     */
    public function injectReactDebugScript() {
        if ($this->environment_info['react_integration']['enabled'] && function_exists('admin_url')) {
            echo '<script>
                window.blackcnoteDebug = {
                    enabled: true,
                    environment: ' . json_encode($this->environment_info) . ',
                    logger: {
                        debug: function(message, data) { 
                            this._sendToPHP("debug", message, data);
                        },
                        info: function(message, data) {
                            this._sendToPHP("info", message, data);
                        },
                        warn: function(message, data) {
                            this._sendToPHP("warn", message, data);
                        },
                        error: function(message, data) {
                            this._sendToPHP("error", message, data);
                        },
                        _sendToPHP: function(level, message, data) {
                            fetch("' . admin_url('admin-ajax.php') . '", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded",
                                },
                                body: "action=blackcnote_react_debug&level=" + level + "&message=" + encodeURIComponent(message) + "&data=" + encodeURIComponent(JSON.stringify(data || {}))
                            }).catch(function(error) {
                                console.error("Failed to send debug to PHP:", error);
                            });
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
                // Log React component lifecycle
                if (window.React && window.React.Component) {
                    const originalComponentDidMount = window.React.Component.prototype.componentDidMount;
                    const originalComponentDidUpdate = window.React.Component.prototype.componentDidUpdate;
                    const originalComponentWillUnmount = window.React.Component.prototype.componentWillUnmount;
                    
                    window.React.Component.prototype.componentDidMount = function() {
                        if (window.blackcnoteDebug && window.blackcnoteDebug.logger) {
                            window.blackcnoteDebug.logger.info("React component mounted", {
                                component: this.constructor.name,
                                props: this.props
                            });
                        }
                        if (originalComponentDidMount) {
                            originalComponentDidMount.call(this);
                        }
                    };
                    
                    window.React.Component.prototype.componentDidUpdate = function(prevProps, prevState) {
                        if (window.blackcnoteDebug && window.blackcnoteDebug.logger) {
                            window.blackcnoteDebug.logger.debug("React component updated", {
                                component: this.constructor.name,
                                prevProps: prevProps,
                                prevState: prevState,
                                currentProps: this.props,
                                currentState: this.state
                            });
                        }
                        if (originalComponentDidUpdate) {
                            originalComponentDidUpdate.call(this, prevProps, prevState);
                        }
                    };
                    
                    window.React.Component.prototype.componentWillUnmount = function() {
                        if (window.blackcnoteDebug && window.blackcnoteDebug.logger) {
                            window.blackcnoteDebug.logger.info("React component unmounted", {
                                component: this.constructor.name
                            });
                        }
                        if (originalComponentWillUnmount) {
                            originalComponentWillUnmount.call(this);
                        }
                    };
                }
            </script>';
        }
    }
    
    /**
     * Handle React debug messages
     */
    public function handleReactDebug() {
        if (function_exists('sanitize_text_field') && function_exists('wp_die')) {
            $level = sanitize_text_field($_POST['level'] ?? 'info');
            $message = sanitize_text_field($_POST['message'] ?? '');
            $data = json_decode(stripslashes($_POST['data'] ?? '{}'), true);
            
            $this->logReact($message, strtoupper($level), $data);
            
            wp_die('OK');
        }
    }
    
    /**
     * Setup error handling
     */
    private function setupErrorHandling() {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
        
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        ini_set('error_log', $this->log_file);
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
        
        if ($errno === E_ERROR || $errno === E_PARSE || $errno === E_CORE_ERROR || 
            $errno === E_COMPILE_ERROR || $errno === E_USER_ERROR) {
            return false;
        }
        
        return true;
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
     * Handle shutdown
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
        
        if (!empty($context)) {
            $log_entry .= "\nContext: " . json_encode($context, JSON_PRETTY_PRINT);
        }
        
        $log_entry .= "\nEnvironment: " . json_encode($this->environment_info, JSON_PRETTY_PRINT);
        
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
        if (defined('ABSPATH') && function_exists('get_bloginfo')) {
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
        } else {
            $log_entry .= "\nWordPress: " . json_encode([
                'status' => 'not_loaded',
                'wp_debug' => defined('WP_DEBUG') ? WP_DEBUG : false,
                'wp_debug_log' => defined('WP_DEBUG_LOG') ? WP_DEBUG_LOG : false,
                'wp_debug_display' => defined('WP_DEBUG_DISPLAY') ? WP_DEBUG_DISPLAY : false
            ], JSON_PRETTY_PRINT);
        }
        
        $log_entry .= "\n" . str_repeat('-', 80) . "\n";
        
        file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);
        
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
}

// Initialize the enhanced debug system
$blackcnote_enhanced_debug = BlackCnoteEnhancedDebugSystem::getInstance();

// Helper functions for easy access
function blackcnote_enhanced_log($message, $level = 'INFO', $context = []) {
    global $blackcnote_enhanced_debug;
    $blackcnote_enhanced_debug->log($message, $level, $context);
}

function blackcnote_react_log($message, $level = 'INFO', $context = []) {
    global $blackcnote_enhanced_debug;
    $blackcnote_enhanced_debug->logReact($message, $level, $context);
}

function blackcnote_dev_log($message, $level = 'INFO', $context = []) {
    global $blackcnote_enhanced_debug;
    $blackcnote_enhanced_debug->logDevelopment($message, $level, $context);
}

function blackcnote_live_log($message, $level = 'INFO', $context = []) {
    global $blackcnote_enhanced_debug;
    $blackcnote_enhanced_debug->logLiveEditing($message, $level, $context);
}

function blackcnote_bs_log($message, $level = 'INFO', $context = []) {
    global $blackcnote_enhanced_debug;
    $blackcnote_enhanced_debug->logBrowsersync($message, $level, $context);
}

function blackcnote_localhost_log($message, $level = 'INFO', $context = []) {
    global $blackcnote_enhanced_debug;
    $blackcnote_enhanced_debug->logLocalhost($message, $level, $context);
}

// Log system initialization
blackcnote_enhanced_log('BlackCnote Enhanced Debug System loaded and ready', 'SYSTEM');
?> 