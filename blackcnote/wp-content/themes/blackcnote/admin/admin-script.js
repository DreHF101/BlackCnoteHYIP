/**
 * BlackCnote Admin Scripts
 * Enhanced functionality for the BlackCnote theme admin interface
 *
 * @package BlackCnote
 * @version 2.0
 */

(function($) {
    'use strict';

    // Main admin object
    var BlackCnoteAdmin = {
        
        // Initialize admin functionality
        init: function() {
            this.initTabs();
            this.initColorPickers();
            this.initCodeEditors();
            this.initServiceStatus();
            this.initFormValidation();
            this.initAutoSave();
            this.initTooltips();
            this.initAccordions();
            this.initToggleSwitches();
            this.initProgressBars();
            this.bindEvents();
        },

        // Initialize tab functionality
        initTabs: function() {
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                
                // Update active tab
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                // Show target content
                $('.tab-content').hide();
                $(target).show();
                
                // Store active tab in localStorage
                localStorage.setItem('blackcnote_active_tab', target);
            });
            
            // Restore active tab from localStorage
            var activeTab = localStorage.getItem('blackcnote_active_tab');
            if (activeTab && $(activeTab).length) {
                $('.nav-tab[href="' + activeTab + '"]').click();
            }
        },

        // Initialize color pickers with preview
        initColorPickers: function() {
            $('input[type="color"]').on('change', function() {
                var color = $(this).val();
                var fieldName = $(this).attr('name');
                
                // Update preview if available
                if (fieldName === 'theme_color') {
                    $('body').css('--primary-color', color);
                }
                
                // Auto-save color changes
                BlackCnoteAdmin.autoSaveField(fieldName, color);
            });
        },

        // Initialize code editors with syntax highlighting
        initCodeEditors: function() {
            $('textarea.code').each(function() {
                var $textarea = $(this);
                var $wrapper = $('<div class="code-editor-wrapper"></div>');
                
                // Wrap textarea
                $textarea.wrap($wrapper);
                
                // Add line numbers
                var lines = $textarea.val().split('\n').length;
                var $lineNumbers = $('<div class="line-numbers"></div>');
                for (var i = 1; i <= lines; i++) {
                    $lineNumbers.append('<span>' + i + '</span>');
                }
                $textarea.before($lineNumbers);
                
                // Auto-resize
                $textarea.on('input', function() {
                    var lines = $(this).val().split('\n').length;
                    var $lineNumbers = $(this).siblings('.line-numbers');
                    $lineNumbers.empty();
                    for (var i = 1; i <= lines; i++) {
                        $lineNumbers.append('<span>' + i + '</span>');
                    }
                });
            });
        },

        // Initialize service status checking
        initServiceStatus: function() {
            $('.service-status-check').on('click', function(e) {
                e.preventDefault();
                var $button = $(this);
                var service = $button.data('service');
                var $status = $button.siblings('.service-status');
                
                $button.addClass('loading').text('Checking...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'blackcnote_check_service',
                        service: service,
                        nonce: blackcnote_admin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $status.removeClass('status-error status-warning').addClass('status-success').text('Online');
                        } else {
                            $status.removeClass('status-success status-warning').addClass('status-error').text('Offline');
                        }
                    },
                    error: function() {
                        $status.removeClass('status-success status-warning').addClass('status-error').text('Error');
                    },
                    complete: function() {
                        $button.removeClass('loading').text('Check Status');
                    }
                });
            });
        },

        // Initialize form validation
        initFormValidation: function() {
            $('form').on('submit', function(e) {
                var isValid = true;
                var $form = $(this);
                
                // Clear previous errors
                $form.find('.field-error').remove();
                
                // Validate required fields
                $form.find('[required]').each(function() {
                    var $field = $(this);
                    var value = $field.val().trim();
                    
                    if (!value) {
                        isValid = false;
                        $field.addClass('error');
                        $field.after('<span class="field-error">This field is required.</span>');
                    } else {
                        $field.removeClass('error');
                    }
                });
                
                // Validate URLs
                $form.find('input[type="url"]').each(function() {
                    var $field = $(this);
                    var value = $field.val().trim();
                    
                    if (value && !BlackCnoteAdmin.isValidUrl(value)) {
                        isValid = false;
                        $field.addClass('error');
                        $field.after('<span class="field-error">Please enter a valid URL.</span>');
                    }
                });
                
                // Validate email addresses
                $form.find('input[type="email"]').each(function() {
                    var $field = $(this);
                    var value = $field.val().trim();
                    
                    if (value && !BlackCnoteAdmin.isValidEmail(value)) {
                        isValid = false;
                        $field.addClass('error');
                        $field.after('<span class="field-error">Please enter a valid email address.</span>');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    BlackCnoteAdmin.showNotification('Please correct the errors above.', 'error');
                }
            });
        },

        // Initialize auto-save functionality
        initAutoSave: function() {
            var autoSaveTimer;
            
            $('input, textarea, select').on('change', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(function() {
                    BlackCnoteAdmin.autoSave();
                }, 2000);
            });
        },

        // Auto-save form data
        autoSave: function() {
            var $form = $('form');
            var formData = $form.serialize();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'blackcnote_auto_save',
                    form_data: formData,
                    nonce: blackcnote_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        BlackCnoteAdmin.showNotification('Settings auto-saved.', 'success');
                    }
                }
            });
        },

        // Auto-save individual field
        autoSaveField: function(fieldName, value) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'blackcnote_auto_save_field',
                    field: fieldName,
                    value: value,
                    nonce: blackcnote_admin.nonce
                }
            });
        },

        // Initialize tooltips
        initTooltips: function() {
            $('.tooltip').each(function() {
                var $element = $(this);
                var tooltipText = $element.attr('title');
                
                if (tooltipText) {
                    $element.removeAttr('title');
                    $element.append('<span class="tooltiptext">' + tooltipText + '</span>');
                }
            });
        },

        // Initialize accordions
        initAccordions: function() {
            $('.accordion-header').on('click', function() {
                var $header = $(this);
                var $content = $header.siblings('.accordion-content');
                var $accordion = $header.parent();
                
                if ($content.hasClass('active')) {
                    $content.removeClass('active').slideUp();
                } else {
                    $accordion.find('.accordion-content').removeClass('active').slideUp();
                    $content.addClass('active').slideDown();
                }
            });
        },

        // Initialize toggle switches
        initToggleSwitches: function() {
            $('.toggle-switch input').on('change', function() {
                var $toggle = $(this);
                var isChecked = $toggle.is(':checked');
                var fieldName = $toggle.attr('name');
                
                // Auto-save toggle state
                BlackCnoteAdmin.autoSaveField(fieldName, isChecked ? '1' : '0');
                
                // Show visual feedback
                if (isChecked) {
                    $toggle.closest('.toggle-switch').addClass('active');
                } else {
                    $toggle.closest('.toggle-switch').removeClass('active');
                }
            });
        },

        // Initialize progress bars
        initProgressBars: function() {
            $('.progress-bar').each(function() {
                var $progressBar = $(this);
                var $fill = $progressBar.find('.progress-bar-fill');
                var percentage = $progressBar.data('percentage') || 0;
                
                $fill.css('width', percentage + '%');
            });
        },

        // Bind additional events
        bindEvents: function() {
            // Reset to defaults
            $('.reset-defaults').on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to reset all settings to defaults?')) {
                    BlackCnoteAdmin.resetToDefaults();
                }
            });
            
            // Export settings
            $('.export-settings').on('click', function(e) {
                e.preventDefault();
                BlackCnoteAdmin.exportSettings();
            });
            
            // Import settings
            $('.import-settings').on('change', function() {
                var file = this.files[0];
                if (file) {
                    BlackCnoteAdmin.importSettings(file);
                }
            });
            
            // Preview changes
            $('.preview-changes').on('click', function(e) {
                e.preventDefault();
                BlackCnoteAdmin.previewChanges();
            });
        },

        // Reset settings to defaults
        resetToDefaults: function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'blackcnote_reset_settings',
                    nonce: blackcnote_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        },

        // Export settings
        exportSettings: function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'blackcnote_export_settings',
                    nonce: blackcnote_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var dataStr = JSON.stringify(response.data, null, 2);
                        var dataBlob = new Blob([dataStr], {type: 'application/json'});
                        var url = URL.createObjectURL(dataBlob);
                        var link = document.createElement('a');
                        link.href = url;
                        link.download = 'blackcnote-settings.json';
                        link.click();
                        URL.revokeObjectURL(url);
                    }
                }
            });
        },

        // Import settings
        importSettings: function(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                try {
                    var settings = JSON.parse(e.target.result);
                    BlackCnoteAdmin.confirmImport(settings);
                } catch (error) {
                    BlackCnoteAdmin.showNotification('Invalid settings file.', 'error');
                }
            };
            reader.readAsText(file);
        },

        // Confirm import
        confirmImport: function(settings) {
            if (confirm('Are you sure you want to import these settings? This will overwrite your current settings.')) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'blackcnote_import_settings',
                        settings: JSON.stringify(settings),
                        nonce: blackcnote_admin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        },

        // Preview changes
        previewChanges: function() {
            var $form = $('form');
            var formData = $form.serialize();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'blackcnote_preview_changes',
                    form_data: formData,
                    nonce: blackcnote_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        window.open(response.data.preview_url, '_blank');
                    }
                }
            });
        },

        // Show notification
        showNotification: function(message, type) {
            var $notification = $('<div class="notice notice-' + type + '"><p>' + message + '</p></div>');
            $('.wrap').prepend($notification);
            
            setTimeout(function() {
                $notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        },

        // Utility functions
        isValidUrl: function(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        },

        isValidEmail: function(email) {
            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        // Format bytes
        formatBytes: function(bytes, decimals) {
            if (bytes === 0) return '0 Bytes';
            var k = 1024;
            var dm = decimals || 2;
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        },

        // Format date
        formatDate: function(date) {
            return new Date(date).toLocaleDateString() + ' ' + new Date(date).toLocaleTimeString();
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        BlackCnoteAdmin.init();
    });

    // Make BlackCnoteAdmin globally available
    window.BlackCnoteAdmin = BlackCnoteAdmin;

})(jQuery); 