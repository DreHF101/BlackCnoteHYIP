<?php
/**
 * Simple Debug Monitor Test
 * Tests services without requiring database connection
 */

echo "🔧 Simple Debug Monitor Test\n";
echo "===========================\n\n";

// Test configuration
$services = [
    'Browsersync' => 'http://localhost:3000',
    'Vite Dev Server' => 'http://localhost:5174',
    'WordPress' => 'http://localhost:8888'
];

// Utility function to test service
function test_service($url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'method' => 'GET'
        ]
    ]);
    
    $result = @file_get_contents($url, false, $context);
    if ($result !== false) {
        return ['status' => 'running', 'response' => 'OK'];
    } else {
        $error = error_get_last();
        return ['status' => 'error', 'response' => $error['message'] ?? 'Connection failed'];
    }
}

// Test each service
echo "Testing Services:\n";
echo "=================\n";

foreach ($services as $name => $url) {
    echo "Testing $name ($url)... ";
    $result = test_service($url);
    
    if ($result['status'] === 'running') {
        echo "✅ RUNNING\n";
    } else {
        echo "❌ NOT RUNNING\n";
        echo "   Error: {$result['response']}\n";
    }
}

echo "\nDebug Monitor Issues Analysis:\n";
echo "==============================\n";

// Check current URL context
$current_url = $_SERVER['HTTP_HOST'] ?? 'unknown';
$current_port = $_SERVER['SERVER_PORT'] ?? 'unknown';

echo "Current URL: $current_url\n";
echo "Current Port: $current_port\n";

// Analyze potential issues
$issues = [];

// 1. Browsersync detection
$browsersync_result = test_service('http://localhost:3000');
if ($browsersync_result['status'] !== 'running') {
    $issues[] = "Browsersync Not Running: Live editing may not work. Run \"npm run dev:full\" in react-app directory to start development server.";
}

// 2. React Router basename conflict
if ($current_port !== '3000' && $current_port !== '3001') {
    $issues[] = "React Router Basename Conflict: React Router basename may not match current URL structure.";
}

// 3. CORS issues
if ($current_port !== '3000' && $current_port !== '3001') {
    $issues[] = "Potential CORS Issue: Running on different port may cause CORS issues with API calls.";
}

// 4. HYIPLab API
$hyiplab_result = test_service('http://localhost:8888/wp-json/hyiplab/v1/status');
if ($hyiplab_result['status'] !== 'running') {
    $issues[] = "Hyiplab API Error: Cannot connect to Hyiplab plugin API.";
}

echo "\nDetected Issues:\n";
echo "================\n";

if (empty($issues)) {
    echo "✅ No issues detected! All services are running correctly.\n";
} else {
    foreach ($issues as $index => $issue) {
        echo ($index + 1) . ". $issue\n";
    }
}

echo "\nRecommendations:\n";
echo "================\n";

if ($browsersync_result['status'] !== 'running') {
    echo "1. Start Browsersync: cd react-app && npm run dev:full\n";
}

if ($current_port !== '3000') {
    echo "2. Access via Browsersync: http://localhost:3000\n";
    echo "3. This will resolve CORS and React Router issues\n";
}

if ($hyiplab_result['status'] !== 'running') {
    echo "4. Check HYIPLab plugin activation in WordPress admin\n";
}

echo "\nService URLs:\n";
echo "=============\n";
echo "• Browsersync (Live Editing): http://localhost:3000\n";
echo "• Vite Dev Server: http://localhost:5174\n";
echo "• WordPress: http://localhost:8888\n";

echo "\n�� Test Complete!\n"; 