<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two custom-data-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Method', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Currency', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Charge', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Withdraw Limit', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($methods as $method) { ?>
                                <tr>
                                    <td><?php echo esc_html($method->name); ?></td>
                                    <td class="fw-bold"><?php echo esc_html($method->currency); ?></td>
                                    <td>
                                        <span class="fw-bold"><?php echo hyiplab_show_amount($method->fixed_charge) . ' ' . hyiplab_currency('text'); ?>
                                            <?php echo ($method->percent_charge > 0) ? ' + ' . hyiplab_show_amount($method->percent_charge) . ' %' : ''; ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            <?php echo hyiplab_show_amount($method->min_limit); ?> - <?php echo hyiplab_show_amount($method->max_limit); ?> <?php echo hyiplab_currency('text'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        if ($method->status == 1) {
                                            echo '<span class="badge badge--success">' . esc_html__("Active", HYIPLAB_PLUGIN_NAME) . '</span';
                                        } else {
                                            echo '<span class="badge badge--warning">' . esc_html__("Inactive", HYIPLAB_PLUGIN_NAME) . '</span';
                                        } ?>
                                    </td>
                                    <td>
                                        <div class="button--group"> 
                                            <a href="<?php echo hyiplab_route_link('admin.withdraw.method.edit'); ?>&amp;id=<?php echo intval($method->id); ?>" class="btn btn-sm btn-outline--primary ms-1"><i class="las la-pen"></i> <?php esc_html_e('Edit', HYIPLAB_PLUGIN_NAME); ?></a>
                                            <?php if ($method->status == 1) { ?>
                                                <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="<?php esc_attr_e('Are you sure to disable this method?', HYIPLAB_PLUGIN_NAME); ?>" data-action="<?php echo hyiplab_route_link('admin.withdraw.method.status'); ?>&amp;id=<?php echo intval($method->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.withdraw.method.status'));?>">
                                                    <i class="la la-eye-slash"></i> <?php esc_html_e('Disable', HYIPLAB_PLUGIN_NAME); ?>
                                                </button>
                                            <?php } else { ?>
                                                <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-question="<?php esc_attr_e('Are you sure to enable this method?', HYIPLAB_PLUGIN_NAME); ?>" data-action="<?php echo hyiplab_route_link('admin.withdraw.method.status'); ?>&amp;id=<?php echo intval($method->id); ?>" data-nonce="<?php echo esc_attr(hyiplab_nonce('admin.withdraw.method.status'));?>">
                                                    <i class="la la-eye"></i> <?php esc_html_e('Enable', HYIPLAB_PLUGIN_NAME); ?>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php if (hyiplab_check_empty($methods)) { ?>
                                <tr>
                                    <td class="text-muted text-center" colspan="100%"><?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div><!-- card end -->
    </div>
</div>
 
<?php

hyiplab_include('partials/confirmation');
$html = '<a class="btn btn-outline--primary" href="' . hyiplab_route_link('admin.withdraw.method.create') . '"><i class="las la-plus"></i>' . esc_html__("Add New", HYIPLAB_PLUGIN_NAME) . '</a>';
hyiplab_push_breadcrumb($html);

?>