/**
 * BlackCnote Debug System Admin JavaScript
 * WordPress admin interface functionality
 *
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Main BlackCnote Debug Admin object
    var BlackCnoteDebugAdmin = {
        
        /**
         * Initialize the admin interface
         */
        init: function() {
            this.bindEvents();
            this.initTooltips();
            this.initAutoRefresh();
        },
        
        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Refresh metrics
            $(document).on('click', '#blackcnote-debug-refresh', this.handleRefresh);
            
            // Clear log
            $(document).on('click', '#blackcnote-debug-clear-log', this.handleClearLog);
            
            // Toggle debug
            $(document).on('click', '#blackcnote-debug-toggle', this.handleToggleDebug);
            
            // Download log
            $(document).on('click', '#blackcnote-debug-download', this.handleDownloadLog);
            
            // Log entry context toggle
            $(document).on('click', '.log-context summary', this.handleContextToggle);
            
            // Auto-refresh toggle
            $(document).on('change', '#blackcnote-debug-auto-refresh', this.handleAutoRefreshToggle);
        },
        
        /**
         * Handle refresh button click
         */
        handleRefresh: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var originalText = $button.text();
            
            $button.text(blackcnoteDebug.strings.loading).prop('disabled', true);
            
            // Add loading class to dashboard
            $('.blackcnote-debug-dashboard').addClass('blackcnote-debug-loading');
            
            // Reload page after short delay to show loading state
            setTimeout(function() {
                location.reload();
            }, 500);
        },
        
        /**
         * Handle clear log button click
         */
        handleClearLog: function(e) {
            e.preventDefault();
            
            if (!confirm(blackcnoteDebug.strings.confirm_clear_log)) {
                return;
            }
            
            var $button = $(this);
            var originalText = $button.text();
            
            $button.text(blackcnoteDebug.strings.loading).prop('disabled', true);
            
            $.ajax({
                url: blackcnoteDebug.ajax_url,
                type: 'POST',
                data: {
                    action: 'blackcnote_debug_clear_log',
                    nonce: blackcnoteDebug.nonce
                },
                success: function(response) {
                    if (response.success) {
                        BlackCnoteDebugAdmin.showNotice(response.data.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        BlackCnoteDebugAdmin.showNotice('Error: ' + response.data.message, 'error');
                        $button.text(originalText).prop('disabled', false);
                    }
                },
                error: function() {
                    BlackCnoteDebugAdmin.showNotice(blackcnoteDebug.strings.error, 'error');
                    $button.text(originalText).prop('disabled', false);
                }
            });
        },
        
        /**
         * Handle toggle debug button click
         */
        handleToggleDebug: function(e) {
            e.preventDefault();
            
            if (!confirm(blackcnoteDebug.strings.confirm_toggle_debug)) {
                return;
            }
            
            var $button = $(this);
            var originalText = $button.text();
            
            $button.text(blackcnoteDebug.strings.loading).prop('disabled', true);
            
            $.ajax({
                url: blackcnoteDebug.ajax_url,
                type: 'POST',
                data: {
                    action: 'blackcnote_debug_toggle',
                    nonce: blackcnoteDebug.nonce
                },
                success: function(response) {
                    if (response.success) {
                        BlackCnoteDebugAdmin.showNotice(response.data.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        BlackCnoteDebugAdmin.showNotice('Error: ' + response.data.message, 'error');
                        $button.text(originalText).prop('disabled', false);
                    }
                },
                error: function() {
                    BlackCnoteDebugAdmin.showNotice(blackcnoteDebug.strings.error, 'error');
                    $button.text(originalText).prop('disabled', false);
                }
            });
        },
        
        /**
         * Handle download log button click
         */
        handleDownloadLog: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var originalText = $button.text();
            
            $button.text(blackcnoteDebug.strings.loading).prop('disabled', true);
            
            // Create temporary form for download
            var $form = $('<form>', {
                method: 'POST',
                action: blackcnoteDebug.ajax_url,
                target: '_blank'
            });
            
            $form.append($('<input>', {
                type: 'hidden',
                name: 'action',
                value: 'blackcnote_debug_download_log'
            }));
            
            $form.append($('<input>', {
                type: 'hidden',
                name: 'nonce',
                value: blackcnoteDebug.nonce
            }));
            
            $('body').append($form);
            $form.submit();
            $form.remove();
            
            // Re-enable button after short delay
            setTimeout(function() {
                $button.text(originalText).prop('disabled', false);
            }, 1000);
        },
        
        /**
         * Handle context toggle
         */
        handleContextToggle: function(e) {
            var $details = $(this).parent();
            var $pre = $details.find('pre');
            
            if ($details.attr('open')) {
                $pre.slideUp(200);
            } else {
                $pre.slideDown(200);
            }
        },
        
        /**
         * Handle auto-refresh toggle
         */
        handleAutoRefreshToggle: function(e) {
            var enabled = $(this).is(':checked');
            
            if (enabled) {
                BlackCnoteDebugAdmin.startAutoRefresh();
            } else {
                BlackCnoteDebugAdmin.stopAutoRefresh();
            }
            
            // Save preference
            localStorage.setItem('blackcnote_debug_auto_refresh', enabled);
        },
        
        /**
         * Initialize tooltips
         */
        initTooltips: function() {
            $('[data-tooltip]').each(function() {
                var $element = $(this);
                var tooltip = $element.data('tooltip');
                
                $element.attr('title', tooltip);
            });
        },
        
        /**
         * Initialize auto-refresh
         */
        initAutoRefresh: function() {
            var autoRefresh = localStorage.getItem('blackcnote_debug_auto_refresh');
            
            if (autoRefresh === 'true') {
                $('#blackcnote-debug-auto-refresh').prop('checked', true);
                this.startAutoRefresh();
            }
        },
        
        /**
         * Start auto-refresh
         */
        startAutoRefresh: function() {
            this.autoRefreshInterval = setInterval(function() {
                BlackCnoteDebugAdmin.refreshMetrics();
            }, 30000); // Refresh every 30 seconds
        },
        
        /**
         * Stop auto-refresh
         */
        stopAutoRefresh: function() {
            if (this.autoRefreshInterval) {
                clearInterval(this.autoRefreshInterval);
                this.autoRefreshInterval = null;
            }
        },
        
        /**
         * Refresh metrics via AJAX
         */
        refreshMetrics: function() {
            $.ajax({
                url: blackcnoteDebug.ajax_url,
                type: 'POST',
                data: {
                    action: 'blackcnote_debug_metrics',
                    nonce: blackcnoteDebug.nonce
                },
                success: function(response) {
                    if (response.success) {
                        BlackCnoteDebugAdmin.updateMetricsDisplay(response.data);
                    }
                }
            });
        },
        
        /**
         * Update metrics display
         */
        updateMetricsDisplay: function(data) {
            // Update metric values
            $.each(data.metrics, function(key, value) {
                var $element = $('[data-metric="' + key + '"]');
                if ($element.length) {
                    $element.text(value);
                }
            });
            
            // Update status indicators
            if (data.system_health) {
                BlackCnoteDebugAdmin.updateStatusIndicators(data.system_health);
            }
        },
        
        /**
         * Update status indicators
         */
        updateStatusIndicators: function(health) {
            $.each(health, function(component, status) {
                var $indicator = $('.status-indicator[data-component="' + component + '"]');
                if ($indicator.length) {
                    $indicator.removeClass('good warning error').addClass(status.status);
                    $indicator.text(status.status.charAt(0).toUpperCase() + status.status.slice(1));
                }
            });
        },
        
        /**
         * Show notice
         */
        showNotice: function(message, type) {
            var $notice = $('<div>', {
                class: 'blackcnote-debug-notice ' + type,
                text: message
            });
            
            $('.blackcnote-debug-dashboard').prepend($notice);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        },
        
        /**
         * Format bytes to human readable
         */
        formatBytes: function(bytes, decimals) {
            if (bytes === 0) return '0 Bytes';
            
            var k = 1024;
            var dm = decimals || 2;
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        },
        
        /**
         * Format timestamp
         */
        formatTimestamp: function(timestamp) {
            var date = new Date(timestamp * 1000);
            return date.toLocaleString();
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        BlackCnoteDebugAdmin.init();
    });
    
    // Make available globally
    window.BlackCnoteDebugAdmin = BlackCnoteDebugAdmin;
    
})(jQuery); 