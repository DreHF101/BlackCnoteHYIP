<?php
/**
 * Comprehensive BlackCnote Automated Testing Script
 * 
 * This script performs comprehensive testing of all BlackCnote functionality
 * including canonical paths, service connectivity, theme functionality,
 * performance metrics, and integration with the existing debug system.
 * 
 * @package BlackCnoteTesting
 * @version 1.0.0
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH') && !defined('BLACKCNOTE_TESTING_MODE')) {
    define('BLACKCNOTE_TESTING_MODE', true);
}

/**
 * BlackCnote Comprehensive Test Suite
 */
class BlackCnoteComprehensiveTest {
    
    private $test_results = [];
    private $start_time;
    private $canonical_paths;
    private $service_urls;
    private $debug_system_available = false;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->start_time = microtime(true);
        $this->initialize_canonical_paths();
        $this->initialize_service_urls();
        $this->check_debug_system();
    }
    
    /**
     * Initialize canonical paths
     */
    private function initialize_canonical_paths() {
        $this->canonical_paths = [
            'project_root' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote',
            'wordpress' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote',
            'wp_content' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content',
            'theme' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content/themes/blackcnote',
            'react_app' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/react-app',
            'plugins' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content/plugins',
            'uploads' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/blackcnote/wp-content/uploads',
            'logs' => 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote/logs'
        ];
    }
    
    /**
     * Initialize service URLs
     */
    private function initialize_service_urls() {
        $this->service_urls = [
            'wordpress' => 'http://localhost:8888',
            'wordpress_admin' => 'http://localhost:8888/wp-admin/',
            'react' => 'http://localhost:5174',
            'phpmyadmin' => 'http://localhost:8080',
            'redis_commander' => 'http://localhost:8081',
            'mailhog' => 'http://localhost:8025',
            'browsersync' => 'http://localhost:3000',
            'dev_tools' => 'http://localhost:9229'
        ];
    }
    
    /**
     * Check if debug system is available
     */
    private function check_debug_system() {
        if (class_exists('BlackCnoteDebugSystem')) {
            $this->debug_system_available = true;
        }
    }
    
    /**
     * Run all tests
     */
    public function run_all_tests() {
        echo "🚀 Starting Comprehensive BlackCnote Test Suite...\n";
        echo "⏰ Test started at: " . date('Y-m-d H:i:s') . "\n\n";
        
        // Test 1: Canonical Paths Verification
        $this->test_canonical_paths();
        
        // Test 2: Service Connectivity
        $this->test_service_connectivity();
        
        // Test 3: WordPress Integration
        $this->test_wordpress_integration();
        
        // Test 4: Theme Functionality
        $this->test_theme_functionality();
        
        // Test 5: Plugin Integration
        $this->test_plugin_integration();
        
        // Test 6: Performance Metrics
        $this->test_performance_metrics();
        
        // Test 7: Debug System Integration
        $this->test_debug_system_integration();
        
        // Test 8: React App Integration
        $this->test_react_integration();
        
        // Test 9: Database Connectivity
        $this->test_database_connectivity();
        
        // Test 10: Security Checks
        $this->test_security_checks();
        
        // Generate comprehensive report
        $this->generate_comprehensive_report();
        
        return $this->test_results;
    }
    
    /**
     * Test canonical paths
     */
    private function test_canonical_paths() {
        echo "📁 Testing Canonical Paths...\n";
        
        $results = [];
        foreach ($this->canonical_paths as $name => $path) {
            $exists = file_exists($path);
            $readable = is_readable($path);
            $writable = is_writable($path);
            
            $results[$name] = [
                'path' => $path,
                'exists' => $exists,
                'readable' => $readable,
                'writable' => $writable,
                'status' => $exists ? '✅' : '❌'
            ];
            
            echo "  {$results[$name]['status']} {$name}: {$path}\n";
        }
        
        $this->test_results['canonical_paths'] = $results;
        echo "\n";
    }
    
    /**
     * Test service connectivity
     */
    private function test_service_connectivity() {
        echo "🌐 Testing Service Connectivity...\n";
        
        $results = [];
        foreach ($this->service_urls as $name => $url) {
            $start_time = microtime(true);
            $response = $this->make_http_request($url);
            $end_time = microtime(true);
            
            $response_time = round(($end_time - $start_time) * 1000, 2);
            $status = $response['success'] ? '✅' : '❌';
            
            $results[$name] = [
                'url' => $url,
                'status' => $response['success'] ? 'up' : 'down',
                'response_code' => $response['code'],
                'response_time_ms' => $response_time,
                'error' => $response['error'] ?? null
            ];
            
            echo "  {$status} {$name}: {$url} ({$response_time}ms)\n";
        }
        
        $this->test_results['service_connectivity'] = $results;
        echo "\n";
    }
    
    /**
     * Test WordPress integration
     */
    private function test_wordpress_integration() {
        echo "🔧 Testing WordPress Integration...\n";
        
        $results = [];
        
        // Check if we're in WordPress context
        $in_wordpress = function_exists('get_bloginfo');
        
        if ($in_wordpress) {
            // Test WordPress core functions
            $results['wp_functions'] = [
                'get_bloginfo' => function_exists('get_bloginfo'),
                'wp_get_theme' => function_exists('wp_get_theme'),
                'get_template_directory' => function_exists('get_template_directory'),
                'wp_enqueue_script' => function_exists('wp_enqueue_script')
            ];
            
            // Test WordPress configuration
            $results['wp_config'] = [
                'wp_debug' => defined('WP_DEBUG') ? WP_DEBUG : false,
                'wp_memory_limit' => defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : 'Not set',
                'wp_max_memory_limit' => defined('WP_MAX_MEMORY_LIMIT') ? WP_MAX_MEMORY_LIMIT : 'Not set',
                'wp_cache' => defined('WP_CACHE') ? WP_CACHE : false
            ];
            
            // Test WordPress database
            global $wpdb;
            $results['database'] = [
                'connected' => !empty($wpdb->dbh),
                'prefix' => $wpdb->prefix,
                'last_error' => $wpdb->last_error ?: 'None'
            ];
        } else {
            // Standalone mode - test file existence
            $results['wp_files'] = [
                'wp-config.php' => file_exists($this->canonical_paths['wordpress'] . '/wp-config.php'),
                'wp-content' => file_exists($this->canonical_paths['wp_content']),
                'wp-admin' => file_exists($this->canonical_paths['wordpress'] . '/wp-admin'),
                'wp-includes' => file_exists($this->canonical_paths['wordpress'] . '/wp-includes')
            ];
            
            $results['wp_context'] = [
                'in_wordpress' => false,
                'message' => 'Running in standalone mode'
            ];
        }
        
        foreach ($results as $category => $tests) {
            if (is_array($tests)) {
                foreach ($tests as $test => $result) {
                    if (is_bool($result)) {
                        $status = $result ? '✅' : '❌';
                        echo "  {$status} {$category} - {$test}\n";
                    } else {
                        echo "  ℹ️ {$category} - {$test}: {$result}\n";
                    }
                }
            }
        }
        
        $this->test_results['wordpress_integration'] = $results;
        echo "\n";
    }
    
    /**
     * Test theme functionality
     */
    private function test_theme_functionality() {
        echo "🎨 Testing Theme Functionality...\n";
        
        $results = [];
        
        // Check if we're in WordPress context
        $in_wordpress = function_exists('get_template_directory');
        
        if ($in_wordpress) {
            // Test theme files
            $theme_files = [
                'style.css' => get_template_directory() . '/style.css',
                'functions.php' => get_template_directory() . '/functions.php',
                'index.php' => get_template_directory() . '/index.php',
                'header.php' => get_template_directory() . '/header.php',
                'footer.php' => get_template_directory() . '/footer.php'
            ];
            
            foreach ($theme_files as $file => $path) {
                $exists = file_exists($path);
                $results['theme_files'][$file] = [
                    'exists' => $exists,
                    'path' => $path,
                    'status' => $exists ? '✅' : '❌'
                ];
                echo "  {$results['theme_files'][$file]['status']} {$file}\n";
            }
            
            // Test theme functions
            $theme_functions = [
                'blackcnote_theme_setup',
                'blackcnote_enqueue_scripts',
                'blackcnote_load_includes'
            ];
            
            foreach ($theme_functions as $function) {
                $exists = function_exists($function);
                $results['theme_functions'][$function] = [
                    'exists' => $exists,
                    'status' => $exists ? '✅' : '❌'
                ];
                echo "  {$results['theme_functions'][$function]['status']} {$function}\n";
            }
            
            // Test theme constants
            $theme_constants = [
                'BLACKCNOTE_THEME_VERSION',
                'BLACKCNOTE_THEME_DIR',
                'BLACKCNOTE_THEME_URI',
                'BLACKCNOTE_CANONICAL_ROOT'
            ];
            
            foreach ($theme_constants as $constant) {
                $defined = defined($constant);
                $results['theme_constants'][$constant] = [
                    'defined' => $defined,
                    'value' => $defined ? constant($constant) : 'Not defined',
                    'status' => $defined ? '✅' : '❌'
                ];
                echo "  {$results['theme_constants'][$constant]['status']} {$constant}\n";
            }
        } else {
            // Standalone mode - test theme files directly
            $theme_files = [
                'style.css' => $this->canonical_paths['theme'] . '/style.css',
                'functions.php' => $this->canonical_paths['theme'] . '/functions.php',
                'index.php' => $this->canonical_paths['theme'] . '/index.php',
                'header.php' => $this->canonical_paths['theme'] . '/header.php',
                'footer.php' => $this->canonical_paths['theme'] . '/footer.php'
            ];
            
            foreach ($theme_files as $file => $path) {
                $exists = file_exists($path);
                $results['theme_files'][$file] = [
                    'exists' => $exists,
                    'path' => $path,
                    'status' => $exists ? '✅' : '❌'
                ];
                echo "  {$results['theme_files'][$file]['status']} {$file}\n";
            }
            
            // Test theme constants by checking if they're defined in functions.php
            $functions_content = file_get_contents($this->canonical_paths['theme'] . '/functions.php');
            $theme_constants = [
                'BLACKCNOTE_THEME_VERSION',
                'BLACKCNOTE_THEME_DIR',
                'BLACKCNOTE_THEME_URI',
                'BLACKCNOTE_CANONICAL_ROOT'
            ];
            
            foreach ($theme_constants as $constant) {
                $defined = strpos($functions_content, "define('{$constant}'") !== false;
                $results['theme_constants'][$constant] = [
                    'defined' => $defined,
                    'value' => $defined ? 'Found in functions.php' : 'Not found',
                    'status' => $defined ? '✅' : '❌'
                ];
                echo "  {$results['theme_constants'][$constant]['status']} {$constant}\n";
            }
        }
        
        $this->test_results['theme_functionality'] = $results;
        echo "\n";
    }
    
    /**
     * Test plugin integration
     */
    private function test_plugin_integration() {
        echo "🔌 Testing Plugin Integration...\n";
        
        $results = [];
        
        // Test required plugins
        $required_plugins = [
            'blackcnote-debug-system' => 'BlackCnote Debug System',
            'blackcnote-cors' => 'BlackCnote CORS Handler',
            'blackcnote-hyiplab-api' => 'BlackCnote HYIPLab API',
            'hyiplab' => 'HYIPLab Investment Platform'
        ];
        
        foreach ($required_plugins as $slug => $name) {
            $active = function_exists('is_plugin_active') ? is_plugin_active($slug . '/' . $slug . '.php') : false;
            $installed = defined('WP_PLUGIN_DIR') ? file_exists(WP_PLUGIN_DIR . '/' . $slug) : false;
            
            $results['required_plugins'][$slug] = [
                'name' => $name,
                'installed' => $installed,
                'active' => $active,
                'status' => $active ? '✅' : ($installed ? '⚠️' : '❌')
            ];
            
            echo "  {$results['required_plugins'][$slug]['status']} {$name}\n";
        }
        
        // Test plugin classes
        $plugin_classes = [
            'BlackCnoteDebugSystem',
            'BlackCnoteCorsHandler',
            'BlackCnoteHyipLabApi'
        ];
        
        foreach ($plugin_classes as $class) {
            $exists = class_exists($class);
            $results['plugin_classes'][$class] = [
                'exists' => $exists,
                'status' => $exists ? '✅' : '❌'
            ];
            echo "  {$results['plugin_classes'][$class]['status']} {$class}\n";
        }
        
        $this->test_results['plugin_integration'] = $results;
        echo "\n";
    }
    
    /**
     * Test performance metrics
     */
    private function test_performance_metrics() {
        echo "⚡ Testing Performance Metrics...\n";
        
        $results = [];
        
        // Memory usage
        $results['memory'] = [
            'usage' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit')
        ];
        
        // Check if we're in WordPress context
        if (function_exists('get_num_queries')) {
            // Database performance
            global $wpdb;
            $results['database'] = [
                'queries' => get_num_queries(),
                'last_error' => $wpdb->last_error ?: 'None',
                'last_query_time' => $wpdb->last_query ? 'Available' : 'None'
            ];
            
            // Load time
            $results['load_time'] = [
                'current' => timer_stop(),
                'target' => 2.0 // Target load time in seconds
            ];
            
            // System resources
            $results['system'] = [
                'disk_free' => disk_free_space(ABSPATH),
                'disk_total' => disk_total_space(ABSPATH),
                'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : 'Not available'
            ];
        } else {
            // Standalone mode
            $results['database'] = [
                'queries' => 'Not available in standalone mode',
                'last_error' => 'Not available in standalone mode',
                'last_query_time' => 'Not available in standalone mode'
            ];
            
            $results['load_time'] = [
                'current' => 'Not available in standalone mode',
                'target' => 2.0
            ];
            
            $results['system'] = [
                'disk_free' => disk_free_space($this->canonical_paths['project_root']),
                'disk_total' => disk_total_space($this->canonical_paths['project_root']),
                'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : 'Not available'
            ];
        }
        
        echo "  📊 Memory Usage: " . round($results['memory']['usage'] / 1024 / 1024, 2) . "MB\n";
        echo "  📊 Peak Memory: " . round($results['memory']['peak'] / 1024 / 1024, 2) . "MB\n";
        if (is_numeric($results['database']['queries'])) {
            echo "  📊 Database Queries: {$results['database']['queries']}\n";
        }
        if (is_numeric($results['load_time']['current'])) {
            echo "  📊 Load Time: {$results['load_time']['current']}s\n";
        }
        
        $this->test_results['performance_metrics'] = $results;
        echo "\n";
    }
    
    /**
     * Test debug system integration
     */
    private function test_debug_system_integration() {
        echo "🐛 Testing Debug System Integration...\n";
        
        $results = [];
        
        if ($this->debug_system_available) {
            // Test debug system functionality
            $results['debug_system'] = [
                'available' => true,
                'version' => defined('BLACKCNOTE_DEBUG_VERSION') ? BLACKCNOTE_DEBUG_VERSION : 'Unknown',
                'log_file' => WP_CONTENT_DIR . '/logs/blackcnote-debug.log',
                'log_writable' => is_writable(WP_CONTENT_DIR . '/logs/')
            ];
            
            // Test debug endpoints
            $debug_endpoints = [
                '/wp-json/blackcnote/v1/health',
                '/wp-json/blackcnote/v1/health/detailed',
                '/wp-json/blackcnote/v1/services',
                '/wp-json/blackcnote/v1/performance'
            ];
            
            foreach ($debug_endpoints as $endpoint) {
                $url = $this->service_urls['wordpress'] . $endpoint;
                $response = $this->make_http_request($url);
                
                $results['debug_endpoints'][$endpoint] = [
                    'url' => $url,
                    'accessible' => $response['success'],
                    'response_code' => $response['code']
                ];
                
                $status = $response['success'] ? '✅' : '❌';
                echo "  {$status} {$endpoint}\n";
            }
        } else {
            $results['debug_system'] = [
                'available' => false,
                'message' => 'Debug system plugin not active'
            ];
            echo "  ❌ Debug system not available\n";
        }
        
        $this->test_results['debug_system_integration'] = $results;
        echo "\n";
    }
    
    /**
     * Test React integration
     */
    private function test_react_integration() {
        echo "⚛️ Testing React Integration...\n";
        
        $results = [];
        
        // Test React app files
        $react_files = [
            'package.json' => $this->canonical_paths['react_app'] . '/package.json',
            'vite.config.ts' => $this->canonical_paths['react_app'] . '/vite.config.ts',
            'src/App.tsx' => $this->canonical_paths['react_app'] . '/src/App.tsx',
            'src/main.tsx' => $this->canonical_paths['react_app'] . '/src/main.tsx'
        ];
        
        foreach ($react_files as $file => $path) {
            $exists = file_exists($path);
            $results['react_files'][$file] = [
                'exists' => $exists,
                'path' => $path,
                'status' => $exists ? '✅' : '❌'
            ];
            echo "  {$results['react_files'][$file]['status']} {$file}\n";
        }
        
        // Test React dev server
        $react_response = $this->make_http_request($this->service_urls['react']);
        $results['react_server'] = [
            'url' => $this->service_urls['react'],
            'accessible' => $react_response['success'],
            'response_code' => $react_response['code']
        ];
        
        $status = $react_response['success'] ? '✅' : '❌';
        echo "  {$status} React Development Server\n";
        
        $this->test_results['react_integration'] = $results;
        echo "\n";
    }
    
    /**
     * Test database connectivity
     */
    private function test_database_connectivity() {
        echo "🗄️ Testing Database Connectivity...\n";
        
        $results = [];
        global $wpdb;
        
        // Test basic connectivity
        $results['connection'] = [
            'connected' => !empty($wpdb->dbh),
            'last_error' => $wpdb->last_error ?: 'None'
        ];
        
        // Test basic queries
        $test_queries = [
            'SELECT 1' => 'Basic select',
            'SHOW TABLES' => 'Show tables',
            'SELECT COUNT(*) FROM wp_posts' => 'Count posts'
        ];
        
        foreach ($test_queries as $query => $description) {
            $result = $wpdb->query($query);
            $success = $result !== false;
            
            $results['test_queries'][$description] = [
                'query' => $query,
                'success' => $success,
                'result' => $result
            ];
            
            $status = $success ? '✅' : '❌';
            echo "  {$status} {$description}\n";
        }
        
        // Test phpMyAdmin
        $phpmyadmin_response = $this->make_http_request($this->service_urls['phpmyadmin']);
        $results['phpmyadmin'] = [
            'url' => $this->service_urls['phpmyadmin'],
            'accessible' => $phpmyadmin_response['success'],
            'response_code' => $phpmyadmin_response['code']
        ];
        
        $status = $phpmyadmin_response['success'] ? '✅' : '❌';
        echo "  {$status} phpMyAdmin\n";
        
        $this->test_results['database_connectivity'] = $results;
        echo "\n";
    }
    
    /**
     * Test security checks
     */
    private function test_security_checks() {
        echo "🔒 Testing Security Checks...\n";
        
        $results = [];
        
        // Test file permissions
        $security_paths = [
            'wp-config.php' => ABSPATH . 'wp-config.php',
            'wp-content' => WP_CONTENT_DIR,
            'uploads' => WP_CONTENT_DIR . '/uploads',
            'plugins' => WP_PLUGIN_DIR
        ];
        
        foreach ($security_paths as $name => $path) {
            $readable = is_readable($path);
            $writable = is_writable($path);
            
            $results['file_permissions'][$name] = [
                'path' => $path,
                'readable' => $readable,
                'writable' => $writable,
                'secure' => $readable && !$writable // Should be readable but not writable
            ];
            
            $status = $results['file_permissions'][$name]['secure'] ? '✅' : '⚠️';
            echo "  {$status} {$name} permissions\n";
        }
        
        // Test WordPress security constants
        $security_constants = [
            'WP_DEBUG' => defined('WP_DEBUG') ? WP_DEBUG : false,
            'WP_DEBUG_DISPLAY' => defined('WP_DEBUG_DISPLAY') ? WP_DEBUG_DISPLAY : false,
            'FORCE_SSL_ADMIN' => defined('FORCE_SSL_ADMIN') ? FORCE_SSL_ADMIN : false
        ];
        
        foreach ($security_constants as $constant => $value) {
            $results['security_constants'][$constant] = [
                'defined' => defined($constant),
                'value' => $value
            ];
            
            $status = defined($constant) ? '✅' : '❌';
            echo "  {$status} {$constant}\n";
        }
        
        $this->test_results['security_checks'] = $results;
        echo "\n";
    }
    
    /**
     * Make HTTP request
     */
    private function make_http_request($url) {
        // Check if WordPress functions are available
        if (function_exists('wp_remote_get')) {
            $args = [
                'timeout' => 5,
                'user-agent' => 'BlackCnote-Test-Suite/1.0'
            ];
            
            $response = wp_remote_get($url, $args);
            
            if (is_wp_error($response)) {
                return [
                    'success' => false,
                    'code' => 0,
                    'error' => $response->get_error_message()
                ];
            }
            
            return [
                'success' => true,
                'code' => wp_remote_retrieve_response_code($response),
                'body' => wp_remote_retrieve_body($response)
            ];
        } else {
            // Fallback for standalone execution
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'BlackCnote-Test-Suite/1.0'
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                return [
                    'success' => false,
                    'code' => 0,
                    'error' => 'Failed to connect to ' . $url
                ];
            }
            
            return [
                'success' => true,
                'code' => 200, // Assume success if we got content
                'body' => $response
            ];
        }
    }
    
    /**
     * Generate comprehensive report
     */
    private function generate_comprehensive_report() {
        $end_time = microtime(true);
        $total_time = round($end_time - $this->start_time, 2);
        
        echo "📋 Generating Comprehensive Test Report...\n";
        echo "⏱️ Total test time: {$total_time}s\n\n";
        
        // Calculate overall status
        $total_tests = 0;
        $passed_tests = 0;
        $failed_tests = 0;
        
        foreach ($this->test_results as $category => $tests) {
            if (is_array($tests)) {
                foreach ($tests as $test_name => $test_data) {
                    if (is_array($test_data)) {
                        foreach ($test_data as $sub_test => $sub_data) {
                            $total_tests++;
                            if (isset($sub_data['status'])) {
                                if (strpos($sub_data['status'], '✅') !== false) {
                                    $passed_tests++;
                                } elseif (strpos($sub_data['status'], '❌') !== false) {
                                    $failed_tests++;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $success_rate = $total_tests > 0 ? round(($passed_tests / $total_tests) * 100, 2) : 0;
        
        echo "🎯 Test Summary:\n";
        echo "  Total Tests: {$total_tests}\n";
        echo "  Passed: {$passed_tests}\n";
        echo "  Failed: {$failed_tests}\n";
        echo "  Success Rate: {$success_rate}%\n\n";
        
        // Save detailed report
        $report_file = $this->canonical_paths['logs'] . '/comprehensive-test-report-' . date('Y-m-d-H-i-s') . '.json';
        $report_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'total_time' => $total_time,
            'summary' => [
                'total_tests' => $total_tests,
                'passed_tests' => $passed_tests,
                'failed_tests' => $failed_tests,
                'success_rate' => $success_rate
            ],
            'results' => $this->test_results
        ];
        
        if (is_writable(dirname($report_file))) {
            file_put_contents($report_file, json_encode($report_data, JSON_PRETTY_PRINT));
            echo "📄 Detailed report saved to: {$report_file}\n";
        }
        
        // Final status
        if ($success_rate >= 90) {
            echo "🎉 BlackCnote Test Suite: EXCELLENT ({$success_rate}%)\n";
        } elseif ($success_rate >= 75) {
            echo "✅ BlackCnote Test Suite: GOOD ({$success_rate}%)\n";
        } elseif ($success_rate >= 50) {
            echo "⚠️ BlackCnote Test Suite: NEEDS IMPROVEMENT ({$success_rate}%)\n";
        } else {
            echo "❌ BlackCnote Test Suite: CRITICAL ISSUES ({$success_rate}%)\n";
        }
        
        echo "\n🚀 BlackCnote Comprehensive Testing Complete!\n";
    }
}

// Run tests if called directly
if (defined('BLACKCNOTE_TESTING_MODE') && BLACKCNOTE_TESTING_MODE) {
    $test_suite = new BlackCnoteComprehensiveTest();
    $results = $test_suite->run_all_tests();
    
    // Exit with appropriate code
    $success_rate = 0;
    if (isset($results['summary']['success_rate'])) {
        $success_rate = $results['summary']['success_rate'];
    }
    
    exit($success_rate >= 75 ? 0 : 1);
} 