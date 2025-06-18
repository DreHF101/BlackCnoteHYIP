<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-deposit text-center">
            <div class="card-header card-header-bg">
                <h3><?php esc_html_e('Payment Preview', HYIPLAB_PLUGIN_NAME); ?></h3>
            </div>
            <div class="card-body text-center">
                <h4 class="my-2"><?php esc_html_e('PLEASE SEND EXACTLY', HYIPLAB_PLUGIN_NAME); ?> <span class="text-success"><?php echo esc_html($data->amount); ?></span> <?php echo esc_html($data->currency); ?></h4>
                <h5 class="mb-2"><?php esc_html_e('TO', HYIPLAB_PLUGIN_NAME); ?> <span class="text-success"> <?php echo esc_html($data->sendto); ?></span></h5>
                <img src="<?php echo esc_url($data->img); ?>" alt="Image">
                <h4 class="text-white bold my-4"><?php esc_html_e('SCAN TO SEND', HYIPLAB_PLUGIN_NAME); ?></h4>
            </div>
        </div>
    </div>
</div>