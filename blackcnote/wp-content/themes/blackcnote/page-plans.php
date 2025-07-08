<?php
/**
 * Template Name: Investment Plans Page
 * The investment plans page template
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
                    <div class="plans-intro">
                        <h2>Investment Plans</h2>
                        <p>Choose from our carefully curated investment plans designed to help you build wealth and achieve your financial goals. Each plan is tailored to different risk tolerances and investment objectives.</p>
                    </div>
                    
                    <div class="plans-grid">
                        <!-- Conservative Plan -->
                        <div class="plan-card conservative">
                            <div class="plan-header">
                                <div class="plan-icon">🛡️</div>
                                <h3>Conservative Plan</h3>
                                <div class="plan-subtitle">Capital Preservation</div>
                            </div>
                            <div class="plan-details">
                                <div class="plan-return">
                                    <span class="return-rate">6-8%</span>
                                    <span class="return-period">Annual Return</span>
                                </div>
                                <div class="plan-features">
                                    <ul>
                                        <li>Low-risk investment strategy</li>
                                        <li>Government bonds and blue-chip stocks</li>
                                        <li>Capital preservation focus</li>
                                        <li>Monthly dividend payments</li>
                                        <li>Professional portfolio management</li>
                                        <li>24/7 account monitoring</li>
                                    </ul>
                                </div>
                                <div class="plan-requirements">
                                    <div class="requirement">
                                        <span class="label">Minimum Investment:</span>
                                        <span class="value">$1,000</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Lock Period:</span>
                                        <span class="value">12 months</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Management Fee:</span>
                                        <span class="value">1.5% annually</span>
                                    </div>
                                </div>
                            </div>
                            <div class="plan-actions">
                                <button class="btn btn-primary">Invest Now</button>
                                <button class="btn btn-secondary">Learn More</button>
                            </div>
                        </div>
                        
                        <!-- Balanced Plan -->
                        <div class="plan-card balanced featured">
                            <div class="plan-badge">Most Popular</div>
                            <div class="plan-header">
                                <div class="plan-icon">⚖️</div>
                                <h3>Balanced Plan</h3>
                                <div class="plan-subtitle">Growth & Income</div>
                            </div>
                            <div class="plan-details">
                                <div class="plan-return">
                                    <span class="return-rate">10-15%</span>
                                    <span class="return-period">Annual Return</span>
                                </div>
                                <div class="plan-features">
                                    <ul>
                                        <li>Balanced risk-reward strategy</li>
                                        <li>Diversified portfolio allocation</li>
                                        <li>Growth and income focus</li>
                                        <li>Quarterly dividend payments</li>
                                        <li>Active portfolio management</li>
                                        <li>Real-time performance tracking</li>
                                    </ul>
                                </div>
                                <div class="plan-requirements">
                                    <div class="requirement">
                                        <span class="label">Minimum Investment:</span>
                                        <span class="value">$5,000</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Lock Period:</span>
                                        <span class="value">18 months</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Management Fee:</span>
                                        <span class="value">2.0% annually</span>
                                    </div>
                                </div>
                            </div>
                            <div class="plan-actions">
                                <button class="btn btn-primary">Invest Now</button>
                                <button class="btn btn-secondary">Learn More</button>
                            </div>
                        </div>
                        
                        <!-- Aggressive Plan -->
                        <div class="plan-card aggressive">
                            <div class="plan-header">
                                <div class="plan-icon">🚀</div>
                                <h3>Aggressive Plan</h3>
                                <div class="plan-subtitle">Maximum Growth</div>
                            </div>
                            <div class="plan-details">
                                <div class="plan-return">
                                    <span class="return-rate">15-25%</span>
                                    <span class="return-period">Annual Return</span>
                                </div>
                                <div class="plan-features">
                                    <ul>
                                        <li>High-growth investment strategy</li>
                                        <li>Emerging markets and tech stocks</li>
                                        <li>Capital appreciation focus</li>
                                        <li>Annual dividend payments</li>
                                        <li>Expert portfolio management</li>
                                        <li>Advanced analytics and insights</li>
                                    </ul>
                                </div>
                                <div class="plan-requirements">
                                    <div class="requirement">
                                        <span class="label">Minimum Investment:</span>
                                        <span class="value">$10,000</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Lock Period:</span>
                                        <span class="value">24 months</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Management Fee:</span>
                                        <span class="value">2.5% annually</span>
                                    </div>
                                </div>
                            </div>
                            <div class="plan-actions">
                                <button class="btn btn-primary">Invest Now</button>
                                <button class="btn btn-secondary">Learn More</button>
                            </div>
                        </div>
                        
                        <!-- Real Estate Plan -->
                        <div class="plan-card real-estate">
                            <div class="plan-header">
                                <div class="plan-icon">🏠</div>
                                <h3>Real Estate Plan</h3>
                                <div class="plan-subtitle">Property Investment</div>
                            </div>
                            <div class="plan-details">
                                <div class="plan-return">
                                    <span class="return-rate">12-18%</span>
                                    <span class="return-period">Annual Return</span>
                                </div>
                                <div class="plan-features">
                                    <ul>
                                        <li>Real estate investment trust (REIT)</li>
                                        <li>Commercial and residential properties</li>
                                        <li>Rental income and appreciation</li>
                                        <li>Monthly rental distributions</li>
                                        <li>Professional property management</li>
                                        <li>Diversified property portfolio</li>
                                    </ul>
                                </div>
                                <div class="plan-requirements">
                                    <div class="requirement">
                                        <span class="label">Minimum Investment:</span>
                                        <span class="value">$15,000</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Lock Period:</span>
                                        <span class="value">36 months</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Management Fee:</span>
                                        <span class="value">2.0% annually</span>
                                    </div>
                                </div>
                            </div>
                            <div class="plan-actions">
                                <button class="btn btn-primary">Invest Now</button>
                                <button class="btn btn-secondary">Learn More</button>
                            </div>
                        </div>
                        
                        <!-- Crypto Plan -->
                        <div class="plan-card crypto">
                            <div class="plan-header">
                                <div class="plan-icon">₿</div>
                                <h3>Crypto Plan</h3>
                                <div class="plan-subtitle">Digital Assets</div>
                            </div>
                            <div class="plan-details">
                                <div class="plan-return">
                                    <span class="return-rate">20-40%</span>
                                    <span class="return-period">Annual Return</span>
                                </div>
                                <div class="plan-features">
                                    <ul>
                                        <li>Cryptocurrency investment strategy</li>
                                        <li>Bitcoin, Ethereum, and altcoins</li>
                                        <li>High volatility, high potential</li>
                                        <li>Staking rewards and mining</li>
                                        <li>Advanced trading algorithms</li>
                                        <li>24/7 market monitoring</li>
                                    </ul>
                                </div>
                                <div class="plan-requirements">
                                    <div class="requirement">
                                        <span class="label">Minimum Investment:</span>
                                        <span class="value">$5,000</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Lock Period:</span>
                                        <span class="value">12 months</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Management Fee:</span>
                                        <span class="value">3.0% annually</span>
                                    </div>
                                </div>
                            </div>
                            <div class="plan-actions">
                                <button class="btn btn-primary">Invest Now</button>
                                <button class="btn btn-secondary">Learn More</button>
                            </div>
                        </div>
                        
                        <!-- Custom Plan -->
                        <div class="plan-card custom">
                            <div class="plan-header">
                                <div class="plan-icon">🎯</div>
                                <h3>Custom Plan</h3>
                                <div class="plan-subtitle">Tailored Strategy</div>
                            </div>
                            <div class="plan-details">
                                <div class="plan-return">
                                    <span class="return-rate">Variable</span>
                                    <span class="return-period">Based on Strategy</span>
                                </div>
                                <div class="plan-features">
                                    <ul>
                                        <li>Personalized investment strategy</li>
                                        <li>Custom portfolio allocation</li>
                                        <li>Individual risk assessment</li>
                                        <li>Flexible payment schedules</li>
                                        <li>Dedicated investment advisor</li>
                                        <li>Regular strategy reviews</li>
                                    </ul>
                                </div>
                                <div class="plan-requirements">
                                    <div class="requirement">
                                        <span class="label">Minimum Investment:</span>
                                        <span class="value">$25,000</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Lock Period:</span>
                                        <span class="value">Flexible</span>
                                    </div>
                                    <div class="requirement">
                                        <span class="label">Management Fee:</span>
                                        <span class="value">Negotiable</span>
                                    </div>
                                </div>
                            </div>
                            <div class="plan-actions">
                                <button class="btn btn-primary">Contact Advisor</button>
                                <button class="btn btn-secondary">Learn More</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Plan Comparison -->
                    <div class="plans-comparison">
                        <h3>Plan Comparison</h3>
                        <div class="table-responsive">
                            <table class="comparison-table">
                                <thead>
                                    <tr>
                                        <th>Feature</th>
                                        <th>Conservative</th>
                                        <th>Balanced</th>
                                        <th>Aggressive</th>
                                        <th>Real Estate</th>
                                        <th>Crypto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Expected Return</td>
                                        <td>6-8%</td>
                                        <td>10-15%</td>
                                        <td>15-25%</td>
                                        <td>12-18%</td>
                                        <td>20-40%</td>
                                    </tr>
                                    <tr>
                                        <td>Risk Level</td>
                                        <td>Low</td>
                                        <td>Medium</td>
                                        <td>High</td>
                                        <td>Medium</td>
                                        <td>Very High</td>
                                    </tr>
                                    <tr>
                                        <td>Minimum Investment</td>
                                        <td>$1,000</td>
                                        <td>$5,000</td>
                                        <td>$10,000</td>
                                        <td>$15,000</td>
                                        <td>$5,000</td>
                                    </tr>
                                    <tr>
                                        <td>Lock Period</td>
                                        <td>12 months</td>
                                        <td>18 months</td>
                                        <td>24 months</td>
                                        <td>36 months</td>
                                        <td>12 months</td>
                                    </tr>
                                    <tr>
                                        <td>Management Fee</td>
                                        <td>1.5%</td>
                                        <td>2.0%</td>
                                        <td>2.5%</td>
                                        <td>2.0%</td>
                                        <td>3.0%</td>
                                    </tr>
                                    <tr>
                                        <td>Dividend Frequency</td>
                                        <td>Monthly</td>
                                        <td>Quarterly</td>
                                        <td>Annual</td>
                                        <td>Monthly</td>
                                        <td>Variable</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Investment Calculator -->
                    <div class="investment-calculator">
                        <h3>Investment Calculator</h3>
                        <div class="calculator-form">
                            <div class="calculator-inputs">
                                <div class="input-group">
                                    <label for="initial-investment">Initial Investment ($)</label>
                                    <input type="number" id="initial-investment" value="10000" min="1000" step="1000">
                                </div>
                                <div class="input-group">
                                    <label for="monthly-contribution">Monthly Contribution ($)</label>
                                    <input type="number" id="monthly-contribution" value="500" min="0" step="100">
                                </div>
                                <div class="input-group">
                                    <label for="annual-return">Annual Return (%)</label>
                                    <input type="number" id="annual-return" value="12" min="1" max="50" step="1">
                                </div>
                                <div class="input-group">
                                    <label for="investment-years">Investment Period (Years)</label>
                                    <input type="number" id="investment-years" value="10" min="1" max="30" step="1">
                                </div>
                            </div>
                            <div class="calculator-results">
                                <div class="result-item">
                                    <span class="result-label">Total Invested:</span>
                                    <span class="result-value" id="total-invested">$70,000</span>
                                </div>
                                <div class="result-item">
                                    <span class="result-label">Total Value:</span>
                                    <span class="result-value" id="total-value">$123,456</span>
                                </div>
                                <div class="result-item">
                                    <span class="result-label">Total Return:</span>
                                    <span class="result-value" id="total-return">$53,456</span>
                                </div>
                                <div class="result-item">
                                    <span class="result-label">Annualized Return:</span>
                                    <span class="result-value" id="annualized-return">12.0%</span>
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