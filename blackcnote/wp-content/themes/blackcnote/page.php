<?php
/**
 * The page template file - React Shell
 * 
 * This template serves as a shell for the React app.
 * All content rendering is handled by React components.
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Check if we should render WordPress header/footer
$render_wp_header_footer = !function_exists('blackcnote_should_render_wp_header_footer') || blackcnote_should_render_wp_header_footer();

if ($render_wp_header_footer) {
    get_header();
} else {
    // React-only mode: provide minimal HTML structure
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php wp_title('|', true, 'right'); ?></title>
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <?php
}
?>

<!-- BlackCnote React App Root -->
<div id="root" class="blackcnote-react-app">
    <!-- React app will render here -->
    <div class="react-loading">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <p class="loading-text">Loading BlackCnote...</p>
    </div>
</div>

<!-- React App Fallback (if JavaScript is disabled) -->
<noscript>
    <div class="react-fallback">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1><?php echo esc_html(get_the_title()); ?></h1>
                    <p>This application requires JavaScript to function properly.</p>
                    <p>Please enable JavaScript in your browser to continue.</p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                        Go Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</noscript>

<?php 
if ($render_wp_header_footer) {
    get_footer();
} else {
    // React-only mode: close minimal HTML structure
    wp_footer();
    ?>
    </body>
    </html>
    <?php
}
?> 