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
}
add_action('widgets_init', 'blackcnote_widgets_init'); 