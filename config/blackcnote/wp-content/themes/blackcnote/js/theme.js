/**
 * BlackCnote Theme JavaScript
 *
 * @package BlackCnote
 */

(function($) {
    'use strict';

    // Theme initialization
    $(document).ready(function() {
        
        // Initialize theme functionality
        BlackCnoteTheme.init();
        
    });

    // Theme object
    var BlackCnoteTheme = {
        
        init: function() {
            this.setupNavigation();
            this.setupReactIntegration();
            this.setupAjaxHandlers();
        },

        setupNavigation: function() {
            // Mobile menu toggle
            $('.menu-toggle').on('click', function() {
                $('.main-navigation ul').toggleClass('active');
            });

            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });
        },

        setupReactIntegration: function() {
            // Check if React app container exists
            var reactContainer = $('#blackcnote-app');
            if (reactContainer.length) {
                // Remove loading message when React app loads
                setTimeout(function() {
                    reactContainer.find('.loading').fadeOut();
                }, 2000);
            }
        },

        setupAjaxHandlers: function() {
            // Handle AJAX requests
            $(document).on('click', '.ajax-button', function(e) {
                e.preventDefault();
                var button = $(this);
                var action = button.data('action');
                var data = button.data('data') || {};

                // Add loading state
                button.addClass('loading').prop('disabled', true);

                // Make AJAX request
                $.ajax({
                    url: blackcnote_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: action,
                        nonce: blackcnote_ajax.nonce,
                        data: data
                    },
                    success: function(response) {
                        if (response.success) {
                            BlackCnoteTheme.showMessage('success', response.data.message || 'Success!');
                        } else {
                            BlackCnoteTheme.showMessage('error', response.data.message || 'Error occurred.');
                        }
                    },
                    error: function() {
                        BlackCnoteTheme.showMessage('error', 'Network error occurred.');
                    },
                    complete: function() {
                        button.removeClass('loading').prop('disabled', false);
                    }
                });
            });
        },

        showMessage: function(type, message) {
            var messageClass = type === 'success' ? 'success' : 'error';
            var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';
            
            // Remove existing messages
            $('.success, .error').remove();
            
            // Add new message
            $('.site-content').prepend(messageHtml);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $('.' + messageClass).fadeOut();
            }, 5000);
        },

        // Utility functions
        utils: {
            debounce: function(func, wait, immediate) {
                var timeout;
                return function() {
                    var context = this, args = arguments;
                    var later = function() {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    var callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
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
        }
    };

    // Expose to global scope for React integration
    window.BlackCnoteTheme = BlackCnoteTheme;

})(jQuery); 