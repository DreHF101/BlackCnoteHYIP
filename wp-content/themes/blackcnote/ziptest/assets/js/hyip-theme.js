/**
 * BlackCnote Theme Custom Scripts
 *
 * @package BlackCnote_Theme
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Initialize theme functionality
    function initTheme() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Initialize form validation
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }

    // Investment calculator functionality
    function initCalculator() {
        $('#investment-calculator').on('submit', function(e) {
            e.preventDefault();
            
            const planId = $('#plan_id').val();
            const amount = $('#amount').val();
            
            if (!planId || !amount) {
                return;
            }
            
            $.ajax({
                url: blackcnoteTheme.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_calculate_return',
                    nonce: blackcnoteTheme.nonce,
                    plan_id: planId,
                    amount: amount
                },
                success: function(response) {
                    if (response.success) {
                        $('#return-amount').text(response.data.return_amount);
                        $('#return-rate').text(response.data.return_rate);
                        $('#calculator-result').show();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    }

    // Transaction filter functionality
    function initTransactionFilter() {
        $('#transaction-filter').on('submit', function(e) {
            e.preventDefault();
            
            const type = $('#type').val();
            const fromDate = $('#from_date').val();
            const toDate = $('#to_date').val();
            
            $.ajax({
                url: blackcnoteTheme.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'blackcnote_filter_transactions',
                    nonce: blackcnoteTheme.nonce,
                    type: type,
                    from_date: fromDate,
                    to_date: toDate
                },
                success: function(response) {
                    if (response.success) {
                        $('#transactions-table tbody').html(response.data.html);
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    }

    // Document ready
    $(document).ready(function() {
        initTheme();
        initCalculator();
        initTransactionFilter();
    });

    // Mobile Navigation
    if ($('.navbar-toggler').length) {
        $('.navbar-toggler').on('click', function() {
            $('.navbar-collapse').toggleClass('show');
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.navbar').length) {
                $('.navbar-collapse').removeClass('show');
            }
        });
    }

    // Smooth Scroll
    $('a[href*="#"]:not([href="#"])').on('click', function() {
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && 
            location.hostname === this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
                return false;
            }
        }
    });

    // Back to Top Button
    if ($('#back-to-top').length) {
        var backToTop = $('#back-to-top');

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 100) {
                backToTop.fadeIn();
            } else {
                backToTop.fadeOut();
            }
        });

        backToTop.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 800);
        });
    }

    // Form Input Formatting
    $('.form-control').on('input', function() {
        var value = $(this).val();
        if (value) {
            $(this).addClass('has-value');
        } else {
            $(this).removeClass('has-value');
        }
    });

    // Password Strength Meter
    if ($('#password').length) {
        $('#password').on('input', function() {
            var password = $(this).val();
            var strength = 0;

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[^a-zA-Z0-9]+/)) strength++;

            var meter = $('#password-strength-meter');
            var feedback = $('#password-strength-feedback');

            switch (strength) {
                case 0:
                case 1:
                    meter.val(20);
                    meter.removeClass().addClass('form-range bg-danger');
                    feedback.text('Very Weak');
                    break;
                case 2:
                    meter.val(40);
                    meter.removeClass().addClass('form-range bg-warning');
                    feedback.text('Weak');
                    break;
                case 3:
                    meter.val(60);
                    meter.removeClass().addClass('form-range bg-info');
                    feedback.text('Medium');
                    break;
                case 4:
                    meter.val(80);
                    meter.removeClass().addClass('form-range bg-primary');
                    feedback.text('Strong');
                    break;
                case 5:
                    meter.val(100);
                    meter.removeClass().addClass('form-range bg-success');
                    feedback.text('Very Strong');
                    break;
            }
        });
    }

    // AJAX Form Submission
    $('.ajax-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var originalText = submitButton.text();

        submitButton.prop('disabled', true).text('Processing...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    form.trigger('reset');
                    form.find('.was-validated').removeClass('was-validated');
                    
                    // Show success message
                    var alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                        .text(response.data.message)
                        .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                    
                    form.before(alert);

                    // Remove alert after 5 seconds
                    setTimeout(function() {
                        alert.alert('close');
                    }, 5000);
                } else {
                    // Show error message
                    var alert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">')
                        .text(response.data.message)
                        .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                    
                    form.before(alert);
                }
            },
            error: function() {
                // Show error message
                var alert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">')
                    .text('An error occurred. Please try again.')
                    .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                
                form.before(alert);
            },
            complete: function() {
                submitButton.prop('disabled', false).text(originalText);
            }
        });
    });

})(jQuery); 