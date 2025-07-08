<?php
/**
 * The single post template file - React Shell
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

get_header();
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

<?php get_footer(); ?> 