<?php
/**
 * WordPress Installation Script for BlackCnote
 * Automatically installs WordPress and activates the BlackCnote theme
 */

declare(strict_types=1);

echo "🚀 BlackCnote WordPress Installation Script\n";
echo "==========================================\n\n";

// Configuration
$site_url = 'http://localhost:8888';
$site_title = 'BlackCnote - Investment Platform';
$admin_username = 'admin';
$admin_password = 'blackcnote_admin_2024!';
$admin_email = 'admin@blackcnote.local';

echo "📋 Installation Configuration:\n";
echo "   Site URL: $site_url\n";
echo "   Site Title: $site_title\n";
echo "   Admin Username: $admin_username\n";
echo "   Admin Email: $admin_email\n\n";

// Step 1: Check if WordPress is already installed
echo "1. Checking WordPress installation status...\n";
$check_url = $site_url . '/wp-admin/install.php';
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'method' => 'GET'
    ]
]);

$response = @file_get_contents($check_url, false, $context);
if (strpos($response, 'WordPress &rsaquo; Installation') !== false) {
    echo "   ✅ WordPress needs to be installed\n";
} else {
    echo "   ⚠️  WordPress may already be installed\n";
    echo "   Proceeding with theme activation...\n";
}

// Step 2: Install WordPress using wp-cli or direct API
echo "\n2. Installing WordPress...\n";

// Create installation data
$install_data = [
    'weblog_title' => $site_title,
    'user_name' => $admin_username,
    'admin_password' => $admin_password,
    'admin_password2' => $admin_password,
    'admin_email' => $admin_email,
    'blog_public' => 0, // Don't discourage search engines
    'Submit' => 'Install WordPress'
];

// Step 3: Submit installation form
echo "   Submitting installation form...\n";
$post_data = http_build_query($install_data);

$install_context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($post_data)
        ],
        'content' => $post_data,
        'timeout' => 30
    ]
]);

$install_response = @file_get_contents($site_url . '/wp-admin/install.php?step=2', false, $install_context);

if ($install_response !== false) {
    if (strpos($install_response, 'Success') !== false || strpos($install_response, 'WordPress has been installed') !== false) {
        echo "   ✅ WordPress installed successfully!\n";
    } else {
        echo "   ⚠️  Installation response received, checking status...\n";
    }
} else {
    echo "   ❌ Installation request failed\n";
}

// Step 4: Wait for installation to complete
echo "\n3. Waiting for installation to complete...\n";
sleep(5);

// Step 5: Test WordPress admin access
echo "\n4. Testing WordPress admin access...\n";
$admin_url = $site_url . '/wp-admin/';
$admin_response = @file_get_contents($admin_url, false, $context);

if ($admin_response !== false) {
    if (strpos($admin_response, 'wp-admin') !== false) {
        echo "   ✅ WordPress admin is accessible\n";
    } else {
        echo "   ⚠️  WordPress admin may need login\n";
    }
} else {
    echo "   ❌ WordPress admin not accessible\n";
}

// Step 6: Create a script to activate the theme
echo "\n5. Creating theme activation script...\n";

$activation_script = '<?php
/**
 * BlackCnote Theme Activation Script
 * Run this after WordPress installation to activate the BlackCnote theme
 */

// Load WordPress
require_once "' . dirname(__DIR__, 2) . '/blackcnote/wp-load.php";

// Check if user is logged in as admin
if (!current_user_can("switch_themes")) {
    wp_die("You do not have sufficient permissions to access this page.");
}

// Activate BlackCnote theme
$theme = "blackcnote";
if (wp_get_theme($theme)->exists()) {
    switch_theme($theme);
    echo "✅ BlackCnote theme activated successfully!\n";
    
    // Create default pages
    $pages = [
        "about" => "About Us",
        "services" => "Services", 
        "contact" => "Contact",
        "privacy" => "Privacy Policy",
        "terms" => "Terms of Service",
        "plans" => "Investment Plans",
        "dashboard" => "Dashboard"
    ];
    
    foreach ($pages as $slug => $title) {
        $page_id = wp_insert_post([
            "post_title" => $title,
            "post_name" => $slug,
            "post_status" => "publish",
            "post_type" => "page",
            "post_content" => "This is the " . $title . " page content."
        ]);
        
        if ($page_id) {
            echo "✅ Created page: $title\n";
        }
    }
    
    // Set up menu
    $menu_name = "Primary Menu";
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
        
        // Add pages to menu
        foreach ($pages as $slug => $title) {
            $page = get_page_by_path($slug);
            if ($page) {
                wp_update_nav_menu_item($menu_id, 0, [
                    "menu-item-title" => $title,
                    "menu-item-object" => "page",
                    "menu-item-object-id" => $page->ID,
                    "menu-item-status" => "publish"
                ]);
            }
        }
        
        // Assign menu to primary location
        $locations = get_theme_mod("nav_menu_locations");
        $locations["menu-1"] = $menu_id;
        set_theme_mod("nav_menu_locations", $locations);
        
        echo "✅ Created and assigned primary menu\n";
    }
    
    echo "\n🎉 BlackCnote theme setup completed!\n";
    echo "Visit: http://localhost:8888 to see your site\n";
    echo "Admin: http://localhost:8888/wp-admin\n";
    echo "Username: ' . $admin_username . '\n";
    echo "Password: ' . $admin_password . '\n";
    
} else {
    echo "❌ BlackCnote theme not found!\n";
    echo "Make sure the theme is in: ' . dirname(__DIR__, 2) . '/blackcnote/wp-content/themes/blackcnote/\n";
}
?>';

$script_path = dirname(__DIR__, 2) . '/scripts/setup/activate-theme.php';
file_put_contents($script_path, $activation_script);
echo "   ✅ Theme activation script created: $script_path\n";

// Step 7: Instructions for manual completion
echo "\n6. Installation Summary:\n";
echo "   ✅ WordPress installation initiated\n";
echo "   ✅ Theme activation script created\n\n";

echo "📋 NEXT STEPS:\n";
echo "1. Complete WordPress installation at: $site_url/wp-admin/install.php\n";
echo "2. Run theme activation script: php scripts/setup/activate-theme.php\n";
echo "3. Visit your site: $site_url\n";
echo "4. Login to admin: $site_url/wp-admin\n";
echo "   Username: $admin_username\n";
echo "   Password: $admin_password\n\n";

echo "🔧 Manual Installation Steps:\n";
echo "1. Open browser and go to: $site_url/wp-admin/install.php\n";
echo "2. Fill in the form with:\n";
echo "   - Site Title: $site_title\n";
echo "   - Username: $admin_username\n";
echo "   - Password: $admin_password\n";
echo "   - Email: $admin_email\n";
echo "3. Click 'Install WordPress'\n";
echo "4. Run: php scripts/setup/activate-theme.php\n\n";

echo "✅ Installation script completed!\n";
?> 