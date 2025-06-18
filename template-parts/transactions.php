<?php
/**
 * Template part for displaying user transactions
 *
 * @package BlackCnote_Theme
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
    "SELECT * FROM {$wpdb->prefix}blackcnotelab_transactions 
     WHERE user_id = %d 
     ORDER BY created_at DESC",
    $user_id
));
?>

<section class="transactions-section py-5 bg-white">
    <div class="container">
        <div class="card mb-4 shadow border-0">
            <div class="card-body">
                <h5 class="card-title mb-4">Filter Transactions</h5>
                <form id="transaction-filters" class="row g-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Transaction Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">All Types</option>
                            <option value="investment">Investment</option>
                            <option value="interest">Interest</option>
                            <option value="withdrawal">Withdrawal</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-warning">Apply Filters</button>
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow border-0">
            <div class="card-body">
                <h5 class="card-title mb-4">Transaction History</h5>
                <?php if ($transactions) : ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction) : ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $transaction->type === 'investment' ? 'primary' : 
                                                    ($transaction->type === 'interest' ? 'success' : 'warning'); 
                                            ?>">
                                                <?php echo esc_html(ucfirst($transaction->type)); ?>
                                            </span>
                                        </td>
                                        <td><?php echo esc_html(number_format($transaction->amount, 2)); ?> <?php echo esc_html(get_woocommerce_currency_symbol()); ?></td>
                                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->created_at))); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $transaction->status === 'completed' ? 'success' : 
                                                    ($transaction->status === 'pending' ? 'warning' : 'secondary'); 
                                            ?>">
                                                <?php echo esc_html(ucfirst($transaction->status)); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($transaction->type === 'investment' && $transaction->plan_id) : 
                                                $plan = $wpdb->get_row($wpdb->prepare(
                                                    "SELECT * FROM {$wpdb->prefix}blackcnotelab_plans WHERE id = %d",
                                                    $transaction->plan_id
                                                ));
                                                if ($plan) :
                                            ?>
                                                <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#plan-details-<?php echo esc_attr($transaction->id); ?>">
                                                    View Plan
                                                </button>
                                                <div class="modal fade" id="plan-details-<?php echo esc_attr($transaction->id); ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"><?php echo esc_html($plan->name); ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ul class="list-unstyled">
                                                                    <li class="mb-2"><strong>Return Rate:</strong> <?php echo esc_html($plan->return_rate); ?>%</li>
                                                                    <li class="mb-2"><strong>Duration:</strong> <?php printf('%d days', $plan->duration); ?></li>
                                                                    <li><strong>Investment Amount:</strong> <?php echo esc_html(number_format($transaction->amount, 2)); ?> <?php echo esc_html(get_woocommerce_currency_symbol()); ?></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="text-muted mb-0">No transactions found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
jQuery(document).ready(function($) {
    const filters = $('#transaction-filters');
    const tableBody = $('.table tbody');

    filters.on('submit', function(e) {
        e.preventDefault();

        const type = $('#type').val();
        const dateFrom = $('#date_from').val();
        const dateTo = $('#date_to').val();

        $.ajax({
            url: blackcnote_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'blackcnote_filter_transactions',
                nonce: blackcnote_ajax.nonce,
                type: type,
                date_from: dateFrom,
                date_to: dateTo
            },
            success: function(response) {
                if (response.success) {
                    tableBody.html(response.data.html);
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    filters.on('reset', function() {
        setTimeout(function() {
            filters.trigger('submit');
        }, 0);
    });
});
</script> 