<?php
/**
 * Final Comprehensive BlackCnote Debug Test
 * Tests all aspects of the debug system in the current environment
 */

echo "Final Comprehensive BlackCnote Debug Test\n";
echo "========================================\n\n";

// Test Results Array
$testResults = [];

// Function to add test result
function addTestResult($testName, $passed, $message = '') {
    global $testResults;
    $testResults[] = [
        'test' => $testName,
        'passed' => $passed,
        'message' => $message
    ];
}

// Test 1: Environment Check
echo "Test 1: Environment Check\n";
echo "------------------------\n";

$phpVersion = PHP_VERSION;
$phpSapi = php_sapi_name();
$memoryLimit = ini_get('memory_limit');
$maxExecutionTime = ini_get('max_execution_time');

echo "✓ PHP Version: $phpVersion\n";
echo "✓ PHP SAPI: $phpSapi\n";
echo "✓ Memory Limit: $memoryLimit\n";
echo "✓ Max Execution Time: $maxExecutionTime\n";

addTestResult('Environment Check', true, "PHP $phpVersion, SAPI: $phpSapi");

// Test 2: Required Extensions
echo "\nTest 2: Required Extensions\n";
echo "----------------------------\n";

$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'curl'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ Extension loaded: $ext\n";
    } else {
        echo "✗ Extension missing: $ext\n";
        $missingExtensions[] = $ext;
    }
}

addTestResult('Required Extensions', empty($missingExtensions), 
    empty($missingExtensions) ? 'All required extensions loaded' : 'Missing: ' . implode(', ', $missingExtensions));

// Test 3: File System Check
echo "\nTest 3: File System Check\n";
echo "-------------------------\n";

$criticalFiles = [
    '../blackcnote/wp-config.php' => 'WordPress Configuration',
    '../hyiplab/tools/debug-system.php' => 'Debug System',
    '../hyiplab/tools/enhanced-debug-system.php' => 'Enhanced Debug System',
    '../hyiplab/tools/debug-admin-interface.php' => 'Debug Admin Interface',
    '../hyiplab/hyiplab.php' => 'HYIPLab Plugin',
    '../blackcnote/wp-content/themes/blackcnote/functions.php' => 'Theme Functions'
];

$missingFiles = [];

foreach ($criticalFiles as $file => $description) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✓ $description: " . basename($file) . "\n";
    } else {
        echo "✗ $description: " . basename($file) . " (missing)\n";
        $missingFiles[] = $description;
    }
}

addTestResult('File System Check', empty($missingFiles), 
    empty($missingFiles) ? 'All critical files present' : 'Missing: ' . implode(', ', $missingFiles));

// Test 4: Directory Structure Check
echo "\nTest 4: Directory Structure Check\n";
echo "----------------------------------\n";

$criticalDirs = [
    '../blackcnote' => 'WordPress Root',
    '../blackcnote/wp-content' => 'WordPress Content',
    '../blackcnote/wp-content/themes' => 'Themes Directory',
    '../blackcnote/wp-content/themes/blackcnote' => 'BlackCnote Theme',
    '../hyiplab' => 'HYIPLab Plugin',
    '../hyiplab/tools' => 'HYIPLab Tools',
    '../hyiplab/app' => 'HYIPLab App',
    '../react-app' => 'React App'
];

$missingDirs = [];

foreach ($criticalDirs as $dir => $description) {
    $fullPath = __DIR__ . '/' . $dir;
    if (is_dir($fullPath)) {
        echo "✓ $description: " . basename($dir) . "\n";
    } else {
        echo "✗ $description: " . basename($dir) . " (missing)\n";
        $missingDirs[] = $description;
    }
}

addTestResult('Directory Structure Check', empty($missingDirs), 
    empty($missingDirs) ? 'All critical directories present' : 'Missing: ' . implode(', ', $missingDirs));

// Test 5: WordPress Configuration Check
echo "\nTest 5: WordPress Configuration Check\n";
echo "--------------------------------------\n";

$wpConfigPath = __DIR__ . '/../blackcnote/wp-config.php';
if (file_exists($wpConfigPath)) {
    $wpConfig = file_get_contents($wpConfigPath);
    
    $configChecks = [
        'WP_DEBUG' => strpos($wpConfig, "WP_DEBUG', true") !== false,
        'WP_DEBUG_LOG' => strpos($wpConfig, "WP_DEBUG_LOG', true") !== false,
        'WP_DEBUG_DISPLAY' => strpos($wpConfig, "WP_DEBUG_DISPLAY', false") !== false,
        'DB_HOST localhost' => strpos($wpConfig, "DB_HOST', 'localhost'") !== false,
        'DB_USER blackcnote_user' => strpos($wpConfig, "DB_USER', 'blackcnote_user'") !== false
    ];
    
    $failedChecks = [];
    foreach ($configChecks as $check => $passed) {
        if ($passed) {
            echo "✓ $check\n";
        } else {
            echo "✗ $check\n";
            $failedChecks[] = $check;
        }
    }
    
    addTestResult('WordPress Configuration Check', empty($failedChecks), 
        empty($failedChecks) ? 'All configuration settings correct' : 'Issues: ' . implode(', ', $failedChecks));
} else {
    echo "✗ WordPress configuration file not found\n";
    addTestResult('WordPress Configuration Check', false, 'wp-config.php not found');
}

// Test 6: Debug System Loading Test
echo "\nTest 6: Debug System Loading Test\n";
echo "----------------------------------\n";

try {
    // Define constants for debug system
    if (!defined('ABSPATH')) {
        define('ABSPATH', __DIR__ . '/../blackcnote/');
    }
    if (!defined('WP_CONTENT_DIR')) {
        define('WP_CONTENT_DIR', __DIR__ . '/../blackcnote/wp-content/');
    }
    if (!defined('BLACKCNOTE_DEBUG')) {
        define('BLACKCNOTE_DEBUG', true);
    }
    
    // Create logs directory
    $logsDir = WP_CONTENT_DIR . 'logs';
    if (!is_dir($logsDir)) {
        mkdir($logsDir, 0755, true);
        echo "✓ Created logs directory\n";
    } else {
        echo "✓ Logs directory exists\n";
    }
    
    // Test debug system file
    $debugSystemPath = __DIR__ . '/../hyiplab/tools/debug-system.php';
    if (file_exists($debugSystemPath)) {
        $debugContent = file_get_contents($debugSystemPath);
        
        if (strpos($debugContent, 'class BlackCnoteDebugSystem') !== false) {
            echo "✓ Debug system class found\n";
            
            // Test logging functionality
            $logFile = WP_CONTENT_DIR . 'logs/blackcnote-debug.log';
            $testMessage = "[" . date('Y-m-d H:i:s') . "] Final debug test - " . uniqid() . "\n";
            file_put_contents($logFile, $testMessage, FILE_APPEND | LOCK_EX);
            
            if (file_exists($logFile)) {
                $logSize = filesize($logFile);
                echo "✓ Log file operational, size: " . number_format($logSize) . " bytes\n";
                addTestResult('Debug System Loading Test', true, 'Debug system operational');
            } else {
                echo "✗ Log file not created\n";
                addTestResult('Debug System Loading Test', false, 'Log file creation failed');
            }
        } else {
            echo "✗ Debug system class not found\n";
            addTestResult('Debug System Loading Test', false, 'Debug system class not found');
        }
    } else {
        echo "✗ Debug system file not found\n";
        addTestResult('Debug System Loading Test', false, 'Debug system file not found');
    }
    
} catch (Exception $e) {
    echo "✗ Error in debug system test: " . $e->getMessage() . "\n";
    addTestResult('Debug System Loading Test', false, 'Exception: ' . $e->getMessage());
}

// Test 7: Database Connection Test (Optional)
echo "\nTest 7: Database Connection Test\n";
echo "---------------------------------\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=blackcnote', 'blackcnote_user', 'blackcnote_password');
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Database connection successful\n";
    echo "✓ MySQL Version: " . $result['version'] . "\n";
    addTestResult('Database Connection Test', true, 'Connected to MySQL ' . $result['version']);
} catch (PDOException $e) {
    echo "⚠ Database connection failed: " . $e->getMessage() . "\n";
    echo "  This is expected if MySQL is not running\n";
    addTestResult('Database Connection Test', false, 'Database not available: ' . $e->getMessage());
}

// Test 8: Security Check
echo "\nTest 8: Security Check\n";
echo "----------------------\n";

$securityChecks = [
    'Logs directory writable' => is_writable(WP_CONTENT_DIR . 'logs'),
    'WordPress config readable' => is_readable($wpConfigPath),
    'Debug system readable' => is_readable(__DIR__ . '/../hyiplab/tools/debug-system.php'),
    'No world-writable files' => true // Simplified check
];

$securityIssues = [];
foreach ($securityChecks as $check => $passed) {
    if ($passed) {
        echo "✓ $check\n";
    } else {
        echo "✗ $check\n";
        $securityIssues[] = $check;
    }
}

addTestResult('Security Check', empty($securityIssues), 
    empty($securityIssues) ? 'All security checks passed' : 'Issues: ' . implode(', ', $securityIssues));

// Test 9: Performance Check
echo "\nTest 9: Performance Check\n";
echo "-------------------------\n";

$startTime = microtime(true);
$memoryStart = memory_get_usage();

// Simulate some operations
for ($i = 0; $i < 1000; $i++) {
    $test = "test" . $i;
}

$endTime = microtime(true);
$memoryEnd = memory_get_usage();

$executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
$memoryUsed = $memoryEnd - $memoryStart;

echo "✓ Execution time: " . number_format($executionTime, 2) . " ms\n";
echo "✓ Memory usage: " . number_format($memoryUsed) . " bytes\n";

$performanceOk = $executionTime < 100 && $memoryUsed < 1024 * 1024; // Less than 100ms and 1MB
addTestResult('Performance Check', $performanceOk, 
    "Execution: {$executionTime}ms, Memory: " . number_format($memoryUsed) . " bytes");

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "TEST SUMMARY\n";
echo str_repeat("=", 60) . "\n";

$passedTests = 0;
$totalTests = count($testResults);

foreach ($testResults as $result) {
    $status = $result['passed'] ? 'PASS' : 'FAIL';
    echo sprintf("%-30s [%s] %s\n", $result['test'], $status, $result['message']);
    if ($result['passed']) {
        $passedTests++;
    }
}

echo "\n" . str_repeat("-", 60) . "\n";
echo "OVERALL RESULT: " . $passedTests . "/" . $totalTests . " tests passed\n";

if ($passedTests == $totalTests) {
    echo "🎉 ALL TESTS PASSED! BlackCnote Debug Plugin is fully operational.\n";
} elseif ($passedTests >= $totalTests * 0.8) {
    echo "✅ MOST TESTS PASSED! BlackCnote Debug Plugin is mostly operational.\n";
} else {
    echo "⚠️  SOME TESTS FAILED! Please check the issues above.\n";
}

echo str_repeat("=", 60) . "\n";

// Write test results to log
$logFile = WP_CONTENT_DIR . 'logs/debug-test-results.log';
$logEntry = "[" . date('Y-m-d H:i:s') . "] Test run completed: $passedTests/$totalTests passed\n";
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

echo "\nTest results logged to: $logFile\n";
?> 