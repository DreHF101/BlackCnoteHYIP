<?php
/**
 * BlackCnote Centralized Configuration System
 * 
 * Centralizes all configuration settings to eliminate conflicts
 * and provide a single source of truth for all BlackCnote settings.
 * 
 * @package BlackCnote
 * @version 2.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Configuration Class
 */
class BlackCnote_Config {
    
    private static $instance = null;
    private $config = [];
    
    /**
     * Singleton pattern
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor - Initialize configuration
     */
    private function __construct() {
        $this->initialize_config();
    }
    
    /**
     * Initialize all configuration settings
     */
    private function initialize_config() {
        $this->config = [
            // Canonical Paths
            'paths' => [
                'project_root' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote',
                'wordpress' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote',
                'wp_content' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content',
                'theme' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content/themes/blackcnote',
                'react_app' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/react-app',
                'plugins' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content/plugins',
                'uploads' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content/uploads',
                'logs' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/logs'
            ],
            
            // Service URLs
            'services' => [
                'wordpress' => 'http://localhost:8888',
                'wordpress_admin' => 'http://localhost:8888/wp-admin/',
                'react' => 'http://localhost:5174',
                'phpmyadmin' => 'http://localhost:8080',
                'redis_commander' => 'http://localhost:8081',
                'mailhog' => 'http://localhost:8025',
                'browsersync' => 'http://localhost:3000',
                'dev_tools' => 'http://localhost:9229',
                'metrics' => 'http://localhost:9091'
            ],
            
            // Performance Settings
            'performance' => [
                'memory_limit' => '512M',
                'max_memory_limit' => '1024M',
                'post_revisions' => 3,
                'autosave_interval' => 300,
                'empty_trash_days' => 7,
                'cache_key_salt' => 'blackcnote_' . md5(__FILE__)
            ],
            
            // Debug Settings
            'debug' => [
                'enabled' => true,
                'level' => 'ALL',
                'log_errors' => true,
                'display_errors' => false,
                'log_file' => 'blackcnote-debug.log'
            ],
            
            // Security Settings
            'security' => [
                'nonce_lifetime' => 86400,
                'max_login_attempts' => 5,
                'session_timeout' => 3600,
                'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'],
                'max_file_size' => 10 * 1024 * 1024 // 10MB
            ],
            
            // Theme Settings
            'theme' => [
                'version' => '2.0.0',
                'name' => 'BlackCnote',
                'description' => 'Empowering Black Wealth Through Strategic Investment',
                'author' => 'BlackCnote Team',
                'author_uri' => 'https://blackcnote.com',
                'text_domain' => 'blackcnote'
            ],
            
            // Database Settings
            'database' => [
                'prefix' => 'wp_',
                'charset' => 'utf8mb4',
                'collate' => 'utf8mb4_unicode_ci',
                'max_queries' => 100,
                'query_timeout' => 30
            ],
            
            // API Settings
            'api' => [
                'version' => 'v1',
                'namespace' => 'blackcnote/v1',
                'rate_limit' => 100,
                'timeout' => 30,
                'cors_enabled' => true
            ],
            
            // Monitoring Settings
            'monitoring' => [
                'enabled' => true,
                'check_interval' => 300, // 5 minutes
                'alert_thresholds' => [
                    'memory_usage' => 80,
                    'response_time' => 2000,
                    'disk_usage' => 85,
                    'error_rate' => 5
                ]
            ]
        ];
        
        // Apply environment-specific overrides
        $this->apply_environment_overrides();
    }
    
    /**
     * Apply environment-specific configuration overrides
     */
    private function apply_environment_overrides() {
        $environment = defined('WP_ENV') ? WP_ENV : 'development';
        
        switch ($environment) {
            case 'production':
                $this->config['debug']['enabled'] = false;
                $this->config['debug']['display_errors'] = false;
                $this->config['performance']['memory_limit'] = '256M';
                $this->config['security']['session_timeout'] = 1800;
                break;
                
            case 'staging':
                $this->config['debug']['enabled'] = true;
                $this->config['debug']['level'] = 'ERROR';
                $this->config['performance']['memory_limit'] = '512M';
                break;
                
            case 'development':
            default:
                $this->config['debug']['enabled'] = true;
                $this->config['debug']['level'] = 'ALL';
                $this->config['debug']['display_errors'] = true;
                $this->config['performance']['memory_limit'] = '1024M';
                break;
        }
    }
    
    /**
     * Get configuration value
     * @param string $key Configuration key (dot notation supported)
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $config = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                return $default;
            }
            $config = $config[$k];
        }
        
        return $config;
    }
    
    /**
     * Set configuration value
     * @param string $key Configuration key (dot notation supported)
     * @param mixed $value Configuration value
     */
    public function set($key, $value) {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }
    
    /**
     * Get canonical path
     * @param string $path_name Path name
     * @return string|null Canonical path
     */
    public function getPath($path_name) {
        return $this->get("paths.{$path_name}");
    }
    
    /**
     * Get service URL
     * @param string $service_name Service name
     * @return string|null Service URL
     */
    public function getServiceUrl($service_name) {
        return $this->get("services.{$service_name}");
    }
    
    /**
     * Get all paths
     * @return array All canonical paths
     */
    public function getAllPaths() {
        return $this->get('paths', []);
    }
    
    /**
     * Get all service URLs
     * @return array All service URLs
     */
    public function getAllServices() {
        return $this->get('services', []);
    }
    
    /**
     * Validate configuration
     * @return array Validation results
     */
    public function validate() {
        $errors = [];
        
        // Validate paths
        foreach ($this->config['paths'] as $name => $path) {
            if (!file_exists($path)) {
                $errors[] = "Path '{$name}' does not exist: {$path}";
            }
        }
        
        // Validate service URLs
        foreach ($this->config['services'] as $name => $url) {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $errors[] = "Invalid service URL '{$name}': {$url}";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Export configuration for debugging
     * @return array Configuration export
     */
    public function export() {
        return [
            'config' => $this->config,
            'validation' => $this->validate(),
            'environment' => defined('WP_ENV') ? WP_ENV : 'development',
            'timestamp' => current_time('mysql')
        ];
    }
    
    /**
     * Get configuration as constants
     * @return array Constants array
     */
    public function getConstants() {
        $constants = [];
        
        // Path constants
        foreach ($this->config['paths'] as $name => $path) {
            $constant_name = 'BLACKCNOTE_' . strtoupper($name) . '_PATH';
            $constants[$constant_name] = $path;
        }
        
        // Service constants
        foreach ($this->config['services'] as $name => $url) {
            $constant_name = 'BLACKCNOTE_' . strtoupper($name) . '_URL';
            $constants[$constant_name] = $url;
        }
        
        return $constants;
    }
}

/**
 * Global configuration instance
 */
function blackcnote_config() {
    return BlackCnote_Config::getInstance();
}

/**
 * Initialize configuration constants
 */
function blackcnote_init_constants() {
    $config = blackcnote_config();
    $constants = $config->getConstants();
    
    foreach ($constants as $name => $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}

// Initialize constants when this file is loaded
blackcnote_init_constants(); 