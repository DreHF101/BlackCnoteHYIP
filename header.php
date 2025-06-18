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

    <header id="masthead" class="site-header bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light py-3">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/header-logo.png'); ?>" alt="BlackCnote Logo" style="height:48px;" class="me-2">
                    <span class="fw-bold fs-4 text-dark">BlackCnote</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                        <li class="nav-item"><a class="nav-link" href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo esc_url(home_url('/plans')); ?>">Investment Plans</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo esc_url(home_url('/calculator')); ?>">Profit Calculator</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
                    </ul>
                    <div class="d-flex ms-lg-4 mt-3 mt-lg-0 gap-2">
                        <?php if (is_user_logged_in()) : ?>
                            <a href="<?php echo esc_url(home_url('/dashboard')); ?>" class="btn btn-warning px-4">Dashboard</a>
                        <?php else : ?>
                            <a href="<?php echo esc_url(home_url('/login')); ?>" class="btn btn-outline-warning px-4">Login</a>
                            <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-warning px-4">Get Started</a>
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
        $output .= '<ul class="dropdown-menu">';
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