<?php
/**
 * BlackCnote Theme functions and definitions
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Define theme constants
 */
define('BLACKCNOTE_THEME_VERSION', '1.0.0');
define('BLACKCNOTE_THEME_DIR', get_template_directory());
define('BLACKCNOTE_THEME_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function blackcnote_theme_setup(): void {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus([
        'primary' => __('Primary Menu', 'blackcnote'),
        'footer' => __('Footer Menu', 'blackcnote'),
    ]);

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ]);

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for RTL
    add_theme_support('rtl');

    // Load theme text domain
    load_theme_textdomain('blackcnote', BLACKCNOTE_THEME_DIR . '/languages');
}
add_action('after_setup_theme', 'blackcnote_theme_setup');

/**
 * Enqueue scripts and styles
 */
function blackcnote_theme_scripts(): void {
    // Bootstrap 5 CSS
    wp_enqueue_style(
        'bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css',
        [],
        '5.1.3'
    );

    // Theme stylesheet
    wp_enqueue_style(
        'blackcnote-theme-style',
        get_stylesheet_uri(),
        ['bootstrap'],
        wp_get_theme()->get('Version')
    );

    // Custom CSS
    wp_enqueue_style(
        'blackcnote-theme-custom',
        get_template_directory_uri() . '/assets/css/hyip-theme.css',
        ['blackcnote-theme-style'],
        wp_get_theme()->get('Version')
    );

    // jQuery
    wp_enqueue_script('jquery');

    // Bootstrap 5 JS
    wp_enqueue_script(
        'bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js',
        ['jquery'],
        '5.1.3',
        true
    );

    // Custom JS
    wp_enqueue_script(
        'blackcnote-theme-script',
        get_template_directory_uri() . '/assets/js/hyip-theme.js',
        ['jquery', 'bootstrap'],
        wp_get_theme()->get('Version'),
        true
    );

    // Localize script
    wp_localize_script('blackcnote-theme-script', 'blackcnoteTheme', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('blackcnote_theme_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'blackcnote_theme_scripts');

/**
 * Custom shortcode for investment plans
 */
function blackcnote_plans_shortcode($atts) {
    global $wpdb;
    
    $atts = shortcode_atts(array(
        'limit' => 10,
        'orderby' => 'id',
        'order' => 'DESC'
    ), $atts, 'blackcnote_plans');

    try {
        $plans = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}hyiplab_plans 
            ORDER BY %s %s 
            LIMIT %d",
            $atts['orderby'],
            $atts['order'],
            $atts['limit']
        ));

        ob_start();
        ?>
        <div class="hyip-plans">
            <?php foreach ($plans as $plan): ?>
                <div class="plan-card">
                    <h3><?php echo esc_html($plan->name); ?></h3>
                    <div class="plan-details">
                        <p class="return-rate">
                            <?php echo esc_html__('Return Rate:', 'blackcnote'); ?>
                            <?php echo esc_html($plan->return_rate); ?>%
                        </p>
                        <p class="min-investment">
                            <?php echo esc_html__('Min Investment:', 'blackcnote'); ?>
                            <?php echo esc_html($plan->min_investment); ?>
                        </p>
                        <p class="max-investment">
                            <?php echo esc_html__('Max Investment:', 'blackcnote'); ?>
                            <?php echo esc_html($plan->max_investment); ?>
                        </p>
                    </div>
                    <button class="btn btn-primary invest-btn" 
                            data-plan-id="<?php echo esc_attr($plan->id); ?>">
                        <?php echo esc_html__('Invest Now', 'blackcnote'); ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    } catch (Exception $e) {
        error_log('BlackCnote Theme Error: ' . $e->getMessage());
        return '<p class="error">' . esc_html__('Error loading investment plans.', 'blackcnote') . '</p>';
    }
}
add_shortcode('blackcnote_plans', 'blackcnote_plans_shortcode');

/**
 * AJAX handler for plan calculations
 */
function blackcnote_calculate_plan() {
    check_ajax_referer('blackcnote_theme_nonce', 'nonce');

    $plan_id = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : 0;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

    if (!$plan_id || !$amount) {
        wp_send_json_error(array(
            'message' => esc_html__('Invalid plan or amount.', 'blackcnote')
        ));
    }

    global $wpdb;
    try {
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE id = %d",
            $plan_id
        ));

        if (!$plan) {
            throw new Exception(__('Plan not found.', 'blackcnote'));
        }

        if ($amount < $plan->min_investment || $amount > $plan->max_investment) {
            throw new Exception(__('Amount is outside the allowed range.', 'blackcnote'));
        }

        $return_amount = $amount * (1 + ($plan->return_rate / 100));
        
        wp_send_json_success(array(
            'return_amount' => $return_amount,
            'profit' => $return_amount - $amount
        ));
    } catch (Exception $e) {
        wp_send_json_error(array(
            'message' => $e->getMessage()
        ));
    }
}
add_action('wp_ajax_blackcnote_calculate', 'blackcnote_calculate_plan');
add_action('wp_ajax_nopriv_blackcnote_calculate', 'blackcnote_calculate_plan');

/**
 * Check if HYIPLab plugin is active
 */
function blackcnote_is_plugin_active() {
    return function_exists('hyiplab_system_instance');
}

/**
 * Add custom body classes
 */
function blackcnote_body_classes($classes) {
    if (blackcnote_is_plugin_active()) {
        $classes[] = 'hyiplab-active';
    }
    return $classes;
}
add_filter('body_class', 'blackcnote_body_classes');

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

/**
 * Register custom post type for investment plans
 */
function blackcnote_theme_register_post_type(): void {
    register_post_type('blackcnote_plan', [
        'labels' => [
            'name' => __('Investment Plans', 'blackcnote'),
            'singular_name' => __('Investment Plan', 'blackcnote'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_icon' => 'dashicons-chart-line',
        'show_in_rest' => true,
    ]);
}
add_action('init', 'blackcnote_theme_register_post_type');

/**
 * Add theme settings page
 */
function blackcnote_theme_add_settings_page(): void {
    add_options_page(
        __('BlackCnote Theme Settings', 'blackcnote'),
        __('BlackCnote Theme', 'blackcnote'),
        'manage_options',
        'blackcnote-theme-settings',
        'blackcnote_theme_settings_page'
    );
}
add_action('admin_menu', 'blackcnote_theme_add_settings_page');

/**
 * Settings page callback
 */
function blackcnote_theme_settings_page(): void {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings
    if (isset($_POST['blackcnote_theme_settings_nonce']) && 
        wp_verify_nonce($_POST['blackcnote_theme_settings_nonce'], 'blackcnote_theme_settings')) {
        
        update_option('blackcnote_theme_settings', [
            'primary_color' => sanitize_hex_color($_POST['primary_color'] ?? '#007bff'),
            'enable_rtl' => isset($_POST['enable_rtl']),
            'custom_css' => wp_strip_all_tags($_POST['custom_css'] ?? ''),
        ]);

        echo '<div class="notice notice-success"><p>' . 
             esc_html__('Settings saved.', 'blackcnote') . 
             '</p></div>';
    }

    // Get current settings
    $settings = get_option('blackcnote_theme_settings', [
        'primary_color' => '#007bff',
        'enable_rtl' => false,
        'custom_css' => '',
    ]);

    // Output settings form
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('BlackCnote Theme Settings', 'blackcnote'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('blackcnote_theme_settings', 'blackcnote_theme_settings_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="primary_color">
                            <?php esc_html_e('Primary Color', 'blackcnote'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="color" 
                               id="primary_color" 
                               name="primary_color" 
                               value="<?php echo esc_attr($settings['primary_color']); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('RTL Support', 'blackcnote'); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="enable_rtl" 
                                   <?php checked($settings['enable_rtl']); ?>>
                            <?php esc_html_e('Enable RTL support', 'blackcnote'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="custom_css">
                            <?php esc_html_e('Custom CSS', 'blackcnote'); ?>
                        </label>
                    </th>
                    <td>
                        <textarea id="custom_css" 
                                  name="custom_css" 
                                  rows="10" 
                                  class="large-text code"><?php 
                            echo esc_textarea($settings['custom_css']); 
                        ?></textarea>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Register custom shortcodes
 */
function blackcnote_theme_shortcodes(): void {
    add_shortcode('blackcnote_dashboard', 'blackcnote_theme_dashboard_shortcode');
    add_shortcode('blackcnote_plans', 'blackcnote_theme_plans_shortcode');
    add_shortcode('blackcnote_transactions', 'blackcnote_theme_transactions_shortcode');
}
add_action('init', 'blackcnote_theme_shortcodes');

/**
 * Dashboard shortcode callback
 */
function blackcnote_theme_dashboard_shortcode($atts): string {
    if (!is_user_logged_in()) {
        return '<p>' . esc_html__('Please log in to view your dashboard.', 'blackcnote') . '</p>';
    }

    ob_start();
    get_template_part('template-parts/dashboard');
    return ob_get_clean();
}

/**
 * Plans shortcode callback
 */
function blackcnote_theme_plans_shortcode($atts): string {
    ob_start();
    get_template_part('template-parts/plans');
    return ob_get_clean();
}

/**
 * Transactions shortcode callback
 */
function blackcnote_theme_transactions_shortcode($atts): string {
    if (!is_user_logged_in()) {
        return '<p>' . esc_html__('Please log in to view your transactions.', 'blackcnote') . '</p>';
    }

    ob_start();
    get_template_part('template-parts/transactions');
    return ob_get_clean();
}

/**
 * AJAX handlers
 */
function blackcnote_theme_ajax_handlers(): void {
    add_action('wp_ajax_blackcnote_calculate_return', 'blackcnote_theme_calculate_return');
    add_action('wp_ajax_blackcnote_filter_transactions', 'blackcnote_theme_filter_transactions');
}
add_action('init', 'blackcnote_theme_ajax_handlers');

/**
 * Calculate return AJAX handler
 */
function blackcnote_theme_calculate_return(): void {
    check_ajax_referer('blackcnote_theme_nonce', 'nonce');

    $amount = floatval($_POST['amount'] ?? 0);
    $plan_id = intval($_POST['plan_id'] ?? 0);

    if ($amount <= 0 || $plan_id <= 0) {
        wp_send_json_error(['message' => __('Invalid input.', 'blackcnote')]);
    }

    // Get plan details from HYIPLab
    global $wpdb;
    $plan = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE id = %d",
        $plan_id
    ));

    if (!$plan) {
        wp_send_json_error(['message' => __('Plan not found.', 'blackcnote')]);
    }

    // Calculate return
    $return_amount = $amount * (1 + ($plan->return_rate / 100));

    wp_send_json_success([
        'return_amount' => $return_amount,
        'plan_name' => $plan->name,
    ]);
}

/**
 * Filter transactions AJAX handler
 */
function blackcnote_theme_filter_transactions(): void {
    check_ajax_referer('blackcnote_theme_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => __('Please log in.', 'blackcnote')]);
    }

    $type = sanitize_text_field($_POST['type'] ?? '');
    $date_from = sanitize_text_field($_POST['date_from'] ?? '');
    $date_to = sanitize_text_field($_POST['date_to'] ?? '');

    // Build query
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->prefix}hyiplab_transactions WHERE user_id = %d";
    $params = [get_current_user_id()];

    if ($type) {
        $query .= " AND type = %s";
        $params[] = $type;
    }

    if ($date_from) {
        $query .= " AND created_at >= %s";
        $params[] = $date_from;
    }

    if ($date_to) {
        $query .= " AND created_at <= %s";
        $params[] = $date_to;
    }

    $query .= " ORDER BY created_at DESC";

    $transactions = $wpdb->get_results($wpdb->prepare($query, $params));

    ob_start();
    get_template_part('template-parts/transactions-table', null, ['transactions' => $transactions]);
    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
}

/**
 * Schedule daily tasks
 */
function blackcnote_theme_schedule_tasks(): void {
    if (!wp_next_scheduled('blackcnote_theme_daily_cron')) {
        wp_schedule_event(time(), 'daily', 'blackcnote_theme_daily_cron');
    }
}
add_action('wp', 'blackcnote_theme_schedule_tasks');

/**
 * Daily cron task
 */
function blackcnote_theme_daily_cron_task(): void {
    // Process interest calculations
    global $wpdb;
    
    $active_investments = $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}hyiplab_transactions 
         WHERE type = 'investment' AND status = 'active'"
    );

    foreach ($active_investments as $investment) {
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE id = %d",
            $investment->plan_id
        ));

        if ($plan) {
            $interest = $investment->amount * ($plan->return_rate / 100);
            
            $wpdb->insert(
                $wpdb->prefix . 'hyiplab_transactions',
                [
                    'user_id' => $investment->user_id,
                    'type' => 'interest',
                    'amount' => $interest,
                    'status' => 'completed',
                    'created_at' => current_time('mysql'),
                ],
                ['%d', '%s', '%f', '%s', '%s']
            );
        }
    }
}
add_action('blackcnote_theme_daily_cron', 'blackcnote_theme_daily_cron_task');

/**
 * Automatically create required pages on theme activation.
 */
function blackcnote_create_required_pages() {
    $pages = [
        [
            'title' => 'Home',
            'slug' => 'home',
            'content' => '<h2>Welcome to BlackCnote!</h2><p>This is your homepage. Add content with Elementor, the WordPress editor, or import demo content for a full experience.</p>',
        ],
        [
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'content' => '<h2>Dashboard</h2><p>Your dashboard content goes here.</p>',
        ],
        [
            'title' => 'Plans',
            'slug' => 'plans',
            'content' => '<h2>Investment Plans</h2><p>Display your plans here or use the [blackcnote_plans] shortcode.</p>',
        ],
        [
            'title' => 'Transactions',
            'slug' => 'transactions',
            'content' => '<h2>Transactions</h2><p>Display your transactions here or use the [blackcnote_transactions] shortcode.</p>',
        ],
        [
            'title' => 'Calculator',
            'slug' => 'calculator',
            'content' => '<h2>Calculator</h2><p>Display your calculator here or use the [blackcnote_plans] shortcode.</p>',
        ],
        [
            'title' => 'About',
            'slug' => 'about',
            'content' => '<h2>About BlackCnote</h2><p>Empowering Black Wealth Through Strategic Investment.</p>',
        ],
        [
            'title' => 'Contact',
            'slug' => 'contact',
            'content' => '<h2>Contact Us</h2><p>Email: info@blackcnote.com<br>Phone: +1 234 567 890<br>Address: 123 Street Name, City, Country</p>',
        ],
    ];
    $home_page_id = null;
    foreach ($pages as $page) {
        $existing = get_page_by_path($page['slug']);
        if (!$existing) {
            $page_id = wp_insert_post([
                'post_title'   => $page['title'],
                'post_name'    => $page['slug'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => $page['content'] ?? '',
            ]);
            if ($page_id && !is_wp_error($page_id)) {
                if ($page['slug'] === 'home') {
                    $home_page_id = $page_id;
                }
            }
        } else {
            if ($page['slug'] === 'home') {
                $home_page_id = $existing->ID;
            }
        }
    }
    // Set Home as static front page
    if ($home_page_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_page_id);
    }
}
add_action('after_switch_theme', 'blackcnote_create_required_pages');

// Add admin tool for one-click demo content import
add_action('admin_menu', function() {
    add_submenu_page(
        'themes.php',
        __('Import Demo Content', 'blackcnote'),
        __('Import Demo Content', 'blackcnote'),
        'manage_options',
        'blackcnote-import-demo-content',
        'blackcnote_import_demo_content_page'
    );
});

function blackcnote_import_demo_content_page() {
    echo '<div class="wrap"><h1>' . esc_html__('Import Demo Content', 'blackcnote') . '</h1>';
    if (isset($_POST['blackcnote_import_demo_content']) && check_admin_referer('blackcnote_import_demo_content')) {
        $import_file = get_template_directory() . '/blackcnote-demo-content.xml';
        if (file_exists($import_file)) {
            if (!class_exists('WP_Import')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
                $plugin = 'wordpress-importer/wordpress-importer.php';
                if (!is_plugin_active($plugin)) {
                    activate_plugin($plugin);
                }
                if (!class_exists('WP_Import')) {
                    require_once ABSPATH . 'wp-content/plugins/wordpress-importer/wordpress-importer.php';
                }
            }
            if (class_exists('WP_Import')) {
                ob_start();
                $importer = new WP_Import();
                $importer->fetch_attachments = true;
                $importer->import($import_file);
                ob_end_clean();
                echo '<div class="notice notice-success"><p>' . esc_html__('Demo content imported successfully!', 'blackcnote') . '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>' . esc_html__('WordPress Importer plugin is not available. Please install and activate it first.', 'blackcnote') . '</p></div>';
            }
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__('Demo content XML file not found in the theme directory. Please export demo content from Tools > Export and save as blackcnote-demo-content.xml in your theme folder.', 'blackcnote') . '</p></div>';
        }
    }
    echo '<form method="post">';
    wp_nonce_field('blackcnote_import_demo_content');
    echo '<p>' . esc_html__('Click the button below to import demo content. This will create sample pages, posts, and categories.', 'blackcnote') . '</p>';
    echo '<input type="submit" class="button button-primary" name="blackcnote_import_demo_content" value="' . esc_attr__('Import Demo Content', 'blackcnote') . '" />';
    echo '</form></div>';
}

// Admin notification for required plugins
add_action('admin_notices', function() {
    // List your required plugins here (slug => name)
    $required_plugins = [
        'wordpress-importer' => 'WordPress Importer',
        // Add more required plugins as needed
    ];
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    foreach ($required_plugins as $slug => $name) {
        $plugin_file = $slug . '/' . $slug . '.php';
        if (!is_plugin_active($plugin_file)) {
            $install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $slug), 'install-plugin_' . $slug);
            $activate_url = wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=' . $plugin_file), 'activate-plugin_' . $plugin_file);
            $plugin_installed = file_exists(WP_PLUGIN_DIR . '/' . $plugin_file);
            echo '<div class="notice notice-warning is-dismissible"><p>';
            echo esc_html($name) . ' ' . esc_html__('is required for the BlackCnote theme. ', 'blackcnote');
            if (!$plugin_installed) {
                echo '<a href="' . esc_url($install_url) . '" class="button button-primary">' . esc_html__('Install Now', 'blackcnote') . '</a> ';
            } else {
                echo '<a href="' . esc_url($activate_url) . '" class="button button-primary">' . esc_html__('Activate Now', 'blackcnote') . '</a> ';
            }
            echo '</p></div>';
        }
    }
});

/**
 * Delete broken themes functionality
 */
function blackcnote_delete_broken_themes() {
    $themes_dir = get_theme_root();
    $broken_themes = [];
    
    // Get all theme directories
    $theme_dirs = glob($themes_dir . '/*', GLOB_ONLYDIR);
    
    foreach ($theme_dirs as $theme_dir) {
        $theme_name = basename($theme_dir);
        
        // Skip current theme and parent theme
        if ($theme_name === get_template() || $theme_name === get_stylesheet()) {
            continue;
        }
        
        // Check if theme has required files
        $style_file = $theme_dir . '/style.css';
        $functions_file = $theme_dir . '/functions.php';
        $index_file = $theme_dir . '/index.php';
        
        $is_broken = false;
        $missing_files = [];
        
        // Check for missing essential files
        if (!file_exists($style_file)) {
            $is_broken = true;
            $missing_files[] = 'style.css';
        }
        
        if (!file_exists($index_file)) {
            $is_broken = true;
            $missing_files[] = 'index.php';
        }
        
        // Check if style.css has valid theme headers
        if (file_exists($style_file)) {
            $theme_data = get_file_data($style_file, [
                'ThemeName' => 'Theme Name',
                'ThemeURI' => 'Theme URI',
                'Description' => 'Description',
                'Author' => 'Author',
                'Version' => 'Version'
            ]);
            
            if (empty($theme_data['ThemeName'])) {
                $is_broken = true;
                $missing_files[] = 'valid theme headers';
            }
        }
        
        if ($is_broken) {
            $broken_themes[] = [
                'name' => $theme_name,
                'path' => $theme_dir,
                'missing' => $missing_files
            ];
        }
    }
    
    return $broken_themes;
}

/**
 * Auto-delete broken themes on theme activation
 */
function blackcnote_auto_delete_broken_themes() {
    // Only run if setting is enabled
    if (get_option('blackcnote_auto_delete_broken_themes', true)) {
        $broken_themes = blackcnote_delete_broken_themes();
        
        foreach ($broken_themes as $theme) {
            // Use WordPress filesystem API for safe deletion
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                WP_Filesystem();
            }
            
            if ($wp_filesystem->delete($theme['path'], true)) {
                // Log deletion
                error_log("BlackCnote: Deleted broken theme: " . $theme['name']);
            }
        }
    }
}
add_action('after_switch_theme', 'blackcnote_auto_delete_broken_themes');

/**
 * Add theme maintenance admin page
 */
add_action('admin_menu', function() {
    add_submenu_page(
        'themes.php',
        __('Theme Maintenance', 'blackcnote'),
        __('Theme Maintenance', 'blackcnote'),
        'manage_options',
        'blackcnote-theme-maintenance',
        'blackcnote_theme_maintenance_page'
    );
});

function blackcnote_theme_maintenance_page() {
    echo '<div class="wrap"><h1>' . esc_html__('Theme Maintenance', 'blackcnote') . '</h1>';
    
    // Handle form submissions
    if (isset($_POST['blackcnote_delete_broken_themes']) && check_admin_referer('blackcnote_delete_broken_themes')) {
        $broken_themes = blackcnote_delete_broken_themes();
        $deleted_count = 0;
        
        foreach ($broken_themes as $theme) {
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                WP_Filesystem();
            }
            
            if ($wp_filesystem->delete($theme['path'], true)) {
                $deleted_count++;
            }
        }
        
        echo '<div class="notice notice-success"><p>' . 
             sprintf(esc_html__('Successfully deleted %d broken themes.', 'blackcnote'), $deleted_count) . 
             '</p></div>';
    }
    
    if (isset($_POST['blackcnote_toggle_auto_delete']) && check_admin_referer('blackcnote_toggle_auto_delete')) {
        $current_setting = get_option('blackcnote_auto_delete_broken_themes', true);
        update_option('blackcnote_auto_delete_broken_themes', !$current_setting);
        echo '<div class="notice notice-success"><p>' . 
             esc_html__('Auto-delete setting updated successfully.', 'blackcnote') . 
             '</p></div>';
    }
    
    // Display current broken themes
    $broken_themes = blackcnote_delete_broken_themes();
    $auto_delete_enabled = get_option('blackcnote_auto_delete_broken_themes', true);
    
    echo '<div class="card"><h2>' . esc_html__('Broken Themes Detection', 'blackcnote') . '</h2>';
    
    if (empty($broken_themes)) {
        echo '<p>' . esc_html__('No broken themes detected. Your theme directory is clean!', 'blackcnote') . '</p>';
    } else {
        echo '<p>' . sprintf(esc_html__('Found %d broken theme(s):', 'blackcnote'), count($broken_themes)) . '</p>';
        echo '<ul>';
        foreach ($broken_themes as $theme) {
            echo '<li><strong>' . esc_html($theme['name']) . '</strong> - ' . 
                 esc_html__('Missing:', 'blackcnote') . ' ' . esc_html(implode(', ', $theme['missing'])) . '</li>';
        }
        echo '</ul>';
        
        echo '<form method="post" style="margin-top: 20px;">';
        wp_nonce_field('blackcnote_delete_broken_themes');
        echo '<input type="submit" class="button button-primary" name="blackcnote_delete_broken_themes" value="' . 
             esc_attr__('Delete All Broken Themes', 'blackcnote') . '" />';
        echo '</form>';
    }
    echo '</div>';
    
    // Auto-delete settings
    echo '<div class="card" style="margin-top: 20px;"><h2>' . esc_html__('Auto-Delete Settings', 'blackcnote') . '</h2>';
    echo '<p>' . esc_html__('Automatically delete broken themes when switching themes:', 'blackcnote') . '</p>';
    echo '<p><strong>' . esc_html__('Current setting:', 'blackcnote') . '</strong> ' . 
         ($auto_delete_enabled ? esc_html__('Enabled', 'blackcnote') : esc_html__('Disabled', 'blackcnote')) . '</p>';
    
    echo '<form method="post">';
    wp_nonce_field('blackcnote_toggle_auto_delete');
    echo '<input type="submit" class="button button-secondary" name="blackcnote_toggle_auto_delete" value="' . 
         esc_attr__($auto_delete_enabled ? 'Disable Auto-Delete' : 'Enable Auto-Delete', 'blackcnote') . '" />';
    echo '</form>';
    echo '</div>';
    
    echo '</div>';
}

/**
 * Add theme maintenance notice in admin
 */
add_action('admin_notices', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'themes.php') {
        $broken_themes = blackcnote_delete_broken_themes();
        if (!empty($broken_themes)) {
            echo '<div class="notice notice-warning is-dismissible"><p>';
            echo sprintf(
                esc_html__('BlackCnote detected %d broken theme(s). ', 'blackcnote'),
                count($broken_themes)
            );
            echo '<a href="' . esc_url(admin_url('themes.php?page=blackcnote-theme-maintenance')) . '" class="button button-primary">' . 
                 esc_html__('Manage Broken Themes', 'blackcnote') . '</a>';
            echo '</p></div>';
        }
    }
});

/**
 * Clean up theme directory on deactivation
 */
function blackcnote_cleanup_on_deactivation() {
    // Delete temporary files and directories
    $temp_dirs = [
        get_template_directory() . '/temp',
        get_template_directory() . '/cache',
        get_template_directory() . '/backup'
    ];
    
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once(ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }
    
    foreach ($temp_dirs as $dir) {
        if ($wp_filesystem->exists($dir)) {
            $wp_filesystem->delete($dir, true);
        }
    }
}
register_deactivation_hook(__FILE__, 'blackcnote_cleanup_on_deactivation');

// --- BlackCnote: Auto-install and activate Full Content Checker plugin on theme activation ---
add_action('after_switch_theme', function() {
    $plugin_dir = WP_PLUGIN_DIR . '/full-content-checker';
    $plugin_file = $plugin_dir . '/full-content-checker.php';
    $theme_plugin = get_template_directory() . '/inc/full-content-checker.php';
    // Copy plugin if not present
    if (!file_exists($plugin_file) && file_exists($theme_plugin)) {
        if (!file_exists($plugin_dir)) {
            mkdir($plugin_dir, 0755, true);
        }
        copy($theme_plugin, $plugin_file);
    }
    // Activate plugin if not active
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if (file_exists($plugin_file) && !is_plugin_active('full-content-checker/full-content-checker.php')) {
        activate_plugin('full-content-checker/full-content-checker.php');
    }
});

// --- BlackCnote: Demo content import admin notice and page ---
add_action('admin_notices', function() {
    if (get_option('blackcnote_demo_imported')) return;
    if (get_template() !== 'blackcnote') return;
    $import_url = admin_url('themes.php?page=blackcnote-demo-import');
    echo '<div class="notice notice-success"><p>';
    echo 'Welcome to BlackCnote! <a href="' . esc_url($import_url) . '">Click here to import demo content</a> (pages, header, footer, sections, etc.).';
    echo '</p></div>';
});

add_action('admin_menu', function() {
    add_theme_page('Import Demo Content', 'Import Demo Content', 'manage_options', 'blackcnote-demo-import', function() {
        if (isset($_POST['import_demo_content'])) {
            $file = get_template_directory() . '/blackcnote-demo-content.xml';
            if (file_exists($file)) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/import.php';
                $importer = 'wordpress-importer/wordpress-importer.php';
                if (!class_exists('WP_Import')) {
                    // Try to load the importer if not present
                    $importer_path = WP_PLUGIN_DIR . '/wordpress-importer/wordpress-importer.php';
                    if (file_exists($importer_path)) {
                        include_once $importer_path;
                    } else {
                        echo '<div class="notice notice-error"><p>Please install and activate the <strong>WordPress Importer</strong> plugin first.</p></div>';
                        return;
                    }
                }
                if (class_exists('WP_Import')) {
                    ob_start();
                    $importer = new WP_Import();
                    $importer->fetch_attachments = true;
                    $importer->import($file);
                    ob_end_clean();
                    update_option('blackcnote_demo_imported', 1);
                    echo '<div class="notice notice-success"><p>Demo content imported successfully!</p></div>';
                }
            } else {
                echo '<div class="notice notice-error"><p>Demo content file not found.</p></div>';
            }
        }
        ?>
        <div class="wrap">
            <h1>Import BlackCnote Demo Content</h1>
            <form method="post">
                <p>This will import all demo pages, header, footer, and sections. Existing content will not be deleted, but may be duplicated.</p>
                <input type="submit" name="import_demo_content" class="button button-primary" value="Import Demo Content">
            </form>
        </div>
        <?php
    });
}); 