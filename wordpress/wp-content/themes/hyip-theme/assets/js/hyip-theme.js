/**
 * HYIP Theme Custom JavaScript
 */
(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        // Handle plan selection
        $('.plan-actions .button').on('click', function(e) {
            e.preventDefault();
            const planName = $(this).closest('.plan-card').find('h2').text();
            alert('You selected the ' + planName + '. This feature will be available soon.');
        });

        // Handle transaction filters
        $('.filter-form').on('submit', function(e) {
            e.preventDefault();
            const type = $('#transaction-type').val();
            const date = $('#date-range').val();
            alert('Filtering transactions by type: ' + type + ' and date: ' + date + '. This feature will be available soon.');
        });

        // Handle pagination
        $('.pagination-links a').on('click', function(e) {
            e.preventDefault();
            if (!$(this).hasClass('disabled')) {
                alert('Pagination will be available soon.');
            }
        });

        // Initialize tooltips
        $('[data-tooltip]').tooltip();

        // Handle mobile menu toggle
        $('.menu-toggle').on('click', function() {
            $('.main-navigation').toggleClass('toggled');
        });

        // Handle dropdown menus
        $('.menu-item-has-children > a').on('click', function(e) {
            if ($(window).width() < 768) {
                e.preventDefault();
                $(this).next('.sub-menu').slideToggle();
            }
        });

        // Handle smooth scrolling
        $('a[href*="#"]:not([href="#"])').on('click', function() {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                let target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                    return false;
                }
            }
        });

        // Handle form validation
        $('form').on('submit', function(e) {
            const requiredFields = $(this).find('[required]');
            let isValid = true;

            requiredFields.each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });

        // Handle error field focus
        $('.error').on('focus', function() {
            $(this).removeClass('error');
        });

        // Handle AJAX loading states
        $('.ajax-trigger').on('click', function() {
            const $button = $(this);
            const originalText = $button.text();

            $button.prop('disabled', true)
                .text('Loading...')
                .addClass('loading');

            // Simulate AJAX request
            setTimeout(function() {
                $button.prop('disabled', false)
                    .text(originalText)
                    .removeClass('loading');
            }, 1000);
        });

        // Handle responsive tables
        function handleResponsiveTables() {
            $('table').each(function() {
                if ($(window).width() < 768) {
                    $(this).addClass('responsive');
                } else {
                    $(this).removeClass('responsive');
                }
            });
        }

        // Call on load and resize
        handleResponsiveTables();
        $(window).on('resize', handleResponsiveTables);
    });

})(jQuery); 