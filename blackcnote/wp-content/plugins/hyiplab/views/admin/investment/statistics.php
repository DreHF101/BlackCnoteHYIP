<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row g-4">
    <div class="col-md-6">
        <div class="row g-4">
            <div class="col-12">
                <div class="card full-view">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-6">
                                <h5 class="card-title mb-0"><?php esc_html_e('Total Invests', HYIPLAB_PLUGIN_NAME); ?></h5>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <div class="d-flex justify-content-sm-end gap-2">
                                    <button class="exit-btn">
                                        <i class="fullscreen-open las la-compress" onclick="openFullscreen();"></i>
                                        <i class="fullscreen-close las la-compress-arrows-alt" onclick="closeFullscreen();"></i>
                                    </button>
                                    <select name="invest_time" class="widget_select">
                                        <option value="week"><?php esc_html_e('Current Week', HYIPLAB_PLUGIN_NAME); ?></option>
                                        <option value="month"><?php esc_html_e('Current Month', HYIPLAB_PLUGIN_NAME); ?></option>
                                        <option value="year" selected><?php esc_html_e('Current Year', HYIPLAB_PLUGIN_NAME); ?></option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center pb-0 px-0">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <p><?php esc_html_e('This', HYIPLAB_PLUGIN_NAME); ?> <span class="time_type"></span> <?php esc_html_e('invest', HYIPLAB_PLUGIN_NAME); ?></p>
                            </div>
                            <div class="col-md-4">
                                <h3><span><?php echo hyiplab_currency('sym'); ?></span><span class="total_invest"></span></h3>
                            </div>
                            <div class="col-md-4">
                                <p class="up_down">

                                </p>
                            </div>
                        </div>
                        <div class="my_invest_canvas"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><?php esc_html_e('Investments', HYIPLAB_PLUGIN_NAME); ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if ($totalInterest > 0) { ?>
                            <div class="card-container">
                                <div class="investments-scheme">
                                    <div class="investments-scheme-item">
                                        <p class="mb-0"><?php esc_html_e('Total Investments', HYIPLAB_PLUGIN_NAME); ?></p>
                                        <h3 class="mb-6">
                                            <small><?php echo hyiplab_currency('sym'); ?></small><?php echo hyiplab_show_amount($widget['total_invest']); ?>
                                        </h3>
                                    </div>
                                    <div class="investments-scheme-arrow">
                                        <div class="text-end">
                                            <i class="las la-arrow-down text--success" style="transform: rotate(30deg);"></i>
                                        </div>
                                        <div class="text-start">
                                            <i class="las la-arrow-down text--success" style="transform: rotate(-30deg);"></i>
                                        </div>
                                    </div>
                                    <div class="investments-scheme-group">
                                        <div class="investments-scheme-content">
                                            <p class="font-12"><?php esc_html_e('Deposit Wallet', HYIPLAB_PLUGIN_NAME); ?></p>
                                            <h3 class="deposit-amount text--success counter">
                                                <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($widget['invest_deposit_wallet']); ?>
                                            </h3>
                                            <p class="mb-0 font-12">
                                                <i class="feather icon-users"></i>
                                                <strong>
                                                    <?php echo hyiplab_show_amount(($widget['invest_deposit_wallet'] / $widget['total_invest']) * 100); ?>%
                                                </strong>
                                            </p>
                                        </div>
                                        <div class="investments-scheme-content">
                                            <p class="font-12"><?php esc_html_e('Interest Wallet', HYIPLAB_PLUGIN_NAME); ?></p>
                                            <h3 class="deposit-amount text--success counter">
                                                <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($widget['invest_interest_wallet']); ?>
                                            </h3>
                                            <p class="mb-0 font-12"><i class="feather icon-users"></i>
                                                <strong>
                                                    <?php echo hyiplab_show_amount(($widget['invest_interest_wallet'] / $widget['total_invest']) * 100); ?>%
                                                </strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <h5 class="text-center"><?php esc_html_e('Invest not found', HYIPLAB_PLUGIN_NAME); ?></h5>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5 class="card-title mb-0"><?php esc_html_e('Profit to Pay', HYIPLAB_PLUGIN_NAME); ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($widget['profit_to_give']) { ?>
                            <div class="card-container">
                                <div class="row align-items-center pb-3 pb-xxl-0">
                                    <div class="col-6">
                                        <p><?php esc_html_e('Should Pay', HYIPLAB_PLUGIN_NAME); ?></p>
                                        <h3 class="deposit-amount">
                                            <sup><?php echo hyiplab_currency('sym'); ?></sup><?php echo hyiplab_show_amount($widget['profit_to_give']); ?>
                                        </h3>
                                    </div>
                                    <div class="col-6 text-end">
                                        <a href="<?php echo hyiplab_route_link('admin.report.invest.history'); ?>" class="btn btn--primary"><?php esc_html_e('History', HYIPLAB_PLUGIN_NAME); ?></a>
                                    </div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress-info-content">
                                        <p><?php esc_html_e('Paid', HYIPLAB_PLUGIN_NAME); ?>
                                            (<?php echo hyiplab_show_amount(($widget['profit_paid'] / ($widget['profit_paid'] + $widget['profit_to_give'])) * 100); ?>%)
                                        </p>
                                    </div>
                                    <div class="progress-info-content">
                                        <p>
                                            <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($widget['profit_paid']); ?> /
                                            <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($widget['profit_paid'] + $widget['profit_to_give']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="progress mb-2 my-progressbar">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo hyiplab_show_amount(($widget['profit_paid'] / ($widget['profit_paid'] + $widget['profit_to_give'])) * 100); ?>%;">
                                    </div>
                                </div>

                                <p class="font-12 mb-0">*<?php esc_html_e('This statistics showing data excluding lifetime investment.', HYIPLAB_PLUGIN_NAME); ?></p>
                            </div>
                        <?php } else { ?>
                            <h5 class="text-center"><?php esc_html_e('No invest found to paid', HYIPLAB_PLUGIN_NAME); ?></h5>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0"><?php esc_html_e('Interest Statistics by Plan', HYIPLAB_PLUGIN_NAME); ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <div class="chart-info">
                                <a href="#" class="chart-info-toggle">
                                    <img src="<?php echo hyiplab_asset('admin/images/collapse.svg'); ?>" alt="image" class="chart-info-img">
                                </a>
                                <div class="chart-info-content">
                                    <ul class="chart-info-list">
                                        <?php foreach ($interestByPlans as $key => $invest) {
                                            ?>
                                            <li class="chart-info-list-item">
                                                <i class="fas fa-plane planPointInterest me-2"></i><?php echo esc_html($key); ?>
                                                <?php echo hyiplab_show_amount(($invest / $totalInterest) * 100); ?>%
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-area">
                                <canvas id="interest_by_plan" height="250" class="chartjs-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="row g-2 align-items-center">
                                    <div class="col-sm-6">
                                        <h5 class="card-title mb-0"><?php esc_html_e('Invest & Interest', HYIPLAB_PLUGIN_NAME); ?></h5>
                                    </div>
                                    <div class="col-sm-6 text-sm-end">
                                        <select name="invest_interest_time" class="widget_select" id="plan_statistics_time">
                                            <option value="all"><?php esc_html_e('All Time', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="week"><?php esc_html_e('Current Week', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="month"><?php esc_html_e('Current Month', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="year"><?php esc_html_e('Current Year', HYIPLAB_PLUGIN_NAME); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="interest-scheme">
                            <div class="interest-scheme__content">
                                <p class="mb-0"><?php esc_html_e('Running Invest', HYIPLAB_PLUGIN_NAME); ?></p>
                                <h5 class="mb-1 text-success runningInvests"></h5>
                                <p class="mb-0">
                                    <a href="<?php echo hyiplab_route_link('admin.report.invest.history'); ?>" class="btn btn-sm btn-success-rgba font-12 px-2"><?php esc_html_e('History', HYIPLAB_PLUGIN_NAME); ?></a>
                                </p>
                            </div>
                            <div class="interest-scheme__content text-sm-center">
                                <p class="mb-0 font-12"><?php esc_html_e('Closed Invest', HYIPLAB_PLUGIN_NAME); ?></p>
                                <h5 class="mb-1 text--warning counter expiredInvests"></h5>
                                <p class="mb-0">
                                    <a href="<?php echo hyiplab_route_link('admin.report.invest.history'); ?>"><button type="button" class="btn btn-sm btn-warning-rgba font-12 px-2"><?php esc_html_e('History', HYIPLAB_PLUGIN_NAME); ?></button></a>
                                </p>
                            </div>
                            <div class="interest-scheme__content text-sm-end">
                                <p class="mb-0 font-12"><?php esc_html_e('Interest', HYIPLAB_PLUGIN_NAME); ?></p>
                                <h5 class="mb-1 text--primary interests"></h5>
                                <p class="mb-0">
                                    <a href="<?php echo hyiplab_route_link('admin.report.invest.history'); ?>" class="btn btn-sm btn-primary-rgba font-12 px-2 speedUp"><?php esc_html_e('History', HYIPLAB_PLUGIN_NAME); ?></a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-6 col-md-12 col-xl-5">
                                <h5 class="card-title mb-0"><?php esc_html_e('Investment Statistics by Plan', HYIPLAB_PLUGIN_NAME); ?></h5>
                            </div>
                            <div class="col-sm-6 col-md-12 col-xl-7">
                                <div class="pair-option justify-content-md-start justify-content-xl-end">
                                    <select name="plan_statistics_invests" class="widget_select">
                                        <option value="all"><?php esc_html_e('All Invests', HYIPLAB_PLUGIN_NAME); ?></option>
                                        <option value="active"><?php esc_html_e('Active Invests', HYIPLAB_PLUGIN_NAME); ?></option>
                                        <option value="closed"><?php esc_html_e('Closed Invests', HYIPLAB_PLUGIN_NAME); ?></option>
                                    </select>
                                    <select name="plan_statistics_time" class="widget_select">
                                        <option value="all"><?php esc_html_e('All Time', HYIPLAB_PLUGIN_NAME); ?></option>
                                        <option value="week"><?php esc_html_e('Current Week', HYIPLAB_PLUGIN_NAME); ?></option>
                                        <option value="month"><?php esc_html_e('Current Month', HYIPLAB_PLUGIN_NAME); ?></option>
                                        <option value="year"><?php esc_html_e('Current Year', HYIPLAB_PLUGIN_NAME); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <div class="chart-info">
                                <a href="#" class="chart-info-toggle">
                                    <img src="<?php echo hyiplab_asset('admin/images/collapse.svg'); ?>" alt="image" class="chart-info-img">
                                </a>
                                <div class="chart-info-content">
                                    <ul class="chart-info-list plan-info-data"></ul>
                                </div>
                            </div>
                            <div class="chart-area chart-area--fixed">
                                <div class="plan_invest_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row align-items-center g-2">
                            <div class="col-sm-6">
                                <h5 class="card-title mb-0"><?php esc_html_e('Invest & Interest', HYIPLAB_PLUGIN_NAME); ?></h5>
                            </div>
                            <?php if (isset($firstInvestYear->date) && $firstInvestYear->date) { ?>
                                <div class="col-sm-6">
                                    <div class="pair-option">
                                        <select name="invest_interest_year" class="widget_select">
                                            <?php for ($i = $firstInvestYear->date; $i <= date('Y'); $i++) { ?>
                                                <option value="<?php echo esc_attr($i); ?>" <?php if (date('Y') == $i) echo 'selected'; ?>>
                                                    <?php echo esc_html($i); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <select name="invest_interest_month" class="widget_select">
                                            <option value="01" <?php if (date('m') == '01') echo 'selected'; ?>><?php esc_html_e('January', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="02" <?php if (date('m') == '02') echo 'selected'; ?>><?php esc_html_e('February', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="03" <?php if (date('m') == '03') echo 'selected'; ?>><?php esc_html_e('March', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="04" <?php if (date('m') == '04') echo 'selected'; ?>><?php esc_html_e('April', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="05" <?php if (date('m') == '05') echo 'selected'; ?>><?php esc_html_e('May', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="06" <?php if (date('m') == '06') echo 'selected'; ?>><?php esc_html_e('June', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="07" <?php if (date('m') == '07') echo 'selected'; ?>><?php esc_html_e('July', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="08" <?php if (date('m') == '08') echo 'selected'; ?>><?php esc_html_e('August', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="09" <?php if (date('m') == '09') echo 'selected'; ?>><?php esc_html_e('September', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="10" <?php if (date('m') == '10') echo 'selected'; ?>><?php esc_html_e('October', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="11" <?php if (date('m') == '11') echo 'selected'; ?>><?php esc_html_e('November', HYIPLAB_PLUGIN_NAME); ?></option>
                                            <option value="12" <?php if (date('m') == '12') echo 'selected'; ?>><?php esc_html_e('December', HYIPLAB_PLUGIN_NAME); ?></option>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas height="80" id="chartjs-boundary-area-chart" class="chartjs-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title mb-0"><?php esc_html_e('Recent Investments', HYIPLAB_PLUGIN_NAME); ?></div>
                    </div>
                    <div class="card-body">
                        <div class="plan-list d-flex flex-wrap flex-xxl-column gap-3 gap-xxl-0">
                            <?php foreach ($recentInvests as $invest) {
                                $plan = get_hyiplab_plan($invest->plan_id);
                                $time = get_hyiplab_time_setting($plan->time_setting_id);
                            ?>
                                <div class="plan-item-two">
                                    <div class="plan-info plan-inner-div">
                                        <div class="plan-name fw-bold"><?php echo esc_html($plan->name); ?> - <?php esc_html_e('Every', HYIPLAB_PLUGIN_NAME); ?>
                                            <?php echo esc_html($invest->time_name); ?>
                                            <?php echo esc_html($plan->interest_type) != 1 ? hyiplab_currency('sym') : ''; ?><?php echo hyiplab_show_amount($plan->interest); ?><?php echo esc_html($plan->interest_type) == 1 ? '%' : ''; ?>
                                            <?php esc_html_e('for', HYIPLAB_PLUGIN_NAME); ?> <?php if ($plan->lifetime == 0) { ?>
                                                <?php echo esc_html($plan->repeat_time); ?>
                                                <?php echo esc_html($time->name); ?>
                                            <?php } else { ?>
                                                <?php esc_html_e('LIFETIME', HYIPLAB_PLUGIN_NAME); ?>
                                            <?php } ?>
                                        </div>
                                        <div class="plan-desc text-end text-xl-start"><?php esc_html_e('Invested', HYIPLAB_PLUGIN_NAME); ?>: <span class="fw-bold"><?php echo hyiplab_show_amount($invest->amount); ?> <?php echo hyiplab_currency('text'); ?></span></div>
                                    </div>
                                    <div class="plan-start plan-inner-div">
                                        <p class="plan-label"><?php esc_html_e('Start Date', HYIPLAB_PLUGIN_NAME); ?></p>
                                        <p class="plan-value date">
                                            <?php echo hyiplab_show_date_time($invest->created_at, 'd M, y h:i A'); ?>
                                        </p>
                                    </div>
                                    <div class="plan-end plan-inner-div">
                                        <p class="plan-label"><?php esc_html_e('End Date', HYIPLAB_PLUGIN_NAME); ?></p>
                                        <p class="plan-value date">
                                            <?php echo hyiplab_show_date_time($invest->created_at, 'd M, y h:i A'); ?>
                                        </p>
                                    </div>
                                    <div class="plan-amount plan-inner-div text-end">
                                        <p class="plan-label"><?php esc_html_e('Net Profit', HYIPLAB_PLUGIN_NAME); ?></p>
                                        <p class="plan-value amount">
                                            <?php echo esc_html($invest->period) != -1 ? hyiplab_show_amount($invest->period * $invest->interest) . ' ' . hyiplab_currency('text') : '---'; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .widget_select {
        padding: 3px 3px;
        font-size: 13px;
    }
</style>

<?php
wp_enqueue_script('chart', hyiplab_asset('admin/js/vendor/chart.js.2.8.0.js'), array('jquery'), null, true);
?>
<script>
    jQuery(document).ready(function($) {
        "use strict";
        $('[name=invest_time]').on('change', function() {
            let time = $(this).val();
            var url = "<?php echo admin_url('admin-ajax.php?action=invest-total'); ?>";
            $.get(url, {
                time: time
            }, function(response) {

                $('.time_type').text(time);
                $('.total_invest').text(response.total_invest.toFixed(2));

                let upDown = `<small>Previous ${time} invest was zero</small>`;
                if (response.invest_diff != 0) {
                    if (response.up_down == 'up') {
                        var className = 'success'
                    } else {
                        var className = 'danger';
                    }
                    upDown =
                        `<span class="badge badge-${className}-inverse font-16">${response.invest_diff}%<i class="las la-arrow-${response.up_down}"></i></span>`;
                }

                $('.up_down').html(upDown);
                $('.my_invest_canvas').html(
                    '<canvas height="150" id="invest_chart" class="chartjs-chart mt-4"></canvas>'
                )
                var ctx = document.getElementById('invest_chart');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(response.invests),
                        datasets: [{
                            data: Object.values(response.invests),
                            backgroundColor: [
                                <?php for ($i = 0; $i < 365; $i++) { ?> '#6c5ce7', <?php } ?>
                            ],
                            borderColor: [
                                'rgba(231, 80, 90, 0.75)'
                            ],
                            borderWidth: 0,

                        }]
                    },
                    options: {
                        aspectRatio: 1,
                        responsive: true,
                        maintainAspectRatio: true,
                        elements: {
                            line: {
                                tension: 0 // disables bezier curves
                            }
                        },
                        scales: {
                            xAxes: [{
                                display: false
                            }],
                            yAxes: [{
                                display: false
                            }]
                        },
                        legend: {
                            display: false,
                        },
                        tooltips: {
                            callbacks: {
                                label: (tooltipItem, data) => data.datasets[0].data[
                                    tooltipItem.index] + ' <?php echo hyiplab_currency('text'); ?>'
                            }
                        }
                    }
                });
            });
        }).change();

        $('[name=plan_statistics_time]').on('change', function() {
            let time = $('[name=plan_statistics_time]').val();
            let investType = $('[name=plan_statistics_invests]').val();
            var url = "<?php echo admin_url('admin-ajax.php?action=invest-plan'); ?>";
            $.get(url, {
                time: time,
                invest_type: investType
            }, function(response) {
                $('.plan_invest_canvas').html(
                    '<canvas height="250" id="plan_invest_statistics"></canvas>');
                let invests = response.invest_data;
                let planInfo = '';
                let investAmount = [];
                let planName = [];
                let planUrl = "<?php echo hyiplab_route_link('admin.report.invest.history', false); ?>";
                $.each(invests, function(key, invest) {
                    let investPercent = (invest.investAmount / response.total_invest) * 100;
                    investAmount.push(parseFloat(invest.investAmount).toFixed(2));
                    planName.push(invest.plan.name);
                    planInfo +=
                        `<li class="chart-info-list-item"><i class="fas fa-plane planPoint me-2"></i>${investPercent.toFixed(2)}% - ${invest.plan} <a href="${planUrl}"><i class="las la-info-circle"></i></a></li>`
                });
                $('.plan-info-data').html(planInfo);

                /* -- Chartjs - Pie Chart -- */
                var pieChartID = document.getElementById("plan_invest_statistics").getContext('2d');
                var pieChart = new Chart(pieChartID, {
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: investAmount,
                            borderColor: 'transparent',
                            backgroundColor: planColors()
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: false
                        },
                        tooltips: {
                            callbacks: {
                                label: (tooltipItem, data) => data.datasets[0].data[
                                    tooltipItem.index] + ' <?php echo hyiplab_currency('text'); ?>'
                            }
                        }
                    }
                });

                var planPoints = $('.planPoint');
                planPoints.each(function(key, planPoint) {
                    var planPoint = $(planPoint)
                    planPoint.css('color', planColors()[key])
                })

            });
        }).change();

        $('[name=plan_statistics_invests]').on('change', function() {
            $('[name=plan_statistics_time]').trigger('change');
        });


        $('[name=invest_interest_time]').on('change', function() {
            let time = $(this).val();
            var url = "<?php echo admin_url('admin-ajax.php?action=invest-interest'); ?>";
            $.get(url, {
                time: time
            }, function(response) {
                $('.runningInvests').text(`<?php echo hyiplab_currency('sym'); ?>${response.running_invests}`);
                $('.expiredInvests').text(`<?php echo hyiplab_currency('sym'); ?>${response.expired_invests}`);
                $('.interests').text(`<?php echo hyiplab_currency('sym'); ?>${response.interests}`);
            });
        }).change();


        $('[name=invest_interest_year]').on('change', function() {
            let year = $('[name=invest_interest_year]').val();
            let month = $('[name=invest_interest_month]').val();
            let url = "<?php echo admin_url('admin-ajax.php?action=invest-chart'); ?>"
            $.get(url, {
                year: year,
                month: month
            }, function(response) {
                var boundaryAreaID = document.getElementById("chartjs-boundary-area-chart")
                    .getContext('2d');
                var boundaryArea = new Chart(boundaryAreaID, {
                    type: 'line',
                    data: {
                        labels: response.keys,
                        datasets: [{
                                backgroundColor: ["rgba(110, 129, 220,0.2)"],
                                borderColor: ["#6e81dc"],
                                pointBorderColor: ["#6e81dc", "#6e81dc", "#6e81dc",
                                    "#6e81dc", "#6e81dc", "#6e81dc", "#6e81dc"
                                ],
                                pointBackgroundColor: ["#6e81dc", "#6e81dc", "#6e81dc",
                                    "#6e81dc", "#6e81dc", "#6e81dc", "#6e81dc"
                                ],
                                pointBorderWidth: 0,
                                data: response.invests,
                                label: 'Invests',
                                fill: 'start'
                            },
                            {
                                backgroundColor: ["rgba(252, 193, 0,0.2)"],
                                borderColor: ["#fcc100"],
                                pointBorderColor: ["#fcc100", "#fcc100", "#fcc100",
                                    "#fcc100", "#fcc100", "#fcc100", "#fcc100"
                                ],
                                pointBackgroundColor: ["#fcc100", "#fcc100", "#fcc100",
                                    "#fcc100", "#fcc100", "#fcc100", "#fcc100"
                                ],
                                pointBorderWidth: 0,
                                data: response.interests,
                                label: 'Interests',
                                fill: 'start'
                            }
                        ]
                    },
                    options: {
                        title: {
                            text: 'fill: start',
                            display: false
                        },
                        maintainAspectRatio: true,
                        spanGaps: true,
                        elements: {
                            point: {
                                radius: 0,
                            }
                        },
                        plugins: {
                            filler: {
                                propagate: false
                            }
                        },
                        legend: {
                            display: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 0
                                },
                                gridLines: {
                                    color: '#dcdde1',
                                    lineWidth: 1,
                                    borderDash: [1]
                                }
                            }],
                            yAxes: [{
                                display: true,
                                gridLines: {
                                    color: '#dcdde1',
                                    lineWidth: 1,
                                    borderDash: [1],
                                    zeroLineColor: '#dcdde1',
                                }
                            }]
                        }
                    }
                });

            });
        }).change();

        $('[name=invest_interest_month]').on('change', function() {
            $('[name=invest_interest_year]').trigger('change');
        });


        var doughnutChartID = document.getElementById("interest_by_plan").getContext('2d');
        var doughnutChart = new Chart(doughnutChartID, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: <?php echo wp_json_encode( array_values($interestByPlans) );?>,
                    borderColor: 'transparent',
                    backgroundColor: planColors(),
                }],
            },
            options: {
                responsive: true,
                cutoutPercentage: 75,
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: false,
                    text: 'Chart.js Doughnut Chart'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                tooltips: {
                    callbacks: {
                        label: (tooltipItem, data) => data.datasets[0].data[tooltipItem.index] +
                            ' <?php echo hyiplab_currency('text');?>'
                    }
                }
            }
        });


        var planPointInterests = $('.planPointInterest');
        planPointInterests.each(function(key, planPointInterest) {
            var planPointInterest = $(planPointInterest)
            planPointInterest.css('color', planColors()[key])
        })


        function planColors() {
            return [
                '#ff7675',
                '#6c5ce7',
                '#ffa62b',
                '#ffeaa7',
                '#D980FA',
                '#fccbcb',
                '#45aaf2',
                '#05dfd7',
                '#FF00F6',
                '#1e90ff',
                '#2ed573',
                '#eccc68',
                '#ff5200',
                '#cd84f1',
                '#7efff5',
                '#7158e2',
                '#fff200',
                '#ff9ff3',
                '#08ffc8',
                '#3742fa',
                '#1089ff',
                '#70FF61',
                '#bf9fee',
                '#574b90'
            ]
        }

        let chartToggle = $('.chart-info-toggle');
        let chartContent = $(".chart-info-content");
        if (chartToggle || chartContent) {
            chartToggle.each(function() {
                $(this).on("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).siblings().toggleClass("is-open");
                });
            });
            chartContent.each(function() {
                $(this).on("click", function(e) {
                    e.stopPropagation();
                });
            });
            $(document).on("click", function() {
                chartContent.removeClass("is-open");
            });
        }

        $('.exit-btn').on('click', function() {
            $(this).toggleClass('active');
        });

    });

    var elems = document.querySelector(".full-view");

    function openFullscreen() {
        if (elems.requestFullscreen) {
            elems.requestFullscreen();
        } else if (elems.mozRequestFullScreen) {
            /* Firefox */
            elems.mozRequestFullScreen();
        } else if (elems.webkitRequestFullscreen) {
            /* Chrome, Safari & Opera */
            elems.webkitRequestFullscreen();
        } else if (elems.msRequestFullscreen) {
            /* IE/Edge */
            elems.msRequestFullscreen();
        }
    }

    function closeFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            /* Firefox */
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            /* Chrome, Safari and Opera */
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            /* IE/Edge */
            document.msExitFullscreen();
        }
    }
</script>