<?php hyiplab_layout('user/layouts/auth'); ?>
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card custom--card">
            <div class="card-body">
                <h3 class="text-center text-danger mb-3"><?php esc_html_e('You are banned', HYIPLAB_PLUGIN_NAME); ?></h3>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="<?php echo hyiplab_route_link('user.logout'); ?>" class="text--primary fw-bold">
                <?php esc_html_e('Logout', HYIPLAB_PLUGIN_NAME);?>
            </a>
        </div>
    </div>
</div>