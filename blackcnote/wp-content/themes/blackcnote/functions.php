<?php
/**
 * BlackCnote functions and definitions
 *
 * @package BlackCnote
 */

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

/**
 * BlackCnote Theme Activation System
 * Automatically activates all BlackCnote components when theme is activated
 */
function blackcnote_theme_activation() {
    // Set theme activation flag
    update_option('blackcnote_theme_activated', true);
    update_option('blackcnote_activation_date', current_time('mysql'));
    
    // Create essential pages
    blackcnote_create_default_pages();
    
    // Set up default widgets
    blackcnote_setup_default_widgets();
    
    // Activate required plugins
    blackcnote_activate_required_plugins();
    
    // Set up default theme options
    blackcnote_setup_default_options();
    
    // Create default menus
    blackcnote_create_default_menus();
    
    // Set up default content
    blackcnote_setup_default_content();
    
    // Configure BlackCnote specific settings
    blackcnote_configure_theme_settings();
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Log activation
    error_log('BlackCnote Theme activated successfully at ' . current_time('mysql'));
}
add_action('after_switch_theme', 'blackcnote_theme_activation');

/**
 * Create essential pages for BlackCnote
 */
function blackcnote_create_default_pages() {
    $pages = array(
        'home' => array(
            'title' => 'Home',
            'content' => 'Welcome to BlackCnote - Your Investment Platform',
            'template' => 'page-home.php'
        ),
        'about' => array(
            'title' => 'About Us',
            'content' => 'Learn more about BlackCnote and our investment services.',
            'template' => 'page-about.php'
        ),
        'services' => array(
            'title' => 'Investment Services',
            'content' => 'Explore our comprehensive investment services and opportunities.',
            'template' => 'page-services.php'
        ),
        'contact' => array(
            'title' => 'Contact Us',
            'content' => 'Get in touch with our investment team.',
            'template' => 'page-contact.php'
        ),
        'privacy-policy' => array(
            'title' => 'Privacy Policy',
            'content' => 'Our privacy policy and data protection practices.',
            'template' => 'page-privacy.php'
        ),
        'terms-of-service' => array(
            'title' => 'Terms of Service',
            'content' => 'Terms and conditions for using BlackCnote services.',
            'template' => 'page-terms.php'
        ),
        'investment-dashboard' => array(
            'title' => 'Investment Dashboard',
            'content' => 'Your personal investment dashboard and portfolio overview.',
            'template' => 'page-dashboard.php'
        ),
        'investment-plans' => array(
            'title' => 'Investment Plans',
            'content' => 'Browse our available investment plans and opportunities.',
            'template' => 'page-plans.php'
        )
    );
    
    foreach ($pages as $slug => $page_data) {
        $existing_page = get_page_by_path($slug);
        if (!$existing_page) {
            $page_id = wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_content' => $page_data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => $slug,
                'page_template' => $page_data['template']
            ));
            
            if ($page_id && $slug === 'home') {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $page_id);
            }
        }
    }
}

/**
 * Set up default widgets for BlackCnote
 */
function blackcnote_setup_default_widgets() {
    // Clear existing widgets
    $sidebars_widgets = get_option('sidebars_widgets');
    $sidebars_widgets['sidebar-1'] = array();
    
    // Add default widgets
    $widgets = array(
        'blackcnote_investment_stats' => array(
            'title' => 'Investment Statistics',
            'sidebar' => 'sidebar-1'
        ),
        'blackcnote_recent_investments' => array(
            'title' => 'Recent Investments',
            'sidebar' => 'sidebar-1'
        ),
        'blackcnote_market_news' => array(
            'title' => 'Market News',
            'sidebar' => 'sidebar-1'
        ),
        'blackcnote_quick_links' => array(
            'title' => 'Quick Links',
            'sidebar' => 'sidebar-1'
        )
    );
    
    foreach ($widgets as $widget_id => $widget_data) {
        $widget_instance = array(
            'title' => $widget_data['title']
        );
        
        $sidebars_widgets[$widget_data['sidebar']][] = $widget_id . '-1';
        update_option('widget_' . $widget_id, array('1' => $widget_instance));
    }
    
    update_option('sidebars_widgets', $sidebars_widgets);
}

/**
 * Activate required plugins for BlackCnote
 */
function blackcnote_activate_required_plugins() {
    $required_plugins = array(
        'blackcnote-debug-system/blackcnote-debug-system.php' => 'BlackCnote Debug System',
        'hyiplab/hyiplab.php' => 'HyipLab Investment Platform'
    );
    
    foreach ($required_plugins as $plugin_path => $plugin_name) {
        if (!is_plugin_active($plugin_path)) {
            activate_plugin($plugin_path);
        }
    }
}

/**
 * Set up default theme options
 */
function blackcnote_setup_default_options() {
    $default_options = array(
        'blackcnote_theme_color' => '#1a1a1a',
        'blackcnote_accent_color' => '#007cba',
        'blackcnote_logo_url' => get_template_directory_uri() . '/images/blackcnote-logo.png',
        'blackcnote_footer_text' => '© ' . date('Y') . ' BlackCnote. All rights reserved.',
        'blackcnote_analytics_code' => '',
        'blackcnote_investment_enabled' => true,
        'blackcnote_dashboard_enabled' => true,
        'blackcnote_market_data_enabled' => true,
        'blackcnote_notifications_enabled' => true,
        'blackcnote_security_level' => 'high',
        'blackcnote_auto_backup' => true,
        'blackcnote_debug_mode' => false
    );
    
    foreach ($default_options as $option_name => $option_value) {
        if (get_option($option_name) === false) {
            update_option($option_name, $option_value);
        }
    }
}

/**
 * Create default menus for BlackCnote
 */
function blackcnote_create_default_menus() {
    // Create main navigation menu
    $main_menu_name = 'Primary Navigation';
    $main_menu_exists = wp_get_nav_menu_object($main_menu_name);
    
    if (!$main_menu_exists) {
        $main_menu_id = wp_create_nav_menu($main_menu_name);
        
        $main_menu_items = array(
            'Home' => '/',
            'About Us' => '/about/',
            'Investment Services' => '/services/',
            'Investment Plans' => '/investment-plans/',
            'Dashboard' => '/investment-dashboard/',
            'Contact Us' => '/contact/'
        );
        
        foreach ($main_menu_items as $title => $url) {
            wp_update_nav_menu_item($main_menu_id, 0, array(
                'menu-item-title' => $title,
                'menu-item-url' => home_url($url),
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom'
            ));
        }
        
        // Assign menu to primary location
        $locations = get_theme_mod('nav_menu_locations');
        $locations['menu-1'] = $main_menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }
    
    // Create footer menu
    $footer_menu_name = 'Footer Menu';
    $footer_menu_exists = wp_get_nav_menu_object($footer_menu_name);
    
    if (!$footer_menu_exists) {
        $footer_menu_id = wp_create_nav_menu($footer_menu_name);
        
        $footer_menu_items = array(
            'Privacy Policy' => '/privacy-policy/',
            'Terms of Service' => '/terms-of-service/',
            'Support' => '/contact/',
            'FAQ' => '/faq/'
        );
        
        foreach ($footer_menu_items as $title => $url) {
            wp_update_nav_menu_item($footer_menu_id, 0, array(
                'menu-item-title' => $title,
                'menu-item-url' => home_url($url),
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom'
            ));
        }
    }
}

/**
 * Set up default content for BlackCnote
 */
function blackcnote_setup_default_content() {
    // Create default post categories
    $categories = array(
        'Investment News' => 'Latest investment market news and updates',
        'Trading Tips' => 'Professional trading advice and strategies',
        'Market Analysis' => 'In-depth market analysis and reports',
        'Investment Opportunities' => 'Featured investment opportunities',
        'Company Updates' => 'BlackCnote company news and updates'
    );
    
    foreach ($categories as $cat_name => $cat_description) {
        if (!term_exists($cat_name, 'category')) {
            wp_insert_term($cat_name, 'category', array('description' => $cat_description));
        }
    }
    
    // Create sample posts
    $sample_posts = array(
        array(
            'title' => 'Welcome to BlackCnote Investment Platform',
            'content' => 'Welcome to BlackCnote, your premier investment platform. We provide comprehensive investment services and opportunities for both beginners and experienced investors.',
            'category' => 'Company Updates'
        ),
        array(
            'title' => 'Getting Started with Investment',
            'content' => 'Learn the basics of investment and how to get started with your investment journey. Our platform provides all the tools you need to succeed.',
            'category' => 'Trading Tips'
        ),
        array(
            'title' => 'Market Trends and Analysis',
            'content' => 'Stay updated with the latest market trends and analysis. Our expert team provides regular insights to help you make informed investment decisions.',
            'category' => 'Market Analysis'
        )
    );
    
    foreach ($sample_posts as $post_data) {
        $existing_post = get_page_by_title($post_data['title'], OBJECT, 'post');
        if (!$existing_post) {
            $post_id = wp_insert_post(array(
                'post_title' => $post_data['title'],
                'post_content' => $post_data['content'],
                'post_status' => 'publish',
                'post_type' => 'post',
                'post_category' => array(get_cat_ID($post_data['category']))
            ));
        }
    }
}

/**
 * Configure BlackCnote specific settings
 */
function blackcnote_configure_theme_settings() {
    // Set up custom post types
    blackcnote_register_custom_post_types();
    
    // Set up custom taxonomies
    blackcnote_register_custom_taxonomies();
    
    // Configure permalinks
    update_option('permalink_structure', '/%postname%/');
    
    // Set up default image sizes
    update_option('thumbnail_size_w', 300);
    update_option('thumbnail_size_h', 200);
    update_option('medium_size_w', 600);
    update_option('medium_size_h', 400);
    update_option('large_size_w', 1200);
    update_option('large_size_h', 800);
    
    // Enable comments on pages
    update_option('default_comment_status', 'open');
    
    // Set timezone
    update_option('timezone_string', 'America/Chicago');
    
    // Configure reading settings
    update_option('posts_per_page', 10);
    update_option('posts_per_rss', 10);
}

/**
 * Register custom post types for BlackCnote
 */
function blackcnote_register_custom_post_types() {
    // Investment Plans post type
    register_post_type('investment_plans', array(
        'labels' => array(
            'name' => 'Investment Plans',
            'singular_name' => 'Investment Plan',
            'add_new' => 'Add New Plan',
            'add_new_item' => 'Add New Investment Plan',
            'edit_item' => 'Edit Investment Plan',
            'new_item' => 'New Investment Plan',
            'view_item' => 'View Investment Plan',
            'search_items' => 'Search Investment Plans',
            'not_found' => 'No investment plans found',
            'not_found_in_trash' => 'No investment plans found in trash'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-chart-line',
        'rewrite' => array('slug' => 'investment-plans')
    ));
    
    // Market News post type
    register_post_type('market_news', array(
        'labels' => array(
            'name' => 'Market News',
            'singular_name' => 'Market News',
            'add_new' => 'Add News',
            'add_new_item' => 'Add New Market News',
            'edit_item' => 'Edit Market News',
            'new_item' => 'New Market News',
            'view_item' => 'View Market News',
            'search_items' => 'Search Market News',
            'not_found' => 'No market news found',
            'not_found_in_trash' => 'No market news found in trash'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-megaphone',
        'rewrite' => array('slug' => 'market-news')
    ));
}

/**
 * Register custom taxonomies for BlackCnote
 */
function blackcnote_register_custom_taxonomies() {
    // Investment Categories
    register_taxonomy('investment_category', array('investment_plans'), array(
        'labels' => array(
            'name' => 'Investment Categories',
            'singular_name' => 'Investment Category',
            'search_items' => 'Search Investment Categories',
            'all_items' => 'All Investment Categories',
            'parent_item' => 'Parent Investment Category',
            'parent_item_colon' => 'Parent Investment Category:',
            'edit_item' => 'Edit Investment Category',
            'update_item' => 'Update Investment Category',
            'add_new_item' => 'Add New Investment Category',
            'new_item_name' => 'New Investment Category Name',
            'menu_name' => 'Investment Categories'
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'investment-category')
    ));
    
    // Market Categories
    register_taxonomy('market_category', array('market_news'), array(
        'labels' => array(
            'name' => 'Market Categories',
            'singular_name' => 'Market Category',
            'search_items' => 'Search Market Categories',
            'all_items' => 'All Market Categories',
            'parent_item' => 'Parent Market Category',
            'parent_item_colon' => 'Parent Market Category:',
            'edit_item' => 'Edit Market Category',
            'update_item' => 'Update Market Category',
            'add_new_item' => 'Add New Market Category',
            'new_item_name' => 'New Market Category Name',
            'menu_name' => 'Market Categories'
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'market-category')
    ));
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function blackcnote_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // Add support for responsive embeds.
    add_theme_support('responsive-embeds');

    // Add support for custom logo.
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Add support for Block Styles.
    add_theme_support('wp-block-styles');

    // Add support for full and wide align images.
    add_theme_support('align-wide');

    // Add support for editor styles.
    add_theme_support('editor-styles');

    // Add support for experimental link color.
    add_theme_support('experimental-link-color');

    // Add support for custom units.
    add_theme_support('custom-units');

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(array(
        'menu-1' => esc_html__('Primary', 'blackcnote'),
    ));

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for core custom logo.
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'blackcnote_setup');

/**
 * Enqueue scripts and styles.
 */
function blackcnote_scripts() {
    // Enqueue theme stylesheet
    wp_enqueue_style('blackcnote-style', get_stylesheet_uri(), array(), _S_VERSION);
    
    // Enqueue React app assets if they exist
    $react_dist_path = get_template_directory() . '/dist';
    if (file_exists($react_dist_path . '/index.html')) {
        // Enqueue React CSS
        if (file_exists($react_dist_path . '/assets/css/index-fc142a5e.css')) {
            wp_enqueue_style('blackcnote-react', get_template_directory_uri() . '/dist/assets/css/index-fc142a5e.css', array(), _S_VERSION);
        }
        
        // Enqueue React JS
        if (file_exists($react_dist_path . '/assets/js/main-a901f4c1.js')) {
            wp_enqueue_script('blackcnote-react-main', get_template_directory_uri() . '/dist/assets/js/main-a901f4c1.js', array(), _S_VERSION, true);
        }
        
        if (file_exists($react_dist_path . '/assets/js/router-16afc5f5.js')) {
            wp_enqueue_script('blackcnote-react-router', get_template_directory_uri() . '/dist/assets/js/router-16afc5f5.js', array(), _S_VERSION, true);
        }
        
        if (file_exists($react_dist_path . '/assets/js/ui-e0e6b94b.js')) {
            wp_enqueue_script('blackcnote-react-ui', get_template_directory_uri() . '/dist/assets/js/ui-e0e6b94b.js', array(), _S_VERSION, true);
        }
        
        if (file_exists($react_dist_path . '/assets/js/vendor-51280515.js')) {
            wp_enqueue_script('blackcnote-react-vendor', get_template_directory_uri() . '/dist/assets/js/vendor-51280515.js', array(), _S_VERSION, true);
        }
    }
    
    // Enqueue theme script
    wp_enqueue_script('blackcnote-script', get_template_directory_uri() . '/js/theme.js', array(), _S_VERSION, true);
    
    // Localize script for AJAX
    wp_localize_script('blackcnote-script', 'blackcnote_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('blackcnote_nonce'),
        'rest_url' => rest_url(),
        'rest_nonce' => wp_create_nonce('wp_rest'),
    ));
}
add_action('wp_enqueue_scripts', 'blackcnote_scripts');

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Custom widgets for BlackCnote
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Add custom image sizes
 */
function blackcnote_image_sizes() {
    add_image_size('blackcnote-featured', 1200, 600, true);
    add_image_size('blackcnote-thumbnail', 300, 200, true);
}
add_action('after_setup_theme', 'blackcnote_image_sizes');

/**
 * Customize excerpt length
 */
function blackcnote_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'blackcnote_excerpt_length');

/**
 * Customize excerpt more
 */
function blackcnote_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'blackcnote_excerpt_more');

/**
 * Add theme support for WooCommerce
 */
function blackcnote_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'blackcnote_woocommerce_support');

/**
 * Register widget areas
 */
function blackcnote_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'blackcnote'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'blackcnote'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
    
    // Register additional widget areas for BlackCnote
    register_sidebar(array(
        'name'          => esc_html__('Footer Widget Area 1', 'blackcnote'),
        'id'            => 'footer-1',
        'description'   => esc_html__('Add widgets to the first footer column.', 'blackcnote'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => esc_html__('Footer Widget Area 2', 'blackcnote'),
        'id'            => 'footer-2',
        'description'   => esc_html__('Add widgets to the second footer column.', 'blackcnote'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => esc_html__('Footer Widget Area 3', 'blackcnote'),
        'id'            => 'footer-3',
        'description'   => esc_html__('Add widgets to the third footer column.', 'blackcnote'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => esc_html__('Investment Dashboard Sidebar', 'blackcnote'),
        'id'            => 'dashboard-sidebar',
        'description'   => esc_html__('Add widgets to the investment dashboard sidebar.', 'blackcnote'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'blackcnote_widgets_init'); 