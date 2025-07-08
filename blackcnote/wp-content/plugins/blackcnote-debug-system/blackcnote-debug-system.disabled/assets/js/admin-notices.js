/**
 * BlackCnote Admin Notices JavaScript
 * Handles dismiss functionality for script checker admin notices
 */

(function($) {
    'use strict';
    
    /**
     * Initialize admin notices functionality
     */
    function initAdminNotices() {
        // Handle dismiss alert buttons
        $(document).on('click', '.blackcnote-script-alert .dismiss-alert', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = $(this);
            const alert = button.closest('.blackcnote-script-alert');
            const dismissHours = button.data('dismiss-hours');
            
            // Prevent multiple clicks
            if (button.hasClass('dismissing')) {
                return;
            }
            
            // Update button state
            button.addClass('dismissing').text(blackcnoteAdminNotices.strings.dismissing);
            
            // Send AJAX request
            $.ajax({
                url: blackcnoteAdminNotices.ajaxurl,
                type: 'POST',
                data: {
                    action: 'blackcnote_dismiss_script_alert',
                    dismiss_hours: dismissHours,
                    nonce: blackcnoteAdminNotices.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message briefly
                        button.text(blackcnoteAdminNotices.strings.dismissed);
                        
                        // Fade out the alert
                        alert.fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        // Show a brief success notification
                        showNotification(blackcnoteAdminNotices.strings.dismissed, 'success');
                    } else {
                        // Show error message
                        button.text(blackcnoteAdminNotices.strings.error);
                        showNotification(blackcnoteAdminNotices.strings.error, 'error');
                    }
                },
                error: function() {
                    // Show error message
                    button.text(blackcnoteAdminNotices.strings.error);
                    showNotification(blackcnoteAdminNotices.strings.error, 'error');
                },
                complete: function() {
                    // Reset button state after a delay
                    setTimeout(function() {
                        button.removeClass('dismissing');
                        if (dismissHours === 24) {
                            button.text('Dismiss for 24 hours');
                        } else {
                            button.text('Dismiss for 1 week');
                        }
                    }, 2000);
                }
            });
        });
        
        // Handle standard WordPress notice dismissal
        $(document).on('click', '.blackcnote-script-alert .notice-dismiss', function(e) {
            const alert = $(this).closest('.blackcnote-script-alert');
            const dismissHours = 24; // Default to 24 hours for standard dismiss
            
            // Send AJAX request to track dismissal
            $.ajax({
                url: blackcnoteAdminNotices.ajaxurl,
                type: 'POST',
                data: {
                    action: 'blackcnote_dismiss_script_alert',
                    dismiss_hours: dismissHours,
                    nonce: blackcnoteAdminNotices.nonce
                }
            });
        });
        
        // Add hover effects for better UX
        $(document).on('mouseenter', '.blackcnote-script-alert', function() {
            $(this).addClass('hover');
        }).on('mouseleave', '.blackcnote-script-alert', function() {
            $(this).removeClass('hover');
        });
    }
    
    /**
     * Show notification message
     */
    function showNotification(message, type) {
        const notificationClass = type === 'success' ? 'notice-success' : 'notice-error';
        const notification = $('<div class="notice ' + notificationClass + ' is-dismissible">' +
            '<p>' + message + '</p>' +
            '</div>');
        
        // Insert at the top of the page
        $('.wrap h1').first().after(notification);
        
        // Auto-dismiss after 3 seconds
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        initAdminNotices();
    });
    
})(jQuery); 