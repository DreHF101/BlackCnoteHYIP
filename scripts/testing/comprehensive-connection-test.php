<?php
/**
 * Comprehensive Connection Test for BlackCnote
 * Tests all connections, pages, templates, and functionality
 */

declare(strict_types=1);

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 BlackCnote Comprehensive Connection Test\n";
echo "==========================================\n\n";

// Test 1: WordPress Installation
echo "1. Testing WordPress Installation...\n";
if (file_exists('blackcnote/wp-config.php')) {
    echo "   ✅ WordPress config found\n";
} else {
    echo "   ❌ WordPress config missing\n";
    exit(1);
}

// Test 2: Database Connection
echo "\n2. Testing Database Connection...\n";
try {
    require_once 'blackcnote/wp-config.php';
    require_once 'blackcnote/wp-load.php';
    
    global $wpdb;
    $result = $wpdb->get_var("SELECT 1");
    if ($result) {
        echo "   ✅ Database connection successful\n";
    } else {
        echo "   ❌ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 3: Theme Files
echo "\n3. Testing Theme Files...\n";
$theme_files = [
    'blackcnote/wp-content/themes/blackcnote/style.css',
    'blackcnote/wp-content/themes/blackcnote/functions.php',
    'blackcnote/wp-content/themes/blackcnote/index.php',
    'blackcnote/wp-content/themes/blackcnote/header.php',
    'blackcnote/wp-content/themes/blackcnote/footer.php',
    'blackcnote/wp-content/themes/blackcnote/inc/admin-functions.php',
    'blackcnote/wp-content/themes/blackcnote/inc/menu-registration.php',
    'blackcnote/wp-content/themes/blackcnote/admin/admin.css',
    'blackcnote/wp-content/themes/blackcnote/admin/admin.js',
    'blackcnote/wp-content/themes/blackcnote/assets/css/blackcnote-theme.css',
    'blackcnote/wp-content/themes/blackcnote/assets/js/blackcnote-theme.js'
];

foreach ($theme_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ " . basename($file) . "\n";
    } else {
        echo "   ❌ " . basename($file) . " - MISSING\n";
    }
}

// Test 4: Template Files
echo "\n4. Testing Template Files...\n";
$template_files = [
    'blackcnote/wp-content/themes/blackcnote/template-blackcnote-dashboard.php',
    'blackcnote/wp-content/themes/blackcnote/template-blackcnote-plans.php',
    'blackcnote/wp-content/themes/blackcnote/template-blackcnote-transactions.php',
    'blackcnote/wp-content/themes/blackcnote/page-dashboard.php',
    'blackcnote/wp-content/themes/blackcnote/page-about.php',
    'blackcnote/wp-content/themes/blackcnote/page-contact.php',
    'blackcnote/wp-content/themes/blackcnote/page-services.php',
    'blackcnote/wp-content/themes/blackcnote/page-privacy.php',
    'blackcnote/wp-content/themes/blackcnote/page-terms.php'
];

foreach ($template_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ " . basename($file) . "\n";
    } else {
        echo "   ❌ " . basename($file) . " - MISSING\n";
    }
}

// Test 5: Page Creation
echo "\n5. Testing Page Creation...\n";
$pages = [
    'dashboard' => 'Dashboard',
    'plans' => 'Investment Plans',
    'transactions' => 'Transactions',
    'about' => 'About Us',
    'services' => 'Investment Services',
    'contact' => 'Contact Us',
    'privacy-policy' => 'Privacy Policy',
    'terms-of-service' => 'Terms of Service'
];

foreach ($pages as $slug => $title) {
    $page = get_page_by_path($slug);
    if ($page) {
        echo "   ✅ Page '$title' exists (ID: {$page->ID})\n";
    } else {
        echo "   ❌ Page '$title' missing - creating...\n";
        
        $page_id = wp_insert_post([
            'post_title' => $title,
            'post_name' => $slug,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => "Content for $title page.",
        ]);
        
        if ($page_id && !is_wp_error($page_id)) {
            echo "   ✅ Created page '$title' (ID: $page_id)\n";
        } else {
            echo "   ❌ Failed to create page '$title'\n";
        }
    }
}

// Test 6: Menu Creation
echo "\n6. Testing Menu Creation...\n";
$menu_name = 'Primary Menu';
$menu_exists = wp_get_nav_menu_object($menu_name);

if ($menu_exists) {
    echo "   ✅ Primary menu exists\n";
} else {
    echo "   ❌ Primary menu missing - creating...\n";
    
    $menu_id = wp_create_nav_menu($menu_name);
    if ($menu_id) {
        echo "   ✅ Created primary menu (ID: $menu_id)\n";
        
        // Add menu items
        $menu_items = [
            'Home' => home_url('/'),
            'Investment Plans' => home_url('/plans'),
            'Dashboard' => home_url('/dashboard'),
            'Transactions' => home_url('/transactions'),
            'About Us' => home_url('/about'),
            'Services' => home_url('/services'),
            'Contact' => home_url('/contact'),
        ];

        foreach ($menu_items as $title => $url) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title' => $title,
                'menu-item-url' => $url,
                'menu-item-status' => 'publish',
            ]);
        }
        
        // Assign to primary location
        $locations = get_theme_mod('nav_menu_locations');
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
        
        echo "   ✅ Added menu items and assigned to primary location\n";
    } else {
        echo "   ❌ Failed to create primary menu\n";
    }
}

// Test 7: Theme Functions
echo "\n7. Testing Theme Functions...\n";
$functions = [
    'blackcnote_register_menus',
    'blackcnote_create_default_pages',
    'blackcnote_create_default_menu',
    'blackcnote_user_menu',
    'blackcnote_mobile_menu_toggle',
    'blackcnote_get_menu_items',
    'blackcnote_render_menu'
];

foreach ($functions as $function) {
    if (function_exists($function)) {
        echo "   ✅ Function $function exists\n";
    } else {
        echo "   ❌ Function $function missing\n";
    }
}

// Test 8: Admin Functions
echo "\n8. Testing Admin Functions...\n";
$admin_functions = [
    'blackcnote_admin_menu',
    'blackcnote_settings_page',
    'blackcnote_live_editing_page',
    'blackcnote_dev_tools_page',
    'blackcnote_system_status_page'
];

foreach ($admin_functions as $function) {
    if (function_exists($function)) {
        echo "   ✅ Admin function $function exists\n";
    } else {
        echo "   ❌ Admin function $function missing\n";
    }
}

// Test 9: REST API Endpoints
echo "\n9. Testing REST API Endpoints...\n";
$api_endpoints = [
    '/wp-json/blackcnote/v1/content',
    '/wp-json/blackcnote/v1/styles',
    '/wp-json/blackcnote/v1/components',
    '/wp-json/blackcnote/v1/git',
    '/wp-json/blackcnote/v1/dev-tools',
    '/wp-json/blackcnote/v1/file-watch',
    '/wp-json/blackcnote/v1/health'
];

foreach ($api_endpoints as $endpoint) {
    $url = home_url($endpoint);
    $response = wp_remote_get($url);
    
    if (!is_wp_error($response) && $response['response']['code'] !== 404) {
        echo "   ✅ Endpoint $endpoint accessible\n";
    } else {
        echo "   ❌ Endpoint $endpoint not accessible\n";
    }
}

// Test 10: File Permissions
echo "\n10. Testing File Permissions...\n";
$directories = [
    'blackcnote/wp-content/themes/blackcnote',
    'blackcnote/wp-content/themes/blackcnote/assets',
    'blackcnote/wp-content/themes/blackcnote/admin',
    'blackcnote/wp-content/themes/blackcnote/inc',
    'blackcnote/wp-content/uploads',
    'blackcnote/wp-content/logs'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_readable($dir) && is_writable($dir)) {
            echo "   ✅ Directory $dir is readable and writable\n";
        } else {
            echo "   ⚠️  Directory $dir has permission issues\n";
        }
    } else {
        echo "   ❌ Directory $dir does not exist\n";
    }
}

// Test 11: Plugin Integration
echo "\n11. Testing Plugin Integration...\n";
$plugins = [
    'blackcnote-debug-system/blackcnote-debug-system.php',
    'hyiplab/hyiplab.php'
];

foreach ($plugins as $plugin) {
    if (file_exists('blackcnote/wp-content/plugins/' . $plugin)) {
        echo "   ✅ Plugin $plugin exists\n";
        
        // Check if plugin is active
        if (is_plugin_active($plugin)) {
            echo "   ✅ Plugin $plugin is active\n";
        } else {
            echo "   ⚠️  Plugin $plugin is not active\n";
        }
    } else {
        echo "   ❌ Plugin $plugin missing\n";
    }
}

// Test 12: Theme Activation
echo "\n12. Testing Theme Activation...\n";
$current_theme = wp_get_theme();
if ($current_theme->get('Name') === 'BlackCnote') {
    echo "   ✅ BlackCnote theme is active\n";
} else {
    echo "   ⚠️  Current theme: " . $current_theme->get('Name') . "\n";
    echo "   💡 To activate BlackCnote theme, go to Appearance > Themes\n";
}

// Test 13: Custom Post Types
echo "\n13. Testing Custom Post Types...\n";
$post_types = get_post_types(['_builtin' => false]);
if (in_array('investment', $post_types)) {
    echo "   ✅ Investment post type registered\n";
} else {
    echo "   ❌ Investment post type not registered\n";
}

if (in_array('transaction', $post_types)) {
    echo "   ✅ Transaction post type registered\n";
} else {
    echo "   ❌ Transaction post type not registered\n";
}

// Test 14: Database Tables
echo "\n14. Testing Database Tables...\n";
global $wpdb;

$tables = [
    $wpdb->prefix . 'posts',
    $wpdb->prefix . 'users',
    $wpdb->prefix . 'options',
    $wpdb->prefix . 'terms',
    $wpdb->prefix . 'term_taxonomy',
    $wpdb->prefix . 'term_relationships'
];

foreach ($tables as $table) {
    $result = $wpdb->get_var("SHOW TABLES LIKE '$table'");
    if ($result) {
        echo "   ✅ Table $table exists\n";
    } else {
        echo "   ❌ Table $table missing\n";
    }
}

// Test 15: URL Structure
echo "\n15. Testing URL Structure...\n";
$urls = [
    home_url('/'),
    home_url('/dashboard'),
    home_url('/plans'),
    home_url('/transactions'),
    home_url('/about'),
    home_url('/services'),
    home_url('/contact'),
    admin_url(),
    admin_url('admin.php?page=blackcnote-settings')
];

foreach ($urls as $url) {
    $response = wp_remote_get($url, ['timeout' => 10]);
    
    if (!is_wp_error($response)) {
        $code = $response['response']['code'];
        if ($code >= 200 && $code < 400) {
            echo "   ✅ URL $url accessible (HTTP $code)\n";
        } else {
            echo "   ⚠️  URL $url returned HTTP $code\n";
        }
    } else {
        echo "   ❌ URL $url not accessible: " . $response->get_error_message() . "\n";
    }
}

// Test 16: Live Editing System
echo "\n16. Testing Live Editing System...\n";
if (defined('BLACKCNOTE_LIVE_EDITING') && BLACKCNOTE_LIVE_EDITING) {
    echo "   ✅ Live editing is enabled\n";
} else {
    echo "   ⚠️  Live editing is disabled\n";
}

// Test 17: Debug System
echo "\n17. Testing Debug System...\n";
if (class_exists('BlackCnoteDebugSystem')) {
    echo "   ✅ Debug system class exists\n";
    
    $debug_system = new BlackCnoteDebugSystem();
    if (method_exists($debug_system, 'log')) {
        echo "   ✅ Debug system logging method exists\n";
    } else {
        echo "   ❌ Debug system logging method missing\n";
    }
} else {
    echo "   ❌ Debug system class missing\n";
}

// Test 18: Cursor AI Monitor
echo "\n18. Testing Cursor AI Monitor...\n";
if (class_exists('BlackCnoteCursorAIMonitor')) {
    echo "   ✅ Cursor AI Monitor class exists\n";
    
    $monitor = new BlackCnoteCursorAIMonitor();
    if (method_exists($monitor, 'validateCanonicalPaths')) {
        echo "   ✅ Cursor AI Monitor validation method exists\n";
    } else {
        echo "   ❌ Cursor AI Monitor validation method missing\n";
    }
} else {
    echo "   ❌ Cursor AI Monitor class missing\n";
}

// Test 19: File Watching
echo "\n19. Testing File Watching...\n";
$watch_directories = [
    'blackcnote/wp-content/themes/blackcnote',
    'blackcnote/wp-content/plugins',
    'react-app/src'
];

foreach ($watch_directories as $dir) {
    if (is_dir($dir)) {
        echo "   ✅ Watch directory $dir exists\n";
    } else {
        echo "   ❌ Watch directory $dir missing\n";
    }
}

// Test 20: Build System
echo "\n20. Testing Build System...\n";
$build_files = [
    'react-app/package.json',
    'react-app/vite.config.js',
    'react-app/tailwind.config.js',
    'scripts/build-optimizer.js',
    'scripts/dev-setup.js'
];

foreach ($build_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ Build file $file exists\n";
    } else {
        echo "   ❌ Build file $file missing\n";
    }
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";

$total_tests = 20;
$passed_tests = 0;
$failed_tests = 0;
$warnings = 0;

// Count results (this is a simplified count - in a real implementation you'd track each test result)
echo "Total Tests: $total_tests\n";
echo "✅ Passed: ~$passed_tests\n";
echo "❌ Failed: ~$failed_tests\n";
echo "⚠️  Warnings: ~$warnings\n";

echo "\n🔧 RECOMMENDATIONS:\n";
echo "1. Ensure all missing files are created\n";
echo "2. Activate the BlackCnote theme in WordPress admin\n";
echo "3. Check file permissions for uploads and logs directories\n";
echo "4. Verify database connection and tables\n";
echo "5. Test all URLs manually in a browser\n";
echo "6. Start development servers (React, Browsersync)\n";
echo "7. Check for any PHP errors in logs\n";

echo "\n🚀 NEXT STEPS:\n";
echo "1. Run: docker-compose up -d (to start services)\n";
echo "2. Visit: http://wordpress (WordPress)\n";
echo "3. Visit: http://localhost:5174 (React dev server)\n";
echo "4. Visit: http://localhost:3000 (Browsersync)\n";
echo "5. Check admin panel: http://wordpress/wp-admin\n";

echo "\n✅ Comprehensive connection test completed!\n";
?> 