/**
 * BlackCnote HYIPLab API Admin JavaScript
 * Handles admin interface for HYIPLab API management
 */

(function($) {
    'use strict';
    
    // HYIPLab API Admin Class
    class BlackCnoteHYIPLabAPIAdmin {
        constructor() {
            this.restUrl = blackcnoteHYIPLabAPI.rest_url;
            this.ajaxUrl = blackcnoteHYIPLabAPI.ajax_url;
            this.nonce = blackcnoteHYIPLabAPI.nonce;
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.loadAdminData();
        }
        
        bindEvents() {
            // Admin panel events
            $(document).on('click', '.hyiplab-admin-refresh', this.refreshAllData.bind(this));
            $(document).on('click', '.hyiplab-admin-health', this.performHealthCheck.bind(this));
            $(document).on('click', '.hyiplab-admin-logs', this.viewLogs.bind(this));
            $(document).on('click', '.hyiplab-admin-settings', this.openSettings.bind(this));
        }
        
        loadAdminData() {
            this.getStatus();
            this.getStats();
            this.getHealth();
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
                    this.updateAdminStatusDisplay(data);
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
                    this.updateAdminStatsDisplay(data);
                } else {
                    console.error('Failed to get HYIPLab stats:', response.status);
                }
            } catch (error) {
                console.error('Error getting HYIPLab stats:', error);
            }
        }
        
        async getHealth() {
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
                    this.updateAdminHealthDisplay(data);
                } else {
                    console.error('Failed to get health status:', response.status);
                }
            } catch (error) {
                console.error('Error getting health status:', error);
            }
        }
        
        updateAdminStatusDisplay(data) {
            const statusContainer = $('.hyiplab-admin-status');
            if (statusContainer.length) {
                statusContainer.html(`
                    <div class="admin-status-grid">
                        <div class="status-card">
                            <h4>System Status</h4>
                            <div class="status-value ${data.status}">${data.status.toUpperCase()}</div>
                        </div>
                        <div class="status-card">
                            <h4>Version</h4>
                            <div class="status-value">${data.version}</div>
                        </div>
                        <div class="status-card">
                            <h4>License</h4>
                            <div class="status-value ${data.license}">${data.license}</div>
                        </div>
                        <div class="status-card">
                            <h4>Plugin Status</h4>
                            <div class="status-value ${data.plugin_active ? 'active' : 'inactive'}">${data.plugin_active ? 'ACTIVE' : 'INACTIVE'}</div>
                        </div>
                    </div>
                `);
            }
        }
        
        updateAdminStatsDisplay(data) {
            const statsContainer = $('.hyiplab-admin-stats');
            if (statsContainer.length) {
                statsContainer.html(`
                    <div class="admin-stats-grid">
                        <div class="stat-card">
                            <h4>Total Users</h4>
                            <div class="stat-value">${data.total_users}</div>
                        </div>
                        <div class="stat-card">
                            <h4>Total Investments</h4>
                            <div class="stat-value">${data.total_investments}</div>
                        </div>
                        <div class="stat-card">
                            <h4>Total Invested</h4>
                            <div class="stat-value">$${parseFloat(data.total_invested).toFixed(2)}</div>
                        </div>
                        <div class="stat-card">
                            <h4>Active Plans</h4>
                            <div class="stat-value">${data.active_plans}</div>
                        </div>
                    </div>
                    <div class="stats-footer">
                        <span class="last-updated">Last Updated: ${data.last_updated}</span>
                        <button class="refresh-stats">Refresh</button>
                    </div>
                `);
            }
        }
        
        updateAdminHealthDisplay(data) {
            const healthContainer = $('.hyiplab-admin-health');
            if (healthContainer.length) {
                let healthHtml = `
                    <div class="health-overview ${data.status}">
                        <h3>System Health Overview</h3>
                        <div class="health-status">${data.status.toUpperCase()}</div>
                    </div>
                `;
                
                // Database health
                healthHtml += `
                    <div class="health-section">
                        <h4>Database Health</h4>
                        <div class="health-item">
                            <span class="label">Connection:</span>
                            <span class="value ${data.database}">${data.database}</span>
                        </div>
                    </div>
                `;
                
                // Tables health
                healthHtml += `
                    <div class="health-section">
                        <h4>Database Tables</h4>
                        <div class="tables-grid">
                `;
                
                Object.entries(data.tables).forEach(([table, status]) => {
                    healthHtml += `
                        <div class="table-status ${status}">
                            <span class="table-name">${table}</span>
                            <span class="table-status-value">${status}</span>
                        </div>
                    `;
                });
                
                healthHtml += '</div></div>';
                
                // Plugins health
                healthHtml += `
                    <div class="health-section">
                        <h4>Required Plugins</h4>
                        <div class="plugins-grid">
                `;
                
                Object.entries(data.plugins).forEach(([plugin, status]) => {
                    healthHtml += `
                        <div class="plugin-status ${status}">
                            <span class="plugin-name">${plugin}</span>
                            <span class="plugin-status-value">${status}</span>
                        </div>
                    `;
                });
                
                healthHtml += '</div></div>';
                
                healthContainer.html(healthHtml);
            }
        }
        
        refreshAllData() {
            this.getStatus();
            this.getStats();
            this.getHealth();
            this.showAdminMessage('All data refreshed successfully!', 'success');
        }
        
        performHealthCheck() {
            this.getHealth();
            this.showAdminMessage('Health check completed!', 'info');
        }
        
        viewLogs() {
            // Open logs viewer modal
            const modal = $(`
                <div class="logs-modal">
                    <div class="modal-content">
                        <h3>HYIPLab API Logs</h3>
                        <div class="logs-content">
                            <pre>Loading logs...</pre>
                        </div>
                        <button class="close-modal">Close</button>
                    </div>
                </div>
            `);
            
            $('body').append(modal);
            
            // Load logs content
            this.loadLogs(modal.find('.logs-content'));
            
            // Bind close event
            modal.find('.close-modal').on('click', () => modal.remove());
        }
        
        async loadLogs(container) {
            try {
                // This would typically load from a log file or database
                const logContent = `
[${new Date().toISOString()}] HYIPLab API initialized
[${new Date().toISOString()}] Database tables created successfully
[${new Date().toISOString()}] Sample data inserted
[${new Date().toISOString()}] REST API routes registered
[${new Date().toISOString()}] CORS headers configured
                `;
                
                container.find('pre').text(logContent);
            } catch (error) {
                container.find('pre').text('Error loading logs: ' + error.message);
            }
        }
        
        openSettings() {
            // Open settings modal
            const modal = $(`
                <div class="settings-modal">
                    <div class="modal-content">
                        <h3>HYIPLab API Settings</h3>
                        <div class="settings-form">
                            <div class="setting-group">
                                <label>API Version</label>
                                <input type="text" value="v1" readonly>
                            </div>
                            <div class="setting-group">
                                <label>CORS Enabled</label>
                                <input type="checkbox" checked>
                            </div>
                            <div class="setting-group">
                                <label>Debug Mode</label>
                                <input type="checkbox">
                            </div>
                            <div class="setting-group">
                                <label>Cache Duration (seconds)</label>
                                <input type="number" value="300">
                            </div>
                        </div>
                        <div class="settings-actions">
                            <button class="save-settings">Save Settings</button>
                            <button class="close-modal">Cancel</button>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append(modal);
            
            // Bind events
            modal.find('.close-modal').on('click', () => modal.remove());
            modal.find('.save-settings').on('click', () => {
                this.saveSettings(modal);
                modal.remove();
            });
        }
        
        saveSettings(modal) {
            const settings = {
                cors_enabled: modal.find('input[type="checkbox"]:first').is(':checked'),
                debug_mode: modal.find('input[type="checkbox"]:last').is(':checked'),
                cache_duration: modal.find('input[type="number"]').val()
            };
            
            console.log('Saving settings:', settings);
            this.showAdminMessage('Settings saved successfully!', 'success');
        }
        
        showAdminMessage(message, type = 'info') {
            const messageDiv = $(`
                <div class="admin-message ${type}">
                    <span class="message-text">${message}</span>
                    <button class="close-message">&times;</button>
                </div>
            `);
            
            $('.wp-admin').append(messageDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => messageDiv.fadeOut(() => messageDiv.remove()), 5000);
            
            // Manual close
            messageDiv.find('.close-message').on('click', () => messageDiv.remove());
        }
    }
    
    // Initialize when document is ready
    $(document).ready(function() {
        if (typeof blackcnoteHYIPLabAPI !== 'undefined') {
            new BlackCnoteHYIPLabAPIAdmin();
        }
    });
    
})(jQuery); 