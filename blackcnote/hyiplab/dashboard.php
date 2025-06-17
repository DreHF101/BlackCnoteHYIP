<?php
/**
 * Override template for HYIPLab dashboard
 *
 * This template overrides the default HYIPLab dashboard view
 * with custom styling and additional features.
 *
 * @package HYIP_Theme
 * @subpackage HYIPLab
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get user data
$user = wp_get_current_user();
if (!$user->exists()) {
    wp_die(__('You must be logged in to view this page.', 'hyip-theme'));
}

// Get user investments
global $wpdb;
$investments = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}hyiplab_investments WHERE user_id = %d ORDER BY created_at DESC",
        $user->ID
    )
);

// Get total balance
$balance = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_transactions WHERE user_id = %d AND status = %s",
        $user->ID,
        'completed'
    )
) ?: 0;

// Get recent transactions
$transactions = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}hyiplab_transactions WHERE user_id = %d ORDER BY created_at DESC LIMIT 5",
        $user->ID
    )
);
?>

<div class="hyip-dashboard">
    <!-- User Overview -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Account Balance', 'hyip-theme'); ?></h5>
                    <h2 class="card-text"><?php echo esc_html(number_format($balance, 2)); ?></h2>
                    <p class="card-text text-muted"><?php esc_html_e('Available for withdrawal', 'hyip-theme'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Active Investments', 'hyip-theme'); ?></h5>
                    <h2 class="card-text"><?php echo esc_html(count($investments)); ?></h2>
                    <p class="card-text text-muted"><?php esc_html_e('Total active plans', 'hyip-theme'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Member Since', 'hyip-theme'); ?></h5>
                    <h2 class="card-text"><?php echo esc_html(date_i18n('M Y', strtotime($user->user_registered))); ?></h2>
                    <p class="card-text text-muted"><?php esc_html_e('Account creation date', 'hyip-theme'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Quick Actions', 'hyip-theme'); ?></h5>
                    <div class="d-flex gap-2">
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('investment-plans'))); ?>" class="btn btn-primary">
                            <?php esc_html_e('New Investment', 'hyip-theme'); ?>
                        </a>
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('withdraw'))); ?>" class="btn btn-outline-primary">
                            <?php esc_html_e('Withdraw Funds', 'hyip-theme'); ?>
                        </a>
                        <a href="<?php echo esc_url(get_edit_profile_url()); ?>" class="btn btn-outline-secondary">
                            <?php esc_html_e('Update Profile', 'hyip-theme'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Investments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Active Investments', 'hyip-theme'); ?></h5>
                    <?php if (!empty($investments)) : ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Plan', 'hyip-theme'); ?></th>
                                        <th><?php esc_html_e('Amount', 'hyip-theme'); ?></th>
                                        <th><?php esc_html_e('Return', 'hyip-theme'); ?></th>
                                        <th><?php esc_html_e('Status', 'hyip-theme'); ?></th>
                                        <th><?php esc_html_e('Date', 'hyip-theme'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($investments as $investment) : ?>
                                        <tr>
                                            <td><?php echo esc_html($investment->plan_name); ?></td>
                                            <td><?php echo esc_html(number_format($investment->amount, 2)); ?></td>
                                            <td><?php echo esc_html(number_format($investment->expected_return, 2)); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo esc_attr($investment->status === 'active' ? 'success' : 'warning'); ?>">
                                                    <?php echo esc_html(ucfirst($investment->status)); ?>
                                                </span>
                                            </td>
                                            <td><?php echo esc_html(date_i18n('M d, Y', strtotime($investment->created_at))); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-muted"><?php esc_html_e('No active investments found.', 'hyip-theme'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Recent Transactions', 'hyip-theme'); ?></h5>
                    <?php if (!empty($transactions)) : ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Type', 'hyip-theme'); ?></th>
                                        <th><?php esc_html_e('Amount', 'hyip-theme'); ?></th>
                                        <th><?php esc_html_e('Status', 'hyip-theme'); ?></th>
                                        <th><?php esc_html_e('Date', 'hyip-theme'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction) : ?>
                                        <tr>
                                            <td><?php echo esc_html(ucfirst($transaction->type)); ?></td>
                                            <td><?php echo esc_html(number_format($transaction->amount, 2)); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo esc_attr($transaction->status === 'completed' ? 'success' : 'warning'); ?>">
                                                    <?php echo esc_html(ucfirst($transaction->status)); ?>
                                                </span>
                                            </td>
                                            <td><?php echo esc_html(date_i18n('M d, Y', strtotime($transaction->created_at))); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('transactions'))); ?>" class="btn btn-link">
                                <?php esc_html_e('View All Transactions', 'hyip-theme'); ?>
                            </a>
                        </div>
                    <?php else : ?>
                        <p class="text-muted"><?php esc_html_e('No recent transactions found.', 'hyip-theme'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Add custom JavaScript for dashboard
wp_enqueue_script(
    'hyip-dashboard',
    get_template_directory_uri() . '/assets/js/hyip-theme.js',
    ['jquery'],
    HYIP_THEME_VERSION,
    true
);

// Localize script
wp_localize_script(
    'hyip-dashboard',
    'hyipDashboard',
    [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('hyip_dashboard_nonce'),
    ]
); 