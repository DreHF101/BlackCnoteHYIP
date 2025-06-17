<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card custom--card">
            <div class="card-header">
                <h3><?php esc_html_e('Withdraw Confirmation', HYIPLAB_PLUGIN_NAME); ?></h3>
            </div>
            <div class="card-body">
                <form action="<?php echo hyiplab_route_link('user.withdraw.submit'); ?>" method="post" enctype="multipart/form-data">
                    <?php hyiplab_nonce_field('user.withdraw.submit');?>
                    <div class="mb-2">
                        <?php echo wp_kses($method->description, hyiplab_allowed_html()); ?>
                    </div>

                    <?php echo hyiplab_get_form($method->form_id); ?>

                    <div class="form-group">
                        <button type="submit" class="btn btn--base w-100"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>