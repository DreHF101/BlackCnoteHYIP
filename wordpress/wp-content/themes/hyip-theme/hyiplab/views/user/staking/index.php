<?php hyiplab_layout('user/layouts/master'); ?>
<script>
    "use strict"

    function createCountDown(elementId, sec) {
        var tms = sec;
        var x = setInterval(function() {
            var distance = tms * 1000;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById(elementId).innerHTML = days + "d: " + hours + "h " + minutes + "m " + seconds + "s ";
            if (distance < 0) {
                clearInterval(x);
                document.getElementById(elementId).innerHTML = "COMPLETE";
            }
            tms--;
        }, 1000);
    }
</script>
<div class="row">
    <div class="col-md-12">
        <div class="text-end mb-3 d-flex flex-wrap justify-content-between gap-1">
            <h3><?php esc_html_e('My staking', HYIPLAB_PLUGIN_NAME); ?></h3>
            <a href="#" class="btn btn--base btn--smd stakingNow">
                <?php esc_html_e('Stak Now', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--responsive--md">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Invest Date', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Invest Amount', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Total Return', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Interest', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('Remaining', HYIPLAB_PLUGIN_NAME); ?></th>
                                <th><?php esc_html_e('End At', HYIPLAB_PLUGIN_NAME); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myStakings->data as $stak) { ?>
                                <tr>
                                    <td>
                                        <span><?php echo hyiplab_show_date_time($stak->created_at); ?></span>
                                    </td>
                                    <td>
                                        <span><?php echo esc_html(hyiplab_show_amount($stak->invest_amount)) . ' ' . hyiplab_currency('text'); ?></span>
                                    </td>
                                    <td>
                                        <span><?php echo esc_html(hyiplab_show_amount($stak->invest_amount + $stak->interest )) . ' ' . hyiplab_currency('text'); ?></span>
                                    </td>
                                    <td><?php echo esc_html(hyiplab_show_amount($stak->interest)) . ' ' . hyiplab_currency('text');?></td>
                                    <td scope="row" class="font-weight-bold">
                                        <?php if ($stak->end_at > hyiplab_date()->now() ) { ?>
                                            <p id="counter<?php echo intval($stak->id) ?>" class="demo countdown timess2 "></p>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped bg--base" role="progressbar" style="width: <?php echo diffDatePercent($stak->created_at, $stak->end_at) ?>%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <span class="badge badge--info"><?php echo esc_html__('Completed', HYIPLAB_PLUGIN_NAME); ?></span>
                                       <?php } ?>
                                    </td>
                                    <td>
                                       <?php echo hyiplab_show_date_time($stak->end_at); ?>
                                    </td>
                                </tr>
                                <?php
                                if (\Carbon\Carbon::parse($stak->end_at) > hyiplab_date()->now()){ ?>
                                    <script>
                                        createCountDown('counter<?php echo $stak->id; ?>',  <?php echo \Carbon\Carbon::parse($stak->end_at)->diffInSeconds() ?>);
                                    </script>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (hyiplab_check_empty($myStakings->data)) { ?>
                <div class="card-body text-center">
                    <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
                </div>
            <?php } ?>
            <?php if ($myStakings->links) { ?>
                <div class="card-footer">
                    <?php echo wp_kses($myStakings->links, hyiplab_allowed_html()); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>


<div class="modal fade" id="stakingModal">
    <div class="modal-dialog modal-dialog-centered modal-content-bg">
        <div class="modal-content">
            <div class="modal-header">
                <strong class="modal-title text-dark fs-6" id="ModalLabel">
                    <?php echo esc_html__('Staking Now', HYIPLAB_PLUGIN_NAME); ?>
                </strong>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="<?php echo hyiplab_route_link('user.staking.save') ?>" method="post">
                <?php echo hyiplab_nonce_field('user.staking.save'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label required"><?php echo esc_html__('Duration', HYIPLAB_PLUGIN_NAME); ?></label>
                        <select name="duration" class="form-control" required>
                            <option hidden><?php echo esc_html__('Select One', HYIPLAB_PLUGIN_NAME); ?></option>
                            <?php foreach ($stakings as $staking){ ?>
                                <option value="<?php echo intval($staking->id); ?>" data-interest="<?php echo esc_attr($staking->interest_percent); ?>"> <?php echo $staking->days ?> <?php echo('Days - Interest') ?> <?php echo $staking->interest_percent ?>%</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required"><?php echo esc_html__('Wallet', HYIPLAB_PLUGIN_NAME); ?></label>
                        <select name="wallet" class="form-control" required>
                            <option hidden><?php echo esc_html__('Select One', HYIPLAB_PLUGIN_NAME)?></option>
                            <option value="deposit_wallet"><?php echo esc_html__('Deposit Wallet - ', HYIPLAB_PLUGIN_NAME); ?> <?php echo hyiplab_currency('sym') . hyiplab_show_amount(get_user_meta(hyiplab_auth()->user->ID, 'hyiplab_deposit_wallet', true) == '' ? 0 : get_user_meta(hyiplab_auth()->user->ID, 'hyiplab_deposit_wallet', true) ); ?></option>
                            <option value="interest_wallet"><?php echo esc_html__('Interest Wallet - ', HYIPLAB_PLUGIN_NAME); ?> <?php echo hyiplab_currency('sym') . hyiplab_show_amount(get_user_meta(hyiplab_auth()->user->ID, 'hyiplab_interest_wallet', true) == '' ? 0 : get_user_meta(hyiplab_auth()->user->ID, 'hyiplab_interest_wallet', true)); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">
                            <?php 
                            echo esc_html__('Amount', HYIPLAB_PLUGIN_NAME) . ' ('. hyiplab_currency('sym') . hyiplab_show_amount(get_option('hyiplab_staking_min_amount', true)) . ' - '. hyiplab_currency('sym') . hyiplab_show_amount(get_option('hyiplab_staking_max_amount', true)) . ')';
                            ?>
                        </label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control" min="0" step="any" autocomplete="off" required>
                            <span class="input-group-text"><?php echo hyiplab_currency('text'); ?></span>
                        </div>
                    </div>
                    <span class="text--danger fs-6 totalReturn"><?php echo esc_html__('Total Return: ', HYIPLAB_PLUGIN_NAME); ?><span class="returnAmount"></span></span>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--base btn-md w-100"><?php echo esc_html__('Submit', HYIPLAB_PLUGIN_NAME)?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        $('.stakingNow').on('click', function() {
            let modal = $('#stakingModal');
            modal.find('[name=invest_id]').val($(this).data('id'));
            modal.modal('show');
        });

        let interest = 0,
            amount = 0,
            totalReturn = 0;

        $('[name=duration]').on('change', function() {
            interest = $(this).find(':selected').data('interest');
            calculateInterest();
        }).change();

        $('[name=amount]').on('input', function() {
            amount = $(this).val() * 1;
            calculateInterest();
        });

        function calculateInterest() {
            totalReturn =  amount * interest / 100 +  amount;
            if (totalReturn) {
                $('.totalReturn').show();
                $('.returnAmount').text(totalReturn.toFixed(2) + ` <?php echo hyiplab_currency('text'); ?>`);
            } else {
                $('.totalReturn').hide();
            }
        }

    })(jQuery);
</script>