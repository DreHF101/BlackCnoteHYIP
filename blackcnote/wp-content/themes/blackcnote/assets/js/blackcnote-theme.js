/**
 * BlackCnote Theme JavaScript
 * Enhanced JavaScript file for the BlackCnote investment platform theme
 * Version: 2.0 - Enhanced Performance, Accessibility & Interactive Features
 */

(function($) {
    'use strict';

    // Theme namespace
    window.BlackCnote = window.BlackCnote || {};

    // Enhanced Configuration
    const config = {
        ajaxUrl: blackcnoteTheme.ajaxUrl || '/wp-admin/admin-ajax.php',
        nonce: blackcnoteTheme.nonce || '',
        strings: blackcnoteTheme.strings || {},
        apiUrl: blackcnoteTheme.apiUrl || '/wp-json/blackcnote/v1/',
        liveEditing: blackcnoteTheme.liveEditing || false,
        debug: blackcnoteTheme.debug || false,
        performance: {
            debounceDelay: 300,
            throttleDelay: 100,
            animationDuration: 300,
            refreshInterval: 30000
        },
        accessibility: {
            focusVisible: true,
            reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
            highContrast: window.matchMedia('(prefers-contrast: high)').matches
        }
    };

    // Enhanced Utility functions
    const utils = {
        // Performance monitoring
        performance: {
            start: function(label) {
                if (config.debug && window.performance) {
                    window.performance.mark(`${label}-start`);
                }
            },
            end: function(label) {
                if (config.debug && window.performance) {
                    window.performance.mark(`${label}-end`);
                    window.performance.measure(label, `${label}-start`, `${label}-end`);
                    const measure = window.performance.getEntriesByName(label)[0];
                    console.log(`${label}: ${measure.duration.toFixed(2)}ms`);
                }
            }
        },

        // Enhanced message system
        showMessage: function(message, type = 'success', duration = 5000, options = {}) {
            const messageId = 'msg-' + Date.now();
            const messageHtml = `
                <div id="${messageId}" class="alert alert-${type} animate-slide-in-up" role="alert" aria-live="polite">
                    <div class="alert-content">
                        <span class="alert-message">${message}</span>
                        <button type="button" class="alert-close" aria-label="Close message" onclick="this.parentElement.parentElement.remove()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            `;
            
            // Remove existing messages of same type
            $(`.alert-${type}`).remove();
            
            $('body').append(messageHtml);
            
            // Auto-remove after duration
            if (duration > 0) {
                setTimeout(() => {
                    $(`#${messageId}`).fadeOut(300, function() {
                        $(this).remove();
                    });
                }, duration);
            }

            // Announce to screen readers
            if (options.announce !== false) {
                this.announceToScreenReader(message);
            }
        },

        // Screen reader announcements
        announceToScreenReader: function(message) {
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'sr-only';
            announcement.textContent = message;
            document.body.appendChild(announcement);
            
            setTimeout(() => {
                document.body.removeChild(announcement);
            }, 1000);
        },

        // Enhanced loading states
        showLoading: function(element, message = 'Loading...') {
            const loadingId = 'loading-' + Date.now();
            const loadingHtml = `
                <div id="${loadingId}" class="loading-overlay" role="status" aria-live="polite">
                    <div class="spinner-container">
                        <div class="spinner"></div>
                        <p class="loading-message">${message}</p>
                    </div>
                </div>
            `;
            
            $(element).addClass('loading');
            $(element).append(loadingHtml);
            
            return loadingId;
        },

        hideLoading: function(element, loadingId = null) {
            $(element).removeClass('loading');
            if (loadingId) {
                $(`#${loadingId}`).remove();
            } else {
                $(element).find('.loading-overlay').remove();
            }
        },

        // Enhanced currency formatting
        formatCurrency: function(amount, currency = 'USD', locale = 'en-US') {
            try {
                return new Intl.NumberFormat(locale, {
                    style: 'currency',
                    currency: currency,
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            } catch (error) {
                console.warn('Currency formatting failed:', error);
                return `${currency} ${amount.toFixed(2)}`;
            }
        },

        // Enhanced number formatting
        formatNumber: function(number, locale = 'en-US', options = {}) {
            try {
                return new Intl.NumberFormat(locale, {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2,
                    ...options
                }).format(number);
            } catch (error) {
                console.warn('Number formatting failed:', error);
                return number.toFixed(2);
            }
        },

        // Enhanced date formatting
        formatDate: function(date, locale = 'en-US', options = {}) {
            try {
                const defaultOptions = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                
                return new Intl.DateTimeFormat(locale, {
                    ...defaultOptions,
                    ...options
                }).format(new Date(date));
            } catch (error) {
                console.warn('Date formatting failed:', error);
                return new Date(date).toLocaleString();
            }
        },

        // Enhanced debounce with immediate option
        debounce: function(func, wait, immediate = false) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    if (!immediate) func(...args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func(...args);
            };
        },

        // Enhanced throttle
        throttle: function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Error handling
        handleError: function(error, context = '') {
            console.error(`BlackCnote Error [${context}]:`, error);
            
            if (config.debug) {
                this.showMessage(`Error: ${error.message}`, 'error', 10000);
            } else {
                this.showMessage('An error occurred. Please try again.', 'error', 5000);
            }
        },

        // Accessibility helpers
        accessibility: {
            // Focus management
            trapFocus: function(element) {
                const focusableElements = element.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];

                element.addEventListener('keydown', function(e) {
                    if (e.key === 'Tab') {
                        if (e.shiftKey) {
                            if (document.activeElement === firstElement) {
                                e.preventDefault();
                                lastElement.focus();
                            }
                        } else {
                            if (document.activeElement === lastElement) {
                                e.preventDefault();
                                firstElement.focus();
                            }
                        }
                    }
                });
            },

            // Skip to content
            setupSkipLinks: function() {
                const skipLink = document.createElement('a');
                skipLink.href = '#main-content';
                skipLink.textContent = 'Skip to main content';
                skipLink.className = 'skip-link sr-only sr-only-focusable';
                document.body.insertBefore(skipLink, document.body.firstChild);
            },

            // Enhanced focus styles
            setupFocusStyles: function() {
                if (config.accessibility.focusVisible) {
                    document.documentElement.classList.add('focus-visible');
                }
            }
        },

        // Performance helpers
        performance: {
            // Lazy loading
            lazyLoad: function(selector, callback, options = {}) {
                const defaultOptions = {
                    root: null,
                    rootMargin: '50px',
                    threshold: 0.1
                };
                
                const observerOptions = { ...defaultOptions, ...options };
                
                if ('IntersectionObserver' in window) {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                callback(entry.target);
                                observer.unobserve(entry.target);
                            }
                        });
                    }, observerOptions);
                    
                    document.querySelectorAll(selector).forEach(el => {
                        observer.observe(el);
                    });
                } else {
                    // Fallback for older browsers
                    document.querySelectorAll(selector).forEach(callback);
                }
            },

            // Image optimization
            optimizeImages: function() {
                const images = document.querySelectorAll('img[data-src]');
                this.lazyLoad('img[data-src]', (img) => {
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    img.classList.add('loaded');
                });
            },

            // Preload critical resources
            preloadResources: function() {
                const criticalResources = [
                    '/wp-content/themes/blackcnote/assets/css/blackcnote-theme.css',
                    '/wp-content/themes/blackcnote/assets/js/blackcnote-theme.js'
                ];
                
                criticalResources.forEach(resource => {
                    const link = document.createElement('link');
                    link.rel = 'preload';
                    link.href = resource;
                    link.as = resource.endsWith('.css') ? 'style' : 'script';
                    document.head.appendChild(link);
                });
            }
        },

        // Animation helpers
        animation: {
            // Intersection Observer for animations
            setupScrollAnimations: function() {
                if (config.accessibility.reducedMotion) return;
                
                const animatedElements = document.querySelectorAll('.animate-on-scroll');
                
                this.lazyLoad('.animate-on-scroll', (element) => {
                    element.classList.add('animate-fade-in-up');
                }, { threshold: 0.1 });
            },

            // Smooth scrolling
            smoothScroll: function(target, duration = 500) {
                if (config.accessibility.reducedMotion) {
                    target.scrollIntoView();
                    return;
                }
                
                const targetPosition = target.offsetTop;
                const startPosition = window.pageYOffset;
                const distance = targetPosition - startPosition;
                let startTime = null;

                function animation(currentTime) {
                    if (startTime === null) startTime = currentTime;
                    const timeElapsed = currentTime - startTime;
                    const run = ease(timeElapsed, startPosition, distance, duration);
                    window.scrollTo(0, run);
                    if (timeElapsed < duration) requestAnimationFrame(animation);
                }

                function ease(t, b, c, d) {
                    t /= d / 2;
                    if (t < 1) return c / 2 * t * t + b;
                    t--;
                    return -c / 2 * (t * (t - 2) - 1) + b;
                }

                requestAnimationFrame(animation);
            }
        }
    };

    // Enhanced Mobile menu functionality
    const mobileMenu = {
        init: function() {
            this.bindEvents();
            this.setupAccessibility();
        },

        bindEvents: function() {
            $('.mobile-menu-toggle').on('click', function(e) {
                e.preventDefault();
                mobileMenu.toggle();
            });

            // Close menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.nav-menu, .mobile-menu-toggle').length) {
                    mobileMenu.close();
                }
            });

            // Close menu when clicking on a link
            $('.nav-link').on('click', function() {
                mobileMenu.close();
            });

            // Keyboard navigation
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    mobileMenu.close();
                }
            });
        },

        setupAccessibility: function() {
            const toggle = $('.mobile-menu-toggle');
            const menu = $('.nav-list');
            
            toggle.attr('aria-expanded', 'false');
            toggle.attr('aria-controls', 'mobile-menu');
            menu.attr('id', 'mobile-menu');
            menu.attr('aria-label', 'Main navigation');
        },

        toggle: function() {
            const toggle = $('.mobile-menu-toggle');
            const menu = $('.nav-list');
            const isOpen = menu.hasClass('active');
            
            if (isOpen) {
                this.close();
            } else {
                this.open();
            }
        },

        open: function() {
            const toggle = $('.mobile-menu-toggle');
            const menu = $('.nav-list');
            
            toggle.addClass('active').attr('aria-expanded', 'true');
            menu.addClass('active');
            
            // Trap focus in mobile menu
            utils.accessibility.trapFocus(menu[0]);
            
            // Announce to screen readers
            utils.announceToScreenReader('Mobile menu opened');
        },

        close: function() {
            const toggle = $('.mobile-menu-toggle');
            const menu = $('.nav-list');
            
            toggle.removeClass('active').attr('aria-expanded', 'false');
            menu.removeClass('active');
            
            // Return focus to toggle button
            toggle.focus();
            
            // Announce to screen readers
            utils.announceToScreenReader('Mobile menu closed');
        }
    };

    // Investment calculator functionality
    const calculator = {
        init: function() {
            this.bindEvents();
            this.calculate();
        },

        bindEvents: function() {
            $('#calculate-btn').on('click', function(e) {
                e.preventDefault();
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
            $('#initial-investment').text(utils.formatCurrency(amount));
            $('#daily-return').text(utils.formatCurrency(dailyReturn));
            $('#total-return').text(utils.formatCurrency(totalReturn));
            $('#final-amount').text(utils.formatCurrency(finalAmount));
            $('#profit').text(utils.formatCurrency(profit));
            $('#roi').text(roi.toFixed(1) + '%');

            $('#calculator-results').fadeIn(300);
        }
    };

    // Portfolio tracking functionality
    const portfolio = {
        init: function() {
            this.loadPortfolioData();
            this.bindEvents();
        },

        bindEvents: function() {
            $('#refresh-portfolio').on('click', function() {
                portfolio.loadPortfolioData();
            });

            // Auto-refresh every 30 seconds
            setInterval(() => {
                portfolio.loadPortfolioData();
            }, 30000);
        },

        loadPortfolioData: function() {
            if (!config.liveEditing) return;

            $.ajax({
                url: config.apiUrl + 'portfolio',
                type: 'GET',
                headers: {
                    'X-WP-Nonce': config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        portfolio.updateDisplay(response.data);
                    }
                },
                error: function() {
                    console.log('Failed to load portfolio data');
                }
            });
        },

        updateDisplay: function(data) {
            $('#total-invested').text(utils.formatCurrency(data.totalInvested));
            $('#total-earned').text(utils.formatCurrency(data.totalEarned));
            $('#current-balance').text(utils.formatCurrency(data.currentBalance));
            $('#active-investments').text(data.activeInvestments);
            $('#total-investments').text(data.totalInvestments);

            // Update activity list
            const activityList = $('#activity-list');
            activityList.empty();

            data.recentActivity.forEach(activity => {
                const activityHtml = `
                    <div class="activity-item fade-in">
                        <div class="activity-icon">${activity.icon}</div>
                        <div class="activity-details">
                            <div class="activity-title">${activity.title}</div>
                            <div class="activity-amount">${utils.formatCurrency(activity.amount)}</div>
                            <div class="activity-time">${utils.formatDate(activity.timestamp)}</div>
                        </div>
                    </div>
                `;
                activityList.append(activityHtml);
            });
        }
    };

    // Investment plans functionality
    const investmentPlans = {
        init: function() {
            this.bindEvents();
            this.loadPlans();
        },

        bindEvents: function() {
            $('.plan-card').on('click', function() {
                const planId = $(this).data('plan-id');
                investmentPlans.selectPlan(planId);
            });

            $('#invest-btn').on('click', function(e) {
                e.preventDefault();
                investmentPlans.processInvestment();
            });
        },

        loadPlans: function() {
            if (!config.liveEditing) return;

            $.ajax({
                url: config.apiUrl + 'plans',
                type: 'GET',
                headers: {
                    'X-WP-Nonce': config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        investmentPlans.updatePlansDisplay(response.data);
                    }
                }
            });
        },

        updatePlansDisplay: function(plans) {
            const plansContainer = $('.plans-grid');
            plansContainer.empty();

            plans.forEach(plan => {
                const planHtml = `
                    <div class="plan-card ${plan.featured ? 'featured' : ''}" data-plan-id="${plan.id}">
                        ${plan.featured ? '<div class="plan-badge">Featured</div>' : ''}
                        <div class="plan-header">
                            <div class="plan-icon">${plan.icon}</div>
                            <div class="plan-price">
                                <span class="currency">$</span>${plan.minAmount}
                                <span class="period">/min</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li>${plan.dailyReturn}% Daily Return</li>
                                <li>${plan.duration} Days Duration</li>
                                <li>${plan.minWithdrawal} Minimum Withdrawal</li>
                                <li>24/7 Support</li>
                            </ul>
                        </div>
                        <div class="plan-actions">
                            <button class="btn btn-primary">Select Plan</button>
                        </div>
                    </div>
                `;
                plansContainer.append(planHtml);
            });
        },

        selectPlan: function(planId) {
            $('.plan-card').removeClass('selected');
            $(`.plan-card[data-plan-id="${planId}"]`).addClass('selected');
            
            $('#selected-plan').val(planId);
            $('#investment-form').fadeIn(300);
        },

        processInvestment: function() {
            const amount = parseFloat($('#investment-amount').val());
            const planId = $('#selected-plan').val();

            if (!amount || !planId) {
                utils.showMessage('Please fill in all required fields', 'error');
                return;
            }

            utils.showLoading('#invest-btn');

            $.ajax({
                url: config.apiUrl + 'invest',
                type: 'POST',
                data: {
                    amount: amount,
                    plan_id: planId,
                    nonce: config.nonce
                },
                success: function(response) {
                    utils.hideLoading('#invest-btn');
                    if (response.success) {
                        utils.showMessage('Investment created successfully!', 'success');
                        $('#investment-form')[0].reset();
                        $('#investment-form').fadeOut(300);
                        portfolio.loadPortfolioData();
                    } else {
                        utils.showMessage(response.data || 'Error creating investment', 'error');
                    }
                },
                error: function() {
                    utils.hideLoading('#invest-btn');
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        }
    };

    // Transactions functionality
    const transactions = {
        init: function() {
            this.loadTransactions();
            this.bindEvents();
        },

        bindEvents: function() {
            $('#filter-transactions').on('change', function() {
                transactions.filterTransactions($(this).val());
            });

            $('#search-transactions').on('input', utils.debounce(function() {
                transactions.searchTransactions($(this).val());
            }, 300));
        },

        loadTransactions: function() {
            if (!config.liveEditing) return;

            $.ajax({
                url: config.apiUrl + 'transactions',
                type: 'GET',
                headers: {
                    'X-WP-Nonce': config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        transactions.updateTransactionsTable(response.data);
                    }
                }
            });
        },

        updateTransactionsTable: function(transactions) {
            const tbody = $('#transactions-table tbody');
            tbody.empty();

            transactions.forEach(transaction => {
                const rowHtml = `
                    <tr class="fade-in">
                        <td>${transaction.id}</td>
                        <td>${transaction.type}</td>
                        <td>${utils.formatCurrency(transaction.amount)}</td>
                        <td><span class="status ${transaction.status}">${transaction.status}</span></td>
                        <td>${utils.formatDate(transaction.created_at)}</td>
                        <td>
                            <button class="btn btn-small btn-secondary view-details" data-id="${transaction.id}">
                                View Details
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(rowHtml);
            });
        },

        filterTransactions: function(status) {
            if (status === 'all') {
                $('#transactions-table tbody tr').show();
            } else {
                $('#transactions-table tbody tr').hide();
                $(`#transactions-table tbody tr .status.${status}`).closest('tr').show();
            }
        },

        searchTransactions: function(query) {
            const rows = $('#transactions-table tbody tr');
            rows.each(function() {
                const text = $(this).text().toLowerCase();
                if (text.includes(query.toLowerCase())) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    };

    // Live editing functionality
    const liveEditing = {
        init: function() {
            if (!config.liveEditing) return;
            
            this.bindEvents();
            this.startFileWatching();
        },

        bindEvents: function() {
            // Enable live editing on content areas
            $('[data-live-edit]').on('click', function() {
                liveEditing.enableEditing($(this));
            });

            // Save changes on blur
            $(document).on('blur', '[contenteditable="true"]', function() {
                liveEditing.saveChanges($(this));
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    liveEditing.saveAllChanges();
                }
            });
        },

        enableEditing: function(element) {
            element.attr('contenteditable', 'true');
            element.addClass('editing');
            element.focus();
        },

        saveChanges: function(element) {
            const content = element.html();
            const field = element.data('field');
            const postId = element.data('post-id');

            $.ajax({
                url: config.apiUrl + 'content',
                type: 'POST',
                data: {
                    content: content,
                    field: field,
                    post_id: postId,
                    nonce: config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        element.removeClass('editing');
                        element.attr('contenteditable', 'false');
                        utils.showMessage('Changes saved successfully', 'success');
                    } else {
                        utils.showMessage('Error saving changes', 'error');
                    }
                },
                error: function() {
                    utils.showMessage('Network error occurred', 'error');
                }
            });
        },

        saveAllChanges: function() {
            $('[contenteditable="true"]').each(function() {
                liveEditing.saveChanges($(this));
            });
        },

        startFileWatching: function() {
            // This would typically connect to a WebSocket or use Server-Sent Events
            // For now, we'll simulate with polling
            setInterval(() => {
                liveEditing.checkForUpdates();
            }, 5000);
        },

        checkForUpdates: function() {
            $.ajax({
                url: config.apiUrl + 'updates',
                type: 'GET',
                headers: {
                    'X-WP-Nonce': config.nonce
                },
                success: function(response) {
                    if (response.success && response.data.updates) {
                        liveEditing.applyUpdates(response.data.updates);
                    }
                }
            });
        },

        applyUpdates: function(updates) {
            updates.forEach(update => {
                const element = $(`[data-field="${update.field}"]`);
                if (element.length) {
                    element.html(update.content);
                    element.addClass('updated');
                    setTimeout(() => {
                        element.removeClass('updated');
                    }, 2000);
                }
            });
        }
    };

    // Smooth scrolling
    const smoothScroll = {
        init: function() {
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                }
            });
        }
    };

    // Form validation
    const formValidation = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $('form').on('submit', function(e) {
                if (!formValidation.validateForm($(this))) {
                    e.preventDefault();
                }
            });

            $('input, textarea, select').on('blur', function() {
                formValidation.validateField($(this));
            });
        },

        validateForm: function(form) {
            let isValid = true;
            form.find('input[required], textarea[required], select[required]').each(function() {
                if (!formValidation.validateField($(this))) {
                    isValid = false;
                }
            });
            return isValid;
        },

        validateField: function(field) {
            const value = field.val().trim();
            const type = field.attr('type');
            const required = field.prop('required');

            field.removeClass('error');
            field.next('.error-message').remove();

            if (required && !value) {
                formValidation.showError(field, 'This field is required');
                return false;
            }

            if (value) {
                switch (type) {
                    case 'email':
                        if (!utils.isValidEmail(value)) {
                            formValidation.showError(field, 'Please enter a valid email address');
                            return false;
                        }
                        break;
                    case 'tel':
                        if (!utils.isValidPhone(value)) {
                            formValidation.showError(field, 'Please enter a valid phone number');
                            return false;
                        }
                        break;
                    case 'number':
                        if (isNaN(value) || value < 0) {
                            formValidation.showError(field, 'Please enter a valid number');
                            return false;
                        }
                        break;
                }
            }

            return true;
        },

        showError: function(field, message) {
            field.addClass('error');
            field.after(`<div class="error-message">${message}</div>`);
        }
    };

    // Add utility functions
    utils.isValidEmail = function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    };

    utils.isValidPhone = function(phone) {
        const re = /^[\+]?[1-9][\d]{0,15}$/;
        return re.test(phone.replace(/[\s\-\(\)]/g, ''));
    };

    // Enhanced Performance & Accessibility Features
    const enhancedFeatures = {
        init: function() {
            this.setupAccessibility();
            this.setupPerformance();
            this.setupAnimations();
            this.setupErrorHandling();
            this.setupAnalytics();
        },

        setupAccessibility: function() {
            // Skip links
            utils.accessibility.setupSkipLinks();
            
            // Focus styles
            utils.accessibility.setupFocusStyles();
            
            // ARIA labels and roles
            this.setupARIA();
            
            // Keyboard navigation
            this.setupKeyboardNavigation();
        },

        setupARIA: function() {
            // Add ARIA labels to interactive elements
            $('.btn').each(function() {
                if (!$(this).attr('aria-label')) {
                    const text = $(this).text().trim();
                    if (text) {
                        $(this).attr('aria-label', text);
                    }
                }
            });

            // Add roles to semantic elements
            $('.card').attr('role', 'article');
            $('.nav-list').attr('role', 'navigation');
            $('.table').attr('role', 'table');
            $('.form').attr('role', 'form');
        },

        setupKeyboardNavigation: function() {
            // Enhanced keyboard navigation for cards
            $('.card, .plan-card').attr('tabindex', '0').on('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    $(this).click();
                }
            });

            // Keyboard navigation for tables
            $('.table tbody tr').attr('tabindex', '0').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $(this).find('.view-details').click();
                }
            });
        },

        setupPerformance: function() {
            // Preload critical resources
            utils.performance.preloadResources();
            
            // Optimize images
            utils.performance.optimizeImages();
            
            // Lazy load non-critical content
            this.setupLazyLoading();
            
            // Service Worker registration (if available)
            this.registerServiceWorker();
        },

        setupLazyLoading: function() {
            // Lazy load images
            utils.performance.lazyLoad('img[data-src]', (img) => {
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                img.classList.add('loaded');
            });

            // Lazy load sections
            utils.performance.lazyLoad('.lazy-section', (section) => {
                section.classList.add('loaded');
                $(section).trigger('lazy-loaded');
            });
        },

        registerServiceWorker: function() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registered:', registration);
                    })
                    .catch(error => {
                        console.log('ServiceWorker registration failed:', error);
                    });
            }
        },

        setupAnimations: function() {
            if (config.accessibility.reducedMotion) return;
            
            // Scroll-triggered animations
            utils.animation.setupScrollAnimations();
            
            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    e.preventDefault();
                    utils.animation.smoothScroll(target[0]);
                }
            });

            // Stagger animations for lists
            $('.stagger-animation').each(function() {
                const items = $(this).children();
                items.each(function(index) {
                    $(this).css('animation-delay', `${index * 0.1}s`);
                });
            });
        },

        setupErrorHandling: function() {
            // Global error handler
            window.addEventListener('error', function(e) {
                utils.handleError(e.error, 'Global');
            });

            // Unhandled promise rejection handler
            window.addEventListener('unhandledrejection', function(e) {
                utils.handleError(new Error(e.reason), 'Promise');
            });

            // AJAX error handler
            $(document).ajaxError(function(event, xhr, settings, error) {
                utils.handleError(error, `AJAX: ${settings.url}`);
            });
        },

        setupAnalytics: function() {
            // Performance monitoring
            if ('performance' in window) {
                window.addEventListener('load', function() {
                    const perfData = performance.getEntriesByType('navigation')[0];
                    console.log('Page Load Time:', perfData.loadEventEnd - perfData.loadEventStart);
                });
            }

            // User interaction tracking
            this.trackUserInteractions();
        },

        trackUserInteractions: function() {
            // Track button clicks
            $('.btn').on('click', function() {
                const buttonText = $(this).text().trim();
                const buttonClass = $(this).attr('class');
                console.log('Button clicked:', { text: buttonText, class: buttonClass });
            });

            // Track form submissions
            $('form').on('submit', function() {
                const formId = $(this).attr('id') || 'unknown';
                console.log('Form submitted:', formId);
            });
        }
    };

    // Enhanced Notifications System
    const notifications = {
        init: function() {
            this.createNotificationContainer();
            this.setupNotificationQueue();
        },

        createNotificationContainer: function() {
            if (!$('#notification-container').length) {
                $('body').append('<div id="notification-container" aria-live="polite"></div>');
            }
        },

        setupNotificationQueue: function() {
            this.queue = [];
            this.isProcessing = false;
        },

        show: function(message, type = 'info', duration = 5000, options = {}) {
            const notification = {
                id: 'notification-' + Date.now(),
                message,
                type,
                duration,
                options
            };

            this.queue.push(notification);
            this.processQueue();
        },

        processQueue: function() {
            if (this.isProcessing || this.queue.length === 0) return;

            this.isProcessing = true;
            const notification = this.queue.shift();
            this.displayNotification(notification);
        },

        displayNotification: function(notification) {
            const html = `
                <div id="${notification.id}" class="notification notification-${notification.type} animate-slide-in-up" role="alert">
                    <div class="notification-content">
                        <span class="notification-message">${notification.message}</span>
                        <button type="button" class="notification-close" aria-label="Close notification" onclick="notifications.close('${notification.id}')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            `;

            $('#notification-container').append(html);

            // Auto-remove
            if (notification.duration > 0) {
                setTimeout(() => {
                    this.close(notification.id);
                }, notification.duration);
            }

            // Process next notification
            setTimeout(() => {
                this.isProcessing = false;
                this.processQueue();
            }, 300);
        },

        close: function(id) {
            $(`#${id}`).fadeOut(300, function() {
                $(this).remove();
            });
        }
    };

    // Enhanced Theme Initialization
    const theme = {
        init: function() {
            utils.performance.start('theme-init');
            
            // Initialize all components
            mobileMenu.init();
            calculator.init();
            portfolio.init();
            transactions.init();
            liveEditing.init();
            enhancedFeatures.init();
            formValidation.init();
            notifications.init();

            // Setup global event handlers
            this.setupGlobalHandlers();
            
            // Announce theme ready
            utils.announceToScreenReader('BlackCnote theme loaded successfully');
            
            utils.performance.end('theme-init');
        },

        setupGlobalHandlers: function() {
            // Handle window resize
            $(window).on('resize', utils.throttle(function() {
                theme.handleResize();
            }, 250));

            // Handle scroll events
            $(window).on('scroll', utils.throttle(function() {
                theme.handleScroll();
            }, 100));

            // Handle visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    theme.handlePageHidden();
                } else {
                    theme.handlePageVisible();
                }
            });
        },

        handleResize: function() {
            // Update mobile menu state
            if ($(window).width() > 768) {
                mobileMenu.close();
            }
        },

        handleScroll: function() {
            // Show/hide back to top button
            const scrollTop = $(window).scrollTop();
            if (scrollTop > 300) {
                $('.back-to-top').addClass('visible');
            } else {
                $('.back-to-top').removeClass('visible');
            }
        },

        handlePageHidden: function() {
            // Pause animations and updates when page is hidden
            $('.animate-pulse').css('animation-play-state', 'paused');
        },

        handlePageVisible: function() {
            // Resume animations when page becomes visible
            $('.animate-pulse').css('animation-play-state', 'running');
        }
    };

    // Initialize theme when DOM is ready
    $(document).ready(function() {
        theme.init();
    });

    // Initialize theme when window loads
    $(window).on('load', function() {
        // Additional initialization after all resources are loaded
        utils.showMessage('BlackCnote theme fully loaded!', 'success', 3000);
    });

    // Expose utilities globally for debugging
    if (config.debug) {
        window.BlackCnoteUtils = utils;
        window.BlackCnoteConfig = config;
    }

    // Enhanced Class Toggling Support
    const classToggler = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Toggle classes on click
            $('[data-toggle-class]').on('click', function() {
                const target = $($(this).data('toggle-class'));
                const className = $(this).data('class') || 'active';
                target.toggleClass(className);
            });

            // Toggle classes on hover
            $('[data-toggle-class-hover]').on('mouseenter mouseleave', function() {
                const target = $($(this).data('toggle-class-hover'));
                const className = $(this).data('class') || 'hover';
                target.toggleClass(className);
            });

            // Toggle classes on focus
            $('[data-toggle-class-focus]').on('focus blur', function() {
                const target = $($(this).data('toggle-class-focus'));
                const className = $(this).data('class') || 'focused';
                target.toggleClass(className);
            });
        },

        toggle: function(element, className) {
            $(element).toggleClass(className);
        },

        add: function(element, className) {
            $(element).addClass(className);
        },

        remove: function(element, className) {
            $(element).removeClass(className);
        },

        has: function(element, className) {
            return $(element).hasClass(className);
        }
    };

    // Enhanced Slide Animations
    const slideAnimations = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Slide in elements on scroll
            $('.slide-in-on-scroll').each(function() {
                const element = $(this);
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            element.addClass('slide-in-left');
                            observer.unobserve(element[0]);
                        }
                    });
                });
                observer.observe(element[0]);
            });

            // Slide toggle elements
            $('[data-slide-toggle]').on('click', function() {
                const target = $($(this).data('slide-toggle'));
                slideAnimations.slideToggle(target);
            });
        },

        slideIn: function(element, direction = 'left', duration = 300) {
            const $element = $(element);
            const startTransform = direction === 'left' ? 'translateX(-100%)' : 'translateX(100%)';
            
            $element.css({
                'transform': startTransform,
                'opacity': '0',
                'transition': `transform ${duration}ms ease-out, opacity ${duration}ms ease-out`
            });

            setTimeout(() => {
                $element.css({
                    'transform': 'translateX(0)',
                    'opacity': '1'
                });
            }, 10);
        },

        slideOut: function(element, direction = 'left', duration = 300) {
            const $element = $(element);
            const endTransform = direction === 'left' ? 'translateX(-100%)' : 'translateX(100%)';
            
            $element.css({
                'transform': endTransform,
                'opacity': '0',
                'transition': `transform ${duration}ms ease-out, opacity ${duration}ms ease-out`
            });
        },

        slideToggle: function(element, duration = 300) {
            const $element = $(element);
            
            if ($element.is(':visible')) {
                slideAnimations.slideOut($element, 'left', duration);
                setTimeout(() => {
                    $element.hide();
                }, duration);
            } else {
                $element.show();
                slideAnimations.slideIn($element, 'left', duration);
            }
        }
    };

    // Enhanced Space Key Support
    const spaceKeySupport = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Add space key support to clickable elements
            $('.space-activatable, [role="button"], .btn, .card, .plan-card').on('keydown', function(e) {
                if (e.key === ' ' || e.key === 'Spacebar') {
                    e.preventDefault();
                    $(this).click();
                }
            });

            // Add space key support to toggle elements
            $('[data-toggle]').on('keydown', function(e) {
                if (e.key === ' ' || e.key === 'Spacebar') {
                    e.preventDefault();
                    const target = $($(this).data('toggle'));
                    const method = $(this).data('toggle-method') || 'toggle';
                    target[method]();
                }
            });
        }
    };

    // Enhanced Real-time Validation
    const realTimeValidation = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Real-time validation on input
            $('.real-time-validation input, .real-time-validation select, .real-time-validation textarea').on('input blur', function() {
                realTimeValidation.validateField($(this));
            });

            // Real-time validation on form submission
            $('form').on('submit', function(e) {
                if (!realTimeValidation.validateForm($(this))) {
                    e.preventDefault();
                }
            });
        },

        validateField: function(field) {
            const value = field.val().trim();
            const type = field.attr('type');
            const required = field.prop('required');
            let isValid = true;
            let errorMessage = '';

            // Clear previous validation state
            field.removeClass('validating valid invalid');
            field.siblings('.validation-indicator').remove();

            // Add validation indicator
            field.after('<div class="validation-indicator"></div>');

            // Show validating state
            field.addClass('validating');

            // Simulate validation delay
            setTimeout(() => {
                // Required field validation
                if (required && !value) {
                    isValid = false;
                    errorMessage = 'This field is required.';
                }

                // Email validation
                if (type === 'email' && value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Please enter a valid email address.';
                    }
                }

                // Number validation
                if (type === 'number' && value) {
                    if (isNaN(value) || parseFloat(value) < 0) {
                        isValid = false;
                        errorMessage = 'Please enter a valid positive number.';
                    }
                }

                // Apply validation result
                field.removeClass('validating');
                if (isValid) {
                    field.addClass('valid');
                } else {
                    field.addClass('invalid');
                    field.siblings('.validation-indicator').text(errorMessage);
                }
            }, 500);
        },

        validateForm: function(form) {
            let isValid = true;
            form.find('input, select, textarea').each(function() {
                if (!realTimeValidation.validateField($(this))) {
                    isValid = false;
                }
            });
            return isValid;
        }
    };

    // Enhanced Try-Catch Support
    const tryCatchSupport = {
        init: function() {
            this.setupErrorBoundaries();
        },

        setupErrorBoundaries: function() {
            // Wrap AJAX calls in try-catch
            const originalAjax = $.ajax;
            $.ajax = function(options) {
                try {
                    return originalAjax.call(this, options);
                } catch (error) {
                    console.error('AJAX Error:', error);
                    utils.handleError(error, 'AJAX');
                    return $.Deferred().reject(error);
                }
            };

            // Wrap event handlers in try-catch
            $(document).on('click', function(e) {
                try {
                    // Event handling logic
                } catch (error) {
                    console.error('Click Event Error:', error);
                    utils.handleError(error, 'Click Event');
                }
            });
        },

        wrapFunction: function(func, context = '') {
            return function(...args) {
                try {
                    return func.apply(this, args);
                } catch (error) {
                    console.error(`Error in ${context}:`, error);
                    utils.handleError(error, context);
                    return null;
                }
            };
        }
    };

    // Enhanced Notification Queue
    const notificationQueue = {
        queue: [],
        isProcessing: false,

        init: function() {
            this.createContainer();
        },

        createContainer: function() {
            if (!$('#notification-queue-container').length) {
                $('body').append('<div id="notification-queue-container" class="notification-queue"></div>');
            }
        },

        add: function(message, type = 'info', duration = 5000) {
            const notification = {
                id: 'notification-' + Date.now(),
                message,
                type,
                duration
            };

            this.queue.push(notification);
            this.processQueue();
        },

        processQueue: function() {
            if (this.isProcessing || this.queue.length === 0) return;

            this.isProcessing = true;
            const notification = this.queue.shift();
            this.display(notification);
        },

        display: function(notification) {
            const html = `
                <div id="${notification.id}" class="notification notification-${notification.type}">
                    <div class="notification-content">
                        <span class="notification-message">${notification.message}</span>
                        <button type="button" class="notification-close" onclick="notificationQueue.remove('${notification.id}')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            `;

            $('#notification-queue-container').append(html);

            // Auto-remove
            if (notification.duration > 0) {
                setTimeout(() => {
                    this.remove(notification.id);
                }, notification.duration);
            }

            // Process next notification
            setTimeout(() => {
                this.isProcessing = false;
                this.processQueue();
            }, 300);
        },

        remove: function(id) {
            $(`#${id}`).addClass('removing');
            setTimeout(() => {
                $(`#${id}`).remove();
            }, 300);
        }
    };

    // Enhanced Security Features
    const securityFeatures = {
        init: function() {
            this.setupCSRFProtection();
            this.setupXSSPrevention();
            this.setupInputSanitization();
        },

        setupCSRFProtection: function() {
            // Add CSRF token to all forms
            $('form').each(function() {
                if (!$(this).find('input[name="_wpnonce"]').length) {
                    $(this).append(`<input type="hidden" name="_wpnonce" value="${config.nonce}">`);
                }
            });

            // Add CSRF token to AJAX requests
            $.ajaxSetup({
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', config.nonce);
                }
            });
        },

        setupXSSPrevention: function() {
            // Sanitize user input
            $('input, textarea').on('input', function() {
                const value = $(this).val();
                const sanitized = this.sanitizeInput(value);
                if (value !== sanitized) {
                    $(this).val(sanitized);
                }
            });
        },

        setupInputSanitization: function() {
            // Add input sanitization to forms
            $('form').on('submit', function(e) {
                const form = $(this);
                form.find('input, textarea').each(function() {
                    const value = $(this).val();
                    const sanitized = securityFeatures.sanitizeInput(value);
                    $(this).val(sanitized);
                });
            });
        },

        sanitizeInput: function(input) {
            if (typeof input !== 'string') return input;
            
            // Remove script tags
            input = input.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
            
            // Remove event handlers
            input = input.replace(/\s*on\w+\s*=\s*["'][^"']*["']/gi, '');
            
            // Remove javascript: protocol
            input = input.replace(/javascript:/gi, '');
            
            return input;
        },

        validateInput: function(input, type = 'text') {
            const validators = {
                email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                number: /^\d+(\.\d+)?$/,
                url: /^https?:\/\/.+/,
                phone: /^[\+]?[1-9][\d]{0,15}$/
            };

            if (validators[type]) {
                return validators[type].test(input);
            }

            return true;
        }
    };

    // Enhanced Mobile Gesture Support
    const mobileGestures = {
        init: function() {
            this.setupSwipeGestures();
            this.setupTouchFeedback();
        },

        setupSwipeGestures: function() {
            let startX, startY, endX, endY;
            const minSwipeDistance = 50;

            $('.swipeable').on('touchstart', function(e) {
                const touch = e.originalEvent.touches[0];
                startX = touch.clientX;
                startY = touch.clientY;
            });

            $('.swipeable').on('touchend', function(e) {
                const touch = e.originalEvent.changedTouches[0];
                endX = touch.clientX;
                endY = touch.clientY;

                const deltaX = endX - startX;
                const deltaY = endY - startY;

                if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > minSwipeDistance) {
                    if (deltaX > 0) {
                        // Swipe right
                        $(this).trigger('swiperight');
                    } else {
                        // Swipe left
                        $(this).trigger('swipeleft');
                    }
                }
            });

            // Handle swipe events
            $('.swipeable').on('swipeleft', function() {
                $(this).addClass('swipe-left');
            });

            $('.swipeable').on('swiperight', function() {
                $(this).addClass('swipe-right');
            });
        },

        setupTouchFeedback: function() {
            $('.touch-feedback').on('touchstart', function() {
                $(this).addClass('touching');
            });

            $('.touch-feedback').on('touchend', function() {
                setTimeout(() => {
                    $(this).removeClass('touching');
                }, 150);
            });
        }
    };

    // Enhanced Performance Monitoring
    const performanceMonitor = {
        init: function() {
            this.createMonitor();
            this.startMonitoring();
        },

        createMonitor: function() {
            if (!$('.performance-monitor').length) {
                $('body').append('<div class="performance-monitor">Performance: <span id="perf-metrics">Loading...</span></div>');
            }
        },

        startMonitoring: function() {
            // Monitor page load performance
            $(window).on('load', () => {
                if ('performance' in window) {
                    const perfData = performance.getEntriesByType('navigation')[0];
                    const loadTime = perfData.loadEventEnd - perfData.loadEventStart;
                    $('#perf-metrics').text(`${loadTime}ms`);
                    $('.performance-monitor').addClass('visible');
                }
            });

            // Monitor scroll performance
            let scrollTimeout;
            $(window).on('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    this.updateScrollMetrics();
                }, 100);
            });
        },

        updateScrollMetrics: function() {
            const scrollTop = $(window).scrollTop();
            const scrollPercent = Math.round((scrollTop / ($(document).height() - $(window).height())) * 100);
            $('#perf-metrics').text(`Scroll: ${scrollPercent}%`);
        }
    };

    // Enhanced ARIA Roles and Labels
    function enhanceARIAFeatures() {
        // Add ARIA roles to elements
        const elements = document.querySelectorAll('[data-aria-role]');
        elements.forEach(element => {
            const role = element.getAttribute('data-aria-role');
            element.setAttribute('role', role);
        });

        // Add ARIA labels to elements
        const labeledElements = document.querySelectorAll('[data-aria-label]');
        labeledElements.forEach(element => {
            const label = element.getAttribute('data-aria-label');
            element.setAttribute('aria-label', label);
        });

        // Add ARIA controls to elements
        const controlledElements = document.querySelectorAll('[data-aria-controls]');
        controlledElements.forEach(element => {
            const controls = element.getAttribute('data-aria-controls');
            element.setAttribute('aria-controls', controls);
        });

        // Add ARIA expanded to dropdowns
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function() {
                const expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !expanded);
            });
        });

        // Add ARIA live regions
        const liveRegions = document.querySelectorAll('[data-aria-live]');
        liveRegions.forEach(region => {
            const live = region.getAttribute('data-aria-live');
            region.setAttribute('aria-live', live);
        });
    }

    // Enhanced Data Escaping
    function escapeData(data) {
        if (typeof data !== 'string') {
            return data;
        }
        
        const div = document.createElement('div');
        div.textContent = data;
        return div.innerHTML;
    }

    function unescapeData(data) {
        if (typeof data !== 'string') {
            return data;
        }
        
        const div = document.createElement('div');
        div.innerHTML = data;
        return div.textContent;
    }

    // Enhanced SQL Injection Prevention
    function sanitizeSQLInput(input) {
        if (typeof input !== 'string') {
            return input;
        }
        
        // Remove SQL injection patterns
        const sqlPatterns = [
            /(\b(union|select|insert|update|delete|drop|create|alter|exec|execute|script)\b)/gi,
            /(['";\\])/g,
            /(\b(or|and)\b\s+\d+\s*=\s*\d+)/gi,
            /(\b(union|select|insert|update|delete|drop|create|alter|exec|execute|script)\b.*\b(union|select|insert|update|delete|drop|create|alter|exec|execute|script)\b)/gi
        ];
        
        let sanitized = input;
        sqlPatterns.forEach(pattern => {
            sanitized = sanitized.replace(pattern, '');
        });
        
        return sanitized.trim();
    }

    // Enhanced CSP Support
    function enforceCSP() {
        // Remove inline scripts
        const inlineScripts = document.querySelectorAll('script:not([src])');
        inlineScripts.forEach(script => {
            if (script.textContent.trim()) {
                console.warn('CSP: Inline script detected and removed');
                script.remove();
            }
        });

        // Remove inline styles
        const inlineStyles = document.querySelectorAll('[style]');
        inlineStyles.forEach(element => {
            const style = element.getAttribute('style');
            if (style && style.includes('javascript:')) {
                console.warn('CSP: Inline style with javascript: detected and removed');
                element.removeAttribute('style');
            }
        });

        // Remove event handlers
        const eventElements = document.querySelectorAll('[onclick], [onload], [onerror], [onmouseover]');
        eventElements.forEach(element => {
            ['onclick', 'onload', 'onerror', 'onmouseover'].forEach(event => {
                if (element.hasAttribute(event)) {
                    console.warn(`CSP: ${event} attribute detected and removed`);
                    element.removeAttribute(event);
                }
            });
        });
    }

    // Enhanced Mobile First Design
    function enhanceMobileFirst() {
        // Add mobile-first classes
        const containers = document.querySelectorAll('.container, .container-fluid');
        containers.forEach(container => {
            if (!container.classList.contains('mobile-first')) {
                container.classList.add('mobile-first');
            }
        });

        // Add responsive image classes
        const images = document.querySelectorAll('img:not(.responsive-image)');
        images.forEach(img => {
            img.classList.add('responsive-image');
        });

        // Add touch target classes
        const touchElements = document.querySelectorAll('a, button, input[type="button"], input[type="submit"]');
        touchElements.forEach(element => {
            if (!element.classList.contains('touch-target')) {
                element.classList.add('touch-target');
            }
        });
    }

    // Enhanced Swipe Gestures
    function initSwipeGestures() {
        const swipeableElements = document.querySelectorAll('.swipeable');
        
        swipeableElements.forEach(element => {
            let startX = 0;
            let startY = 0;
            let currentX = 0;
            let currentY = 0;
            let isSwiping = false;

            element.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                isSwiping = true;
                element.classList.add('swiping');
            });

            element.addEventListener('touchmove', function(e) {
                if (!isSwiping) return;
                
                currentX = e.touches[0].clientX;
                currentY = e.touches[0].clientY;
                
                const diffX = startX - currentX;
                const diffY = startY - currentY;
                
                if (Math.abs(diffX) > Math.abs(diffY)) {
                    e.preventDefault();
                    element.style.transform = `translateX(-${diffX}px)`;
                }
            });

            element.addEventListener('touchend', function(e) {
                if (!isSwiping) return;
                
                const diffX = startX - currentX;
                const threshold = 50;
                
                if (Math.abs(diffX) > threshold) {
                    if (diffX > 0) {
                        element.classList.add('swipe-left');
                        element.dispatchEvent(new CustomEvent('swipeleft'));
                    } else {
                        element.classList.add('swipe-right');
                        element.dispatchEvent(new CustomEvent('swiperight'));
                    }
                }
                
                element.style.transform = '';
                element.classList.remove('swiping');
                isSwiping = false;
            });
        });
    }

    // Enhanced Hamburger Menu
    function initHamburgerMenu() {
        const hamburgerButtons = document.querySelectorAll('.hamburger-menu');
        
        hamburgerButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                this.classList.toggle('active');
                
                const target = this.getAttribute('data-bs-target');
                const targetElement = document.querySelector(target);
                
                if (targetElement) {
                    targetElement.classList.toggle('show');
                }
                
                // Update ARIA attributes
                const expanded = this.classList.contains('active');
                this.setAttribute('aria-expanded', expanded);
            });

            // Keyboard support
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    }

    // Enhanced Error CSS Classes
    function addErrorClasses() {
        // Add error class to invalid form fields
        const formFields = document.querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            field.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('error');
                    this.classList.remove('success');
                } else if (this.value.trim()) {
                    this.classList.add('success');
                    this.classList.remove('error');
                }
            });
        });
    }

    // Enhanced Form Validation CSS Classes
    function enhanceFormValidation() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            const fields = form.querySelectorAll('input, select, textarea');
            
            fields.forEach(field => {
                // Real-time validation
                field.addEventListener('input', function() {
                    validateField(this);
                });
                
                field.addEventListener('blur', function() {
                    validateField(this);
                });
            });
            
            // Form submission validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                fields.forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    showNotification('Please correct the errors in the form.', 'error');
                }
            });
        });
    }

    function validateField(field) {
        const wrapper = field.parentNode;
        const indicator = wrapper.querySelector('.validation-indicator');
        
        wrapper.classList.add('real-time-validation', 'validating');
        
        setTimeout(() => {
            const isValid = isFieldValid(field);
            
            wrapper.classList.remove('validating');
            wrapper.classList.add(isValid ? 'valid' : 'invalid');
            
            if (indicator) {
                indicator.style.backgroundColor = isValid ? '#28a745' : '#dc3545';
            }
        }, 300);
        
        return isValid;
    }

    function isFieldValid(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');
        
        if (required && !value) return false;
        if (!value) return true;
        
        switch (type) {
            case 'email':
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            case 'url':
                return /^https?:\/\/.+/.test(value);
            case 'number':
                return !isNaN(value) && value !== '';
            case 'tel':
                return /^[\+]?[0-9\s\-\(\)]+$/.test(value);
            default:
                return true;
        }
    }

    // Enhanced Security Features
    function enhanceSecurity() {
        // Add security indicators
        const secureElements = document.querySelectorAll('.sql-safe, .csp-safe');
        secureElements.forEach(element => {
            element.classList.add('security-enhanced');
        });
        
        // Sanitize user inputs
        const inputs = document.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = sanitizeSQLInput(e.target.value);
            });
        });
        
        // Add nonce verification to forms
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            if (!form.querySelector('input[name="_wpnonce"]')) {
                const nonceField = document.createElement('input');
                nonceField.type = 'hidden';
                nonceField.name = '_wpnonce';
                nonceField.value = window.blackcnoteTheme?.nonce || '';
                form.appendChild(nonceField);
            }
        });
    }

    // Enhanced Mobile Optimization
    function enhanceMobileOptimization() {
        // Add mobile optimization classes
        document.body.classList.add('mobile-optimized');
        
        // Add touch action to interactive elements
        const touchElements = document.querySelectorAll('a, button, input, select, textarea');
        touchElements.forEach(element => {
            element.style.touchAction = 'manipulation';
        });
        
        // Add viewport meta tag if missing
        if (!document.querySelector('meta[name="viewport"]')) {
            const viewport = document.createElement('meta');
            viewport.name = 'viewport';
            viewport.content = 'width=device-width, initial-scale=1';
            document.head.appendChild(viewport);
        }
    }

    // Enhanced Accessibility System
    function enhanceAccessibility() {
        // Add accessibility classes
        document.body.classList.add('accessibility-enhanced');
        
        // Add focus management
        const focusableElements = document.querySelectorAll('a, button, input, select, textarea, [tabindex]');
        focusableElements.forEach(element => {
            element.addEventListener('focus', function() {
                this.classList.add('focus-visible');
            });
            
            element.addEventListener('blur', function() {
                this.classList.remove('focus-visible');
            });
        });
        
        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });
        
        document.addEventListener('mousedown', function() {
            document.body.classList.remove('keyboard-navigation');
        });
        
        // Add screen reader announcements
        const liveRegion = document.createElement('div');
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.className = 'sr-only';
        document.body.appendChild(liveRegion);
        
        window.announceToScreenReader = (message) => {
            liveRegion.textContent = message;
            setTimeout(() => {
                liveRegion.textContent = '';
            }, 1000);
        };
    }

    // Enhanced Performance System
    function enhancePerformance() {
        // Add performance classes
        document.body.classList.add('performance-enhanced');
        
        // Lazy load images
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
        
        // Performance monitoring
        if ('performance' in window) {
            window.addEventListener('load', () => {
                const perfData = performance.getEntriesByType('navigation')[0];
                console.log('Page Load Time:', perfData.loadEventEnd - perfData.loadEventStart);
                
                // Mark performance
                performance.mark('blackcnote-enhanced-features-loaded');
            });
        }
    }

    // Enhanced Error Handling System
    function enhanceErrorHandling() {
        // Add error handling classes
        document.body.classList.add('error-handling-enhanced');
        
        // Global error handler
        window.addEventListener('error', (e) => {
            console.error('Global Error:', e.error);
            showNotification('An error occurred. Please try again.', 'error');
        });
        
        // Promise rejection handler
        window.addEventListener('unhandledrejection', (e) => {
            console.error('Unhandled Promise Rejection:', e.reason);
            showNotification('An error occurred. Please try again.', 'error');
        });
    }

    // Enhanced Form Validation System
    function enhanceFormValidationSystem() {
        // Add form validation classes
        document.body.classList.add('form-validation-system-enhanced');
        
        // Real-time validation with indicators
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.classList.add('form-validation-enhanced');
            
            const fields = form.querySelectorAll('input, select, textarea');
            fields.forEach(field => {
                // Add validation indicator
                const indicator = document.createElement('div');
                indicator.className = 'validation-indicator';
                field.parentNode.style.position = 'relative';
                field.parentNode.appendChild(indicator);
                
                // Real-time validation
                field.addEventListener('input', function() {
                    validateField(this);
                });
            });
            
            form.addEventListener('submit', (e) => {
                if (!validateForm(form)) {
                    e.preventDefault();
                    showNotification('Please correct the errors in the form.', 'error');
                }
            });
        });
    }

    function validateForm(form) {
        const fields = form.querySelectorAll('input, select, textarea');
        let isValid = true;
        
        fields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    // Initialize all enhancements
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 BlackCnote Theme Enhanced Features Initializing...');
        
        // Initialize all enhancement systems
        enhanceARIAFeatures();
        enhanceMobileFirst();
        initSwipeGestures();
        initHamburgerMenu();
        addErrorClasses();
        enhanceFormValidation();
        enhanceSecurity();
        enhanceMobileOptimization();
        enhanceAccessibility();
        enhancePerformance();
        enhanceErrorHandling();
        enhanceFormValidationSystem();
        
        // Enforce CSP
        enforceCSP();
        
        console.log('✅ BlackCnote Theme Enhanced Features Initialized Successfully!');
        
        // Performance mark
        if ('performance' in window) {
            performance.mark('blackcnote-enhanced-features-loaded');
        }
    });

    // Enhanced notification system with better queue management
    class EnhancedNotificationSystem {
        constructor() {
            this.queue = [];
            this.isProcessing = false;
            this.container = this.createContainer();
        }
        
        createContainer() {
            let container = document.querySelector('.notification-enhanced');
            if (!container) {
                container = document.createElement('div');
                container.className = 'notification-enhanced';
                document.body.appendChild(container);
            }
            return container;
        }
        
        show(message, type = 'info', duration = 5000) {
            this.queue.push({ message, type, duration });
            this.processQueue();
        }
        
        processQueue() {
            if (this.isProcessing || this.queue.length === 0) return;
            
            this.isProcessing = true;
            const { message, type, duration } = this.queue.shift();
            
            const notification = this.createNotification(message, type);
            this.container.appendChild(notification);
            
            // Animate in
            requestAnimationFrame(() => {
                notification.style.transform = 'translateX(0)';
            });
            
            // Auto remove
            setTimeout(() => {
                this.removeNotification(notification);
            }, duration);
        }
        
        createNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.setAttribute('role', 'alert');
            notification.setAttribute('aria-live', 'polite');
            
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-message">${escapeData(message)}</span>
                    <button class="notification-close" aria-label="Close notification" role="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            
            // Close button functionality
            const closeBtn = notification.querySelector('.notification-close');
            closeBtn.addEventListener('click', () => {
                this.removeNotification(notification);
            });
            
            return notification;
        }
        
        removeNotification(notification) {
            $(`#${notification.id}`).fadeOut(300, function() {
                $(this).remove();
            });
        }
    }

    // Initialize enhanced notification system
    const enhancedNotifications = new EnhancedNotificationSystem();

    // Enhanced mobile menu with better accessibility
    class EnhancedMobileMenu {
        constructor() {
            this.menu = document.querySelector('#primary-menu');
            this.toggle = document.querySelector('.hamburger-menu');
            this.isOpen = false;
            this.init();
        }
        
        init() {
            if (!this.menu || !this.toggle) return;
            
            this.toggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMenu();
            });
            
            // Keyboard support
            this.toggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.toggleMenu();
                }
            });
            
            // Close on escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.closeMenu();
                }
            });
            
            // Close on outside click
            document.addEventListener('click', (e) => {
                if (this.isOpen && !this.menu.contains(e.target) && !this.toggle.contains(e.target)) {
                    this.closeMenu();
                }
            });
        }
        
        toggleMenu() {
            if (this.isOpen) {
                this.closeMenu();
            } else {
                this.openMenu();
            }
        }
        
        openMenu() {
            this.menu.classList.add('show');
            this.toggle.classList.add('active');
            this.toggle.setAttribute('aria-expanded', 'true');
            this.isOpen = true;
            
            // Focus management
            const firstLink = this.menu.querySelector('a');
            if (firstLink) {
                firstLink.focus();
            }
            
            // Announce to screen reader
            if (window.announceToScreenReader) {
                window.announceToScreenReader('Navigation menu opened');
            }
        }
        
        closeMenu() {
            this.menu.classList.remove('show');
            this.toggle.classList.remove('active');
            this.toggle.setAttribute('aria-expanded', 'false');
            this.isOpen = false;
            
            // Return focus to toggle
            this.toggle.focus();
            
            // Announce to screen reader
            if (window.announceToScreenReader) {
                window.announceToScreenReader('Navigation menu closed');
            }
        }
    }

    // Initialize enhanced mobile menu
    document.addEventListener('DOMContentLoaded', function() {
        new EnhancedMobileMenu();
    });

    // Export for global access
    window.BlackCnoteEnhanced = {
        notifications: enhancedNotifications,
        escapeData,
        unescapeData,
        sanitizeSQLInput,
        announceToScreenReader: window.announceToScreenReader
    };

    console.log('🎯 BlackCnote Enhanced Features Loaded Successfully!');
})(jQuery); 