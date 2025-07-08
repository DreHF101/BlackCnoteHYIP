<?php
/**
 * BlackCnote Core Test Suite
 * 
 * Simplified testing script for core BlackCnote functionality
 * without database dependencies.
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
 * BlackCnote Core Test Suite
 */
class BlackCnoteCoreTest {
    
    private $test_results = [];
    private $start_time;
    private $canonical_paths;
    private $service_urls;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->start_time = microtime(true);
        $this->initialize_canonical_paths();
        $this->initialize_service_urls();
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
            'mailhog' => 'http://localhost:8025'
        ];
    }
    
    /**
     * Run all core tests
     */
    public function run_core_tests() {
        echo "🚀 Starting BlackCnote Core Test Suite...\n";
        echo "⏰ Test started at: " . date('Y-m-d H:i:s') . "\n\n";
        
        // Test 1: Canonical Paths Verification
        $this->test_canonical_paths();
        
        // Test 2: Service Connectivity
        $this->test_service_connectivity();
        
        // Test 3: Theme Functionality
        $this->test_theme_functionality();
        
        // Test 4: Docker Container Status
        $this->test_docker_containers();
        
        // Test 5: Performance Metrics
        $this->test_performance_metrics();
        
        // Generate report
        $this->generate_core_report();
        
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
     * Test theme functionality
     */
    private function test_theme_functionality() {
        echo "🎨 Testing Theme Functionality...\n";
        
        $theme_dir = $this->canonical_paths['theme'];
        $results = [];
        
        // Test essential theme files
        $essential_files = [
            'style.css',
            'functions.php',
            'index.php',
            'header.php',
            'footer.php'
        ];
        
        foreach ($essential_files as $file) {
            $file_path = $theme_dir . '/' . $file;
            $exists = file_exists($file_path);
            $readable = is_readable($file_path);
            
            $results[$file] = [
                'exists' => $exists,
                'readable' => $readable,
                'status' => $exists ? '✅' : '❌'
            ];
            
            echo "  {$results[$file]['status']} {$file}\n";
        }
        
        // Test theme constants
        $constants_file = $theme_dir . '/functions.php';
        if (file_exists($constants_file)) {
            $content = file_get_contents($constants_file);
            
            $constants = [
                'BLACKCNOTE_THEME_VERSION',
                'BLACKCNOTE_THEME_DIR',
                'BLACKCNOTE_THEME_URI',
                'BLACKCNOTE_CANONICAL_ROOT'
            ];
            
            foreach ($constants as $constant) {
                $defined = strpos($content, $constant) !== false;
                $results['constants'][$constant] = [
                    'defined' => $defined,
                    'status' => $defined ? '✅' : '❌'
                ];
                
                echo "  {$results['constants'][$constant]['status']} {$constant}\n";
            }
        }
        
        $this->test_results['theme_functionality'] = $results;
        echo "\n";
    }
    
    /**
     * Test Docker containers
     */
    private function test_docker_containers() {
        echo "🐳 Testing Docker Containers...\n";
        
        $results = [];
        
        // Check if Docker is available
        $docker_available = function_exists('shell_exec') && is_callable('shell_exec');
        
        if ($docker_available) {
            $docker_ps = shell_exec('docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" 2>&1');
            
            if ($docker_ps) {
                $lines = explode("\n", trim($docker_ps));
                foreach ($lines as $line) {
                    if (strpos($line, 'blackcnote') !== false) {
                        $parts = preg_split('/\s+/', trim($line), 3);
                        if (count($parts) >= 2) {
                            $container_name = $parts[0];
                            $status = $parts[1];
                            $ports = isset($parts[2]) ? $parts[2] : '';
                            
                            $running = strpos($status, 'Up') !== false;
                            $status_icon = $running ? '✅' : '❌';
                            
                            $results[$container_name] = [
                                'status' => $status,
                                'running' => $running,
                                'ports' => $ports
                            ];
                            
                            echo "  {$status_icon} {$container_name}: {$status}\n";
                        }
                    }
                }
            }
        } else {
            echo "  ⚠️  Docker command not available\n";
        }
        
        $this->test_results['docker_containers'] = $results;
        echo "\n";
    }
    
    /**
     * Test performance metrics
     */
    private function test_performance_metrics() {
        echo "⚡ Testing Performance Metrics...\n";
        
        $results = [];
        
        // Memory usage
        $memory_usage = memory_get_usage(true);
        $peak_memory = memory_get_peak_usage(true);
        
        $results['memory'] = [
            'current' => $this->format_bytes($memory_usage),
            'peak' => $this->format_bytes($peak_memory),
            'current_bytes' => $memory_usage,
            'peak_bytes' => $peak_memory
        ];
        
        echo "  📊 Memory Usage: {$results['memory']['current']}\n";
        echo "  📊 Peak Memory: {$results['memory']['peak']}\n";
        
        // Execution time
        $execution_time = microtime(true) - $this->start_time;
        $results['execution_time'] = round($execution_time, 4);
        
        echo "  ⏱️  Execution Time: {$results['execution_time']}s\n";
        
        $this->test_results['performance_metrics'] = $results;
        echo "\n";
    }
    
    /**
     * Make HTTP request
     */
    private function make_http_request($url) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'method' => 'GET',
                'header' => 'User-Agent: BlackCnote-Test/1.0'
            ]
        ]);
        
        $start_time = microtime(true);
        $content = @file_get_contents($url, false, $context);
        $end_time = microtime(true);
        
        if ($content === false) {
            return [
                'success' => false,
                'code' => 0,
                'error' => 'Connection failed',
                'response_time' => round(($end_time - $start_time) * 1000, 2)
            ];
        }
        
        $headers = $http_response_header ?? [];
        $status_line = $headers[0] ?? '';
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $status_line, $matches);
        $status_code = isset($matches[1]) ? (int)$matches[1] : 200;
        
        return [
            'success' => $status_code >= 200 && $status_code < 400,
            'code' => $status_code,
            'content_length' => strlen($content),
            'response_time' => round(($end_time - $start_time) * 1000, 2)
        ];
    }
    
    /**
     * Format bytes to human readable
     */
    private function format_bytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Generate core report
     */
    private function generate_core_report() {
        echo "📋 Generating Core Test Report...\n";
        echo "================================\n\n";
        
        $total_tests = 0;
        $passed_tests = 0;
        
        // Canonical paths summary
        if (isset($this->test_results['canonical_paths'])) {
            $paths = $this->test_results['canonical_paths'];
            $total_tests += count($paths);
            $passed_tests += count(array_filter($paths, fn($p) => $p['exists']));
        }
        
        // Service connectivity summary
        if (isset($this->test_results['service_connectivity'])) {
            $services = $this->test_results['service_connectivity'];
            $total_tests += count($services);
            $passed_tests += count(array_filter($services, fn($s) => $s['status'] === 'up'));
        }
        
        // Theme functionality summary
        if (isset($this->test_results['theme_functionality'])) {
            $theme = $this->test_results['theme_functionality'];
            if (isset($theme['constants'])) {
                $total_tests += count($theme['constants']);
                $passed_tests += count(array_filter($theme['constants'], fn($c) => $c['defined']));
            }
        }
        
        // Docker containers summary
        if (isset($this->test_results['docker_containers'])) {
            $containers = $this->test_results['docker_containers'];
            $total_tests += count($containers);
            $passed_tests += count(array_filter($containers, fn($c) => $c['running']));
        }
        
        $success_rate = $total_tests > 0 ? round(($passed_tests / $total_tests) * 100, 2) : 0;
        
        echo "📊 Test Summary:\n";
        echo "  Total Tests: {$total_tests}\n";
        echo "  Passed: {$passed_tests}\n";
        echo "  Failed: " . ($total_tests - $passed_tests) . "\n";
        echo "  Success Rate: {$success_rate}%\n\n";
        
        if ($success_rate >= 80) {
            echo "🎉 BlackCnote Core Test Suite PASSED!\n";
        } else {
            echo "⚠️  BlackCnote Core Test Suite has issues that need attention.\n";
        }
        
        echo "\n⏰ Test completed at: " . date('Y-m-d H:i:s') . "\n";
    }
}

// Run the test if executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    $test = new BlackCnoteCoreTest();
    $test->run_core_tests();
} 