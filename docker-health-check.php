<?php
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
