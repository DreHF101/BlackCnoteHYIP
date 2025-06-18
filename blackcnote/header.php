<?php
/**
 * The header for our theme
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary">
        <?php esc_html_e('Skip to content', 'blackcnote'); ?>
    </a>

    <header id="masthead" class="site-header bg-white border-bottom">
        <nav class="navbar navbar-expand-lg navbar-light py-3">
            <div class="container">
                <div class="site-branding">
                    <?php
                    if (file_exists(get_template_directory() . '/assets/img/header-logo.png')) : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/header-logo.png'); ?>" alt="<?php bloginfo('name'); ?> Logo" class="img-fluid" style="max-height:50px;">
                        </a>
                    <?php else : ?>
                        <h1 class="site-title mb-0">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="text-decoration-none">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                    <?php endif; ?>
                </div>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#primary-menu" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'blackcnote'); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="primary-menu">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'menu_id' => 'primary-menu',
                        'container' => false,
                        'menu_class' => 'navbar-nav mx-auto mb-2 mb-lg-0',
                        'fallback_cb' => '__return_false',
                        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'depth' => 2,
                        'walker' => new Bootstrap_5_Nav_Walker(),
                    ]);
                    ?>

                    <div class="d-flex align-items-center">
                        <?php if (is_user_logged_in()) : ?>
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo esc_url(get_avatar_url(get_current_user_id())); ?>" alt="User Avatar" class="rounded-circle me-2" width="32" height="32">
                                    <span class="d-none d-lg-inline"><?php echo esc_html(wp_get_current_user()->display_name); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="<?php echo esc_url(home_url('/dashboard')); ?>">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo esc_url(home_url('/profile')); ?>">
                                        <i class="bi bi-person me-2"></i>Profile
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo esc_url(home_url('/investments')); ?>">
                                        <i class="bi bi-graph-up me-2"></i>Investments
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo esc_url(home_url('/transactions')); ?>">
                                        <i class="bi bi-wallet2 me-2"></i>Transactions
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="<?php echo esc_url(wp_logout_url(home_url())); ?>">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </a></li>
                                </ul>
                            </div>
                        <?php else : ?>
                            <a href="<?php echo esc_url(home_url('/login')); ?>" class="btn btn-link text-dark text-decoration-none me-3">
                                <?php esc_html_e('Login', 'blackcnote'); ?>
                            </a>
                            <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-warning">
                                <?php esc_html_e('Get Started', 'blackcnote'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

<?php
/**
 * Bootstrap 5 Nav Walker
 */
class Bootstrap_5_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= '<ul class="dropdown-menu shadow-sm border-0">';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $item_html = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        
        if (in_array('menu-item-has-children', $classes)) {
            $item_html .= '<li class="nav-item dropdown">';
            $item_html .= '<a class="nav-link dropdown-toggle" href="' . esc_url($item->url) . '" data-bs-toggle="dropdown" aria-expanded="false">' . esc_html($item->title) . '</a>';
        } else {
            $item_html .= '<li class="nav-item">';
            $item_html .= '<a class="nav-link" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        }

        $output .= $item_html;
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</ul>';
    }
} 