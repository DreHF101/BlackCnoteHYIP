<?php
/**
 * HYIPLab Demo Content Setup Script
 * 
 * This script creates demo content for the HYIPLab investment platform
 * to showcase its features within the BlackCnote theme.
 * 
 * Usage: php scripts/setup-hyiplab-demo.php
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // Try Docker path first
    $wp_load_path = '/var/www/html/wp-load.php';
    if (file_exists($wp_load_path)) {
        require_once $wp_load_path;
    } else {
        // Fallback to local relative path
        $wp_load_path = __DIR__ . '/../blackcnote/wp-load.php';
        if (file_exists($wp_load_path)) {
            require_once $wp_load_path;
        } else {
            die('WordPress not found. Please run this script from the project root.');
        }
    }
}

// Ensure WordPress is loaded
if (!function_exists('wp_insert_post')) {
    require_once ABSPATH . 'wp-load.php';
}

class HYIPLabDemoSetup {
    
    private $demo_plans = [];
    private $demo_users = [];
    private $demo_investments = [];
    
    public function __construct() {
        $this->init_demo_data();
    }
    
    /**
     * Initialize demo data arrays
     */
    private function init_demo_data() {
        // Demo investment plans
        $this->demo_plans = [
            [
                'name' => 'Starter Plan',
                'description' => 'Perfect for beginners. Start your investment journey with our most accessible plan.',
                'min_amount' => 100,
                'max_amount' => 1000,
                'profit_rate' => 2.5,
                'duration' => 30,
                'features' => ['Daily profit', '24/7 support', 'Instant activation'],
                'color' => '#4CAF50'
            ],
            [
                'name' => 'Premium Plan',
                'description' => 'For serious investors looking for higher returns with moderate risk.',
                'min_amount' => 1000,
                'max_amount' => 10000,
                'profit_rate' => 5.0,
                'duration' => 60,
                'features' => ['Higher returns', 'Priority support', 'Weekly bonuses'],
                'color' => '#2196F3'
            ],
            [
                'name' => 'VIP Plan',
                'description' => 'Exclusive VIP benefits with maximum returns for high-value investors.',
                'min_amount' => 10000,
                'max_amount' => 100000,
                'profit_rate' => 8.0,
                'duration' => 90,
                'features' => ['Maximum returns', 'VIP support', 'Monthly bonuses', 'Exclusive events'],
                'color' => '#9C27B0'
            ]
        ];
        
        // Demo users
        $this->demo_users = [
            [
                'username' => 'demo_investor',
                'email' => 'investor@demo.blackcnote.com',
                'password' => 'demo123456',
                'first_name' => 'John',
                'last_name' => 'Investor',
                'role' => 'subscriber'
            ],
            [
                'username' => 'demo_admin',
                'email' => 'admin@demo.blackcnote.com',
                'password' => 'admin123456',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => 'administrator'
            ],
            [
                'username' => 'demo_support',
                'email' => 'support@demo.blackcnote.com',
                'password' => 'support123456',
                'first_name' => 'Support',
                'last_name' => 'Team',
                'role' => 'editor'
            ]
        ];
    }
    
    /**
     * Run the complete demo setup
     */
    public function run() {
        echo "🚀 Starting HYIPLab Demo Setup...\n\n";
        
        try {
            // Check if HYIPLab is active
            if (!$this->check_hyiplab_active()) {
                echo "❌ HYIPLab plugin is not active. Please activate it first.\n";
                return false;
            }
            
            // Create demo users
            $this->create_demo_users();
            
            // Create demo investment plans
            $this->create_demo_plans();
            
            // Create demo investments
            $this->create_demo_investments();
            
            // Create demo transactions
            $this->create_demo_transactions();
            
            // Create demo pages
            $this->create_demo_pages();
            
            // Setup demo widgets
            $this->setup_demo_widgets();
            
            echo "\n✅ HYIPLab Demo Setup Completed Successfully!\n\n";
            echo "📋 Demo Content Summary:\n";
            echo "- Created " . count($this->demo_users) . " demo users\n";
            echo "- Created " . count($this->demo_plans) . " investment plans\n";
            echo "- Created sample investments and transactions\n";
            echo "- Setup demo pages and widgets\n\n";
            
            echo "🔗 Access Your Demo:\n";
            echo "- WordPress Admin: " . admin_url() . "\n";
            echo "- Demo Investor Login: demo_investor / demo123456\n";
            echo "- Demo Admin Login: demo_admin / admin123456\n";
            echo "- Investment Plans: " . home_url('/plans/') . "\n";
            echo "- User Dashboard: " . home_url('/dashboard/') . "\n\n";
            
            return true;
            
        } catch (Exception $e) {
            echo "❌ Error during demo setup: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Check if HYIPLab plugin is active
     */
    private function check_hyiplab_active() {
        if (!function_exists('hyiplab_system_instance')) {
            return false;
        }
        
        // Check if HYIPLab tables exist
        global $wpdb;
        $table_name = $wpdb->prefix . 'hyiplab_plans';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        
        return $table_exists;
    }
    
    /**
     * Create demo users
     */
    private function create_demo_users() {
        echo "👥 Creating demo users...\n";
        
        foreach ($this->demo_users as $user_data) {
            // Check if user already exists
            $existing_user = get_user_by('username', $user_data['username']);
            if ($existing_user) {
                echo "  - User '{$user_data['username']}' already exists, skipping...\n";
                continue;
            }
            
            // Create user
            $user_id = wp_create_user(
                $user_data['username'],
                $user_data['password'],
                $user_data['email']
            );
            
            if (is_wp_error($user_id)) {
                echo "  ❌ Failed to create user '{$user_data['username']}': " . $user_id->get_error_message() . "\n";
                continue;
            }
            
            // Update user meta
            wp_update_user([
                'ID' => $user_id,
                'first_name' => $user_data['first_name'],
                'last_name' => $user_data['last_name'],
                'role' => $user_data['role']
            ]);
            
            // Add demo user meta
            update_user_meta($user_id, 'demo_user', true);
            update_user_meta($user_id, 'demo_balance', 5000);
            
            echo "  ✅ Created user '{$user_data['username']}' (ID: $user_id)\n";
        }
    }
    
    /**
     * Create demo investment plans
     */
    private function create_demo_plans() {
        echo "\n📊 Creating demo investment plans...\n";
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'hyiplab_plans';
        
        foreach ($this->demo_plans as $plan_data) {
            // Check if plan already exists
            $existing_plan = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table_name WHERE name = %s", $plan_data['name'])
            );
            
            if ($existing_plan) {
                echo "  - Plan '{$plan_data['name']}' already exists, skipping...\n";
                continue;
            }
            
            // Insert plan
            $result = $wpdb->insert(
                $table_name,
                [
                    'name' => $plan_data['name'],
                    'description' => $plan_data['description'],
                    'min_amount' => $plan_data['min_amount'],
                    'max_amount' => $plan_data['max_amount'],
                    'profit_rate' => $plan_data['profit_rate'],
                    'duration' => $plan_data['duration'],
                    'features' => json_encode($plan_data['features']),
                    'color' => $plan_data['color'],
                    'status' => 'active',
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ],
                ['%s', '%s', '%d', '%d', '%f', '%d', '%s', '%s', '%s', '%s', '%s']
            );
            
            if ($result === false) {
                echo "  ❌ Failed to create plan '{$plan_data['name']}'\n";
                continue;
            }
            
            $plan_id = $wpdb->insert_id;
            echo "  ✅ Created plan '{$plan_data['name']}' (ID: $plan_id)\n";
        }
    }
    
    /**
     * Create demo investments
     */
    private function create_demo_investments() {
        echo "\n💰 Creating demo investments...\n";
        
        global $wpdb;
        $plans_table = $wpdb->prefix . 'hyiplab_plans';
        $investments_table = $wpdb->prefix . 'hyiplab_investments';
        
        // Get demo user
        $demo_user = get_user_by('username', 'demo_investor');
        if (!$demo_user) {
            echo "  ❌ Demo investor user not found\n";
            return;
        }
        
        // Get available plans
        $plans = $wpdb->get_results("SELECT * FROM $plans_table WHERE status = 'active'");
        
        foreach ($plans as $plan) {
            // Create investment for this plan
            $investment_amount = rand($plan->min_amount, min($plan->max_amount, 2000));
            $start_date = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
            $end_date = date('Y-m-d H:i:s', strtotime($start_date . ' +' . $plan->duration . ' days'));
            
            $result = $wpdb->insert(
                $investments_table,
                [
                    'user_id' => $demo_user->ID,
                    'plan_id' => $plan->id,
                    'amount' => $investment_amount,
                    'profit_rate' => $plan->profit_rate,
                    'total_profit' => ($investment_amount * $plan->profit_rate / 100),
                    'status' => 'active',
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ],
                ['%d', '%d', '%f', '%f', '%f', '%s', '%s', '%s', '%s', '%s']
            );
            
            if ($result === false) {
                echo "  ❌ Failed to create investment for plan '{$plan->name}'\n";
                continue;
            }
            
            $investment_id = $wpdb->insert_id;
            echo "  ✅ Created investment for '{$plan->name}' (Amount: $investment_amount, ID: $investment_id)\n";
        }
    }
    
    /**
     * Create demo transactions
     */
    private function create_demo_transactions() {
        echo "\n💳 Creating demo transactions...\n";
        
        global $wpdb;
        $transactions_table = $wpdb->prefix . 'hyiplab_transactions';
        
        // Get demo user
        $demo_user = get_user_by('username', 'demo_investor');
        if (!$demo_user) {
            echo "  ❌ Demo investor user not found\n";
            return;
        }
        
        // Create sample transactions
        $transaction_types = ['deposit', 'withdrawal', 'profit', 'investment'];
        $payment_methods = ['bank_transfer', 'credit_card', 'paypal', 'crypto'];
        
        for ($i = 0; $i < 10; $i++) {
            $type = $transaction_types[array_rand($transaction_types)];
            $amount = rand(100, 5000);
            $status = rand(0, 1) ? 'completed' : 'pending';
            $date = date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' days'));
            
            $result = $wpdb->insert(
                $transactions_table,
                [
                    'user_id' => $demo_user->ID,
                    'type' => $type,
                    'amount' => $amount,
                    'payment_method' => $payment_methods[array_rand($payment_methods)],
                    'status' => $status,
                    'description' => "Demo $type transaction",
                    'created_at' => $date,
                    'updated_at' => current_time('mysql')
                ],
                ['%d', '%s', '%f', '%s', '%s', '%s', '%s', '%s']
            );
            
            if ($result === false) {
                echo "  ❌ Failed to create transaction\n";
                continue;
            }
            
            echo "  ✅ Created $type transaction (Amount: $amount, Status: $status)\n";
        }
    }
    
    /**
     * Create demo pages
     */
    private function create_demo_pages() {
        echo "\n📄 Creating demo pages...\n";
        
        $pages = [
            [
                'title' => 'Investment Plans',
                'slug' => 'plans',
                'content' => $this->get_plans_page_content(),
                'template' => 'page-plans.php'
            ],
            [
                'title' => 'User Dashboard',
                'slug' => 'dashboard',
                'content' => $this->get_dashboard_page_content(),
                'template' => 'page-dashboard.php'
            ],
            [
                'title' => 'Transaction History',
                'slug' => 'transactions',
                'content' => $this->get_transactions_page_content(),
                'template' => 'template-blackcnote-transactions.php'
            ]
        ];
        
        foreach ($pages as $page_data) {
            // Check if page already exists
            $existing_page = get_page_by_path($page_data['slug']);
            if ($existing_page) {
                echo "  - Page '{$page_data['title']}' already exists, skipping...\n";
                continue;
            }
            
            // Create page
            $page_id = wp_insert_post([
                'post_title' => $page_data['title'],
                'post_name' => $page_data['slug'],
                'post_content' => $page_data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => $page_data['template']
            ]);
            
            if (is_wp_error($page_id)) {
                echo "  ❌ Failed to create page '{$page_data['title']}': " . $page_id->get_error_message() . "\n";
                continue;
            }
            
            echo "  ✅ Created page '{$page_data['title']}' (ID: $page_id)\n";
        }
    }
    
    /**
     * Setup demo widgets
     */
    private function setup_demo_widgets() {
        echo "\n🎛️ Setting up demo widgets...\n";
        
        // Add widgets to sidebar
        $widget_areas = get_option('sidebars_widgets', []);
        
        // Investment Plans Widget
        $widget_areas['sidebar-1'][] = 'hyiplab_plans_widget-1';
        update_option('widget_hyiplab_plans_widget', [
            1 => [
                'title' => 'Investment Plans',
                'show_count' => 3
            ]
        ]);
        
        // User Stats Widget
        $widget_areas['sidebar-1'][] = 'hyiplab_stats_widget-1';
        update_option('widget_hyiplab_stats_widget', [
            1 => [
                'title' => 'Your Stats',
                'show_balance' => true,
                'show_investments' => true
            ]
        ]);
        
        update_option('sidebars_widgets', $widget_areas);
        echo "  ✅ Demo widgets configured\n";
    }
    
    /**
     * Get plans page content
     */
    private function get_plans_page_content() {
        return '
        <div class="hyiplab-demo-content">
            <h2>Investment Plans</h2>
            <p>Choose from our carefully crafted investment plans designed to meet your financial goals.</p>
            
            [hyiplab_plans]
            
            <div class="demo-notice">
                <h3>Demo Notice</h3>
                <p>This is demo content showcasing the HYIPLab investment platform features. In a real environment, you would see actual investment plans with real profit rates and terms.</p>
            </div>
        </div>';
    }
    
    /**
     * Get dashboard page content
     */
    private function get_dashboard_page_content() {
        return '
        <div class="hyiplab-demo-content">
            <h2>Your Investment Dashboard</h2>
            <p>Track your investments, monitor profits, and manage your portfolio.</p>
            
            [hyiplab_dashboard]
            
            <div class="demo-notice">
                <h3>Demo Notice</h3>
                <p>This dashboard shows sample investment data. In a real environment, you would see your actual investments, profits, and transaction history.</p>
            </div>
        </div>';
    }
    
    /**
     * Get transactions page content
     */
    private function get_transactions_page_content() {
        return '
        <div class="hyiplab-demo-content">
            <h2>Transaction History</h2>
            <p>View all your deposits, withdrawals, and profit distributions.</p>
            
            [hyiplab_transactions]
            
            <div class="demo-notice">
                <h3>Demo Notice</h3>
                <p>This page displays sample transaction data. In a real environment, you would see your actual transaction history with real amounts and dates.</p>
            </div>
        </div>';
    }
}

// Run the demo setup
if (php_sapi_name() === 'cli') {
    $demo_setup = new HYIPLabDemoSetup();
    $demo_setup->run();
} else {
    // If accessed via web, check permissions
    if (current_user_can('manage_options')) {
        $demo_setup = new HYIPLabDemoSetup();
        $demo_setup->run();
    } else {
        echo "Access denied. You need administrator privileges to run this script.";
    }
} 