<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Subject', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Submitted By', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Priority', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Last Reply', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items->data as $item) {
                                $user = get_userdata($item->user_id);
                            ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo hyiplab_route_link('admin.ticket.view'); ?>&amp;id=<?php echo intval($item->id); ?>" class="fw-bold">
                                            [<?php esc_html_e('Ticket', HYIPLAB_PLUGIN_NAME); ?># <?php echo esc_html($item->ticket); ?>] <?php echo mb_strimwidth($item->subject, 0, 30, '...'); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($item->user_id) { ?>
                                            <a href="<?php echo hyiplab_route_link('admin.users.detail');?>&amp;id=<?php echo intval($user->ID); ?>">
                                                <?php echo esc_html($user->display_name); ?>
                                            </a>
                                        <?php } else { ?>
                                            <p class="fw-bold"><?php esc_html($item->name); ?></p>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php
                                        $html = '';
                                        if ($item->status == 0) {
                                            $html = '<span class="badge badge--success">' . esc_html__("Open", HYIPLAB_PLUGIN_NAME) . '</span>';
                                        } elseif ($item->status == 1) {
                                            $html = '<span class="badge badge--primary">' . esc_html__("Answered", HYIPLAB_PLUGIN_NAME) . '</span>';
                                        } elseif ($item->status == 2) {
                                            $html = '<span class="badge badge--warning">' . esc_html__("Customer Reply", HYIPLAB_PLUGIN_NAME) . '</span>';
                                        } elseif ($item->status == 3) {
                                            $html = '<span class="badge badge--dark">' . esc_html__("Closed", HYIPLAB_PLUGIN_NAME) . '</span>';
                                        }
                                        echo wp_kses($html, hyiplab_allowed_html());
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($item->priority == 1) { ?>
                                            <span class="badge badge--dark"><?php esc_html_e('Low', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } elseif ($item->priority == 2) { ?>
                                            <span class="badge  badge--warning"><?php esc_html_e('Medium', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } elseif ($item->priority == 3) { ?>
                                            <span class="badge badge--danger"><?php esc_html_e('High', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php echo hyiplab_diff_for_humans($item->last_reply); ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo hyiplab_route_link('admin.ticket.view'); ?>&amp;id=<?php echo intval($item->id); ?>" class="btn btn-sm btn-outline--primary ms-1">
                                            <i class="las la-desktop"></i> <?php esc_html_e('Details', HYIPLAB_PLUGIN_NAME); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php if (hyiplab_check_empty($items->data)) { ?>
                                <tr>
                                    <td class="text-muted text-center" colspan="100%"><?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            <?php if ($items->links) { ?>
                <div class="card-footer">
                    <?php echo wp_kses($items->links, hyiplab_allowed_html()); ?>
                </div>
            <?php } ?>
        </div><!-- card end -->
    </div>
</div>