<?php
/**
 * The template for displaying the footer
 *
 * @package HYIP_Theme
 */

?>

    <footer id="colophon" class="site-footer">
        <div class="footer-content">
            <div class="footer-branding">
                <h2><?php bloginfo('name'); ?></h2>
                <p class="site-description"><?php bloginfo('description'); ?></p>
            </div>

            <div class="footer-navigation">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer',
                        'menu_id'        => 'footer-menu',
                        'depth'          => 1,
                    )
                );
                ?>
            </div>

            <div class="footer-contact">
                <h3><?php esc_html_e('Contact Us', 'hyip-theme'); ?></h3>
                <a href="mailto:<?php echo esc_attr(get_option('admin_email')); ?>"><?php echo esc_html(get_option('admin_email')); ?></a>
            </div>
        </div>

        <div class="footer-legal">
            <div class="legal-disclaimer">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading"><?php esc_html_e('Risk Warning', 'hyip-theme'); ?></h4>
                                <p class="mb-0"><?php esc_html_e('High Yield Investment Programs (HYIPs) involve significant risks. Past performance is not indicative of future results. Please ensure compliance with your local regulations and invest only what you can afford to lose.', 'hyip-theme'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="site-info">
                <p>
                    <?php
                    printf(
                        esc_html__('© %1$s %2$s. All rights reserved.', 'hyip-theme'),
                        date('Y'),
                        get_bloginfo('name')
                    );
                    ?>
                </p>
                <p class="wordpress-credit">
                    <?php
                    printf(
                        esc_html__('Proudly powered by %s', 'hyip-theme'),
                        '<a href="' . esc_url(__('https://wordpress.org/', 'hyip-theme')) . '">WordPress</a>'
                    );
                    ?>
                </p>
                <p class="hyiplab-credit">
                    <?php
                    printf(
                        esc_html__('Theme by %s', 'hyip-theme'),
                        '<a href="' . esc_url('https://hyiplab.com/') . '">HYIPLab</a>'
                    );
                    ?>
                </p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html> 