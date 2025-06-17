<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    <link rel="shortcut icon" href="<?php echo esc_url(hyiplab_asset('global/images/favicon.png')); ?>" type="image/x-icon">
    <?php wp_head(); ?>
</head>

<body <?php body_class('vl-public'); ?>>

    <section class="account-section position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-8">

                    <a href="<?php echo home_url('/'); ?>" class="text-center d-block mb-3 mb-sm-4 auth-page-logo">
                        <img src="<?php echo hyiplab_asset('global/images/logo.png'); ?>" alt="logo">
                    </a>

                    {{yield}}

                </div>
            </div>
        </div>
    </section>

    <script>
        jQuery(document).ready(function($) {
            "use strict"
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>

    <?php hyiplab_include('partials/notify'); ?>

    <?php wp_footer(); ?>
</body>

</html>