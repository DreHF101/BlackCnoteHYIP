<?php
/**
 * Template part for displaying the user dashboard
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if HYIPLab plugin is active
$hyiplab_active = function_exists('hyiplab_system_instance');

// Get user data
$user = wp_get_current_user();
$user_id = $user->ID;

// Initialize variables
$investments = [];
$earnings = 0;
$active_investments = 0;

// Get user's investments if HYIPLab is active
if ($hyiplab_active) {
    global $wpdb;
    $investments = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}hyiplab_transactions 
         WHERE user_id = %d AND type = 'investment' 
         ORDER BY created_at DESC",
        $user_id
    ));

    // Get user's earnings
    $earnings = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_transactions 
         WHERE user_id = %d AND type = 'interest'",
        $user_id
    )) ?: 0;

    // Get active investments
    $active_investments = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(amount) FROM {$wpdb->prefix}hyiplab_transactions 
         WHERE user_id = %d AND type = 'investment' AND status = 'active'",
        $user_id
    )) ?: 0;
}
?>

<div class="dashboard">
    <!-- User Overview -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Total Balance', 'blackcnote'); ?></h5>
                    <p class="card-text h3">
                        <?php echo esc_html(number_format($active_investments + $earnings, 2)); ?>
                        <small class="text-muted">$</small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Active Investments', 'blackcnote'); ?></h5>
                    <p class="card-text h3">
                        <?php echo esc_html(number_format($active_investments, 2)); ?>
                        <small class="text-muted">$</small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Total Earnings', 'blackcnote'); ?></h5>
                    <p class="card-text h3">
                        <?php echo esc_html(number_format($earnings, 2)); ?>
                        <small class="text-muted">$</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Quick Actions', 'blackcnote'); ?></h5>
                    <div class="d-flex gap-2">
                        <a href="<?php echo esc_url(home_url('/plans')); ?>" class="btn btn-primary">
                            <?php esc_html_e('New Investment', 'blackcnote'); ?>
                        </a>
                        <a href="<?php echo esc_url(home_url('/withdraw')); ?>" class="btn btn-outline-primary">
                            <?php esc_html_e('Withdraw', 'blackcnote'); ?>
                        </a>
                        <a href="<?php echo esc_url(home_url('/profile')); ?>" class="btn btn-outline-secondary">
                            <?php esc_html_e('Edit Profile', 'blackcnote'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($hyiplab_active && $investments) : ?>
        <!-- Active Investments -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php esc_html_e('Active Investments', 'blackcnote'); ?></h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Plan', 'blackcnote'); ?></th>
                                        <th><?php esc_html_e('Amount', 'blackcnote'); ?></th>
                                        <th><?php esc_html_e('Return Rate', 'blackcnote'); ?></th>
                                        <th><?php esc_html_e('Start Date', 'blackcnote'); ?></th>
                                        <th><?php esc_html_e('End Date', 'blackcnote'); ?></th>
                                        <th><?php esc_html_e('Status', 'blackcnote'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($investments as $investment) : 
                                        $plan = $wpdb->get_row($wpdb->prepare(
                                            "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE id = %d",
                                            $investment->plan_id
                                        ));
                                    ?>
                                        <tr>
                                            <td><?php echo esc_html($plan->name); ?></td>
                                            <td>
                                                <?php 
                                                echo esc_html(number_format($investment->amount, 2));
                                                echo ' $';
                                                ?>
                                            </td>
                                            <td><?php echo esc_html($plan->return_rate); ?>%</td>
                                            <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($investment->created_at))); ?></td>
                                            <td>
                                                <?php 
                                                $end_date = strtotime($investment->created_at . ' + ' . $plan->duration . ' days');
                                                echo esc_html(date_i18n(get_option('date_format'), $end_date));
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $investment->status === 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo esc_html(ucfirst($investment->status)); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php esc_html_e('Recent Transactions', 'blackcnote'); ?></h5>
                        <?php
                        $transactions = $wpdb->get_results($wpdb->prepare(
                            "SELECT * FROM {$wpdb->prefix}hyiplab_transactions 
                             WHERE user_id = %d 
                             ORDER BY created_at DESC 
                             LIMIT 5",
                            $user_id
                        ));
                        ?>
                        <?php if ($transactions) : ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?php esc_html_e('Type', 'blackcnote'); ?></th>
                                            <th><?php esc_html_e('Amount', 'blackcnote'); ?></th>
                                            <th><?php esc_html_e('Date', 'blackcnote'); ?></th>
                                            <th><?php esc_html_e('Status', 'blackcnote'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $transaction) : ?>
                                            <tr>
                                                <td><?php echo esc_html(ucfirst($transaction->type)); ?></td>
                                                <td>
                                                    <?php 
                                                    echo esc_html(number_format($transaction->amount, 2));
                                                    echo ' $';
                                                    ?>
                                                </td>
                                                <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->created_at))); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $transaction->status === 'completed' ? 'success' : 'warning'; ?>">
                                                        <?php echo esc_html(ucfirst($transaction->status)); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else : ?>
                            <p class="text-muted mb-0">
                                <?php esc_html_e('No recent transactions found.', 'blackcnote'); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <!-- No HYIPLab or no investments message -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php esc_html_e('Investment Dashboard', 'blackcnote'); ?></h5>
                        <?php if (!$hyiplab_active) : ?>
                            <p class="card-text">
                                <?php esc_html_e('The HYIPLab plugin is not active. Please install and activate the HYIPLab plugin to view your investment dashboard.', 'blackcnote'); ?>
                            </p>
                        <?php else : ?>
                            <p class="card-text">
                                <?php esc_html_e('No active investments found. Start your first investment to see your dashboard data.', 'blackcnote'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/plans')); ?>" class="btn btn-primary">
                                <?php esc_html_e('View Investment Plans', 'blackcnote'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div> 