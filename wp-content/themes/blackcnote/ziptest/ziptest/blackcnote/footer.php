<?php
/**
 * The template for displaying the footer
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

    <footer id="colophon" class="site-footer bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="footer-widget">
                        <?php if (is_active_sidebar('footer-1')) : ?>
                            <?php dynamic_sidebar('footer-1'); ?>
                        <?php else : ?>
                            <h3 class="h5 mb-3"><?php bloginfo('name'); ?></h3>
                            <p class="text-muted">
                                <?php bloginfo('description'); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="footer-widget">
                        <?php if (is_active_sidebar('footer-2')) : ?>
                            <?php dynamic_sidebar('footer-2'); ?>
                        <?php else : ?>
                            <h3 class="h5 mb-3"><?php esc_html_e('Quick Links', 'blackcnote'); ?></h3>
                            <?php
                            wp_nav_menu([
                                'theme_location' => 'footer',
                                'menu_class' => 'list-unstyled',
                                'container' => false,
                                'fallback_cb' => '__return_false',
                            ]);
                            ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="footer-widget">
                        <?php if (is_active_sidebar('footer-3')) : ?>
                            <?php dynamic_sidebar('footer-3'); ?>
                        <?php else : ?>
                            <h3 class="h5 mb-3"><?php esc_html_e('Contact Us', 'blackcnote'); ?></h3>
                            <ul class="list-unstyled text-muted">
                                <li class="mb-2">
                                    <i class="bi bi-envelope me-2"></i>
                                    <?php echo esc_html(get_option('admin_email')); ?>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-telephone me-2"></i>
                                    <?php echo esc_html(get_theme_mod('contact_phone', '+1 234 567 890')); ?>
                                </li>
                                <li>
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <?php echo esc_html(get_theme_mod('contact_address', '123 Street Name, City, Country')); ?>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. 
                        <?php esc_html_e('All rights reserved.', 'blackcnote'); ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">
                        <?php
                        printf(
                            /* translators: %s: WordPress */
                            esc_html__('Proudly powered by %s', 'blackcnote'),
                            '<a href="https://wordpress.org/" class="text-muted">WordPress</a>'
                        );
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html> 