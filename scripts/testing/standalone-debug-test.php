<?php
/**
 * BlackCnote Standalone Debug Test
 * 
 * Tests debug functionality without requiring WordPress database connection
 * 
 * @package BlackCnote
 * @version 2.0.0
 * @author BlackCnote Development Team
 */

declare(strict_types=1);

echo "=== BlackCnote Standalone Debug Test ===\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check if debug system files exist
echo "Testing Debug System Files...\n";

$possible_paths = [
    dirname(dirname(dirname(__DIR__))) . '/blackcnote/',
    dirname(dirname(dirname(__DIR__))) . '/blackcnote/blackcnote/',
    dirname(dirname(__DIR__)) . '/blackcnote/',
    dirname(__DIR__) . '/blackcnote/',
    __DIR__ . '/blackcnote/'
];

$wp_path = null;
foreach ($possible_paths as $path) {
    if (file_exists($path . 'wp-config.php')) {
        $wp_path = $path;
        break;
    }
}

if ($wp_path) {
    echo "✓ WordPress found at: {$wp_path}\n";
} else {
    echo "✗ WordPress not found\n";
    exit(1);
}

$debug_system_file = $wp_path . 'wp-content/plugins/blackcnote-hyiplab/app/Log/BlackCnoteDebugSystem.php';
$test_framework_file = $wp_path . 'wp-content/plugins/blackcnote-hyiplab/app/Log/BlackCnoteTestFramework.php';

if (file_exists($debug_system_file)) {
    echo "✓ BlackCnoteDebugSystem.php found\n";
} else {
    echo "✗ BlackCnoteDebugSystem.php not found at: {$debug_system_file}\n";
}

if (file_exists($test_framework_file)) {
    echo "✓ BlackCnoteTestFramework.php found\n";
} else {
    echo "✗ BlackCnoteTestFramework.php not found at: {$test_framework_file}\n";
}

echo "\n";

// Test 2: Check React integration
echo "Testing React Integration...\n";

$react_tests = [
    'react_app_directory' => is_dir($wp_path . 'react-app'),
    'react_src_directory' => is_dir($wp_path . 'react-app/src'),
    'vite_config' => file_exists($wp_path . 'react-app/vite.config.ts'),
    'package_json' => file_exists($wp_path . 'react-app/package.json'),
    'app_component' => file_exists($wp_path . 'react-app/src/App.tsx'),
    'main_entry' => file_exists($wp_path . 'react-app/src/main.tsx')
];

foreach ($react_tests as $test_name => $result) {
    if ($result) {
        echo "✓ {$test_name}: Found\n";
    } else {
        echo "✗ {$test_name}: Not found\n";
    }
}

echo "\n";

// Test 3: Check Docker integration
echo "Testing Docker Integration...\n";

$docker_tests = [
    'docker_compose' => file_exists($wp_path . 'docker-compose.yml'),
    'docker_compose_override' => file_exists($wp_path . 'docker-compose.override.yml'),
    'docker_compose_prod' => file_exists($wp_path . 'docker-compose.prod.yml'),
    'nginx_config' => file_exists($wp_path . 'config/nginx/blackcnote.conf'),
    'apache_config' => file_exists($wp_path . 'config/apache/blackcnote-vhost.conf')
];

foreach ($docker_tests as $test_name => $result) {
    if ($result) {
        echo "✓ {$test_name}: Found\n";
    } else {
        echo "✗ {$test_name}: Not found\n";
    }
}

echo "\n";

// Test 4: Check file permissions
echo "Testing File Permissions...\n";

$permission_tests = [
    'wp_config_readable' => is_readable($wp_path . 'wp-config.php'),
    'wp_content_writable' => is_writable($wp_path . 'wp-content'),
    'plugins_directory' => is_dir($wp_path . 'wp-content/plugins'),
    'themes_directory' => is_dir($wp_path . 'wp-content/themes')
];

foreach ($permission_tests as $test_name => $result) {
    if ($result) {
        echo "✓ {$test_name}: OK\n";
    } else {
        echo "✗ {$test_name}: Failed\n";
    }
}

echo "\n";

// Test 5: Check PHP environment
echo "Testing PHP Environment...\n";

$php_tests = [
    'php_version' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'error_reporting' => error_reporting() !== 0
];

foreach ($php_tests as $test_name => $result) {
    if (is_bool($result)) {
        if ($result) {
            echo "✓ {$test_name}: OK\n";
        } else {
            echo "✗ {$test_name}: Failed\n";
        }
    } else {
        echo "✓ {$test_name}: {$result}\n";
    }
}

echo "\n";

// Test 6: Check log directory creation
echo "Testing Log Directory Creation...\n";

$log_dir = $wp_path . 'wp-content/logs/blackcnote/';

if (!is_dir($log_dir)) {
    if (mkdir($log_dir, 0755, true)) {
        echo "✓ Log directory created successfully\n";
    } else {
        echo "✗ Failed to create log directory\n";
    }
    } else {
    echo "✓ Log directory already exists\n";
    }
    
if (is_writable($log_dir)) {
    echo "✓ Log directory is writable\n";
} else {
    echo "✗ Log directory is not writable\n";
}

// Create test log files
$log_files = [
    'debug.log' => "=== BlackCnote Debug Log ===\n",
    'errors.log' => "=== BlackCnote Error Log ===\n",
    'performance.log' => "=== BlackCnote Performance Log ===\n",
    'tests.log' => "=== BlackCnote Test Log ===\n"
];

foreach ($log_files as $filename => $content) {
    $filepath = $log_dir . $filename;
    if (file_put_contents($filepath, $content)) {
        echo "✓ Created {$filename}\n";
        } else {
        echo "✗ Failed to create {$filename}\n";
    }
}

echo "\n";

// Test 7: Check WordPress configuration
echo "Testing WordPress Configuration...\n";

$wp_config_content = file_get_contents($wp_path . 'wp-config.php');

$wp_config_tests = [
    'wp_debug_enabled' => strpos($wp_config_content, "define( 'WP_DEBUG', true )") !== false,
    'wp_debug_log_enabled' => strpos($wp_config_content, "define( 'WP_DEBUG_LOG', true )") !== false,
    'wp_debug_display_disabled' => strpos($wp_config_content, "define( 'WP_DEBUG_DISPLAY', false )") !== false,
    'script_debug_enabled' => strpos($wp_config_content, "define( 'SCRIPT_DEBUG', true )") !== false,
    'save_queries_enabled' => strpos($wp_config_content, "define( 'SAVEQUERIES', true )") !== false
];

foreach ($wp_config_tests as $test_name => $result) {
    if ($result) {
        echo "✓ {$test_name}: Enabled\n";
        } else {
        echo "✗ {$test_name}: Not enabled\n";
    }
}

echo "\n";

// Test 8: Check project structure
echo "Testing Project Structure...\n";

$structure_tests = [
    'docs_directory' => is_dir(dirname(dirname(__DIR__)) . '/docs'),
    'scripts_directory' => is_dir(dirname(__DIR__)),
    'config_directory' => is_dir(dirname(dirname(__DIR__)) . '/config'),
    'hyiplab_directory' => is_dir(dirname(dirname(__DIR__)) . '/hyiplab'),
    'react_app_directory' => is_dir(dirname(dirname(__DIR__)) . '/react-app')
];

foreach ($structure_tests as $test_name => $result) {
    if ($result) {
        echo "✓ {$test_name}: Found\n";
    } else {
        echo "✗ {$test_name}: Not found\n";
    }
}

echo "\n";

// Generate summary
echo "=== Test Summary ===\n";

$total_tests = count($react_tests) + count($docker_tests) + count($permission_tests) + count($php_tests) + count($wp_config_tests) + count($structure_tests);
$passed_tests = 0;

foreach ([$react_tests, $docker_tests, $permission_tests, $php_tests, $wp_config_tests, $structure_tests] as $test_group) {
    foreach ($test_group as $result) {
        if ($result) $passed_tests++;
    }
}

echo "Total Tests: {$total_tests}\n";
echo "Passed: {$passed_tests}\n";
echo "Failed: " . ($total_tests - $passed_tests) . "\n";
echo "Success Rate: " . round(($passed_tests / $total_tests) * 100, 2) . "%\n";
echo "Completed at: " . date('Y-m-d H:i:s') . "\n\n";

if ($passed_tests === $total_tests) {
    echo "🎉 All tests passed! The BlackCnote debug system is ready.\n";
} else {
    echo "⚠️  Some tests failed. Please check the issues above.\n";
}

echo "\n=== BlackCnote Standalone Debug Test Complete ===\n"; 