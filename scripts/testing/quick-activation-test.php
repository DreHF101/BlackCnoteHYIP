<?php
/**
 * Quick BlackCnote System Activation Test
 * 
 * This script tests basic system activation without loading WordPress
 * to avoid header and environment issues.
 */

declare(strict_types=1);

// Test results tracking
$test_results = [
    'total' => 0,
    'passed' => 0,
    'failed' => 0,
    'warnings' => 0
];

function add_test_result($test_name, $passed, $message, $details = '') {
    global $test_results;
    $test_results['total']++;
    
    if ($passed) {
        $test_results['passed']++;
        echo "[PASS] $test_name - $message\n";
    } else {
        $test_results['failed']++;
        echo "[FAIL] $test_name - $message\n";
    }
    
    if ($details) {
        echo "   Details: $details\n";
    }
}

function add_test_warning($test_name, $message, $details = '') {
    global $test_results;
    $test_results['warnings']++;
    echo "[WARN] $test_name - $message\n";
    
    if ($details) {
        echo "   Details: $details\n";
    }
}

echo "QUICK BLACKCNOTE SYSTEM ACTIVATION TEST\n";
echo "=======================================\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Canonical Paths
echo "TESTING CANONICAL PATHS\n";
echo "----------------------\n";

$canonical_paths = [
    'ProjectRoot' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote',
    'WordPress' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote',
    'Theme' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote',
    'ReactApp' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app',
    'Scripts' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\scripts'
];

foreach ($canonical_paths as $name => $path) {
    $exists = is_dir($path);
    add_test_result(
        "Canonical Path: $name",
        $exists,
        "Path verification",
        "Path: $path"
    );
}

// Test 2: WordPress Files
echo "\nTESTING WORDPRESS FILES\n";
echo "----------------------\n";

$wp_files = [
    'wp-config.php',
    'wp-load.php',
    'wp-admin/index.php',
    'wp-includes/functions.php'
];

foreach ($wp_files as $file) {
    $file_path = $canonical_paths['WordPress'] . '/' . $file;
    $exists = file_exists($file_path);
    add_test_result(
        "WordPress File: $file",
        $exists,
        "WordPress file verification",
        "Path: $file_path"
    );
}

// Test 3: Theme Files
echo "\nTESTING THEME FILES\n";
echo "------------------\n";

$theme_files = [
    'style.css',
    'index.php',
    'functions.php',
    'header.php',
    'footer.php',
    'single.php',
    'page.php'
];

foreach ($theme_files as $file) {
    $file_path = $canonical_paths['Theme'] . '/' . $file;
    $exists = file_exists($file_path);
    add_test_result(
        "Theme File: $file",
        $exists,
        "Theme file verification",
        "Path: $file_path"
    );
}

// Test 4: Plugin Directories
echo "\nTESTING PLUGIN DIRECTORIES\n";
echo "-------------------------\n";

$plugins_dir = $canonical_paths['WordPress'] . '/wp-content/plugins';
$plugin_dirs = [
    'blackcnote-debug-system',
    'hyiplab',
    'full-content-checker'
];

foreach ($plugin_dirs as $plugin) {
    $plugin_path = $plugins_dir . '/' . $plugin;
    $exists = is_dir($plugin_path);
    add_test_result(
        "Plugin Directory: $plugin",
        $exists,
        "Plugin directory verification",
        "Path: $plugin_path"
    );
}

// Test 5: React App Files
echo "\nTESTING REACT APP FILES\n";
echo "----------------------\n";

$react_files = [
    'package.json',
    'src/App.tsx',
    'src/main.tsx',
    'index.html',
    'vite.config.ts'
];

foreach ($react_files as $file) {
    $file_path = $canonical_paths['ReactApp'] . '/' . $file;
    $exists = file_exists($file_path);
    add_test_result(
        "React File: $file",
        $exists,
        "React file verification",
        "Path: $file_path"
    );
}

// Test 6: Service Connectivity
echo "\nTESTING SERVICE CONNECTIVITY\n";
echo "---------------------------\n";

$services = [
    'WordPress' => 'http://localhost:8888',
    'ReactApp' => 'http://localhost:5174',
    'phpMyAdmin' => 'http://localhost:8080',
    'RedisCommander' => 'http://localhost:8081',
    'MailHog' => 'http://localhost:8025',
    'Browsersync' => 'http://localhost:3000',
    'DevTools' => 'http://localhost:9229'
];

foreach ($services as $name => $url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];
    
    if ($response !== false) {
        $status_line = $headers[0] ?? '';
        if (strpos($status_line, '200') !== false || strpos($status_line, '302') !== false) {
            add_test_result(
                "Service: $name",
                true,
                "Service is accessible",
                "URL: $url"
            );
        } else {
            add_test_warning(
                "Service: $name",
                "Service responded with non-success status",
                "URL: $url, Status: $status_line"
            );
        }
    } else {
        add_test_result(
            "Service: $name",
            false,
            "Service is not accessible",
            "URL: $url"
        );
    }
}

// Test 7: Docker Containers
echo "\nTESTING DOCKER CONTAINERS\n";
echo "------------------------\n";

// Check if Docker is available
$docker_output = shell_exec('docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}" 2>&1');
if ($docker_output && strpos($docker_output, 'blackcnote') !== false) {
    add_test_result(
        "Docker Containers",
        true,
        "BlackCnote containers are running",
        "Found BlackCnote containers"
    );
    
    // Show container status
    $lines = explode("\n", trim($docker_output));
    foreach ($lines as $line) {
        if (trim($line) && strpos($line, 'NAMES') === false) {
            echo "   $line\n";
        }
    }
} else {
    add_test_result(
        "Docker Containers",
        false,
        "No BlackCnote containers found",
        "Docker output: " . ($docker_output ?: 'No output')
    );
}

// Test 8: File Permissions
echo "\nTESTING FILE PERMISSIONS\n";
echo "-----------------------\n";

$critical_files = [
    $canonical_paths['WordPress'] . '/wp-config.php',
    $canonical_paths['Theme'] . '/functions.php',
    $canonical_paths['ReactApp'] . '/package.json'
];

foreach ($critical_files as $file) {
    if (file_exists($file)) {
        $readable = is_readable($file);
        add_test_result(
            "File Permissions: " . basename($file),
            $readable,
            "File is readable",
            "Path: $file"
        );
    }
}

// Test 9: Configuration Files
echo "\nTESTING CONFIGURATION FILES\n";
echo "--------------------------\n";

$config_files = [
    'docker-compose.yml',
    'config/docker/blackcnote-wordpress.conf',
    'scripts/automate-docker-startup.bat'
];

foreach ($config_files as $file) {
    $file_path = $canonical_paths['ProjectRoot'] . '/' . $file;
    $exists = file_exists($file_path);
    add_test_result(
        "Config File: $file",
        $exists,
        "Configuration file verification",
        "Path: $file_path"
    );
}

// Generate Test Summary
echo "\nTEST SUMMARY\n";
echo "============\n";
echo "Total Tests: {$test_results['total']}\n";
echo "PASSED: {$test_results['passed']}\n";
echo "FAILED: {$test_results['failed']}\n";
echo "WARNINGS: {$test_results['warnings']}\n";

$success_rate = $test_results['total'] > 0 ? round(($test_results['passed'] / $test_results['total']) * 100, 2) : 0;
echo "Success Rate: {$success_rate}%\n";

// Overall status
if ($test_results['failed'] === 0 && $success_rate >= 90) {
    echo "\nBLACKCNOTE STATUS: FULLY OPERATIONAL\n";
    echo "All critical components are activated and functioning properly.\n";
} elseif ($test_results['failed'] === 0) {
    echo "\nBLACKCNOTE STATUS: OPERATIONAL WITH WARNINGS\n";
    echo "System is operational but some components have warnings.\n";
} else {
    echo "\nBLACKCNOTE STATUS: ISSUES DETECTED\n";
    echo "Some critical components failed activation tests.\n";
}

// Recommendations
echo "\nRecommendations:\n";
if ($test_results['failed'] > 0) {
    echo "Review failed tests and fix issues\n";
}
if ($test_results['warnings'] > 0) {
    echo "Address warnings to improve system reliability\n";
}
echo "Run this test regularly to monitor system health\n";
echo "Check logs for detailed error information\n";

// Save test results
$test_report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'results' => $test_results,
    'success_rate' => $success_rate,
    'canonical_paths' => $canonical_paths,
    'services' => $services
];

$reports_dir = $canonical_paths['ProjectRoot'] . '/reports';
if (!is_dir($reports_dir)) {
    mkdir($reports_dir, 0755, true);
}

$report_file = $reports_dir . '/quick-activation-test-' . date('Y-m-d-H-i-s') . '.json';
file_put_contents($report_file, json_encode($test_report, JSON_PRETTY_PRINT));

echo "\nTest report saved to: $report_file\n";
echo "\nQuick BlackCnote activation test completed!\n"; 