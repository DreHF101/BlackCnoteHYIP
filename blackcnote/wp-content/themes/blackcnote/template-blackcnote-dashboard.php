<?php
/**
 * Template Name: BlackCnote Dashboard Template
 * Template for the investment dashboard page
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
                    <div class="dashboard-intro">
                        <h2>Welcome to Your Investment Dashboard</h2>
                        <p>Track your investments, monitor performance, and manage your portfolio all in one place.</p>
                    </div>
                    
                    <div class="dashboard-grid">
                        <!-- Portfolio Overview -->
                        <div class="dashboard-section portfolio-overview">
                            <h3>Portfolio Overview</h3>
                            <div class="portfolio-stats">
                                <div class="stat-card">
                                    <div class="stat-value">$125,450.00</div>
                                    <div class="stat-label">Total Portfolio Value</div>
                                    <div class="stat-change positive">+$2,340.50 (+1.9%)</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value">$98,200.00</div>
                                    <div class="stat-label">Total Invested</div>
                                    <div class="stat-change positive">+$27,250.00 (+38.4%)</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value">8</div>
                                    <div class="stat-label">Active Investments</div>
                                    <div class="stat-change neutral">No change</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value">12.5%</div>
                                    <div class="stat-label">Average Return</div>
                                    <div class="stat-change positive">+2.1%</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Activity -->
                        <div class="dashboard-section recent-activity">
                            <h3>Recent Activity</h3>
                            <div class="activity-list">
                                <div class="activity-item">
                                    <div class="activity-icon deposit">💰</div>
                                    <div class="activity-details">
                                        <div class="activity-title">Deposit Completed</div>
                                        <div class="activity-amount">+$5,000.00</div>
                                        <div class="activity-time">2 hours ago</div>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon investment">📈</div>
                                    <div class="activity-details">
                                        <div class="activity-title">New Investment</div>
                                        <div class="activity-amount">Real Estate Fund</div>
                                        <div class="activity-time">1 day ago</div>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon dividend">💵</div>
                                    <div class="activity-details">
                                        <div class="activity-title">Dividend Payment</div>
                                        <div class="activity-amount">+$245.30</div>
                                        <div class="activity-time">3 days ago</div>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon withdrawal">💸</div>
                                    <div class="activity-details">
                                        <div class="activity-title">Withdrawal</div>
                                        <div class="activity-amount">-$1,000.00</div>
                                        <div class="activity-time">1 week ago</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Investment Performance -->
                        <div class="dashboard-section investment-performance">
                            <h3>Investment Performance</h3>
                            <div class="performance-chart">
                                <div class="chart-placeholder">
                                    <div class="chart-message">📊 Performance Chart</div>
                                    <p>Interactive chart showing your portfolio performance over time</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="dashboard-section quick-actions">
                            <h3>Quick Actions</h3>
                            <div class="action-buttons">
                                <a href="#" class="action-btn">
                                    <div class="action-icon">💰</div>
                                    <div class="action-text">Deposit Funds</div>
                                </a>
                                <a href="#" class="action-btn">
                                    <div class="action-icon">💸</div>
                                    <div class="action-text">Withdraw</div>
                                </a>
                                <a href="#" class="action-btn">
                                    <div class="action-icon">📈</div>
                                    <div class="action-text">Invest Now</div>
                                </a>
                                <a href="#" class="action-btn">
                                    <div class="action-icon">📊</div>
                                    <div class="action-text">View Reports</div>
                                </a>
                                <a href="#" class="action-btn">
                                    <div class="action-icon">⚙️</div>
                                    <div class="action-text">Settings</div>
                                </a>
                                <a href="#" class="action-btn">
                                    <div class="action-icon">💬</div>
                                    <div class="action-text">Support</div>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Market News -->
                        <div class="dashboard-section market-news">
                            <h3>Market News</h3>
                            <div class="news-list">
                                <div class="news-item">
                                    <div class="news-title">Bitcoin Reaches New Highs</div>
                                    <div class="news-excerpt">Cryptocurrency markets show strong momentum as Bitcoin breaks previous resistance levels...</div>
                                    <div class="news-time">2 hours ago</div>
                                </div>
                                <div class="news-item">
                                    <div class="news-title">Real Estate Market Update</div>
                                    <div class="news-excerpt">Commercial real estate shows signs of recovery with increased investment activity...</div>
                                    <div class="news-time">4 hours ago</div>
                                </div>
                                <div class="news-item">
                                    <div class="news-title">Federal Reserve Policy Changes</div>
                                    <div class="news-excerpt">New monetary policy decisions impact market sentiment and investment strategies...</div>
                                    <div class="news-time">6 hours ago</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Investment Alerts -->
                        <div class="dashboard-section investment-alerts">
                            <h3>Investment Alerts</h3>
                            <div class="alerts-list">
                                <div class="alert-item warning">
                                    <div class="alert-icon">⚠️</div>
                                    <div class="alert-content">
                                        <div class="alert-title">Portfolio Rebalancing Due</div>
                                        <div class="alert-message">Your portfolio allocation has drifted from target. Consider rebalancing.</div>
                                    </div>
                                </div>
                                <div class="alert-item info">
                                    <div class="alert-icon">ℹ️</div>
                                    <div class="alert-content">
                                        <div class="alert-title">New Investment Opportunity</div>
                                        <div class="alert-message">A new high-yield investment fund is now available.</div>
                                    </div>
                                </div>
                                <div class="alert-item success">
                                    <div class="alert-icon">✅</div>
                                    <div class="alert-content">
                                        <div class="alert-title">Document Verification Complete</div>
                                        <div class="alert-message">Your account verification has been approved.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Investment Details Table -->
                    <div class="dashboard-section investment-details">
                        <h3>Investment Details</h3>
                        <div class="table-responsive">
                            <table class="investment-table">
                                <thead>
                                    <tr>
                                        <th>Investment</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Current Value</th>
                                        <th>Return</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Real Estate Fund A</td>
                                        <td>Real Estate</td>
                                        <td>$25,000</td>
                                        <td>$28,500</td>
                                        <td class="positive">+14.0%</td>
                                        <td><span class="status active">Active</span></td>
                                        <td>
                                            <button class="btn-small">View</button>
                                            <button class="btn-small">Sell</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Tech Growth Fund</td>
                                        <td>Equity</td>
                                        <td>$15,000</td>
                                        <td>$16,800</td>
                                        <td class="positive">+12.0%</td>
                                        <td><span class="status active">Active</span></td>
                                        <td>
                                            <button class="btn-small">View</button>
                                            <button class="btn-small">Sell</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Gold ETF</td>
                                        <td>Commodity</td>
                                        <td>$10,000</td>
                                        <td>$9,500</td>
                                        <td class="negative">-5.0%</td>
                                        <td><span class="status active">Active</span></td>
                                        <td>
                                            <button class="btn-small">View</button>
                                            <button class="btn-small">Sell</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bond Portfolio</td>
                                        <td>Fixed Income</td>
                                        <td>$20,000</td>
                                        <td>$20,400</td>
                                        <td class="positive">+2.0%</td>
                                        <td><span class="status active">Active</span></td>
                                        <td>
                                            <button class="btn-small">View</button>
                                            <button class="btn-small">Sell</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?> 