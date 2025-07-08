
/**
 * The main template file - React App Integration
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

<!-- BlackCnote React App Container -->
<?php blackcnote_add_react_container(); ?>

<!-- React App Fallback (if JavaScript is disabled) -->
<noscript>
    <div class="react-fallback">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1>Welcome to BlackCnote</h1>
                    <p>This application requires JavaScript to function properly.</p>
                    <p>Please enable JavaScript in your browser to continue.</p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                        Refresh Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</noscript>

<?php get_footer(); 