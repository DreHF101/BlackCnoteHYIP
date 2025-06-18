<?php
/**
 * Template part for displaying transactions table
 *
 * @package BlackCnote_Theme
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get transactions
global $wpdb;
$transactions = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}blackcnotelab_transactions
    ORDER BY created_at DESC
    LIMIT 20"
);
?>

<div class="transactions-table">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Type', 'blackcnote-theme'); ?></th>
                    <th><?php esc_html_e('Amount', 'blackcnote-theme'); ?></th>
                    <th><?php esc_html_e('Date', 'blackcnote-theme'); ?></th>
                    <th><?php esc_html_e('Status', 'blackcnote-theme'); ?></th>
                    <th><?php esc_html_e('Details', 'blackcnote-theme'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)) : ?>
                    <?php foreach ($transactions as $transaction) : ?>
                        <tr>
                            <td>
                                <span class="badge bg-<?php echo esc_attr($transaction->type === 'investment' ? 'primary' : ($transaction->type === 'interest' ? 'success' : 'warning')); ?>">
                                    <?php echo esc_html(ucfirst($transaction->type)); ?>
                                </span>
                            </td>
                            <td>
                                <strong>$<?php echo esc_html(number_format($transaction->amount, 2)); ?></strong>
                            </td>
                            <td><?php echo esc_html(date('M j, Y', strtotime($transaction->created_at))); ?></td>
                            <td>
                                <span class="badge bg-<?php echo esc_attr($transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger')); ?>">
                                    <?php echo esc_html(ucfirst($transaction->status)); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($transaction->plan_id) : ?>
                                    <?php
                                    $plan = $wpdb->get_row(
                                        $wpdb->prepare(
                                            "SELECT * FROM {$wpdb->prefix}blackcnotelab_plans WHERE id = %d",
                                            $transaction->plan_id
                                        )
                                    );
                                    ?>
                                    <?php if ($plan) : ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#planModal<?php echo esc_attr($transaction->id); ?>">
                                            <?php esc_html_e('View Plan', 'blackcnote-theme'); ?>
                                        </button>
                                        
                                        <!-- Plan Modal -->
                                        <div class="modal fade" id="planModal<?php echo esc_attr($transaction->id); ?>" tabindex="-1" aria-labelledby="planModalLabel<?php echo esc_attr($transaction->id); ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="planModalLabel<?php echo esc_attr($transaction->id); ?>"><?php echo esc_html($plan->name); ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e('Close', 'blackcnote-theme'); ?>"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong><?php esc_html_e('Return Rate:', 'blackcnote-theme'); ?></strong> <?php echo esc_html($plan->return_rate); ?>%</p>
                                                        <p><strong><?php esc_html_e('Duration:', 'blackcnote-theme'); ?></strong> 
                                                            <?php
                                                            printf(
                                                                esc_html__('%d days', 'blackcnote-theme'),
                                                                $plan->duration
                                                            );
                                                            ?>
                                                        </p>
                                                        <p><strong><?php esc_html_e('Investment Amount:', 'blackcnote-theme'); ?></strong> $<?php echo esc_html(number_format($transaction->amount, 2)); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            <?php esc_html_e('No transactions found.', 'blackcnote-theme'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div> 