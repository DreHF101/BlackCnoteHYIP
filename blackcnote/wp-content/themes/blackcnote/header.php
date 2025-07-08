/**
 * The header for the BlackCnote theme
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Only render header if allowed by theme settings
if (!function_exists('blackcnote_should_render_wp_header_footer') || blackcnote_should_render_wp_header_footer()) :

// Security and context variables
$ajax_nonce      = wp_create_nonce('blackcnote_ajax_nonce');
$csrf_token      = wp_create_nonce('blackcnote_csrf_token');
$current_user    = wp_get_current_user();
$user_display    = sanitize_text_field($current_user->display_name);
$user_avatar     = esc_url(get_avatar_url(get_current_user_id()));
$site_name       = esc_html(get_bloginfo('name'));
$site_desc       = esc_html(get_bloginfo('description'));
$home_url        = esc_url(home_url('/'));
$template_uri    = esc_url(get_template_directory_uri());
$is_logged_in    = is_user_logged_in();
$logout_url      = esc_url(wp_logout_url($home_url));

// Menu setup
$primary_menu = wp_nav_menu([
    'theme_location' => 'primary',
    'menu_id'        => 'primary-menu',
    'container'      => false,
    'menu_class'     => 'navbar-nav mx-auto mb-2 mb-lg-0',
    'fallback_cb'    => '__return_false',
    'items_wrap'     => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
    'depth'          => 2,
    'walker'         => class_exists('Bootstrap_5_Nav_Walker') ? new Bootstrap_5_Nav_Walker() : null,
    'echo'           => false,
]);
?>
<!doctype html>
<html <?php language_attributes(); ?> class="accessibility-enhanced performance-enhanced security-enhanced">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo esc_attr($site_desc); ?>">
    <meta name="author" content="<?php echo esc_attr($site_name); ?>">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data: https:; font-src 'self' https://cdn.jsdelivr.net; connect-src 'self' https:;">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script>
        window.blackcnoteTheme = {
            ajaxUrl: '<?php echo esc_js(admin_url('admin-ajax.php')); ?>',
            nonce: '<?php echo esc_js($ajax_nonce); ?>',
            csrfToken: '<?php echo esc_js($csrf_token); ?>',
            strings: {
                loading: '<?php echo esc_js(__('Loading...', 'blackcnote')); ?>',
                error: '<?php echo esc_js(__('An error occurred', 'blackcnote')); ?>',
                success: '<?php echo esc_js(__('Success!', 'blackcnote')); ?>'
            },
            apiUrl: '<?php echo esc_js(rest_url('blackcnote/v1/')); ?>',
            liveEditing: <?php echo wp_json_encode(defined('WP_DEBUG') && WP_DEBUG); ?>,
            debug: <?php echo wp_json_encode(defined('WP_DEBUG') && WP_DEBUG); ?>
        };
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class('accessibility-enhanced performance-enhanced security-enhanced mobile-optimized'); ?>>
<?php wp_body_open(); ?>

<!-- BlackCnote React App Container -->
<?php if (!is_admin()) : ?>
    <div id="root" class="blackcnote-react-app">
        <div class="react-loading">
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <p class="loading-text">Loading BlackCnote...</p>
        </div>
    </div>
<?php else : ?>
    <div id="page" class="site accessibility-enhanced performance-enhanced security-enhanced">
        <!-- Accessibility: Skip Links -->
        <a class="skip-link sr-only sr-only-focusable" href="#main-content"><?php esc_html_e('Skip to main content', 'blackcnote'); ?></a>
        <a class="skip-link sr-only sr-only-focusable" href="#primary-menu"><?php esc_html_e('Skip to navigation', 'blackcnote'); ?></a>

        <header id="masthead" class="site-header bg-white border-bottom" role="banner">
            <nav class="navbar navbar-expand-lg navbar-light py-3" role="navigation" aria-label="<?php esc_attr_e('Main navigation', 'blackcnote'); ?>">
                <div class="container mobile-first">
                    <div class="site-branding" role="banner">
                        <?php
                        $logo_path = get_template_directory() . '/assets/img/header-logo.png';
                        if (file_exists($logo_path)) : ?>
                            <a href="<?php echo $home_url; ?>" rel="home" aria-label="<?php echo esc_attr($site_name); ?> - <?php esc_attr_e('Home', 'blackcnote'); ?>">
                                <img src="<?php echo $template_uri . '/assets/img/header-logo.png'; ?>"
                                     alt="<?php echo esc_attr($site_name); ?> Logo"
                                     class="img-fluid responsive-image"
                                     style="max-height:50px;"
                                     loading="lazy">
                            </a>
                        <?php else : ?>
                            <h1 class="site-title mb-0">
                                <a href="<?php echo $home_url; ?>" rel="home" class="text-decoration-none" aria-label="<?php echo esc_attr($site_name); ?> - <?php esc_attr_e('Home', 'blackcnote'); ?>">
                                    <?php echo $site_name; ?>
                                </a>
                            </h1>
                        <?php endif; ?>
                    </div>
                    <!-- Mobile Menu Button -->
                    <button class="navbar-toggler hamburger-menu border-0 shadow-none touch-target"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#primary-menu"
                            aria-controls="primary-menu"
                            aria-expanded="false"
                            aria-label="<?php esc_attr_e('Toggle navigation menu', 'blackcnote'); ?>"
                            role="button">
                        <span class="sr-only"><?php esc_html_e('Toggle navigation', 'blackcnote'); ?></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <div class="collapse navbar-collapse" id="primary-menu" role="navigation" aria-label="<?php esc_attr_e('Primary menu', 'blackcnote'); ?>">
                        <?php echo $primary_menu; ?>
                        <div class="d-flex align-items-center" role="complementary" aria-label="<?php esc_attr_e('User actions', 'blackcnote'); ?>">
                            <?php if ($is_logged_in) : ?>
                                <div class="dropdown" role="menu">
                                    <a href="#"
                                       class="d-flex align-items-center text-decoration-none dropdown-toggle touch-target"
                                       id="userDropdown"
                                       data-bs-toggle="dropdown"
                                       aria-expanded="false"
                                       aria-haspopup="true"
                                       aria-label="<?php esc_attr_e('User menu', 'blackcnote'); ?>"
                                       role="button">
                                        <img src="<?php echo $user_avatar; ?>"
                                             alt="<?php esc_attr_e('User Avatar', 'blackcnote'); ?>"
                                             class="rounded-circle me-2 responsive-image"
                                             width="32"
                                             height="32"
                                             loading="lazy">
                                        <span class="d-none d-lg-inline"><?php echo $user_display; ?></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                                        aria-labelledby="userDropdown"
                                        role="menu">
                                        <li role="none">
                                            <a class="dropdown-item"
                                               href="<?php echo esc_url(home_url('/dashboard')); ?>"
                                               role="menuitem"
                                               aria-label="<?php esc_attr_e('Go to dashboard', 'blackcnote'); ?>">
                                                <i class="bi bi-speedometer2 me-2" aria-hidden="true"></i><?php esc_html_e('Dashboard', 'blackcnote'); ?>
                                            </a>
                                        </li>
                                        <li role="none">
                                            <a class="dropdown-item"
                                               href="<?php echo esc_url(home_url('/profile')); ?>"
                                               role="menuitem"
                                               aria-label="<?php esc_attr_e('Go to profile', 'blackcnote'); ?>">
                                                <i class="bi bi-person me-2" aria-hidden="true"></i><?php esc_html_e('Profile', 'blackcnote'); ?>
                                            </a>
                                        </li>
                                        <li role="none">
                                            <a class="dropdown-item"
                                               href="<?php echo esc_url(home_url('/investments')); ?>"
                                               role="menuitem"
                                               aria-label="<?php esc_attr_e('View investments', 'blackcnote'); ?>">
                                                <i class="bi bi-graph-up me-2" aria-hidden="true"></i><?php esc_html_e('Investments', 'blackcnote'); ?>
                                            </a>
                                        </li>
                                        <li role="none">
                                            <a class="dropdown-item"
                                               href="<?php echo esc_url(home_url('/transactions')); ?>"
                                               role="menuitem"
                                               aria-label="<?php esc_attr_e('View transactions', 'blackcnote'); ?>">
                                                <i class="bi bi-wallet2 me-2" aria-hidden="true"></i><?php esc_html_e('Transactions', 'blackcnote'); ?>
                                            </a>
                                        </li>
                                        <li role="separator"><hr class="dropdown-divider"></li>
                                        <li role="none">
                                            <a class="dropdown-item text-danger"
                                               href="<?php echo $logout_url; ?>"
                                               role="menuitem"
                                               aria-label="<?php esc_attr_e('Logout from account', 'blackcnote'); ?>">
                                                <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i><?php esc_html_e('Logout', 'blackcnote'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            <?php else : ?>
                                <a href="<?php echo esc_url(home_url('/login')); ?>"
                                   class="btn btn-link text-dark text-decoration-none me-3 touch-target"
                                   aria-label="<?php esc_attr_e('Go to login page', 'blackcnote'); ?>"
                                   role="button">
                                    <?php esc_html_e('Login', 'blackcnote'); ?>
                                </a>
                                <a href="<?php echo esc_url(home_url('/register')); ?>"
                                   class="btn btn-warning touch-target"
                                   aria-label="<?php esc_attr_e('Go to registration page', 'blackcnote'); ?>"
                                   role="button">
                                    <?php esc_html_e('Get Started', 'blackcnote'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <main id="main-content" class="site-main" role="main" tabindex="-1">
            <div class="container mobile-first">
<?php endif; // End header/footer toggle ?> 