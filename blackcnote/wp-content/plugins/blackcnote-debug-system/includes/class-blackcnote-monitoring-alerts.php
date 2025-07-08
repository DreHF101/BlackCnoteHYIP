<?php
/**
 * BlackCnote Monitoring and Alerting System
 * 
 * Provides comprehensive monitoring and alerting for all BlackCnote services
 * Integrates with the existing debug system for enhanced functionality
 * 
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Monitoring and Alerting Class
 */
class BlackCnote_Monitoring_Alerts {
    
    private $debug_system;
    private $alert_thresholds;
    private $monitoring_config;
    private $last_check_time;
    
    /**
     * Constructor
     */
    public function __construct($debug_system = null) {
        $this->debug_system = $debug_system;
        $this->initialize_thresholds();
        $this->initialize_monitoring_config();
        $this->last_check_time = time();
        
        // Register hooks
        add_action('init', [$this, 'init_monitoring']);
        add_action('wp_ajax_blackcnote_monitoring_status', [$this, 'ajax_monitoring_status']);
        add_action('wp_ajax_blackcnote_monitoring_alerts', [$this, 'ajax_monitoring_alerts']);
        add_action('rest_api_init', [$this, 'register_monitoring_endpoints']);
        
        // Schedule monitoring checks
        if (!wp_next_scheduled('blackcnote_monitoring_check')) {
            wp_schedule_event(time(), 'every_5_minutes', 'blackcnote_monitoring_check');
        }
        add_action('blackcnote_monitoring_check', [$this, 'run_monitoring_check']);
    }
    
    /**
     * Initialize alert thresholds
     */
    private function initialize_thresholds() {
        $this->alert_thresholds = [
            'memory_usage' => [
                'warning' => 80, // 80% of memory limit
                'critical' => 95 // 95% of memory limit
            ],
            'response_time' => [
                'warning' => 2000, // 2 seconds
                'critical' => 5000 // 5 seconds
            ],
            'disk_usage' => [
                'warning' => 85, // 85% disk usage
                'critical' => 95 // 95% disk usage
            ],
            'database_queries' => [
                'warning' => 100, // 100 queries per request
                'critical' => 200 // 200 queries per request
            ],
            'error_rate' => [
                'warning' => 5, // 5% error rate
                'critical' => 10 // 10% error rate
            ]
        ];
    }
    
    /**
     * Initialize monitoring configuration
     */
    private function initialize_monitoring_config() {
        $this->monitoring_config = [
            'services' => [
                'wordpress' => [
                    'url' => 'http://localhost:8888',
                    'name' => 'WordPress Frontend',
                    'critical' => true
                ],
                'wordpress_admin' => [
                    'url' => 'http://localhost:8888/wp-admin/',
                    'name' => 'WordPress Admin',
                    'critical' => true
                ],
                'react' => [
                    'url' => 'http://localhost:5174',
                    'name' => 'React App',
                    'critical' => false
                ],
                'phpmyadmin' => [
                    'url' => 'http://localhost:8080',
                    'name' => 'phpMyAdmin',
                    'critical' => false
                ],
                'redis_commander' => [
                    'url' => 'http://localhost:8081',
                    'name' => 'Redis Commander',
                    'critical' => false
                ],
                'mailhog' => [
                    'url' => 'http://localhost:8025',
                    'name' => 'MailHog',
                    'critical' => false
                ]
            ],
            'canonical_paths' => [
                'project_root' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote',
                'wordpress' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote',
                'theme' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content/themes/blackcnote',
                'react_app' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/react-app',
                'logs' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/logs'
            ]
        ];
    }
    
    /**
     * Initialize monitoring
     */
    public function init_monitoring() {
        // Add custom cron interval
        add_filter('cron_schedules', [$this, 'add_cron_intervals']);
        
        // Register monitoring endpoints
        $this->register_monitoring_endpoints();
    }
    
    /**
     * Add custom cron intervals
     */
    public function add_cron_intervals($schedules) {
        $schedules['every_5_minutes'] = [
            'interval' => 300,
            'display' => 'Every 5 Minutes'
        ];
        
        $schedules['every_30_seconds'] = [
            'interval' => 30,
            'display' => 'Every 30 Seconds'
        ];
        
        return $schedules;
    }
    
    /**
     * Register monitoring REST endpoints
     */
    public function register_monitoring_endpoints() {
        register_rest_route('blackcnote/v1', '/monitoring/status', [
            'methods' => 'GET',
            'callback' => [$this, 'get_monitoring_status'],
            'permission_callback' => '__return_true'
        ]);
        
        register_rest_route('blackcnote/v1', '/monitoring/alerts', [
            'methods' => 'GET',
            'callback' => [$this, 'get_monitoring_alerts'],
            'permission_callback' => '__return_true'
        ]);
        
        register_rest_route('blackcnote/v1', '/monitoring/metrics', [
            'methods' => 'GET',
            'callback' => [$this, 'get_monitoring_metrics'],
            'permission_callback' => '__return_true'
        ]);
    }
    
    /**
     * Run monitoring check
     */
    public function run_monitoring_check() {
        $check_results = [
            'timestamp' => current_time('mysql'),
            'services' => $this->check_services(),
            'performance' => $this->check_performance(),
            'canonical_paths' => $this->check_canonical_paths(),
            'alerts' => []
        ];
        
        // Generate alerts
        $check_results['alerts'] = $this->generate_alerts($check_results);
        
        // Log results
        if ($this->debug_system) {
            $this->debug_system->log('Monitoring check completed', 'MONITORING', $check_results);
        }
        
        // Store results
        update_option('blackcnote_monitoring_last_check', $check_results);
        update_option('blackcnote_monitoring_last_check_time', time());
        
        // Send critical alerts
        $this->send_critical_alerts($check_results['alerts']);
        
        return $check_results;
    }
    
    /**
     * Check services
     */
    private function check_services() {
        $results = [];
        
        foreach ($this->monitoring_config['services'] as $service_key => $service) {
            $start_time = microtime(true);
            $response = wp_remote_get($service['url'], [
                'timeout' => 10,
                'user-agent' => 'BlackCnote-Monitoring/1.0'
            ]);
            $end_time = microtime(true);
            
            $response_time = round(($end_time - $start_time) * 1000, 2);
            $is_success = !is_wp_error($response);
            $response_code = $is_success ? wp_remote_retrieve_response_code($response) : 0;
            
            $results[$service_key] = [
                'name' => $service['name'],
                'url' => $service['url'],
                'status' => $is_success && $response_code >= 200 && $response_code < 400 ? 'up' : 'down',
                'response_code' => $response_code,
                'response_time_ms' => $response_time,
                'critical' => $service['critical'],
                'last_check' => current_time('mysql')
            ];
        }
        
        return $results;
    }
    
    /**
     * Check performance metrics
     */
    private function check_performance() {
        global $wpdb;
        
        $memory_usage = memory_get_usage(true);
        $memory_limit = $this->parse_memory_limit(ini_get('memory_limit'));
        $memory_percentage = $memory_limit > 0 ? ($memory_usage / $memory_limit) * 100 : 0;
        
        $disk_free = disk_free_space(ABSPATH);
        $disk_total = disk_total_space(ABSPATH);
        $disk_usage_percentage = $disk_total > 0 ? (($disk_total - $disk_free) / $disk_total) * 100 : 0;
        
        return [
            'memory' => [
                'usage_bytes' => $memory_usage,
                'limit_bytes' => $memory_limit,
                'usage_percentage' => round($memory_percentage, 2),
                'peak_bytes' => memory_get_peak_usage(true)
            ],
            'disk' => [
                'free_bytes' => $disk_free,
                'total_bytes' => $disk_total,
                'usage_percentage' => round($disk_usage_percentage, 2)
            ],
            'database' => [
                'queries' => get_num_queries(),
                'last_error' => $wpdb->last_error ?: null
            ],
            'load_time' => timer_stop(),
            'uptime' => time() - get_option('blackcnote_startup_time', time())
        ];
    }
    
    /**
     * Check canonical paths
     */
    private function check_canonical_paths() {
        $results = [];
        
        foreach ($this->monitoring_config['canonical_paths'] as $path_key => $path) {
            $results[$path_key] = [
                'path' => $path,
                'exists' => file_exists($path),
                'readable' => is_readable($path),
                'writable' => is_writable($path),
                'size' => file_exists($path) ? filesize($path) : 0
            ];
        }
        
        return $results;
    }
    
    /**
     * Generate alerts
     */
    private function generate_alerts($check_results) {
        $alerts = [];
        
        // Check service alerts
        foreach ($check_results['services'] as $service_key => $service) {
            if ($service['status'] === 'down') {
                $alerts[] = [
                    'type' => $service['critical'] ? 'critical' : 'warning',
                    'category' => 'service',
                    'message' => "Service {$service['name']} is down",
                    'details' => [
                        'service' => $service_key,
                        'url' => $service['url'],
                        'response_code' => $service['response_code']
                    ],
                    'timestamp' => current_time('mysql')
                ];
            }
            
            // Check response time alerts
            if ($service['response_time_ms'] > $this->alert_thresholds['response_time']['warning']) {
                $alerts[] = [
                    'type' => $service['response_time_ms'] > $this->alert_thresholds['response_time']['critical'] ? 'critical' : 'warning',
                    'category' => 'performance',
                    'message' => "Service {$service['name']} is slow",
                    'details' => [
                        'service' => $service_key,
                        'response_time_ms' => $service['response_time_ms'],
                        'threshold' => $this->alert_thresholds['response_time']['warning']
                    ],
                    'timestamp' => current_time('mysql')
                ];
            }
        }
        
        // Check performance alerts
        $performance = $check_results['performance'];
        
        // Memory usage alert
        if ($performance['memory']['usage_percentage'] > $this->alert_thresholds['memory_usage']['warning']) {
            $alerts[] = [
                'type' => $performance['memory']['usage_percentage'] > $this->alert_thresholds['memory_usage']['critical'] ? 'critical' : 'warning',
                'category' => 'performance',
                'message' => 'High memory usage detected',
                'details' => [
                    'usage_percentage' => $performance['memory']['usage_percentage'],
                    'usage_bytes' => $performance['memory']['usage_bytes'],
                    'limit_bytes' => $performance['memory']['limit_bytes']
                ],
                'timestamp' => current_time('mysql')
            ];
        }
        
        // Disk usage alert
        if ($performance['disk']['usage_percentage'] > $this->alert_thresholds['disk_usage']['warning']) {
            $alerts[] = [
                'type' => $performance['disk']['usage_percentage'] > $this->alert_thresholds['disk_usage']['critical'] ? 'critical' : 'warning',
                'category' => 'system',
                'message' => 'High disk usage detected',
                'details' => [
                    'usage_percentage' => $performance['disk']['usage_percentage'],
                    'free_bytes' => $performance['disk']['free_bytes'],
                    'total_bytes' => $performance['disk']['total_bytes']
                ],
                'timestamp' => current_time('mysql')
            ];
        }
        
        // Database queries alert
        if ($performance['database']['queries'] > $this->alert_thresholds['database_queries']['warning']) {
            $alerts[] = [
                'type' => $performance['database']['queries'] > $this->alert_thresholds['database_queries']['critical'] ? 'critical' : 'warning',
                'category' => 'performance',
                'message' => 'High number of database queries',
                'details' => [
                    'queries' => $performance['database']['queries'],
                    'threshold' => $this->alert_thresholds['database_queries']['warning']
                ],
                'timestamp' => current_time('mysql')
            ];
        }
        
        // Canonical paths alert
        foreach ($check_results['canonical_paths'] as $path_key => $path) {
            if (!$path['exists']) {
                $alerts[] = [
                    'type' => 'critical',
                    'category' => 'canonical_paths',
                    'message' => "Canonical path missing: {$path_key}",
                    'details' => [
                        'path' => $path['path'],
                        'path_key' => $path_key
                    ],
                    'timestamp' => current_time('mysql')
                ];
            }
        }
        
        return $alerts;
    }
    
    /**
     * Send critical alerts
     */
    private function send_critical_alerts($alerts) {
        $critical_alerts = array_filter($alerts, function($alert) {
            return $alert['type'] === 'critical';
        });
        
        if (empty($critical_alerts)) {
            return;
        }
        
        // Log critical alerts
        if ($this->debug_system) {
            $this->debug_system->log('Critical alerts detected', 'ALERT', $critical_alerts);
        }
        
        // Send email alerts (if configured)
        $this->send_email_alerts($critical_alerts);
        
        // Store alerts for admin interface
        $existing_alerts = get_option('blackcnote_critical_alerts', []);
        $existing_alerts = array_merge($existing_alerts, $critical_alerts);
        
        // Keep only last 50 alerts
        if (count($existing_alerts) > 50) {
            $existing_alerts = array_slice($existing_alerts, -50);
        }
        
        update_option('blackcnote_critical_alerts', $existing_alerts);
    }
    
    /**
     * Send email alerts
     */
    private function send_email_alerts($alerts) {
        $admin_email = get_option('admin_email');
        if (!$admin_email) {
            return;
        }
        
        $subject = 'BlackCnote Critical Alert - ' . count($alerts) . ' issues detected';
        $message = "Critical alerts detected in BlackCnote system:\n\n";
        
        foreach ($alerts as $alert) {
            $message .= "Type: {$alert['type']}\n";
            $message .= "Category: {$alert['category']}\n";
            $message .= "Message: {$alert['message']}\n";
            $message .= "Time: {$alert['timestamp']}\n\n";
        }
        
        $message .= "Please check the BlackCnote admin panel for more details.\n";
        $message .= "Admin URL: " . admin_url('admin.php?page=blackcnote-debug') . "\n";
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Get monitoring status
     */
    public function get_monitoring_status() {
        $last_check = get_option('blackcnote_monitoring_last_check');
        
        if (!$last_check) {
            // Run initial check
            $last_check = $this->run_monitoring_check();
        }
        
        return $last_check;
    }
    
    /**
     * Get monitoring alerts
     */
    public function get_monitoring_alerts() {
        $alerts = get_option('blackcnote_critical_alerts', []);
        
        return [
            'alerts' => $alerts,
            'total_alerts' => count($alerts),
            'critical_alerts' => count(array_filter($alerts, function($alert) {
                return $alert['type'] === 'critical';
            })),
            'warning_alerts' => count(array_filter($alerts, function($alert) {
                return $alert['type'] === 'warning';
            }))
        ];
    }
    
    /**
     * Get monitoring metrics
     */
    public function get_monitoring_metrics() {
        $status = $this->get_monitoring_status();
        
        return [
            'services' => [
                'total' => count($status['services']),
                'up' => count(array_filter($status['services'], function($service) {
                    return $service['status'] === 'up';
                })),
                'down' => count(array_filter($status['services'], function($service) {
                    return $service['status'] === 'down';
                }))
            ],
            'performance' => $status['performance'],
            'alerts' => [
                'total' => count($status['alerts']),
                'critical' => count(array_filter($status['alerts'], function($alert) {
                    return $alert['type'] === 'critical';
                })),
                'warning' => count(array_filter($status['alerts'], function($alert) {
                    return $alert['type'] === 'warning';
                }))
            ]
        ];
    }
    
    /**
     * AJAX monitoring status
     */
    public function ajax_monitoring_status() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $status = $this->get_monitoring_status();
        wp_send_json_success($status);
    }
    
    /**
     * AJAX monitoring alerts
     */
    public function ajax_monitoring_alerts() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $alerts = $this->get_monitoring_alerts();
        wp_send_json_success($alerts);
    }
    
    /**
     * Parse memory limit
     */
    private function parse_memory_limit($memory_limit) {
        $unit = strtolower(substr($memory_limit, -1));
        $value = (int) substr($memory_limit, 0, -1);
        
        switch ($unit) {
            case 'k':
                return $value * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'g':
                return $value * 1024 * 1024 * 1024;
            default:
                return $value;
        }
    }
}

// Initialize monitoring if debug system is available
if (class_exists('BlackCnoteDebugSystem')) {
    global $blackcnote_debug_system;
    if ($blackcnote_debug_system) {
        new BlackCnote_Monitoring_Alerts($blackcnote_debug_system);
    }
} 