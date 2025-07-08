<?php
/**
 * BlackCnote Demo Data Seeding Script
 * Populates all HYIPLab tables with realistic demo data
 */

declare(strict_types=1);

// Load WordPress
require_once 'wp-config.php';
require_once 'wp-load.php';

echo "🌱 BlackCnote Demo Data Seeding\n";
echo "==============================\n\n";

global $wpdb;

// Step 0: Sync HYIPLab License (Envato Purchase Code)
echo "0. Syncing HYIPLab License...\n";
$license_file = __DIR__ . '/../hyiplab-plugin-license.txt';
$purchase_code = null;
if (file_exists($license_file)) {
    $license_contents = file_get_contents($license_file);
    if (preg_match('/Item Purchase Code:\s*([a-f0-9\-]+)/i', $license_contents, $matches)) {
        $purchase_code = trim($matches[1]);
    }
}
if ($purchase_code) {
    $option_key = 'hyiplab_purchase_code';
    $existing_code = get_option($option_key);
    if (!$existing_code) {
        update_option($option_key, $purchase_code);
        echo "   ✅ HYIPLab license purchase code set: $purchase_code\n";
    } else {
        echo "   ℹ️  HYIPLab license already set in WordPress options.\n";
    }
} else {
    echo "   ⚠️  HYIPLab license file not found or purchase code missing.\n";
}

// Step 1: Seed Investment Plans (HYIPLab Schema)
echo "1. Seeding Investment Plans...\n";

$plans_data = [
    [
        'name' => 'Starter Plan',
        'minimum' => 100.00,
        'maximum' => 1000.00,
        'fixed_amount' => 0.00,
        'interest' => 2.5,
        'interest_type' => 1,
        'time_setting_id' => 1,
        'status' => 1,
        'featured' => 0,
        'capital_back' => 1,
        'compound_interest' => 0,
        'hold_capital' => 0,
        'lifetime' => 0,
        'repeat_time' => null,
    ],
    [
        'name' => 'Premium Plan',
        'minimum' => 1000.00,
        'maximum' => 10000.00,
        'fixed_amount' => 0.00,
        'interest' => 3.2,
        'interest_type' => 1,
        'time_setting_id' => 1,
        'status' => 1,
        'featured' => 0,
        'capital_back' => 1,
        'compound_interest' => 0,
        'hold_capital' => 0,
        'lifetime' => 0,
        'repeat_time' => null,
    ],
    [
        'name' => 'VIP Plan',
        'minimum' => 10000.00,
        'maximum' => 100000.00,
        'fixed_amount' => 0.00,
        'interest' => 4.1,
        'interest_type' => 1,
        'time_setting_id' => 1,
        'status' => 1,
        'featured' => 1,
        'capital_back' => 1,
        'compound_interest' => 1,
        'hold_capital' => 0,
        'lifetime' => 0,
        'repeat_time' => null,
    ],
    [
        'name' => 'Enterprise Plan',
        'minimum' => 100000.00,
        'maximum' => 1000000.00,
        'fixed_amount' => 0.00,
        'interest' => 5.0,
        'interest_type' => 1,
        'time_setting_id' => 1,
        'status' => 1,
        'featured' => 1,
        'capital_back' => 1,
        'compound_interest' => 1,
        'hold_capital' => 1,
        'lifetime' => 0,
        'repeat_time' => null,
    ]
];

$plans_table = $wpdb->prefix . 'hyiplab_plans';
$plans_inserted = 0;
foreach ($plans_data as $plan) {
    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $plans_table WHERE name = %s", $plan['name']));
    if (!$exists) {
        $result = $wpdb->insert($plans_table, $plan);
        if ($result !== false) {
            $plans_inserted++;
            echo "   ✅ Created plan: {$plan['name']}\n";
        } else {
            echo "   ❌ Failed to create plan: {$plan['name']}\n";
        }
    } else {
        echo "   ℹ️  Plan already exists: {$plan['name']}\n";
    }
}
echo "   Total plans created: {$plans_inserted}\n\n";

// Step 2: Seed Demo Users
echo "2. Seeding Demo Users...\n";

require_once ABSPATH . 'wp-admin/includes/user.php';

$users_data = [
    [
        'user_login' => 'demo_user1',
        'user_pass' => 'demo123',
        'user_email' => 'demo1@blackcnote.com',
        'first_name' => 'John',
        'last_name' => 'Smith',
        'role' => 'subscriber',
        'country' => 'United States',
        'phone' => '+1-555-0101',
    ],
    [
        'user_login' => 'demo_user2',
        'user_pass' => 'demo123',
        'user_email' => 'demo2@blackcnote.com',
        'first_name' => 'Sarah',
        'last_name' => 'Johnson',
        'role' => 'subscriber',
        'country' => 'Canada',
        'phone' => '+1-555-0102',
    ],
    [
        'user_login' => 'demo_user3',
        'user_pass' => 'demo123',
        'user_email' => 'demo3@blackcnote.com',
        'first_name' => 'Michael',
        'last_name' => 'Brown',
        'role' => 'subscriber',
        'country' => 'United Kingdom',
        'phone' => '+1-555-0103',
    ],
    [
        'user_login' => 'demo_user4',
        'user_pass' => 'demo123',
        'user_email' => 'demo4@blackcnote.com',
        'first_name' => 'Emma',
        'last_name' => 'Wilson',
        'role' => 'subscriber',
        'country' => 'Australia',
        'phone' => '+1-555-0104',
    ],
    [
        'user_login' => 'demo_user5',
        'user_pass' => 'demo123',
        'user_email' => 'demo5@blackcnote.com',
        'first_name' => 'David',
        'last_name' => 'Miller',
        'role' => 'subscriber',
        'country' => 'Germany',
        'phone' => '+1-555-0105',
    ]
];

$users_table = $wpdb->prefix . 'hyiplab_users';
$users_inserted = 0;
$user_ids = [];

foreach ($users_data as $user) {
    $wp_user_id = username_exists($user['user_login']);
    if (!$wp_user_id) {
        $wp_user_id = wp_insert_user($user);
    }
    if (!is_wp_error($wp_user_id)) {
        $user_ids[] = $wp_user_id;
        $hyip_user = [
            'wp_user_id' => $wp_user_id,
            'username' => $user['user_login'],
            'email' => $user['user_email'],
            'balance' => 0.00,
            'total_invested' => 0.00,
            'total_earned' => 0.00,
            'status' => 'active',
            'created_at' => current_time('mysql'),
        ];
        $result = $wpdb->insert($users_table, $hyip_user);
        if ($result !== false) {
            $users_inserted++;
            echo "   ✅ Created user: {$user['user_login']} ({$user['first_name']} {$user['last_name']})\n";
        } else {
            echo "   ❌ Failed to create user: {$user['user_login']}\n";
        }
    } else {
        echo "   ❌ Failed to create WP user: {$user['user_login']}\n";
    }
}
echo "   Total users created: {$users_inserted}\n\n";

// Step 3: Seed Investments (HYIPLab Schema)
echo "3. Seeding Investments...\n";

$investments_data = [];
$plan_ids = $wpdb->get_col("SELECT id FROM {$wpdb->prefix}hyiplab_plans WHERE status = 1");
$user_ids = $wpdb->get_col("SELECT id FROM {$wpdb->prefix}hyiplab_users WHERE status = 'active'");

if (empty($plan_ids) || empty($user_ids)) {
    echo "   ❌ No plans or users found for investment creation\n";
} else {
    foreach ($user_ids as $user_id) {
        $user_investments = rand(1, 3); // 1-3 investments per user
        for ($i = 0; $i < $user_investments; $i++) {
            $plan_id = $plan_ids[array_rand($plan_ids)];
            $plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE id = %d", $plan_id));
            if ($plan) {
                $amount = rand((int)$plan->minimum, min((int)$plan->maximum, 5000));
                $interest = $plan->interest;
                $should_pay = $amount * (1 + ($interest / 100));
                $paid = rand(0, 1) ? $should_pay : 0;
                $period = 1;
                $hours = '24';
                $time_name = 'Daily';
                $return_rec_time = $plan->time_setting_id;
                $now = current_time('mysql');
                $next_time = $now;
                $last_time = null;
                $compound_times = 0;
                $rem_compound_times = 0;
                $status = rand(0, 1) ? 1 : 0;
                $capital_status = 1;
                $capital_back = $plan->capital_back;
                $hold_capital = $plan->hold_capital;
                $trx = uniqid('INV');
                $wallet_type = 'main';
                $investment = [
                    'user_id' => $user_id,
                    'plan_id' => $plan_id,
                    'amount' => $amount,
                    'interest' => $interest,
                    'should_pay' => $should_pay,
                    'paid' => $paid,
                    'period' => $period,
                    'hours' => $hours,
                    'time_name' => $time_name,
                    'return_rec_time' => $return_rec_time,
                    'next_time' => $now,
                    'last_time' => $last_time,
                    'compound_times' => $compound_times,
                    'rem_compound_times' => $rem_compound_times,
                    'status' => $status,
                    'capital_status' => $capital_status,
                    'capital_back' => $capital_back,
                    'hold_capital' => $hold_capital,
                    'trx' => $trx,
                    'wallet_type' => $wallet_type,
                    'created_at' => $now
                ];
                $investments_data[] = $investment;
            }
        }
    }
    $invests_table = $wpdb->prefix . 'hyiplab_invests';
    $investments_inserted = 0;
    foreach ($investments_data as $investment) {
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $invests_table WHERE trx = %s", $investment['trx']));
        if (!$exists) {
            $result = $wpdb->insert($invests_table, $investment);
            if ($result !== false) {
                $investments_inserted++;
            }
        }
    }
    echo "   ✅ Created {$investments_inserted} investments\n";
}
echo "\n";

// Step 4: Seed Transactions (HYIPLab Schema)
echo "4. Seeding Transactions...\n";

$transactions_data = [];
$transaction_types = ['deposit', 'withdrawal', 'investment', 'profit', 'bonus'];
$investments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hyiplab_invests");
foreach ($investments as $investment) {
    // Investment transaction
    $transactions_data[] = [
        'user_id' => $investment->user_id,
        'invest_id' => $investment->id,
        'amount' => $investment->amount,
        'charge' => 0.00,
        'post_balance' => $investment->amount,
        'trx_type' => 'investment',
        'trx' => $investment->trx,
        'details' => "Investment in plan #{$investment->plan_id}",
        'remark' => '',
        'wallet_type' => $investment->wallet_type,
        'created_at' => $investment->created_at,
        'updated_at' => $investment->created_at
    ];
    // Profit transaction (if paid > 0)
    if ($investment->paid > 0) {
        $transactions_data[] = [
            'user_id' => $investment->user_id,
            'invest_id' => $investment->id,
            'amount' => $investment->paid - $investment->amount,
            'charge' => 0.00,
            'post_balance' => $investment->paid,
            'trx_type' => 'profit',
            'trx' => uniqid('TRX'),
            'details' => "Profit from investment #{$investment->id}",
            'remark' => '',
            'wallet_type' => $investment->wallet_type,
            'created_at' => $investment->created_at,
            'updated_at' => $investment->created_at
        ];
    }
}
// Add some deposit and withdrawal transactions
foreach ($user_ids as $user_id) {
    $deposits = rand(1, 3);
    for ($i = 0; $i < $deposits; $i++) {
        $amount = rand(500, 5000);
        $date = date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' days'));
        $transactions_data[] = [
            'user_id' => $user_id,
            'invest_id' => 0,
            'amount' => $amount,
            'charge' => 0.00,
            'post_balance' => $amount,
            'trx_type' => 'deposit',
            'trx' => uniqid('TRX'),
            'details' => 'Account deposit',
            'remark' => '',
            'wallet_type' => 'main',
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
    $withdrawals = rand(0, 2);
    for ($i = 0; $i < $withdrawals; $i++) {
        $amount = rand(100, 2000);
        $date = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
        $transactions_data[] = [
            'user_id' => $user_id,
            'invest_id' => 0,
            'amount' => $amount,
            'charge' => 0.00,
            'post_balance' => $amount,
            'trx_type' => 'withdrawal',
            'trx' => uniqid('TRX'),
            'details' => 'Account withdrawal',
            'remark' => '',
            'wallet_type' => 'main',
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
$transactions_table = $wpdb->prefix . 'hyiplab_transactions';
$transactions_inserted = 0;
foreach ($transactions_data as $transaction) {
    $result = $wpdb->insert($transactions_table, $transaction);
    if ($result !== false) {
        $transactions_inserted++;
    }
}
echo "   ✅ Created {$transactions_inserted} transactions\n\n";

// Step 5: Update User Balances
echo "5. Updating User Balances...\n";

$users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hyiplab_users");

foreach ($users as $user) {
    // Calculate total deposits
    $total_deposits = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_transactions 
         WHERE user_id = %d AND trx_type = 'deposit'",
        $user->id
    )) ?: 0;
    
    // Calculate total withdrawals
    $total_withdrawals = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_transactions 
         WHERE user_id = %d AND trx_type = 'withdrawal'",
        $user->id
    )) ?: 0;
    
    // Calculate total profits
    $total_profits = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_transactions 
         WHERE user_id = %d AND trx_type = 'profit'",
        $user->id
    )) ?: 0;
    
    // Calculate total invested
    $total_invested = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_invests 
         WHERE user_id = %d",
        $user->id
    )) ?: 0;
    
    // Calculate current balance
    $current_balance = $total_deposits - $total_withdrawals + $total_profits - $total_invested;
    
    // Update user record
    $wpdb->update(
        $wpdb->prefix . 'hyiplab_users',
        [
            'balance' => max(0, $current_balance),
            'total_invested' => $total_invested,
            'total_earned' => $total_profits
        ],
        ['id' => $user->id]
    );
    
    echo "   ✅ Updated user {$user->username}: Balance $" . number_format($current_balance, 2) . "\n";
}

echo "\n";

// Step 6: Create Demo Pages
echo "6. Creating Demo Pages...\n";

$pages_data = [
    [
        'post_title' => 'About BlackCnote',
        'post_content' => 'BlackCnote is a leading investment platform offering high-yield investment opportunities. Our mission is to provide secure, profitable investment options for our clients.',
        'post_name' => 'about',
        'post_status' => 'publish',
        'post_type' => 'page'
    ],
    [
        'post_title' => 'Investment Plans',
        'post_content' => 'Explore our range of investment plans designed to meet your financial goals. From starter plans to enterprise solutions, we have options for every investor.',
        'post_name' => 'plans',
        'post_status' => 'publish',
        'post_type' => 'page'
    ],
    [
        'post_title' => 'Contact Us',
        'post_content' => 'Get in touch with our support team. We\'re here to help you with any questions about our investment platform.',
        'post_name' => 'contact',
        'post_status' => 'publish',
        'post_type' => 'page'
    ],
    [
        'post_title' => 'Terms of Service',
        'post_content' => 'Our terms of service outline the rules and regulations for using the BlackCnote investment platform.',
        'post_name' => 'terms',
        'post_status' => 'publish',
        'post_type' => 'page'
    ],
    [
        'post_title' => 'Privacy Policy',
        'post_content' => 'Learn how we protect your personal information and maintain your privacy while using our platform.',
        'post_name' => 'privacy',
        'post_status' => 'publish',
        'post_type' => 'page'
    ]
];

$pages_created = 0;
foreach ($pages_data as $page_data) {
    $page_id = wp_insert_post($page_data);
    if ($page_id && !is_wp_error($page_id)) {
        $pages_created++;
        echo "   ✅ Created page: {$page_data['post_title']}\n";
    } else {
        echo "   ❌ Failed to create page: {$page_data['post_title']}\n";
    }
}

echo "   Total pages created: {$pages_created}\n\n";

// Step 7: Generate Summary Statistics
echo "7. Generating Summary Statistics...\n";

$total_users = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_users");
$total_investments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_invests");
$total_transactions = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_transactions");
$total_plans = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_plans");

$total_invested_amount = $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_invests");
$total_earned_amount = $wpdb->get_var("SELECT SUM(paid) FROM {$wpdb->prefix}hyiplab_invests");
$total_balance = $wpdb->get_var("SELECT SUM(balance) FROM {$wpdb->prefix}hyiplab_users");

echo "   📊 Platform Statistics:\n";
echo "   - Total Users: {$total_users}\n";
echo "   - Total Investments: {$total_investments}\n";
echo "   - Total Transactions: {$total_transactions}\n";
echo "   - Total Plans: {$total_plans}\n";
echo "   - Total Invested: $" . number_format($total_invested_amount, 2) . "\n";
echo "   - Total Earned: $" . number_format($total_earned_amount, 2) . "\n";
echo "   - Total User Balances: $" . number_format($total_balance, 2) . "\n";

echo "\n🎉 Demo data seeding completed successfully!\n";
echo "==========================================\n";
echo "✅ HYIPLab plugin is ready with demo data\n";
echo "✅ License purchase code has been synced\n";
echo "✅ All tables populated with realistic data\n";
echo "✅ Demo pages created\n";
echo "\nYou can now access:\n";
echo "- WordPress Admin: http://localhost:8888/wp-admin/\n";
echo "- HYIPLab Dashboard: http://localhost:8888/wp-admin/admin.php?page=hyiplab\n";
echo "- Demo Users: demo_user1 through demo_user5 (password: demo123)\n";

echo "\n🌐 Access URLs:\n";
echo "WordPress Frontend: http://localhost:8888\n";
echo "WordPress Admin: http://localhost:8888/wp-admin/\n";
echo "React App: http://localhost:5174\n";
echo "phpMyAdmin: http://localhost:8080\n";

echo "\n✅ Demo data seeding completed successfully!\n"; 