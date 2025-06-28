<?php
// Test database connection
try {
    $pdo = new PDO('mysql:host=mysql;dbname=blackcnote', 'root', 'blackcnote_password');
    echo "Database connection successful\n";
    
    // Test if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . count($tables) . "\n";
    
    if (count($tables) > 0) {
        echo "First few tables: " . implode(', ', array_slice($tables, 0, 5)) . "\n";
    }
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}

// Test WordPress configuration
echo "\nTesting WordPress configuration:\n";
if (file_exists('/var/www/html/wp-config.php')) {
    echo "wp-config.php exists\n";
    
    // Test if we can include wp-config.php
    try {
        // Define a test constant to avoid conflicts
        if (!defined('WP_TESTS_DOMAIN')) {
            define('WP_TESTS_DOMAIN', 'test');
        }
        
        // Include wp-config.php
        include_once '/var/www/html/wp-config.php';
        echo "wp-config.php loaded successfully\n";
        
        // Check if WordPress constants are defined
        if (defined('DB_NAME')) {
            echo "DB_NAME: " . DB_NAME . "\n";
        }
        if (defined('DB_HOST')) {
            echo "DB_HOST: " . DB_HOST . "\n";
        }
        
    } catch (Exception $e) {
        echo "Error loading wp-config.php: " . $e->getMessage() . "\n";
    }
} else {
    echo "wp-config.php not found\n";
}
?> 