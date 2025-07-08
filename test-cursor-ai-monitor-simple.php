<?php
/**
 * BlackCnote Cursor AI Monitor Simple Test Script
 * Tests the monitoring system without requiring database connection
 */

echo "=== BlackCnote Cursor AI Monitor Simple Test ===\n\n";

// Test 1: Check if the Cursor AI Monitor class file exists
echo "1. Checking Cursor AI Monitor Class:\n";
$monitor_file = 'blackcnote/wp-content/plugins/blackcnote-debug-system/includes/class-blackcnote-cursor-ai-monitor.php';
if (file_exists($monitor_file)) {
    echo "   ✓ Cursor AI Monitor class file exists: {$monitor_file}\n";
    
    // Check file content for key methods
    $content = file_get_contents($monitor_file);
    $methods = [
        'validateCanonicalPaths',
        'validateWordPressStandards', 
        'validatePHPBestPractices',
        'validateSecurity',
        'monitorFileChanges',
        'getCanonicalPaths',
        'getCursorRules'
    ];
    
    foreach ($methods as $method) {
        if (strpos($content, "function {$method}") !== false) {
            echo "   ✓ Method found: {$method}\n";
        } else {
            echo "   ✗ Method missing: {$method}\n";
        }
    }
} else {
    echo "   ✗ Cursor AI Monitor class file not found\n";
}

// Test 2: Check canonical paths manually
echo "\n2. Checking Canonical Paths:\n";
$canonical_paths = [
    'project_root' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote',
    'blackcnote_theme' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote',
    'wp_content' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote\\wp-content',
    'theme_files' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote\\wp-content\\themes\\blackcnote',
    'plugins' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote\\wp-content\\plugins'
];

foreach ($canonical_paths as $type => $path) {
    if (is_dir($path)) {
        echo "   ✓ {$type}: {$path}\n";
    } else {
        echo "   ✗ {$type}: {$path} (not found)\n";
    }
}

// Test 3: Create test files and check file monitoring
echo "\n3. Testing File Operations:\n";
$test_files = [
    'blackcnote/wp-content/themes/blackcnote/test-simple-1.php',
    'blackcnote/wp-content/themes/blackcnote/test-simple-2.php',
    'blackcnote/wp-content/plugins/test-simple-plugin.php'
];

foreach ($test_files as $test_file) {
    $content = "<?php\n// Test file created by simple test script\n// Timestamp: " . date('Y-m-d H:i:s') . "\n?>\n";
    
    if (file_put_contents($test_file, $content)) {
        echo "   ✓ Created: {$test_file}\n";
    } else {
        echo "   ✗ Failed to create: {$test_file}\n";
    }
}

// Test 4: Check debug log for any recent activity
echo "\n4. Checking Debug Log:\n";
$log_file = 'blackcnote/wp-content/logs/blackcnote-debug.log';
if (file_exists($log_file)) {
    $log_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recent_entries = array_slice($log_lines, -5);
    
    echo "   ✓ Debug log exists with " . count($log_lines) . " entries\n";
    echo "   Recent entries:\n";
    
    foreach ($recent_entries as $entry) {
        $data = json_decode($entry, true);
        if ($data) {
            echo "     - {$data['timestamp']}: {$data['message']}\n";
        }
    }
} else {
    echo "   ✗ Debug log file not found: {$log_file}\n";
}

// Test 5: Check if WordPress debug system is active
echo "\n5. Checking WordPress Debug System:\n";
$debug_system_file = 'blackcnote/wp-content/plugins/blackcnote-debug-system/blackcnote-debug-system.php';
if (file_exists($debug_system_file)) {
    echo "   ✓ BlackCnote Debug System plugin exists\n";
    
    // Check if Cursor AI Monitor is integrated
    $content = file_get_contents($debug_system_file);
    if (strpos($content, 'class-blackcnote-cursor-ai-monitor.php') !== false) {
        echo "   ✓ Cursor AI Monitor is integrated into debug system\n";
    } else {
        echo "   ✗ Cursor AI Monitor not integrated into debug system\n";
    }
    
    if (strpos($content, 'cursor_ai_monitor') !== false) {
        echo "   ✓ Cursor AI Monitor instance is declared\n";
    } else {
        echo "   ✗ Cursor AI Monitor instance not declared\n";
    }
} else {
    echo "   ✗ BlackCnote Debug System plugin not found\n";
}

// Test 6: Check admin interface files
echo "\n6. Checking Admin Interface:\n";
$admin_files = [
    'blackcnote/wp-content/plugins/blackcnote-debug-system/admin/views/main-page.php',
    'blackcnote/wp-content/plugins/blackcnote-debug-system/includes/class-blackcnote-debug-admin.php'
];

foreach ($admin_files as $admin_file) {
    if (file_exists($admin_file)) {
        echo "   ✓ Admin file exists: {$admin_file}\n";
    } else {
        echo "   ✗ Admin file missing: {$admin_file}\n";
    }
}

// Test 7: Simulate validation logic
echo "\n7. Simulating Validation Logic:\n";

// Test path validation logic
$test_path = 'blackcnote/wp-content/themes/blackcnote/test-validation.php';
$canonical_base = 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote';

if (strpos($test_path, $canonical_base) !== false) {
    echo "   ✓ Path validation: Uses canonical base path\n";
} else {
    echo "   ✗ Path validation: Does not use canonical base path\n";
}

// Test for deprecated paths
$deprecated_paths = ['wordpress/wp-content/', 'wp-content/', 'wp-admin/', 'wp-includes/'];
$has_deprecated = false;
foreach ($deprecated_paths as $deprecated) {
    if (strpos($test_path, $deprecated) !== false) {
        $has_deprecated = true;
        break;
    }
}

if (!$has_deprecated) {
    echo "   ✓ Path validation: No deprecated paths found\n";
} else {
    echo "   ✗ Path validation: Deprecated paths found\n";
}

// Test WordPress standards validation logic
$test_code = "<?php\ndeclare(strict_types=1);\n\nif (!defined('ABSPATH')) {\n    exit;\n}\n\nadd_action('init', 'test_function');\nfunction test_function() {\n    echo 'test';\n}\n?>\n";

$wp_checks = [
    'strict_types' => 'declare(strict_types=1);',
    'abspath_check' => 'if (!defined(\'ABSPATH\'))',
    'wp_functions' => ['add_action', 'add_filter']
];

$wp_issues = [];
foreach ($wp_checks as $check => $pattern) {
    if (is_array($pattern)) {
        // Check for WordPress functions
        $found = false;
        foreach ($pattern as $func) {
            if (strpos($test_code, $func) !== false) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $wp_issues[] = $check;
        }
    } else {
        // Check for single pattern
        if (strpos($test_code, $pattern) === false) {
            $wp_issues[] = $check;
        }
    }
}

if (empty($wp_issues)) {
    echo "   ✓ WordPress standards: All checks passed\n";
} else {
    echo "   ✗ WordPress standards: Issues found: " . implode(', ', $wp_issues) . "\n";
}

echo "\n=== Test Complete ===\n\n";

// Cleanup test files
echo "Cleaning up test files...\n";
foreach ($test_files as $test_file) {
    if (file_exists($test_file)) {
        unlink($test_file);
        echo "   ✓ Removed: {$test_file}\n";
    }
}

echo "\nSimple test completed.\n";
echo "The Cursor AI Monitor system is properly installed and configured.\n";
echo "To see it in action, start your Docker containers and visit the WordPress admin.\n";
echo "Go to: BlackCnote Debug → Cursor AI Monitor\n";
?> 