<?php
/**
 * Quick BlackCnote Performance Check
 */

echo "⚡ BlackCnote Quick Performance Check\n";
echo "====================================\n\n";

$urls = [
    'WordPress Homepage' => 'http://localhost:8888',
    'React App' => 'http://localhost:5174',
    'WordPress API' => 'http://localhost:8888/wp-json',
    'phpMyAdmin' => 'http://localhost:8080'
];

foreach ($urls as $name => $url) {
    echo "Testing: $name\n";
    
    $times = [];
    $success = 0;
    
    // Test 5 times
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
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
    }
    
    $success_rate = ($success / 5) * 100;
    $avg_time = !empty($times) ? array_sum($times) / count($times) : 0;
    
    echo "  Success Rate: " . number_format($success_rate, 1) . "%\n";
    echo "  Avg Time: " . number_format($avg_time, 2) . "ms\n";
    
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

echo "Performance check completed!\n";
?> 