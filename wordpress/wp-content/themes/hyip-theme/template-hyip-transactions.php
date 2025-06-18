<?php
/**
 * Template Name: HYIP Transactions
 * Template Post Type: page
 *
 * @package HYIP_Theme
 */

if (!hyip_is_hyiplab_active()) {
    wp_die(esc_html__('HYIPLab plugin is required for this template.', 'hyip-theme'));
}

get_header();
?>

<main id="primary" class="site-main">
    <div class="hyip-transactions">
        <h1><?php esc_html_e('Transaction History', 'hyip-theme'); ?></h1>

        <div class="transactions-filters">
            <form class="filter-form">
                <div class="form-group">
                    <label for="transaction-type"><?php esc_html_e('Transaction Type', 'hyip-theme'); ?></label>
                    <select id="transaction-type" name="type">
                        <option value="all"><?php esc_html_e('All Types', 'hyip-theme'); ?></option>
                        <option value="deposit"><?php esc_html_e('Deposits', 'hyip-theme'); ?></option>
                        <option value="withdrawal"><?php esc_html_e('Withdrawals', 'hyip-theme'); ?></option>
                        <option value="interest"><?php esc_html_e('Interest', 'hyip-theme'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date-range"><?php esc_html_e('Date Range', 'hyip-theme'); ?></label>
                    <select id="date-range" name="date">
                        <option value="all"><?php esc_html_e('All Time', 'hyip-theme'); ?></option>
                        <option value="today"><?php esc_html_e('Today', 'hyip-theme'); ?></option>
                        <option value="week"><?php esc_html_e('This Week', 'hyip-theme'); ?></option>
                        <option value="month"><?php esc_html_e('This Month', 'hyip-theme'); ?></option>
                    </select>
                </div>
                <button type="submit" class="button"><?php esc_html_e('Apply Filters', 'hyip-theme'); ?></button>
            </form>
        </div>

        <div class="transactions-list">
            <table>
                <thead>
                    <tr>
                        <th><?php esc_html_e('Date', 'hyip-theme'); ?></th>
                        <th><?php esc_html_e('Type', 'hyip-theme'); ?></th>
                        <th><?php esc_html_e('Amount', 'hyip-theme'); ?></th>
                        <th><?php esc_html_e('Status', 'hyip-theme'); ?></th>
                        <th><?php esc_html_e('Details', 'hyip-theme'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="no-transactions">
                        <td colspan="5"><?php esc_html_e('No transactions found.', 'hyip-theme'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="transactions-pagination">
            <span class="pagination-info"><?php esc_html_e('Showing 0 of 0 transactions', 'hyip-theme'); ?></span>
            <div class="pagination-links">
                <a href="#" class="prev disabled"><?php esc_html_e('Previous', 'hyip-theme'); ?></a>
                <a href="#" class="next disabled"><?php esc_html_e('Next', 'hyip-theme'); ?></a>
            </div>
        </div>
    </div>
</main>

<?php
get_footer(); 