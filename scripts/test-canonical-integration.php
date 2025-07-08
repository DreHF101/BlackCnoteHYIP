<?php
/**
 * BlackCnote Canonical Integration Test
 * Tests all canonical pathways and service connectivity
 */

echo "=== BlackCnote Canonical Integration Test ===\n\n";

// Test 1: Canonical Paths Verification
echo "1. Testing Canonical Paths:\n";
$canonical_paths = [
    'Project Root' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote',
    'WordPress Installation' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote',
    'WordPress Content' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content',
    'Theme Directory' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote',
    'React App' => 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app'
];

foreach ($canonical_paths as $name => $path) {
    if (is_dir($path)) {
        echo "   ✅ {$name}: {$path}\n";
    } else {
        echo "   ❌ {$name}: {$path} (NOT FOUND)\n";
    }
}

// Test 2: Canonical Service URLs
echo "\n2. Testing Canonical Service URLs:\n";
$canonical_urls = [
    'WordPress Frontend' => 'http://localhost:8888',
    'WordPress Admin' => 'http://localhost:8888/wp-admin/',
    'React App (CANONICAL)' => 'http://localhost:5174',
    'phpMyAdmin' => 'http://localhost:8080',
    'Redis Commander' => 'http://localhost:8081',
    'MailHog' => 'http://localhost:8025',
    'Browsersync' => 'http://localhost:3000',
    'Dev Tools' => 'http://localhost:9229'
];

foreach ($canonical_urls as $name => $url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
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
        echo "   ✅ {$name}: {$url} ({$duration}ms)\n";
    } else {
        echo "   ❌ {$name}: {$url} (FAILED - {$duration}ms)\n";
    }
}

// Test 3: Docker Container Status
echo "\n3. Testing Docker Container Status:\n";
$docker_containers = [
    'blackcnote-wordpress',
    'blackcnote-react',
    'blackcnote-mysql',
    'blackcnote-redis',
    'blackcnote-phpmyadmin',
    'blackcnote-redis-commander',
    'blackcnote-mailhog',
    'blackcnote-browsersync',
    'blackcnote-dev-tools'
];

foreach ($docker_containers as $container) {
    $output = shell_exec("docker ps --filter \"name={$container}\" --format \"{{.Status}}\" 2>&1");
    if (trim($output) && strpos($output, 'Up') !== false) {
        echo "   ✅ {$container}: Running\n";
    } else {
        echo "   ❌ {$container}: Not Running\n";
    }
}

// Test 4: React App Specific Test
echo "\n4. Testing React App Integration:\n";

// Test React dev server directly
$react_url = 'http://localhost:5174';
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'method' => 'GET',
        'header' => [
            'User-Agent: BlackCnote-Test/1.0',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        ]
    ]
]);

$response = @file_get_contents($react_url, false, $context);
if ($response !== false) {
    if (strpos($response, 'BlackCnote') !== false || strpos($response, 'React') !== false || strpos($response, 'Vite') !== false) {
        echo "   ✅ React App: Accessible and serving content\n";
    } else {
        echo "   ⚠️ React App: Accessible but content not recognized\n";
    }
} else {
    echo "   ❌ React App: Not accessible\n";
}

// Test WordPress React integration
$wp_url = 'http://localhost:8888';
$wp_response = @file_get_contents($wp_url, false, $context);
if ($wp_response !== false) {
    if (strpos($wp_response, 'localhost:5174') !== false) {
        echo "   ✅ WordPress React Integration: Using canonical port 5174\n";
    } elseif (strpos($wp_response, 'localhost:5176') !== false) {
        echo "   ❌ WordPress React Integration: Still using deprecated port 5176\n";
    } else {
        echo "   ⚠️ WordPress React Integration: No React dev server detected\n";
    }
} else {
    echo "   ❌ WordPress: Not accessible\n";
}

// Test 5: Canonical Configuration Files
echo "\n5. Testing Canonical Configuration Files:\n";
$config_files = [
    'Docker Compose' => 'docker-compose.yml',
    'Vite Config' => 'react-app/vite.config.ts',
    'WordPress Functions' => 'blackcnote/wp-content/themes/blackcnote/functions.php',
    'Canonical Paths Doc' => 'BLACKCNOTE-CANONICAL-PATHS.md'
];

foreach ($config_files as $name => $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, '5174') !== false) {
            echo "   ✅ {$name}: Uses canonical port 5174\n";
        } elseif (strpos($content, '5176') !== false) {
            echo "   ❌ {$name}: Still references deprecated port 5176\n";
        } else {
            echo "   ⚠️ {$name}: No port reference found\n";
        }
    } else {
        echo "   ❌ {$name}: File not found\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "Canonical port 5174 should be used for React app.\n";
echo "All services should be accessible on their canonical ports.\n";
echo "All paths should follow the canonical structure.\n"; 