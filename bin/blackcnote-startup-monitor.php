#!/usr/bin/env php
<?php
/**
 * BlackCnote Startup Monitor
 * Integrates with BlackCnote Debug System for 24/7 startup script monitoring
 * Provides real-time health checks and diagnostics for the entire BlackCnote project
 * 
 * ================================================
 * CANONICAL PATHWAYS - DO NOT CHANGE
 * Project Root: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
 * Theme Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote
 * WP-Content Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
 * Theme Files: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote
 * ================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../hyiplab/app/Log/BlackCnoteDebugSystem.php';

use BlackCnote\Log\BlackCnoteDebugSystem;

class BlackCnoteStartupMonitor {
    private $debug;
    private $base_path;
    private $blackcnote_path;
    private $wp_content_path;
    private $running = true;
    private $last_health_check = 0;
    private $health_check_interval = 300; // 5 minutes
    private $startup_script_path;
    private $docker_compose_path;
    private $services = [];
    private $containers = [];
    
    public function __construct($debug, $base_path) {
        $this->debug = $debug;
        $this->base_path = $base_path;
        $this->blackcnote_path = $base_path . '/blackcnote';
        $this->wp_content_path = $base_path . '/blackcnote/wp-content';
        $this->startup_script_path = $base_path . '/start-blackcnote-complete.ps1';
        $this->docker_compose_path = $base_path . '/docker-compose.yml';
        
        $this->initializeServices();
        $this->debug->log('BlackCnote Startup Monitor initialized', 'SYSTEM', [
            'base_path' => $this->base_path,
            'startup_script' => $this->startup_script_path,
            'docker_compose' => $this->docker_compose_path
        ]);
    }
    
    private function initializeServices() {
        $this->services = [
            'wordpress' => [
                'url' => 'http://localhost:8888',
                'name' => 'WordPress',
                'required' => true,
                'health_endpoint' => '/health'
            ],
            'react' => [
                'url' => 'http://localhost:5174',
                'name' => 'React App',
                'required' => true
            ],
            'phpmyadmin' => [
                'url' => 'http://localhost:8080',
                'name' => 'phpMyAdmin',
                'required' => true
            ],
            'redis_commander' => [
                'url' => 'http://localhost:8081',
                'name' => 'Redis Commander',
                'required' => false
            ],
            'mailhog' => [
                'url' => 'http://localhost:8025',
                'name' => 'MailHog',
                'required' => false
            ],
            'browsersync' => [
                'url' => 'http://localhost:3000',
                'name' => 'Browsersync',
                'required' => false
            ],
            'browsersync_ui' => [
                'url' => 'http://localhost:3001',
                'name' => 'Browsersync UI',
                'required' => false
            ],
            'dev_tools' => [
                'url' => 'http://localhost:9229',
                'name' => 'Dev Tools',
                'required' => false
            ],
            'metrics' => [
                'url' => 'http://localhost:9091',
                'name' => 'Metrics Exporter',
                'required' => false
            ]
        ];
        
        $this->containers = [
            'blackcnote_wordpress',
            'blackcnote_mysql',
            'blackcnote_redis',
            'blackcnote_react',
            'blackcnote_phpmyadmin',
            'blackcnote_mailhog',
            'blackcnote_redis_commander',
            'blackcnote_browsersync',
            'blackcnote_file_watcher',
            'blackcnote_dev_tools',
            'blackcnote_debug',
            'blackcnote_debug_exporter'
        ];
    }
    
    private function checkStartupScriptHealth() {
        $health = [
            'script_exists' => file_exists($this->startup_script_path),
            'script_readable' => is_readable($this->startup_script_path),
            'script_executable' => is_executable($this->startup_script_path),
            'last_modified' => file_exists($this->startup_script_path) ? filemtime($this->startup_script_path) : 0,
            'file_size' => file_exists($this->startup_script_path) ? filesize($this->startup_script_path) : 0
        ];
        
        if (!$health['script_exists']) {
            $this->debug->log('Startup script not found', 'ERROR', [
                'path' => $this->startup_script_path
            ]);
        }
        
        return $health;
    }
    
    private function checkDockerComposeHealth() {
        $health = [
            'file_exists' => file_exists($this->docker_compose_path),
            'file_readable' => is_readable($this->docker_compose_path),
            'last_modified' => file_exists($this->docker_compose_path) ? filemtime($this->docker_compose_path) : 0,
            'file_size' => file_exists($this->docker_compose_path) ? filesize($this->docker_compose_path) : 0,
            'docker_running' => false,
            'docker_compose_available' => false
        ];
        
        // Check if Docker is running
        $output = shell_exec('docker info 2>&1');
        $health['docker_running'] = strpos($output, 'Server Version') !== false;
        
        // Check if Docker Compose is available
        $output = shell_exec('docker-compose --version 2>&1');
        $health['docker_compose_available'] = strpos($output, 'docker-compose version') !== false;
        
        if (!$health['docker_running']) {
            $this->debug->log('Docker is not running', 'ERROR', [
                'docker_info_output' => $output
            ]);
        }
        
        if (!$health['docker_compose_available']) {
            $this->debug->log('Docker Compose not available', 'ERROR', [
                'docker_compose_output' => $output
            ]);
        }
        
        return $health;
    }
    
    private function checkServiceHealth() {
        $results = [];
        
        foreach ($this->services as $key => $service) {
            $result = [
                'service' => $service['name'],
                'url' => $service['url'],
                'required' => $service['required'],
                'status' => 'unknown',
                'response_time' => 0,
                'error' => null
            ];
            
            $start_time = microtime(true);
            
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 5,
                        'user_agent' => 'BlackCnote-StartupMonitor/1.0'
                    ]
                ]);
                
                $response = file_get_contents($service['url'], false, $context);
                $end_time = microtime(true);
                
                if ($response !== false) {
                    $result['status'] = 'healthy';
                    $result['response_time'] = round(($end_time - $start_time) * 1000, 2);
                } else {
                    $result['status'] = 'unhealthy';
                    $result['error'] = 'Failed to connect';
                }
            } catch (Exception $e) {
                $result['status'] = 'unhealthy';
                $result['error'] = $e->getMessage();
            }
            
            $results[$key] = $result;
            
            // Log unhealthy services
            if ($result['status'] === 'unhealthy' && $service['required']) {
                $this->debug->log('Required service unhealthy', 'ERROR', [
                    'service' => $service['name'],
                    'url' => $service['url'],
                    'error' => $result['error']
                ]);
            }
        }
        
        return $results;
    }
    
    private function checkContainerHealth() {
        $results = [];
        
        foreach ($this->containers as $container) {
            $result = [
                'container' => $container,
                'status' => 'unknown',
                'running' => false,
                'error' => null
            ];
            
            try {
                $output = shell_exec("docker ps --filter name=$container --format '{{.Names}}\t{{.Status}}\t{{.Ports}}' 2>&1");
                
                if ($output && trim($output)) {
                    $lines = explode("\n", trim($output));
                    foreach ($lines as $line) {
                        if (strpos($line, $container) !== false) {
                            $parts = explode("\t", $line);
                            $result['status'] = $parts[1] ?? 'unknown';
                            $result['running'] = strpos($result['status'], 'Up') !== false;
                            break;
                        }
                    }
                } else {
                    $result['status'] = 'not_running';
                    $result['running'] = false;
                }
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            
            $results[$container] = $result;
            
            // Log stopped containers
            if (!$result['running']) {
                $this->debug->log('Container not running', 'WARNING', [
                    'container' => $container,
                    'status' => $result['status']
                ]);
            }
        }
        
        return $results;
    }
    
    private function checkSystemResources() {
        $resources = [
            'memory' => [
                'total' => 0,
                'available' => 0,
                'used' => 0,
                'usage_percent' => 0
            ],
            'disk' => [
                'total' => 0,
                'free' => 0,
                'used' => 0,
                'usage_percent' => 0
            ],
            'cpu' => [
                'load_average' => [0, 0, 0]
            ]
        ];
        
        // Memory information
        if (function_exists('sys_getloadavg')) {
            $resources['cpu']['load_average'] = sys_getloadavg();
        }
        
        // Disk information
        $disk_free = disk_free_space($this->base_path);
        $disk_total = disk_total_space($this->base_path);
        
        if ($disk_free !== false && $disk_total !== false) {
            $resources['disk']['total'] = $disk_total;
            $resources['disk']['free'] = $disk_free;
            $resources['disk']['used'] = $disk_total - $disk_free;
            $resources['disk']['usage_percent'] = round(($resources['disk']['used'] / $disk_total) * 100, 2);
        }
        
        // Check for critical resource usage
        if ($resources['disk']['usage_percent'] > 90) {
            $this->debug->log('Critical disk usage', 'ERROR', [
                'usage_percent' => $resources['disk']['usage_percent'],
                'free_space' => $resources['disk']['free']
            ]);
        }
        
        return $resources;
    }
    
    private function generateHealthReport() {
        $report = [
            'timestamp' => time(),
            'startup_script' => $this->checkStartupScriptHealth(),
            'docker_compose' => $this->checkDockerComposeHealth(),
            'services' => $this->checkServiceHealth(),
            'containers' => $this->checkContainerHealth(),
            'system_resources' => $this->checkSystemResources(),
            'overall_health' => 'unknown'
        ];
        
        // Calculate overall health
        $critical_issues = 0;
        $warnings = 0;
        
        // Check startup script
        if (!$report['startup_script']['script_exists']) {
            $critical_issues++;
        }
        
        // Check Docker
        if (!$report['docker_compose']['docker_running']) {
            $critical_issues++;
        }
        
        // Check required services
        foreach ($report['services'] as $service) {
            if ($service['required'] && $service['status'] !== 'healthy') {
                $critical_issues++;
            } elseif (!$service['required'] && $service['status'] !== 'healthy') {
                $warnings++;
            }
        }
        
        // Check required containers
        foreach ($report['containers'] as $container) {
            if (strpos($container, 'wordpress') !== false || 
                strpos($container, 'mysql') !== false || 
                strpos($container, 'redis') !== false) {
                if (!$container['running']) {
                    $critical_issues++;
                }
            } elseif (!$container['running']) {
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
            'total_services' => count($report['services']),
            'total_containers' => count($report['containers'])
        ];
        
        return $report;
    }
    
    private function saveHealthReport($report) {
        $report_file = $this->base_path . '/logs/startup-monitor-health.json';
        $report_dir = dirname($report_file);
        
        if (!is_dir($report_dir)) {
            mkdir($report_dir, 0755, true);
        }
        
        file_put_contents($report_file, json_encode($report, JSON_PRETTY_PRINT));
        
        // Also save to debug system
        $this->debug->log('Health report generated', 'INFO', [
            'overall_health' => $report['overall_health'],
            'critical_issues' => $report['metrics']['critical_issues'],
            'warnings' => $report['metrics']['warnings']
        ]);
    }
    
    public function run() {
        $this->debug->log('BlackCnote Startup Monitor started', 'SYSTEM');
        
        while ($this->running) {
            $current_time = time();
            
            // Run health check every 5 minutes
            if ($current_time - $this->last_health_check >= $this->health_check_interval) {
                $this->debug->log('Running scheduled health check', 'INFO');
                
                $health_report = $this->generateHealthReport();
                $this->saveHealthReport($health_report);
                
                $this->last_health_check = $current_time;
                
                // Log health status
                $this->debug->log('Health check completed', 'INFO', [
                    'overall_health' => $health_report['overall_health'],
                    'critical_issues' => $health_report['metrics']['critical_issues'],
                    'warnings' => $health_report['metrics']['warnings']
                ]);
            }
            
            sleep(60); // Check every minute
        }
    }
    
    public function stop() {
        $this->running = false;
        $this->debug->log('BlackCnote Startup Monitor stopped', 'SYSTEM');
    }
    
    public function getHealthReport() {
        return $this->generateHealthReport();
    }
}

// Configuration
$config = [
    'base_path' => dirname(__DIR__),
    'blackcnote_path' => dirname(__DIR__) . '/blackcnote',
    'wp_content_path' => dirname(__DIR__) . '/blackcnote/wp-content',
    'log_file' => dirname(__DIR__) . '/logs/blackcnote-startup-monitor.log',
    'debug_enabled' => true,
    'log_level' => 'ALL',
];

$debug = new BlackCnoteDebugSystem($config);
$monitor = new BlackCnoteStartupMonitor($debug, $config['base_path']);

// Handle command line arguments
if (isset($argv[1])) {
    switch ($argv[1]) {
        case '--health':
            $report = $monitor->getHealthReport();
            echo json_encode($report, JSON_PRETTY_PRINT);
            break;
            
        case '--status':
            $report = $monitor->getHealthReport();
            echo "Overall Health: " . $report['overall_health'] . "\n";
            echo "Critical Issues: " . $report['metrics']['critical_issues'] . "\n";
            echo "Warnings: " . $report['metrics']['warnings'] . "\n";
            break;
            
        case '--daemon':
            $monitor->run();
            break;
            
        default:
            echo "Usage: php blackcnote-startup-monitor.php [--health|--status|--daemon]\n";
            break;
    }
} else {
    // Run as daemon by default
    $monitor->run();
} 