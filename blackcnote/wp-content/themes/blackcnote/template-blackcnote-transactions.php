<?php
/**
 * Template Name: BlackCnote Transactions Template
 * Template for the transactions page
 *
 * @package BlackCnote
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><?php the_title(); ?></h1>
        </div>

        <div class="page-content">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="default-content">
                    <div class="transactions-intro">
                        <h2>Transaction History</h2>
                        <p>View and manage all your investment transactions, deposits, withdrawals, and earnings.</p>
                    </div>
                    
                    <!-- Transaction Filters -->
                    <div class="transaction-filters">
                        <div class="filter-group">
                            <label for="transaction-type">Transaction Type:</label>
                            <select id="transaction-type" class="filter-select">
                                <option value="">All Types</option>
                                <option value="deposit">Deposits</option>
                                <option value="withdrawal">Withdrawals</option>
                                <option value="investment">Investments</option>
                                <option value="dividend">Dividends</option>
                                <option value="fee">Fees</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="transaction-status">Status:</label>
                            <select id="transaction-status" class="filter-select">
                                <option value="">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="date-range">Date Range:</label>
                            <select id="date-range" class="filter-select">
                                <option value="7">Last 7 days</option>
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 3 months</option>
                                <option value="365">Last year</option>
                                <option value="all">All time</option>
                            </select>
                        </div>
                        
                        <button class="btn btn-primary" id="apply-filters">Apply Filters</button>
                        <button class="btn btn-secondary" id="reset-filters">Reset</button>
                    </div>
                    
                    <!-- Transaction Summary -->
                    <div class="transaction-summary">
                        <div class="summary-card">
                            <div class="summary-title">Total Deposits</div>
                            <div class="summary-value positive">$45,000.00</div>
                            <div class="summary-period">This month</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-title">Total Withdrawals</div>
                            <div class="summary-value negative">$12,500.00</div>
                            <div class="summary-period">This month</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-title">Total Earnings</div>
                            <div class="summary-value positive">$8,750.00</div>
                            <div class="summary-period">This month</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-title">Pending Transactions</div>
                            <div class="summary-value neutral">3</div>
                            <div class="summary-period">Awaiting processing</div>
                        </div>
                    </div>
                    
                    <!-- Transactions Table -->
                    <div class="transactions-table-container">
                        <div class="table-header">
                            <h3>Recent Transactions</h3>
                            <div class="table-actions">
                                <button class="btn btn-small" id="export-csv">Export CSV</button>
                                <button class="btn btn-small" id="print-transactions">Print</button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="transactions-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction ID</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>2024-12-15 14:30</td>
                                        <td>#TX-2024-001</td>
                                        <td><span class="transaction-type deposit">Deposit</span></td>
                                        <td>Bank transfer deposit</td>
                                        <td class="amount positive">+$5,000.00</td>
                                        <td><span class="status completed">Completed</span></td>
                                        <td>
                                            <button class="btn-small" title="View Details">👁️</button>
                                            <button class="btn-small" title="Download Receipt">📄</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-14 09:15</td>
                                        <td>#TX-2024-002</td>
                                        <td><span class="transaction-type investment">Investment</span></td>
                                        <td>Real Estate Fund purchase</td>
                                        <td class="amount negative">-$25,000.00</td>
                                        <td><span class="status completed">Completed</span></td>
                                        <td>
                                            <button class="btn-small" title="View Details">👁️</button>
                                            <button class="btn-small" title="Download Receipt">📄</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-13 16:45</td>
                                        <td>#TX-2024-003</td>
                                        <td><span class="transaction-type dividend">Dividend</span></td>
                                        <td>Quarterly dividend payment</td>
                                        <td class="amount positive">+$1,250.00</td>
                                        <td><span class="status completed">Completed</span></td>
                                        <td>
                                            <button class="btn-small" title="View Details">👁️</button>
                                            <button class="btn-small" title="Download Receipt">📄</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-12 11:20</td>
                                        <td>#TX-2024-004</td>
                                        <td><span class="transaction-type withdrawal">Withdrawal</span></td>
                                        <td>Withdrawal to bank account</td>
                                        <td class="amount negative">-$3,000.00</td>
                                        <td><span class="status pending">Pending</span></td>
                                        <td>
                                            <button class="btn-small" title="View Details">👁️</button>
                                            <button class="btn-small" title="Cancel">❌</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-11 13:30</td>
                                        <td>#TX-2024-005</td>
                                        <td><span class="transaction-type fee">Fee</span></td>
                                        <td>Monthly account maintenance fee</td>
                                        <td class="amount negative">-$25.00</td>
                                        <td><span class="status completed">Completed</span></td>
                                        <td>
                                            <button class="btn-small" title="View Details">👁️</button>
                                            <button class="btn-small" title="Download Receipt">📄</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-10 08:45</td>
                                        <td>#TX-2024-006</td>
                                        <td><span class="transaction-type investment">Investment</span></td>
                                        <td>Tech Growth Fund purchase</td>
                                        <td class="amount negative">-$15,000.00</td>
                                        <td><span class="status completed">Completed</span></td>
                                        <td>
                                            <button class="btn-small" title="View Details">👁️</button>
                                            <button class="btn-small" title="Download Receipt">📄</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-09 15:20</td>
                                        <td>#TX-2024-007</td>
                                        <td><span class="transaction-type dividend">Dividend</span></td>
                                        <td>Monthly dividend payment</td>
                                        <td class="amount positive">+$850.00</td>
                                        <td><span class="status completed">Completed</span></td>
                                        <td>
                                            <button class="btn-small" title="View Details">👁️</button>
                                            <button class="btn-small" title="Download Receipt">📄</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-08 10:30</td>
                                        <td>#TX-2024-008</td>
                                        <td><span class="transaction-type deposit">Deposit</span></td>
                                        <td>Credit card deposit</td>
                                        <td class="amount positive">+$2,500.00</td>
                                        <td><span class="status failed">Failed</span></td>
                                        <td>
                                            <button class="btn-small" title="View Details">👁️</button>
                                            <button class="btn-small" title="Retry">🔄</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="pagination">
                            <button class="btn btn-small" disabled>Previous</button>
                            <span class="page-info">Page 1 of 5</span>
                            <button class="btn btn-small">Next</button>
                        </div>
                    </div>
                    
                    <!-- Transaction Statistics -->
                    <div class="transaction-statistics">
                        <h3>Transaction Statistics</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-chart">
                                    <div class="chart-placeholder">
                                        <div class="chart-message">📊 Transaction Volume</div>
                                        <p>Monthly transaction volume chart</p>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-chart">
                                    <div class="chart-placeholder">
                                        <div class="chart-message">📈 Success Rate</div>
                                        <p>Transaction success rate over time</p>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-chart">
                                    <div class="chart-placeholder">
                                        <div class="chart-message">💰 Average Transaction</div>
                                        <p>Average transaction amount by type</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transaction Alerts -->
                    <div class="transaction-alerts">
                        <h3>Transaction Alerts</h3>
                        <div class="alerts-list">
                            <div class="alert-item warning">
                                <div class="alert-icon">⚠️</div>
                                <div class="alert-content">
                                    <div class="alert-title">Large Withdrawal Alert</div>
                                    <div class="alert-message">A withdrawal of $3,000.00 is pending approval.</div>
                                </div>
                            </div>
                            <div class="alert-item info">
                                <div class="alert-icon">ℹ️</div>
                                <div class="alert-content">
                                    <div class="alert-title">Failed Transaction</div>
                                    <div class="alert-message">Credit card deposit failed. Please check your card details.</div>
                                </div>
                            </div>
                            <div class="alert-item success">
                                <div class="alert-icon">✅</div>
                                <div class="alert-content">
                                    <div class="alert-title">New Dividend Available</div>
                                    <div class="alert-message">A new dividend payment of $1,250.00 has been processed.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?> 