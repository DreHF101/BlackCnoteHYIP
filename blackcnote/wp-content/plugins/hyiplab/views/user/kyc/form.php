<?php hyiplab_layout('user/layouts/master'); ?>
<div class="mb-4">
    <h3><?php esc_html_e('KYC Form', HYIPLAB_PLUGIN_NAME); ?></h3>
</div>


<div class="row gy-4">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <form action="<?php echo esc_url(hyiplab_route_link('user.kyc.submit')); ?>" method="post" enctype="multipart/form-data" >
                    <?php hyiplab_nonce_field('user.kyc.submit'); ?>
                    <?php hyiplab_get_form($form->id); ?>
                    <div class="d-flex flex-wrap justify-content-end mt-3 mt-sm-4 gap-2">
                        <button class="btn btn--sm btn--base"><?php esc_html_e('Submit Now', HYIPLAB_PLUGIN_NAME); ?> <i class="las la-arrow-right fs--12px ms-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



