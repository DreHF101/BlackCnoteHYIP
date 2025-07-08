<?php
/**
 * BlackCnote Comprehensive Diagnostic Script
 * 
 * Tests all services, ports, and functionality in the development environment
 */

declare(strict_types=1);

echo "🔍 BLACKCNOTE COMPREHENSIVE DIAGNOSTIC\n";
echo "=====================================\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

class BlackCnoteDiagnostic {
    private $services = [
        'wordpress' => [
            'url' => 'http://localhost:8888',
            'port' => 8888,
            'container' => 'blackcnote-wordpress',
            'required' => true
        ],
        'react' => [
            'url' => 'http://localhost:5174',
            'port' => 5174,
            'container' => 'blackcnote-react',
            'required' => true
        ],
        'mysql' => [
            'url' => 'mysql://localhost:3306',
            'port' => 3306,
            'container' => 'blackcnote-mysql',
            'required' => true
        ],
        'phpmyadmin' => [
            'url' => 'http://localhost:8080',
            'port' => 8080,
            'container' => 'blackcnote-phpmyadmin',
            'required' => false
        ],
        'redis' => [
            'url' => 'redis://localhost:6379',
            'port' => 6379,
            'container' => 'blackcnote-redis',
            'required' => true
        ],
        'redis-commander' => [
            'url' => 'http://localhost:8081',
            'port' => 8081,
            'container' => 'blackcnote-redis-commander',
            'required' => false
        ],
        'mailhog' => [
            'url' => 'http://localhost:8025',
            'port' => 8025,
            'container' => 'blackcnote-mailhog',
            'required' => false
        ],
        'browsersync' => [
            'url' => 'http://localhost:3000',
            'port' => 3000,
            'container' => 'blackcnote-browsersync',
            'required' => false
        ]
    ];
    
    private $results = [];
    private $errors = [];
    private $warnings = [];
    
    public function run(): void {
        echo "TESTING DOCKER SERVICES\n";
        echo "----------------------\n";
        $this->testDockerServices();
        
        echo "\nTESTING PORT ACCESSIBILITY\n";
        echo "-------------------------\n";
        $this->testPortAccessibility();
        
        echo "\nTESTING WORDPRESS FUNCTIONALITY\n";
        echo "-------------------------------\n";
        $this->testWordPressFunctionality();
        
        echo "\nTESTING REACT APP FUNCTIONALITY\n";
        echo "-------------------------------\n";
        $this->testReactAppFunctionality();
        
        echo "\nTESTING DATABASE CONNECTIVITY\n";
        echo "----------------------------\n";
        $this->testDatabaseConnectivity();
        
        echo "\nTESTING CORS FUNCTIONALITY\n";
        echo "-------------------------\n";
        $this->testCorsFunctionality();
        
        echo "\nTESTING HYIPLAB INTEGRATION\n";
        echo "--------------------------\n";
        $this->testHYIPLabIntegration();
        
        echo "\nDIAGNOSTIC SUMMARY\n";
        echo "=================\n";
        $this->printSummary();
    }
    
    private function testDockerServices(): void {
        foreach ($this->services as $service => $config) {
            $status = $this->checkDockerService($service, $config);
            $this->results['docker'][$service] = $status;
            
            if ($status['running']) {
                echo "✅ {$service}: Running (PID: {$status['pid']})\n";
            } else {
                echo "❌ {$service}: Not running\n";
                if ($config['required']) {
                    $this->errors[] = "Required service {$service} is not running";
                } else {
                    $this->warnings[] = "Optional service {$service} is not running";
                }
            }
        }
    }
    
    private function checkDockerService(string $service, array $config): array {
        $output = shell_exec("docker ps --filter name={$config['container']} --format '{{.Names}}\t{{.Status}}\t{{.Ports}}' 2>&1");
        
        if (empty($output)) {
            return ['running' => false, 'pid' => null, 'status' => 'not found'];
        }
        
        $lines = explode("\n", trim($output));
        foreach ($lines as $line) {
            if (strpos($line, $config['container']) !== false) {
                $parts = explode("\t", $line);
                $status = $parts[1] ?? 'unknown';
                $ports = $parts[2] ?? '';
                
                return [
                    'running' => strpos($status, 'Up') !== false,
                    'pid' => 'docker',
                    'status' => $status,
                    'ports' => $ports
                ];
            }
        }
        
        return ['running' => false, 'pid' => null, 'status' => 'not found'];
    }
    
    private function testPortAccessibility(): void {
        foreach ($this->services as $service => $config) {
            if ($config['port']) {
                $accessible = $this->testPort($config['port']);
                $this->results['ports'][$service] = $accessible;
                
                if ($accessible) {
                    echo "✅ Port {$config['port']} ({$service}): Accessible\n";
                } else {
                    echo "❌ Port {$config['port']} ({$service}): Not accessible\n";
                    if ($config['required']) {
                        $this->errors[] = "Required port {$config['port']} ({$service}) is not accessible";
                    } else {
                        $this->warnings[] = "Optional port {$config['port']} ({$service}) is not accessible";
                    }
                }
            }
        }
    }
    
    private function testPort(int $port): bool {
        $connection = @fsockopen('localhost', $port, $errno, $errstr, 5);
        if ($connection) {
            fclose($connection);
            return true;
        }
        return false;
    }
    
    private function testWordPressFunctionality(): void {
        // Test WordPress homepage
        $response = $this->makeHttpRequest('http://localhost:8888');
        if ($response && $response['status'] === 200) {
            echo "✅ WordPress Homepage: Accessible\n";
            $this->results['wordpress']['homepage'] = true;
        } else {
            echo "❌ WordPress Homepage: Not accessible\n";
            $this->errors[] = "WordPress homepage not accessible";
            $this->results['wordpress']['homepage'] = false;
        }
        
        // Test WordPress admin
        $response = $this->makeHttpRequest('http://localhost:8888/wp-admin/');
        if ($response && $response['status'] === 200) {
            echo "✅ WordPress Admin: Accessible\n";
            $this->results['wordpress']['admin'] = true;
        } else {
            echo "❌ WordPress Admin: Not accessible\n";
            $this->errors[] = "WordPress admin not accessible";
            $this->results['wordpress']['admin'] = false;
        }
        
        // Test WordPress REST API
        $response = $this->makeHttpRequest('http://localhost:8888/wp-json/');
        if ($response && $response['status'] === 200) {
            echo "✅ WordPress REST API: Accessible\n";
            $this->results['wordpress']['rest_api'] = true;
        } else {
            echo "❌ WordPress REST API: Not accessible\n";
            $this->errors[] = "WordPress REST API not accessible";
            $this->results['wordpress']['rest_api'] = false;
        }
    }
    
    private function testReactAppFunctionality(): void {
        // Test React app
        $response = $this->makeHttpRequest('http://localhost:5174');
        if ($response && $response['status'] === 200) {
            echo "✅ React App: Accessible\n";
            $this->results['react']['app'] = true;
        } else {
            echo "❌ React App: Not accessible\n";
            $this->errors[] = "React app not accessible";
            $this->results['react']['app'] = false;
        }
        
        // Test React HMR
        $response = $this->makeHttpRequest('http://localhost:5178');
        if ($response && $response['status'] === 200) {
            echo "✅ React HMR: Accessible\n";
            $this->results['react']['hmr'] = true;
        } else {
            echo "⚠️ React HMR: Not accessible (this is normal if not in use)\n";
            $this->results['react']['hmr'] = false;
        }
    }
    
    private function testDatabaseConnectivity(): void {
        // Test MySQL connection
        $connection = @fsockopen('localhost', 3306, $errno, $errstr, 5);
        if ($connection) {
            echo "✅ MySQL Database: Accessible\n";
            fclose($connection);
            $this->results['database']['mysql'] = true;
        } else {
            echo "❌ MySQL Database: Not accessible\n";
            $this->errors[] = "MySQL database not accessible";
            $this->results['database']['mysql'] = false;
        }
        
        // Test Redis connection
        $connection = @fsockopen('localhost', 6379, $errno, $errstr, 5);
        if ($connection) {
            echo "✅ Redis Cache: Accessible\n";
            fclose($connection);
            $this->results['database']['redis'] = true;
        } else {
            echo "❌ Redis Cache: Not accessible\n";
            $this->errors[] = "Redis cache not accessible";
            $this->results['database']['redis'] = false;
        }
    }
    
    private function testCorsFunctionality(): void {
        // Test CORS headers
        $headers = [
            'Origin: http://localhost:5174',
            'X-Requested-With: XMLHttpRequest'
        ];
        
        $response = $this->makeHttpRequest('http://localhost:8888/wp-json/', $headers);
        if ($response && isset($response['headers']['Access-Control-Allow-Origin'])) {
            echo "✅ CORS Headers: Present\n";
            $this->results['cors']['headers'] = true;
        } else {
            echo "❌ CORS Headers: Missing\n";
            $this->errors[] = "CORS headers missing";
            $this->results['cors']['headers'] = false;
        }
    }
    
    private function testHYIPLabIntegration(): void {
        // Test HYIPLab plugin
        $response = $this->makeHttpRequest('http://localhost:8888/wp-content/plugins/hyiplab/');
        if ($response && $response['status'] === 200) {
            echo "✅ HYIPLab Plugin: Accessible\n";
            $this->results['hyiplab']['plugin'] = true;
        } else {
            echo "❌ HYIPLab Plugin: Not accessible\n";
            $this->errors[] = "HYIPLab plugin not accessible";
            $this->results['hyiplab']['plugin'] = false;
        }
        
        // Test HYIPLab API
        $response = $this->makeHttpRequest('http://localhost:8888/wp-json/hyiplab/v1/');
        if ($response && $response['status'] === 200) {
            echo "✅ HYIPLab API: Accessible\n";
            $this->results['hyiplab']['api'] = true;
        } else {
            echo "❌ HYIPLab API: Not accessible\n";
            $this->errors[] = "HYIPLab API not accessible";
            $this->results['hyiplab']['api'] = false;
        }
    }
    
    private function makeHttpRequest(string $url, array $headers = []): ?array {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'header' => implode("\r\n", $headers)
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return null;
        }
        
        $httpResponseHeader = $http_response_header ?? [];
        $statusLine = $httpResponseHeader[0] ?? '';
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $statusLine, $matches);
        $statusCode = $matches[1] ?? 0;
        
        $headers = [];
        foreach ($httpResponseHeader as $header) {
            if (strpos($header, ':') !== false) {
                list($key, $value) = explode(':', $header, 2);
                $headers[trim($key)] = trim($value);
            }
        }
        
        return [
            'status' => (int)$statusCode,
            'headers' => $headers,
            'body' => $response
        ];
    }
    
    private function printSummary(): void {
        $totalTests = 0;
        $passedTests = 0;
        
        foreach ($this->results as $category => $tests) {
            foreach ($tests as $test => $result) {
                $totalTests++;
                if (is_array($result) && isset($result['running'])) {
                    if ($result['running']) $passedTests++;
                } elseif (is_bool($result)) {
                    if ($result) $passedTests++;
                }
            }
        }
        
        $successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 0;
        
        echo "Total Tests: {$totalTests}\n";
        echo "Passed: {$passedTests}\n";
        echo "Failed: " . ($totalTests - $passedTests) . "\n";
        echo "Success Rate: {$successRate}%\n\n";
        
        if (!empty($this->errors)) {
            echo "❌ ERRORS:\n";
            foreach ($this->errors as $error) {
                echo "  - {$error}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->warnings)) {
            echo "⚠️ WARNINGS:\n";
            foreach ($this->warnings as $warning) {
                echo "  - {$warning}\n";
            }
            echo "\n";
        }
        
        if (empty($this->errors) && empty($this->warnings)) {
            echo "✅ All systems operational!\n";
        } else {
            echo "🔧 Some issues detected. Please review the errors and warnings above.\n";
        }
    }
}

// Run the diagnostic
$diagnostic = new BlackCnoteDiagnostic();
$diagnostic->run(); 