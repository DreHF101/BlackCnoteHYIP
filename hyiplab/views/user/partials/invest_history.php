<?php
foreach ($invests as $invest) {
    $plan = get_hyiplab_plan($invest->plan_id);
    if ($invest->last_time) {
        $start = $invest->last_time;
    } else {
        $start = $invest->created_at;
    }
    $time = get_hyiplab_time_setting($plan->time_setting_id);
?>
    <div class="plan-item-two">
        <div class="plan-info plan-inner-div">
            <div class="d-flex align-items-center gap-3">
                <?php if ($invest->status == 1) { ?>
                    <svg class="custom-progress">
                        <circle class="progress-circle" cx="20" cy="22" r="16" style="stroke-dasharray: 100; stroke-dashoffset: calc(100 - <?php echo (diffDatePercent($start, $invest->next_time) * 100) / 100; ?>)" ; />
                        <circle class="bg-circle" cx="20" cy="22" r="16" style="stroke-dasharray: 100; stroke-dashoffset: 0" ; />
                    </svg>
                <?php } ?>

                <div class="plan-name-data">
                    <div class="plan-name fw-bold"><?php echo esc_html($plan->name); ?> - <?php esc_html_e('Every', HYIPLAB_PLUGIN_NAME); ?> <?php echo esc_html($invest->time_name); ?> <?php echo esc_html($plan->interest_type != 1 ? hyiplab_currency('sym') : ''); ?><?php echo hyiplab_show_amount($plan->interest); ?> <?php ($plan->interest_type == 1) ? '%' : ''; ?> <?php esc_html_e('for', HYIPLAB_PLUGIN_NAME); ?> <?php if ($plan->lifetime == 0) {
                                                                                                                                                                                                                                                                                                                                                                                                                                echo esc_html($plan->repeat_time . ' ' . $time->name);
                                                                                                                                                                                                                                                                                                                                                                                                                            } else { ?> <?php esc_html_e('LIFETIME', HYIPLAB_PLUGIN_NAME); ?> <?php } ?></div>
                    <div class="plan-desc"><?php esc_html_e('Invested', HYIPLAB_PLUGIN_NAME); ?>: <span class="fw-bold"><?php echo hyiplab_show_amount($invest->amount); ?> <?php echo hyiplab_currency('text'); ?> <?php if ($invest->capital_status) { ?>
                                <small class="capital-back"><i>(<?php esc_html_e('Capital will be back', HYIPLAB_PLUGIN_NAME); ?>)</i></small><?php } ?> </span></div>
                </div>
            </div>
        </div>
        <div class="plan-start plan-inner-div">
            <p class="plan-label"><?php esc_html_e('Start Date', HYIPLAB_PLUGIN_NAME); ?></p>
            <p class="plan-value date"><?php echo hyiplab_show_date_time($invest->created_at, 'M d, Y h:i A'); ?></p>
        </div>
        <div class="plan-inner-div">
            <p class="plan-label"><?php esc_html_e('Next Return', HYIPLAB_PLUGIN_NAME); ?></p>
            <p class="plan-value"><?php echo hyiplab_show_date_time($invest->next_time, 'M d, Y h:i A'); ?></p>
        </div>
        <div class="plan-inner-div text-end">
            <p class="plan-label"><?php esc_html_e('Total Return', HYIPLAB_PLUGIN_NAME); ?></p>
            <p class="plan-value amount"><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($invest->interest); ?> x <?php echo esc_html($invest->return_rec_time); ?> = <?php echo hyiplab_show_amount($invest->paid); ?> <?php echo hyiplab_currency('text'); ?></p>
        </div>
        <div class="plan-inner-div text-end justify-content-end">
            <a href="<?php echo hyiplab_route_link('user.invest.detail');?>?id=<?php echo hyiplab_encrypt($invest->id);?>" class="invest-details-link">
                <i class="las la-angle-right"></i>
            </a>
        </div>
    </div>
<?php } ?>

<style>
    .custom-progress {
        max-width: 40px !important;
        max-height: 40px;
        transform: rotate(-90deg);
    }

    .custom-progress .bg-circle {
        stroke: #00000011;
        fill: none;
        stroke-width: 4px;
        position: relative;
        z-index: -1;
    }

    .custom-progress .progress-circle {
        fill: none;
        stroke: hsl(var(--base));
        stroke-width: 4px;
        z-index: 11;
        position: absolute;
    }

    .expired-time-circle {
        position: relative;
        border: none !important;
        height: 38px;
        width: 38px;
        margin-right: 7px;
    }

    .expired-time-circle::before {
        position: absolute;
        content: '';
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid #dbdce1;
    }

    .expired-time-circle.danger-border .animation-circle {
        border-color: hsl(var(--base)) !important;
    }

    .animation-circle {
        position: absolute;
        top: 0;
        left: 0;
        border: 4px solid hsl(var(--base));
        height: 100%;
        width: 100%;
        border-radius: 150px;
        transform: rotateY(180deg);
        animation-name: clipCircle;
        animation-iteration-count: 1;
        animation-timing-function: cubic-bezier(0, 0, 1, 1);
        z-index: 1;
    }

    .account-wrapper .left .top {
        margin-top: 0;
    }

    .account-wrapper .left,
    .account-wrapper .right {
        width: 100%;
    }

    .account-wrapper .right {
        padding-left: 0;
        margin-top: 35px;
    }

    @keyframes clipCircle {
        0% {
            clip-path: polygon(50% 50%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%);
            /* center, top-center*/
        }

        12.5% {
            clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%);
            /* center, top-center, top-left*/
        }

        25% {
            clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 50%, 0% 50%, 0% 50%, 0% 50%, 0% 50%, 0% 50%);
            /* center, top-center, top-left, left-center*/
        }

        37.5% {
            clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 0% 100%, 0% 100%, 0% 100%, 0% 100%, 0% 100%);
            /* center, top-center, top-left, left-center, bottom-left*/
        }

        50% {
            clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 50% 100%);
            /* center, top-center, top-left, left-center, bottom-left, bottom-center*/
        }

        62.5% {
            clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 100% 100%, 100% 100%, 100% 100%, 100% 100%);
            /* center, top-center, top-left, left-center, bottom-left, bottom-center, bottom-right*/
        }

        75% {
            clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 100% 100%, 100% 50%, 100% 50%, 100% 50%);
            /* center, top-center, top-left, left-center, bottom-left, bottom-center, bottom-right, right-center*/
        }

        87.5% {
            clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 100% 100%, 100% 50%, 100% 0%, 100% 0%);
            /* center, top-center, top-left, left-center, bottom-left, bottom-center, bottom-right, right-center top-right*/
        }

        100% {
            clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 100% 100%, 100% 50%, 100% 0%, 50% 0%);
            /* center, top-center, top-left, left-center, bottom-left, bottom-center, bottom-right, right-center top-right, top-center*/
        }
    }

    .capital-back {
        font-size: 10px;
    }

    .invest-details-link{
        height: 40px;
        width: 40px;
        line-height: 40px;
        text-align: center;
        border: 1px solid #c3bfbf;
        border-radius: 50px;
        color: #c3bfbf;
    }
    .invest-details-link:hover{
        border-color: #a7a7a7;
        color: #a7a7a7;
    }
    
</style>

<script>
    jQuery(document).ready(function($) {
        "use strict";
        let animationCircle = $('.animation-circle');
        animationCircle.css('animation-duration', function() {
            let duration = ($(this).data('duration'));
            return duration;
        });
    })
</script>