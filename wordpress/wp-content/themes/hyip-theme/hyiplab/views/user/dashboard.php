<?php hyiplab_layout('user/layouts/master'); ?>
<div class="row g-3 mt-4">
    <div class="col-md-12">
        <?php if ($data['deposit_wallet'] <= 0 && $data['interest_wallet'] <= 0) : ?>
            <div class="alert border border--danger" role="alert">
                <div class="alert__icon d-flex align-items-center text--danger"><i class="fas fa-exclamation-triangle"></i></div>
                <p class="alert__message">
                    <span class="fw-bold"><?php esc_html_e('Empty Balance', HYIPLAB_PLUGIN_NAME); ?></span><br>
                    <small><i><?php esc_html_e('Your balance is empty. Please make', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo hyiplab_route_link('user.deposit.index'); ?>" class="link-color">
                    <?php esc_html_e('deposit', HYIPLAB_PLUGIN_NAME); ?></a> <?php esc_html_e('for your next investment.', HYIPLAB_PLUGIN_NAME); ?></i>
                    </small>
                </p>
            </div>
        <?php endif ?>

        <?php if ($data['pendingWithdraws']) : ?>
            <div class="alert border border--primary" role="alert">
                <div class="alert__icon d-flex align-items-center text--primary"><i class="fas fa-spinner"></i>
                </div>
                <p class="alert__message">
                    <span class="fw-bold"><?php esc_html_e('Withdrawal Pending', HYIPLAB_PLUGIN_NAME); ?></span><br>
                    <small><i><?php esc_html_e('Total', HYIPLAB_PLUGIN_NAME) . hyiplab_show_amount($data['pendingWithdraws']) . hyiplab_currency('text') . esc_html__('withdrawal request is pending. Please wait for admin approval. The amount will send to the account which you\'ve provided. See', HYIPLAB_PLUGIN_NAME); ?><a href="<?php echo hyiplab_route_link('user.withdraw.history'); ?>" class="link-color"><?php esc_html_e('withdrawal history'); ?></a></i>
                    </small>
                </p>
            </div>
        <?php endif ?>

        <?php if ($data["pendingDeposits"]) : ?>
            <div class="alert border border--primary" role="alert">
                <div class="alert__icon d-flex align-items-center text--primary"><i class="fas fa-spinner"></i>
                </div>
                <p class="alert__message">
                    <span class="fw-bold"><?php echo esc_html__('Deposit Pending', HYIPLAB_PLUGIN_NAME); ?></span><br>
                    <small><i><?php esc_html_e('Total', HYIPLAB_PLUGIN_NAME) . hyiplab_show_amount($data['pendingDeposits']) . hyiplab_currency('text') . esc_html__('deposit request is pending. Please wait for admin approval. See', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo  hyiplab_route_link('user.deposit.history'); ?>" class="link-color"><?php esc_html_e('deposit history', HYIPLAB_PLUGIN_NAME ); ?></a></i></small>
                </p>
            </div>
        <?php endif ?>

        <?php if ($data['isHoliday']) : ?>
            <div class="alert border border--info" role="alert">
                <div class="alert__icon d-flex align-items-center text--info"><i class="fas fa-toggle-off"></i>
                </div>
                <p class="alert__message">
                    <span class="fw-bold"><?php esc_html_e('Holiday', HYIPLAB_PLUGIN_NAME);?></span><br>
                    <small><i><?php esc_html_e('Today is holiday on this system. You\'ll not get any interest today from this system. Also you\'re unable to make withdrawal request today.', HYIPLAB_PLUGIN_NAME); ?> <br> <?php esc_html_e('The next working day is coming after', HYIPLAB_PLUGIN_NAME); ?> <span id="counter"
                    class="fw-bold text--primary fs--15px"></span></i></small>
                </p>
            </div>
        <?php endif ?>
        <?php $isKycEnable = get_option('hyiplab_kyc');  ?>
        <?php if ($isKycEnable && !@$data['kyc']->kv) : ?>
            <div class="alert border border--info" role="alert">
                <div class="alert__icon d-flex align-items-center text--info"><i class="fas fa-file-signature"></i>
                </div>
                <p class="alert__message">
                    <span class="fw-bold"><?php esc_html_e('KYC Verification Required', HYIPLAB_PLUGIN_NAME);?> </span><br>
                    <small><i><?php esc_html_e('Please submit the required KYC information to verify yourself. Otherwise, you couldn\'t make any withdrawal requests to the system.', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo hyiplab_route_link('user.kyc.form'); ?>" class="link-color"><?php esc_html_e('Click here', HYIPLAB_PLUGIN_NAME); ?></a> <?php esc_html_e('to submit KYC information.', HYIPLAB_PLUGIN_NAME); ?></i></small>
                </p>
            </div>
        <?php elseif($isKycEnable && @$data['kyc']->kv == 2) : ?>
            <div class="alert border border--warning" role="alert">
                <div class="alert__icon d-flex align-items-center text--warning"><i
                        class="fas fa-user-check"></i></div>
                <p class="alert__message">
                    <span class="fw-bold"><?php esc_html_e('KYC Verification Pending', HYIPLAB_PLUGIN_NAME); ?></span><br>
                    <small><i><?php esc_html_e('Your submitted KYC information is pending for admin approval. Please wait till that.', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo hyiplab_route_link('user.kyc.data'); ?>" class="link-color"><?php esc_html_e('Click here', HYIPLAB_PLUGIN_NAME); ?></a> <?php esc_html_e('to see your submitted information', HYIPLAB_PLUGIN_NAME); ?></i></small>
                </p>
            </div>
        <?php endif ?>
        <?php if($isKycEnable && @$data['kyc']->kv == 3) : ?>
            <div class="alert border border--warning" role="alert">
                <div class="alert__icon d-flex align-items-center text--warning"><i
                        class="fas fa-user-check"></i></div>
                <p class="alert__message">
                    <span class="fw-bold"><?php esc_html_e('KYC Verification Rejected', HYIPLAB_PLUGIN_NAME); ?></span><br>
                    <small><i><?php esc_html_e('Your submitted KYC information is rejected. Please re-submit your KYC information.', HYIPLAB_PLUGIN_NAME); ?> <a href="<?php echo hyiplab_route_link('user.kyc.data'); ?>" class="link-color"><?php esc_html_e('Click here', HYIPLAB_PLUGIN_NAME); ?></a> <?php esc_html_e('to see your submitted information', HYIPLAB_PLUGIN_NAME); ?></i></small>
                </p>
            </div>
        <?php endif ?>
    </div>
    <div class="col-lg-4">
        <div class="dashboard-widget">
            <div class="d-flex justify-content-between">
                <h5 class="text-secondary"><?php esc_html_e('Successful Deposits', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <h3 class="text--secondary my-4"><?php echo hyiplab_show_amount($data['successfulDeposits']); ?> <?php echo hyiplab_currency('text'); ?></h3>
            <div class="widget-lists">
                <div class="row">
                    <div class="col-4">
                        <p class="fw-bold"><?php esc_html_e('Submitted', HYIPLAB_PLUGIN_NAME); ?></p>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['submittedDeposits']); ?></span>
                    </div>
                    <div class="col-4">
                        <p class="fw-bold"><?php esc_html_e('Pending', HYIPLAB_PLUGIN_NAME); ?></p>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['pendingDeposits']); ?></span>
                    </div>
                    <div class="col-4">
                        <p class="fw-bold"><?php esc_html_e('Rejected', HYIPLAB_PLUGIN_NAME); ?></p>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['rejectedDeposits']); ?></span>
                    </div>
                </div>
                <hr>
                <p><small><i><?php esc_html_e('You\'ve requested to deposit', HYIPLAB_PLUGIN_NAME); ?> <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['requestedDeposits']); ?>. <?php esc_html_e('Where', HYIPLAB_PLUGIN_NAME); ?> <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['initiatedDeposits']); ?> <?php esc_html_e('is just initiated but not submitted', HYIPLAB_PLUGIN_NAME); ?>.</i></small></p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="dashboard-widget">
            <div class="d-flex justify-content-between">
                <h5 class="text-secondary"><?php esc_html_e('Successful Withdrawals', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <h3 class="text--secondary my-4"><?php echo hyiplab_show_amount($data['successfulWithdraws']); ?> <?php echo hyiplab_currency('text'); ?></h3>
            <div class="widget-lists">
                <div class="row">
                    <div class="col-4">
                        <p class="fw-bold"><?php esc_html_e('Submitted', HYIPLAB_PLUGIN_NAME); ?></p>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['submittedWithdraws']); ?></span>
                    </div>
                    <div class="col-4">
                        <p class="fw-bold"><?php esc_html_e('Pending', HYIPLAB_PLUGIN_NAME); ?></p>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['pendingWithdraws']); ?></span>
                    </div>
                    <div class="col-4">
                        <p class="fw-bold"><?php esc_html_e('Rejected', HYIPLAB_PLUGIN_NAME); ?></p>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['rejectedWithdraws']); ?></span>
                    </div>
                </div>
                <hr>
                <p><small><i><?php esc_html_e('You\'ve requested to withdraw', HYIPLAB_PLUGIN_NAME); ?> <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['requestedWithdraws']); ?>. <?php esc_html_e('Where', HYIPLAB_PLUGIN_NAME); ?> <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['initiatedWithdraws']); ?> <?php esc_html_e('is just initiated but not submitted', HYIPLAB_PLUGIN_NAME); ?>.</i></small></p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="dashboard-widget">
            <div class="d-flex justify-content-between">
                <h5 class="text-secondary"><?php esc_html_e('Total Investments', HYIPLAB_PLUGIN_NAME); ?></h5>
            </div>
            <h3 class="text--secondary my-4"><?php echo hyiplab_show_amount($data['invests']); ?> <?php echo hyiplab_currency('text'); ?></h3>
            <div class="widget-lists">
                <div class="row">
                    <div class="col-4">
                        <p class="fw-bold"><?php esc_html_e('Completed', HYIPLAB_PLUGIN_NAME); ?></p>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['completedInvests']); ?></span>
                    </div>
                    <div class="col-4">
                        <p class="fw-bold"><?php esc_html_e('Running', HYIPLAB_PLUGIN_NAME); ?></p>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['runningInvests']); ?></span>
                    </div>
                    <div class="col-4">
                        <p class="fw-bold"><?php esc_html_e('Interests', HYIPLAB_PLUGIN_NAME); ?></p>
                        <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['interests']); ?></span>
                    </div>
                </div>
                <hr>
                <p><small><i><?php esc_html_e('You\'ve invested', HYIPLAB_PLUGIN_NAME); ?> <?php echo hyiplab_currency('symbol'); ?><?php echo hyiplab_show_amount($data['depositWalletInvests']); ?> <?php esc_html_e('from the deposit wallet and', HYIPLAB_PLUGIN_NAME); ?> <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($data['interestWalletInvests']); ?> <?php esc_html_e('from the interest wallet', HYIPLAB_PLUGIN_NAME); ?></i></small></p>
            </div>
        </div>
    </div>
</div>


<div class="card mt-4 mb-4">
    <div class="card-body">
        <div class="mb-2">
            <h5 class="title"><?php esc_html_e('Latest ROI Statistics', HYIPLAB_PLUGIN_NAME); ?></h5>
            <p><small><i><?php esc_html_e('Here is last 30 days statistics of your ROI (Return on Investment)', HYIPLAB_PLUGIN_NAME); ?></i></small></p>
        </div>
        <div id="chart"></div>
    </div>
</div>

<script>
    jQuery(document).ready(function($){
        "use strict";
        // apex-line chart
        var options = {
            chart: {
                height: 350,
                type: "area",
                toolbar: {
                    show: false
                },
                dropShadow: {
                    enabled: true,
                    enabledSeries: [0],
                    top: -2,
                    left: 0,
                    blur: 10,
                    opacity: 0.08,
                },
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: "Price",
                data: [
                    <?php foreach ($data['chart'] as $cData) { ?>
                        <?php echo hyiplab_get_amount($cData->amount); ?>,
                    <?php } ?>
    
                ]
            }],
            fill: {
                type: "gradient",
                colors: ['#4c7de6', '#4c7de6', '#4c7de6'],
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.6,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                title: "Value",
                categories: [
                    <?php foreach ($data['chart'] as $cData) { ?> 
                        "<?php echo date('d F', strtotime($cData->date)); ?>",
                    <?php } ?>
                ]
            },
            grid: {
                padding: {
                    left: 5,
                    right: 5
                },
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
            },
        };
    
        var chart = new ApexCharts(document.querySelector("#chart"), options);
    
        chart.render();
    
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>


<script>
        'use strict';
        (function ($) {
            <?php if($data['isHoliday']) : ?>
                function createCountDown(elementId, sec) {
                    var tms = sec;
                    var x = setInterval(function () {
                        var distance = tms * 1000;
                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        var days = `<span>${days}d</span>`;
                        var hours = `<span>${hours}h</span>`;
                        var minutes = `<span>${minutes}m</span>`;
                        var seconds = `<span>${seconds}s</span>`;
                        document.getElementById(elementId).innerHTML = days +' '+ hours + " " + minutes + " " + seconds;
                        if (distance < 0) {
                            clearInterval(x);
                            document.getElementById(elementId).innerHTML = "COMPLETE";
                        }
                        tms--;
                    }, 1000);
                }

                createCountDown('counter', <?php echo \Carbon\Carbon::parse($data['nextWorkingDay'])->diffInSeconds();?>);
            <?php endif ?>
        })(jQuery);
 </script>

<?php
wp_enqueue_script('apexchart', hyiplab_asset('public/js/apexcharts.min.js'), array('jquery'), null, true);
?>