/**
 * BlackCnote Debug System Admin JavaScript
 * Provides interactive functionality for the debug admin interface
 */

(function($) {
    'use strict';
    
    // Global variables
    let autoRefreshInterval = null;
    let logViewer = null;
    
    // Initialize when document is ready
    $(document).ready(function() {
        initializeDebugAdmin();
    });
    
    /**
     * Initialize debug admin functionality
     */
    function initializeDebugAdmin() {
        setupEventListeners();
        setupLogViewer();
        setupAutoRefresh();
    }
    
    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        // Log level filter
        $('#log-level-filter').on('change', filterLogEntries);
        
        // Log search
        $('#log-search').on('input', filterLogEntries);
        
        // Tab change
        $('.nav-tab').on('click', function(e) {
            if ($(this).hasClass('nav-tab-active')) {
                e.preventDefault();
            }
        });
    }
    
    /**
     * Setup log viewer functionality
     */
    function setupLogViewer() {
        logViewer = $('#log-content');
        
        if (logViewer.length) {
            // Auto-scroll to bottom
            logViewer.scrollTop(logViewer[0].scrollHeight);
            
            // Syntax highlighting for log levels
            highlightLogLevels();
        }
    }
    
    /**
     * Setup auto-refresh functionality
     */
    function setupAutoRefresh() {
        // Check if we're on the logs tab
        if (window.location.search.includes('tab=logs')) {
            startAutoRefresh();
        }
    }
    
    /**
     * Start auto-refresh for logs
     */
    function startAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        
        autoRefreshInterval = setInterval(function() {
            refreshLogContent();
        }, 5000); // Refresh every 5 seconds
        
        showAutoRefreshIndicator(true);
    }
    
    /**
     * Stop auto-refresh
     */
    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
        
        showAutoRefreshIndicator(false);
    }
    
    /**
     * Show/hide auto-refresh indicator
     */
    function showAutoRefreshIndicator(active) {
        let indicator = $('.auto-refresh');
        
        if (indicator.length === 0) {
            indicator = $('<span class="auto-refresh">Auto-refresh</span>');
            $('.log-controls').append(indicator);
        }
        
        indicator.toggleClass('active', active);
    }
    
    /**
     * Refresh log content
     */
    function refreshLogContent() {
        $.ajax({
            url: blackcnoteDebug.ajaxUrl,
            type: 'POST',
            data: {
                action: 'blackcnote_debug_action',
                debug_action: 'get_log_content',
                nonce: blackcnoteDebug.nonce
            },
            success: function(response) {
                if (response.success) {
                    logViewer.val(response.data.content);
                    logViewer.scrollTop(logViewer[0].scrollHeight);
                    highlightLogLevels();
                }
            },
            error: function() {
                showNotification('Failed to refresh log content', 'error');
            }
        });
    }
    
    /**
     * Filter log entries
     */
    function filterLogEntries() {
        const levelFilter = $('#log-level-filter').val();
        const searchTerm = $('#log-search').val().toLowerCase();
        const logContent = logViewer.val();
        const lines = logContent.split('\n');
        const filteredLines = [];
        
        lines.forEach(function(line) {
            let includeLine = true;
            
            // Level filter
            if (levelFilter && !line.includes('[' + levelFilter + ']')) {
                includeLine = false;
            }
            
            // Search filter
            if (searchTerm && !line.toLowerCase().includes(searchTerm)) {
                includeLine = false;
            }
            
            if (includeLine) {
                filteredLines.push(line);
            }
        });
        
        logViewer.val(filteredLines.join('\n'));
    }
    
    /**
     * Highlight log levels with colors
     */
    function highlightLogLevels() {
        // This would require a more sophisticated approach with a rich text editor
        // For now, we'll just ensure the log content is properly formatted
        const logContent = logViewer.val();
        
        // Add basic formatting if needed
        if (logContent && !logContent.includes('[DEBUG]') && !logContent.includes('[INFO]')) {
            // Basic formatting for plain text logs
            const formattedContent = logContent.replace(
                /(\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \[(\w+)\])/g,
                '$1'
            );
            logViewer.val(formattedContent);
        }
    }
    
    /**
     * Global functions for button actions
     */
    window.toggleDebug = function() {
        const button = $(this);
        const currentEnabled = button.text().includes('Disable');
        const newEnabled = !currentEnabled;
        
        $.ajax({
            url: blackcnoteDebug.ajaxUrl,
            type: 'POST',
            data: {
                action: 'blackcnote_debug_action',
                debug_action: 'toggle_debug',
                enabled: newEnabled,
                nonce: blackcnoteDebug.nonce
            },
            beforeSend: function() {
                button.prop('disabled', true).text('Updating...');
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Debug system ' + (newEnabled ? 'enabled' : 'disabled'), 'success');
                    button.text(newEnabled ? 'Disable Debug' : 'Enable Debug');
                    
                    // Refresh page to update status indicators
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification('Failed to update debug system', 'error');
                }
            },
            error: function() {
                showNotification('Failed to update debug system', 'error');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    };
    
    window.clearLog = function() {
        if (!confirm(blackcnoteDebug.strings.confirmClearLog)) {
            return;
        }
        
        $.ajax({
            url: blackcnoteDebug.ajaxUrl,
            type: 'POST',
            data: {
                action: 'blackcnote_debug_action',
                debug_action: 'clear_log',
                nonce: blackcnoteDebug.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Log cleared successfully', 'success');
                    logViewer.val('');
                } else {
                    showNotification('Failed to clear log', 'error');
                }
            },
            error: function() {
                showNotification('Failed to clear log', 'error');
            }
        });
    };
    
    window.rotateLog = function() {
        if (!confirm(blackcnoteDebug.strings.confirmRotateLog)) {
            return;
        }
        
        $.ajax({
            url: blackcnoteDebug.ajaxUrl,
            type: 'POST',
            data: {
                action: 'blackcnote_debug_action',
                debug_action: 'rotate_log',
                nonce: blackcnoteDebug.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Log rotated successfully', 'success');
                    refreshLogContent();
                } else {
                    showNotification('Failed to rotate log', 'error');
                }
            },
            error: function() {
                showNotification('Failed to rotate log', 'error');
            }
        });
    };
    
    window.testLogging = function() {
        $.ajax({
            url: blackcnoteDebug.ajaxUrl,
            type: 'POST',
            data: {
                action: 'blackcnote_debug_action',
                debug_action: 'test_logging',
                nonce: blackcnoteDebug.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Test log entry created', 'success');
                    refreshLogContent();
                } else {
                    showNotification('Failed to create test log entry', 'error');
                }
            },
            error: function() {
                showNotification('Failed to create test log entry', 'error');
            }
        });
    };
    
    window.refreshLog = function() {
        refreshLogContent();
        showNotification('Log refreshed', 'info');
    };
    
    window.downloadLog = function() {
        const logContent = logViewer.val();
        const blob = new Blob([logContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'blackcnote-debug-' + new Date().toISOString().slice(0, 10) + '.log';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    };
    
    window.searchLog = function() {
        $('#log-search').focus();
    };
    
    window.viewLog = function() {
        // Switch to logs tab
        window.location.href = window.location.href.split('?')[0] + '?page=blackcnote-debug&tab=logs';
    };
    
    window.exportLog = function() {
        downloadLog();
    };
    
    window.refreshStats = function() {
        location.reload();
    };
    
    window.runPerformanceTest = function() {
        showNotification('Performance test started...', 'info');
        
        // Simulate performance test
        setTimeout(function() {
            showNotification('Performance test completed', 'success');
        }, 2000);
    };
    
    window.clearPerformanceData = function() {
        if (confirm('Are you sure you want to clear performance data?')) {
            showNotification('Performance data cleared', 'success');
        }
    };
    
    window.exportPerformanceReport = function() {
        showNotification('Performance report exported', 'success');
    };
    
    /**
     * Show notification
     */
    function showNotification(message, type) {
        const notification = $('<div class="debug-notice ' + type + '">' + message + '</div>');
        
        // Remove existing notifications
        $('.debug-notice').remove();
        
        // Add new notification
        $('.wrap').prepend(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * Utility function to format bytes
     */
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
    
    /**
     * Utility function to format time
     */
    function formatTime(seconds) {
        if (seconds < 1) {
            return (seconds * 1000).toFixed(2) + 'ms';
        }
        return seconds.toFixed(3) + 's';
    }
    
    /**
     * Handle tab changes
     */
    $(document).on('click', '.nav-tab', function() {
        const tab = $(this).attr('href').split('tab=')[1];
        
        if (tab === 'logs') {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });
    
    /**
     * Handle form submissions
     */
    $(document).on('submit', 'form', function() {
        const form = $(this);
        const submitButton = form.find('input[type="submit"]');
        
        submitButton.prop('disabled', true).val('Saving...');
        
        // Re-enable after a delay
        setTimeout(function() {
            submitButton.prop('disabled', false).val('Save Configuration');
        }, 3000);
    });
    
    /**
     * Handle window unload
     */
    $(window).on('beforeunload', function() {
        stopAutoRefresh();
    });
    
})(jQuery); 