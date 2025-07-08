<?php
// Test database connectivity and WordPress configuration
echo "<h1>BlackCnote Database Test</h1>";

// Test 1: Check if wp-config.php can be loaded
echo "<h2>Test 1: WordPress Configuration</h2>";
if (file_exists('wp-config.php')) {
    echo "✅ wp-config.php exists<br>";
    
    // Load WordPress configuration
    require_once('wp-config.php');
    
    echo "✅ WordPress configuration loaded<br>";
    echo "Database Name: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "<br>";
    echo "Database User: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "<br>";
    echo "Database Host: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "<br>";
    echo "WP_DEBUG: " . (defined('WP_DEBUG') ? (WP_DEBUG ? 'TRUE' : 'FALSE') : 'NOT DEFINED') . "<br>";
} else {
    echo "❌ wp-config.php not found<br>";
}

// Test 2: Test database connection
echo "<h2>Test 2: Database Connection</h2>";
if (defined('DB_NAME') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_HOST')) {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Database connection successful<br>";
        
        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Database query successful. Tables found: " . $result['count'] . "<br>";
        
    } catch (PDOException $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Database constants not defined<br>";
}

// Test 3: Check WordPress core files
echo "<h2>Test 3: WordPress Core Files</h2>";
$required_files = ['wp-load.php', 'wp-settings.php', 'wp-includes/wp-db.php'];
foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 4: Check permissions
echo "<h2>Test 4: File Permissions</h2>";
$test_dirs = ['wp-content', 'wp-content/uploads', 'wp-content/plugins'];
foreach ($test_dirs as $dir) {
    if (is_dir($dir)) {
        if (is_readable($dir)) {
            echo "✅ $dir is readable<br>";
        } else {
            echo "❌ $dir is not readable<br>";
        }
        if (is_writable($dir)) {
            echo "✅ $dir is writable<br>";
        } else {
            echo "❌ $dir is not writable<br>";
        }
    } else {
        echo "❌ $dir does not exist<br>";
    }
}

// Test 5: PHP Info
echo "<h2>Test 5: PHP Configuration</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "<br>";
echo "Display Errors: " . (ini_get('display_errors') ? 'ON' : 'OFF') . "<br>";
echo "Error Reporting: " . ini_get('error_reporting') . "<br>";

echo "<h2>Test Complete</h2>";
?> 