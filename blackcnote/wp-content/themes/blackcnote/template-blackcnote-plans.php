<?php
/**
 * Template Name: BlackCnote Investment Plans
 * Template Post Type: page
 *
 * @package BlackCnote
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">
            <header class="page-header">
                <h1 class="page-title"><?php the_title(); ?></h1>
                <div class="page-description">
                    <p>Choose from our carefully curated investment plans designed to maximize your returns while maintaining security and transparency.</p>
                </div>
            </header>

            <div class="investment-plans-section">
                <div class="plans-grid">
                    <!-- Starter Plan -->
                    <div class="plan-card">
                        <div class="plan-header">
                            <div class="plan-icon">🚀</div>
                            <h3 class="plan-name">Starter Plan</h3>
                            <div class="plan-price">
                                <span class="currency">$</span>100
                                <span class="period">/min</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li>1.2% Daily Return</li>
                                <li>30 Days Duration</li>
                                <li>$50 Minimum Withdrawal</li>
                                <li>24/7 Support</li>
                                <li>Secure Transactions</li>
                                <li>Real-time Monitoring</li>
                            </ul>
                        </div>
                        <div class="plan-actions">
                            <button class="btn btn-primary" onclick="selectPlan('starter')">Select Plan</button>
                        </div>
                    </div>

                    <!-- Growth Plan -->
                    <div class="plan-card featured">
                        <div class="plan-badge">Most Popular</div>
                        <div class="plan-header">
                            <div class="plan-icon">📈</div>
                            <h3 class="plan-name">Growth Plan</h3>
                            <div class="plan-price">
                                <span class="currency">$</span>500
                                <span class="period">/min</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li>1.8% Daily Return</li>
                                <li>45 Days Duration</li>
                                <li>$100 Minimum Withdrawal</li>
                                <li>Priority Support</li>
                                <li>Advanced Analytics</li>
                                <li>Portfolio Management</li>
                            </ul>
                        </div>
                        <div class="plan-actions">
                            <button class="btn btn-primary" onclick="selectPlan('growth')">Select Plan</button>
                        </div>
                    </div>

                    <!-- Premium Plan -->
                    <div class="plan-card">
                        <div class="plan-header">
                            <div class="plan-icon">💎</div>
                            <h3 class="plan-name">Premium Plan</h3>
                            <div class="plan-price">
                                <span class="currency">$</span>1000
                                <span class="period">/min</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li>2.5% Daily Return</li>
                                <li>60 Days Duration</li>
                                <li>$200 Minimum Withdrawal</li>
                                <li>VIP Support</li>
                                <li>Custom Strategies</li>
                                <li>Exclusive Opportunities</li>
                            </ul>
                        </div>
                        <div class="plan-actions">
                            <button class="btn btn-primary" onclick="selectPlan('premium')">Select Plan</button>
                        </div>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="plan-card">
                        <div class="plan-header">
                            <div class="plan-icon">🏢</div>
                            <h3 class="plan-name">Enterprise Plan</h3>
                            <div class="plan-price">
                                <span class="currency">$</span>5000
                                <span class="period">/min</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li>3.2% Daily Return</li>
                                <li>90 Days Duration</li>
                                <li>$500 Minimum Withdrawal</li>
                                <li>Dedicated Manager</li>
                                <li>Custom Solutions</li>
                                <li>Private Investment Pool</li>
                            </ul>
                        </div>
                        <div class="plan-actions">
                            <button class="btn btn-primary" onclick="selectPlan('enterprise')">Select Plan</button>
                        </div>
                    </div>
                </div>

                <!-- Investment Calculator -->
                <div class="investment-calculator">
                    <h3>Investment Calculator</h3>
                    <div class="calculator-form">
                        <div class="form-group">
                            <label for="investment-amount">Investment Amount ($)</label>
                            <input type="number" id="investment-amount" class="form-control" placeholder="Enter amount" min="100">
                        </div>
                        <div class="form-group">
                            <label for="investment-plan">Select Plan</label>
                            <select id="investment-plan" class="form-control">
                                <option value="starter">Starter Plan (1.2% daily)</option>
                                <option value="growth">Growth Plan (1.8% daily)</option>
                                <option value="premium">Premium Plan (2.5% daily)</option>
                                <option value="enterprise">Enterprise Plan (3.2% daily)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="investment-duration">Duration (days)</label>
                            <input type="number" id="investment-duration" class="form-control" value="30" min="1" max="365">
                        </div>
                        <button id="calculate-btn" class="btn btn-primary">Calculate Returns</button>
                    </div>

                    <div id="calculator-results" class="calculator-results" style="display: none;">
                        <h4>Investment Results</h4>
                        <div class="results-grid">
                            <div class="result-item">
                                <span class="result-label">Initial Investment:</span>
                                <span id="initial-investment" class="result-value">$0</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Daily Return:</span>
                                <span id="daily-return" class="result-value">$0</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Total Return:</span>
                                <span id="total-return" class="result-value">$0</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Final Amount:</span>
                                <span id="final-amount" class="result-value">$0</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Profit:</span>
                                <span id="profit" class="result-value">$0</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">ROI:</span>
                                <span id="roi" class="result-value">0%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Investment Form -->
                <div id="investment-form" class="investment-form" style="display: none;">
                    <h3>Create Investment</h3>
                    <form id="create-investment-form">
                        <input type="hidden" id="selected-plan" name="plan_id">
                        <div class="form-group">
                            <label for="investment-amount-input">Investment Amount ($)</label>
                            <input type="number" id="investment-amount-input" name="amount" class="form-control" required min="100">
                        </div>
                        <div class="form-group">
                            <label for="payment-method">Payment Method</label>
                            <select id="payment-method" name="payment_method" class="form-control" required>
                                <option value="">Select payment method</option>
                                <option value="bitcoin">Bitcoin</option>
                                <option value="ethereum">Ethereum</option>
                                <option value="usdt">USDT</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="wallet-address">Wallet Address</label>
                            <input type="text" id="wallet-address" name="wallet_address" class="form-control" placeholder="Enter your wallet address">
                        </div>
                        <button type="submit" id="invest-btn" class="btn btn-success">Create Investment</button>
                    </form>
                </div>
            </div>

            <!-- Plan Comparison -->
            <div class="plan-comparison">
                <h3>Plan Comparison</h3>
                <div class="comparison-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Feature</th>
                                <th>Starter</th>
                                <th>Growth</th>
                                <th>Premium</th>
                                <th>Enterprise</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Minimum Investment</td>
                                <td>$100</td>
                                <td>$500</td>
                                <td>$1,000</td>
                                <td>$5,000</td>
                            </tr>
                            <tr>
                                <td>Daily Return</td>
                                <td>1.2%</td>
                                <td>1.8%</td>
                                <td>2.5%</td>
                                <td>3.2%</td>
                            </tr>
                            <tr>
                                <td>Duration</td>
                                <td>30 days</td>
                                <td>45 days</td>
                                <td>60 days</td>
                                <td>90 days</td>
                            </tr>
                            <tr>
                                <td>Min Withdrawal</td>
                                <td>$50</td>
                                <td>$100</td>
                                <td>$200</td>
                                <td>$500</td>
                            </tr>
                            <tr>
                                <td>Support</td>
                                <td>24/7</td>
                                <td>Priority</td>
                                <td>VIP</td>
                                <td>Dedicated</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function selectPlan(planId) {
    document.getElementById('selected-plan').value = planId;
    document.getElementById('investment-form').style.display = 'block';
    document.getElementById('investment-form').scrollIntoView({ behavior: 'smooth' });
}

// Investment calculator functionality
document.addEventListener('DOMContentLoaded', function() {
    const calculateBtn = document.getElementById('calculate-btn');
    const amountInput = document.getElementById('investment-amount');
    const planSelect = document.getElementById('investment-plan');
    const durationInput = document.getElementById('investment-duration');

    function calculate() {
        const amount = parseFloat(amountInput.value) || 0;
        const plan = planSelect.value;
        const duration = parseInt(durationInput.value) || 30;

        const rates = {
            'starter': 0.012,
            'growth': 0.018,
            'premium': 0.025,
            'enterprise': 0.032
        };

        const dailyRate = rates[plan] || 0.012;
        const dailyReturn = amount * dailyRate;
        const totalReturn = dailyReturn * duration;
        const finalAmount = amount + totalReturn;
        const profit = totalReturn;
        const roi = (totalReturn / amount) * 100;

        document.getElementById('initial-investment').textContent = '$' + amount.toLocaleString();
        document.getElementById('daily-return').textContent = '$' + dailyReturn.toFixed(2);
        document.getElementById('total-return').textContent = '$' + totalReturn.toFixed(2);
        document.getElementById('final-amount').textContent = '$' + finalAmount.toFixed(2);
        document.getElementById('profit').textContent = '$' + profit.toFixed(2);
        document.getElementById('roi').textContent = roi.toFixed(1) + '%';

        document.getElementById('calculator-results').style.display = 'block';
    }

    calculateBtn.addEventListener('click', calculate);
    amountInput.addEventListener('input', calculate);
    planSelect.addEventListener('change', calculate);
    durationInput.addEventListener('input', calculate);
});
</script>

<?php
get_footer();
?> 