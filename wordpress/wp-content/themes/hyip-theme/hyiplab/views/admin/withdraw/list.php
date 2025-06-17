<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="card b-radius--10 ">
    <div class="card-body p-0">
        <div class="table-responsive--sm table-responsive">
            <table class="table table--light style--two">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Gateway | Transaction', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('Initiated', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('User', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('Conversion', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($withdrawals->data as $withdraw) {
                        $user = get_userdata($withdraw->user_id);
                        $method = hyiplab_withdraw_methods($withdraw->method_id);
                    ?>
                        <tr>
                            <td>
                                <span class="fw-bold"><?php echo esc_html($method->name); ?></span></br>
                                <small><?php echo esc_html($withdraw->trx); ?></small>
                            </td>
                            <td>
                                <?php echo hyiplab_show_date_time($withdraw->created_at); ?><br><?php echo hyiplab_diff_for_humans($withdraw->created_at); ?>
                            </td>
                            <td>
                                <span class="fw-bold"><?php echo esc_html($user->display_name); ?></span>
                                <br />
                                <a href="<?php echo hyiplab_route_link('admin.users.detail');?>&amp;id=<?php echo intval($user->ID); ?>">
                                    <span class="small">@<?php echo esc_html($user->user_login); ?></span>
                                </a>
                            </td>
                            <td>
                                <?php echo hyiplab_currency('sym') . hyiplab_show_amount($withdraw->amount); ?> + <span class="text-danger" title="<?php esc_attr_e('charge', HYIPLAB_PLUGIN_NAME);?>"><?php echo hyiplab_show_amount($withdraw->charge); ?></span>
                                <br>
                                <strong title="<?php esc_attr_e('Amount after charge', HYIPLAB_PLUGIN_NAME);?>">
                                    <?php echo hyiplab_show_amount($withdraw->amount - $withdraw->charge); ?> <?php echo hyiplab_currency('text'); ?>
                                </strong>
                            </td>

                            <td>
                                1 <?php echo hyiplab_currency('text'); ?> = <?php echo hyiplab_show_amount($withdraw->rate); ?> <?php echo esc_html($withdraw->currency); ?>
                                <br>
                                <span class="fw-bold"><?php echo hyiplab_show_amount($withdraw->final_amount); ?> <?php echo esc_html($withdraw->currency); ?></span>
                            </td>

                            <td>
                                <?php
                                $html = '';
                                if ($withdraw->status == 2) {
                                    $html = '<span class="badge badge--warning">' . esc_html__('Pending', HYIPLAB_PLUGIN_NAME) . '</span>';
                                } elseif ($withdraw->status == 1) {
                                    $html = '<span><span class="badge badge--success">' . esc_html__('Approved', HYIPLAB_PLUGIN_NAME) . '</span><br>' . hyiplab_diff_for_humans($withdraw->updated_at) . '</span>';
                                } elseif ($withdraw->status == 3) {
                                    $html = '<span><span class="badge badge--danger">' . esc_html__('Rejected', HYIPLAB_PLUGIN_NAME) . '</span><br>' . hyiplab_diff_for_humans($withdraw->updated_at) . '</span>';
                                }
                                echo wp_kses($html, hyiplab_allowed_html());
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo hyiplab_route_link('admin.withdraw.detail'); ?>&amp;id=<?php echo intval($withdraw->id); ?>" class="btn btn-sm btn-outline--primary ms-1">
                                    <i class="la la-desktop"></i> <?php esc_html_e('Details', HYIPLAB_PLUGIN_NAME); ?>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (hyiplab_check_empty($withdrawals->data)) { ?>
                        <tr>
                            <td colspan="7" class="text-center"><?php esc_html_e('Data Not Found', HYIPLAB_PLUGIN_NAME); ?></td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table><!-- table end -->
        </div>
    </div>
    <?php if ($withdrawals->links) { ?>
        <div class="card-footer">
            <?php echo wp_kses($withdrawals->links, hyiplab_allowed_html()); ?>
        </div>
    <?php } ?>
</div><!-- card end -->