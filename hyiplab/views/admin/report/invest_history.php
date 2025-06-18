<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">

    <div class="col-xxl-3 col-sm-6 mb-30">
        <div class="widget-two box--shadow2 has-link b-radius--5 bg--info">
            <div class="widget-two__content">
                <h2 class="text-white"><?php echo esc_html($totalInvestCount); ?></h2>
                <p class="text-white"><?php esc_html_e('Total Invest Count', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
        </div><!-- widget-two end -->
    </div>
    <div class="col-xxl-3 col-sm-6 mb-30">
        <div class="widget-two box--shadow2 b-radius--5 bg--success has-link">
            <div class="widget-two__content">
                <h2 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($totalInvestAmount); ?></h2>
                <p class="text-white"><?php esc_html_e('Total Invest', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
        </div><!-- widget-two end -->
    </div>
    <div class="col-xxl-3 col-sm-6 mb-30">
        <div class="widget-two box--shadow2 b-radius--5 bg--6 has-link">
            <div class="widget-two__content">
                <h2 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($totalPaid); ?></h2>
                <p class="text-white"><?php esc_html_e('Total Paid', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
        </div><!-- widget-two end -->
    </div>
    <div class="col-xxl-3 col-sm-6 mb-30">
        <div class="widget-two box--shadow2 has-link b-radius--5 bg--dark">
            <div class="widget-two__content">
                <h2 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($shouldPay); ?></h2>
                <p class="text-white"><?php esc_html_e('To Pay', HYIPLAB_PLUGIN_NAME); ?> (<small><?php esc_html_e('Without lifetime plan invest', HYIPLAB_PLUGIN_NAME); ?></small>)</p>
            </div>
        </div><!-- widget-two end -->
    </div>

    <div class="col-lg-12">

        <div class="card b-radius--10 p-0">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col"><?php esc_html_e('User', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Plan Name', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Amount', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Interest', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('To Pay', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Paid', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th scope="col"><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!hyiplab_check_empty($invests->data)) {
                                foreach ($invests->data as $invest) {
                                    $user = get_userdata($invest->user_id);
                                    $plan = get_hyiplab_plan($invest->plan_id);
                            ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold"><?php echo esc_html($user->display_name); ?></span><br />
                                            <a href="<?php echo hyiplab_route_link('admin.users.detail'); ?>&amp;id=<?php echo intval($user->ID); ?>">
                                                @<?php echo esc_html($user->user_login); ?>
                                            </a>
                                        </td>
                                        <td><?php echo esc_html($plan->name); ?></td>

                                        <td><?php echo hyiplab_show_amount($invest->amount); ?> <?php echo hyiplab_currency('text'); ?></td>

                                        <td><?php echo hyiplab_show_amount($invest->interest); ?> <?php echo hyiplab_currency('text'); ?></td>

                                        <td><?php echo hyiplab_show_amount($invest->should_pay); ?> <?php echo hyiplab_currency('text'); ?></td>

                                        <td><?php echo hyiplab_show_amount($invest->paid); ?> <?php echo hyiplab_currency('text'); ?></td>

                                        <td>
                                            <?php if ($invest->status == 1) { ?>
                                                <span class="badge badge--success"><?php esc_html_e('Running', HYIPLAB_PLUGIN_NAME); ?></span>
                                            <?php } else { ?>
                                                <span class="badge badge--dark"><?php esc_html_e('Closed', HYIPLAB_PLUGIN_NAME); ?></span>
                                            <?php } ?>
                                        </td>

                                    </tr>

                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="7"><?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($invests->links) { ?>
                <div class="card-footer">
                    <?php echo wp_kses($invests->links, hyiplab_allowed_html()); ?>
                </div>
            <?php } ?>

        </div>

    </div>
</div>