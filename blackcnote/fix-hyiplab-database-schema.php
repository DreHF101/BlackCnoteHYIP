<?php
/**
 * BlackCnote HyipLab Database Schema Fix
 * 
 * This script fixes the database schema mismatch between the HyipLab plugin
 * and the actual database table structure.
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

global $wpdb;

echo "==========================================\n";
echo "BLACKCNOTE HYIPLAB DATABASE SCHEMA FIX\n";
echo "==========================================\n\n";

// Check if table exists
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}hyiplab_plans'");

if (!$table_exists) {
    echo "❌ Table {$wpdb->prefix}hyiplab_plans does not exist. Creating it...\n";
    
    // Create the table with the correct schema
    $sql = "CREATE TABLE `{$wpdb->prefix}hyiplab_plans` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` varchar(40) NOT NULL,
        `minimum` decimal(28,8) NOT NULL DEFAULT 0.00000000,
        `maximum` decimal(28,8) NOT NULL DEFAULT 0.00000000,
        `fixed_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
        `interest` decimal(28,8) NOT NULL DEFAULT 0.00000000,
        `interest_type` tinyint(1) DEFAULT 0 COMMENT '1 = ''%'' / 0 =''currency''',
        `time_setting_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `featured` tinyint(1) NOT NULL DEFAULT 0,
        `capital_back` tinyint(1) DEFAULT 0,
        `compound_interest` tinyint(1) NOT NULL DEFAULT 0,
        `hold_capital` tinyint(1) NOT NULL DEFAULT 0,
        `lifetime` tinyint(1) DEFAULT 0,
        `repeat_time` varchar(40) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $result = $wpdb->query($sql);
    if ($result !== false) {
        echo "✅ Table created successfully\n";
    } else {
        echo "❌ Failed to create table: " . $wpdb->last_error . "\n";
        exit(1);
    }
} else {
    echo "✅ Table {$wpdb->prefix}hyiplab_plans exists\n";
}

// Check current table structure
echo "\nCurrent table structure:\n";
$columns = $wpdb->get_results("DESCRIBE {$wpdb->prefix}hyiplab_plans");
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

// Add missing columns if they don't exist
$missing_columns = [
    'min_investment' => 'ALTER TABLE `' . $wpdb->prefix . 'hyiplab_plans` ADD COLUMN `min_investment` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `name`',
    'max_investment' => 'ALTER TABLE `' . $wpdb->prefix . 'hyiplab_plans` ADD COLUMN `max_investment` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `min_investment`',
    'return_rate' => 'ALTER TABLE `' . $wpdb->prefix . 'hyiplab_plans` ADD COLUMN `return_rate` decimal(5,2) NOT NULL DEFAULT 0.00 AFTER `max_investment`',
    'duration_days' => 'ALTER TABLE `' . $wpdb->prefix . 'hyiplab_plans` ADD COLUMN `duration_days` int(11) NOT NULL DEFAULT 0 AFTER `return_rate`',
    'description' => 'ALTER TABLE `' . $wpdb->prefix . 'hyiplab_plans` ADD COLUMN `description` text AFTER `name`',
    'created_at' => 'ALTER TABLE `' . $wpdb->prefix . 'hyiplab_plans` ADD COLUMN `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `repeat_time`',
    'updated_at' => 'ALTER TABLE `' . $wpdb->prefix . 'hyiplab_plans` ADD COLUMN `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`'
];

echo "\nAdding missing columns...\n";
foreach ($missing_columns as $column_name => $sql) {
    $column_exists = $wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}hyiplab_plans LIKE '$column_name'");
    
    if (!$column_exists) {
        echo "Adding column: $column_name\n";
        $result = $wpdb->query($sql);
        if ($result !== false) {
            echo "✅ Added column: $column_name\n";
        } else {
            echo "❌ Failed to add column $column_name: " . $wpdb->last_error . "\n";
        }
    } else {
        echo "✅ Column $column_name already exists\n";
    }
}

// Copy data from old columns to new columns if needed
echo "\nMigrating data from old columns to new columns...\n";

// Copy minimum to min_investment
$wpdb->query("UPDATE {$wpdb->prefix}hyiplab_plans SET min_investment = minimum WHERE min_investment = 0 AND minimum > 0");
echo "✅ Migrated minimum to min_investment\n";

// Copy maximum to max_investment
$wpdb->query("UPDATE {$wpdb->prefix}hyiplab_plans SET max_investment = maximum WHERE max_investment = 0 AND maximum > 0");
echo "✅ Migrated maximum to max_investment\n";

// Copy interest to return_rate
$wpdb->query("UPDATE {$wpdb->prefix}hyiplab_plans SET return_rate = interest WHERE return_rate = 0 AND interest > 0");
echo "✅ Migrated interest to return_rate\n";

// Insert sample data if table is empty
$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_plans");
if ($count == 0) {
    echo "\nInserting sample investment plans...\n";
    
    $sample_plans = [
        [
            'name' => 'Starter Plan',
            'description' => 'Perfect for beginners with low risk and steady returns',
            'min_investment' => 100.00,
            'max_investment' => 1000.00,
            'return_rate' => 2.5,
            'duration_days' => 30,
            'minimum' => 100.00,
            'maximum' => 1000.00,
            'interest' => 2.5,
            'status' => 1
        ],
        [
            'name' => 'Premium Plan',
            'description' => 'For experienced investors with higher returns',
            'min_investment' => 1000.00,
            'max_investment' => 10000.00,
            'return_rate' => 3.5,
            'duration_days' => 60,
            'minimum' => 1000.00,
            'maximum' => 10000.00,
            'interest' => 3.5,
            'status' => 1
        ],
        [
            'name' => 'VIP Plan',
            'description' => 'Exclusive high-yield investment for VIP members',
            'min_investment' => 10000.00,
            'max_investment' => 100000.00,
            'return_rate' => 5.0,
            'duration_days' => 90,
            'minimum' => 10000.00,
            'maximum' => 100000.00,
            'interest' => 5.0,
            'status' => 1
        ]
    ];
    
    foreach ($sample_plans as $plan) {
        $result = $wpdb->insert("{$wpdb->prefix}hyiplab_plans", $plan);
        if ($result !== false) {
            echo "✅ Inserted plan: {$plan['name']}\n";
        } else {
            echo "❌ Failed to insert plan {$plan['name']}: " . $wpdb->last_error . "\n";
        }
    }
} else {
    echo "\n✅ Table already contains $count plans\n";
}

// Verify the fix
echo "\n==========================================\n";
echo "VERIFICATION\n";
echo "==========================================\n";

$plans = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hyiplab_plans LIMIT 3");
foreach ($plans as $plan) {
    echo "Plan: {$plan->name}\n";
    echo "- Min Investment: {$plan->min_investment}\n";
    echo "- Max Investment: {$plan->max_investment}\n";
    echo "- Return Rate: {$plan->return_rate}%\n";
    echo "- Duration: {$plan->duration_days} days\n";
    echo "---\n";
}

echo "\n✅ Database schema fix completed successfully!\n";
echo "The HyipLab plugin should now work without database errors.\n";
?> 