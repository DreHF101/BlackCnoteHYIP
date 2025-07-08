<?php
/**
 * BlackCnote Cursor AI Monitor Test Trigger Script
 * Manually triggers the monitoring system and checks validation results
 */

// Load WordPress
require_once 'blackcnote/wp-config.php';

// Ensure we're in the right environment
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/blackcnote/');
}

// Load the debug system
if (class_exists('BlackCnoteDebugSystem')) {
    $debug_system = BlackCnoteDebugSystem::instance();
    echo "✓ BlackCnote Debug System loaded\n";
} else {
    echo "✗ BlackCnote Debug System not found\n";
    exit(1);
}

// Get the Cursor AI Monitor instance
$cursor_ai_monitor = null;
if (isset($debug_system->cursor_ai_monitor)) {
    $cursor_ai_monitor = $debug_system->cursor_ai_monitor;
    echo "✓ Cursor AI Monitor loaded\n";
} else {
    echo "✗ Cursor AI Monitor not found\n";
    exit(1);
}

echo "\n=== BlackCnote Cursor AI Monitor Test ===\n\n";

// Test 1: Check canonical paths
echo "1. Testing Canonical Paths:\n";
$canonical_paths = $cursor_ai_monitor->getCanonicalPaths();
foreach ($canonical_paths as $type => $path) {
    if (is_dir($path) || filter_var($path, FILTER_VALIDATE_URL)) {
        echo "   ✓ {$type}: {$path}\n";
    } else {
        echo "   ✗ {$type}: {$path} (not found)\n";
    }
}

// Test 2: Check cursor rules
echo "\n2. Testing Cursor Rules:\n";
$cursor_rules = $cursor_ai_monitor->getCursorRules();
foreach ($cursor_rules as $rule => $config) {
    $status = $config['enabled'] ? 'Active' : 'Disabled';
    echo "   ✓ {$rule}: {$status} - {$config['description']}\n";
}

// Test 3: Create a test file and trigger validation
echo "\n3. Testing File Change Detection:\n";
$test_file = ABSPATH . 'wp-content/themes/blackcnote/test-cursor-ai-monitor-trigger.php';
$test_content = "<?php\n// Test file created by Cursor AI Monitor Trigger Script\n// Timestamp: " . date('Y-m-d H:i:s') . "\n?>\n";

if (file_put_contents($test_file, $test_content)) {
    echo "   ✓ Test file created: {$test_file}\n";
    
    // Trigger file monitoring manually
    if (method_exists($cursor_ai_monitor, 'monitorFileChanges')) {
        $cursor_ai_monitor->monitorFileChanges();
        echo "   ✓ File monitoring triggered\n";
    } else {
        echo "   ✗ File monitoring method not found\n";
    }
} else {
    echo "   ✗ Failed to create test file\n";
}

// Test 4: Test validation functions directly
echo "\n4. Testing Validation Functions:\n";

// Test path validation
$test_path = ABSPATH . 'wp-content/themes/blackcnote/test-validation.php';
if (method_exists($cursor_ai_monitor, 'validateCanonicalPaths')) {
    $path_validation = $cursor_ai_monitor->validateCanonicalPaths($test_path, 'test');
    echo "   ✓ Path validation: " . ($path_validation['valid'] ? 'Valid' : 'Invalid') . "\n";
    if (!empty($path_validation['warnings'])) {
        foreach ($path_validation['warnings'] as $warning) {
            echo "     Warning: {$warning}\n";
        }
    }
} else {
    echo "   ✗ Path validation method not found\n";
}

// Test WordPress standards validation
$test_code = "<?php\ndeclare(strict_types=1);\n\nif (!defined('ABSPATH')) {\n    exit;\n}\n\nadd_action('init', 'test_function');\nfunction test_function() {\n    echo 'test';\n}\n?>\n";
if (method_exists($cursor_ai_monitor, 'validateWordPressStandards')) {
    $wp_validation = $cursor_ai_monitor->validateWordPressStandards($test_code, $test_path);
    echo "   ✓ WordPress standards validation: " . ($wp_validation['valid'] ? 'Valid' : 'Invalid') . "\n";
    if (!empty($wp_validation['warnings'])) {
        foreach ($wp_validation['warnings'] as $warning) {
            echo "     Warning: {$warning}\n";
        }
    }
} else {
    echo "   ✗ WordPress standards validation method not found\n";
}

// Test 5: Check validation results
echo "\n5. Checking Validation Results:\n";
$validation_results = $cursor_ai_monitor->getValidationResults();
if (!empty($validation_results)) {
    echo "   ✓ Found " . count($validation_results) . " validation results:\n";
    foreach (array_slice($validation_results, -3) as $file => $result) {
        echo "     - {$file} (" . $result['timestamp'] . ")\n";
        foreach ($result['validations'] as $type => $validation) {
            $status = $validation['valid'] ? 'Valid' : 'Invalid';
            echo "       {$type}: {$status}\n";
        }
    }
} else {
    echo "   ✗ No validation results found\n";
}

// Test 6: Check debug log for recent entries
echo "\n6. Checking Debug Log:\n";
$log_file = WP_CONTENT_DIR . '/logs/blackcnote-debug.log';
if (file_exists($log_file)) {
    $log_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recent_entries = array_slice($log_lines, -10);
    $cursor_entries = array_filter($recent_entries, function($line) {
        return strpos($line, 'Cursor AI') !== false || strpos($line, 'test-cursor-ai-monitor') !== false;
    });
    
    if (!empty($cursor_entries)) {
        echo "   ✓ Found " . count($cursor_entries) . " recent Cursor AI entries in log:\n";
        foreach ($cursor_entries as $entry) {
            $data = json_decode($entry, true);
            if ($data) {
                echo "     - {$data['timestamp']}: {$data['message']}\n";
            }
        }
    } else {
        echo "   ✗ No recent Cursor AI entries found in log\n";
    }
} else {
    echo "   ✗ Debug log file not found: {$log_file}\n";
}

// Test 7: Test AJAX endpoint
echo "\n7. Testing AJAX Endpoint:\n";
if (function_exists('wp_ajax_blackcnote_cursor_validation')) {
    echo "   ✓ AJAX endpoint exists\n";
} else {
    echo "   ✗ AJAX endpoint not found\n";
}

echo "\n=== Test Complete ===\n\n";

// Cleanup test files
echo "Cleaning up test files...\n";
$test_files = [
    ABSPATH . 'wp-content/themes/blackcnote/test-cursor-ai-monitor.php',
    ABSPATH . 'wp-content/themes/blackcnote/test-cursor-ai-monitor-2.php',
    ABSPATH . 'wp-content/themes/blackcnote/test-cursor-ai-monitor-trigger.php'
];

foreach ($test_files as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "   ✓ Removed: {$file}\n";
    }
}

echo "\nTest script completed. Check the results above.\n";
echo "If validation results are empty, the monitoring system may need to be triggered by a WordPress page load.\n";
echo "Visit your WordPress admin and go to: BlackCnote Debug → Cursor AI Monitor\n";
?> 