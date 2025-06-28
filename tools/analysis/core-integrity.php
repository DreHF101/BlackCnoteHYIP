<?php
/**
 * WordPress Core Integrity Check
 * Access via: http://localhost:8888/core-integrity-check.php
 * DELETE THIS FILE AFTER USE!
 */

echo "<h2>WordPress Core Integrity Check</h2>";

// Check if WordPress is loading correctly
if (!defined('ABSPATH')) {
    echo "<p style='color:red'>❌ WordPress core is not loading correctly. ABSPATH not defined.</p>";
} else {
    echo "<p style='color:green'>✅ WordPress core is loading correctly. ABSPATH: " . ABSPATH . "</p>";
}

// Check essential files
$essential_files = [
    'wp-config.php',
    'wp-load.php',
    'wp-settings.php',
    'wp-includes/wp-db.php',
    'wp-includes/load.php',
    'wp-includes/plugin.php',
    'wp-includes/formatting.php',
    'wp-includes/query.php',
    'wp-includes/rewrite.php'
];

echo "<h3>Essential File Check:</h3><ul>";
foreach ($essential_files as $file) {
    if (file_exists(ABSPATH . $file)) {
        echo "<li style='color:green'>✅ $file</li>";
    } else {
        echo "<li style='color:red'>❌ $file (MISSING)</li>";
    }
}
echo "</ul>";

// Check for redirect loops in core files
echo "<h3>Redirect Loop Detection:</h3>";
$redirect_files = [
    'wp-includes/load.php',
    'wp-includes/query.php',
    'wp-includes/rewrite.php'
];

foreach ($redirect_files as $file) {
    if (file_exists(ABSPATH . $file)) {
        $content = file_get_contents(ABSPATH . $file);
        if (strpos($content, 'wp_redirect') !== false) {
            echo "<p style='color:orange'>⚠️ $file contains redirect logic</p>";
        }
    }
}

// Check .htaccess
echo "<h3>.htaccess Check:</h3>";
if (file_exists(ABSPATH . '.htaccess')) {
    $htaccess = file_get_contents(ABSPATH . '.htaccess');
    if (strpos($htaccess, 'RewriteBase /') !== false) {
        echo "<p style='color:green'>✅ .htaccess has correct RewriteBase</p>";
    } else {
        echo "<p style='color:red'>❌ .htaccess may have incorrect RewriteBase</p>";
    }
} else {
    echo "<p style='color:red'>❌ .htaccess file missing</p>";
}

echo "<h3>Recommendation:</h3>";
echo "<p>If all files are present and .htaccess is correct, the issue may be:</p>";
echo "<ul>";
echo "<li>A server-level configuration issue</li>";
echo "<li>A corrupted database that needs a fresh install</li>";
echo "<li>A fundamental WordPress configuration problem</li>";
echo "</ul>";
echo "<p><strong>Next step:</strong> Consider a fresh WordPress install with a new database to isolate the issue.</p>"; 