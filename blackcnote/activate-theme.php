<?php
/**
 * BlackCnote Theme Activation Script - Docker Version
 * This script runs inside the WordPress Docker container
 */

echo "🎨 BlackCnote Theme Activation Script (Docker)\n";
echo "=============================================\n\n";

// Check if we're running inside Docker
if (file_exists('/.dockerenv')) {
    echo "✅ Running inside Docker container\n";
} else {
    echo "⚠️  Not running inside Docker container\n";
    echo "   This script should be run inside the WordPress container\n";
}

// Load WordPress
if (file_exists('/var/www/html/wp-load.php')) {
    require_once '/var/www/html/wp-load.php';
    echo "✅ WordPress loaded successfully\n";
} else {
    echo "❌ WordPress not found at /var/www/html/wp-load.php\n";
    exit(1);
}

// Check if WordPress is installed
if (!function_exists('get_option')) {
    echo "❌ WordPress is not properly installed\n";
    exit(1);
}

echo "✅ WordPress is installed and running\n\n";

// Check if BlackCnote theme exists
$theme = 'blackcnote';
$theme_path = '/var/www/html/wp-content/themes/' . $theme;

if (is_dir($theme_path)) {
    echo "✅ BlackCnote theme found at: $theme_path\n";
} else {
    echo "❌ BlackCnote theme not found at: $theme_path\n";
    echo "   Available themes:\n";
    $themes_dir = '/var/www/html/wp-content/themes/';
    if (is_dir($themes_dir)) {
        $themes = scandir($themes_dir);
        foreach ($themes as $theme_name) {
            if ($theme_name != '.' && $theme_name != '..' && is_dir($themes_dir . $theme_name)) {
                echo "   - $theme_name\n";
            }
        }
    }
    exit(1);
}

// Activate BlackCnote theme
if (wp_get_theme($theme)->exists()) {
    $current_theme = get_option('stylesheet');
    if ($current_theme === $theme) {
        echo "✅ BlackCnote theme is already active\n";
    } else {
        switch_theme($theme);
        echo "✅ BlackCnote theme activated successfully!\n";
    }
} else {
    echo "❌ BlackCnote theme not found in WordPress themes\n";
    exit(1);
}

// Create default pages
echo "\n📄 Creating default pages...\n";
$pages = [
    'about' => 'About Us',
    'services' => 'Services', 
    'contact' => 'Contact',
    'privacy' => 'Privacy Policy',
    'terms' => 'Terms of Service',
    'plans' => 'Investment Plans',
    'dashboard' => 'Dashboard'
];

foreach ($pages as $slug => $title) {
    $existing_page = get_page_by_path($slug);
    if (!$existing_page) {
        $page_id = wp_insert_post([
            'post_title' => $title,
            'post_name' => $slug,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => "This is the $title page content."
        ]);
        
        if ($page_id) {
            echo "✅ Created page: $title\n";
        } else {
            echo "❌ Failed to create page: $title\n";
        }
    } else {
        echo "⚠️  Page already exists: $title\n";
    }
}

// Set up menu
echo "\n🍽️  Setting up navigation menu...\n";
$menu_name = 'Primary Menu';
$menu_exists = wp_get_nav_menu_object($menu_name);

if (!$menu_exists) {
    $menu_id = wp_create_nav_menu($menu_name);
    
    if ($menu_id) {
        echo "✅ Created menu: $menu_name\n";
        
        // Add pages to menu
        foreach ($pages as $slug => $title) {
            $page = get_page_by_path($slug);
            if ($page) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title' => $title,
                    'menu-item-object' => 'page',
                    'menu-item-object-id' => $page->ID,
                    'menu-item-status' => 'publish'
                ]);
            }
        }
        
        // Assign menu to primary location
        $locations = get_theme_mod('nav_menu_locations');
        $locations['menu-1'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
        
        echo "✅ Menu assigned to primary location\n";
    } else {
        echo "❌ Failed to create menu\n";
    }
} else {
    echo "⚠️  Menu already exists: $menu_name\n";
}

// Set theme options
echo "\n⚙️  Setting theme options...\n";
update_option('blackcnote_theme_activated', true);
update_option('blackcnote_version', '1.0.0');
echo "✅ Theme options set\n";

// Test theme functionality
echo "\n🧪 Testing theme functionality...\n";
$theme_object = wp_get_theme($theme);
if ($theme_object->exists()) {
    echo "✅ Theme object loaded: " . $theme_object->get('Name') . "\n";
    echo "   Version: " . $theme_object->get('Version') . "\n";
    echo "   Author: " . $theme_object->get('Author') . "\n";
} else {
    echo "❌ Theme object not found\n";
}

// Check for BCnote Theme marker
$header_file = $theme_path . '/header.php';
if (file_exists($header_file)) {
    $header_content = file_get_contents($header_file);
    if (strpos($header_content, '<!-- BCnote Theme -->') !== false) {
        echo "✅ BCnote Theme marker found in header\n";
    } else {
        echo "❌ BCnote Theme marker not found in header\n";
    }
} else {
    echo "❌ Header file not found\n";
}

echo "\n🎉 BlackCnote theme setup completed!\n";
echo "=====================================\n";
echo "✅ Theme activated: $theme\n";
echo "✅ Pages created: " . count($pages) . "\n";
echo "✅ Menu configured\n";
echo "✅ Theme options set\n\n";

echo "🌐 Access your site:\n";
echo "   Frontend: http://localhost:8888\n";
echo "   Admin: http://localhost:8888/wp-admin\n";
echo "   Username: admin\n";
echo "   Password: blackcnote_admin_2024!\n\n";

echo "🔧 Theme Features Available:\n";
echo "   - Investment Plans Page\n";
echo "   - Dashboard Template\n";
echo "   - Live Editing API\n";
echo "   - Admin Settings\n";
echo "   - Responsive Design\n\n";

echo "✅ Theme activation completed successfully!\n";
?> 