<?php
require_once '/var/www/html/wp-load.php';

echo "BlackCnote Infrastructure Completion (Fixed)\n";
echo "===========================================\n\n";

// Activate plugins
if (!is_plugin_active('hyiplab/hyiplab.php')) {
    activate_plugin('hyiplab/hyiplab.php');
    echo "HYIPLab plugin activated\n";
}

if (!is_plugin_active('blackcnote-debug-system/blackcnote-debug-system.php')) {
    activate_plugin('blackcnote-debug-system/blackcnote-debug-system.php');
    echo "Debug System activated\n";
}

if (!is_plugin_active('full-content-checker/full-content-checker.php')) {
    activate_plugin('full-content-checker/full-content-checker.php');
    echo "Full Content Checker activated\n";
}

// Set options
update_option('hyiplab_activated', 1);
update_option('blackcnote_debug_enabled', 1);
update_option('blackcnote_live_editing_enabled', 1);

// Create database tables with correct column names
global $wpdb;

$tables = [
    'hyiplab_plans' => "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}hyiplab_plans` (
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
    
    'hyiplab_users' => "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}hyiplab_users` (
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
    
    'hyiplab_investments' => "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}hyiplab_investments` (
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

// Insert sample plans with correct column names
$sample_plans = [
    ['name' => 'Starter Plan', 'min_investment' => 100.00, 'max_investment' => 1000.00, 'return_rate' => 2.5, 'duration_days' => 30],
    ['name' => 'Premium Plan', 'min_investment' => 1000.00, 'max_investment' => 10000.00, 'return_rate' => 3.5, 'duration_days' => 60],
    ['name' => 'VIP Plan', 'min_investment' => 10000.00, 'max_investment' => 100000.00, 'return_rate' => 5.0, 'duration_days' => 90]
];

foreach ($sample_plans as $plan) {
    $existing = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$wpdb->prefix}hyiplab_plans WHERE name = %s", $plan['name']));
    if (!$existing) {
        $wpdb->insert("{$wpdb->prefix}hyiplab_plans", $plan);
        echo "Created plan: {$plan['name']}\n";
    }
}

// Add REST API endpoints
add_action('rest_api_init', function () {
    register_rest_route('hyiplab/v1', '/status', [
        'methods' => 'GET',
        'callback' => function () {
            return rest_ensure_response(['status' => 'active', 'version' => '3.0']);
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
});

flush_rewrite_rules();

echo "\nBlackCnote infrastructure completed successfully!\n";
?> 