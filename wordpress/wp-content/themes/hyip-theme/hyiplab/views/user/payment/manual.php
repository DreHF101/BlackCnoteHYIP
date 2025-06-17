<?php hyiplab_layout('user/layouts/master'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card custom--card">
                <div class="card-header card-header-bg">
                    <h5 class="card-title mb-0 text-center"><?php echo esc_html($gateway->name); ?> <?php esc_html_e('Payment', HYIPLAB_PLUGIN_NAME); ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo hyiplab_route_link('user.deposit.manual.update'); ?>" method="POST" enctype="multipart/form-data">
                        <?php hyiplab_nonce_field('user.deposit.manual.update'); ?>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <p class="text-center mt-2"><?php esc_html_e('You have requested', HYIPLAB_PLUGIN_NAME); ?> <b class="text-success"><?php echo hyiplab_show_amount($deposit->amount); ?> <?php echo hyiplab_currency('text'); ?></b> , <?php esc_html_e('Please pay', HYIPLAB_PLUGIN_NAME); ?>
                                    <b class="text-success"><?php echo hyiplab_show_amount($deposit->final_amo) . ' ' . esc_html($deposit->method_currency); ?> </b> <?php esc_html_e('for successful payment', HYIPLAB_PLUGIN_NAME); ?>
                                </p>
                                <h4 class="text-center mb-4"><?php esc_html_e('Please follow the instruction below', HYIPLAB_PLUGIN_NAME); ?></h4>
                                <div class="my-4">
                                    <?php echo wp_kses($method->description, hyiplab_allowed_html()); ?>
                                </div>
                            </div>

                            <?php echo hyiplab_get_form($method->form_id); ?>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn--base w-100"><?php esc_html_e('Pay Now', HYIPLAB_PLUGIN_NAME); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>