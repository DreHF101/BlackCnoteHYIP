<?php
/**
 * Simple BlackCnote Server Stress Test
 */

echo "🚀 BlackCnote Simple Stress Test\n";
echo "================================\n\n";

$urls = [
    'WordPress Homepage' => 'http://wordpress',
    'React App' => 'http://react-app:5176',
    'WordPress API' => 'http://wordpress/wp-json',
    'phpMyAdmin' => 'http://localhost:8080'
];

$test_count = 20;

foreach ($urls as $name => $url) {
    echo "Testing: $name\n";
    
    $times = [];
    $success = 0;
    
    for ($i = 0; $i < $test_count; $i++) {
        $start = microtime(true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $end = microtime(true);
        $time = ($end - $start) * 1000; // Convert to milliseconds
        
        if ($result !== false && $http_code >= 200 && $http_code < 400) {
            $success++;
            $times[] = $time;
        }
        
        usleep(100000); // 0.1 second delay
    }
    
    $success_rate = ($success / $test_count) * 100;
    $avg_time = !empty($times) ? array_sum($times) / count($times) : 0;
    $min_time = !empty($times) ? min($times) : 0;
    $max_time = !empty($times) ? max($times) : 0;
    
    echo "  Success Rate: " . number_format($success_rate, 1) . "%\n";
    echo "  Avg Time: " . number_format($avg_time, 2) . "ms\n";
    echo "  Min/Max: " . number_format($min_time, 2) . "ms / " . number_format($max_time, 2) . "ms\n";
    
    if ($success_rate >= 95 && $avg_time < 1000) {
        echo "  Status: ✅ EXCELLENT\n";
    } elseif ($success_rate >= 90 && $avg_time < 3000) {
        echo "  Status: ✅ GOOD\n";
    } elseif ($success_rate >= 80) {
        echo "  Status: ⚠️  ACCEPTABLE\n";
    } else {
        echo "  Status: ❌ POOR\n";
    }
    echo "\n";
}

echo "Stress test completed!\n";
?> 