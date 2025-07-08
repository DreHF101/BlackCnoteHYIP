<?php
declare(strict_types=1);

/**
 * Register navigation menus
 *
 * @package BlackCnote
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register navigation menus
 */
function blackcnote_register_menus(): void {
    register_nav_menus([
        'primary' => esc_html__('Primary Menu', 'blackcnote'),
        'footer' => esc_html__('Footer Menu', 'blackcnote'),
    ]);
}
add_action('init', 'blackcnote_register_menus');

/**
 * Create default pages if they don't exist
 */
function blackcnote_create_default_pages(): void {
    $pages = [
        'dashboard' => [
            'title' => 'Dashboard',
            'template' => 'template-dashboard.php',
        ],
        'profile' => [
            'title' => 'My Profile',
            'template' => 'template-profile.php',
        ],
        'investments' => [
            'title' => 'My Investments',
            'template' => 'template-investments.php',
        ],
        'transactions' => [
            'title' => 'Transactions',
            'template' => 'template-transactions.php',
        ],
        'calculator' => [
            'title' => 'Profit Calculator',
            'template' => 'template-calculator.php',
        ],
        'plans' => [
            'title' => 'Investment Plans',
            'template' => 'template-plans.php',
        ],
        'about' => [
            'title' => 'About Us',
            'template' => 'template-about.php',
        ],
        'contact' => [
            'title' => 'Contact Us',
            'template' => 'template-contact.php',
        ],
    ];

    foreach ($pages as $slug => $page) {
        $existing_page = get_page_by_path($slug);
        if (!$existing_page) {
            wp_insert_post([
                'post_title' => $page['title'],
                'post_name' => $slug,
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '',
                'page_template' => $page['template'],
            ]);
        }
    }
}
add_action('after_switch_theme', 'blackcnote_create_default_pages');

/**
 * Create default menu items
 */
function blackcnote_create_default_menu(): void {
    $menu_name = 'Primary Menu';
    $menu_exists = wp_get_nav_menu_object($menu_name);

    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);

        $menu_items = [
            'Home' => home_url('/'),
            'Investment Plans' => home_url('/plans'),
            'Calculator' => home_url('/calculator'),
            'About Us' => home_url('/about'),
            'Contact' => home_url('/contact'),
        ];

        foreach ($menu_items as $title => $url) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title' => $title,
                'menu-item-url' => $url,
                'menu-item-status' => 'publish',
            ]);
        }

        $locations = get_theme_mod('nav_menu_locations');
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }
}
add_action('after_switch_theme', 'blackcnote_create_default_menu'); 