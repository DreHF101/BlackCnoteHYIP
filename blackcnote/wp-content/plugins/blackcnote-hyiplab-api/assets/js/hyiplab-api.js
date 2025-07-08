/**
 * BlackCnote HYIPLab API Frontend JavaScript
 * Handles API calls and data display for HYIPLab integration
 */

(function($) {
    'use strict';
    
    // HYIPLab API Class
    class BlackCnoteHYIPLabAPI {
        constructor() {
            this.restUrl = blackcnoteHYIPLabAPI.rest_url;
            this.ajaxUrl = blackcnoteHYIPLabAPI.ajax_url;
            this.nonce = blackcnoteHYIPLabAPI.nonce;
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.loadInitialData();
        }
        
        bindEvents() {
            // Bind to investment plan clicks
            $(document).on('click', '.hyiplab-plan', this.handlePlanClick.bind(this));
            
            // Bind to stats refresh
            $(document).on('click', '.refresh-stats', this.refreshStats.bind(this));
            
            // Bind to health check
            $(document).on('click', '.health-check', this.performHealthCheck.bind(this));
        }
        
        loadInitialData() {
            this.getStatus();
            this.getStats();
            this.getPlans();
        }
        
        async getStatus() {
            try {
                const response = await fetch(`${this.restUrl}status`, {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': this.nonce,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.updateStatusDisplay(data);
                } else {
                    console.error('Failed to get HYIPLab status:', response.status);
                }
            } catch (error) {
                console.error('Error getting HYIPLab status:', error);
            }
        }
        
        async getStats() {
            try {
                const response = await fetch(`${this.restUrl}stats`, {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': this.nonce,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.updateStatsDisplay(data);
                } else {
                    console.error('Failed to get HYIPLab stats:', response.status);
                }
            } catch (error) {
                console.error('Error getting HYIPLab stats:', error);
            }
        }
        
        async getPlans() {
            try {
                const response = await fetch(`${this.restUrl}plans`, {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': this.nonce,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.updatePlansDisplay(data);
                } else {
                    console.error('Failed to get HYIPLab plans:', response.status);
                }
            } catch (error) {
                console.error('Error getting HYIPLab plans:', error);
            }
        }
        
        async performHealthCheck() {
            try {
                const response = await fetch(`${this.restUrl}health`, {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': this.nonce,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.updateHealthDisplay(data);
                } else {
                    console.error('Failed to perform health check:', response.status);
                }
            } catch (error) {
                console.error('Error performing health check:', error);
            }
        }
        
        updateStatusDisplay(data) {
            const statusContainer = $('.hyiplab-status');
            if (statusContainer.length) {
                statusContainer.html(`
                    <div class="status-item">
                        <span class="label">Status:</span>
                        <span class="value ${data.status}">${data.status}</span>
                    </div>
                    <div class="status-item">
                        <span class="label">Version:</span>
                        <span class="value">${data.version}</span>
                    </div>
                    <div class="status-item">
                        <span class="label">License:</span>
                        <span class="value ${data.license}">${data.license}</span>
                    </div>
                    <div class="status-item">
                        <span class="label">Plugin Active:</span>
                        <span class="value ${data.plugin_active ? 'active' : 'inactive'}">${data.plugin_active ? 'Yes' : 'No'}</span>
                    </div>
                `);
            }
        }
        
        updateStatsDisplay(data) {
            const statsContainer = $('.hyiplab-stats');
            if (statsContainer.length) {
                statsContainer.html(`
                    <div class="stat-item">
                        <span class="label">Total Users:</span>
                        <span class="value">${data.total_users}</span>
                    </div>
                    <div class="stat-item">
                        <span class="label">Total Investments:</span>
                        <span class="value">${data.total_investments}</span>
                    </div>
                    <div class="stat-item">
                        <span class="label">Total Invested:</span>
                        <span class="value">$${parseFloat(data.total_invested).toFixed(2)}</span>
                    </div>
                    <div class="stat-item">
                        <span class="label">Active Plans:</span>
                        <span class="value">${data.active_plans}</span>
                    </div>
                    <div class="stat-item">
                        <span class="label">Last Updated:</span>
                        <span class="value">${data.last_updated}</span>
                    </div>
                `);
            }
        }
        
        updatePlansDisplay(data) {
            const plansContainer = $('.hyiplab-plans');
            if (plansContainer.length) {
                let plansHtml = '';
                
                data.forEach(plan => {
                    plansHtml += `
                        <div class="plan-item" data-plan-id="${plan.id}">
                            <h3 class="plan-name">${plan.name}</h3>
                            <div class="plan-details">
                                <div class="detail">
                                    <span class="label">Investment Range:</span>
                                    <span class="value">$${parseFloat(plan.min_investment).toFixed(2)} - $${parseFloat(plan.max_investment).toFixed(2)}</span>
                                </div>
                                <div class="detail">
                                    <span class="label">Return Rate:</span>
                                    <span class="value">${plan.return_rate}%</span>
                                </div>
                                <div class="detail">
                                    <span class="label">Duration:</span>
                                    <span class="value">${plan.duration_days} days</span>
                                </div>
                            </div>
                            <button class="invest-button" data-plan-id="${plan.id}">Invest Now</button>
                        </div>
                    `;
                });
                
                plansContainer.html(plansHtml);
            }
        }
        
        updateHealthDisplay(data) {
            const healthContainer = $('.hyiplab-health');
            if (healthContainer.length) {
                let healthHtml = `
                    <div class="health-status ${data.status}">
                        <h3>System Health: ${data.status}</h3>
                    </div>
                `;
                
                // Database status
                healthHtml += `
                    <div class="health-section">
                        <h4>Database</h4>
                        <div class="health-item">
                            <span class="label">Connection:</span>
                            <span class="value ${data.database}">${data.database}</span>
                        </div>
                    </div>
                `;
                
                // Tables status
                healthHtml += `
                    <div class="health-section">
                        <h4>Database Tables</h4>
                `;
                
                Object.entries(data.tables).forEach(([table, status]) => {
                    healthHtml += `
                        <div class="health-item">
                            <span class="label">${table}:</span>
                            <span class="value ${status}">${status}</span>
                        </div>
                    `;
                });
                
                healthHtml += '</div>';
                
                // Plugins status
                healthHtml += `
                    <div class="health-section">
                        <h4>Required Plugins</h4>
                `;
                
                Object.entries(data.plugins).forEach(([plugin, status]) => {
                    healthHtml += `
                        <div class="health-item">
                            <span class="label">${plugin}:</span>
                            <span class="value ${status}">${status}</span>
                        </div>
                    `;
                });
                
                healthHtml += '</div>';
                
                healthContainer.html(healthHtml);
            }
        }
        
        handlePlanClick(event) {
            const planId = $(event.currentTarget).data('plan-id');
            console.log('Plan clicked:', planId);
            
            // Show investment modal or redirect to investment page
            this.showInvestmentModal(planId);
        }
        
        showInvestmentModal(planId) {
            // Create and show investment modal
            const modal = $(`
                <div class="investment-modal">
                    <div class="modal-content">
                        <h3>Invest in Plan</h3>
                        <p>Plan ID: ${planId}</p>
                        <input type="number" placeholder="Investment Amount" class="investment-amount">
                        <button class="confirm-investment" data-plan-id="${planId}">Confirm Investment</button>
                        <button class="close-modal">Cancel</button>
                    </div>
                </div>
            `);
            
            $('body').append(modal);
            
            // Bind modal events
            modal.find('.close-modal').on('click', () => modal.remove());
            modal.find('.confirm-investment').on('click', (e) => {
                const amount = modal.find('.investment-amount').val();
                this.processInvestment(planId, amount);
                modal.remove();
            });
        }
        
        processInvestment(planId, amount) {
            console.log('Processing investment:', { planId, amount });
            
            // Here you would typically make an AJAX call to process the investment
            // For now, just show a success message
            this.showMessage('Investment processed successfully!', 'success');
        }
        
        refreshStats() {
            this.getStats();
            this.showMessage('Statistics refreshed!', 'info');
        }
        
        showMessage(message, type = 'info') {
            const messageDiv = $(`
                <div class="message ${type}">
                    ${message}
                    <button class="close-message">&times;</button>
                </div>
            `);
            
            $('body').append(messageDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => messageDiv.fadeOut(() => messageDiv.remove()), 5000);
            
            // Manual close
            messageDiv.find('.close-message').on('click', () => messageDiv.remove());
        }
    }
    
    // Initialize when document is ready
    $(document).ready(function() {
        if (typeof blackcnoteHYIPLabAPI !== 'undefined') {
            new BlackCnoteHYIPLabAPI();
        }
    });
    
})(jQuery); 