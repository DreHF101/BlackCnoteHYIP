/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @package BlackCnote
 */

(function($) {

    // Site title and description.
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });

    // Header text color.
    wp.customize('header_textcolor', function(value) {
        value.bind(function(to) {
            if ('blank' === to) {
                $('.site-title, .site-description').css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute',
                });
            } else {
                $('.site-title, .site-description').css({
                    'clip': 'auto',
                    'position': 'relative',
                });
                $('.site-title a, .site-description').css({
                    'color': to,
                });
            }
        });
    });

    // Custom logo
    wp.customize('custom_logo', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.custom-logo-link').html('<img src="' + to + '" alt="' + wp.customize('blogname').get() + '">');
            } else {
                $('.custom-logo-link').empty();
            }
        });
    });

    // Primary color
    wp.customize('primary_color', function(value) {
        value.bind(function(to) {
            // Update CSS custom property
            document.documentElement.style.setProperty('--primary-color', to);
        });
    });

    // Secondary color
    wp.customize('secondary_color', function(value) {
        value.bind(function(to) {
            // Update CSS custom property
            document.documentElement.style.setProperty('--secondary-color', to);
        });
    });

    // Typography
    wp.customize('body_font', function(value) {
        value.bind(function(to) {
            $('body').css('font-family', to);
        });
    });

    wp.customize('heading_font', function(value) {
        value.bind(function(to) {
            $('h1, h2, h3, h4, h5, h6').css('font-family', to);
        });
    });

    // Layout options
    wp.customize('container_width', function(value) {
        value.bind(function(to) {
            $('.container').css('max-width', to + 'px');
        });
    });

    // Footer text
    wp.customize('footer_text', function(value) {
        value.bind(function(to) {
            $('.site-info').html(to);
        });
    });

    // Social media links
    wp.customize('social_facebook', function(value) {
        value.bind(function(to) {
            $('.social-facebook').attr('href', to);
        });
    });

    wp.customize('social_twitter', function(value) {
        value.bind(function(to) {
            $('.social-twitter').attr('href', to);
        });
    });

    wp.customize('social_instagram', function(value) {
        value.bind(function(to) {
            $('.social-instagram').attr('href', to);
        });
    });

    // Custom CSS
    wp.customize('custom_css', function(value) {
        value.bind(function(to) {
            $('#blackcnote-custom-css').html(to);
        });
    });

    // Theme options
    wp.customize('show_sidebar', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.sidebar').show();
            } else {
                $('.sidebar').hide();
            }
        });
    });

    wp.customize('sticky_header', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.site-header').addClass('sticky');
            } else {
                $('.site-header').removeClass('sticky');
            }
        });
    });

    // Blog options
    wp.customize('blog_layout', function(value) {
        value.bind(function(to) {
            $('.blog-posts').removeClass('grid list masonry').addClass(to);
        });
    });

    wp.customize('excerpt_length', function(value) {
        value.bind(function(to) {
            // This would need to be handled server-side for actual excerpt length
            // For preview, we can simulate by truncating existing excerpts
            $('.entry-summary').each(function() {
                var text = $(this).text();
                if (text.length > to) {
                    $(this).text(text.substring(0, to) + '...');
                }
            });
        });
    });

    // WooCommerce options (if WooCommerce is active)
    if (typeof wc_add_to_cart_params !== 'undefined') {
        wp.customize('shop_columns', function(value) {
            value.bind(function(to) {
                $('.woocommerce ul.products').removeClass('columns-2 columns-3 columns-4').addClass('columns-' + to);
            });
        });

        wp.customize('shop_sidebar', function(value) {
            value.bind(function(to) {
                if (to) {
                    $('.woocommerce-sidebar').show();
                } else {
                    $('.woocommerce-sidebar').hide();
                }
            });
        });
    }

    // Performance optimizations
    var debounce = function(func, wait, immediate) {
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
    };

    // Debounce color changes for better performance
    wp.customize('primary_color', debounce(function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--primary-color', to);
        });
    }, 250));

    wp.customize('secondary_color', debounce(function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--secondary-color', to);
        });
    }, 250));

})(jQuery); 