<?php
declare(strict_types=1);

class BlackCnote_Debug_REST {
    public static function register_routes() {
        // Health check endpoint
        register_rest_route('blackcnote/v1', '/health', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_health'],
            'permission_callback' => '__return_true',
        ]);
        
        // Enhanced health check with detailed metrics
        register_rest_route('blackcnote/v1', '/health/detailed', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_detailed_health'],
            'permission_callback' => '__return_true',
        ]);
        
        // Service status endpoint
        register_rest_route('blackcnote/v1', '/services', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_services_status'],
            'permission_callback' => '__return_true',
        ]);
        
        // Performance metrics endpoint
        register_rest_route('blackcnote/v1', '/performance', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_performance_metrics'],
            'permission_callback' => '__return_true',
        ]);
        
        // Canonical paths verification endpoint
        register_rest_route('blackcnote/v1', '/canonical-paths', [
            'methods' => 'GET',
            'callback' => [self::class, 'verify_canonical_paths'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function get_health() {
        require_once __DIR__ . '/class-blackcnote-debug-health.php';
        return BlackCnote_Debug_Health::check_services();
    }
    
    public static function get_detailed_health() {
        require_once __DIR__ . '/class-blackcnote-debug-health.php';
        
        $basic_health = BlackCnote_Debug_Health::check_services();
        $performance = self::get_performance_metrics();
        $canonical_paths = self::verify_canonical_paths();
        
        return [
            'status' => 'healthy',
            'timestamp' => current_time('mysql'),
            'services' => $basic_health,
            'performance' => $performance,
            'canonical_paths' => $canonical_paths,
            'wordpress' => [
                'version' => get_bloginfo('version'),
                'memory_limit' => WP_MEMORY_LIMIT,
                'debug_mode' => WP_DEBUG,
                'multisite' => is_multisite(),
                'active_plugins' => count(get_option('active_plugins', [])),
            ]
        ];
    }
    
    public static function get_services_status() {
        $services = [
            'wordpress' => [
                'url' => 'http://localhost:8888',
                'status' => 'up',
                'response_time' => self::measure_response_time('http://localhost:8888')
            ],
            'react' => [
                'url' => 'http://localhost:5174',
                'status' => self::check_service_status('http://localhost:5174'),
                'response_time' => self::measure_response_time('http://localhost:5174')
            ],
            'phpmyadmin' => [
                'url' => 'http://localhost:8080',
                'status' => self::check_service_status('http://localhost:8080'),
                'response_time' => self::measure_response_time('http://localhost:8080')
            ],
            'redis_commander' => [
                'url' => 'http://localhost:8081',
                'status' => self::check_service_status('http://localhost:8081'),
                'response_time' => self::measure_response_time('http://localhost:8081')
            ],
            'mailhog' => [
                'url' => 'http://localhost:8025',
                'status' => self::check_service_status('http://localhost:8025'),
                'response_time' => self::measure_response_time('http://localhost:8025')
            ],
            'browsersync' => [
                'url' => 'http://localhost:3000',
                'status' => self::check_service_status('http://localhost:3000'),
                'response_time' => self::measure_response_time('http://localhost:3000')
            ]
        ];
        
        return $services;
    }
    
    public static function get_performance_metrics() {
        global $wpdb;
        
        return [
            'memory' => [
                'usage' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit')
            ],
            'database' => [
                'queries' => get_num_queries(),
                'last_error' => $wpdb->last_error,
                'last_query' => $wpdb->last_query
            ],
            'wordpress' => [
                'load_time' => timer_stop(),
                'active_plugins' => count(get_option('active_plugins', [])),
                'total_plugins' => count(get_plugins())
            ],
            'system' => [
                'disk_free' => disk_free_space(ABSPATH),
                'disk_total' => disk_total_space(ABSPATH),
                'load_average' => sys_getloadavg()
            ]
        ];
    }
    
    public static function verify_canonical_paths() {
        $canonical_paths = [
            'project_root' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote',
            'wordpress' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote',
            'wp_content' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content',
            'theme' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content/themes/blackcnote',
            'react_app' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/react-app'
        ];
        
        $verification = [];
        foreach ($canonical_paths as $name => $path) {
            $verification[$name] = [
                'path' => $path,
                'exists' => file_exists($path),
                'readable' => is_readable($path),
                'writable' => is_writable($path)
            ];
        }
        
        return $verification;
    }
    
    private static function check_service_status($url) {
        $response = wp_remote_get($url, ['timeout' => 3]);
        if (is_wp_error($response)) {
            return 'down';
        }
        $code = wp_remote_retrieve_response_code($response);
        return ($code >= 200 && $code < 400) ? 'up' : 'down';
    }
    
    private static function measure_response_time($url) {
        $start = microtime(true);
        $response = wp_remote_get($url, ['timeout' => 5]);
        $end = microtime(true);
        
        if (is_wp_error($response)) {
            return -1; // Error
        }
        
        return round(($end - $start) * 1000, 2); // Response time in milliseconds
    }
} 