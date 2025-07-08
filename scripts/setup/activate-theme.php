<?php
/**
 * BlackCnote Theme Activation Script
 * Run this after WordPress installation to activate the BlackCnote theme
 */

// Load WordPress
require_once "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote/blackcnote/wp-load.php";

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
    echo "Username: admin\n";
    echo "Password: blackcnote_admin_2024!\n";
    
} else {
    echo "❌ BlackCnote theme not found!\n";
    echo "Make sure the theme is in: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote/blackcnote/wp-content/themes/blackcnote/\n";
}
?>