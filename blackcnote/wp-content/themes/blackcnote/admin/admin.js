/**
 * BlackCnote Admin JavaScript
 * Handles admin interface functionality
 */

(function($) {
    'use strict';

    // Admin namespace
    window.BlackCnoteAdmin = window.BlackCnoteAdmin || {};

    // Configuration
    const config = {
        ajaxUrl: blackcnoteAdmin.ajaxUrl,
        nonce: blackcnoteAdmin.nonce,
        strings: blackcnoteAdmin.strings || {}
    };

    // Utility functions
    const utils = {
        showMessage: function(message, type = 'success') {
            const messageHtml = `
                <div class="message ${type}">
                    <p>${message}</p>
                </div>
            `;
            
            $('.blackcnote-admin h1').after(messageHtml);
            
            setTimeout(() => {
                $('.message').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        },

        showLoading: function(element) {
            $(element).addClass('loading');
        },

        hideLoading: function(element) {
            $(element).removeClass('loading');
        },

        formatBytes: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    };

    // Tab functionality
    const tabs = {
        init: function() {
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                
                const target = $(this).attr('href');
                
                // Update active tab
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                // Show target content
                $('.tab-content').removeClass('active');
                $(target).addClass('active');
            });
        }
    };

    // Settings functionality
    const settings = {
        init: function() {
            this.bindEvents();
            this.initFormValidation();
        },

        bindEvents: function() {
            // Save settings
            $('input[name="blackcnote_save_settings"]').on('click', function(e) {
                e.preventDefault();
                settings.saveSettings();
            });

            // Reset settings
            $('#reset-settings').on('click', function(e) {
                e.preventDefault();
                if (confirm(config.strings.confirmReset || 'Are you sure you want to reset all settings?')) {
                    settings.resetSettings();
                }
            });
        },

        saveSettings: function() {
            const form = $('form');
            const formData = new FormData(form[0]);
            formData.append('action', 'blackcnote_save_settings');
            formData.append('nonce', config.nonce);

            utils.showLoading(form);

            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    utils.hideLoading(form);
                    if (response.success) {
                        utils.showMessage(response.data, 'success');
                    } else {
                        utils.showMessage(response.data || 'Error saving settings', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading(form);
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        resetSettings: function() {
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_reset_settings',
                    nonce: config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        utils.showMessage('Settings reset successfully', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        utils.showMessage('Error resetting settings', 'error');
                    }
                },
                error: function() {
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        initFormValidation: function() {
            $('input[type="email"]').on('blur', function() {
                const email = $(this).val();
                if (email && !utils.isValidEmail(email)) {
                    $(this).addClass('error');
                    $(this).after('<span class="error-message">Please enter a valid email address</span>');
                } else {
                    $(this).removeClass('error');
                    $(this).next('.error-message').remove();
                }
            });
        }
    };

    // Live editing functionality
    const liveEditing = {
        init: function() {
            this.bindEvents();
            this.checkStatus();
        },

        bindEvents: function() {
            $('#start-live-editing').on('click', function() {
                liveEditing.start();
            });

            $('#stop-live-editing').on('click', function() {
                liveEditing.stop();
            });

            $('#test-connection').on('click', function() {
                liveEditing.testConnection();
            });
        },

        checkStatus: function() {
            const services = [
                { name: 'WordPress API', url: '/wp-json/wp/v2/' },
                { name: 'React Service', url: 'http://localhost:5174' },
                { name: 'File Watching', url: '/wp-json/blackcnote/v1/health' }
            ];

            services.forEach(service => {
                this.checkServiceStatus(service);
            });
        },

        checkServiceStatus: function(service) {
            $.ajax({
                url: service.url,
                type: 'GET',
                timeout: 5000,
                success: function() {
                    $(`#${service.name.toLowerCase().replace(/\s+/g, '-')}-status`)
                        .text('Online')
                        .removeClass('offline warning')
                        .addClass('online');
                },
                error: function() {
                    $(`#${service.name.toLowerCase().replace(/\s+/g, '-')}-status`)
                        .text('Offline')
                        .removeClass('online warning')
                        .addClass('offline');
                }
            });
        },

        start: function() {
            utils.showLoading('#start-live-editing');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_start_live_editing',
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#start-live-editing');
                    if (response.success) {
                        utils.showMessage('Live editing started successfully', 'success');
                        liveEditing.updateLogs('Live editing started');
                    } else {
                        utils.showMessage('Error starting live editing', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#start-live-editing');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        stop: function() {
            utils.showLoading('#stop-live-editing');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_stop_live_editing',
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#stop-live-editing');
                    if (response.success) {
                        utils.showMessage('Live editing stopped successfully', 'success');
                        liveEditing.updateLogs('Live editing stopped');
                    } else {
                        utils.showMessage('Error stopping live editing', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#stop-live-editing');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        testConnection: function() {
            utils.showLoading('#test-connection');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_test_connection',
                    nonce: config.nonce,
                    url: 'http://localhost:8888'
                },
                success: function(response) {
                    utils.hideLoading('#test-connection');
                    if (response.success) {
                        utils.showMessage('Connection test successful', 'success');
                    } else {
                        utils.showMessage('Connection test failed', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#test-connection');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        updateLogs: function(message) {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = `[${timestamp}] ${message}\n`;
            
            const logsContainer = $('#live-editing-logs');
            if (logsContainer.find('p').length) {
                logsContainer.empty();
            }
            
            logsContainer.append(`<div>${logEntry}</div>`);
            logsContainer.scrollTop(logsContainer[0].scrollHeight);
        }
    };

    // Development tools functionality
    const devTools = {
        init: function() {
            this.bindEvents();
            this.loadSystemInfo();
        },

        bindEvents: function() {
            // Database tools
            $('#backup-database').on('click', function() {
                devTools.backupDatabase();
            });

            $('#optimize-database').on('click', function() {
                devTools.optimizeDatabase();
            });

            $('#repair-database').on('click', function() {
                devTools.repairDatabase();
            });

            // Cache management
            $('#clear-cache').on('click', function() {
                devTools.clearCache();
            });

            $('#clear-transients').on('click', function() {
                devTools.clearTransients();
            });

            $('#clear-object-cache').on('click', function() {
                devTools.clearObjectCache();
            });

            // File management
            $('#regenerate-thumbnails').on('click', function() {
                devTools.regenerateThumbnails();
            });

            $('#clean-uploads').on('click', function() {
                devTools.cleanUploads();
            });

            $('#export-theme').on('click', function() {
                devTools.exportTheme();
            });
        },

        loadSystemInfo: function() {
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_get_system_info',
                    nonce: config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#system-info').html(response.data);
                    }
                }
            });
        },

        backupDatabase: function() {
            utils.showLoading('#backup-database');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_backup_database',
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#backup-database');
                    if (response.success) {
                        utils.showMessage('Database backup completed successfully', 'success');
                    } else {
                        utils.showMessage('Error creating database backup', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#backup-database');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        optimizeDatabase: function() {
            utils.showLoading('#optimize-database');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_optimize_database',
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#optimize-database');
                    if (response.success) {
                        utils.showMessage('Database optimization completed', 'success');
                    } else {
                        utils.showMessage('Error optimizing database', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#optimize-database');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        repairDatabase: function() {
            if (confirm('Are you sure you want to repair the database? This may take some time.')) {
                utils.showLoading('#repair-database');
                
                $.ajax({
                    url: config.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'blackcnote_repair_database',
                        nonce: config.nonce
                    },
                    success: function(response) {
                        utils.hideLoading('#repair-database');
                        if (response.success) {
                            utils.showMessage('Database repair completed', 'success');
                        } else {
                            utils.showMessage('Error repairing database', 'error');
                        }
                    },
                    error: function() {
                        utils.hideLoading('#repair-database');
                        utils.showMessage('Network error occurred', 'error');
                    }
                });
            }
        },

        clearCache: function() {
            utils.showLoading('#clear-cache');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_clear_cache',
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#clear-cache');
                    if (response.success) {
                        utils.showMessage('Cache cleared successfully', 'success');
                    } else {
                        utils.showMessage('Error clearing cache', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#clear-cache');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        clearTransients: function() {
            utils.showLoading('#clear-transients');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_clear_transients',
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#clear-transients');
                    if (response.success) {
                        utils.showMessage('Transients cleared successfully', 'success');
                    } else {
                        utils.showMessage('Error clearing transients', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#clear-transients');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        clearObjectCache: function() {
            utils.showLoading('#clear-object-cache');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_clear_object_cache',
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#clear-object-cache');
                    if (response.success) {
                        utils.showMessage('Object cache cleared successfully', 'success');
                    } else {
                        utils.showMessage('Error clearing object cache', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#clear-object-cache');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        regenerateThumbnails: function() {
            utils.showLoading('#regenerate-thumbnails');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_regenerate_thumbnails',
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#regenerate-thumbnails');
                    if (response.success) {
                        utils.showMessage('Thumbnails regenerated successfully', 'success');
                    } else {
                        utils.showMessage('Error regenerating thumbnails', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#regenerate-thumbnails');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        cleanUploads: function() {
            if (confirm('Are you sure you want to clean the uploads directory? This will remove unused files.')) {
                utils.showLoading('#clean-uploads');
                
                $.ajax({
                    url: config.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'blackcnote_clean_uploads',
                        nonce: config.nonce
                    },
                    success: function(response) {
                        utils.hideLoading('#clean-uploads');
                        if (response.success) {
                            utils.showMessage('Uploads directory cleaned successfully', 'success');
                        } else {
                            utils.showMessage('Error cleaning uploads directory', 'error');
                        }
                    },
                    error: function() {
                        utils.hideLoading('#clean-uploads');
                        utils.showMessage('Network error occurred', 'error');
                    }
                });
            }
        },

        exportTheme: function() {
            utils.showLoading('#export-theme');
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_export_theme',
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#export-theme');
                    if (response.success) {
                        utils.showMessage('Theme exported successfully', 'success');
                        // Trigger download
                        const link = document.createElement('a');
                        link.href = response.data.download_url;
                        link.download = 'blackcnote-theme.zip';
                        link.click();
                    } else {
                        utils.showMessage('Error exporting theme', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#export-theme');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        }
    };

    // FAQ functionality
    const faq = {
        init: function() {
            $('.faq-question').on('click', function() {
                const answer = $(this).next('.faq-answer');
                const toggle = $(this).find('.faq-toggle');
                
                if (answer.is(':visible')) {
                    answer.slideUp(300);
                    toggle.text('+');
                } else {
                    $('.faq-answer').slideUp(300);
                    $('.faq-toggle').text('+');
                    answer.slideDown(300);
                    toggle.text('-');
                }
            });
        }
    };

    // Investment calculator functionality
    const calculator = {
        init: function() {
            $('#calculate-btn').on('click', function() {
                calculator.calculate();
            });

            // Auto-calculate on input change
            $('#investment-amount, #investment-plan, #investment-duration').on('input change', function() {
                calculator.calculate();
            });
        },

        calculate: function() {
            const amount = parseFloat($('#investment-amount').val()) || 0;
            const plan = $('#investment-plan').val();
            const duration = parseInt($('#investment-duration').val()) || 30;

            // Daily return rates by plan
            const rates = {
                'starter': 0.012,
                'growth': 0.018,
                'premium': 0.025,
                'enterprise': 0.032
            };

            const dailyRate = rates[plan] || 0.018;
            const dailyReturn = amount * dailyRate;
            const totalReturn = dailyReturn * duration;
            const finalAmount = amount + totalReturn;
            const profit = totalReturn;
            const roi = (totalReturn / amount) * 100;

            // Update results
            $('#initial-investment').text('$' + amount.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            $('#daily-return').text('$' + dailyReturn.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            $('#total-return').text('$' + totalReturn.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            $('#final-amount').text('$' + finalAmount.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            $('#profit').text('$' + profit.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            $('#roi').text(roi.toFixed(1) + '%');

            $('#calculator-results').show();
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        tabs.init();
        settings.init();
        liveEditing.init();
        devTools.init();
        faq.init();
        calculator.init();

        // Add utility functions to global scope
        window.BlackCnoteAdmin.utils = utils;
    });

    // Add utility functions
    utils.isValidEmail = function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    };

})(jQuery); 