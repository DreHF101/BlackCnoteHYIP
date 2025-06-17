<?php
/**
 * BlackCnote Theme functions and definitions
 *
 * @package BlackCnote_Theme
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
        get_template_directory_uri() . '/assets/css/blackcnote-theme.css',
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
        get_template_directory_uri() . '/assets/js/blackcnote-theme.js',
        ['jquery', 'bootstrap'],
        wp_get_theme()->get('Version'),
        true
    );

    // Localize script
    wp_localize_script('blackcnote-theme-script', 'blackcnote_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('blackcnote_nonce'),
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
            "SELECT * FROM {$wpdb->prefix}blackcnotelab_plans 
            ORDER BY %s %s 
            LIMIT %d",
            $atts['orderby'],
            $atts['order'],
            $atts['limit']
        ));

        ob_start();
        ?>
        <div class="blackcnote-plans">
            <?php foreach ($plans as $plan): ?>
                <div class="blackcnote-card">
                    <h3><?php echo esc_html($plan->name); ?></h3>
                    <div class="plan-details">
                        <p class="return-rate">
                            <?php echo esc_html__('Return Rate:', 'blackcnote'); ?>
                            <?php echo esc_html($plan->interest_rate); ?>%
                        </p>
                        <p class="min-investment">
                            <?php echo esc_html__('Min Investment:', 'blackcnote'); ?>
                            <?php echo esc_html($plan->min_amount); ?>
                        </p>
                        <p class="max-investment">
                            <?php echo esc_html__('Max Investment:', 'blackcnote'); ?>
                            <?php echo esc_html($plan->max_amount); ?>
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
    check_ajax_referer('blackcnote_nonce', 'nonce');

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
            "SELECT * FROM {$wpdb->prefix}blackcnotelab_plans WHERE id = %d",
            $plan_id
        ));

        if (!$plan) {
            throw new Exception(__('Plan not found.', 'blackcnote'));
        }

        if ($amount < $plan->min_amount || $amount > $plan->max_amount) {
            throw new Exception(__('Amount is outside the allowed range.', 'blackcnote'));
        }

        $return_amount = $amount * (1 + ($plan->interest_rate / 100));
        
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
 * Check if BlackCnoteLab plugin is active
 */
function blackcnote_is_plugin_active() {
    return function_exists('blackcnotelab_system_instance');
}

/**
 * Add custom body classes
 */
function blackcnote_body_classes($classes) {
    if (blackcnote_is_plugin_active()) {
        $classes[] = 'blackcnotelab-active';
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
    check_ajax_referer('blackcnote_nonce', 'nonce');

    $amount = floatval($_POST['amount'] ?? 0);
    $plan_id = intval($_POST['plan_id'] ?? 0);

    if ($amount <= 0 || $plan_id <= 0) {
        wp_send_json_error(['message' => __('Invalid input.', 'blackcnote')]);
    }

    // Get plan details from BlackCnoteLab
    global $wpdb;
    $plan = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}blackcnotelab_plans WHERE id = %d",
        $plan_id
    ));

    if (!$plan) {
        wp_send_json_error(['message' => __('Plan not found.', 'blackcnote')]);
    }

    // Calculate return
    $return_amount = $amount * (1 + ($plan->interest_rate / 100));

    wp_send_json_success([
        'return_amount' => $return_amount,
        'plan_name' => $plan->name,
    ]);
}

/**
 * Filter transactions AJAX handler
 */
function blackcnote_theme_filter_transactions(): void {
    check_ajax_referer('blackcnote_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => __('Please log in.', 'blackcnote')]);
    }

    $type = sanitize_text_field($_POST['type'] ?? '');
    $date_from = sanitize_text_field($_POST['date_from'] ?? '');
    $date_to = sanitize_text_field($_POST['date_to'] ?? '');

    // Build query
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->prefix}blackcnotelab_transactions WHERE user_id = %d";
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
        "SELECT * FROM {$wpdb->prefix}blackcnotelab_transactions 
         WHERE type = 'investment' AND status = 'active'"
    );

    foreach ($active_investments as $investment) {
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}blackcnotelab_plans WHERE id = %d",
            $investment->plan_id
        ));

        if ($plan) {
            $interest = $investment->amount * ($plan->interest_rate / 100);
            
            $wpdb->insert(
                $wpdb->prefix . 'blackcnotelab_transactions',
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
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'template' => 'template-blackcnote-dashboard.php',
        ],
        [
            'title' => 'Plans',
            'slug' => 'plans',
            'template' => 'template-blackcnote-plans.php',
        ],
        [
            'title' => 'Transactions',
            'slug' => 'transactions',
            'template' => 'template-blackcnote-transactions.php',
        ],
        // Add more pages as needed
    ];

    foreach ($pages as $page) {
        if (!get_page_by_path($page['slug'])) {
            $page_id = wp_insert_post([
                'post_title'   => $page['title'],
                'post_name'    => $page['slug'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ]);
            if ($page_id && !is_wp_error($page_id)) {
                update_post_meta($page_id, '_wp_page_template', $page['template']);
            }
        }
    }
}
add_action('after_switch_theme', 'blackcnote_create_required_pages'); 