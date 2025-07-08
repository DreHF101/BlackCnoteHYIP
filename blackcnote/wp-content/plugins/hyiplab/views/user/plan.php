<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row mb-4">
    <div class="col-lg-8">
        <h3 class="mb-2"><?php esc_html_e('Investment Plan', HYIPLAB_PLUGIN_NAME); ?></h3>
    </div>
</div>
<div class="row gy-4">
    <?php hyiplab_include('user/partials/plans', ['plans' => $plans]); ?>
</div>