<?php
/**
 * BlackCnote Debug Startup Monitor Integration
 * Integrates startup script monitoring with the BlackCnote Debug System
 * 
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Debug Startup Monitor Class
 */
class BlackCnoteDebugStartupMonitor {
    
    /**
     * Debug system instance
     */
    private $debug_system;
    
    /**
     * Startup monitor instance
     */
    private $startup_monitor;
    
    /**
     * Health check interval (seconds)
     */
    private $health_check_interval = 300; // 5 minutes
    
    /**
     * Last health check time
     */
    private $last_health_check = 0;
    
    /**
     * Constructor
     */
    public function __construct($debug_system) {
        $this->debug_system = $debug_system;
        $this->init_hooks();
        $this->debug_system->log('BlackCnote Debug Startup Monitor integration initialized', 'SYSTEM');
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Add startup monitoring to admin menu
        add_action('admin_menu', [$this, 'add_startup_monitor_menu']);
        
        // Add AJAX handlers for startup monitoring
        add_action('wp_ajax_blackcnote_startup_health', [$this, 'ajax_startup_health']);
        add_action('wp_ajax_blackcnote_startup_status', [$this, 'ajax_startup_status']);
        add_action('wp_ajax_blackcnote_startup_restart', [$this, 'ajax_startup_restart']);
        
        // Add REST API endpoints
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        
        // Add health check cron job
        add_action('blackcnote_startup_health_check', [$this, 'run_health_check']);
        
        // Schedule health check if not already scheduled
        if (!wp_next_scheduled('blackcnote_startup_health_check')) {
            wp_schedule_event(time(), 'every_5_minutes', 'blackcnote_startup_health_check');
        }
    }
    
    /**
     * Add startup monitor menu
     */
    public function add_startup_monitor_menu() {
        add_submenu_page(
            'blackcnote-debug',
            'Startup Monitor',
            'Startup Monitor',
            'manage_options',
            'blackcnote-debug-startup',
            [$this, 'startup_monitor_page']
        );
    }
    
    /**
     * Startup monitor admin page
     */
    public function startup_monitor_page() {
        $health_report = $this->get_startup_health_report();
        include BLACKCNOTE_DEBUG_PLUGIN_DIR . 'admin/views/startup-monitor-page.php';
    }
    
    /**
     * Get startup health report
     */
    public function get_startup_health_report() {
        $current_time = time();
        
        // Check if we need to run a new health check
        if ($current_time - $this->last_health_check >= $this->health_check_interval) {
            $this->run_health_check();
            $this->last_health_check = $current_time;
        }
        
        // Try to get cached health report
        $health_report = get_transient('blackcnote_startup_health_report');
        
        if (!$health_report) {
            // Generate new health report
            $health_report = $this->generate_health_report();
            set_transient('blackcnote_startup_health_report', $health_report, 300); // Cache for 5 minutes
        }
        
        return $health_report;
    }
    
    /**
     * Generate comprehensive health report
     */
    private function generate_health_report() {
        $report = [
            'timestamp' => current_time('mysql'),
            'startup_script' => $this->check_startup_script(),
            'docker_services' => $this->check_docker_services(),
            'wordpress_services' => $this->check_wordpress_services(),
            'system_resources' => $this->check_system_resources(),
            'overall_health' => 'unknown',
            'metrics' => []
        ];
        
        // Calculate overall health
        $critical_issues = 0;
        $warnings = 0;
        
        // Check startup script
        if (!$report['startup_script']['exists']) {
            $critical_issues++;
        }
        
        // Check Docker services
        foreach ($report['docker_services'] as $service) {
            if ($service['required'] && !$service['healthy']) {
                $critical_issues++;
            } elseif (!$service['required'] && !$service['healthy']) {
                $warnings++;
            }
        }
        
        // Check WordPress services
        foreach ($report['wordpress_services'] as $service) {
            if ($service['required'] && !$service['healthy']) {
                $critical_issues++;
            } elseif (!$service['required'] && !$service['healthy']) {
                $warnings++;
            }
        }
        
        // Determine overall health
        if ($critical_issues === 0) {
            $report['overall_health'] = 'healthy';
        } elseif ($critical_issues <= 2) {
            $report['overall_health'] = 'degraded';
        } else {
            $report['overall_health'] = 'critical';
        }
        
        $report['metrics'] = [
            'critical_issues' => $critical_issues,
            'warnings' => $warnings,
            'total_services' => count($report['docker_services']) + count($report['wordpress_services'])
        ];
        
        return $report;
    }
    
    /**
     * Check startup script health
     */
    private function check_startup_script() {
        $project_root = dirname(WP_CONTENT_DIR, 2);
        $startup_script = $project_root . '/start-blackcnote-complete.ps1';
        
        $health = [
            'exists' => file_exists($startup_script),
            'readable' => is_readable($startup_script),
            'executable' => is_executable($startup_script),
            'last_modified' => file_exists($startup_script) ? filemtime($startup_script) : 0,
            'size' => file_exists($startup_script) ? filesize($startup_script) : 0,
            'path' => $startup_script
        ];
        
        if (!$health['exists']) {
            $this->debug_system->log('Startup script not found', 'ERROR', [
                'path' => $startup_script
            ]);
        }
        
        return $health;
    }
    
    /**
     * Check Docker services
     */
    private function check_docker_services() {
        $services = [
            [
                'name' => 'Docker Desktop',
                'type' => 'process',
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'Docker Daemon',
                'type' => 'daemon',
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'WordPress Container',
                'type' => 'container',
                'container' => 'blackcnote_wordpress',
                'url' => 'http://localhost:8888',
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'React Container',
                'type' => 'container',
                'container' => 'blackcnote_react',
                'url' => 'http://localhost:5174',
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'MySQL Container',
                'type' => 'container',
                'container' => 'blackcnote_mysql',
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'Redis Container',
                'type' => 'container',
                'container' => 'blackcnote_redis',
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'phpMyAdmin Container',
                'type' => 'container',
                'container' => 'blackcnote_phpmyadmin',
                'url' => 'http://localhost:8080',
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'MailHog Container',
                'type' => 'container',
                'container' => 'blackcnote_mailhog',
                'url' => 'http://localhost:8025',
                'required' => false,
                'healthy' => false
            ],
            [
                'name' => 'Redis Commander Container',
                'type' => 'container',
                'container' => 'blackcnote_redis_commander',
                'url' => 'http://localhost:8081',
                'required' => false,
                'healthy' => false
            ],
            [
                'name' => 'Browsersync Container',
                'type' => 'container',
                'container' => 'blackcnote_browsersync',
                'url' => 'http://localhost:3000',
                'required' => false,
                'healthy' => false
            ]
        ];
        
        foreach ($services as &$service) {
            switch ($service['type']) {
                case 'process':
                    $service['healthy'] = $this->check_docker_desktop_process();
                    break;
                    
                case 'daemon':
                    $service['healthy'] = $this->check_docker_daemon();
                    break;
                    
                case 'container':
                    $service['healthy'] = $this->check_docker_container($service['container']);
                    
                    // Also check URL if available
                    if (isset($service['url'])) {
                        $service['url_healthy'] = $this->check_service_url($service['url']);
                    }
                    break;
            }
        }
        
        return $services;
    }
    
    /**
     * Check WordPress services
     */
    private function check_wordpress_services() {
        $services = [
            [
                'name' => 'WordPress Core',
                'url' => home_url(),
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'WordPress Admin',
                'url' => admin_url(),
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'WordPress REST API',
                'url' => rest_url(),
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'BlackCnote Theme',
                'type' => 'theme',
                'theme' => 'blackcnote',
                'required' => true,
                'healthy' => false
            ],
            [
                'name' => 'BlackCnote Debug System',
                'type' => 'plugin',
                'plugin' => 'blackcnote-debug-system/blackcnote-debug-system.php',
                'required' => true,
                'healthy' => false
            ]
        ];
        
        foreach ($services as &$service) {
            if (isset($service['url'])) {
                $service['healthy'] = $this->check_service_url($service['url']);
            } elseif (isset($service['theme'])) {
                $service['healthy'] = $this->check_theme_status($service['theme']);
            } elseif (isset($service['plugin'])) {
                $service['healthy'] = $this->check_plugin_status($service['plugin']);
            }
        }
        
        return $services;
    }
    
    /**
     * Check system resources
     */
    private function check_system_resources() {
        $resources = [
            'memory' => [
                'limit' => ini_get('memory_limit'),
                'usage' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true)
            ],
            'disk' => [
                'free' => disk_free_space(WP_CONTENT_DIR),
                'total' => disk_total_space(WP_CONTENT_DIR),
                'usage_percent' => 0
            ],
            'php' => [
                'version' => PHP_VERSION,
                'extensions' => get_loaded_extensions(),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize')
            ]
        ];
        
        // Calculate disk usage percentage
        if ($resources['disk']['free'] !== false && $resources['disk']['total'] !== false) {
            $used = $resources['disk']['total'] - $resources['disk']['free'];
            $resources['disk']['usage_percent'] = round(($used / $resources['disk']['total']) * 100, 2);
        }
        
        return $resources;
    }
    
    /**
     * Check Docker Desktop process
     */
    private function check_docker_desktop_process() {
        if (function_exists('shell_exec')) {
            $output = shell_exec('tasklist /FI "IMAGENAME eq Docker Desktop.exe" 2>&1');
            return strpos($output, 'Docker Desktop.exe') !== false;
        }
        return false;
    }
    
    /**
     * Check Docker daemon
     */
    private function check_docker_daemon() {
        if (function_exists('shell_exec')) {
            $output = shell_exec('docker info 2>&1');
            return strpos($output, 'Server Version') !== false;
        }
        return false;
    }
    
    /**
     * Check Docker container
     */
    private function check_docker_container($container_name) {
        if (function_exists('shell_exec')) {
            $output = shell_exec("docker ps --filter name=$container_name --format '{{.Names}}' 2>&1");
            return strpos($output, $container_name) !== false;
        }
        return false;
    }
    
    /**
     * Check service URL
     */
    private function check_service_url($url) {
        $response = wp_remote_get($url, [
            'timeout' => 5,
            'user-agent' => 'BlackCnote-StartupMonitor/1.0'
        ]);
        
        return !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
    }
    
    /**
     * Check theme status
     */
    private function check_theme_status($theme_name) {
        $theme = wp_get_theme($theme_name);
        return $theme->exists();
    }
    
    /**
     * Check plugin status
     */
    private function check_plugin_status($plugin_file) {
        return is_plugin_active($plugin_file);
    }
    
    /**
     * Run health check
     */
    public function run_health_check() {
        $this->debug_system->log('Running startup health check', 'INFO');
        
        $health_report = $this->generate_health_report();
        
        // Log health status
        $this->debug_system->log('Startup health check completed', 'INFO', [
            'overall_health' => $health_report['overall_health'],
            'critical_issues' => $health_report['metrics']['critical_issues'],
            'warnings' => $health_report['metrics']['warnings']
        ]);
        
        // Log critical issues
        if ($health_report['metrics']['critical_issues'] > 0) {
            $this->debug_system->log('Critical startup issues detected', 'ERROR', [
                'critical_issues' => $health_report['metrics']['critical_issues'],
                'overall_health' => $health_report['overall_health']
            ]);
        }
        
        return $health_report;
    }
    
    /**
     * AJAX handler for startup health
     */
    public function ajax_startup_health() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $health_report = $this->get_startup_health_report();
        wp_send_json_success($health_report);
    }
    
    /**
     * AJAX handler for startup status
     */
    public function ajax_startup_status() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $health_report = $this->get_startup_health_report();
        $status = [
            'overall_health' => $health_report['overall_health'],
            'critical_issues' => $health_report['metrics']['critical_issues'],
            'warnings' => $health_report['metrics']['warnings'],
            'timestamp' => $health_report['timestamp']
        ];
        
        wp_send_json_success($status);
    }
    
    /**
     * AJAX handler for startup restart
     */
    public function ajax_startup_restart() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $this->debug_system->log('Startup restart requested via admin interface', 'INFO');
        
        // This would trigger the startup script
        // For now, just log the request
        wp_send_json_success(['message' => 'Restart request logged']);
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('blackcnote/v1', '/startup/health', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_startup_health'],
            'permission_callback' => [$this, 'rest_permission_callback']
        ]);
        
        register_rest_route('blackcnote/v1', '/startup/status', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_startup_status'],
            'permission_callback' => [$this, 'rest_permission_callback']
        ]);
    }
    
    /**
     * REST API permission callback
     */
    public function rest_permission_callback() {
        return current_user_can('manage_options');
    }
    
    /**
     * REST API startup health endpoint
     */
    public function rest_startup_health() {
        $health_report = $this->get_startup_health_report();
        return new WP_REST_Response($health_report, 200);
    }
    
    /**
     * REST API startup status endpoint
     */
    public function rest_startup_status() {
        $health_report = $this->get_startup_health_report();
        $status = [
            'overall_health' => $health_report['overall_health'],
            'critical_issues' => $health_report['metrics']['critical_issues'],
            'warnings' => $health_report['metrics']['warnings'],
            'timestamp' => $health_report['timestamp']
        ];
        
        return new WP_REST_Response($status, 200);
    }
} 