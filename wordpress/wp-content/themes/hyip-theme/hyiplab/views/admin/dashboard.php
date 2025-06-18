<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row gy-4">
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="fas fa-hand-holding-usd overlay-icon text--success"></i>
            <div class="widget-two__icon b-radius--5 bg--success">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="widget-two__content">
                <h3><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($deposit['total']); ?></h3>
                <p><?php esc_html_e('Total Deposited', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.deposit.list'); ?>" class="widget-two__btn btn btn-outline--success">
                <?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
    </div>
    <!-- dashboard-w1 end -->
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="fas fa-spinner overlay-icon text--warning"></i>
            <div class="widget-two__icon b-radius--5 bg--warning">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="widget-two__content">
                <h3><?php echo esc_html($deposit['pending']); ?></h3>
                <p><?php esc_html_e('Pending Deposits', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.deposit.pending'); ?>" class="widget-two__btn btn btn-outline--warning">
                <?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
    </div>
    <!-- dashboard-w1 end -->
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="fas fa-ban overlay-icon text--danger"></i>
            <div class="widget-two__icon b-radius--5 bg--danger">
                <i class="fas fa-ban"></i>
            </div>
            <div class="widget-two__content">
                <h3><?php echo esc_html($deposit['rejected']); ?></h3>
                <p><?php esc_html_e('Rejected Deposits', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.deposit.rejected'); ?>" class="widget-two__btn btn btn-outline--danger">
                <?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
    </div>
    <!-- dashboard-w1 end -->
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="fas fa-percentage overlay-icon text--primary"></i>
            <div class="widget-two__icon b-radius--5   bg--primary  ">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="widget-two__content">
                <h3><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($deposit['charge']); ?></h3>
                <p><?php esc_html_e('Deposited Charge', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.deposit.list'); ?>" class="widget-two__btn btn btn-outline--primary">
                <?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
    </div>
    <!-- dashboard-w1 end -->
</div><!-- /row -->

<div class="row gy-4 mt-2">
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="lar la-credit-card overlay-icon text--success"></i>
            <div class="widget-two__icon b-radius--5 border border--success text--success">
                <i class="lar la-credit-card"></i>
            </div>
            <div class="widget-two__content">
                <h3><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($withdraw['total']); ?></h3>
                <p><?php esc_html_e('Total Withdrawn', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.withdraw.log'); ?>" class="widget-two__btn btn btn-outline--success">
                <?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="las la-sync overlay-icon text--warning"></i>
            <div class="widget-two__icon b-radius--5 border border--warning text--warning">
                <i class="las la-sync"></i>
            </div>
            <div class="widget-two__content">
                <h3><?php echo esc_html($withdraw['pending']); ?></h3>
                <p><?php esc_html_e('Pending Withdrawals', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.withdraw.pending'); ?>" class="widget-two__btn btn btn-outline--warning">
                <?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="las la-times-circle overlay-icon text--danger"></i>
            <div class="widget-two__icon b-radius--5 border border--danger text--danger">
                <i class="las la-times-circle"></i>
            </div>
            <div class="widget-two__content">
                <h3><?php echo esc_html($withdraw['rejected']); ?></h3>
                <p><?php esc_html_e('Rejected Withdrawals', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.withdraw.rejected'); ?>" class="widget-two__btn btn btn-outline--danger">
                <?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="las la-percent overlay-icon text--primary"></i>
            <div class="widget-two__icon b-radius--5 border border--primary text--primary">
                <i class="las la-percent"></i>
            </div>
            <div class="widget-two__content">
                <h3><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($withdraw['charge']); ?></h3>
                <p><?php esc_html_e('Withdrawal Charge', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.withdraw.log'); ?>" class="widget-two__btn btn btn-outline--primary">
                <?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?>
            </a>
        </div>
    </div>
</div>

<div class="row gy-4 mt-2">
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
            <i class="las la-chart-bar overlay-icon text--white"></i>
            <div class="widget-two__icon b-radius--5 bg--primary">
                <i class="las la-chart-bar"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($invest['total']); ?></h3>
                <p class="text-white"><?php esc_html_e('Total Investment', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.report.invest.history'); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
        </div>
    </div><!-- dashboard-w1 end -->
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--1">
            <i class="las la-chart-pie overlay-icon text--white"></i>
            <div class="widget-two__icon b-radius--5 bg--primary">
                <i class="las la-chart-pie"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($invest['interest']); ?></h3>
                <p class="text-white"><?php esc_html_e('Total Interest', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.report.invest.history'); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
        </div>
    </div><!-- dashboard-w1 end -->
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--14">
            <i class="las la-chart-area overlay-icon text--white"></i>
            <div class="widget-two__icon b-radius--5 bg--primary">
                <i class="las la-chart-area"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($invest['active']); ?></h3>
                <p class="text-white"><?php esc_html_e('Active Investments', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.report.invest.history'); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
        </div>
    </div><!-- dashboard-w1 end -->
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--19">
            <i class="las la-chart-line overlay-icon text--white"></i>
            <div class="widget-two__icon b-radius--5 bg--primary">
                <i class="las la-chart-line"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($invest['close']); ?></h3>
                <p class="text-white"><?php esc_html_e('Closed Investment', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
            <a href="<?php echo hyiplab_route_link('admin.report.invest.history'); ?>" class="widget-two__btn"><?php esc_html_e('View All', HYIPLAB_PLUGIN_NAME); ?></a>
        </div>
    </div><!-- dashboard-w1 end -->
</div><!-- row end-->

<div class="row mt-2">
    <div class="col-xl-12 mb-30">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php esc_html_e('Transactions Report', HYIPLAB_PLUGIN_NAME); ?> (<?php esc_html_e('Last 30 Days', HYIPLAB_PLUGIN_NAME); ?>)</h5>
                <div id="apex-line"></div>
            </div>
        </div>
    </div>
</div>

<?php
$html = "";
$last_cron = get_option('hyiplab_last_cron_run');
if ($last_cron) {
    if( hyiplab_date()->parse($last_cron)->addMinutes(10)->toTimeStamp() < hyiplab_date()->toTimeStamp() ){
        $btn = "btn--danger";
    } else {
        $btn  = "btn--success";
    }
    $html = '<a class="btn '.$btn.'"><i class="fa fa-fw fa-clock"></i>'.esc_html__('Last Cron Run', HYIPLAB_PLUGIN_NAME).' : '. hyiplab_diff_for_humans($last_cron) .'</a>';
}
hyiplab_push_breadcrumb($html);
?>

<?php wp_enqueue_script('apexcharts', hyiplab_asset('admin/js/vendor/apexcharts.min.js'), array('jquery'), null, true); ?>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        // apex-line chart
        var options = {
            chart: {
                height: 450,
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
                    opacity: 0.08
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
                    name: "Plus Transactions",
                    data: [
                        <?php foreach ($plusTrx as $trx) { ?>
                            <?php echo hyiplab_get_amount($trx->amount); ?>,
                        <?php } ?>
                    ]
                },
                {
                    name: "Minus Transactions",
                    data: [
                        <?php foreach ($minusTrx as $trx) { ?>
                            <?php echo hyiplab_get_amount($trx->amount); ?>,
                        <?php } ?>
                    ]
                }
            ],
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: [
                    <?php foreach ($plusTrx as $trx) { ?> "<?php echo date('d F', strtotime($trx->date)); ?>",
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
        
        var chart = new ApexCharts(document.querySelector("#apex-line"), options);
        chart.render();
    });
</script>