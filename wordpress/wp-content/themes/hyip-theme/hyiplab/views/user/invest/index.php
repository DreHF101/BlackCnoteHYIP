<?php hyiplab_layout('user/layouts/master'); ?>
<div class="mb-4">
    <p><?php esc_html_e('Investment', HYIPLAB_PLUGIN_NAME); ?></p>
    <h3><?php esc_html_e('All Investment', HYIPLAB_PLUGIN_NAME); ?></h3>
</div>

<div class="row gy-4">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <p class="mb-2 fw-bold"><?php esc_html_e('Total Invest', HYIPLAB_PLUGIN_NAME); ?></p>
                        <h4 class="text--base"><sup class="top-0 fw-light me-1"><?php echo hyiplab_currency('sym'); ?></sup><?php echo hyiplab_show_amount($totalInvest); ?></h4>
                    </div>
                    <div>
                        <p class="mb-2 fw-bold"><?php esc_html_e('Total Profit', HYIPLAB_PLUGIN_NAME); ?></p>
                        <h4 class="text--base"><sup class="top-0 fw-light me-1"><?php echo hyiplab_currency('sym'); ?></sup><?php echo hyiplab_show_amount($totalProfit); ?></h4>
                    </div>
                </div>
                <div class="d-flex flex-wrap justify-content-between mt-3 mt-sm-4 gap-2">
                    <a href="<?php echo hyiplab_route_link('user.plan.index'); ?>" class="btn btn--sm btn--base"><?php esc_html_e('Invest Now', HYIPLAB_PLUGIN_NAME); ?> <i class="las la-arrow-right fs--12px ms-1"></i></a>
                    <a href="<?php echo hyiplab_route_link('user.withdraw.index'); ?>" class="btn btn--sm btn--secondary"><?php esc_html_e('Withdraw Now', HYIPLAB_PLUGIN_NAME); ?> <i class="las la-arrow-right fs--12px ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-body">
                <?php if (!hyiplab_check_empty($investChart)) { ?>
                    <div class="invest-statistics d-flex flex-wrap justify-content-between align-items-center">
                        <div class="flex-shrink-0">
                            <?php foreach ($investChart as $chart) {
                                $plan = get_hyiplab_plan($chart->plan_id);
                            ?>
                                <p class="my-2"><i class="fas fa-plane planPoint me-2"></i><?php echo hyiplab_show_amount(($chart->investAmount / $totalInvest) * 100); ?>% - <?php echo esc_html($plan->name); ?></p>
                            <?php } ?>
                        </div>
                        <div class="invest-statistics__chart">
                            <canvas height="150" id="chartjs-pie-chart" style="width: 150px;"></canvas>
                        </div>
                    </div>
                <?php } else { ?>
                    <h4 class="text-center"><?php esc_html_e('No Investment Found Yet', HYIPLAB_PLUGIN_NAME); ?></h4>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="d-flex justify-content-between">
        <h5 class="title mb-3"><?php esc_html_e('Active Plan', HYIPLAB_PLUGIN_NAME); ?> <span class="count text-base">(<?php echo esc_html($activePlan); ?>)</span></h5>
        <a href="<?php echo hyiplab_route_link('user.invest.log'); ?>" class="link-color"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?>
            <i class="las la-arrow-right"></i>
        </a>
    </div>
    <div class="plan-list d-flex flex-wrap flex-xxl-column gap-3 gap-xxl-0">
        <?php 
            hyiplab_include('user/partials/invest_history', ['invests' => $invests]);
        ?>
        <?php if (hyiplab_check_empty($invests)) { ?>
            <div class="accordion-body text-center bg-white p-4">
                <h4 class="text--muted"><i class="far fa-frown"></i> <?php esc_html_e('Data not found', HYIPLAB_PLUGIN_NAME); ?></h4>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    
    jQuery(document).ready(function($) {
        "use strict";
        /* -- Chartjs - Pie Chart -- */
        var pieChartID = document.getElementById("chartjs-pie-chart").getContext('2d');
        var pieChart = new Chart(pieChartID, {
            type: 'pie',
            data: {
                datasets: [{
                    data: [
                        <?php foreach ($investChart as $chart) { ?>
                            <?php echo esc_html($chart->investAmount); ?>,
                        <?php } ?>
                    ],
                    borderColor: 'transparent',
                    backgroundColor: planColors(),
                    label: 'Dataset 1'
                }],
                labels: [
                    <?php foreach ($investChart as $chart) {
                        $plan = get_hyiplab_plan($chart->plan_id);
                    ?> '<?php echo esc_html($plan->name); ?>',
                    <?php } ?>
                ]
            },
            options: {
                responsive: true,
                legend: {
                    display: false
                }
            }
        });
        var planPoints = $('.planPoint');
        planPoints.each(function(key, planPoint) {
            var planPoint = $(planPoint)
            planPoint.css('color', planColors()[key])
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
    });
</script>

<?php
wp_enqueue_script('chart-js', hyiplab_asset('public/js/chart.min.js'), array('jquery'), null, true);
?>