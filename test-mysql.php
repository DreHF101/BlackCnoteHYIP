<?php
try {
    $pdo = new PDO('mysql:host=mysql;dbname=blackcnote', 'root', 'blackcnote_password');
    echo "✅ MySQL connection successful from WordPress container\n";
    
    // Test a simple query
    $stmt = $pdo->query('SELECT 1 as test, DATABASE() as current_db');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Query test successful: " . json_encode($result) . "\n";
    
} catch (Exception $e) {
    echo "❌ MySQL connection failed: " . $e->getMessage() . "\n";
}
?> 