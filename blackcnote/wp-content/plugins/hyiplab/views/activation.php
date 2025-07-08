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

<body <?php body_class('vl-public vl-activation'); ?>>

<div class="installation-section padding-bottom padding-top">
		<div class="container">
			<div class="installation-wrapper">
				<div class="install-content-area">
					<div class="install-item">
						<h3 class="title text-center"><?php esc_html_e(hyiplab_system_details()['real_name'],HYIPLAB_PLUGIN_NAME); ?> <?php esc_html_e('License Activation') ?></h3>
                        <div class="box-item">
                            <div class="row mt-lg-4">
                                <div class="col-12">
                                    <div class="alert-area d-none">
                                        <div class="alert alert-danger d-block">
                                            <h5 class="resp-msg"></h5>
                                            <p class="my-3"><?php esc_html_e('You can ask for support by creating a support ticket.',HYIPLAB_PLUGIN_NAME)?></p>
                                            <a href="https://viserlab.com/support" class="btn btn-outline-light btn-sm" target="_blank"><?php esc_html_e('create ticket',HYIPLAB_PLUGIN_NAME) ?></a>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div class="alert alert-success" role="alert">
                                        <p class="fs-17 mb-0"><?php esc_html_e('To validate your purchase details, following information will sent to ViserLab server.',HYIPLAB_PLUGIN_NAME) ?></p>
                                    </div>
                                    <div class="alert alert-primary flex-column" role="alert">
                                        <p class="fs-17"><?php esc_html_e('Application',HYIPLAB_PLUGIN_NAME) ?>: <?php esc_html_e(hyiplab_system_details()['real_name'],HYIPLAB_PLUGIN_NAME); ?> - v<?php esc_html_e(hyiplab_system_details()['version'],HYIPLAB_PLUGIN_NAME); ?></p>
                                        <p class="fs-17"><?php esc_html_e('Envato Username',HYIPLAB_PLUGIN_NAME) ?>: <span class="envato_username"></span></p>
                                        <p class="fs-17"><?php esc_html_e('Purchase Code',HYIPLAB_PLUGIN_NAME)?>: <span class="purchase_code"></span></p>
                                        <p class="fs-17"><?php esc_html_e('Your Email',HYIPLAB_PLUGIN_NAME)?>: <span class="email"></span></p>
                                        <p class="fs-17 mb-0 word-break-all"><?php esc_html_e('Activation URL',HYIPLAB_PLUGIN_NAME) ?>: <?php echo home_url();?></p>
                                    </div>
                                    <div class="alert alert-warning" role="alert">
                                        <p class="fs-17 mb-0"><?php esc_html_e('We never collect any sensitive or confidential data.',HYIPLAB_PLUGIN_NAME) ?></p>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <p><?php esc_html_e('The purchase code(license) is for one website or domain only. Please activate the license into the correct domain(URL) to avoid any unwanted issues in the future.',HYIPLAB_PLUGIN_NAME)?></p>
                                    <p>
                                        <?php esc_html_e('To get the purchase code',HYIPLAB_PLUGIN_NAME) ?> <a class="text--base" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"> <?php esc_html_e('click here',HYIPLAB_PLUGIN_NAME)?></a>.
                                    </p>
                                    <form class="verForm">
                                        <div class="information-form-group">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <label for="purchase_code" class="mb-1"><?php esc_html_e('Enter Purchase Code',HYIPLAB_PLUGIN_NAME)?> <span class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                            <input type="text" name="purchase_code" id="purchase_code" required="">
                                        </div>
                                        <div class="information-form-group">
                                            <label for="username" class="mb-1"><?php esc_html_e('Enter Envato Username',HYIPLAB_PLUGIN_NAME) ?> <span class="text-danger">*</span></label>
                                            <input type="text" name="envato_username" id="username" required="">
                                        </div>
                                        <div class="information-form-group">
                                            <label for="email" class="mb-1"><?php esc_html_e('Enter Your Email',HYIPLAB_PLUGIN_NAME) ?> <span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="email" required="">
                                        </div>

                                        <div class="information-form-group d-flex align-items-start">
                                            <input type="checkbox" id="agree" class="checkbox w-auto h-auto mt-1" required="">
                                            <label for="agree" class="agree-label"><?php esc_html_e('I accept the terms of the',HYIPLAB_PLUGIN_NAME) ?> <a href="https://codecanyon.net/licenses/standard" class="text--base" target="_blank"><?php esc_html_e('Envato Standard License',HYIPLAB_PLUGIN_NAME) ?></a> <?php esc_html_e('as well as the Viserlab terms and conditions.',HYIPLAB_PLUGIN_NAME) ?></label>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="theme-button choto sbmBtn"><?php esc_html_e('Activate Now',HYIPLAB_PLUGIN_NAME) ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>




    
    <script>
        jQuery(document).ready(function($) {
            "use strict"
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            $(function () {
                $('[data-bs-toggle="tooltip"]').tooltip({
                    animated: 'fade',
                    trigger: 'click'
                })
            });

            $('[name=email]').on('input', function () {
                $('.email').text($(this).val());
            });
            $('[name=envato_username]').on('input', function () {
                $('.envato_username').text($(this).val());
            });
            $('[name=purchase_code]').on('input', function () {
                $('.purchase_code').text($(this).val());
            });

            $('.verForm').submit(function (e) {
                e.preventDefault();
                $('.alert-area').addClass('d-none');
                $('.sbmBtn').text('Processing...');
                var url = "<?php echo admin_url('/admin-ajax.php') ?>";
                var data = {
                    "purchase_code":$(this).find('[name=purchase_code]').val(),
                    "email":$(this).find('[name=email]').val(),
                    "envato_username":$(this).find('[name=envato_username]').val(),
                    "action":"active_plugin"
                };

                $.post(url, data,function (response) {
                    if (response.type == 'error') {
                        $('.sbmBtn').text('Submit');
                        $('.verForm').trigger("reset");
                        $('.alert-area').removeClass('d-none');
                        $('.resp-msg').text(response.message);
                    }else{
                        window.location.href = "<?php echo admin_url('/admin.php?page='.HYIPLAB_PLUGIN_NAME) ?>";
                    }
                });
            });
        });
    </script>

    <?php hyiplab_include('partials/notify'); ?>

    <?php wp_footer(); ?>
</body>

</html>