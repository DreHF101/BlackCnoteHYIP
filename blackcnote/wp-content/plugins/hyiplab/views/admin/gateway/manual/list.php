<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two custom-data-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Gateway', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gateways as $k => $gateway) { ?>
                                <tr>
                                    <td><?php echo esc_html($gateway->name); ?></td>
                                    <td>
                                        <?php
                                        if ($gateway->status == 1) {
                                            echo '<span class="text--small badge badge--success">' . esc_html__("Enabled", HYIPLAB_PLUGIN_NAME) . '</span';
                                        } else {
                                            echo '<span class="text--small badge badge--warning">' . esc_html__("Disabled", HYIPLAB_PLUGIN_NAME) . '</span';
                                        } ?>
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <a href="<?php echo hyiplab_route_link('admin.gateway.manual.edit'); ?>&amp;id=<?php echo intval($gateway->id); ?>" class="btn btn-sm btn-outline--primary editGatewayBtn">
                                                <i class="la la-pencil"></i> <?php esc_html_e('Edit', HYIPLAB_PLUGIN_NAME); ?>
                                            </a>
                                            <?php if ($gateway->status == 0) { ?>
                                                <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="<?php esc_attr_e('Are you sure to enable this gateway?', HYIPLAB_PLUGIN_NAME); ?>" data-action="<?php echo hyiplab_route_link('admin.gateway.manual.status'); ?>&amp;id=<?php echo intval($gateway->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.gateway.manual.status'));?>">
                                                    <i class="la la-eye"></i> <?php esc_html_e('Enable', HYIPLAB_PLUGIN_NAME); ?>
                                                </button>
                                            <?php } else { ?>
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="<?php esc_attr_e('Are you sure to disable this gateway?', HYIPLAB_PLUGIN_NAME); ?>" data-action="<?php echo hyiplab_route_link('admin.gateway.manual.status'); ?>&amp;id=<?php echo intval($gateway->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.gateway.manual.status'));?>">
                                                    <i class="la la-eye-slash"></i> <?php esc_html_e('Disable', HYIPLAB_PLUGIN_NAME); ?>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (hyiplab_check_empty($gateways)) { ?>
                                <tr>
                                    <td class="text-muted text-center" colspan="100%"><?php esc_html_e('Data Not found', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div><!-- card end -->
    </div>
</div>

<?php hyiplab_include('partials/confirmation'); ?>

<?php
$html = '<a class="btn btn-outline--primary" href="' . hyiplab_route_link('admin.gateway.manual.create') . '"><i class="las la-plus"></i>' . esc_html__("Add New", HYIPLAB_PLUGIN_NAME) . '</a>';
hyiplab_push_breadcrumb($html);
?>