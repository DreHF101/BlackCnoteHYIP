<?php
/**
 * HYIPLab Theme Integration
 * 
 * This file handles the integration between HYIPLab investment platform
 * and the BlackCnote WordPress theme.
 */

if (!defined('ABSPATH')) {
    exit;
}

class BlackCnoteHYIPLabIntegration {
    
    public function __construct() {
        $this->init();
    }
    
    /**
     * Initialize the integration
     */
    public function init() {
        // Only run if HYIPLab is active
        if (!function_exists('hyiplab_system_instance')) {
            return;
        }
        
        // Add hooks
        add_action('init', [$this, 'register_shortcodes']);
        add_action('widgets_init', [$this, 'register_widgets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_hyiplab_assets']);
        add_action('wp_head', [$this, 'add_hyiplab_meta']);
        add_filter('body_class', [$this, 'add_hyiplab_body_classes']);
        
        // Add navigation integration
        add_filter('wp_nav_menu_items', [$this, 'add_hyiplab_menu_items'], 10, 2);
        
        // Add theme customizer options
        add_action('customize_register', [$this, 'add_hyiplab_customizer_options']);
        
        // Register AJAX handlers
        add_action('wp_ajax_hyiplab_create_investment', [$this, 'handle_create_investment']);
        add_action('wp_ajax_nopriv_hyiplab_create_investment', [$this, 'handle_create_investment']);
        add_action('wp_ajax_hyiplab_get_plan_details', [$this, 'handle_get_plan_details']);
        add_action('wp_ajax_nopriv_hyiplab_get_plan_details', [$this, 'handle_get_plan_details']);
        add_action('wp_ajax_hyiplab_calculate_returns', [$this, 'handle_calculate_returns']);
        add_action('wp_ajax_nopriv_hyiplab_calculate_returns', [$this, 'handle_calculate_returns']);
        add_action('wp_ajax_hyiplab_get_user_stats', [$this, 'handle_get_user_stats']);
        add_action('wp_ajax_hyiplab_filter_transactions', [$this, 'handle_filter_transactions']);
        add_action('wp_ajax_nopriv_hyiplab_filter_transactions', [$this, 'handle_filter_transactions']);
        add_action('wp_ajax_hyiplab_get_recent_transactions', [$this, 'handle_get_recent_transactions']);
        add_action('wp_ajax_nopriv_hyiplab_get_recent_transactions', [$this, 'handle_get_recent_transactions']);
        add_action('wp_ajax_hyiplab_refresh_widget', [$this, 'handle_refresh_widget']);
        add_action('wp_ajax_nopriv_hyiplab_refresh_widget', [$this, 'handle_refresh_widget']);
    }
    
    /**
     * Register HYIPLab shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('hyiplab_plans', [$this, 'plans_shortcode']);
        add_shortcode('hyiplab_dashboard', [$this, 'dashboard_shortcode']);
        add_shortcode('hyiplab_transactions', [$this, 'transactions_shortcode']);
        add_shortcode('hyiplab_invest_form', [$this, 'invest_form_shortcode']);
        add_shortcode('hyiplab_stats', [$this, 'stats_shortcode']);
    }
    
    /**
     * Register HYIPLab widgets
     */
    public function register_widgets() {
        register_widget('BlackCnote_HYIPLab_Plans_Widget');
        register_widget('BlackCnote_HYIPLab_Stats_Widget');
        register_widget('BlackCnote_HYIPLab_Transactions_Widget');
    }
    
    /**
     * Enqueue HYIPLab assets
     */
    public function enqueue_hyiplab_assets() {
        // HYIPLab specific styles
        wp_enqueue_style(
            'blackcnote-hyiplab',
            get_template_directory_uri() . '/assets/css/hyiplab.css',
            [],
            BLACKCNOTE_THEME_VERSION
        );
        
        // HYIPLab specific scripts
        wp_enqueue_script(
            'blackcnote-hyiplab',
            get_template_directory_uri() . '/assets/js/hyiplab.js',
            ['jquery'],
            BLACKCNOTE_THEME_VERSION,
            true
        );
        
        // Localize script with HYIPLab data
        wp_localize_script('blackcnote-hyiplab', 'blackCnoteHYIPLab', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('blackcnote_hyiplab_nonce'),
            'currency' => get_option('hyiplab_currency', 'USD'),
            'isLoggedIn' => is_user_logged_in(),
            'userId' => get_current_user_id()
        ]);
    }
    
    /**
     * Add HYIPLab meta tags
     */
    public function add_hyiplab_meta() {
        if (is_page_template('page-plans.php') || is_page_template('page-dashboard.php')) {
            echo '<meta name="hyiplab-page" content="true">';
        }
    }
    
    /**
     * Add HYIPLab body classes
     */
    public function add_hyiplab_body_classes($classes) {
        if (is_page_template('page-plans.php')) {
            $classes[] = 'hyiplab-plans-page';
        }
        
        if (is_page_template('page-dashboard.php')) {
            $classes[] = 'hyiplab-dashboard-page';
        }
        
        if (function_exists('hyiplab_system_instance')) {
            $classes[] = 'hyiplab-active';
        }
        
        return $classes;
    }
    
    /**
     * Add HYIPLab menu items
     */
    public function add_hyiplab_menu_items($items, $args) {
        if ($args->theme_location !== 'primary') {
            return $items;
        }
        
        $hyiplab_items = '';
        
        // Add Investment Plans link
        $plans_page = get_page_by_path('plans');
        if ($plans_page) {
            $hyiplab_items .= '<li class="menu-item menu-item-hyiplab-plans"><a href="' . get_permalink($plans_page->ID) . '">Investment Plans</a></li>';
        }
        
        // Add Dashboard link for logged-in users
        if (is_user_logged_in()) {
            $dashboard_page = get_page_by_path('dashboard');
            if ($dashboard_page) {
                $hyiplab_items .= '<li class="menu-item menu-item-hyiplab-dashboard"><a href="' . get_permalink($dashboard_page->ID) . '">Dashboard</a></li>';
            }
        }
        
        return $items . $hyiplab_items;
    }
    
    /**
     * Add HYIPLab customizer options
     */
    public function add_hyiplab_customizer_options($wp_customize) {
        // HYIPLab Section
        $wp_customize->add_section('hyiplab_options', [
            'title' => __('HYIPLab Options', 'blackcnote'),
            'priority' => 30,
        ]);
        
        // Show HYIPLab features
        $wp_customize->add_setting('hyiplab_show_features', [
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ]);
        
        $wp_customize->add_control('hyiplab_show_features', [
            'label' => __('Show HYIPLab Features', 'blackcnote'),
            'section' => 'hyiplab_options',
            'type' => 'checkbox',
        ]);
        
        // HYIPLab accent color
        $wp_customize->add_setting('hyiplab_accent_color', [
            'default' => '#2196F3',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hyiplab_accent_color', [
            'label' => __('HYIPLab Accent Color', 'blackcnote'),
            'section' => 'hyiplab_options',
        ]));
    }
    
    /**
     * Plans shortcode
     */
    public function plans_shortcode($atts) {
        $atts = shortcode_atts([
            'limit' => 10,
            'show_featured' => true,
            'layout' => 'grid'
        ], $atts);
        
        if (!function_exists('hyiplab_system_instance')) {
            return '<p>HYIPLab plugin is not active.</p>';
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'hyiplab_plans';
        
        $plans = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = 'active' ORDER BY min_amount ASC LIMIT %d",
                $atts['limit']
            )
        );
        
        if (empty($plans)) {
            return '<p>No investment plans available.</p>';
        }
        
        ob_start();
        ?>
        <div class="hyiplab-plans-container layout-<?php echo esc_attr($atts['layout']); ?>">
            <?php foreach ($plans as $plan): ?>
                <div class="hyiplab-plan-card" style="border-left-color: <?php echo esc_attr($plan->color); ?>">
                    <div class="plan-header">
                        <h3 class="plan-name"><?php echo esc_html($plan->name); ?></h3>
                        <div class="plan-profit-rate">
                            <?php echo esc_html($plan->profit_rate); ?>% Profit
                        </div>
                    </div>
                    
                    <div class="plan-description">
                        <?php echo esc_html($plan->description); ?>
                    </div>
                    
                    <div class="plan-details">
                        <div class="plan-amount">
                            <span class="label">Investment Range:</span>
                            <span class="value">$<?php echo number_format($plan->min_amount); ?> - $<?php echo number_format($plan->max_amount); ?></span>
                        </div>
                        <div class="plan-duration">
                            <span class="label">Duration:</span>
                            <span class="value"><?php echo esc_html($plan->duration); ?> days</span>
                        </div>
                    </div>
                    
                    <?php if (is_user_logged_in()): ?>
                        <div class="plan-actions">
                            <button class="btn btn-primary invest-btn" data-plan-id="<?php echo esc_attr($plan->id); ?>">
                                Invest Now
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="plan-actions">
                            <a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn btn-secondary">
                                Login to Invest
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Dashboard shortcode
     */
    public function dashboard_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to view your dashboard.</p>';
        }
        
        if (!function_exists('hyiplab_system_instance')) {
            return '<p>HYIPLab plugin is not active.</p>';
        }
        
        $user_id = get_current_user_id();
        
        global $wpdb;
        $investments_table = $wpdb->prefix . 'hyiplab_investments';
        $transactions_table = $wpdb->prefix . 'hyiplab_transactions';
        
        // Get user investments
        $investments = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT i.*, p.name as plan_name FROM $investments_table i 
                LEFT JOIN {$wpdb->prefix}hyiplab_plans p ON i.plan_id = p.id 
                WHERE i.user_id = %d ORDER BY i.created_at DESC LIMIT 5",
                $user_id
            )
        );
        
        // Get recent transactions
        $transactions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $transactions_table WHERE user_id = %d ORDER BY created_at DESC LIMIT 10",
                $user_id
            )
        );
        
        // Calculate totals
        $total_invested = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(amount) FROM $investments_table WHERE user_id = %d AND status = 'active'",
                $user_id
            )
        );
        
        $total_profit = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(total_profit) FROM $investments_table WHERE user_id = %d AND status = 'active'",
                $user_id
            )
        );
        
        ob_start();
        ?>
        <div class="hyiplab-dashboard">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-value">$<?php echo number_format($total_invested ?: 0, 2); ?></div>
                    <div class="stat-label">Total Invested</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">$<?php echo number_format($total_profit ?: 0, 2); ?></div>
                    <div class="stat-label">Total Profit</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($investments); ?></div>
                    <div class="stat-label">Active Investments</div>
                </div>
            </div>
            
            <div class="dashboard-content">
                <div class="investments-section">
                    <h3>Your Investments</h3>
                    <?php if (!empty($investments)): ?>
                        <div class="investments-list">
                            <?php foreach ($investments as $investment): ?>
                                <div class="investment-item">
                                    <div class="investment-plan"><?php echo esc_html($investment->plan_name); ?></div>
                                    <div class="investment-amount">$<?php echo number_format($investment->amount, 2); ?></div>
                                    <div class="investment-profit">$<?php echo number_format($investment->total_profit, 2); ?></div>
                                    <div class="investment-status <?php echo esc_attr($investment->status); ?>">
                                        <?php echo esc_html(ucfirst($investment->status)); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No investments found. <a href="<?php echo get_permalink(get_page_by_path('plans')->ID); ?>">Browse investment plans</a></p>
                    <?php endif; ?>
                </div>
                
                <div class="transactions-section">
                    <h3>Recent Transactions</h3>
                    <?php if (!empty($transactions)): ?>
                        <div class="transactions-list">
                            <?php foreach ($transactions as $transaction): ?>
                                <div class="transaction-item">
                                    <div class="transaction-type <?php echo esc_attr($transaction->type); ?>">
                                        <?php echo esc_html(ucfirst($transaction->type)); ?>
                                    </div>
                                    <div class="transaction-amount">$<?php echo number_format($transaction->amount, 2); ?></div>
                                    <div class="transaction-status <?php echo esc_attr($transaction->status); ?>">
                                        <?php echo esc_html(ucfirst($transaction->status)); ?>
                                    </div>
                                    <div class="transaction-date">
                                        <?php echo date('M j, Y', strtotime($transaction->created_at)); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No transactions found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Transactions shortcode
     */
    public function transactions_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to view your transactions.</p>';
        }
        
        if (!function_exists('hyiplab_system_instance')) {
            return '<p>HYIPLab plugin is not active.</p>';
        }
        
        $user_id = get_current_user_id();
        
        global $wpdb;
        $transactions_table = $wpdb->prefix . 'hyiplab_transactions';
        
        $transactions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $transactions_table WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            )
        );
        
        ob_start();
        ?>
        <div class="hyiplab-transactions">
            <div class="transactions-header">
                <h2>Transaction History</h2>
                <div class="transactions-filters">
                    <select class="type-filter">
                        <option value="">All Types</option>
                        <option value="deposit">Deposits</option>
                        <option value="withdrawal">Withdrawals</option>
                        <option value="profit">Profits</option>
                        <option value="investment">Investments</option>
                    </select>
                    <select class="status-filter">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>
            
            <?php if (!empty($transactions)): ?>
                <div class="transactions-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr class="transaction-row" data-type="<?php echo esc_attr($transaction->type); ?>" data-status="<?php echo esc_attr($transaction->status); ?>">
                                    <td class="transaction-type <?php echo esc_attr($transaction->type); ?>">
                                        <?php echo esc_html(ucfirst($transaction->type)); ?>
                                    </td>
                                    <td class="transaction-amount">
                                        $<?php echo number_format($transaction->amount, 2); ?>
                                    </td>
                                    <td class="transaction-method">
                                        <?php echo esc_html(ucfirst(str_replace('_', ' ', $transaction->payment_method))); ?>
                                    </td>
                                    <td class="transaction-status <?php echo esc_attr($transaction->status); ?>">
                                        <?php echo esc_html(ucfirst($transaction->status)); ?>
                                    </td>
                                    <td class="transaction-date">
                                        <?php echo date('M j, Y H:i', strtotime($transaction->created_at)); ?>
                                    </td>
                                    <td class="transaction-description">
                                        <?php echo esc_html($transaction->description); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No transactions found.</p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Investment form shortcode
     */
    public function invest_form_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to make investments.</p>';
        }
        
        if (!function_exists('hyiplab_system_instance')) {
            return '<p>HYIPLab plugin is not active.</p>';
        }
        
        $atts = shortcode_atts([
            'plan_id' => 0
        ], $atts);
        
        global $wpdb;
        $plans_table = $wpdb->prefix . 'hyiplab_plans';
        
        if ($atts['plan_id']) {
            $plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM $plans_table WHERE id = %d", $atts['plan_id']));
        } else {
            $plans = $wpdb->get_results("SELECT * FROM $plans_table WHERE status = 'active' ORDER BY min_amount ASC");
        }
        
        ob_start();
        ?>
        <div class="hyiplab-invest-form">
            <h3>Make Investment</h3>
            
            <form id="hyiplab-investment-form" method="post">
                <?php wp_nonce_field('hyiplab_investment_nonce', 'hyiplab_nonce'); ?>
                
                <?php if ($atts['plan_id'] && $plan): ?>
                    <input type="hidden" name="plan_id" value="<?php echo esc_attr($plan->id); ?>">
                    <div class="selected-plan">
                        <h4><?php echo esc_html($plan->name); ?></h4>
                        <p><?php echo esc_html($plan->description); ?></p>
                        <div class="plan-details">
                            <span>Profit Rate: <?php echo esc_html($plan->profit_rate); ?>%</span>
                            <span>Duration: <?php echo esc_html($plan->duration); ?> days</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label for="plan_id">Select Investment Plan</label>
                        <select name="plan_id" id="plan_id" required>
                            <option value="">Choose a plan...</option>
                            <?php foreach ($plans as $plan): ?>
                                <option value="<?php echo esc_attr($plan->id); ?>" 
                                        data-min="<?php echo esc_attr($plan->min_amount); ?>"
                                        data-max="<?php echo esc_attr($plan->max_amount); ?>"
                                        data-rate="<?php echo esc_attr($plan->profit_rate); ?>">
                                    <?php echo esc_html($plan->name); ?> (<?php echo esc_html($plan->profit_rate); ?>% profit)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="investment_amount">Investment Amount</label>
                    <input type="number" name="investment_amount" id="investment_amount" 
                           min="<?php echo esc_attr($plan ? $plan->min_amount : 0); ?>" 
                           max="<?php echo esc_attr($plan ? $plan->max_amount : 999999); ?>" 
                           step="0.01" required>
                    <small class="amount-range">
                        Range: $<?php echo number_format($plan ? $plan->min_amount : 0); ?> - 
                        $<?php echo number_format($plan ? $plan->max_amount : 999999); ?>
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="">Select payment method...</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="crypto">Cryptocurrency</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Make Investment</button>
                </div>
            </form>
            
            <div id="investment-result" class="hidden"></div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Stats shortcode
     */
    public function stats_shortcode($atts) {
        if (!function_exists('hyiplab_system_instance')) {
            return '<p>HYIPLab plugin is not active.</p>';
        }
        
        global $wpdb;
        $plans_table = $wpdb->prefix . 'hyiplab_plans';
        $investments_table = $wpdb->prefix . 'hyiplab_investments';
        $transactions_table = $wpdb->prefix . 'hyiplab_transactions';
        
        // Get statistics
        $total_plans = $wpdb->get_var("SELECT COUNT(*) FROM $plans_table WHERE status = 'active'");
        $total_investments = $wpdb->get_var("SELECT COUNT(*) FROM $investments_table WHERE status = 'active'");
        $total_invested = $wpdb->get_var("SELECT SUM(amount) FROM $investments_table WHERE status = 'active'");
        $total_profit = $wpdb->get_var("SELECT SUM(total_profit) FROM $investments_table WHERE status = 'active'");
        
        ob_start();
        ?>
        <div class="hyiplab-stats">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($total_plans); ?></div>
                    <div class="stat-label">Active Plans</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($total_investments); ?></div>
                    <div class="stat-label">Active Investments</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">$<?php echo number_format($total_invested ?: 0, 0); ?></div>
                    <div class="stat-label">Total Invested</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">$<?php echo number_format($total_profit ?: 0, 0); ?></div>
                    <div class="stat-label">Total Profit</div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX Handlers
     */
    
    /**
     * Handle investment creation
     */
    public function handle_create_investment() {
        check_ajax_referer('blackcnote_hyiplab_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in to make investments.']);
        }
        
        $plan_id = intval($_POST['plan_id']);
        $amount = floatval($_POST['amount']);
        $payment_method = sanitize_text_field($_POST['payment_method']);
        
        if (!$plan_id || !$amount || !$payment_method) {
            wp_send_json_error(['message' => 'Please fill in all required fields.']);
        }
        
        global $wpdb;
        
        // Verify plan exists and is active
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE id = %d AND status = 'active'",
            $plan_id
        ));
        
        if (!$plan) {
            wp_send_json_error(['message' => 'Invalid investment plan.']);
        }
        
        // Validate amount
        if ($amount < $plan->min_investment || $amount > $plan->max_investment) {
            wp_send_json_error(['message' => 'Amount must be between $' . $plan->min_investment . ' and $' . $plan->max_investment . '.']);
        }
        
        // Create investment
        $user_id = get_current_user_id();
        $result = $wpdb->insert(
            $wpdb->prefix . 'hyiplab_investments',
            [
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'amount' => $amount,
                'payment_method' => $payment_method,
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ],
            ['%d', '%d', '%f', '%s', '%s', '%s']
        );
        
        if ($result) {
            $investment_id = $wpdb->insert_id;
            
            // Create transaction record
            $wpdb->insert(
                $wpdb->prefix . 'hyiplab_transactions',
                [
                    'user_id' => $user_id,
                    'investment_id' => $investment_id,
                    'type' => 'investment',
                    'amount' => $amount,
                    'payment_method' => $payment_method,
                    'status' => 'pending',
                    'description' => 'Investment in ' . $plan->name,
                    'created_at' => current_time('mysql')
                ],
                ['%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s']
            );
            
            wp_send_json_success(['message' => 'Investment created successfully!']);
        } else {
            wp_send_json_error(['message' => 'Failed to create investment. Please try again.']);
        }
    }
    
    /**
     * Get plan details
     */
    public function handle_get_plan_details() {
        check_ajax_referer('blackcnote_hyiplab_nonce', 'nonce');
        
        $plan_id = intval($_POST['plan_id']);
        
        if (!$plan_id) {
            wp_send_json_error(['message' => 'Invalid plan ID.']);
        }
        
        global $wpdb;
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE id = %d AND status = 'active'",
            $plan_id
        ));
        
        if ($plan) {
            wp_send_json_success($plan);
        } else {
            wp_send_json_error(['message' => 'Plan not found.']);
        }
    }
    
    /**
     * Calculate investment returns
     */
    public function handle_calculate_returns() {
        check_ajax_referer('blackcnote_hyiplab_nonce', 'nonce');
        
        $plan_id = intval($_POST['plan_id']);
        $amount = floatval($_POST['amount']);
        
        if (!$plan_id || !$amount) {
            wp_send_json_error(['message' => 'Invalid parameters.']);
        }
        
        global $wpdb;
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE id = %d AND status = 'active'",
            $plan_id
        ));
        
        if (!$plan) {
            wp_send_json_error(['message' => 'Plan not found.']);
        }
        
        // Calculate returns
        $return_rate = floatval($plan->return_rate) / 100;
        $duration_days = intval($plan->duration_days);
        $total_return = $amount * (1 + $return_rate);
        $profit = $total_return - $amount;
        
        $calculation = [
            'investment' => $amount,
            'return_rate' => $plan->return_rate,
            'duration_days' => $duration_days,
            'total_return' => $total_return,
            'profit' => $profit
        ];
        
        wp_send_json_success($calculation);
    }
    
    /**
     * Get user statistics
     */
    public function handle_get_user_stats() {
        check_ajax_referer('blackcnote_hyiplab_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'User not logged in.']);
        }
        
        $user_id = get_current_user_id();
        global $wpdb;
        
        $stats = [
            'total_invested' => $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_investments WHERE user_id = %d AND status = 'active'",
                $user_id
            )) ?: 0,
            'total_earned' => $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(total_profit) FROM {$wpdb->prefix}hyiplab_investments WHERE user_id = %d AND status = 'active'",
                $user_id
            )) ?: 0,
            'active_investments' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_investments WHERE user_id = %d AND status = 'active'",
                $user_id
            )) ?: 0,
            'total_transactions' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}hyiplab_transactions WHERE user_id = %d",
                $user_id
            )) ?: 0,
            'balance' => $wpdb->get_var($wpdb->prepare(
                "SELECT balance FROM {$wpdb->prefix}hyiplab_users WHERE wp_user_id = %d",
                $user_id
            )) ?: 0
        ];
        
        wp_send_json_success($stats);
    }
    
    /**
     * Filter transactions
     */
    public function handle_filter_transactions() {
        check_ajax_referer('blackcnote_hyiplab_nonce', 'nonce');
        
        $type = sanitize_text_field($_POST['type']);
        $status = sanitize_text_field($_POST['status']);
        $date_range = sanitize_text_field($_POST['date_range']);
        
        global $wpdb;
        
        $where_conditions = ['1=1'];
        $where_values = [];
        
        if ($type && $type !== 'all') {
            $where_conditions[] = 'type = %s';
            $where_values[] = $type;
        }
        
        if ($status && $status !== 'all') {
            $where_conditions[] = 'status = %s';
            $where_values[] = $status;
        }
        
        if ($date_range && $date_range !== 'all') {
            $date_condition = '';
            switch ($date_range) {
                case 'today':
                    $date_condition = 'DATE(created_at) = CURDATE()';
                    break;
                case 'week':
                    $date_condition = 'created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
                    break;
                case 'month':
                    $date_condition = 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
                    break;
            }
            if ($date_condition) {
                $where_conditions[] = $date_condition;
            }
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        $query = "SELECT * FROM {$wpdb->prefix}hyiplab_transactions WHERE $where_clause ORDER BY created_at DESC";
        
        if (!empty($where_values)) {
            $transactions = $wpdb->get_results($wpdb->prepare($query, $where_values));
        } else {
            $transactions = $wpdb->get_results($query);
        }
        
        ob_start();
        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                ?>
                <div class="transaction-item">
                    <div class="transaction-header">
                        <div class="transaction-type <?php echo esc_attr($transaction->type); ?>">
                            <?php echo esc_html(ucfirst($transaction->type)); ?>
                        </div>
                        <div class="transaction-status <?php echo esc_attr($transaction->status); ?>">
                            <?php echo esc_html(ucfirst($transaction->status)); ?>
                        </div>
                    </div>
                    <div class="transaction-amount">
                        $<?php echo number_format($transaction->amount, 2); ?>
                    </div>
                    <div class="transaction-date">
                        <?php echo date('M j, Y', strtotime($transaction->created_at)); ?>
                    </div>
                    <?php if (!empty($transaction->description)): ?>
                        <div class="transaction-description">
                            <?php echo esc_html($transaction->description); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
            }
        } else {
            echo '<p>No transactions found.</p>';
        }
        
        $html = ob_get_clean();
        wp_send_json_success(['html' => $html]);
    }
    
    /**
     * Get recent transactions
     */
    public function handle_get_recent_transactions() {
        check_ajax_referer('blackcnote_hyiplab_nonce', 'nonce');
        
        $limit = intval($_POST['limit']) ?: 10;
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        
        global $wpdb;
        
        if ($user_id) {
            $transactions = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}hyiplab_transactions WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
                $user_id,
                $limit
            ));
        } else {
            $transactions = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}hyiplab_transactions WHERE status = 'completed' ORDER BY created_at DESC LIMIT %d",
                $limit
            ));
        }
        
        ob_start();
        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                ?>
                <div class="transaction-item">
                    <div class="transaction-header">
                        <div class="transaction-type <?php echo esc_attr($transaction->type); ?>">
                            <?php echo esc_html(ucfirst($transaction->type)); ?>
                        </div>
                        <div class="transaction-status <?php echo esc_attr($transaction->status); ?>">
                            <?php echo esc_html(ucfirst($transaction->status)); ?>
                        </div>
                    </div>
                    <div class="transaction-amount">
                        $<?php echo number_format($transaction->amount, 2); ?>
                    </div>
                    <div class="transaction-date">
                        <?php echo date('M j, Y', strtotime($transaction->created_at)); ?>
                    </div>
                    <?php if (!empty($transaction->description)): ?>
                        <div class="transaction-description">
                            <?php echo esc_html($transaction->description); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
            }
        } else {
            echo '<p>No transactions found.</p>';
        }
        
        $html = ob_get_clean();
        wp_send_json_success(['html' => $html]);
    }
    
    /**
     * Refresh widget
     */
    public function handle_refresh_widget() {
        check_ajax_referer('blackcnote_hyiplab_nonce', 'nonce');
        
        $widget_type = sanitize_text_field($_POST['widget_type']);
        
        ob_start();
        
        switch ($widget_type) {
            case 'plans':
                echo do_shortcode('[hyiplab_plans limit="5"]');
                break;
            case 'stats':
                echo do_shortcode('[hyiplab_stats]');
                break;
            case 'transactions':
                echo do_shortcode('[hyiplab_transactions limit="5"]');
                break;
            default:
                echo '<p>Invalid widget type.</p>';
        }
        
        $html = ob_get_clean();
        wp_send_json_success(['html' => $html]);
    }
}

// Initialize the integration
new BlackCnoteHYIPLabIntegration();

// Include widget classes
require_once get_template_directory() . '/inc/widgets/hyiplab-plans-widget.php';
require_once get_template_directory() . '/inc/widgets/hyiplab-stats-widget.php';
require_once get_template_directory() . '/inc/widgets/hyiplab-transactions-widget.php'; 