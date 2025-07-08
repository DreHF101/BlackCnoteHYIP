/**
 * HYIPLab Integration JavaScript
 * 
 * Handles dynamic interactions and functionality for HYIPLab integration
 */

(function($) {
    'use strict';

    // HYIPLab namespace
    window.BlackCnoteHYIPLab = {
        
        // Configuration
        config: {
            ajaxUrl: blackCnoteHYIPLab.ajaxUrl || '/wp-admin/admin-ajax.php',
            nonce: blackCnoteHYIPLab.nonce || '',
            currency: blackCnoteHYIPLab.currency || 'USD',
            isLoggedIn: blackCnoteHYIPLab.isLoggedIn || false,
            userId: blackCnoteHYIPLab.userId || 0
        },

        // Initialize
        init: function() {
            this.bindEvents();
            this.initInvestmentCalculator();
            this.initPlanSelection();
            this.initTransactionFilters();
            this.initRealTimeUpdates();
        },

        // Bind event handlers
        bindEvents: function() {
            var self = this;

            // Investment form submission
            $(document).on('submit', '#hyiplab-investment-form', function(e) {
                e.preventDefault();
                self.handleInvestmentSubmission($(this));
            });

            // Plan selection
            $(document).on('change', '.hyiplab-plan-select', function() {
                self.updateInvestmentForm($(this).val());
            });

            // Amount input changes
            $(document).on('input', '#investment-amount', function() {
                self.calculateInvestmentReturns();
            });

            // Transaction filter changes
            $(document).on('change', '.transaction-filter', function() {
                self.filterTransactions();
            });

            // Widget refresh buttons
            $(document).on('click', '.refresh-widget', function() {
                self.refreshWidget($(this).closest('.widget'));
            });

            // Demo mode toggle
            $(document).on('click', '.demo-mode-toggle', function() {
                self.toggleDemoMode();
            });
        },

        // Initialize investment calculator
        initInvestmentCalculator: function() {
            var $calculator = $('.hyiplab-investment-calculator');
            if ($calculator.length) {
                this.calculateInvestmentReturns();
            }
        },

        // Initialize plan selection
        initPlanSelection: function() {
            var $planSelect = $('.hyiplab-plan-select');
            if ($planSelect.length && $planSelect.val()) {
                this.updateInvestmentForm($planSelect.val());
            }
        },

        // Initialize transaction filters
        initTransactionFilters: function() {
            var $filters = $('.transaction-filter');
            if ($filters.length) {
                this.filterTransactions();
            }
        },

        // Initialize real-time updates
        initRealTimeUpdates: function() {
            if (this.config.isLoggedIn) {
                // Update stats every 30 seconds
                setInterval(function() {
                    BlackCnoteHYIPLab.updateUserStats();
                }, 30000);

                // Update transactions every minute
                setInterval(function() {
                    BlackCnoteHYIPLab.updateTransactions();
                }, 60000);
            }
        },

        // Handle investment form submission
        handleInvestmentSubmission: function($form) {
            var self = this;
            var $submitBtn = $form.find('.btn');
            var $result = $('#investment-result');

            // Validate form
            if (!this.validateInvestmentForm($form)) {
                return;
            }

            // Show loading state
            $submitBtn.prop('disabled', true).text('Processing...');
            $result.removeClass('success error').hide();

            // Prepare form data
            var formData = {
                action: 'hyiplab_create_investment',
                nonce: this.config.nonce,
                plan_id: $form.find('#plan-id').val(),
                amount: $form.find('#investment-amount').val(),
                payment_method: $form.find('#payment-method').val()
            };

            // Submit via AJAX
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $result.removeClass('error').addClass('success')
                              .html('<strong>Success!</strong> ' + response.data.message)
                              .show();
                        
                        // Reset form
                        $form[0].reset();
                        
                        // Update user stats
                        self.updateUserStats();
                        
                        // Show success animation
                        self.showSuccessAnimation();
                    } else {
                        $result.removeClass('success').addClass('error')
                              .html('<strong>Error!</strong> ' + response.data.message)
                              .show();
                    }
                },
                error: function() {
                    $result.removeClass('success').addClass('error')
                          .html('<strong>Error!</strong> Network error. Please try again.')
                          .show();
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text('Create Investment');
                }
            });
        },

        // Validate investment form
        validateInvestmentForm: function($form) {
            var isValid = true;
            var $result = $('#investment-result');

            // Clear previous errors
            $result.removeClass('success error').hide();

            // Check plan selection
            var planId = $form.find('#plan-id').val();
            if (!planId) {
                this.showFormError('Please select an investment plan.');
                isValid = false;
            }

            // Check amount
            var amount = parseFloat($form.find('#investment-amount').val());
            var minAmount = parseFloat($form.find('#investment-amount').attr('data-min'));
            var maxAmount = parseFloat($form.find('#investment-amount').attr('data-max'));

            if (!amount || amount < minAmount || amount > maxAmount) {
                this.showFormError('Please enter a valid amount between $' + minAmount + ' and $' + maxAmount + '.');
                isValid = false;
            }

            // Check payment method
            var paymentMethod = $form.find('#payment-method').val();
            if (!paymentMethod) {
                this.showFormError('Please select a payment method.');
                isValid = false;
            }

            return isValid;
        },

        // Show form error
        showFormError: function(message) {
            $('#investment-result').removeClass('success').addClass('error')
                                 .html('<strong>Error!</strong> ' + message)
                                 .show();
        },

        // Update investment form based on selected plan
        updateInvestmentForm: function(planId) {
            if (!planId) return;

            var self = this;
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hyiplab_get_plan_details',
                    nonce: this.config.nonce,
                    plan_id: planId
                },
                success: function(response) {
                    if (response.success) {
                        var plan = response.data;
                        
                        // Update form fields
                        $('#plan-id').val(plan.id);
                        $('#investment-amount').attr({
                            'data-min': plan.min_investment,
                            'data-max': plan.max_investment
                        });
                        
                        // Update selected plan display
                        $('.selected-plan h4').text(plan.name);
                        $('.selected-plan p').text(plan.description);
                        $('.selected-plan .plan-details').html(
                            '<span>Min: $' + plan.min_investment + '</span>' +
                            '<span>Max: $' + plan.max_investment + '</span>' +
                            '<span>Return: ' + plan.return_rate + '%</span>' +
                            '<span>Duration: ' + plan.duration_days + ' days</span>'
                        );
                        
                        // Update amount range display
                        $('.amount-range').text('Range: $' + plan.min_investment + ' - $' + plan.max_investment);
                        
                        // Recalculate returns
                        self.calculateInvestmentReturns();
                        
                        // Show form
                        $('.hyiplab-invest-form').addClass('hyiplab-fade-in');
                    }
                }
            });
        },

        // Calculate investment returns
        calculateInvestmentReturns: function() {
            var $amount = $('#investment-amount');
            var $planSelect = $('.hyiplab-plan-select');
            
            if (!$amount.length || !$planSelect.length) return;

            var amount = parseFloat($amount.val()) || 0;
            var planId = $planSelect.val();

            if (amount > 0 && planId) {
                $.ajax({
                    url: this.config.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hyiplab_calculate_returns',
                        nonce: this.config.nonce,
                        plan_id: planId,
                        amount: amount
                    },
                    success: function(response) {
                        if (response.success) {
                            var calculation = response.data;
                            
                            // Update calculation display
                            $('.calculation-results').html(
                                '<div class="calculation-item">' +
                                    '<span class="label">Investment:</span>' +
                                    '<span class="value">$' + calculation.investment.toFixed(2) + '</span>' +
                                '</div>' +
                                '<div class="calculation-item">' +
                                    '<span class="label">Return Rate:</span>' +
                                    '<span class="value">' + calculation.return_rate + '%</span>' +
                                '</div>' +
                                '<div class="calculation-item">' +
                                    '<span class="label">Duration:</span>' +
                                    '<span class="value">' + calculation.duration_days + ' days</span>' +
                                '</div>' +
                                '<div class="calculation-item total">' +
                                    '<span class="label">Total Return:</span>' +
                                    '<span class="value">$' + calculation.total_return.toFixed(2) + '</span>' +
                                '</div>' +
                                '<div class="calculation-item profit">' +
                                    '<span class="label">Profit:</span>' +
                                    '<span class="value">$' + calculation.profit.toFixed(2) + '</span>' +
                                '</div>'
                            );
                        }
                    }
                });
            }
        },

        // Filter transactions
        filterTransactions: function() {
            var type = $('.transaction-filter[data-filter="type"]').val();
            var status = $('.transaction-filter[data-filter="status"]').val();
            var dateRange = $('.transaction-filter[data-filter="date"]').val();

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hyiplab_filter_transactions',
                    nonce: this.config.nonce,
                    type: type,
                    status: status,
                    date_range: dateRange
                },
                success: function(response) {
                    if (response.success) {
                        $('.transactions-list').html(response.data.html);
                        $('.transactions-list').addClass('hyiplab-fade-in');
                    }
                }
            });
        },

        // Update user statistics
        updateUserStats: function() {
            if (!this.config.isLoggedIn) return;

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hyiplab_get_user_stats',
                    nonce: this.config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var stats = response.data;
                        
                        // Update dashboard stats
                        $('.stat-card[data-stat="total_invested"] .stat-value').text('$' + stats.total_invested.toFixed(2));
                        $('.stat-card[data-stat="total_earned"] .stat-value').text('$' + stats.total_earned.toFixed(2));
                        $('.stat-card[data-stat="active_investments"] .stat-value').text(stats.active_investments);
                        $('.stat-card[data-stat="total_transactions"] .stat-value').text(stats.total_transactions);
                        
                        // Update widget stats
                        $('.hyiplab-stats-widget .stat-value[data-stat="balance"]').text('$' + stats.balance.toFixed(2));
                        $('.hyiplab-stats-widget .stat-value[data-stat="profit"]').text('$' + stats.total_earned.toFixed(2));
                    }
                }
            });
        },

        // Update transactions
        updateTransactions: function() {
            if (!this.config.isLoggedIn) return;

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hyiplab_get_recent_transactions',
                    nonce: this.config.nonce,
                    limit: 10
                },
                success: function(response) {
                    if (response.success) {
                        $('.hyiplab-transactions-widget .hyiplab-transactions-widget').html(response.data.html);
                    }
                }
            });
        },

        // Refresh widget
        refreshWidget: function($widget) {
            var widgetType = $widget.data('widget-type');
            var $refreshBtn = $widget.find('.refresh-widget');
            
            $refreshBtn.addClass('loading');
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hyiplab_refresh_widget',
                    nonce: this.config.nonce,
                    widget_type: widgetType
                },
                success: function(response) {
                    if (response.success) {
                        $widget.find('.widget-content').html(response.data.html);
                        $widget.addClass('hyiplab-fade-in');
                    }
                },
                complete: function() {
                    $refreshBtn.removeClass('loading');
                }
            });
        },

        // Toggle demo mode
        toggleDemoMode: function() {
            var $demoContent = $('.hyiplab-demo-content');
            var $realContent = $('.hyiplab-real-content');
            
            if ($demoContent.is(':visible')) {
                $demoContent.hide();
                $realContent.show();
                $('.demo-mode-toggle').text('Show Demo Content');
            } else {
                $realContent.hide();
                $demoContent.show();
                $('.demo-mode-toggle').text('Show Real Content');
            }
        },

        // Show success animation
        showSuccessAnimation: function() {
            var $successIcon = $('<div class="success-animation">✓</div>');
            $('body').append($successIcon);
            
            setTimeout(function() {
                $successIcon.remove();
            }, 2000);
        },

        // Format currency
        formatCurrency: function(amount) {
            return '$' + parseFloat(amount).toFixed(2);
        },

        // Format percentage
        formatPercentage: function(value) {
            return parseFloat(value).toFixed(2) + '%';
        },

        // Format date
        formatDate: function(dateString) {
            var date = new Date(dateString);
            return date.toLocaleDateString();
        },

        // Show loading state
        showLoading: function($element) {
            $element.addClass('hyiplab-loading');
        },

        // Hide loading state
        hideLoading: function($element) {
            $element.removeClass('hyiplab-loading');
        },

        // Show notification
        showNotification: function(message, type) {
            var $notification = $('<div class="hyiplab-notification ' + type + '">' + message + '</div>');
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.addClass('show');
            }, 100);
            
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 3000);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        BlackCnoteHYIPLab.init();
    });

    // Add CSS for animations and notifications
    var style = document.createElement('style');
    style.textContent = `
        .success-animation {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #4CAF50;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            z-index: 9999;
            animation: successPulse 2s ease-out;
        }
        
        @keyframes successPulse {
            0% { transform: translate(-50%, -50%) scale(0); opacity: 0; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
            100% { transform: translate(-50%, -50%) scale(1); opacity: 0; }
        }
        
        .hyiplab-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 15px 20px;
            border-radius: 6px;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        
        .hyiplab-notification.show {
            transform: translateX(0);
        }
        
        .hyiplab-notification.success {
            background: #4CAF50;
        }
        
        .hyiplab-notification.error {
            background: #f44336;
        }
        
        .hyiplab-notification.warning {
            background: #FF9800;
        }
        
        .refresh-widget {
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .refresh-widget:hover {
            background: #e9ecef;
        }
        
        .refresh-widget.loading {
            animation: spin 1s linear infinite;
        }
        
        .calculation-results {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .calculation-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .calculation-item:last-child {
            margin-bottom: 0;
        }
        
        .calculation-item.total,
        .calculation-item.profit {
            border-top: 1px solid #e9ecef;
            padding-top: 8px;
            font-weight: 600;
        }
        
        .calculation-item.profit .value {
            color: #4CAF50;
        }
    `;
    document.head.appendChild(style);

})(jQuery); 