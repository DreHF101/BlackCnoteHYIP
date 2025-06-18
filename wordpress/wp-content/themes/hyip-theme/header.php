<?php
/**
 * The header for our theme
 *
 * @package HYIP_Theme
 */

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
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'hyip-theme'); ?></a>

    <header id="masthead" class="site-header">
        <div class="container">
            <div class="site-branding">
                <?php
                if (has_custom_logo()) :
                    the_custom_logo();
                else :
                    ?>
                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                    <?php
                    $hyip_theme_description = get_bloginfo('description', 'display');
                    if ($hyip_theme_description || is_customize_preview()) :
                        ?>
                        <p class="site-description"><?php echo $hyip_theme_description; ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <nav id="site-navigation" class="main-navigation">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e('Primary Menu', 'hyip-theme'); ?></button>
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                    )
                );
                ?>
            </nav>

            <?php if (hyip_is_hyiplab_active()) : ?>
            <div class="hyiplab-user-menu">
                <?php if (is_user_logged_in()) : ?>
                    <a href="<?php echo esc_url(home_url('/dashboard')); ?>" class="btn btn-primary"><?php esc_html_e('Dashboard', 'hyip-theme'); ?></a>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/login')); ?>" class="btn btn-outline-primary"><?php esc_html_e('Login', 'hyip-theme'); ?></a>
                    <a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-primary"><?php esc_html_e('Register', 'hyip-theme'); ?></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </header>
</body>
</html> 