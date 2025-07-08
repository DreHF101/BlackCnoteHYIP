<?php
/**
 * Template part for displaying user transactions
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

// Initialize transactions
$transactions = [];

// Get transactions if HYIPLab is active
if ($hyiplab_active) {
    global $wpdb;
    $transactions = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}hyiplab_transactions 
         WHERE user_id = %d 
         ORDER BY created_at DESC",
        $user_id
    ));
}
?>

<div class="transactions">
    <?php if ($hyiplab_active) : ?>
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?php esc_html_e('Filter Transactions', 'blackcnote'); ?></h5>
                <form id="transaction-filters" class="row g-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">
                            <?php esc_html_e('Transaction Type', 'blackcnote'); ?>
                        </label>
                        <select class="form-select" id="type" name="type">
                            <option value=""><?php esc_html_e('All Types', 'blackcnote'); ?></option>
                            <option value="investment"><?php esc_html_e('Investment', 'blackcnote'); ?></option>
                            <option value="interest"><?php esc_html_e('Interest', 'blackcnote'); ?></option>
                            <option value="withdrawal"><?php esc_html_e('Withdrawal', 'blackcnote'); ?></option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="date_from" class="form-label">
                            <?php esc_html_e('From Date', 'blackcnote'); ?>
                        </label>
                        <input type="date" 
                               class="form-control" 
                               id="date_from" 
                               name="date_from">
                    </div>

                    <div class="col-md-4">
                        <label for="date_to" class="form-label">
                            <?php esc_html_e('To Date', 'blackcnote'); ?>
                        </label>
                        <input type="date" 
                               class="form-control" 
                               id="date_to" 
                               name="date_to">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <?php esc_html_e('Apply Filters', 'blackcnote'); ?>
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <?php esc_html_e('Reset', 'blackcnote'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php esc_html_e('Transaction History', 'blackcnote'); ?></h5>
                <?php if ($transactions) : ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Type', 'blackcnote'); ?></th>
                                    <th><?php esc_html_e('Amount', 'blackcnote'); ?></th>
                                    <th><?php esc_html_e('Date', 'blackcnote'); ?></th>
                                    <th><?php esc_html_e('Status', 'blackcnote'); ?></th>
                                    <th><?php esc_html_e('Details', 'blackcnote'); ?></th>
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
                                        <td>
                                            <?php 
                                            echo esc_html(number_format($transaction->amount, 2));
                                            echo ' $';
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->created_at))); ?>
                                        </td>
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
                                                    "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE id = %d",
                                                    $transaction->plan_id
                                                ));
                                                if ($plan) :
                                            ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-link" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#plan-details-<?php echo esc_attr($transaction->id); ?>">
                                                    <?php esc_html_e('View Plan', 'blackcnote'); ?>
                                                </button>

                                                <!-- Plan Details Modal -->
                                                <div class="modal fade" 
                                                     id="plan-details-<?php echo esc_attr($transaction->id); ?>" 
                                                     tabindex="-1" 
                                                     aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <?php echo esc_html($plan->name); ?>
                                                                </h5>
                                                                <button type="button" 
                                                                        class="btn-close" 
                                                                        data-bs-dismiss="modal" 
                                                                        aria-label="<?php esc_attr_e('Close', 'blackcnote'); ?>">
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ul class="list-unstyled">
                                                                    <li class="mb-2">
                                                                        <strong><?php esc_html_e('Return Rate:', 'blackcnote'); ?></strong>
                                                                        <?php echo esc_html($plan->return_rate); ?>%
                                                                    </li>
                                                                    <li class="mb-2">
                                                                        <strong><?php esc_html_e('Duration:', 'blackcnote'); ?></strong>
                                                                        <?php 
                                                                        printf(
                                                                            /* translators: %d: number of days */
                                                                            esc_html__('%d days', 'blackcnote'),
                                                                            $plan->duration
                                                                        );
                                                                        ?>
                                                                    </li>
                                                                    <li>
                                                                        <strong><?php esc_html_e('Investment Amount:', 'blackcnote'); ?></strong>
                                                                        <?php 
                                                                        echo esc_html(number_format($transaction->amount, 2));
                                                                        echo ' $';
                                                                        ?>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php 
                                                endif;
                                            endif; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="text-muted mb-0">
                        <?php esc_html_e('No transactions found.', 'blackcnote'); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php else : ?>
        <!-- No HYIPLab message -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php esc_html_e('Transaction History', 'blackcnote'); ?></h5>
                        <p class="card-text">
                            <?php esc_html_e('The HYIPLab plugin is not active. Please install and activate the HYIPLab plugin to view your transaction history.', 'blackcnote'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($hyiplab_active) : ?>
<script>
jQuery(document).ready(function($) {
    const filters = $('#transaction-filters');
    
    filters.on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'blackcnote_filter_transactions');
        formData.append('nonce', blackcnoteTheme.nonce);
        
        $.ajax({
            url: blackcnoteTheme.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Update the table with filtered results
                    $('.table tbody').html(response.data.html);
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('<?php esc_html_e('An error occurred. Please try again.', 'blackcnote'); ?>');
            }
        });
    });
});
</script>
<?php endif; ?> 