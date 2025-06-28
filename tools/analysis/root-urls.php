<?php
/**
 * Update WordPress URLs to root for Docker setup
 * Access this file through: http://localhost:8888/update-urls-root.php
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "<h2>🔧 WordPress URL Update - Root Installation</h2>";

// Get current values
$current_home = get_option('home');
$current_siteurl = get_option('siteurl');

echo "<p><strong>Current Database Values:</strong></p>";
echo "<ul>";
echo "<li>Home URL: " . htmlspecialchars($current_home) . "</li>";
echo "<li>Site URL: " . htmlspecialchars($current_siteurl) . "</li>";
echo "</ul>";

if ($_POST['action'] === 'update_urls') {
    try {
        // Update home URL to root
        $home_updated = update_option('home', 'http://localhost:8888');
        
        // Update site URL to root
        $siteurl_updated = update_option('siteurl', 'http://localhost:8888');
        
        // Clear any cached options
        wp_cache_flush();
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>✅ URLs Updated Successfully!</h3>";
        echo "<p><strong>New Values:</strong></p>";
        echo "<ul>";
        echo "<li>Home URL: " . get_option('home') . "</li>";
        echo "<li>Site URL: " . get_option('siteurl') . "</li>";
        echo "</ul>";
        echo "<p><strong>Next Steps:</strong></p>";
        echo "<ol>";
        echo "<li>Visit <a href='http://localhost:8888/' target='_blank'>http://localhost:8888/</a> to see your site</li>";
        echo "<li>Visit <a href='http://localhost:8888/wp-admin/' target='_blank'>http://localhost:8888/wp-admin/</a> to access admin</li>";
        echo "<li>Delete this file for security</li>";
        echo "</ol>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>❌ Error Updating URLs</h3>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
} else {
    echo "<form method='post' style='margin: 20px 0;'>";
    echo "<input type='hidden' name='action' value='update_urls'>";
    echo "<button type='submit' style='background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "🔄 Update URLs to Root (http://localhost:8888)";
    echo "</button>";
    echo "</form>";
    
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>⚠️ Important</h3>";
    echo "<p>This will update your WordPress URLs from the subdirectory to the root path.</p>";
    echo "<p>After updating, your site will be accessible at:</p>";
    echo "<ul>";
    echo "<li><strong>Frontend:</strong> <a href='http://localhost:8888/' target='_blank'>http://localhost:8888/</a></li>";
    echo "<li><strong>Admin:</strong> <a href='http://localhost:8888/wp-admin/' target='_blank'>http://localhost:8888/wp-admin/</a></li>";
    echo "</ul>";
    echo "</div>";
}
?> 