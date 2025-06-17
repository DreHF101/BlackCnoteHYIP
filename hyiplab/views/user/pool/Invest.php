<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="text-end mb-3 d-flex flex-wrap justify-content-between gap-1">
            <h3><?php esc_html_e('My Pool Invests', HYIPLAB_PLUGIN_NAME); ?></h3>
            <a href="<?php echo hyiplab_route_link('user.pool.index'); ?>" class="btn btn--base btn--smd">
                <?php esc_html_e('Pool', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--responsive--md">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Pool', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Invest Amount', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Invested Date', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Return Date', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Total Return', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($poolInvests->data as $poolInvest) { ?>
                                <tr>
                                    <td>
                                        <?php 
                                            $pool = \Hyiplab\Models\Pool::find($poolInvest->pool_id);
                                            echo esc_html($pool->name); 
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            echo esc_html(hyiplab_show_amount($poolInvest->invest_amount), 2) . hyiplab_currency('text');
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            echo esc_html(hyiplab_show_date_time($poolInvest->created_at, 'd M Y H:i A'));
                                        ?>
                                    </td>
                                    <td><?php echo esc_html(hyiplab_show_date_time($pool->end_date, 'd M Y H:i A')); ?></td>
                                    <td>
                                        <?php
                                            if($pool->share_interest){

                                                echo esc_html(hyiplab_show_amount(($poolInvest->invest_amount * ( 1 + $pool->interest / 100))), 2).' ' . hyiplab_currency('text');
                                            }else{
                                                echo esc_html__('Not return yet!', HYIPLAB_PLUGIN_NAME);
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <?php if (hyiplab_check_empty($poolInvests->data)) { ?>
                <div class="card-body text-center">
                    <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
                </div>
            <?php } ?>
            <?php if ($poolInvests->links) { ?>
                <div class="card-footer">
                    <?php echo wp_kses($poolInvests->links, hyiplab_allowed_html()); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>