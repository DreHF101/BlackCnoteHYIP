<?php
/**
 * Override template for BlackCnoteLab dashboard
 *
 * This template overrides the default BlackCnoteLab dashboard view
 * with custom styling and additional features.
 *
 * @package BlackCnote_Theme
 * @subpackage BlackCnoteLab
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get user data
$user = wp_get_current_user();
if (!$user->exists()) {
    wp_die(__('You must be logged in to view this page.', 'blackcnote-theme'));
}

// Get user investments
global $wpdb;
$investments = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}blackcnotelab_investments WHERE user_id = %d ORDER BY created_at DESC",
        $user->ID
    )
);

// Get total balance
$balance = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT SUM(amount) FROM {$wpdb->prefix}blackcnotelab_transactions WHERE user_id = %d AND status = %s",
        $user->ID,
        'completed'
    )
) ?: 0;

// Get recent transactions
$transactions = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}blackcnotelab_transactions WHERE user_id = %d ORDER BY created_at DESC LIMIT 5",
        $user->ID
    )
);
?>

<div class="blackcnote-dashboard">
    <!-- User Overview -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Account Balance', 'blackcnote-theme'); ?></h5>
                    <h2 class="card-text"><?php echo esc_html(number_format($balance, 2)); ?></h2>
                    <p class="card-text text-muted"><?php esc_html_e('Available for withdrawal', 'blackcnote-theme'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Active Investments', 'blackcnote-theme'); ?></h5>
                    <h2 class="card-text"><?php echo esc_html(count($investments)); ?></h2>
                    <p class="card-text text-muted"><?php esc_html_e('Total active plans', 'blackcnote-theme'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Member Since', 'blackcnote-theme'); ?></h5>
                    <h2 class="card-text"><?php echo esc_html(date_i18n('M Y', strtotime($user->user_registered))); ?></h2>
                    <p class="card-text text-muted"><?php esc_html_e('Account creation date', 'blackcnote-theme'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Quick Actions', 'blackcnote-theme'); ?></h5>
                    <div class="d-flex gap-2">
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('investment-plans'))); ?>" class="btn btn-primary">
                            <?php esc_html_e('New Investment', 'blackcnote-theme'); ?>
                        </a>
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('withdraw'))); ?>" class="btn btn-outline-primary">
                            <?php esc_html_e('Withdraw Funds', 'blackcnote-theme'); ?>
                        </a>
                        <a href="<?php echo esc_url(get_edit_profile_url()); ?>" class="btn btn-outline-secondary">
                            <?php esc_html_e('Update Profile', 'blackcnote-theme'); ?>
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
                    <h5 class="card-title"><?php esc_html_e('Active Investments', 'blackcnote-theme'); ?></h5>
                    <?php if (!empty($investments)) : ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Plan', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Amount', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Return', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Status', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Date', 'blackcnote-theme'); ?></th>
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
                        <p class="text-muted"><?php esc_html_e('No active investments found.', 'blackcnote-theme'); ?></p>
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
                    <h5 class="card-title"><?php esc_html_e('Recent Transactions', 'blackcnote-theme'); ?></h5>
                    <?php if (!empty($transactions)) : ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Type', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Amount', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Status', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Date', 'blackcnote-theme'); ?></th>
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
                                <?php esc_html_e('View All Transactions', 'blackcnote-theme'); ?>
                            </a>
                        </div>
                    <?php else : ?>
                        <p class="text-muted"><?php esc_html_e('No recent transactions found.', 'blackcnote-theme'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Add custom JavaScript for dashboard
wp_enqueue_script(
    'blackcnote-dashboard',
    get_template_directory_uri() . '/assets/js/blackcnote-theme.js',
    ['jquery'],
    BLACKCNOTE_THEME_VERSION,
    true
);

// Localize script
wp_localize_script(
    'blackcnote-dashboard',
    'blackcnoteDashboard',
    [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('blackcnote_dashboard_nonce'),
    ]
); 