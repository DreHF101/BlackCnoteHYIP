<?php
/**
 * BlackCnote WordPress Configuration Script
 * Automatically configures WordPress with optimal settings
 */

// Load WordPress
require_once('/var/www/html/wp-config.php');
require_once('/var/www/html/wp-admin/includes/upgrade.php');

// Ensure we're running this from CLI or with proper permissions
if (!defined('WP_CLI') && !current_user_can('manage_options')) {
    die('Access denied');
}

echo "🚀 Configuring BlackCnote WordPress...\n";

// 1. Update Site Settings
echo "📝 Updating site settings...\n";
update_option('blogname', 'BlackCnote');
update_option('blogdescription', 'Advanced Investment Platform');
update_option('siteurl', 'http://localhost:8888');
update_option('home', 'http://localhost:8888');

// 2. Configure Permalinks
echo "🔗 Configuring permalinks...\n";
global $wp_rewrite;
$wp_rewrite->set_permalink_structure('/%postname%/');
$wp_rewrite->flush_rules();

// 3. Set Timezone
echo "⏰ Setting timezone...\n";
update_option('timezone_string', 'America/Chicago');
update_option('date_format', 'F j, Y');
update_option('time_format', 'g:i a');

// 4. Configure Reading Settings
echo "📖 Configuring reading settings...\n";
update_option('show_on_front', 'page');
update_option('posts_per_page', 10);

// 5. Configure Discussion Settings
echo "💬 Configuring discussion settings...\n";
update_option('default_comment_status', 'closed');
update_option('default_ping_status', 'closed');
update_option('comment_moderation', 1);
update_option('comment_whitelist', 1);

// 6. Configure Media Settings
echo "🖼️ Configuring media settings...\n";
update_option('thumbnail_size_w', 150);
update_option('thumbnail_size_h', 150);
update_option('medium_size_w', 300);
update_option('medium_size_h', 300);
update_option('large_size_w', 1024);
update_option('large_size_h', 1024);

// 7. Disable Automatic Updates
echo "🔄 Configuring update settings...\n";
update_option('automatic_updater_disabled', true);
update_option('auto_update_core_major', false);
update_option('auto_update_core_minor', false);
update_option('auto_update_core_dev', false);

// 8. Configure Security Settings
echo "🔒 Configuring security settings...\n";
update_option('users_can_register', false);

// 9. Create Essential Pages
echo "📄 Creating essential pages...\n";

// Home Page
$home_page = array(
    'post_title' => 'Home',
    'post_content' => 'Welcome to BlackCnote - Advanced Investment Platform',
    'post_status' => 'publish',
    'post_type' => 'page',
    'post_name' => 'home'
);
$home_id = wp_insert_post($home_page);
update_option('page_on_front', $home_id);

// About Page
$about_page = array(
    'post_title' => 'About',
    'post_content' => 'Learn more about BlackCnote and our investment platform.',
    'post_status' => 'publish',
    'post_type' => 'page',
    'post_name' => 'about'
);
wp_insert_post($about_page);

// Contact Page
$contact_page = array(
    'post_title' => 'Contact',
    'post_content' => 'Get in touch with our team.',
    'post_status' => 'publish',
    'post_type' => 'page',
    'post_name' => 'contact'
);
wp_insert_post($contact_page);

// 10. Configure Menu
echo "🍔 Configuring navigation menu...\n";
$menu_name = 'Primary Menu';
$menu_exists = wp_get_nav_menu_object($menu_name);

if (!$menu_exists) {
    $menu_id = wp_create_nav_menu($menu_name);
    
    // Add pages to menu
    $pages = get_pages();
    foreach ($pages as $page) {
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => $page->post_title,
            'menu-item-object' => 'page',
            'menu-item-object-id' => $page->ID,
            'menu-item-status' => 'publish'
        ));
    }
    
    // Assign menu to primary location
    $locations = get_theme_mod('nav_menu_locations');
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
}

// 11. Configure Widgets
echo "🧩 Configuring widgets...\n";
$sidebars_widgets = get_option('sidebars_widgets');
if (empty($sidebars_widgets)) {
    $sidebars_widgets = array(
        'sidebar-1' => array(),
        'wp_inactive_widgets' => array()
    );
    update_option('sidebars_widgets', $sidebars_widgets);
}

// 12. Set Default Theme (if Twenty Twenty-Four is available)
echo "🎨 Configuring theme...\n";
$theme = wp_get_theme('twentytwentyfour');
if ($theme->exists()) {
    switch_theme('twentytwentyfour');
}

// 13. Configure Cache Settings
echo "⚡ Configuring cache settings...\n";
update_option('wp_cache_enabled', true);

// 14. Create .htaccess for Pretty Permalinks
echo "📝 Creating .htaccess file...\n";
$htaccess_content = "# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress";

$htaccess_file = ABSPATH . '.htaccess';
if (!file_exists($htaccess_file)) {
    file_put_contents($htaccess_file, $htaccess_content);
}

echo "✅ WordPress configuration completed successfully!\n";
echo "🌐 Your site is now available at: http://localhost:8888\n";
echo "🔧 Admin panel: http://localhost:8888/wp-admin\n";
echo "📧 Default admin credentials:\n";
echo "   Username: admin\n";
echo "   Password: (check your wp-config.php or use password reset)\n";
?> 