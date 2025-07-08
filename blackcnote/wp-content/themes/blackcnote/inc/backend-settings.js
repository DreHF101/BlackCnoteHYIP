/**
 * BlackCnote Backend Settings & Frontend JavaScript
 * Comprehensive functionality for admin settings and frontend interactions
 *
 * @package BlackCnote
 * @version 2.0
 */

(function($) {
    'use strict';

    // Main BlackCnote object
    var BlackCnote = {
        
        // Initialize all functionality
        init: function() {
            this.initBackendSettings();
            this.initFrontendFeatures();
            this.initInvestmentCalculator();
            this.initContactForm();
            this.initLiveEditing();
            this.initResponsiveMenu();
            this.initSmoothScrolling();
            this.initTooltips();
            this.initAnimations();
        },

        // Backend Settings Functionality
        initBackendSettings: function() {
            if ($('.blackcnote-admin-wrap').length) {
                this.initTabs();
                this.initColorPickers();
                this.initCodeEditors();
                this.initFormValidation();
                this.initAutoSave();
                this.initSettingsExport();
                this.initSettingsImport();
                this.initSettingsBackup();
                this.initSettingsRestore();
                this.initSettingsReset();
                this.initSettingsValidation();
            }
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
                if (fieldName === 'blackcnote_primary_color') {
                    $('body').css('--primary-color', color);
                }
                
                // Auto-save color changes
                BlackCnote.autoSaveField(fieldName, color);
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

        // Initialize form validation
        initFormValidation: function() {
            $('.blackcnote-settings-form').on('submit', function(e) {
                var isValid = true;
                var $form = $(this);
                
                // Clear previous errors
                $form.find('.error-message').remove();
                $form.find('.form-error').removeClass('form-error');
                
                // Validate required fields
                $form.find('[required]').each(function() {
                    var $field = $(this);
                    var value = $field.val().trim();
                    
                    if (!value) {
                        isValid = false;
                        $field.addClass('form-error');
                        $field.after('<div class="error-message">This field is required.</div>');
                    }
                });
                
                // Validate email fields
                $form.find('input[type="email"]').each(function() {
                    var $field = $(this);
                    var value = $field.val().trim();
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    
                    if (value && !emailRegex.test(value)) {
                        isValid = false;
                        $field.addClass('form-error');
                        $field.after('<div class="error-message">Please enter a valid email address.</div>');
                    }
                });
                
                // Validate URL fields
                $form.find('input[type="url"]').each(function() {
                    var $field = $(this);
                    var value = $field.val().trim();
                    var urlRegex = /^https?:\/\/.+/;
                    
                    if (value && !urlRegex.test(value)) {
                        isValid = false;
                        $field.addClass('form-error');
                        $field.after('<div class="error-message">Please enter a valid URL starting with http:// or https://</div>');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    BlackCnote.showMessage('Please correct the errors above.', 'error');
                }
            });
        },

        // Auto-save functionality
        initAutoSave: function() {
            var autoSaveTimer;
            
            $('.blackcnote-settings-form input, .blackcnote-settings-form textarea, .blackcnote-settings-form select').on('change', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(function() {
                    BlackCnote.autoSaveSettings();
                }, 2000);
            });
        },

        // Auto-save field
        autoSaveField: function(fieldName, value) {
            $.ajax({
                url: blackcnoteBackend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_save_settings',
                    nonce: blackcnoteBackend.nonce,
                    field: fieldName,
                    value: value
                },
                success: function(response) {
                    if (response.success) {
                        BlackCnote.showMessage('Field saved automatically.', 'success');
                    }
                }
            });
        },

        // Auto-save all settings
        autoSaveSettings: function() {
            var $form = $('.blackcnote-settings-form');
            var formData = $form.serialize();
            
            $.ajax({
                url: blackcnoteBackend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_save_settings',
                    nonce: blackcnoteBackend.nonce,
                    settings: formData
                },
                success: function(response) {
                    if (response.success) {
                        BlackCnote.showMessage('Settings saved automatically.', 'success');
                    }
                }
            });
        },

        // Initialize settings export
        initSettingsExport: function() {
            $('.export-settings').on('click', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: blackcnoteBackend.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'blackcnote_export_settings',
                        nonce: blackcnoteBackend.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            var dataStr = JSON.stringify(response.data, null, 2);
                            var dataBlob = new Blob([dataStr], {type: 'application/json'});
                            var url = URL.createObjectURL(dataBlob);
                            var link = document.createElement('a');
                            link.href = url;
                            link.download = 'blackcnote-settings-' + new Date().toISOString().split('T')[0] + '.json';
                            link.click();
                            URL.revokeObjectURL(url);
                        }
                    }
                });
            });
        },

        // Initialize settings import
        initSettingsImport: function() {
            $('.import-settings').on('click', function(e) {
                e.preventDefault();
                
                var $input = $('<input type="file" accept=".json" style="display: none;">');
                $('body').append($input);
                
                $input.on('change', function() {
                    var file = this.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            try {
                                var data = JSON.parse(e.target.result);
                                
                                if (confirm('Are you sure you want to import these settings? This will overwrite your current settings.')) {
                                    $.ajax({
                                        url: blackcnoteBackend.ajaxUrl,
                                        type: 'POST',
                                        data: {
                                            action: 'blackcnote_import_settings',
                                            nonce: blackcnoteBackend.nonce,
                                            import_data: data
                                        },
                                        success: function(response) {
                                            if (response.success) {
                                                BlackCnote.showMessage('Settings imported successfully.', 'success');
                                                location.reload();
                                            } else {
                                                BlackCnote.showMessage('Failed to import settings.', 'error');
                                            }
                                        }
                                    });
                                }
                            } catch (error) {
                                BlackCnote.showMessage('Invalid file format.', 'error');
                            }
                        };
                        reader.readAsText(file);
                    }
                });
                
                $input.click();
                $input.remove();
            });
        },

        // Initialize settings backup
        initSettingsBackup: function() {
            $('.backup-settings').on('click', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: blackcnoteBackend.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'blackcnote_backup_settings',
                        nonce: blackcnoteBackend.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            BlackCnote.showMessage('Settings backed up successfully.', 'success');
                        }
                    }
                });
            });
        },

        // Initialize settings restore
        initSettingsRestore: function() {
            $('.restore-settings').on('click', function(e) {
                e.preventDefault();
                
                if (confirm('Are you sure you want to restore the last backup? This will overwrite your current settings.')) {
                    $.ajax({
                        url: blackcnoteBackend.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'blackcnote_restore_settings',
                            nonce: blackcnoteBackend.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                BlackCnote.showMessage('Settings restored successfully.', 'success');
                                location.reload();
                            }
                        }
                    });
                }
            });
        },

        // Initialize settings reset
        initSettingsReset: function() {
            $('.reset-settings').on('click', function(e) {
                e.preventDefault();
                
                if (confirm('Are you sure you want to reset all settings to default? This action cannot be undone.')) {
                    $.ajax({
                        url: blackcnoteBackend.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'blackcnote_reset_settings',
                            nonce: blackcnoteBackend.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                BlackCnote.showMessage('Settings reset successfully.', 'success');
                                location.reload();
                            }
                        }
                    });
                }
            });
        },

        // Initialize settings validation
        initSettingsValidation: function() {
            $('.validate-settings').on('click', function(e) {
                e.preventDefault();
                
                var $form = $('.blackcnote-settings-form');
                var formData = $form.serialize();
                
                $.ajax({
                    url: blackcnoteBackend.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'blackcnote_validate_settings',
                        nonce: blackcnoteBackend.nonce,
                        settings: formData
                    },
                    success: function(response) {
                        if (response.success) {
                            BlackCnote.showMessage('Settings validation passed.', 'success');
                        } else {
                            BlackCnote.showMessage('Settings validation failed: ' + response.data.join(', '), 'error');
                        }
                    }
                });
            });
        },

        // Frontend Features
        initFrontendFeatures: function() {
            this.initInvestmentCalculator();
            this.initContactForm();
            this.initResponsiveMenu();
            this.initSmoothScrolling();
            this.initTooltips();
            this.initAnimations();
        },

        // Investment Calculator
        initInvestmentCalculator: function() {
            var $calculator = $('.investment-calculator');
            if ($calculator.length) {
                var $inputs = $calculator.find('input');
                var $results = $calculator.find('.result-value');
                
                function calculateInvestment() {
                    var initial = parseFloat($('#initial-investment').val()) || 0;
                    var monthly = parseFloat($('#monthly-contribution').val()) || 0;
                    var rate = parseFloat($('#annual-return').val()) || 0;
                    var years = parseFloat($('#investment-years').val()) || 0;
                    
                    var monthlyRate = rate / 100 / 12;
                    var months = years * 12;
                    
                    var totalInvested = initial + (monthly * months);
                    var totalValue = initial * Math.pow(1 + monthlyRate, months);
                    
                    if (monthly > 0) {
                        totalValue += monthly * ((Math.pow(1 + monthlyRate, months) - 1) / monthlyRate);
                    }
                    
                    var totalReturn = totalValue - totalInvested;
                    var annualizedReturn = ((totalValue / totalInvested) - 1) * 100;
                    
                    $('#total-invested').text('$' + totalInvested.toLocaleString());
                    $('#total-value').text('$' + totalValue.toLocaleString());
                    $('#total-return').text('$' + totalReturn.toLocaleString());
                    $('#annualized-return').text(annualizedReturn.toFixed(1) + '%');
                }
                
                $inputs.on('input', calculateInvestment);
                calculateInvestment();
            }
        },

        // Contact Form
        initContactForm: function() {
            $('.contact-form').on('submit', function(e) {
                e.preventDefault();
                
                var $form = $(this);
                var $submitBtn = $form.find('button[type="submit"]');
                var originalText = $submitBtn.text();
                
                // Show loading state
                $submitBtn.text('Sending...').prop('disabled', true);
                
                // Collect form data
                var formData = $form.serialize();
                
                // Simulate form submission (replace with actual AJAX call)
                setTimeout(function() {
                    $submitBtn.text('Message Sent!').addClass('btn-success');
                    
                    // Reset form
                    $form[0].reset();
                    
                    // Show success message
                    BlackCnote.showMessage('Thank you! Your message has been sent successfully.', 'success');
                    
                    // Reset button after 3 seconds
                    setTimeout(function() {
                        $submitBtn.text(originalText).prop('disabled', false).removeClass('btn-success');
                    }, 3000);
                }, 2000);
            });
        },

        // Responsive Menu
        initResponsiveMenu: function() {
            var $menuToggle = $('.menu-toggle');
            var $nav = $('#site-navigation');
            
            if ($menuToggle.length) {
                $menuToggle.on('click', function() {
                    $nav.toggleClass('active');
                    $(this).toggleClass('active');
                });
                
                // Close menu when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#site-navigation, .menu-toggle').length) {
                        $nav.removeClass('active');
                        $menuToggle.removeClass('active');
                    }
                });
            }
        },

        // Smooth Scrolling
        initSmoothScrolling: function() {
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            });
        },

        // Tooltips
        initTooltips: function() {
            $('[data-tooltip]').on('mouseenter', function() {
                var $this = $(this);
                var tooltip = $this.attr('data-tooltip');
                
                var $tooltip = $('<div class="tooltip">' + tooltip + '</div>');
                $('body').append($tooltip);
                
                var offset = $this.offset();
                $tooltip.css({
                    left: offset.left + ($this.outerWidth() / 2) - ($tooltip.outerWidth() / 2),
                    top: offset.top - $tooltip.outerHeight() - 10
                });
                
                $tooltip.fadeIn(200);
            }).on('mouseleave', function() {
                $('.tooltip').fadeOut(200, function() {
                    $(this).remove();
                });
            });
        },

        // Animations
        initAnimations: function() {
            // Animate elements on scroll
            function animateOnScroll() {
                $('.animate-on-scroll').each(function() {
                    var $this = $(this);
                    var elementTop = $this.offset().top;
                    var elementBottom = elementTop + $this.outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();
                    
                    if (elementBottom > viewportTop && elementTop < viewportBottom) {
                        $this.addClass('animated');
                    }
                });
            }
            
            $(window).on('scroll', animateOnScroll);
            animateOnScroll();
        },

        // Live Editing
        initLiveEditing: function() {
            if (typeof blackcnoteLiveEditing !== 'undefined') {
                // Initialize live editing features
                this.initContentEditing();
                this.initStyleEditing();
                this.initComponentEditing();
            }
        },

        // Content Editing
        initContentEditing: function() {
            $('[data-editable]').on('click', function(e) {
                if (blackcnoteLiveEditing.isEnabled) {
                    e.preventDefault();
                    var $this = $(this);
                    var content = $this.text();
                    
                    var $input = $('<textarea class="live-edit-input">' + content + '</textarea>');
                    $this.html($input);
                    $input.focus();
                    
                    $input.on('blur', function() {
                        var newContent = $(this).val();
                        $this.text(newContent);
                        
                        // Save changes
                        BlackCnote.saveContentChange($this.attr('data-editable'), newContent);
                    });
                }
            });
        },

        // Style Editing
        initStyleEditing: function() {
            $('.style-editor').on('change', function() {
                var property = $(this).attr('data-property');
                var value = $(this).val();
                
                $('body').css(property, value);
                
                // Save style change
                BlackCnote.saveStyleChange(property, value);
            });
        },

        // Component Editing
        initComponentEditing: function() {
            $('.component-editor').on('change', function() {
                var component = $(this).attr('data-component');
                var value = $(this).val();
                
                // Update component
                BlackCnote.updateComponent(component, value);
            });
        },

        // Save content change
        saveContentChange: function(selector, content) {
            $.ajax({
                url: blackcnoteLiveEditing.apiUrl + '/content',
                type: 'POST',
                data: {
                    selector: selector,
                    content: content,
                    nonce: blackcnoteLiveEditing.nonce
                },
                success: function(response) {
                    if (response.success) {
                        BlackCnote.showMessage('Content updated successfully.', 'success');
                    }
                }
            });
        },

        // Save style change
        saveStyleChange: function(property, value) {
            $.ajax({
                url: blackcnoteLiveEditing.apiUrl + '/styles',
                type: 'POST',
                data: {
                    property: property,
                    value: value,
                    nonce: blackcnoteLiveEditing.nonce
                },
                success: function(response) {
                    if (response.success) {
                        BlackCnote.showMessage('Style updated successfully.', 'success');
                    }
                }
            });
        },

        // Update component
        updateComponent: function(component, value) {
            $.ajax({
                url: blackcnoteLiveEditing.apiUrl + '/components',
                type: 'POST',
                data: {
                    component: component,
                    value: value,
                    nonce: blackcnoteLiveEditing.nonce
                },
                success: function(response) {
                    if (response.success) {
                        BlackCnote.showMessage('Component updated successfully.', 'success');
                    }
                }
            });
        },

        // Show message
        showMessage: function(message, type) {
            var $message = $('<div class="message ' + type + '">' + message + '</div>');
            $('body').append($message);
            
            $message.fadeIn(300);
            
            setTimeout(function() {
                $message.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        },

        // Utility functions
        formatCurrency: function(amount) {
            return '$' + parseFloat(amount).toLocaleString();
        },

        formatPercentage: function(value) {
            return parseFloat(value).toFixed(2) + '%';
        },

        debounce: function(func, wait) {
            var timeout;
            return function executedFunction() {
                var later = function() {
                    clearTimeout(timeout);
                    func();
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle: function(func, limit) {
            var inThrottle;
            return function() {
                var args = arguments;
                var context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(function() {
                        inThrottle = false;
                    }, limit);
                }
            };
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        BlackCnote.init();
    });

    // Expose BlackCnote globally
    window.BlackCnote = BlackCnote;

})(jQuery); 