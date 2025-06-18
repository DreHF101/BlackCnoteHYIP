<?php
/**
 * Template part for displaying transactions
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
    LIMIT 50"
);
?>

<div class="transactions">
    <!-- Filter Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Filter Transactions', 'blackcnote-theme'); ?></h5>
                    <form id="transaction-filter" class="row g-3">
                        <div class="col-md-3">
                            <label for="transaction-type" class="form-label">
                                <?php esc_html_e('Transaction Type', 'blackcnote-theme'); ?>
                            </label>
                            <select id="transaction-type" name="type" class="form-select">
                                <option value=""><?php esc_html_e('All Types', 'blackcnote-theme'); ?></option>
                                <option value="investment"><?php esc_html_e('Investment', 'blackcnote-theme'); ?></option>
                                <option value="interest"><?php esc_html_e('Interest', 'blackcnote-theme'); ?></option>
                                <option value="withdrawal"><?php esc_html_e('Withdrawal', 'blackcnote-theme'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="from-date" class="form-label">
                                <?php esc_html_e('From Date', 'blackcnote-theme'); ?>
                            </label>
                            <input type="date" id="from-date" name="from_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="to-date" class="form-label">
                                <?php esc_html_e('To Date', 'blackcnote-theme'); ?>
                            </label>
                            <input type="date" id="to-date" name="to_date" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <?php esc_html_e('Apply Filters', 'blackcnote-theme'); ?>
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <?php esc_html_e('Reset', 'blackcnote-theme'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php esc_html_e('Transaction History', 'blackcnote-theme'); ?></h5>
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
                            <tbody id="transactions-table-body">
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
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#transaction-filter').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: blackcnoteTheme.ajaxUrl,
            type: 'POST',
            data: {
                action: 'blackcnote_filter_transactions',
                nonce: blackcnoteTheme.nonce,
                type: $('#transaction-type').val(),
                from_date: $('#from-date').val(),
                to_date: $('#to-date').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#transactions-table-body').html(response.data.html);
                } else {
                    alert('<?php esc_html_e('An error occurred. Please try again.', 'blackcnote-theme'); ?>');
                }
            },
            error: function() {
                alert('<?php esc_html_e('An error occurred. Please try again.', 'blackcnote-theme'); ?>');
            }
        });
    });
});
</script> 