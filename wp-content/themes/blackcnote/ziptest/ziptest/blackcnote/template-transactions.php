<?php
/**
 * Template Name: Transactions
 * Template Post Type: page
 *
 * @package BlackCnote
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

// Check if BlackCnote plugin is active
if (!function_exists('blackcnote_system_instance')) {
    wp_die(esc_html__('BlackCnote plugin is required for this page.', 'blackcnote'));
}
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="blackcnote-transactions-page">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Transaction History', 'blackcnote'); ?></h1>
                <p class="page-description">
                    <?php esc_html_e('View your investment transactions and returns.', 'blackcnote'); ?>
                </p>
            </header>

            <!-- Transaction Filters -->
            <div class="transaction-filters mb-4">
                <form id="transactionFilterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="transaction_type" class="form-label"><?php esc_html_e('Type', 'blackcnote'); ?></label>
                        <select class="form-select" id="transaction_type" name="type">
                            <option value=""><?php esc_html_e('All Types', 'blackcnote'); ?></option>
                            <option value="investment"><?php esc_html_e('Investment', 'blackcnote'); ?></option>
                            <option value="return"><?php esc_html_e('Return', 'blackcnote'); ?></option>
                            <option value="withdrawal"><?php esc_html_e('Withdrawal', 'blackcnote'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label"><?php esc_html_e('From Date', 'blackcnote'); ?></label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label"><?php esc_html_e('To Date', 'blackcnote'); ?></label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <?php esc_html_e('Filter', 'blackcnote'); ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="transaction-table-wrapper">
                <?php
                global $wpdb;
                try {
                    $user_id = get_current_user_id();
                    $per_page = 20;
                    $current_page = max(1, get_query_var('paged'));
                    $offset = ($current_page - 1) * $per_page;

                    // Get total transactions count
                    $total_transactions = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM {$wpdb->prefix}blackcnote_transactions WHERE user_id = %d",
                        $user_id
                    ));

                    // Get transactions
                    $transactions = $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM {$wpdb->prefix}blackcnote_transactions 
                        WHERE user_id = %d 
                        ORDER BY created_at DESC 
                        LIMIT %d OFFSET %d",
                        $user_id,
                        $per_page,
                        $offset
                    ));

                    if ($transactions) :
                        ?>
                        <div class="table-responsive">
                            <table class="table transaction-table">
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
                                    <?php foreach ($transactions as $transaction) : ?>
                                        <tr>
                                            <td>
                                                <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($transaction->created_at))); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo esc_attr(get_transaction_type_class($transaction->type)); ?>">
                                                    <?php echo esc_html(ucfirst($transaction->type)); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo esc_html(number_format($transaction->amount, 2)); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo esc_attr(get_transaction_status_class($transaction->status)); ?>">
                                                    <?php echo esc_html(ucfirst($transaction->status)); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-info view-details" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#transactionModal"
                                                        data-transaction-id="<?php echo esc_attr($transaction->id); ?>">
                                                    <?php esc_html_e('View', 'blackcnote'); ?>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php
                        $total_pages = ceil($total_transactions / $per_page);
                        if ($total_pages > 1) :
                            ?>
                            <nav aria-label="Transaction pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php
                                    echo paginate_links(array(
                                        'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                                        'format' => '?paged=%#%',
                                        'current' => $current_page,
                                        'total' => $total_pages,
                                        'prev_text' => '&laquo;',
                                        'next_text' => '&raquo;',
                                        'type' => 'list',
                                        'end_size' => 3,
                                        'mid_size' => 3
                                    ));
                                    ?>
                                </ul>
                            </nav>
                            <?php
                        endif;
                    else :
                        ?>
                        <div class="no-transactions alert alert-info">
                            <?php esc_html_e('No transactions found.', 'blackcnote'); ?>
                        </div>
                        <?php
                    endif;
                } catch (Exception $e) {
                    error_log('BlackCnote Theme Error: ' . $e->getMessage());
                    echo '<p class="error">' . esc_html__('Error loading transactions.', 'blackcnote') . '</p>';
                }
                ?>
            </div>

            <!-- Transaction Details Modal -->
            <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="transactionModalLabel">
                                <?php esc_html_e('Transaction Details', 'blackcnote'); ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="transactionDetails">
                                <!-- Content will be loaded via AJAX -->
                                <div class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden"><?php esc_html_e('Loading...', 'blackcnote'); ?></span>
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