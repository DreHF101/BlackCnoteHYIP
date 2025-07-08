<?php hyiplab_layout('user/layouts/master'); ?>
<div class="mb-4">
    <p><?php esc_html_e('Investment', HYIPLAB_PLUGIN_NAME); ?></p>
    <h3><?php esc_html_e('My Investment Statistics', HYIPLAB_PLUGIN_NAME); ?></h3>
</div>

<div class="mt-4">
    <div class="plan-list d-flex flex-wrap flex-xxl-column gap-3 gap-xxl-0">
        <?php
            hyiplab_include('user/partials/invest_history', ['invests' => $invests->data]);
        ?>
        <?php if (hyiplab_check_empty($invests->data)) { ?>
            <div class="accordion-body text-center bg-white p-4">
                <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
            </div>
        <?php } ?>
    </div>
    <?php if ($invests->links) { ?>
        <div class="mt-3">
            <?php echo wp_kses($invests->links, hyiplab_allowed_html()); ?>
        </div>
    <?php } ?>
</div>

