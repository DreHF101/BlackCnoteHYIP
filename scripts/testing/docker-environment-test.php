<?php
/**
 * BlackCnote Docker Environment Test Script
 * 
 * This script tests all components of the Docker environment
 * to ensure everything is working correctly.
 */

declare(strict_types=1);

class DockerEnvironmentTester {
    private array $results = [];
    private array $errors = [];
    private array $warnings = [];

    public function runAllTests(): void {
        echo "🐳 BlackCnote Docker Environment Test\n";
        echo "=====================================\n\n";

        $this->testDockerServices();
        $this->testWordPressConfiguration();
        $this->testDatabaseConnection();
        $this->testRedisConnection();
        $this->testFileSystem();
        $this->testNetworkConnectivity();
        $this->testDevelopmentTools();
        $this->testSecuritySettings();

        $this->displayResults();
    }

    private function testDockerServices(): void {
        echo "1. Testing Docker Services...\n";
        
        $services = [
            'wordpress' => 'http://localhost:8888/blackcnote',
            'react-app' => 'http://localhost:5174',
            'phpmyadmin' => 'http://localhost:8080',
            'mailhog' => 'http://localhost:8025',
            'redis-commander' => 'http://localhost:8081'
        ];

        foreach ($services as $service => $url) {
            $this->testService($service, $url);
        }
    }

    private function testService(string $service, string $url): void {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'method' => 'GET'
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $this->results[$service] = '✅ Running';
            echo "   ✅ $service: Running\n";
        } else {
            $this->errors[$service] = '❌ Not accessible';
            echo "   ❌ $service: Not accessible\n";
        }
    }

    private function testWordPressConfiguration(): void {
        echo "\n2. Testing WordPress Configuration...\n";
        
        // Test wp-config.php
        if (file_exists('blackcnote/wp-config.php')) {
            $this->results['wp-config'] = '✅ Exists';
            echo "   ✅ wp-config.php: Exists\n";
            
            // Check Docker-specific settings
            $config = file_get_contents('blackcnote/wp-config.php');
            
            if (strpos($config, "DB_HOST', 'mysql'") !== false) {
                $this->results['db-host'] = '✅ Configured for Docker';
                echo "   ✅ Database host: Configured for Docker\n";
            } else {
                $this->errors['db-host'] = '❌ Not configured for Docker';
                echo "   ❌ Database host: Not configured for Docker\n";
            }
            
            if (strpos($config, "WP_HOME', 'http://localhost:8888/blackcnote'") !== false) {
                $this->results['wp-urls'] = '✅ Configured for Docker';
                echo "   ✅ WordPress URLs: Configured for Docker\n";
            } else {
                $this->errors['wp-urls'] = '❌ Not configured for Docker';
                echo "   ❌ WordPress URLs: Not configured for Docker\n";
            }
        } else {
            $this->errors['wp-config'] = '❌ Missing';
            echo "   ❌ wp-config.php: Missing\n";
        }
    }

    private function testDatabaseConnection(): void {
        echo "\n3. Testing Database Connection...\n";
        
        try {
            $pdo = new PDO(
                'mysql:host=localhost;port=3306;dbname=blackcnote',
                'root',
                'blackcnote_password',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            $this->results['database'] = '✅ Connected';
            echo "   ✅ Database: Connected successfully\n";
            
            // Test WordPress tables
            $stmt = $pdo->query("SHOW TABLES LIKE 'wp_%'");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (count($tables) > 0) {
                $this->results['wp-tables'] = '✅ WordPress tables exist';
                echo "   ✅ WordPress tables: " . count($tables) . " tables found\n";
            } else {
                $this->warnings['wp-tables'] = '⚠️ No WordPress tables found';
                echo "   ⚠️ WordPress tables: No tables found (may need installation)\n";
            }
            
        } catch (PDOException $e) {
            $this->errors['database'] = '❌ Connection failed: ' . $e->getMessage();
            echo "   ❌ Database: Connection failed\n";
        }
    }

    private function testRedisConnection(): void {
        echo "\n4. Testing Redis Connection...\n";
        
        if (extension_loaded('redis')) {
            try {
                $redis = new Redis();
                $redis->connect('localhost', 6379);
                
                if ($redis->ping() === '+PONG') {
                    $this->results['redis'] = '✅ Connected';
                    echo "   ✅ Redis: Connected successfully\n";
                } else {
                    $this->errors['redis'] = '❌ Ping failed';
                    echo "   ❌ Redis: Ping failed\n";
                }
            } catch (Exception $e) {
                $this->errors['redis'] = '❌ Connection failed: ' . $e->getMessage();
                echo "   ❌ Redis: Connection failed\n";
            }
        } else {
            $this->warnings['redis'] = '⚠️ Redis extension not loaded';
            echo "   ⚠️ Redis: Extension not loaded\n";
        }
    }

    private function testFileSystem(): void {
        echo "\n5. Testing File System...\n";
        
        $directories = [
            'blackcnote/wp-content/themes/blackcnote',
            'blackcnote/wp-content/plugins/blackcnote-hyiplab',
            'blackcnote/wp-content/uploads',
            'react-app/src',
            'config/nginx'
        ];
        
        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $this->results[$dir] = '✅ Exists';
                echo "   ✅ $dir: Exists\n";
            } else {
                $this->errors[$dir] = '❌ Missing';
                echo "   ❌ $dir: Missing\n";
            }
        }
        
        // Test file permissions
        if (is_writable('blackcnote/wp-content/uploads')) {
            $this->results['uploads-writable'] = '✅ Writable';
            echo "   ✅ Uploads directory: Writable\n";
        } else {
            $this->warnings['uploads-writable'] = '⚠️ Not writable';
            echo "   ⚠️ Uploads directory: Not writable\n";
        }
    }

    private function testNetworkConnectivity(): void {
        echo "\n6. Testing Network Connectivity...\n";
        
        $ports = [
            8888 => 'Nginx Proxy',
            5174 => 'React Dev Server',
            8080 => 'PHPMyAdmin',
            8025 => 'MailHog',
            8081 => 'Redis Commander',
            3306 => 'MySQL',
            6379 => 'Redis'
        ];
        
        foreach ($ports as $port => $service) {
            $connection = @fsockopen('localhost', $port, $errno, $errstr, 2);
            
            if ($connection) {
                $this->results["port-$port"] = '✅ Open';
                echo "   ✅ Port $port ($service): Open\n";
                fclose($connection);
            } else {
                $this->errors["port-$port"] = '❌ Closed';
                echo "   ❌ Port $port ($service): Closed\n";
            }
        }
    }

    private function testDevelopmentTools(): void {
        echo "\n7. Testing Development Tools...\n";
        
        // Test Docker Compose
        $output = shell_exec('docker-compose --version 2>&1');
        if (strpos($output, 'Docker Compose') !== false) {
            $this->results['docker-compose'] = '✅ Available';
            echo "   ✅ Docker Compose: Available\n";
        } else {
            $this->errors['docker-compose'] = '❌ Not available';
            echo "   ❌ Docker Compose: Not available\n";
        }
        
        // Test Docker
        $output = shell_exec('docker --version 2>&1');
        if (strpos($output, 'Docker version') !== false) {
            $this->results['docker'] = '✅ Available';
            echo "   ✅ Docker: Available\n";
        } else {
            $this->errors['docker'] = '❌ Not available';
            echo "   ❌ Docker: Not available\n";
        }
        
        // Test file watching
        if (file_exists('logs/file-changes.log')) {
            $this->results['file-watcher'] = '✅ Active';
            echo "   ✅ File Watcher: Active\n";
        } else {
            $this->warnings['file-watcher'] = '⚠️ Not active';
            echo "   ⚠️ File Watcher: Not active\n";
        }
    }

    private function testSecuritySettings(): void {
        echo "\n8. Testing Security Settings...\n";
        
        $config = file_get_contents('blackcnote/wp-config.php');
        
        $securityChecks = [
            'DISALLOW_FILE_EDIT' => 'File editing disabled',
            'DISALLOW_UNFILTERED_HTML' => 'Unfiltered HTML disabled',
            'WP_DEBUG' => 'Debug mode enabled (development)',
            'WP_DEBUG_LOG' => 'Debug logging enabled'
        ];
        
        foreach ($securityChecks as $constant => $description) {
            if (strpos($config, "define('$constant', true)") !== false) {
                $this->results[$constant] = '✅ Enabled';
                echo "   ✅ $description: Enabled\n";
            } else {
                $this->warnings[$constant] = '⚠️ Not enabled';
                echo "   ⚠️ $description: Not enabled\n";
            }
        }
    }

    private function displayResults(): void {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "TEST RESULTS SUMMARY\n";
        echo str_repeat("=", 50) . "\n\n";
        
        $totalTests = count($this->results) + count($this->errors) + count($this->warnings);
        $passedTests = count($this->results);
        $failedTests = count($this->errors);
        $warningTests = count($this->warnings);
        
        echo "Total Tests: $totalTests\n";
        echo "✅ Passed: $passedTests\n";
        echo "❌ Failed: $failedTests\n";
        echo "⚠️ Warnings: $warningTests\n\n";
        
        if ($failedTests === 0) {
            echo "🎉 All critical tests passed! Docker environment is ready.\n\n";
        } else {
            echo "⚠️ Some tests failed. Please check the errors above.\n\n";
        }
        
        if (count($this->errors) > 0) {
            echo "❌ ERRORS:\n";
            foreach ($this->errors as $test => $message) {
                echo "   - $test: $message\n";
            }
            echo "\n";
        }
        
        if (count($this->warnings) > 0) {
            echo "⚠️ WARNINGS:\n";
            foreach ($this->warnings as $test => $message) {
                echo "   - $test: $message\n";
            }
            echo "\n";
        }
        
        echo "🌐 Access URLs:\n";
        echo "   WordPress: http://localhost:8888/blackcnote\n";
        echo "   React App: http://localhost:5174\n";
        echo "   PHPMyAdmin: http://localhost:8080\n";
        echo "   MailHog: http://localhost:8025\n";
        echo "   Redis Commander: http://localhost:8081\n\n";
        
        echo "📋 Useful Commands:\n";
        echo "   View logs: docker-compose logs -f\n";
        echo "   Stop services: docker-compose down\n";
        echo "   Restart services: docker-compose restart\n";
        echo "   Update containers: docker-compose pull && docker-compose up -d\n";
    }
}

// Run the tests
$tester = new DockerEnvironmentTester();
$tester->runAllTests(); 