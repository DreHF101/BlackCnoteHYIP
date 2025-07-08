<?php
/**
 * BlackCnote Comprehensive Server Test Suite
 * 
 * This script performs a complete analysis of server performance, functionality,
 * and identifies potential bottlenecks causing slow responses.
 * 
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

// Load WordPress
require_once dirname(__DIR__) . '/wp-load.php';

class BlackCnote_Server_Test_Suite {
    
    private $start_time;
    private $results = [];
    private $errors = [];
    private $performance_data = [];
    
    public function __construct() {
        $this->start_time = microtime(true);
        echo "=== BlackCnote Comprehensive Server Test Suite ===\n\n";
    }
    
    /**
     * Run all tests
     */
    public function run_all_tests(): void {
        $this->test_wordpress_core();
        $this->test_database_performance();
        $this->test_api_endpoints();
        $this->test_plugin_functionality();
        $this->test_theme_performance();
        $this->test_cors_functionality();
        $this->test_memory_usage();
        $this->test_file_permissions();
        $this->test_cache_performance();
        $this->test_security_headers();
        $this->test_error_logs();
        $this->test_demo_data_issues();
        
        $this->generate_report();
    }
    
    /**
     * Test WordPress core functionality
     */
    private function test_wordpress_core(): void {
        echo "🔍 Testing WordPress Core...\n";
        
        $tests = [
            'WordPress Version' => function() {
                return get_bloginfo('version');
            },
            'Site URL' => function() {
                return get_option('siteurl');
            },
            'Home URL' => function() {
                return get_option('home');
            },
            'Database Connection' => function() {
                global $wpdb;
                return $wpdb->check_connection() ? 'Connected' : 'Failed';
            },
            'Memory Limit' => function() {
                return WP_MEMORY_LIMIT;
            },
            'Max Memory Limit' => function() {
                return WP_MAX_MEMORY_LIMIT;
            },
            'Debug Mode' => function() {
                return WP_DEBUG ? 'Enabled' : 'Disabled';
            }
        ];
        
        foreach ($tests as $test_name => $test_func) {
            try {
                $start = microtime(true);
                $result = $test_func();
                $duration = (microtime(true) - $start) * 1000;
                
                $this->results['wordpress_core'][$test_name] = [
                    'status' => 'PASS',
                    'result' => $result,
                    'duration' => round($duration, 2)
                ];
                
                echo "  ✅ {$test_name}: {$result} ({$duration}ms)\n";
            } catch (Exception $e) {
                $this->results['wordpress_core'][$test_name] = [
                    'status' => 'FAIL',
                    'error' => $e->getMessage()
                ];
                $this->errors[] = "WordPress Core - {$test_name}: " . $e->getMessage();
                echo "  ❌ {$test_name}: FAILED - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Test database performance
     */
    private function test_database_performance(): void {
        echo "🔍 Testing Database Performance...\n";
        global $wpdb;
        
        $tests = [
            'Basic Query Performance' => function() use ($wpdb) {
                $start = microtime(true);
                $result = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
                $duration = (microtime(true) - $start) * 1000;
                return ['count' => $result, 'duration' => $duration];
            },
            'HYIPLab Tables Check' => function() use ($wpdb) {
                $tables = ['hyiplab_users', 'hyiplab_investments', 'hyiplab_transactions', 'hyiplab_plans'];
                $results = [];
                
                foreach ($tables as $table) {
                    $full_table = $wpdb->prefix . $table;
                    $start = microtime(true);
                    $exists = $wpdb->get_var("SHOW TABLES LIKE '{$full_table}'") === $full_table;
                    $duration = (microtime(true) - $start) * 1000;
                    
                    if ($exists) {
                        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$full_table}");
                        $results[$table] = ['exists' => true, 'count' => $count, 'duration' => $duration];
                    } else {
                        $results[$table] = ['exists' => false, 'duration' => $duration];
                    }
                }
                return $results;
            },
            'Slow Query Detection' => function() use ($wpdb) {
                $slow_queries = [];
                
                // Test potentially slow queries
                $queries = [
                    'Stats Query' => "SELECT COUNT(*) as users, SUM(amount) as invested FROM {$wpdb->prefix}hyiplab_users u LEFT JOIN {$wpdb->prefix}hyiplab_investments i ON u.id = i.user_id",
                    'Complex Join' => "SELECT * FROM {$wpdb->prefix}hyiplab_transactions t JOIN {$wpdb->prefix}hyiplab_users u ON t.user_id = u.id LIMIT 100"
                ];
                
                foreach ($queries as $name => $query) {
                    $start = microtime(true);
                    try {
                        $result = $wpdb->get_results($query);
                        $duration = (microtime(true) - $start) * 1000;
                        $slow_queries[$name] = ['duration' => $duration, 'rows' => count($result)];
                    } catch (Exception $e) {
                        $slow_queries[$name] = ['error' => $e->getMessage()];
                    }
                }
                return $slow_queries;
            }
        ];
        
        foreach ($tests as $test_name => $test_func) {
            try {
                $start = microtime(true);
                $result = $test_func();
                $total_duration = (microtime(true) - $start) * 1000;
                
                $this->results['database'][$test_name] = [
                    'status' => 'PASS',
                    'result' => $result,
                    'duration' => round($total_duration, 2)
                ];
                
                echo "  ✅ {$test_name}: {$total_duration}ms\n";
                
                // Check for performance issues
                if ($total_duration > 1000) {
                    $this->errors[] = "Database Performance - {$test_name}: Slow query detected ({$total_duration}ms)";
                    echo "  ⚠️  WARNING: Slow query detected!\n";
                }
            } catch (Exception $e) {
                $this->results['database'][$test_name] = [
                    'status' => 'FAIL',
                    'error' => $e->getMessage()
                ];
                $this->errors[] = "Database - {$test_name}: " . $e->getMessage();
                echo "  ❌ {$test_name}: FAILED - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Test API endpoints
     */
    private function test_api_endpoints(): void {
        echo "🔍 Testing API Endpoints...\n";
        
        $endpoints = [
            'homepage' => '/wp-json/blackcnote/v1/homepage',
            'plans' => '/wp-json/blackcnote/v1/plans',
            'settings' => '/wp-json/blackcnote/v1/settings',
            'stats' => '/wp-json/blackcnote/v1/stats',
            'health' => '/wp-json/blackcnote/v1/health'
        ];
        
        foreach ($endpoints as $name => $endpoint) {
            try {
                $start = microtime(true);
                $response = wp_remote_get(home_url($endpoint), [
                    'timeout' => 30,
                    'headers' => [
                        'Origin' => 'http://localhost:5174',
                        'X-Requested-With' => 'XMLHttpRequest'
                    ]
                ]);
                $duration = (microtime(true) - $start) * 1000;
                
                if (is_wp_error($response)) {
                    $this->results['api'][$name] = [
                        'status' => 'FAIL',
                        'error' => $response->get_error_message(),
                        'duration' => round($duration, 2)
                    ];
                    $this->errors[] = "API - {$name}: " . $response->get_error_message();
                    echo "  ❌ {$name}: FAILED - " . $response->get_error_message() . "\n";
                } else {
                    $status_code = wp_remote_retrieve_response_code($response);
                    $body = wp_remote_retrieve_body($response);
                    $headers = wp_remote_retrieve_headers($response);
                    
                    $this->results['api'][$name] = [
                        'status' => $status_code === 200 ? 'PASS' : 'FAIL',
                        'status_code' => $status_code,
                        'duration' => round($duration, 2),
                        'size' => strlen($body),
                        'cors_headers' => $headers->get('Access-Control-Allow-Origin')
                    ];
                    
                    if ($status_code === 200) {
                        echo "  ✅ {$name}: {$status_code} ({$duration}ms, " . strlen($body) . " bytes)\n";
                        
                        // Check for demo data
                        if (strpos($body, 'demo') !== false || strpos($body, 'test') !== false) {
                            echo "  ⚠️  WARNING: Demo/test data detected in response\n";
                        }
                        
                        // Check for performance issues
                        if ($duration > 2000) {
                            $this->errors[] = "API Performance - {$name}: Slow response ({$duration}ms)";
                            echo "  ⚠️  WARNING: Slow response detected!\n";
                        }
                    } else {
                        echo "  ❌ {$name}: {$status_code} ({$duration}ms)\n";
                    }
                }
            } catch (Exception $e) {
                $this->results['api'][$name] = [
                    'status' => 'FAIL',
                    'error' => $e->getMessage()
                ];
                $this->errors[] = "API - {$name}: " . $e->getMessage();
                echo "  ❌ {$name}: FAILED - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Test plugin functionality
     */
    private function test_plugin_functionality(): void {
        echo "🔍 Testing Plugin Functionality...\n";
        
        $plugins = [
            'blackcnote-cors' => 'BlackCnote CORS Handler',
            'hyiplab' => 'HYIPLab System',
            'full-content-checker' => 'Full Content Checker',
            'blackcnote-debug-system' => 'BlackCnote Debug System'
        ];
        
        foreach ($plugins as $slug => $name) {
            try {
                $start = microtime(true);
                $active = is_plugin_active($slug . '/' . $slug . '.php');
                $duration = (microtime(true) - $start) * 1000;
                
                $this->results['plugins'][$name] = [
                    'status' => $active ? 'ACTIVE' : 'INACTIVE',
                    'duration' => round($duration, 2)
                ];
                
                if ($active) {
                    echo "  ✅ {$name}: ACTIVE ({$duration}ms)\n";
                } else {
                    echo "  ⚠️  {$name}: INACTIVE ({$duration}ms)\n";
                }
            } catch (Exception $e) {
                $this->results['plugins'][$name] = [
                    'status' => 'ERROR',
                    'error' => $e->getMessage()
                ];
                $this->errors[] = "Plugin - {$name}: " . $e->getMessage();
                echo "  ❌ {$name}: ERROR - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Test theme performance
     */
    private function test_theme_performance(): void {
        echo "🔍 Testing Theme Performance...\n";
        
        $tests = [
            'Theme Loading' => function() {
                $start = microtime(true);
                get_template_directory();
                return (microtime(true) - $start) * 1000;
            },
            'Functions Loading' => function() {
                $start = microtime(true);
                get_template_part('functions');
                return (microtime(true) - $start) * 1000;
            },
            'Shortcode Processing' => function() {
                $start = microtime(true);
                do_shortcode('[blackcnote_plans limit="3"]');
                return (microtime(true) - $start) * 1000;
            }
        ];
        
        foreach ($tests as $test_name => $test_func) {
            try {
                $duration = $test_func();
                
                $this->results['theme'][$test_name] = [
                    'status' => 'PASS',
                    'duration' => round($duration, 2)
                ];
                
                echo "  ✅ {$test_name}: {$duration}ms\n";
                
                if ($duration > 500) {
                    $this->errors[] = "Theme Performance - {$test_name}: Slow operation ({$duration}ms)";
                    echo "  ⚠️  WARNING: Slow operation detected!\n";
                }
            } catch (Exception $e) {
                $this->results['theme'][$test_name] = [
                    'status' => 'FAIL',
                    'error' => $e->getMessage()
                ];
                $this->errors[] = "Theme - {$test_name}: " . $e->getMessage();
                echo "  ❌ {$test_name}: FAILED - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Test CORS functionality
     */
    private function test_cors_functionality(): void {
        echo "🔍 Testing CORS Functionality...\n";
        
        $test_urls = [
            'http://localhost:5174',
            'http://localhost:5173',
            'http://localhost:3000'
        ];
        
        foreach ($test_urls as $origin) {
            try {
                $start = microtime(true);
                $response = wp_remote_get(home_url('/wp-json/blackcnote/v1/health'), [
                    'timeout' => 10,
                    'headers' => [
                        'Origin' => $origin,
                        'X-Requested-With' => 'XMLHttpRequest'
                    ]
                ]);
                $duration = (microtime(true) - $start) * 1000;
                
                if (is_wp_error($response)) {
                    $this->results['cors'][$origin] = [
                        'status' => 'FAIL',
                        'error' => $response->get_error_message(),
                        'duration' => round($duration, 2)
                    ];
                    echo "  ❌ {$origin}: FAILED - " . $response->get_error_message() . "\n";
                } else {
                    $headers = wp_remote_retrieve_headers($response);
                    $cors_header = $headers->get('Access-Control-Allow-Origin');
                    
                    $this->results['cors'][$origin] = [
                        'status' => $cors_header ? 'PASS' : 'FAIL',
                        'cors_header' => $cors_header,
                        'duration' => round($duration, 2)
                    ];
                    
                    if ($cors_header) {
                        echo "  ✅ {$origin}: CORS OK ({$duration}ms)\n";
                    } else {
                        echo "  ❌ {$origin}: No CORS headers ({$duration}ms)\n";
                        $this->errors[] = "CORS - {$origin}: Missing CORS headers";
                    }
                }
            } catch (Exception $e) {
                $this->results['cors'][$origin] = [
                    'status' => 'FAIL',
                    'error' => $e->getMessage()
                ];
                $this->errors[] = "CORS - {$origin}: " . $e->getMessage();
                echo "  ❌ {$origin}: FAILED - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Test memory usage
     */
    private function test_memory_usage(): void {
        echo "🔍 Testing Memory Usage...\n";
        
        $memory_limit = ini_get('memory_limit');
        $memory_usage = memory_get_usage(true);
        $memory_peak = memory_get_peak_usage(true);
        
        $this->results['memory'] = [
            'limit' => $memory_limit,
            'current' => $this->format_bytes($memory_usage),
            'peak' => $this->format_bytes($memory_peak),
            'percentage' => round(($memory_usage / $this->parse_memory_limit($memory_limit)) * 100, 2)
        ];
        
        echo "  📊 Memory Limit: {$memory_limit}\n";
        echo "  📊 Current Usage: {$this->format_bytes($memory_usage)}\n";
        echo "  📊 Peak Usage: {$this->format_bytes($memory_peak)}\n";
        echo "  📊 Usage Percentage: {$this->results['memory']['percentage']}%\n";
        
        if ($this->results['memory']['percentage'] > 80) {
            $this->errors[] = "Memory Usage: High memory usage detected ({$this->results['memory']['percentage']}%)";
            echo "  ⚠️  WARNING: High memory usage!\n";
        }
        echo "\n";
    }
    
    /**
     * Test file permissions
     */
    private function test_file_permissions(): void {
        echo "🔍 Testing File Permissions...\n";
        
        $paths = [
            WP_CONTENT_DIR => 'Content Directory',
            WP_CONTENT_DIR . '/uploads' => 'Uploads Directory',
            WP_CONTENT_DIR . '/plugins' => 'Plugins Directory',
            WP_CONTENT_DIR . '/themes' => 'Themes Directory',
            WP_CONTENT_DIR . '/debug.log' => 'Debug Log'
        ];
        
        foreach ($paths as $path => $name) {
            try {
                $start = microtime(true);
                $readable = is_readable($path);
                $writable = is_writable($path);
                $duration = (microtime(true) - $start) * 1000;
                
                $this->results['permissions'][$name] = [
                    'readable' => $readable,
                    'writable' => $writable,
                    'duration' => round($duration, 2)
                ];
                
                if ($readable && $writable) {
                    echo "  ✅ {$name}: Read/Write OK ({$duration}ms)\n";
                } elseif ($readable) {
                    echo "  ⚠️  {$name}: Read-only ({$duration}ms)\n";
                } else {
                    echo "  ❌ {$name}: Access denied ({$duration}ms)\n";
                    $this->errors[] = "Permissions - {$name}: Access denied";
                }
            } catch (Exception $e) {
                $this->results['permissions'][$name] = [
                    'error' => $e->getMessage()
                ];
                $this->errors[] = "Permissions - {$name}: " . $e->getMessage();
                echo "  ❌ {$name}: ERROR - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Test cache performance
     */
    private function test_cache_performance(): void {
        echo "🔍 Testing Cache Performance...\n";
        
        $tests = [
            'Object Cache' => function() {
                return function_exists('wp_cache_get') && wp_cache_get('test_key') !== false;
            },
            'Transients' => function() {
                set_transient('test_transient', 'test_value', 60);
                return get_transient('test_transient') === 'test_value';
            },
            'Options Cache' => function() {
                $start = microtime(true);
                get_option('home');
                return (microtime(true) - $start) * 1000;
            }
        ];
        
        foreach ($tests as $test_name => $test_func) {
            try {
                $start = microtime(true);
                $result = $test_func();
                $duration = (microtime(true) - $start) * 1000;
                
                $this->results['cache'][$test_name] = [
                    'status' => $result ? 'PASS' : 'FAIL',
                    'duration' => round($duration, 2)
                ];
                
                if ($result) {
                    echo "  ✅ {$test_name}: OK ({$duration}ms)\n";
                } else {
                    echo "  ❌ {$test_name}: FAILED ({$duration}ms)\n";
                    $this->errors[] = "Cache - {$test_name}: Failed";
                }
            } catch (Exception $e) {
                $this->results['cache'][$test_name] = [
                    'status' => 'FAIL',
                    'error' => $e->getMessage()
                ];
                $this->errors[] = "Cache - {$test_name}: " . $e->getMessage();
                echo "  ❌ {$test_name}: ERROR - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Test security headers
     */
    private function test_security_headers(): void {
        echo "🔍 Testing Security Headers...\n";
        
        $response = wp_remote_get(home_url('/'));
        
        if (!is_wp_error($response)) {
            $headers = wp_remote_retrieve_headers($response);
            
            $security_headers = [
                'X-Content-Type-Options',
                'X-Frame-Options',
                'X-XSS-Protection',
                'Content-Security-Policy'
            ];
            
            foreach ($security_headers as $header) {
                $value = $headers->get($header);
                $this->results['security'][$header] = $value ?: 'Not Set';
                
                if ($value) {
                    echo "  ✅ {$header}: {$value}\n";
                } else {
                    echo "  ⚠️  {$header}: Not Set\n";
                }
            }
        }
        echo "\n";
    }
    
    /**
     * Test error logs
     */
    private function test_error_logs(): void {
        echo "🔍 Testing Error Logs...\n";
        
        $log_file = WP_CONTENT_DIR . '/debug.log';
        
        if (file_exists($log_file)) {
            $log_size = filesize($log_file);
            $log_lines = count(file($log_file));
            
            $this->results['logs'] = [
                'size' => $this->format_bytes($log_size),
                'lines' => $log_lines,
                'last_modified' => date('Y-m-d H:i:s', filemtime($log_file))
            ];
            
            echo "  📊 Log Size: {$this->format_bytes($log_size)}\n";
            echo "  📊 Log Lines: {$log_lines}\n";
            echo "  📊 Last Modified: " . date('Y-m-d H:i:s', filemtime($log_file)) . "\n";
            
            if ($log_size > 10 * 1024 * 1024) { // 10MB
                $this->errors[] = "Error Logs: Large log file detected ({$this->format_bytes($log_size)})";
                echo "  ⚠️  WARNING: Large log file detected!\n";
            }
        } else {
            echo "  ℹ️  No debug log file found\n";
        }
        echo "\n";
    }
    
    /**
     * Test demo data issues
     */
    private function test_demo_data_issues(): void {
        echo "🔍 Testing for Demo Data Issues...\n";
        
        // Check if HYIPLab tables exist and have real data
        global $wpdb;
        
        $tables = ['hyiplab_users', 'hyiplab_investments', 'hyiplab_transactions'];
        $demo_indicators = [];
        
        foreach ($tables as $table) {
            $full_table = $wpdb->prefix . $table;
            
            if ($wpdb->get_var("SHOW TABLES LIKE '{$full_table}'") === $full_table) {
                $count = $wpdb->get_var("SELECT COUNT(*) FROM {$full_table}");
                
                if ($count == 0) {
                    $demo_indicators[] = "Table {$table} is empty";
                    echo "  ⚠️  {$table}: Empty table (demo data indicator)\n";
                } else {
                    echo "  ✅ {$table}: {$count} records\n";
                }
            } else {
                $demo_indicators[] = "Table {$table} does not exist";
                echo "  ⚠️  {$table}: Table does not exist\n";
            }
        }
        
        // Check for demo data in API responses
        $response = wp_remote_get(home_url('/wp-json/blackcnote/v1/stats'));
        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if ($data && isset($data['totalUsers']) && $data['totalUsers'] > 10000) {
                $demo_indicators[] = "High user count in stats API (likely demo data)";
                echo "  ⚠️  Stats API: High user count detected ({$data['totalUsers']})\n";
            }
        }
        
        $this->results['demo_data'] = [
            'indicators' => $demo_indicators,
            'has_demo_data' => !empty($demo_indicators)
        ];
        
        if (!empty($demo_indicators)) {
            $this->errors[] = "Demo Data: Multiple indicators of demo/test data detected";
            echo "  ⚠️  WARNING: Demo data indicators detected!\n";
        }
        echo "\n";
    }
    
    /**
     * Generate comprehensive report
     */
    private function generate_report(): void {
        $total_duration = (microtime(true) - $this->start_time) * 1000;
        
        echo "=== COMPREHENSIVE TEST REPORT ===\n\n";
        
        // Summary
        $total_tests = 0;
        $passed_tests = 0;
        $failed_tests = 0;
        
        foreach ($this->results as $category => $tests) {
            foreach ($tests as $test_name => $test_data) {
                $total_tests++;
                if (isset($test_data['status'])) {
                    if ($test_data['status'] === 'PASS' || $test_data['status'] === 'ACTIVE') {
                        $passed_tests++;
                    } else {
                        $failed_tests++;
                    }
                }
            }
        }
        
        echo "📊 TEST SUMMARY:\n";
        echo "  Total Tests: {$total_tests}\n";
        echo "  Passed: {$passed_tests}\n";
        echo "  Failed: {$failed_tests}\n";
        echo "  Success Rate: " . round(($passed_tests / $total_tests) * 100, 2) . "%\n";
        echo "  Total Duration: " . round($total_duration, 2) . "ms\n\n";
        
        // Performance Analysis
        echo "🚀 PERFORMANCE ANALYSIS:\n";
        $slow_operations = [];
        
        foreach ($this->results as $category => $tests) {
            foreach ($tests as $test_name => $test_data) {
                if (isset($test_data['duration']) && $test_data['duration'] > 1000) {
                    $slow_operations[] = "{$category} - {$test_name}: {$test_data['duration']}ms";
                }
            }
        }
        
        if (!empty($slow_operations)) {
            echo "  ⚠️  SLOW OPERATIONS DETECTED:\n";
            foreach ($slow_operations as $operation) {
                echo "    - {$operation}\n";
            }
        } else {
            echo "  ✅ No slow operations detected\n";
        }
        echo "\n";
        
        // Error Summary
        if (!empty($this->errors)) {
            echo "❌ ERRORS DETECTED:\n";
            foreach ($this->errors as $error) {
                echo "  - {$error}\n";
            }
            echo "\n";
        } else {
            echo "✅ No errors detected\n\n";
        }
        
        // Recommendations
        echo "💡 RECOMMENDATIONS:\n";
        
        if (!empty($this->errors)) {
            echo "  1. Address the errors listed above\n";
        }
        
        if (!empty($slow_operations)) {
            echo "  2. Optimize slow operations for better performance\n";
        }
        
        if (isset($this->results['memory']['percentage']) && $this->results['memory']['percentage'] > 80) {
            echo "  3. Consider increasing memory limit or optimizing memory usage\n";
        }
        
        if (isset($this->results['demo_data']['has_demo_data']) && $this->results['demo_data']['has_demo_data']) {
            echo "  4. Replace demo data with real data or implement proper data handling\n";
        }
        
        echo "  5. Monitor server performance regularly\n";
        echo "  6. Implement caching strategies for better performance\n";
        echo "  7. Consider using a CDN for static assets\n";
        
        echo "\n=== TEST COMPLETE ===\n";
        
        // Save detailed results to file
        $report_file = WP_CONTENT_DIR . '/server-test-report-' . date('Y-m-d-H-i-s') . '.json';
        file_put_contents($report_file, json_encode($this->results, JSON_PRETTY_PRINT));
        echo "📄 Detailed report saved to: {$report_file}\n";
    }
    
    /**
     * Helper function to format bytes
     */
    private function format_bytes($bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Helper function to parse memory limit
     */
    private function parse_memory_limit($limit): int {
        $unit = strtolower(substr($limit, -1));
        $value = (int) substr($limit, 0, -1);
        
        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return $value;
        }
    }
}

// Run the test suite
$test_suite = new BlackCnote_Server_Test_Suite();
$test_suite->run_all_tests(); 