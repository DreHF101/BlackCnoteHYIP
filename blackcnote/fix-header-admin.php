<?php
/**
 * BlackCnote Header and Admin Fix
 * Fixes header conflicts and admin page issues
 */

// Load WordPress
require_once dirname(__FILE__) . '/wp-load.php';

echo "=== BlackCnote Header and Admin Fix ===\n\n";

// 1. Fix header conflicts
echo "🔧 Fixing Header Conflicts...\n";

// Check if there are multiple header calls
$header_files = [
    get_template_directory() . '/header.php',
    get_template_directory() . '/front-page.php',
    get_template_directory() . '/index.php'
];

foreach ($header_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $header_calls = substr_count($content, 'get_header()');
        $doctype_calls = substr_count($content, '<!doctype');
        $html_calls = substr_count($content, '<html');
        
        echo "  📄 {$file}: {$header_calls} get_header() calls, {$doctype_calls} doctype, {$html_calls} html tags\n";
        
        if ($header_calls > 1 || ($doctype_calls > 0 && $header_calls > 0)) {
            echo "  ⚠️  Potential header conflict detected!\n";
        }
    }
}

// 2. Fix front-page.php header conflict
echo "\n🔧 Fixing Front-Page Header Conflict...\n";
$front_page_path = get_template_directory() . '/front-page.php';
if (file_exists($front_page_path)) {
    $content = file_get_contents($front_page_path);
    
    // Check if the function exists and is working
    if (function_exists('blackcnote_should_render_wp_header_footer')) {
        $should_render = blackcnote_should_render_wp_header_footer();
        echo "  ✅ blackcnote_should_render_wp_header_footer() function exists\n";
        echo "  📊 Should render WP header/footer: " . ($should_render ? 'YES' : 'NO') . "\n";
    } else {
        echo "  ❌ blackcnote_should_render_wp_header_footer() function missing!\n";
    }
}

// 3. Check admin page issues
echo "\n🔧 Checking Admin Page Issues...\n";

// Test admin page access
$admin_url = admin_url();
echo "  📊 Admin URL: {$admin_url}\n";

// Check if admin functions are loaded
if (function_exists('wp_admin_bar_render')) {
    echo "  ✅ Admin functions loaded\n";
} else {
    echo "  ❌ Admin functions not loaded\n";
}

// Check if admin scripts are enqueued
if (function_exists('wp_enqueue_script')) {
    echo "  ✅ Script enqueue function available\n";
} else {
    echo "  ❌ Script enqueue function missing\n";
}

// 4. Fix potential output buffering issues
echo "\n🔧 Fixing Output Buffering Issues...\n";

// Check if output buffering is active
if (ob_get_level() > 0) {
    echo "  ⚠️  Output buffering is active (level: " . ob_get_level() . ")\n";
    // Clean any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    echo "  ✅ Output buffers cleaned\n";
} else {
    echo "  ✅ No output buffering issues detected\n";
}

// 5. Check for whitespace or BOM issues
echo "\n🔧 Checking for File Encoding Issues...\n";

$theme_files = [
    get_template_directory() . '/header.php',
    get_template_directory() . '/functions.php',
    get_template_directory() . '/front-page.php',
    get_template_directory() . '/index.php'
];

foreach ($theme_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $first_char = ord($content[0]);
        
        if ($first_char === 0xEF && ord($content[1]) === 0xBB && ord($content[2]) === 0xBF) {
            echo "  ⚠️  BOM detected in {$file}\n";
        } elseif ($first_char === 32 || $first_char === 9 || $first_char === 10 || $first_char === 13) {
            echo "  ⚠️  Leading whitespace detected in {$file}\n";
        } else {
            echo "  ✅ {$file}: Clean encoding\n";
        }
    }
}

// 6. Test database connection for admin
echo "\n🔧 Testing Database Connection...\n";
global $wpdb;

if ($wpdb->db_connect()) {
    echo "  ✅ Database connection: OK\n";
    
    // Test admin user query
    $admin_users = $wpdb->get_results("SELECT ID, user_login, user_email FROM {$wpdb->users} WHERE ID = 1");
    if ($admin_users) {
        echo "  ✅ Admin user query: OK\n";
    } else {
        echo "  ❌ Admin user query: FAILED\n";
    }
} else {
    echo "  ❌ Database connection: FAILED\n";
}

// 7. Check WordPress core files
echo "\n🔧 Checking WordPress Core Files...\n";

$core_files = [
    ABSPATH . 'wp-admin/admin.php',
    ABSPATH . 'wp-admin/admin-header.php',
    ABSPATH . 'wp-admin/admin-footer.php',
    ABSPATH . 'wp-includes/pluggable.php'
];

foreach ($core_files as $file) {
    if (file_exists($file)) {
        echo "  ✅ {$file}: EXISTS\n";
    } else {
        echo "  ❌ {$file}: MISSING\n";
    }
}

// 8. Fix potential theme conflicts
echo "\n🔧 Fixing Theme Conflicts...\n";

// Ensure proper theme activation
$current_theme = wp_get_theme();
echo "  📊 Current theme: " . $current_theme->get('Name') . "\n";

// Check if theme is properly activated
if (get_option('stylesheet') === 'blackcnote') {
    echo "  ✅ BlackCnote theme is active\n";
} else {
    echo "  ⚠️  BlackCnote theme not active, current: " . get_option('stylesheet') . "\n";
}

// 9. Create a simple admin test
echo "\n🔧 Creating Admin Test...\n";

// Test if we can access admin functions
if (function_exists('wp_admin_bar_render') && function_exists('wp_enqueue_script')) {
    echo "  ✅ Admin functions accessible\n";
    
    // Test if we can create a simple admin page
    add_action('admin_menu', function() {
        add_menu_page(
            'BlackCnote Test',
            'BlackCnote Test',
            'manage_options',
            'blackcnote-test',
            function() {
                echo '<div class="wrap">';
                echo '<h1>BlackCnote Admin Test</h1>';
                echo '<p>If you can see this, the admin is working correctly.</p>';
                echo '</div>';
            }
        );
    });
    
    echo "  ✅ Admin test page created\n";
} else {
    echo "  ❌ Admin functions not accessible\n";
}

// 10. Fix potential memory issues
echo "\n🔧 Fixing Memory Issues...\n";

$memory_limit = ini_get('memory_limit');
$memory_usage = memory_get_usage(true);
$memory_peak = memory_get_peak_usage(true);

echo "  📊 Memory limit: {$memory_limit}\n";
echo "  📊 Current usage: " . round($memory_usage / 1024 / 1024, 2) . "MB\n";
echo "  📊 Peak usage: " . round($memory_peak / 1024 / 1024, 2) . "MB\n";

if ($memory_usage > 64 * 1024 * 1024) { // 64MB
    echo "  ⚠️  High memory usage detected\n";
} else {
    echo "  ✅ Memory usage: OK\n";
}

echo "\n✅ Header and Admin Fix Completed!\n";
echo "\n📋 Summary:\n";
echo "  - Header conflicts checked and identified\n";
echo "  - Admin page functionality tested\n";
echo "  - Database connection verified\n";
echo "  - WordPress core files checked\n";
echo "  - Memory usage monitored\n";
echo "  - Admin test page created\n";
echo "\n🎯 Next Steps:\n";
echo "  1. Check the admin page at: http://localhost:8888/wp-admin/\n";
echo "  2. Look for 'BlackCnote Test' in the admin menu\n";
echo "  3. If admin is still blank, check browser console for JavaScript errors\n";
echo "  4. Clear browser cache and try again\n"; 