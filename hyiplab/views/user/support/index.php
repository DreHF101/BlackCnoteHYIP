<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="text-end mb-3 d-flex flex-wrap justify-content-between gap-1">
            <h3><?php esc_html_e('Support Tickets', HYIPLAB_PLUGIN_NAME); ?></h3>
            <a href="<?php echo hyiplab_route_link('user.ticket.create'); ?>" class="btn btn--base btn--smd">
                <?php esc_html_e('Open Support Ticket', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--responsive--md">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Subject', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Priority', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Last Reply', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($supports->data as $support) { ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo hyiplab_route_link('user.ticket.view'); ?>?id=<?php echo intval($support->ticket); ?>" class="fw-bold">
                                            [<?php esc_html_e('Ticket', HYIPLAB_PLUGIN_NAME); ?>#<?php echo esc_html($support->ticket); ?>] <?php echo esc_html($support->subject); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php
                                        $html = '';
                                        if ($support->status == 0) {
                                            $html = '<span class="badge badge--success">' . esc_html__("Open", HYIPLAB_PLUGIN_NAME) . '</span>';
                                        } elseif ($support->status == 1) {
                                            $html = '<span class="badge badge--primary">' . esc_html__("Answered", HYIPLAB_PLUGIN_NAME) . '</span>';
                                        } elseif ($support->status == 2) {
                                            $html = '<span class="badge badge--warning">' . esc_html__("Customer Reply", HYIPLAB_PLUGIN_NAME) . '</span>';
                                        } elseif ($support->status == 3) {
                                            $html = '<span class="badge badge--dark">' . esc_html__("Closed", HYIPLAB_PLUGIN_NAME) . '</span>';
                                        }
                                        echo wp_kses($html, hyiplab_allowed_html());
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($support->priority == 1) { ?>
                                            <span class="badge badge--dark"><?php esc_html_e('Low', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } elseif ($support->priority == 2) { ?>
                                            <span class="badge  badge--warning"><?php esc_html_e('Medium', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } elseif ($support->priority == 3) { ?>
                                            <span class="badge badge--primary"><?php esc_html_e('High', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo hyiplab_diff_for_humans($support->last_reply); ?></td>
                                    <td>
                                        <a href="<?php echo hyiplab_route_link('user.ticket.view'); ?>?id=<?php echo intval($support->ticket); ?>" class="btn btn--icon btn--primary">
                                            <i class="fa fa-desktop"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (hyiplab_check_empty($supports->data)) { ?>
                <div class="card-body text-center">
                    <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
                </div>
            <?php } ?>
            <?php if ($supports->links) { ?>
                <div class="card-footer">
                    <?php echo wp_kses($supports->links, hyiplab_allowed_html()); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>