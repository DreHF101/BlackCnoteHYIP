<?php
/**
 * The header for our theme
 *
 * @package BlackCnote_Theme
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
        <?php esc_html_e('Skip to content', 'blackcnote-theme'); ?>
    </a>

    <header id="masthead" class="site-header bg-light">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <div class="site-branding">
                    <?php
                    if (has_custom_logo()) :
                        the_custom_logo();
                    else :
                        ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description || is_customize_preview()) :
                            ?>
                            <p class="site-description text-muted">
                                <?php echo $description; ?>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primary-menu" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'blackcnote-theme'); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="primary-menu">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'menu_id' => 'primary-menu',
                        'container' => false,
                        'menu_class' => 'navbar-nav ms-auto mb-2 mb-lg-0',
                        'fallback_cb' => '__return_false',
                        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'depth' => 2,
                        'walker' => new Bootstrap_5_Nav_Walker(),
                    ]);
                    ?>

                    <?php if (is_user_logged_in()) : ?>
                        <div class="ms-lg-3">
                            <a href="<?php echo esc_url(home_url('/dashboard')); ?>" class="btn btn-primary">
                                <?php esc_html_e('Dashboard', 'blackcnote-theme'); ?>
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="ms-lg-3">
                            <a href="<?php echo esc_url(home_url('/login')); ?>" class="btn btn-outline-primary me-2">
                                <?php esc_html_e('Login', 'blackcnote-theme'); ?>
                            </a>
                            <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-primary">
                                <?php esc_html_e('Register', 'blackcnote-theme'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
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