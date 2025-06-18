<?php hyiplab_layout('user/layouts/master'); ?>


<div class="row">
    <div class="col-md-12">
        <div class="text-end mb-3 d-flex flex-wrap justify-content-between gap-1">
            <h3><?php esc_html_e('Schedule Investments', HYIPLAB_PLUGIN_NAME); ?></h3>
            <a href="<?php echo hyiplab_route_link('user.plan.index'); ?>" class="btn btn--base btn--smd">
                <?php esc_html_e('Investment Plan', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--responsive--md">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Plan', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Return', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Wallet', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Remaining Times', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Next Invest', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Action', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scheduleInvests->data as $schedule) { ?>
                                <?php $plan = \Hyiplab\Models\Plan::where('id', $schedule->plan_id)->first();
                                $interest = $plan->interest_type == 1 ? ($schedule->amount * $plan->interest) / 100 : $plan->interest;
                                ?>
                                <tr>
                                    <td>
                                        <span><?php echo $plan->name; ?></span><br>
                                        <span><?php echo hyiplab_show_amount($schedule->amount) . ' ' . hyiplab_currency('text'); ?></span>
                                    </td>
                                    <td>
                                        <span>
                                            <?php
                                                $timeSettingsName = \Hyiplab\Models\TimeSetting::where('id', $plan->time_setting_id)->first();
                                                echo hyiplab_show_amount($interest) . ' ' . hyiplab_currency('text') . esc_html(' every ') . $timeSettingsName->data->name . '<br>' . esc_html('for ');
                                                if ($plan->lifetime) {
                                                    echo esc_html('Lifetime');
                                                } else {
                                                    echo $plan->repeat_time . ' ' . $timeSettingsName->data->name;
                                                }
                                                if ($plan->capital_back) {
                                                    echo ' + ' . esc_html('Capital');
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span><?php echo hyiplab_key_to_title($schedule->wallet); ?></span>
                                    </td>
                                    <td><?php echo $schedule->rem_schedule_times; ?></td>
                                    <td><?php echo $schedule->next_invest && $schedule->next_invest !== '0000-00-00 00:00:00' ? hyiplab_show_date_time($schedule->next_invest) : '----' ; ?></td>
                                    <td>
                                        <button class="icon-btn base--bg btn-sm text-white detailsBtn" data-schedule_invest='<?php echo json_encode($schedule); ?>' data-plan_name="<?php echo $plan->name; ?>" data-interest="<?php echo hyiplab_show_amount($interest); ?>" data-next_invest="<?php echo $schedule->next_invest && $schedule->next_invest !== '0000-00-00 00:00:00' ? hyiplab_show_date_time($schedule->next_invest) : '-----'  ?>">
                                            <i class="fa fa-desktop"></i>
                                        </button>
                                        
                                        <?php
                                            if ($schedule->rem_schedule_times){
                                                if ($schedule->status) {?>
                                                    <button class="icon-btn base--bg btn-sm text-white confirmationBtn" data-question="<?php echo esc_attr('Are you sure to pause this schedule invest?') ?>" data-action="<?php echo hyiplab_route_link('user.schedule.status');?>/?id=<?php echo $schedule->id; ?>" data-nonce="<?php echo hyiplab_nonce('user.schedule.status'); ?>">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                               <?php }else{ ?>
                                                    <button class="icon-btn base--bg btn-sm text-white confirmationBtn" data-question="<?php echo esc_attr('Are you sure to continue this schedule invest?') ?>" data-action="<?php echo hyiplab_route_link('user.schedule.status');?>/?id=<?php echo $schedule->id; ?>" data-nonce="<?php echo hyiplab_nonce('user.schedule.status'); ?>">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                    <?php
                                                }
                                            }
                                        ?>

                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (hyiplab_check_empty($scheduleInvests->data)) { ?>
                <div class="card-body text-center">
                    <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
                </div>
            <?php } ?>
            <?php if ($scheduleInvests->links) { ?>
                <div class="card-footer">
                    <?php echo wp_kses($scheduleInvests->links, hyiplab_allowed_html()); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>


<div class="modal fade" id="detailsModal">
    <div class="modal-dialog modal-dialog-centered modal-content-bg">
        <div class="modal-content">
            <div class="modal-header">
                <strong class="modal-title text-white text--dark fs-5" id="ModalLabel">
                    <?php esc_html_e('Schedule Invest Details', HYIPLAB_PLUGIN_NAME); ?>
                </strong>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Plan Name', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="planName"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Invest Amount', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="investAmount"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Interest', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="interestAmount"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between compoundInterestBlock">
                        <?php esc_html_e('Compound Interest', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="compoundInterest"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Schedule Times', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="scheduleTimes"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Remaining Schedule Times', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="remScheduleTimes"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Interval', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="intervalHours"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php esc_html_e('Next Invest', HYIPLAB_PLUGIN_NAME); ?>
                        <span class="nextInvest"></span>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-md" data-bs-dismiss="modal"><?php esc_html_e('Close', HYIPLAB_PLUGIN_NAME); ?></button>
            </div>
        </div>
    </div>
</div>

<?php hyiplab_include('partials/confirmation') ?>

<script>
    (function($) {
        "use strict";

        let curSym = '<?php echo hyiplab_currency('sym'); ?>';

        $('.detailsBtn').on('click', function() {
            let modal = $('#detailsModal');
            let data = $(this).data();
            let scheduleInvest = data.schedule_invest;
            modal.find('.planName').text(data.plan_name);
            modal.find('.investAmount').text(curSym + parseFloat(scheduleInvest.amount).toFixed(2));
            modal.find('.interestAmount').text(curSym + parseFloat(data.interest).toFixed(2));
            modal.find('.scheduleTimes').text(scheduleInvest.schedule_times);
            modal.find('.remScheduleTimes').text(scheduleInvest.rem_schedule_times);
            modal.find('.intervalHours').text(`${scheduleInvest.interval_hours} Hours`);
            modal.find('.nextInvest').text(data.next_invest);

            if (scheduleInvest.compound_times) {
                modal.find('.compoundInterest').text(`${scheduleInvest.compound_times} times`);
                $('.compoundInterestBlock').removeClass('d-none');
            } else {
                $('.compoundInterestBlock').addClass('d-none');
            }

            modal.modal('show');
        });
    })(jQuery);
</script>