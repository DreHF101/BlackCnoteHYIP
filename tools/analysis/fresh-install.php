<?php
/**
 * Fresh WordPress Install Helper
 * Access via: http://localhost:8888/fresh-install.php
 * DELETE THIS FILE AFTER USE!
 */

echo "<h2>Fresh WordPress Install Helper</h2>";

// Check if we can connect to database
global $wpdb;
if ($wpdb->db_connect()) {
    echo "<p style='color:green'>✅ Database connection successful</p>";
    
    // Backup current options
    $options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name IN ('home', 'siteurl', 'blogname', 'blogdescription')");
    
    echo "<h3>Current WordPress Settings:</h3><ul>";
    foreach ($options as $opt) {
        echo "<li><strong>{$opt->option_name}:</strong> " . htmlspecialchars($opt->option_value) . "</li>";
    }
    echo "</ul>";
    
    echo "<h3>Recommended Fresh Install Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Backup your current database:</strong><br>";
    echo "docker-compose exec mysql mysqldump -u root -pblackcnote_password blackcnote > backup_$(date +%Y%m%d_%H%M%S).sql</li>";
    echo "<li><strong>Create fresh database:</strong><br>";
    echo "docker-compose exec mysql mysql -u root -pblackcnote_password -e 'DROP DATABASE blackcnote; CREATE DATABASE blackcnote;'</li>";
    echo "<li><strong>Download fresh WordPress:</strong><br>";
    echo "wget https://wordpress.org/latest.zip && unzip latest.zip</li>";
    echo "<li><strong>Replace core files:</strong><br>";
    echo "Copy fresh WordPress files to blackcnote/ directory (excluding wp-content and wp-config.php)</li>";
    echo "<li><strong>Run WordPress installation:</strong><br>";
    echo "Visit http://localhost:8888/ to complete fresh install</li>";
    echo "</ol>";
    
    echo "<h3>Alternative Quick Fix:</h3>";
    echo "<p>If you want to try one more automated fix, click the button below to reset all WordPress options to defaults:</p>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='reset' value='1'>";
    echo "<button type='submit' style='background:red;color:white;padding:10px;'>Reset All WordPress Options to Defaults</button>";
    echo "</form>";
    
} else {
    echo "<p style='color:red'>❌ Cannot connect to database</p>";
}

if (isset($_POST['reset'])) {
    // Reset critical options to defaults
    update_option('home', 'http://localhost:8888');
    update_option('siteurl', 'http://localhost:8888');
    update_option('rewrite_rules', '');
    
    // Clear any cached options
    wp_cache_flush();
    
    echo "<p style='color:green'>✅ WordPress options reset to defaults. Try accessing your site now.</p>";
}
?> 