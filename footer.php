<?php
/**
 * The template for displaying the footer
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

    <footer id="colophon" class="site-footer bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="footer-widget">
                        <h3 class="fw-bold mb-3">BLACKCNOTE</h3>
                        <p class="text-muted small mb-3">Empowering Black communities through strategic investments and wealth circulation. Building generational wealth by 2040.</p>
                        <div class="d-flex gap-2">
                            <a href="#" class="text-light"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="text-light"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="text-light"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="text-light"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <div class="footer-widget">
                        <h5 class="fw-bold mb-3">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>" class="text-light">Home</a></li>
                            <li><a href="<?php echo esc_url(home_url('/plans')); ?>" class="text-light">Investment Plans</a></li>
                            <li><a href="<?php echo esc_url(home_url('/calculator')); ?>" class="text-light">Profit Calculator</a></li>
                            <li><a href="<?php echo esc_url(home_url('/about')); ?>" class="text-light">About Us</a></li>
                            <li><a href="<?php echo esc_url(home_url('/contact')); ?>" class="text-light">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="footer-widget">
                        <h5 class="fw-bold mb-3">Services</h5>
                        <ul class="list-unstyled">
                            <li>High-Yield Investment Programs</li>
                            <li>Crowdfunding Opportunities</li>
                            <li>Financial Education</li>
                            <li>Community Investment</li>
                            <li>Wealth Building Strategies</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="footer-widget">
                        <h5 class="fw-bold mb-3">Contact Info</h5>
                        <ul class="list-unstyled text-muted">
                            <li class="mb-2"><i class="bi bi-envelope me-2"></i> info@blackcnote.com</li>
                            <li class="mb-2"><i class="bi bi-telephone me-2"></i> +1 (555) 123-4567</li>
                            <li><i class="bi bi-geo-alt me-2"></i> Atlanta, GA, USA</li>
                        </ul>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-muted small">&copy; <?php echo date('Y'); ?> BlackCnote. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted small">
                        <a href="#" class="text-muted">Privacy Policy</a> |
                        <a href="#" class="text-muted">Terms of Service</a> |
                        <a href="#" class="text-muted">Risk Disclaimer</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html> 