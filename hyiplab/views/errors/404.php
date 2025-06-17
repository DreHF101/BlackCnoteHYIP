<?php if (!is_admin()) { ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    <link rel="shortcut icon" href="<?php echo hyiplab_get_image(hyiplab_file_path('logoIcon') . '/favicon.png'); ?>"
        type="image/x-icon">
    <?php wp_head(); ?>
</head>
<body <?php body_class('vl-public'); ?>>
    <?php } ?>
    <div class="vl-error">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 text-center">
                    <img src="<?php echo esc_url(hyiplab_asset('global/images/404.png')); ?>" alt="image">
                    <h2><b><?php esc_html_e('404', HYIPLAB_PLUGIN_NAME); ?></b>
                        <?php esc_html_e('Page not found', HYIPLAB_PLUGIN_NAME); ?></h2>
                    <p><?php esc_html_e('page you are looking for doesn\'t exit or an other error ocurred'); ?> <br>
                        <?php esc_html_e('or temporarily unavailable.', HYIPLAB_PLUGIN_NAME); ?>
                    </p>
                    <br />
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary btn-sm mt-3">
                        <?php esc_html_e('Go to Home', HYIPLAB_PLUGIN_NAME); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php if (!is_admin()) { ?>
    <?php wp_footer(); ?>
</body>
</html>
<?php } ?>
<?php exit; ?>