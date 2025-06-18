<?php
/**
 * HYIPLab Dashboard Integration
 *
 * @package HYIP_Theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Check if HYIPLab plugin is active
 */
function hyip_is_hyiplab_active() {
    return function_exists('hyiplab_init');
}

/**
 * HYIPLab Dashboard Integration
 */
if (hyip_is_hyiplab_active()) {
    /**
     * Add HYIPLab dashboard menu items
     */
    function hyip_theme_hyiplab_menu() {
        add_menu_page(
            __('HYIP Dashboard', 'hyip-theme'),
            __('HYIP Dashboard', 'hyip-theme'),
            'manage_options',
            'hyip-dashboard',
            'hyip_theme_dashboard_page',
            'dashicons-chart-area',
            30
        );

        add_submenu_page(
            'hyip-dashboard',
            __('Investment Plans', 'hyip-theme'),
            __('Investment Plans', 'hyip-theme'),
            'manage_options',
            'hyip-plans',
            'hyip_theme_plans_page'
        );

        add_submenu_page(
            'hyip-dashboard',
            __('Transactions', 'hyip-theme'),
            __('Transactions', 'hyip-theme'),
            'manage_options',
            'hyip-transactions',
            'hyip_theme_transactions_page'
        );
    }
    add_action('admin_menu', 'hyip_theme_hyiplab_menu');

    /**
     * Dashboard page callback
     */
    function hyip_theme_dashboard_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('HYIP Dashboard', 'hyip-theme'); ?></h1>
            <div class="dashboard-stats">
                <div class="stat-box">
                    <h3><?php esc_html_e('Total Balance', 'hyip-theme'); ?></h3>
                    <p class="amount">$0.00</p>
                </div>
                <div class="stat-box">
                    <h3><?php esc_html_e('Active Investments', 'hyip-theme'); ?></h3>
                    <p class="amount">0</p>
                </div>
                <div class="stat-box">
                    <h3><?php esc_html_e('Total Earnings', 'hyip-theme'); ?></h3>
                    <p class="amount">$0.00</p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Plans page callback
     */
    function hyip_theme_plans_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Investment Plans', 'hyip-theme'); ?></h1>
            <div class="plans-list">
                <p><?php esc_html_e('No investment plans available.', 'hyip-theme'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Transactions page callback
     */
    function hyip_theme_transactions_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Transactions', 'hyip-theme'); ?></h1>
            <div class="transactions-list">
                <p><?php esc_html_e('No transactions found.', 'hyip-theme'); ?></p>
            </div>
        </div>
        <?php
    }
}

/**
 * Get user investment plans
 *
 * @return array|false Array of investment plans or false if none found
 */
function hyiplab_get_investment_plans() {
    if (!function_exists('hyiplab_get_plans')) {
        return false;
    }
    return hyiplab_get_plans();
}

/**
 * Get user transactions
 *
 * @return array|false Array of transactions or false if none found
 */
function hyiplab_get_user_transactions() {
    if (!function_exists('hyiplab_get_user_transactions')) {
        return false;
    }
    return hyiplab_get_user_transactions();
}

/**
 * Get user dashboard stats
 *
 * @return array|false Array of dashboard stats or false if none found
 */
function hyiplab_get_dashboard_stats() {
    if (!function_exists('hyiplab_get_user_stats')) {
        return false;
    }
    return hyiplab_get_user_stats();
}

/**
 * Add dashboard menu items
 */
function hyip_theme_add_dashboard_menu() {
    if (!hyip_is_hyiplab_active()) {
        return;
    }

    add_menu_page(
        __('Dashboard', 'hyip-theme'),
        __('Dashboard', 'hyip-theme'),
        'read',
        'hyip-dashboard',
        'hyip_theme_dashboard_page',
        'dashicons-chart-area',
        2
    );

    add_submenu_page(
        'hyip-dashboard',
        __('Investment Plans', 'hyip-theme'),
        __('Plans', 'hyip-theme'),
        'read',
        'hyip-plans',
        'hyip_theme_plans_page'
    );

    add_submenu_page(
        'hyip-dashboard',
        __('Transactions', 'hyip-theme'),
        __('Transactions', 'hyip-theme'),
        'read',
        'hyip-transactions',
        'hyip_theme_transactions_page'
    );
}
add_action('admin_menu', 'hyip_theme_add_dashboard_menu');

/**
 * Dashboard page callback
 */
function hyip_theme_dashboard_page() {
    if (!hyip_is_hyiplab_active()) {
        wp_die(esc_html__('HYIPLab plugin is required for this page.', 'hyip-theme'));
    }

    $stats = hyiplab_get_dashboard_stats();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Dashboard', 'hyip-theme'); ?></h1>
        
        <?php if ($stats) : ?>
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3><?php esc_html_e('Total Balance', 'hyip-theme'); ?></h3>
                    <p class="stat-value"><?php echo esc_html($stats['balance']); ?></p>
                </div>
                <div class="stat-card">
                    <h3><?php esc_html_e('Active Investments', 'hyip-theme'); ?></h3>
                    <p class="stat-value"><?php echo esc_html($stats['active_investments']); ?></p>
                </div>
                <div class="stat-card">
                    <h3><?php esc_html_e('Total Earnings', 'hyip-theme'); ?></h3>
                    <p class="stat-value"><?php echo esc_html($stats['total_earnings']); ?></p>
                </div>
            </div>
        <?php else : ?>
            <p><?php esc_html_e('No statistics available.', 'hyip-theme'); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Plans page callback
 */
function hyip_theme_plans_page() {
    if (!hyip_is_hyiplab_active()) {
        wp_die(esc_html__('HYIPLab plugin is required for this page.', 'hyip-theme'));
    }

    $plans = hyiplab_get_investment_plans();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Investment Plans', 'hyip-theme'); ?></h1>
        
        <?php if ($plans) : ?>
            <div class="investment-plans">
                <?php foreach ($plans as $plan) : ?>
                    <div class="plan-card">
                        <h3><?php echo esc_html($plan['name']); ?></h3>
                        <div class="plan-details">
                            <p class="plan-return">
                                <?php printf(
                                    esc_html__('Return: %s%%', 'hyip-theme'),
                                    esc_html($plan['return_rate'])
                                ); ?>
                            </p>
                            <p class="plan-term">
                                <?php printf(
                                    esc_html__('Term: %s days', 'hyip-theme'),
                                    esc_html($plan['term'])
                                ); ?>
                            </p>
                            <p class="plan-min">
                                <?php printf(
                                    esc_html__('Minimum: %s', 'hyip-theme'),
                                    esc_html($plan['min_investment'])
                                ); ?>
                            </p>
                        </div>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=hyip-invest&plan=' . $plan['id'])); ?>" class="button button-primary">
                            <?php esc_html_e('Invest Now', 'hyip-theme'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p><?php esc_html_e('No investment plans available.', 'hyip-theme'); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Transactions page callback
 */
function hyip_theme_transactions_page() {
    if (!hyip_is_hyiplab_active()) {
        wp_die(esc_html__('HYIPLab plugin is required for this page.', 'hyip-theme'));
    }

    $transactions = hyiplab_get_user_transactions();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Transactions', 'hyip-theme'); ?></h1>
        
        <?php if ($transactions) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Date', 'hyip-theme'); ?></th>
                        <th><?php esc_html_e('Type', 'hyip-theme'); ?></th>
                        <th><?php esc_html_e('Amount', 'hyip-theme'); ?></th>
                        <th><?php esc_html_e('Status', 'hyip-theme'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction) : ?>
                        <tr>
                            <td><?php echo esc_html($transaction['date']); ?></td>
                            <td><?php echo esc_html($transaction['type']); ?></td>
                            <td><?php echo esc_html($transaction['amount']); ?></td>
                            <td>
                                <span class="status-<?php echo esc_attr($transaction['status']); ?>">
                                    <?php echo esc_html($transaction['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php esc_html_e('No transactions found.', 'hyip-theme'); ?></p>
        <?php endif; ?>
    </div>
    <?php
} 