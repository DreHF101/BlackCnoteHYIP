#!/usr/bin/env php
<?php
/**
 * BlackCnote Metrics Exporter
 * Exports debug system metrics for Prometheus monitoring
 */

declare(strict_types=1);

require_once __DIR__ . '/../hyiplab/app/Log/BlackCnoteDebugSystem.php';

use BlackCnote\Log\BlackCnoteDebugSystem;

class BlackCnoteMetricsExporter {
    private $debug;
    private $log_file;
    private $metrics = [];
    
    public function __construct($log_file) {
        $this->log_file = $log_file;
        $this->debug = new BlackCnoteDebugSystem([
            'log_file' => dirname(__DIR__) . '/logs/metrics-exporter.log'
        ]);
    }
    
    public function collectMetrics() {
        $this->metrics = [
            'blackcnote_debug_log_entries_total' => 0,
            'blackcnote_debug_errors_total' => 0,
            'blackcnote_debug_warnings_total' => 0,
            'blackcnote_debug_info_total' => 0,
            'blackcnote_debug_debug_total' => 0,
            'blackcnote_debug_system_total' => 0,
            'blackcnote_file_changes_total' => 0,
            'blackcnote_docker_containers_running' => 0,
            'blackcnote_memory_usage_bytes' => 0,
            'blackcnote_disk_free_bytes' => 0,
            'blackcnote_daemon_uptime_seconds' => 0
        ];
        
        if (!file_exists($this->log_file)) {
            return $this->metrics;
        }
        
        $lines = file($this->log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->metrics['blackcnote_debug_log_entries_total'] = count($lines);
        
        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if (!$entry) continue;
            
            $level = strtolower($entry['level'] ?? 'unknown');
            $message = $entry['message'] ?? '';
            $context = $entry['context'] ?? [];
            
            // Count by log level
            switch ($level) {
                case 'error':
                    $this->metrics['blackcnote_debug_errors_total']++;
                    break;
                case 'warning':
                    $this->metrics['blackcnote_debug_warnings_total']++;
                    break;
                case 'info':
                    $this->metrics['blackcnote_debug_info_total']++;
                    break;
                case 'debug':
                    $this->metrics['blackcnote_debug_debug_total']++;
                    break;
                case 'system':
                    $this->metrics['blackcnote_debug_system_total']++;
                    break;
            }
            
            // Extract specific metrics from context
            if (isset($context['memory_usage'])) {
                $this->metrics['blackcnote_memory_usage_bytes'] = $context['memory_usage'];
            }
            
            if (isset($context['uptime'])) {
                $this->metrics['blackcnote_daemon_uptime_seconds'] = $context['uptime'];
            }
            
            if (isset($context['total_containers'])) {
                $this->metrics['blackcnote_docker_containers_running'] = $context['total_containers'];
            }
            
            if (isset($context['changes'])) {
                $this->metrics['blackcnote_file_changes_total'] += count($context['changes']);
            }
            
            if (isset($context['disk_free'])) {
                $this->metrics['blackcnote_disk_free_bytes'] = $context['disk_free'];
            }
        }
        
        return $this->metrics;
    }
    
    public function exportPrometheusFormat() {
        $metrics = $this->collectMetrics();
        $output = "# BlackCnote Debug System Metrics\n";
        $output .= "# Generated at " . date('c') . "\n\n";
        
        foreach ($metrics as $metric_name => $value) {
            $output .= sprintf("%s %s\n", $metric_name, $value);
        }
        
        return $output;
    }
    
    public function serveMetrics($port = 9091) {
        if (php_sapi_name() !== 'cli') {
            http_response_code(500);
            echo "This script must be run from CLI\n";
            return;
        }
        
        echo "Starting BlackCnote Metrics Exporter on port $port...\n";
        echo "Metrics available at: http://localhost:$port/metrics\n";
        echo "Landing page at: http://localhost:$port/\n";
        echo "Press Ctrl+C to stop\n\n";
        
        $server = stream_socket_server("tcp://0.0.0.0:$port", $errno, $errstr);
        if (!$server) {
            echo "Failed to start server: $errstr ($errno)\n";
            return;
        }
        
        // Set server socket to non-blocking
        stream_set_blocking($server, false);
        
        while (true) {
            // Use stream_select for non-blocking accept
            $read = [$server];
            $write = [];
            $except = [];
            
            if (stream_select($read, $write, $except, 1) > 0) {
                $client = @stream_socket_accept($server, 30);
                if ($client === false || $client === null) {
                    $this->log('Socket connection failed: ' . error_get_last()['message'], 'ERROR');
                    continue;
                }
                
                // Validate connection before processing
                if (!is_resource($client)) {
                    $this->log('Invalid connection resource', 'ERROR');
                    continue;
                }
                
                // Set socket timeout
                stream_set_timeout($client, 30);
                
                // Read request with timeout
                $request = fgets($client);
                if ($request === false) {
                    $this->log('Failed to read request from socket', 'ERROR');
                    fclose($client);
                    continue;
                }
                
                // Validate request
                if (empty($request)) {
                    $this->log('Empty request received', 'WARNING');
                    fclose($client);
                    continue;
                }
                
                try {
                    if (strpos($request, 'GET /metrics') !== false) {
                        $response = "HTTP/1.1 200 OK\r\n";
                        $response .= "Content-Type: text/plain; version=0.0.4; charset=utf-8\r\n";
                        $response .= "Content-Length: " . strlen($this->exportPrometheusFormat()) . "\r\n";
                        $response .= "\r\n";
                        $response .= $this->exportPrometheusFormat();
                    } elseif (strpos($request, 'GET /') !== false) {
                        // Landing page
                        $html = $this->generateLandingPage();
                        $response = "HTTP/1.1 200 OK\r\n";
                        $response .= "Content-Type: text/html; charset=utf-8\r\n";
                        $response .= "Content-Length: " . strlen($html) . "\r\n";
                        $response .= "\r\n";
                        $response .= $html;
                    } else {
                        $response = "HTTP/1.1 404 Not Found\r\n";
                        $response .= "Content-Type: text/plain\r\n";
                        $response .= "\r\n";
                        $response .= "404 - Not Found\n";
                        $response .= "Available endpoints:\n";
                        $response .= "- / (landing page)\n";
                        $response .= "- /metrics (Prometheus metrics)\n";
                    }
                    
                    fwrite($client, $response);
                } catch (Exception $e) {
                    $this->log('Error processing request: ' . $e->getMessage(), 'ERROR');
                } finally {
                    fclose($client);
                }
            }
            
            // Small delay to prevent CPU spinning
            usleep(100000); // 100ms
        }
        
        fclose($server);
    }
    
    private function generateLandingPage() {
        $metrics = $this->collectMetrics();
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlackCnote Metrics Exporter</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        .metric { background: #ecf0f1; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #3498db; }
        .metric-name { font-weight: bold; color: #2c3e50; }
        .metric-value { color: #e74c3c; font-size: 1.2em; }
        .endpoint { background: #2c3e50; color: white; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 3px; color: white; font-weight: bold; }
        .status.online { background: #27ae60; }
        .status.offline { background: #e74c3c; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖤 BlackCnote Metrics Exporter</h1>
        
        <div class="endpoint">
            <strong>Status:</strong> <span class="status online">ONLINE</span>
            <br><strong>Port:</strong> 9091
            <br><strong>Prometheus Endpoint:</strong> <a href="/metrics" style="color: #3498db;">/metrics</a>
        </div>
        
        <h2>📊 Current Metrics</h2>';
        
        foreach ($metrics as $name => $value) {
            $display_name = str_replace('_', ' ', ucfirst($name));
            $html .= "
        <div class='metric'>
            <div class='metric-name'>$display_name</div>
            <div class='metric-value'>$value</div>
        </div>";
        }
        
        $html .= '
        
        <h2>🔗 Available Endpoints</h2>
        <div class="endpoint">
            <strong>GET /</strong> - This landing page
        </div>
        <div class="endpoint">
            <strong>GET /metrics</strong> - Prometheus-formatted metrics
        </div>
        
        <h2>📈 Integration</h2>
        <p>Add this endpoint to your Prometheus configuration:</p>
        <div class="endpoint">
            <code>scrape_configs:<br>
&nbsp;&nbsp;- job_name: "blackcnote"<br>
&nbsp;&nbsp;&nbsp;&nbsp;static_configs:<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- targets: ["localhost:9091"]</code>
        </div>
        
        <footer style="margin-top: 40px; text-align: center; color: #7f8c8d;">
            <p>BlackCnote Debug System - Metrics Exporter</p>
            <p>Generated at ' . date('Y-m-d H:i:s T') . '</p>
        </footer>
    </div>
</body>
</html>';
        
        return $html;
    }
}

// CLI usage
if (php_sapi_name() === 'cli') {
    $exporter = new BlackCnoteMetricsExporter(dirname(__DIR__) . '/logs/blackcnote-debug.log');
    
    if (isset($argv[1]) && $argv[1] === '--serve') {
        $port = isset($argv[2]) ? (int)$argv[2] : 9091;
        $exporter->serveMetrics($port);
    } else {
        echo $exporter->exportPrometheusFormat();
    }
} 