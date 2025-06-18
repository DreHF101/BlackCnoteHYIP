<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="text-end mb-3 d-flex flex-wrap justify-content-between gap-1">
            <h3><?php esc_html_e('Pool Plan', HYIPLAB_PLUGIN_NAME); ?></h3>
            <a href="<?php echo hyiplab_route_link('user.pool.invest'); ?>" class="btn btn--base btn--smd">
                <?php esc_html_e('My Pools', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
    </div>
</div>
<div class="row gy-4">
    <?php hyiplab_include('user/partials/pool_plans', ['pools' => $pools]); ?>
</div>