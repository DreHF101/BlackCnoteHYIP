hp
/**
 * The footer for the BlackCnote theme
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Only render footer if allowed by theme settings
if (!function_exists('blackcnote_should_render_wp_header_footer') || blackcnote_should_render_wp_header_footer()) :
?>
        </div><!-- .container.mobile-first -->
    </main><!-- #main-content -->

    <footer id="colophon" class="site-footer bg-dark text-white pt-5 pb-3" role="contentinfo">
        <div class="container">
            <div class="row">
                <!-- Branding and Mission -->
                <div class="col-md-4 mb-4">
                    <div class="mb-3">
                        <?php
                        $logo_path = get_template_directory() . '/assets/img/header-logo.png';
                        if (file_exists($logo_path)) : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/header-logo.png'); ?>"
                                 alt="<?php esc_attr_e('BlackCnote Logo', 'blackcnote'); ?>"
                                 class="img-fluid mb-2"
                                 style="max-height:40px;">
                        <?php endif; ?>
                    </div>
                    <p class="text-muted small">
                        <?php esc_html_e('Empowering Black communities through strategic investments and wealth circulation. Building generational wealth by 2040.', 'blackcnote'); ?>
                    </p>
                    <div>
                        <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-2"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <!-- Quick Links -->
                <div class="col-md-2 mb-4">
                    <h6 class="text-uppercase text-yellow-500 mb-3"><?php esc_html_e('Quick Links', 'blackcnote'); ?></h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="text-white text-decoration-none"><?php esc_html_e('Home', 'blackcnote'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/investment-plans')); ?>" class="text-white text-decoration-none"><?php esc_html_e('Investment Plans', 'blackcnote'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/calculator')); ?>" class="text-white text-decoration-none"><?php esc_html_e('Profit Calculator', 'blackcnote'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/about')); ?>" class="text-white text-decoration-none"><?php esc_html_e('About Us', 'blackcnote'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>" class="text-white text-decoration-none"><?php esc_html_e('Contact', 'blackcnote'); ?></a></li>
                    </ul>
                </div>
                <!-- Services -->
                <div class="col-md-3 mb-4">
                    <h6 class="text-uppercase text-yellow-500 mb-3"><?php esc_html_e('Services', 'blackcnote'); ?></h6>
                    <ul class="list-unstyled text-muted small">
                        <li><?php esc_html_e('High-Yield Investment Programs', 'blackcnote'); ?></li>
                        <li><?php esc_html_e('Crowdfunding Opportunities', 'blackcnote'); ?></li>
                        <li><?php esc_html_e('Financial Education', 'blackcnote'); ?></li>
                        <li><?php esc_html_e('Community Investment', 'blackcnote'); ?></li>
                        <li><?php esc_html_e('Wealth Building Strategies', 'blackcnote'); ?></li>
                    </ul>
                </div>
                <!-- Contact Info -->
                <div class="col-md-3 mb-4">
                    <h6 class="text-uppercase text-yellow-500 mb-3"><?php esc_html_e('Contact Info', 'blackcnote'); ?></h6>
                    <ul class="list-unstyled text-muted small">
                        <li><i class="bi bi-envelope me-2"></i> info@blackcnote.com</li>
                        <li><i class="bi bi-telephone me-2"></i> +1 (555) 123-4567</li>
                        <li><i class="bi bi-geo-alt me-2"></i> Atlanta, GA, USA</li>
                    </ul>
                </div>
            </div>
            <div class="border-top border-secondary mt-4 pt-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="mb-0 text-muted small">&copy; <?php echo date('Y'); ?> BlackCnote. <?php esc_html_e('All rights reserved.', 'blackcnote'); ?></p>
                <div>
                    <a href="<?php echo esc_url(home_url('/privacy')); ?>" class="text-muted text-decoration-none me-3"><?php esc_html_e('Privacy Policy', 'blackcnote'); ?></a>
                    <a href="<?php echo esc_url(home_url('/terms')); ?>" class="text-muted text-decoration-none me-3"><?php esc_html_e('Terms of Service', 'blackcnote'); ?></a>
                    <a href="<?php echo esc_url(home_url('/disclaimer')); ?>" class="text-muted text-decoration-none"><?php esc_html_e('Risk Disclaimer', 'blackcnote'); ?></a>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
<?php endif; // End header/footer toggle 