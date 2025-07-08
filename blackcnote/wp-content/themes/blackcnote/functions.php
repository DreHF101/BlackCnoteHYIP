<?php
/**
 * BlackCnote Theme Functions
 * Optimized for performance and speed
 */

if (!defined('ABSPATH')) {
    exit;
}

// Theme version
define('BLACKCNOTE_THEME_VERSION', '2.0.0');

// Canonical Pathways - ENFORCED
define('BLACKCNOTE_CANONICAL_ROOT', 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote');
define('BLACKCNOTE_CANONICAL_WORDPRESS', BLACKCNOTE_CANONICAL_ROOT . '/blackcnote');
define('BLACKCNOTE_CANONICAL_WP_CONTENT', BLACKCNOTE_CANONICAL_WORDPRESS . '/wp-content');
define('BLACKCNOTE_CANONICAL_THEME', BLACKCNOTE_CANONICAL_WP_CONTENT . '/themes/blackcnote');
define('BLACKCNOTE_CANONICAL_REACT_APP', BLACKCNOTE_CANONICAL_ROOT . '/react-app');

// Canonical Service URLs - ENFORCED
define('BLACKCNOTE_WORDPRESS_URL', 'http://localhost:8888');
define('BLACKCNOTE_REACT_URL', 'http://localhost:5174');
define('BLACKCNOTE_PHPMYADMIN_URL', 'http://localhost:8080');
define('BLACKCNOTE_REDIS_COMMANDER_URL', 'http://localhost:8081');
define('BLACKCNOTE_MAILHOG_URL', 'http://localhost:8025');
define('BLACKCNOTE_BROWSERSYNC_URL', 'http://localhost:3000');
define('BLACKCNOTE_DEV_TOOLS_URL', 'http://localhost:9229');

// Theme directories
define('BLACKCNOTE_THEME_DIR', get_template_directory());
define('BLACKCNOTE_THEME_URI', get_template_directory_uri());

// Performance Optimizations
function blackcnote_performance_optimizations() {
    // Remove unnecessary WordPress features
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    
    // Disable emojis for better performance
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    
    // Remove unnecessary feeds
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    
    // Optimize database queries
    add_filter('wp_headers', function($headers) {
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options'] = 'SAMEORIGIN';
        $headers['X-XSS-Protection'] = '1; mode=block';
        return $headers;
    });
}
add_action('init', 'blackcnote_performance_optimizations');

// Cache optimization
function blackcnote_cache_headers() {
    if (!is_admin()) {
        header('Cache-Control: public, max-age=3600');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
    }
}
add_action('send_headers', 'blackcnote_cache_headers');

// Theme setup
function blackcnote_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'blackcnote'),
    ));
}
add_action('after_setup_theme', 'blackcnote_setup');

/**
 * Check if React dev server is accessible
 */
function blackcnote_check_react_dev_server($port = 5174) {
    // For Docker environment, always use localhost since the browser will access it
    // The WordPress container doesn't need to access the React dev server directly
    $dev_server_url = "http://localhost:{$port}";
    
    // Simple check - if we're in development mode, assume React dev server is available
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("BlackCnote: Development mode detected, using React dev server at {$dev_server_url}");
        return $dev_server_url;
    }
    
    // Fallback: try to check if dev server is accessible
    $response = wp_remote_get($dev_server_url, [
        'timeout' => 2,
        'sslverify' => false
    ]);
    
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        error_log("BlackCnote: React dev server accessible at {$dev_server_url}");
        return $dev_server_url;
    }
    
    error_log("BlackCnote: React dev server not accessible, using built files");
    return false;
}

/**
 * Enqueue React app assets with seamless integration
 */
function blackcnote_enqueue_react_app() {
    // Check if React dev server is accessible (port 5174)
    $dev_server_url = blackcnote_check_react_dev_server(5174);
    if ($dev_server_url) {
        // Development mode - load from React dev server
        
        // Enqueue Vite client for hot reload
        wp_enqueue_script(
            'blackcnote-react-vite-client',
            $dev_server_url . '/@vite/client',
            [],
            BLACKCNOTE_THEME_VERSION,
            false
        );
        
        // Enqueue React main entry point
        wp_enqueue_script(
            'blackcnote-react-main',
            $dev_server_url . '/src/main.tsx',
            ['blackcnote-react-vite-client'],
            BLACKCNOTE_THEME_VERSION,
            true
        );
        
        // Inject development configuration
        $user = wp_get_current_user();
        $config = [
            'homeUrl' => home_url(),
            'isDevelopment' => true,
            'devServerUrl' => $dev_server_url,
            'apiUrl' => home_url('/wp-json/blackcnote/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'isLoggedIn' => is_user_logged_in(),
            'userId' => is_user_logged_in() ? $user->ID : 0,
            'baseUrl' => home_url(),
            'themeUrl' => BLACKCNOTE_THEME_URI,
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'environment' => 'development',
            'themeActive' => true,
            'pluginActive' => function_exists('hyiplab_system_instance'),
            'wpHeaderFooterDisabled' => false,
        ];
        
        wp_add_inline_script(
            'blackcnote-react-main',
            'window.blackCnoteApiSettings = ' . wp_json_encode($config) . ';',
            'before'
        );
        
        error_log('BlackCnote: Loading React app from development server at ' . $dev_server_url);
        return;
    }
    
    // Fallback: Check if React app is built in dist directory
    $dist_path = BLACKCNOTE_THEME_DIR . '/dist';
    $dist_uri = BLACKCNOTE_THEME_URI . '/dist';

    if (file_exists($dist_path . '/index.html')) {
        // Enqueue React CSS
        $css_files = glob($dist_path . '/assets/*.css');
        foreach ($css_files as $css_file) {
            $filename = basename($css_file);
            wp_enqueue_style(
                'blackcnote-react-' . md5($filename),
                $dist_uri . '/assets/' . $filename,
                [],
                BLACKCNOTE_THEME_VERSION
            );
        }

        // Enqueue React JS
        $js_files = glob($dist_path . '/assets/*.js');
        $main_js_handle = null;
        
        foreach ($js_files as $js_file) {
            $filename = basename($js_file);
            $handle = 'blackcnote-react-' . md5($filename);
            
            wp_enqueue_script(
                $handle,
                $dist_uri . '/assets/' . $filename,
                ['jquery'],
                BLACKCNOTE_THEME_VERSION,
                true
            );
            
            // Track main JS file for config injection
            if (strpos($filename, 'main') !== false || strpos($filename, 'index') !== false) {
                $main_js_handle = $handle;
            }
        }

        // Inject WordPress configuration for React app
        if ($main_js_handle) {
            $user = wp_get_current_user();
            $config = [
                'homeUrl' => home_url(),
                'isDevelopment' => false,
                'apiUrl' => home_url('/wp-json/blackcnote/v1/'),
                'nonce' => wp_create_nonce('wp_rest'),
                'isLoggedIn' => is_user_logged_in(),
                'userId' => is_user_logged_in() ? $user->ID : 0,
                'baseUrl' => home_url(),
                'themeUrl' => BLACKCNOTE_THEME_URI,
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'environment' => 'production',
                'themeActive' => true,
                'pluginActive' => function_exists('hyiplab_system_instance'),
                'wpHeaderFooterDisabled' => false,
            ];

            wp_add_inline_script(
                $main_js_handle,
                'window.blackCnoteApiSettings = ' . wp_json_encode($config) . ';',
                'before'
            );
        }

        error_log('BlackCnote: React app loaded from dist directory');
    } else {
        error_log('BlackCnote: React app not found. Dev server not accessible and no built files found.');
    }
}
add_action('wp_enqueue_scripts', 'blackcnote_enqueue_react_app', 100);

/**
 * Custom shortcode for investment plans
 */
function blackcnote_investment_plans_shortcode($atts) {
    $atts = shortcode_atts([
        'limit' => 10,
        'featured' => false
    ], $atts);

    global $wpdb;
    
    $where_clause = "status = 1";
    if ($atts['featured']) {
        $where_clause .= " AND featured = 1";
    }
    
    $plans = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE {$where_clause} ORDER BY id ASC LIMIT %d",
        $atts['limit']
    ));

    if (empty($plans)) {
        return '<p>No investment plans available.</p>';
    }

    $output = '<div class="investment-plans">';
    foreach ($plans as $plan) {
        $output .= sprintf(
            '<div class="plan">
                <h3>%s</h3>
                <p>Min: $%s | Max: $%s</p>
                <p>Return: %s%% | Duration: %s days</p>
            </div>',
            esc_html($plan->name),
            number_format($plan->min_investment, 2),
            number_format($plan->max_investment, 2),
            $plan->return_rate,
            $plan->duration_days
        );
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('blackcnote_plans', 'blackcnote_investment_plans_shortcode');

// Include additional theme files
require_once BLACKCNOTE_THEME_DIR . '/inc/blackcnote-react-loader.php'; 