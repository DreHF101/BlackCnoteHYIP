<?php
/**
 * Template Name: BlackCnote Transactions
 * Template Post Type: page
 *
 * @package BlackCnote_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Check if BlackCnoteLab plugin is active
if (!function_exists('blackcnotelab_system_instance')) {
    wp_die(esc_html__('BlackCnoteLab plugin is required for this page.', 'blackcnote-theme'));
}

// Get current user
$user = wp_get_current_user();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="blackcnote-transactions-page">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Transaction History', 'blackcnote-theme'); ?></h1>
                <p class="page-description">
                    <?php esc_html_e('View your investment transactions and returns.', 'blackcnote-theme'); ?>
                </p>
            </header>

            <!-- Transaction Filters -->
            <div class="transaction-filters mb-4">
                <form id="transactionFilterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="transaction_type" class="form-label"><?php esc_html_e('Type', 'blackcnote-theme'); ?></label>
                        <select class="form-select" id="transaction_type" name="type">
                            <option value=""><?php esc_html_e('All Types', 'blackcnote-theme'); ?></option>
                            <option value="investment"><?php esc_html_e('Investment', 'blackcnote-theme'); ?></option>
                            <option value="return"><?php esc_html_e('Return', 'blackcnote-theme'); ?></option>
                            <option value="withdrawal"><?php esc_html_e('Withdrawal', 'blackcnote-theme'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label"><?php esc_html_e('From Date', 'blackcnote-theme'); ?></label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label"><?php esc_html_e('To Date', 'blackcnote-theme'); ?></label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <?php esc_html_e('Filter', 'blackcnote-theme'); ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="transaction-table-wrapper">
                <?php
                global $wpdb;
                try {
                    $transactions = $wpdb->get_results(
                        "SELECT * FROM {$wpdb->prefix}blackcnotelab_transactions
                        WHERE user_id = {$user->ID}
                        ORDER BY created_at DESC
                        LIMIT 50"
                    );

                    if ($transactions) {
                        ?>
                        <div class="table-responsive">
                            <table class="table transaction-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Date', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Type', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Amount', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Status', 'blackcnote-theme'); ?></th>
                                        <th><?php esc_html_e('Details', 'blackcnote-theme'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction) : ?>
                                        <tr>
                                            <td>
                                                <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($transaction->created_at))); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $transaction->type === 'deposit' ? 'success' : 'warning'; ?>">
                                                    <?php echo esc_html(ucfirst($transaction->type)); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo esc_html(number_format($transaction->amount, 2)); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $transaction->status === 'completed' ? 'success' : 'warning'; ?>">
                                                    <?php echo esc_html(ucfirst($transaction->status)); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo esc_html($transaction->details); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="no-transactions alert alert-info">
                            <?php esc_html_e('No transactions found.', 'blackcnote-theme'); ?>
                        </div>
                    <?php }
                } catch (Exception $e) {
                    error_log('BlackCnote Theme Error: ' . $e->getMessage());
                    echo '<p class="error">' . esc_html__('Error loading transactions.', 'blackcnote-theme') . '</p>';
                }
                ?>
            </div>

            <!-- Transaction Details Modal -->
            <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="transactionModalLabel">
                                <?php esc_html_e('Transaction Details', 'blackcnote-theme'); ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="transactionDetails">
                                <!-- Content will be loaded via AJAX -->
                                <div class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden"><?php esc_html_e('Loading...', 'blackcnote-theme'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
/**
 * Helper function to get transaction type class
 */
function get_transaction_type_class($type) {
    switch ($type) {
        case 'investment':
            return 'primary';
        case 'return':
            return 'success';
        case 'withdrawal':
            return 'warning';
        default:
            return 'secondary';
    }
}

/**
 * Helper function to get transaction status class
 */
function get_transaction_status_class($status) {
    switch ($status) {
        case 'completed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'failed':
            return 'danger';
        default:
            return 'secondary';
    }
}

get_footer(); 