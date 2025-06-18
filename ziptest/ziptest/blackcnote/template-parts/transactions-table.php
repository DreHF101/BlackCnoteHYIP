<?php
/**
 * Template part for displaying transactions table
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$user_id = get_current_user_id();
$transactions = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}blackcnote_transactions 
    WHERE user_id = %d 
    ORDER BY date DESC 
    LIMIT 10",
    $user_id
));
?>

<div class="table-responsive">
    <table class="table blackcnote-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Date', 'blackcnote'); ?></th>
                <th><?php esc_html_e('Type', 'blackcnote'); ?></th>
                <th><?php esc_html_e('Amount', 'blackcnote'); ?></th>
                <th><?php esc_html_e('Status', 'blackcnote'); ?></th>
                <th><?php esc_html_e('Details', 'blackcnote'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($transactions): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->date))); ?></td>
                        <td><?php echo esc_html($transaction->type); ?></td>
                        <td>$<?php echo number_format($transaction->amount, 2); ?></td>
                        <td>
                            <span class="badge bg-<?php echo esc_attr($transaction->status === 'completed' ? 'success' : 'warning'); ?>">
                                <?php echo esc_html($transaction->status); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($transaction->type === 'investment'): ?>
                                <button type="button" 
                                        class="btn btn-link" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#planDetailsModal" 
                                        data-plan-id="<?php echo esc_attr($transaction->plan_id); ?>">
                                    <?php esc_html_e('View Plan', 'blackcnote'); ?>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">
                        <?php esc_html_e('No transactions found.', 'blackcnote'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Plan Details Modal -->
<div class="modal fade" id="planDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Plan Details', 'blackcnote'); ?></h5>
                <button type="button" 
                        class="btn-close" 
                        data-bs-dismiss="modal" 
                        aria-label="<?php esc_attr_e('Close', 'blackcnote'); ?>">
                </button>
            </div>
            <div class="modal-body">
                <?php
                $plan = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM {$wpdb->prefix}blackcnote_plans WHERE id = %d",
                        $transaction->plan_id
                    )
                );
                if ($plan):
                ?>
                    <div class="plan-details">
                        <p>
                            <strong><?php esc_html_e('Return Rate:', 'blackcnote'); ?></strong>
                            <?php echo esc_html($plan->return_rate); ?>%
                        </p>
                        <p>
                            <strong><?php esc_html_e('Duration:', 'blackcnote'); ?></strong>
                            <?php 
                            printf(
                                esc_html__('%d days', 'blackcnote'),
                                $plan->duration
                            );
                            ?>
                        </p>
                        <p>
                            <strong><?php esc_html_e('Investment Amount:', 'blackcnote'); ?></strong>
                            $<?php echo number_format($transaction->amount, 2); ?>
                        </p>
                        <p>
                            <strong><?php esc_html_e('Expected Return:', 'blackcnote'); ?></strong>
                            $<?php echo number_format($transaction->amount * ($plan->return_rate / 100), 2); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" 
                        class="btn btn-secondary" 
                        data-bs-dismiss="modal">
                    <?php esc_html_e('Close', 'blackcnote'); ?>
                </button>
            </div>
        </div>
    </div>
</div> 