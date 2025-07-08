<div class="d-flex mb-15 flex-wrap gap-3 justify-content-between align-items-center">
    <h6 class="page-title"><?php esc_html_e(isset($pageTitle) ? $pageTitle : 'Dashboard', HYIPLAB_PLUGIN_NAME); ?></h6>
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
        <?php do_action('hyiplab_breadcrumb_plugins') ?>
    </div>
</div>