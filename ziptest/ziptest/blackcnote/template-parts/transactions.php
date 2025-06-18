<?php
/**
 * Template part for displaying transactions
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get user data
$user = wp_get_current_user();
$user_id = $user->ID;

// Get transactions
global $wpdb;
$transactions = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}blackcnote_transactions 
     WHERE user_id = %d 
     ORDER BY created_at DESC",
    $user_id
));
?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?php esc_html_e('Filter Transactions', 'blackcnote'); ?></h5>
                <form id="transactionFilterForm" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="transactionType">
                                    <?php esc_html_e('Transaction Type', 'blackcnote'); ?>
                                </label>
                                <select class="form-control" id="transactionType" name="type">
                                    <option value=""><?php esc_html_e('All Types', 'blackcnote'); ?></option>
                                    <option value="investment"><?php esc_html_e('Investment', 'blackcnote'); ?></option>
                                    <option value="interest"><?php esc_html_e('Interest', 'blackcnote'); ?></option>
                                    <option value="withdrawal"><?php esc_html_e('Withdrawal', 'blackcnote'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fromDate">
                                    <?php esc_html_e('From Date', 'blackcnote'); ?>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fromDate" 
                                       name="from_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="toDate">
                                    <?php esc_html_e('To Date', 'blackcnote'); ?>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="toDate" 
                                       name="to_date">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <?php esc_html_e('Apply Filters', 'blackcnote'); ?>
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <?php esc_html_e('Reset', 'blackcnote'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php esc_html_e('Transaction History', 'blackcnote'); ?></h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Type', 'blackcnote'); ?></th>
                                <th><?php esc_html_e('Amount', 'blackcnote'); ?></th>
                                <th><?php esc_html_e('Date', 'blackcnote'); ?></th>
                                <th><?php esc_html_e('Status', 'blackcnote'); ?></th>
                                <th><?php esc_html_e('Details', 'blackcnote'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTableBody">
                            <?php if ($transactions): ?>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo esc_html($transaction->type); ?></td>
                                        <td>$<?php echo number_format($transaction->amount, 2); ?></td>
                                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->created_at))); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo esc_attr($transaction->status === 'completed' ? 'success' : 'warning'); ?>">
                                                <?php echo esc_html($transaction->status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($transaction->type === 'investment'): ?>
                                                <?php
                                                $plan = $wpdb->get_row(
                                                    $wpdb->prepare(
                                                        "SELECT * FROM {$wpdb->prefix}blackcnote_plans WHERE id = %d",
                                                        $transaction->plan_id
                                                    )
                                                );
                                                if ($plan):
                                                ?>
                                                    <button type="button" 
                                                            class="btn btn-link" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#planDetailsModal" 
                                                            data-plan-id="<?php echo esc_attr($transaction->plan_id); ?>">
                                                        <?php esc_html_e('View Plan', 'blackcnote'); ?>
                                                    </button>
                                                <?php endif; ?>
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
            </div>
        </div>
    </div>
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
                <div class="plan-details">
                    <p>
                        <strong><?php esc_html_e('Return Rate:', 'blackcnote'); ?></strong>
                        <span id="planReturnRate"></span>
                    </p>
                    <p>
                        <strong><?php esc_html_e('Duration:', 'blackcnote'); ?></strong>
                        <span id="planDuration"></span>
                        <?php esc_html__('%d days', 'blackcnote'); ?>
                    </p>
                    <p>
                        <strong><?php esc_html_e('Investment Amount:', 'blackcnote'); ?></strong>
                        <span id="planAmount"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load initial transactions
    loadTransactions();

    // Handle filter form submission
    $('#transactionFilterForm').on('submit', function(e) {
        e.preventDefault();
        loadTransactions();
    });

    // Handle reset button
    $('#transactionFilterForm button[type="reset"]').on('click', function() {
        setTimeout(function() {
            loadTransactions();
        }, 100);
    });

    function loadTransactions() {
        var formData = $('#transactionFilterForm').serialize();
        
        $.ajax({
            url: blackcnoteTheme.ajaxUrl,
            type: 'POST',
            data: {
                action: 'blackcnote_filter_transactions',
                nonce: blackcnoteTheme.nonce,
                ...formData
            },
            success: function(response) {
                if (response.success) {
                    $('#transactionsTableBody').html(response.data.html);
                } else {
                    alert('<?php esc_html_e('An error occurred. Please try again.', 'blackcnote'); ?>');
                }
            },
            error: function() {
                alert('<?php esc_html_e('An error occurred. Please try again.', 'blackcnote'); ?>');
            }
        });
    }
});
</script> 