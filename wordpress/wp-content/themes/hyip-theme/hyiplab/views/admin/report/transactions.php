<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="card b-radius--10 ">
    <div class="card-body p-0">
        <div class="table-responsive--sm table-responsive">
            <table class="table table--light style--two">
                <thead>
                    <tr>
                        <th><?php esc_html_e('User', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('TRX', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('Transacted', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('Post Balance', HYIPLAB_PLUGIN_NAME); ?></th>
                        <th><?php esc_html_e('Details', HYIPLAB_PLUGIN_NAME); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions->data as $transaction) {
                        $user = get_userdata($transaction->user_id);
                    ?>
                        <tr>
                            <td>
                                <span class="fw-bold"><?php echo esc_html($user->display_name); ?></span>
                                <br />
                                <a href="<?php echo hyiplab_route_link('admin.users.detail');?>&amp;id=<?php echo intval($user->ID); ?>">
                                    @<?php echo esc_html($user->user_login); ?>
                                </a>
                            </td>
                            <td><span class="fw-bold"><?php echo esc_html($transaction->trx); ?></span></td>
                            <td>
                                <?php echo hyiplab_show_date_time($transaction->created_at); ?> <br />
                                <?php echo hyiplab_diff_for_humans($transaction->created_at); ?>
                            </td>
                            <td class="<?php echo esc_attr($transaction->trx_type) == '+' ? 'text--success fw-bold' : 'text--danger fw-bold' ?>">
                                <?php echo esc_html($transaction->trx_type); ?>
                                <?php echo hyiplab_show_amount($transaction->amount); ?> <?php echo hyiplab_currency('text'); ?>
                            </td>
                            <td class="budget">
                                <?php echo hyiplab_show_amount($transaction->post_balance); ?> <?php echo hyiplab_currency('text'); ?>
                            </td>
                            <td><?php echo esc_html($transaction->details); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (hyiplab_check_empty($transactions->data)) { ?>
                        <tr>
                            <td class="text-muted text-center" colspan="100%"><?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table><!-- table end -->
        </div>
    </div>
    <?php if ($transactions->links) { ?>
        <div class="card-footer">
            <?php echo wp_kses($transactions->links, hyiplab_allowed_html()); ?>
        </div>
    <?php } ?>
</div>