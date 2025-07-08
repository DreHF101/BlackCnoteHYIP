<?php
/**
 * Test React Dev Server Accessibility
 */

echo "=== Testing React Dev Server Accessibility ===\n\n";

// Test URLs to try
$test_urls = [
    'http://react-app:5176',
    'http://localhost:5176',
    'http://blackcnote-react:5176',
    'http://host.docker.internal:5176'
];

foreach ($test_urls as $url) {
    echo "Testing: {$url}\n";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 3,
            'method' => 'GET',
            'header' => [
                'User-Agent: BlackCnote-Test/1.0'
            ]
        ]
    ]);
    
    $start_time = microtime(true);
    $response = @file_get_contents($url, false, $context);
    $end_time = microtime(true);
    $duration = round(($end_time - $start_time) * 1000, 2);
    
    if ($response !== false) {
        echo "  ✓ SUCCESS - Response time: {$duration}ms\n";
        if (strpos($response, 'BlackCnote') !== false) {
            echo "  ✓ React app content found\n";
        } else {
            echo "  ⚠ React app content not found\n";
        }
        echo "  Response length: " . strlen($response) . " bytes\n";
    } else {
        echo "  ✗ FAILED - {$duration}ms\n";
        $error = error_get_last();
        if ($error) {
            echo "  Error: " . $error['message'] . "\n";
        }
    }
    echo "\n";
}

echo "=== Test Complete ===\n"; 