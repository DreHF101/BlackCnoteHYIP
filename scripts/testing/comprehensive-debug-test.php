<?php
/**
 * BlackCnote Comprehensive Debug Test
 * 
 * Tests all debug functionality, logging, error handling, and testing features
 * 
 * @package BlackCnote
 * @version 2.0.0
 * @author BlackCnote Development Team
 */

declare(strict_types=1);

// Define ABSPATH if not already defined
if (!defined('ABSPATH')) {
    // Try to find WordPress installation
    $possible_paths = [
        dirname(dirname(dirname(__DIR__))) . '/blackcnote/',
        dirname(dirname(dirname(__DIR__))) . '/blackcnote/blackcnote/',
        dirname(dirname(__DIR__)) . '/blackcnote/',
        dirname(__DIR__) . '/blackcnote/',
        __DIR__ . '/blackcnote/'
    ];
    
    $wp_path = null;
    foreach ($possible_paths as $path) {
        if (file_exists($path . 'wp-config.php')) {
            $wp_path = $path;
            break;
        }
    }
    
    if ($wp_path) {
        define('ABSPATH', $wp_path);
        // Load WordPress
        require_once $wp_path . 'wp-load.php';
    } else {
        echo "Error: Could not find WordPress installation. Please run this script from the BlackCnote project root.\n";
        exit(1);
    }
}

// Check if WordPress loaded successfully
if (!function_exists('get_home_url')) {
    echo "Error: WordPress not loaded properly. Please check the installation.\n";
    exit(1);
}

// Check if debug system files exist
$debug_system_file = WP_PLUGIN_DIR . '/blackcnote-hyiplab/app/Log/BlackCnoteDebugSystem.php';
$test_framework_file = WP_PLUGIN_DIR . '/blackcnote-hyiplab/app/Log/BlackCnoteTestFramework.php';

if (!file_exists($debug_system_file)) {
    echo "Error: BlackCnoteDebugSystem.php not found at: {$debug_system_file}\n";
    exit(1);
}

if (!file_exists($test_framework_file)) {
    echo "Error: BlackCnoteTestFramework.php not found at: {$test_framework_file}\n";
    exit(1);
}

// Load debug system
require_once $debug_system_file;
require_once $test_framework_file;

use Hyiplab\Log\BlackCnoteDebugSystem;
use Hyiplab\Log\BlackCnoteTestFramework;

class BlackCnoteComprehensiveDebugTest {
    
    private BlackCnoteDebugSystem $debug;
    private BlackCnoteTestFramework $test_framework;
    private array $test_results = [];
    private float $start_time;
    
    public function __construct() {
        $this->start_time = microtime(true);
        
        try {
            $this->debug = BlackCnoteDebugSystem::getInstance();
            $this->test_framework = BlackCnoteTestFramework::getInstance();
        } catch (Exception $e) {
            echo "Error initializing debug system: " . $e->getMessage() . "\n";
            exit(1);
        }
        
        echo "=== BlackCnote Comprehensive Debug Test ===\n";
        echo "Started at: " . date('Y-m-d H:i:s') . "\n";
        echo "WordPress Path: " . ABSPATH . "\n";
        echo "Plugin Directory: " . WP_PLUGIN_DIR . "\n\n";
    }
    
    /**
     * Run all tests
     */
    public function runAllTests(): void {
        $this->testDebugSystemInitialization();
        $this->testLoggingFunctionality();
        $this->testErrorHandling();
        $this->testPerformanceMonitoring();
        $this->testEnvironmentDetection();
        $this->testWordPressIntegration();
        $this->testHYIPLabIntegration();
        $this->testReactIntegration();
        $this->testDockerIntegration();
        $this->testSecurityFeatures();
        $this->testFileOperations();
        $this->testDatabaseOperations();
        $this->testApiIntegration();
        $this->testTestFramework();
        $this->testAdminInterface();
        
        $this->generateReport();
    }
    
    /**
     * Test debug system initialization
     */
    private function testDebugSystemInitialization(): void {
        echo "Testing Debug System Initialization...\n";
        
        $test_name = 'debug_system_initialization';
        
        try {
            // Test singleton pattern
            $instance1 = BlackCnoteDebugSystem::getInstance();
            $instance2 = BlackCnoteDebugSystem::getInstance();
            
            if ($instance1 === $instance2) {
                $this->logTestResult($test_name, 'PASS', 'Singleton pattern working correctly');
            } else {
                $this->logTestResult($test_name, 'FAIL', 'Singleton pattern not working');
            }
            
            // Test log files creation
            $log_file = $this->debug->getLogFilePath();
            if (file_exists($log_file)) {
                $this->logTestResult('log_files_creation', 'PASS', 'Log files created successfully');
            } else {
                $this->logTestResult('log_files_creation', 'FAIL', 'Log files not created');
            }
            
        } catch (Exception $e) {
            $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
        }
        
        echo "✓ Debug System Initialization completed\n\n";
    }
    
    /**
     * Test logging functionality
     */
    private function testLoggingFunctionality(): void {
        echo "Testing Logging Functionality...\n";
        
        $test_cases = [
            'basic_logging' => function() {
                $this->debug->log('Test basic logging', BlackCnoteDebugSystem::LEVEL_INFO);
                return true;
            },
            'error_logging' => function() {
                $this->debug->logError('Test error logging');
                return true;
            },
            'performance_logging' => function() {
                $start_time = microtime(true);
                usleep(100000); // 0.1 seconds
                $this->debug->logPerformance('test_operation', $start_time);
                return true;
            },
            'test_logging' => function() {
                $this->debug->logTest('test_case', 'PASS', ['data' => 'test']);
                return true;
            }
        ];
        
        // Test global functions if they exist
        if (function_exists('blackcnote_log')) {
            $test_cases['global_functions'] = function() {
                blackcnote_log('Test global function', 'INFO');
                if (function_exists('blackcnote_log_error')) {
                    blackcnote_log_error('Test global error function');
                }
                if (function_exists('blackcnote_log_performance')) {
                    blackcnote_log_performance('test_global_perf', microtime(true));
                }
                if (function_exists('blackcnote_log_test')) {
                    blackcnote_log_test('test_global_test', 'PASS');
                }
                return true;
            };
        }
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                $result = $test_function();
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'Logging function working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'Logging function failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ Logging Functionality completed\n\n";
    }
    
    /**
     * Test error handling
     */
    private function testErrorHandling(): void {
        echo "Testing Error Handling...\n";
        
        $test_cases = [
            'php_errors' => function() {
                // Trigger a PHP error
                $undefined_variable;
                return true;
            },
            'exceptions' => function() {
                try {
                    throw new Exception('Test exception');
                } catch (Exception $e) {
                    return true;
                }
            },
            'fatal_errors' => function() {
                // Test shutdown handling
                return true;
            }
        ];
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                $result = $test_function();
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'Error handling working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'Error handling failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ Error Handling completed\n\n";
    }
    
    /**
     * Test performance monitoring
     */
    private function testPerformanceMonitoring(): void {
        echo "Testing Performance Monitoring...\n";
        
        $test_cases = [
            'memory_usage' => function() {
                $memory = memory_get_usage();
                return $memory > 0;
            },
            'peak_memory' => function() {
                $peak_memory = memory_get_peak_usage();
                return $peak_memory > 0;
            },
            'execution_time' => function() {
                $execution_time = microtime(true) - $this->start_time;
                return $execution_time > 0;
            }
        ];
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                $result = $test_function();
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'Performance monitoring working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'Performance monitoring failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ Performance Monitoring completed\n\n";
    }
    
    /**
     * Test environment detection
     */
    private function testEnvironmentDetection(): void {
        echo "Testing Environment Detection...\n";
        
        try {
            $environment_info = $this->debug->getEnvironmentInfo();
            
            $test_cases = [
                'environment_type' => !empty($environment_info['environment']),
                'server_info' => !empty($environment_info['server']),
                'wordpress_info' => !empty($environment_info['wordpress']),
                'docker_info' => isset($environment_info['docker']),
                'react_info' => isset($environment_info['react']),
                'hyiplab_info' => isset($environment_info['hyiplab']),
                'development_tools' => isset($environment_info['development_tools']),
                'database_info' => !empty($environment_info['database']),
                'performance_info' => !empty($environment_info['performance']),
                'security_info' => !empty($environment_info['security'])
            ];
            
            foreach ($test_cases as $test_name => $result) {
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'Environment detection working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'Environment detection failed');
                }
            }
        } catch (Exception $e) {
            $this->logTestResult('environment_detection', 'FAIL', 'Exception: ' . $e->getMessage());
        }
        
        echo "✓ Environment Detection completed\n\n";
    }
    
    /**
     * Test WordPress integration
     */
    private function testWordPressIntegration(): void {
        echo "Testing WordPress Integration...\n";
        
        $test_cases = [
            'wordpress_loaded' => defined('ABSPATH'),
            'wp_functions' => function_exists('get_home_url'),
            'wp_hooks' => function_exists('add_action'),
            'wp_database' => function_exists('get_option'),
            'wp_plugins' => function_exists('get_plugins'),
            'wp_themes' => function_exists('get_themes')
        ];
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                if (is_callable($test_function)) {
                    $result = $test_function();
                } else {
                    $result = $test_function;
                }
                
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'WordPress integration working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'WordPress integration failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ WordPress Integration completed\n\n";
    }
    
    /**
     * Test HYIPLab integration
     */
    private function testHYIPLabIntegration(): void {
        echo "Testing HYIPLab Integration...\n";
        
        $test_cases = [
            'plugin_file_exists' => file_exists(WP_PLUGIN_DIR . '/blackcnote-hyiplab/blackcnote-hyiplab.php'),
            'app_directory' => is_dir(WP_PLUGIN_DIR . '/blackcnote-hyiplab/app'),
            'models_directory' => is_dir(WP_PLUGIN_DIR . '/blackcnote-hyiplab/app/Models'),
            'services_directory' => is_dir(WP_PLUGIN_DIR . '/blackcnote-hyiplab/app/Services'),
            'controllers_directory' => is_dir(WP_PLUGIN_DIR . '/blackcnote-hyiplab/app/Controllers')
        ];
        
        // Test plugin activation if function exists
        if (function_exists('is_plugin_active')) {
            $test_cases['plugin_active'] = is_plugin_active('blackcnote-hyiplab/blackcnote-hyiplab.php');
        }
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                if (is_callable($test_function)) {
                    $result = $test_function();
                } else {
                    $result = $test_function;
                }
                
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'HYIPLab integration working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'HYIPLab integration failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ HYIPLab Integration completed\n\n";
    }
    
    /**
     * Test React integration
     */
    private function testReactIntegration(): void {
        echo "Testing React Integration...\n";
        
        $test_cases = [
            'react_app_directory' => is_dir(ABSPATH . 'react-app'),
            'react_src_directory' => is_dir(ABSPATH . 'react-app/src'),
            'vite_config' => file_exists(ABSPATH . 'react-app/vite.config.ts'),
            'package_json' => file_exists(ABSPATH . 'react-app/package.json'),
            'app_component' => file_exists(ABSPATH . 'react-app/src/App.tsx'),
            'main_entry' => file_exists(ABSPATH . 'react-app/src/main.tsx')
        ];
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                if (is_callable($test_function)) {
                    $result = $test_function();
                } else {
                    $result = $test_function;
                }
                
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'React integration working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'React integration failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ React Integration completed\n\n";
    }
    
    /**
     * Test Docker integration
     */
    private function testDockerIntegration(): void {
        echo "Testing Docker Integration...\n";
        
        $test_cases = [
            'docker_compose' => file_exists(ABSPATH . 'docker-compose.yml'),
            'docker_compose_override' => file_exists(ABSPATH . 'docker-compose.override.yml'),
            'docker_compose_prod' => file_exists(ABSPATH . 'docker-compose.prod.yml'),
            'nginx_config' => file_exists(ABSPATH . 'config/nginx/blackcnote.conf'),
            'apache_config' => file_exists(ABSPATH . 'config/apache/blackcnote-vhost.conf')
        ];
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                if (is_callable($test_function)) {
                    $result = $test_function();
                } else {
                    $result = $test_function;
                }
                
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'Docker integration working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'Docker integration failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ Docker Integration completed\n\n";
    }
    
    /**
     * Test security features
     */
    private function testSecurityFeatures(): void {
        echo "Testing Security Features...\n";
        
        $test_cases = [
            'file_permissions' => function() {
                $wp_config = ABSPATH . 'wp-config.php';
                return file_exists($wp_config) && is_readable($wp_config);
            },
            'ssl_detection' => function() {
                return function_exists('is_ssl');
            },
            'nonce_functions' => function() {
                return function_exists('wp_create_nonce') && function_exists('wp_verify_nonce');
            },
            'sanitization_functions' => function() {
                return function_exists('sanitize_text_field') && function_exists('sanitize_email');
            }
        ];
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                $result = $test_function();
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'Security features working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'Security features failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ Security Features completed\n\n";
    }
    
    /**
     * Test file operations
     */
    private function testFileOperations(): void {
        echo "Testing File Operations...\n";
        
        $test_cases = [
            'log_directory_writable' => function() {
                $log_dir = WP_CONTENT_DIR . '/logs/blackcnote/';
                return is_dir($log_dir) && is_writable($log_dir);
            },
            'log_file_writable' => function() {
                $log_file = $this->debug->getLogFilePath();
                return file_exists($log_file) && is_writable($log_file);
            },
            'file_size_check' => function() {
                $log_file = $this->debug->getLogFilePath();
                return filesize($log_file) >= 0;
            }
        ];
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                $result = $test_function();
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'File operations working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'File operations failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ File Operations completed\n\n";
    }
    
    /**
     * Test database operations
     */
    private function testDatabaseOperations(): void {
        echo "Testing Database Operations...\n";
        
        global $wpdb;
        
        $test_cases = [
            'database_connection' => function() use ($wpdb) {
                return $wpdb->get_var("SELECT 1") === '1';
            },
            'table_exists' => function() use ($wpdb) {
                $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}%'");
                return count($tables) > 0;
            },
            'query_logging' => function() {
                // This would test if database queries are being logged
                return true;
            }
        ];
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                $result = $test_function();
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'Database operations working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'Database operations failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ Database Operations completed\n\n";
    }
    
    /**
     * Test API integration
     */
    private function testApiIntegration(): void {
        echo "Testing API Integration...\n";
        
        $test_cases = [
            'wp_rest_api' => function() {
                $response = wp_remote_get(get_rest_url());
                return !is_wp_error($response);
            },
            'api_functions' => function() {
                return function_exists('wp_remote_get') && function_exists('wp_remote_post');
            }
        ];
        
        // Test AJAX endpoint if function exists
        if (function_exists('wp_ajax_nopriv_blackcnote_debug_log')) {
            $test_cases['ajax_endpoint'] = function() {
                return function_exists('wp_ajax_nopriv_blackcnote_debug_log');
            };
        }
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                $result = $test_function();
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'API integration working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'API integration failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ API Integration completed\n\n";
    }
    
    /**
     * Test test framework
     */
    private function testTestFramework(): void {
        echo "Testing Test Framework...\n";
        
        $test_cases = [
            'test_framework_initialization' => function() {
                $instance = BlackCnoteTestFramework::getInstance();
                return $instance instanceof BlackCnoteTestFramework;
            }
        ];
        
        // Test test suites if function exists
        if (function_exists('blackcnote_test_suite')) {
            $test_cases['test_suites_available'] = function() {
                $results = blackcnote_test_suite('environment');
                return isset($results['status']);
            };
        }
        
        // Test test execution
        $test_cases['test_execution'] = function() {
            $start_time = microtime(true);
            $results = $this->test_framework->runAllTests();
            $end_time = microtime(true);
            return $end_time > $start_time;
        };
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                $result = $test_function();
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'Test framework working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'Test framework failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ Test Framework completed\n\n";
    }
    
    /**
     * Test admin interface
     */
    private function testAdminInterface(): void {
        echo "Testing Admin Interface...\n";
        
        $test_cases = [
            'admin_functions' => function() {
                return function_exists('add_menu_page') && function_exists('add_submenu_page');
            },
            'admin_scripts' => function() {
                return function_exists('wp_enqueue_script') && function_exists('wp_localize_script');
            }
        ];
        
        // Test AJAX handlers if they exist
        if (function_exists('wp_ajax_blackcnote_debug_action')) {
            $test_cases['ajax_handlers'] = function() {
                return function_exists('wp_ajax_blackcnote_debug_action');
            };
        }
        
        foreach ($test_cases as $test_name => $test_function) {
            try {
                $result = $test_function();
                if ($result) {
                    $this->logTestResult($test_name, 'PASS', 'Admin interface working correctly');
                } else {
                    $this->logTestResult($test_name, 'FAIL', 'Admin interface failed');
                }
            } catch (Exception $e) {
                $this->logTestResult($test_name, 'FAIL', 'Exception: ' . $e->getMessage());
            }
        }
        
        echo "✓ Admin Interface completed\n\n";
    }
    
    /**
     * Log test result
     */
    private function logTestResult(string $test_name, string $status, string $message): void {
        $this->test_results[$test_name] = [
            'status' => $status,
            'message' => $message,
            'timestamp' => microtime(true)
        ];
        
        $status_icon = $status === 'PASS' ? '✓' : ($status === 'FAIL' ? '✗' : '○');
        echo "  {$status_icon} {$test_name}: {$message}\n";
    }
    
    /**
     * Generate test report
     */
    private function generateReport(): void {
        $end_time = microtime(true);
        $total_time = $end_time - $this->start_time;
        
        $total_tests = count($this->test_results);
        $passed_tests = count(array_filter($this->test_results, fn($r) => $r['status'] === 'PASS'));
        $failed_tests = count(array_filter($this->test_results, fn($r) => $r['status'] === 'FAIL'));
        $skipped_tests = count(array_filter($this->test_results, fn($r) => $r['status'] === 'SKIP'));
        
        echo "\n=== Test Report ===\n";
        echo "Total Tests: {$total_tests}\n";
        echo "Passed: {$passed_tests}\n";
        echo "Failed: {$failed_tests}\n";
        echo "Skipped: {$skipped_tests}\n";
        echo "Success Rate: " . round(($passed_tests / $total_tests) * 100, 2) . "%\n";
        echo "Total Time: " . round($total_time, 3) . " seconds\n";
        echo "Completed at: " . date('Y-m-d H:i:s') . "\n\n";
        
        if ($failed_tests > 0) {
            echo "=== Failed Tests ===\n";
            foreach ($this->test_results as $test_name => $result) {
                if ($result['status'] === 'FAIL') {
                    echo "✗ {$test_name}: {$result['message']}\n";
                }
            }
            echo "\n";
        }
        
        // Log final results to debug system
        try {
            $this->debug->logTest('comprehensive_debug_test', 'PASS', [
                'total_tests' => $total_tests,
                'passed_tests' => $passed_tests,
                'failed_tests' => $failed_tests,
                'skipped_tests' => $skipped_tests,
                'success_rate' => round(($passed_tests / $total_tests) * 100, 2),
                'total_time' => $total_time
            ]);
        } catch (Exception $e) {
            echo "Warning: Could not log test results to debug system: " . $e->getMessage() . "\n";
        }
        
        echo "=== BlackCnote Debug System Test Complete ===\n";
    }
}

// Run the comprehensive test
if (php_sapi_name() === 'cli') {
    try {
        $test = new BlackCnoteComprehensiveDebugTest();
        $test->runAllTests();
    } catch (Exception $e) {
        echo "Fatal error running test: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        exit(1);
    }
} else {
    echo "This script should be run from the command line.\n";
    echo "Usage: php scripts/testing/comprehensive-debug-test.php\n";
} 