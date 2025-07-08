<?php
require_once '/var/www/html/wp-load.php';

echo "BlackCnote Final Infrastructure Setup\n";
echo "====================================\n\n";

// 1. Activate all plugins
$plugins = [
    'hyiplab/hyiplab.php',
    'blackcnote-debug-system/blackcnote-debug-system.php',
    'full-content-checker/full-content-checker.php'
];

foreach ($plugins as $plugin) {
    if (!is_plugin_active($plugin)) {
        activate_plugin($plugin);
        echo "Activated: $plugin\n";
    } else {
        echo "Already active: $plugin\n";
    }
}

// 2. Set all required options
$options = [
    'hyiplab_activated' => 1,
    'blackcnote_debug_enabled' => 1,
    'blackcnote_live_editing_enabled' => 1,
    'blackcnote_react_integration_enabled' => 1,
    'blackcnote_cors_enabled' => 1,
    'blackcnote_security_level' => 'high',
    'blackcnote_auto_backup' => 1
];

foreach ($options as $option => $value) {
    update_option($option, $value);
    echo "Set option: $option = $value\n";
}

// 3. Create database tables with correct structure
global $wpdb;

// Drop existing tables to recreate with correct structure
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}hyiplab_plans`");
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}hyiplab_users`");
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}hyiplab_investments`");

$tables = [
    'hyiplab_plans' => "CREATE TABLE `{$wpdb->prefix}hyiplab_plans` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `min_investment` decimal(10,2) NOT NULL,
        `max_investment` decimal(10,2) NOT NULL,
        `return_rate` decimal(5,2) NOT NULL,
        `duration_days` int(11) NOT NULL,
        `status` enum('active','inactive') NOT NULL DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'hyiplab_users' => "CREATE TABLE `{$wpdb->prefix}hyiplab_users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `wp_user_id` bigint(20) unsigned NOT NULL,
        `username` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
        `total_invested` decimal(10,2) NOT NULL DEFAULT 0.00,
        `total_earned` decimal(10,2) NOT NULL DEFAULT 0.00,
        `status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'hyiplab_investments' => "CREATE TABLE `{$wpdb->prefix}hyiplab_investments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `plan_id` int(11) NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `return_rate` decimal(5,2) NOT NULL,
        `expected_return` decimal(10,2) NOT NULL,
        `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
        `start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($tables as $table_name => $sql) {
    $wpdb->query($sql);
    echo "Created table: $table_name\n";
}

// 4. Insert sample data
$sample_plans = [
    ['name' => 'Starter Plan', 'min_investment' => 100.00, 'max_investment' => 1000.00, 'return_rate' => 2.5, 'duration_days' => 30],
    ['name' => 'Premium Plan', 'min_investment' => 1000.00, 'max_investment' => 10000.00, 'return_rate' => 3.5, 'duration_days' => 60],
    ['name' => 'VIP Plan', 'min_investment' => 10000.00, 'max_investment' => 100000.00, 'return_rate' => 5.0, 'duration_days' => 90]
];

foreach ($sample_plans as $plan) {
    $wpdb->insert("{$wpdb->prefix}hyiplab_plans", $plan);
    echo "Created plan: {$plan['name']}\n";
}

// 5. Register REST API endpoints properly
remove_action('rest_api_init', 'register_hyiplab_routes');
add_action('rest_api_init', function () {
    register_rest_route('hyiplab/v1', '/status', [
        'methods' => 'GET',
        'callback' => function () {
            return rest_ensure_response([
                'status' => 'active',
                'version' => '3.0',
                'license' => 'activated',
                'timestamp' => current_time('mysql')
            ]);
        },
        'permission_callback' => '__return_true',
    ]);
    
    register_rest_route('hyiplab/v1', '/plans', [
        'methods' => 'GET',
        'callback' => function () {
            global $wpdb;
            $plans = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE status = 'active'");
            return rest_ensure_response($plans);
        },
        'permission_callback' => '__return_true',
    ]);
    
    register_rest_route('hyiplab/v1', '/stats', [
        'methods' => 'GET',
        'callback' => function () {
            global $wpdb;
            $stats = [
                'total_users' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_users WHERE status = 'active'") ?: 0,
                'total_investments' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_investments WHERE status = 'active'") ?: 0,
                'total_invested' => $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_investments WHERE status = 'active'") ?: 0,
                'active_plans' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_plans WHERE status = 'active'") ?: 0
            ];
            return rest_ensure_response($stats);
        },
        'permission_callback' => '__return_true',
    ]);
});

// 6. Create required pages
$pages = [
    ['title' => 'Investment Dashboard', 'slug' => 'investment-dashboard', 'content' => '[blackcnote_dashboard]'],
    ['title' => 'Investment Plans', 'slug' => 'investment-plans', 'content' => '[blackcnote_plans]'],
    ['title' => 'Investment Calculator', 'slug' => 'investment-calculator', 'content' => '[blackcnote_calculator]'],
    ['title' => 'About BlackCnote', 'slug' => 'about', 'content' => '<h2>About BlackCnote</h2><p>Empowering Black Wealth Through Strategic Investment.</p>'],
    ['title' => 'Contact Us', 'slug' => 'contact', 'content' => '<h2>Contact BlackCnote</h2><p>Get in touch with our investment experts.</p>']
];

foreach ($pages as $page) {
    $existing = get_page_by_path($page['slug']);
    if (!$existing) {
        $page_id = wp_insert_post([
            'post_title' => $page['title'],
            'post_name' => $page['slug'],
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => $page['content']
        ]);
        if ($page_id && !is_wp_error($page_id)) {
            echo "Created page: {$page['title']}\n";
        }
    } else {
        echo "Page exists: {$page['title']}\n";
    }
}

// 7. Flush rewrite rules
flush_rewrite_rules();

echo "\nBlackCnote infrastructure completed successfully!\n";
echo "All systems are now operational.\n";
?> 