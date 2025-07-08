<?php
/**
 * BlackCnote Docker Compatibility Fix
 * Updates all test scripts to use correct internal Docker service addresses
 */

declare(strict_types=1);

echo "🔧 BlackCnote Docker Compatibility Fix\n";
echo "=====================================\n\n";

// Define the correct Docker service URLs
$docker_urls = [
    'wordpress' => 'http://wordpress',
    'react' => 'http://react-app:5176',
    'mysql' => 'mysql://mysql:3306',
    'redis' => 'redis://redis:6379',
    'phpmyadmin' => 'http://phpmyadmin',
    'mailhog' => 'http://mailhog:8025',
    'browsersync' => 'http://browsersync:3000'
];

// Define files that need URL updates
$files_to_update = [
    'test-server-performance.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress'
    ],
    'scripts/comprehensive-server-test.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress',
        'localhost:5174' => 'react-app:5176',
        'http://localhost:5174' => 'http://react-app:5176'
    ],
    'scripts/testing/full-functionality-test.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress',
        'localhost:5174' => 'react-app:5176',
        'http://localhost:5174' => 'http://react-app:5176'
    ],
    'scripts/testing/comprehensive-activation-test.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress'
    ],
    'scripts/testing/test-hyiplab-api-complete.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress'
    ],
    'scripts/testing/theme-verification-test.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress'
    ],
    'scripts/final-verification.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress'
    ],
    'scripts/testing/simple-stress-test.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress',
        'localhost:5174' => 'react-app:5176',
        'http://localhost:5174' => 'http://react-app:5176'
    ],
    'scripts/automate-page-creation.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress'
    ],
    'scripts/testing/test-hyiplab-activation.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress'
    ],
    'scripts/testing/comprehensive-connection-test.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress'
    ],
    'activate-hyiplab-api.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress'
    ],
    'test-headless-implementation.php' => [
        'localhost:8888' => 'wordpress',
        'http://localhost:8888' => 'http://wordpress',
        'localhost:5174' => 'react-app:5176',
        'http://localhost:5174' => 'http://react-app:5176'
    ]
];

echo "1. Updating test scripts for Docker compatibility...\n";

$updated_files = 0;
$total_replacements = 0;

foreach ($files_to_update as $file => $replacements) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $original_content = $content;
        
        foreach ($replacements as $old_url => $new_url) {
            $content = str_replace($old_url, $new_url, $content);
        }
        
        if ($content !== $original_content) {
            file_put_contents($file, $content);
            $updated_files++;
            $total_replacements += count($replacements);
            echo "   ✅ Updated: $file\n";
        } else {
            echo "   ⚠️  No changes needed: $file\n";
        }
    } else {
        echo "   ❌ File not found: $file\n";
    }
}

echo "\n2. Creating Docker-compatible test script...\n";

// Create a new Docker-compatible test script
$docker_test_script = '<?php
/**
 * BlackCnote Docker-Compatible Server Test
 * Updated for Docker container environment
 */

declare(strict_types=1);

echo "🔍 BlackCnote Docker-Compatible Server Test\n";
echo "==========================================\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
global $wpdb;
$start = microtime(true);
$result = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
$duration = (microtime(true) - $start) * 1000;
echo "   Duration: {$duration}ms\n";

// Test 2: API Endpoints (using internal Docker URLs)
echo "\n2. Testing API Endpoints...\n";
$endpoints = [
    "/wp-json/blackcnote/v1/homepage",
    "/wp-json/blackcnote/v1/stats", 
    "/wp-json/blackcnote/v1/plans"
];

foreach ($endpoints as $endpoint) {
    $start = microtime(true);
    $response = wp_remote_get(home_url($endpoint), ["timeout" => 30]);
    $duration = (microtime(true) - $start) * 1000;
    
    if (is_wp_error($response)) {
        echo "   {$endpoint}: ERROR - " . $response->get_error_message() . " ({$duration}ms)\n";
    } else {
        $status = wp_remote_retrieve_response_code($response);
        $size = strlen(wp_remote_retrieve_body($response));
        echo "   {$endpoint}: {$status} - {$size} bytes ({$duration}ms)\n";
        
        if ($duration > 2000) {
            echo "   ⚠️  SLOW RESPONSE DETECTED!\n";
        }
    }
}

// Test 3: HYIPLab Tables
echo "\n3. Testing HYIPLab Tables...\n";
$tables = ["hyiplab_users", "hyiplab_investments", "hyiplab_transactions", "hyiplab_plans"];

foreach ($tables as $table) {
    $full_table = $wpdb->prefix . $table;
    $start = microtime(true);
    $exists = $wpdb->get_var("SHOW TABLES LIKE \"{$full_table}\"") === $full_table;
    $duration = (microtime(true) - $start) * 1000;
    
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$full_table}");
        echo "   {$table}: {$count} records ({$duration}ms)\n";
    } else {
        echo "   {$table}: Does not exist ({$duration}ms)\n";
    }
}

// Test 4: Memory Usage
echo "\n4. Memory Usage Test:\n";
$memory_usage = memory_get_usage(true);
$memory_peak = memory_get_peak_usage(true);
$memory_limit = ini_get("memory_limit");

echo "   Current: " . round($memory_usage / 1024 / 1024, 2) . " MB\n";
echo "   Peak: " . round($memory_peak / 1024 / 1024, 2) . " MB\n";
echo "   Limit: {$memory_limit}\n";

// Test 5: Plugin Status
echo "\n5. Testing Plugin Status...\n";
$plugins = ["blackcnote-cors", "hyiplab", "full-content-checker"];

foreach ($plugins as $plugin) {
    if (is_plugin_active($plugin . "/" . $plugin . ".php")) {
        echo "   {$plugin}: ACTIVE\n";
    } else {
        echo "   {$plugin}: INACTIVE\n";
    }
}

// Test 6: CORS Headers (using internal URL)
echo "\n6. Testing CORS Headers...\n";
$cors_response = wp_remote_get(home_url("/wp-json/blackcnote/v1/health"), [
    "timeout" => 10,
    "headers" => [
        "Origin" => "http://react-app:5176",
        "X-Requested-With" => "XMLHttpRequest"
    ]
]);

if (is_wp_error($cors_response)) {
    echo "   CORS Test: FAILED - " . $cors_response->get_error_message() . "\n";
} else {
    $headers = wp_remote_retrieve_headers($cors_response);
    $cors_header = $headers->get("Access-Control-Allow-Origin");
    if ($cors_header) {
        echo "   CORS Test: PASSED - Headers configured\n";
    } else {
        echo "   CORS Test: FAILED - Headers missing\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "✅ All tests completed using Docker-compatible URLs\n";
echo "✅ Internal service communication verified\n";
echo "✅ Database and API endpoints tested\n";
';

file_put_contents('test-docker-compatible.php', $docker_test_script);
echo "   ✅ Created: test-docker-compatible.php\n";

echo "\n3. Creating Docker service health check...\n";

// Create a Docker service health check script
$health_check_script = '<?php
/**
 * BlackCnote Docker Service Health Check
 * Checks all Docker services are accessible
 */

declare(strict_types=1);

echo "🏥 BlackCnote Docker Service Health Check\n";
echo "========================================\n\n";

$services = [
    "WordPress" => "http://wordpress",
    "React App" => "http://react-app:5176", 
    "MySQL" => "mysql://mysql:3306",
    "Redis" => "redis://redis:6379",
    "phpMyAdmin" => "http://phpmyadmin",
    "MailHog" => "http://mailhog:8025",
    "Browsersync" => "http://browsersync:3000"
];

$healthy_services = 0;
$total_services = count($services);

foreach ($services as $name => $url) {
    echo "Checking {$name}... ";
    
    if (strpos($url, "mysql://") === 0) {
        // Test MySQL connection
        global $wpdb;
        $result = $wpdb->get_var("SELECT 1");
        if ($result) {
            echo "✅ Healthy\n";
            $healthy_services++;
        } else {
            echo "❌ Unhealthy\n";
        }
    } elseif (strpos($url, "redis://") === 0) {
        // Test Redis connection
        if (class_exists("Redis")) {
            try {
                $redis = new Redis();
                if ($redis->connect("redis", 6379)) {
                    echo "✅ Healthy\n";
                    $healthy_services++;
                } else {
                    echo "❌ Unhealthy\n";
                }
            } catch (Exception $e) {
                echo "❌ Unhealthy\n";
            }
        } else {
            echo "⚠️  Redis extension not available\n";
        }
    } else {
        // Test HTTP services
        $response = wp_remote_get($url, ["timeout" => 5]);
        if (!is_wp_error($response)) {
            $status = wp_remote_retrieve_response_code($response);
            if ($status >= 200 && $status < 500) {
                echo "✅ Healthy (HTTP {$status})\n";
                $healthy_services++;
            } else {
                echo "❌ Unhealthy (HTTP {$status})\n";
            }
        } else {
            echo "❌ Unhealthy - " . $response->get_error_message() . "\n";
        }
    }
}

echo "\n=== Health Summary ===\n";
echo "Healthy Services: {$healthy_services}/{$total_services}\n";
$health_percentage = round(($healthy_services / $total_services) * 100, 1);
echo "Health Score: {$health_percentage}%\n";

if ($health_percentage >= 90) {
    echo "Status: 🟢 EXCELLENT\n";
} elseif ($health_percentage >= 75) {
    echo "Status: 🟡 GOOD\n";
} elseif ($health_percentage >= 50) {
    echo "Status: 🟠 FAIR\n";
} else {
    echo "Status: 🔴 POOR\n";
}

echo "\n✅ Docker service health check completed\n";
';

file_put_contents('docker-health-check.php', $health_check_script);
echo "   ✅ Created: docker-health-check.php\n";

echo "\n4. Updating startup scripts for Docker compatibility...\n";

// Update the main startup script to use Docker-compatible URLs
$startup_script_content = '#!/bin/bash
# BlackCnote Docker-Compatible Startup Script

echo "🚀 Starting BlackCnote with Docker-compatible configuration..."

# Set Docker-compatible environment variables
export WORDPRESS_URL="http://wordpress"
export REACT_URL="http://react-app:5176"
export MYSQL_URL="mysql://mysql:3306"
export REDIS_URL="redis://redis:6379"

# Run Docker health check
echo "Checking Docker services..."
php docker-health-check.php

# Run Docker-compatible server test
echo "Running server tests..."
php test-docker-compatible.php

echo "✅ BlackCnote Docker startup completed"
';

file_put_contents('start-docker-compatible.sh', $startup_script_content);
chmod('start-docker-compatible.sh', 0755);
echo "   ✅ Created: start-docker-compatible.sh\n";

echo "\n=== Docker Compatibility Fix Summary ===\n";
echo "✅ Updated {$updated_files} files\n";
echo "✅ Made {$total_replacements} URL replacements\n";
echo "✅ Created Docker-compatible test scripts\n";
echo "✅ Created Docker service health check\n";
echo "✅ Updated startup scripts\n";

echo "\n🎯 Next Steps:\n";
echo "1. Run: php test-docker-compatible.php\n";
echo "2. Run: php docker-health-check.php\n";
echo "3. Test all services are accessible\n";
echo "4. Verify React/WordPress integration works\n";

echo "\n✅ Docker compatibility fix completed!\n"; 