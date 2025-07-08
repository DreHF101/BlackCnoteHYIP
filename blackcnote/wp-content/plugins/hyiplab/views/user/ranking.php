<?php hyiplab_layout('user/layouts/master'); ?>
<div class="mb-4">
    <h3><?php esc_html_e('Rankings', HYIPLAB_PLUGIN_NAME); ?></h3>
</div>
<hr>
<div class="row justify-content-center">
    <?php if ($nextRanking) { ?>
        <div class="col-md-12 mb-4">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="row gy-4 align-items-center">
                        <div class="col-lg-4 col-md-6">
                            <div class="d-flex align-items-center raking-invest">
                                <img class="me-2" src="<?php echo hyiplab_get_image(hyiplab_file_path('userRanking') . '/' . $nextRanking->icon); ?>" alt="image">
                                <div>
                                    <span><?php esc_html_e('My Invest', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <h5><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($total_invests); ?> / <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($nextRanking->minimum_invest ?? 0); ?></h6>
                                        <?php if ($nextRanking->minimum_invest - $total_invests > 0) { ?>
                                            <span><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($nextRanking->minimum_invest - $total_invests); ?> <?php esc_html_e('To unlock', HYIPLAB_PLUGIN_NAME); ?></span>
                                        <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="raking-common text-center">
                                <span><?php esc_html_e('No. of Direct Referral', HYIPLAB_PLUGIN_NAME); ?></span>
                                <h5><?php echo esc_html($activeReferrals); ?> / <?php echo esc_html($nextRanking->min_referral); ?></h5>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="raking-common text-center">
                                <span><?php esc_html_e('Team Invest', HYIPLAB_PLUGIN_NAME); ?></span>
                                <h5><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($team_invests); ?> / <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($nextRanking->min_referral_invest); ?></h5>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="raking-common text-center">
                                <span><?php esc_html_e('Bonus', HYIPLAB_PLUGIN_NAME); ?></span>
                                <h5><?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($nextRanking->bonus); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="col-md-12">
        <div class="row gy-4">
            <?php
            $iteration = 0;
            foreach ($userRankings as $userRanking) {
            ?>
                <?php if ($userRankingId >= $userRanking->id) {
                    $progressPercent = 100;
                } else {

                    $myInvestPercent = ($total_invests / $userRanking->minimum_invest) * 100;
                    $refInvestPercent = ($team_invests / $userRanking->min_referral_invest) * 100;
                    $refCountPercent = ($activeReferrals / $userRanking->min_referral) * 100;

                    $myInvestPercent = $myInvestPercent < 100 ? $myInvestPercent : 100;
                    $refInvestPercent = $refInvestPercent < 100 ? $refInvestPercent : 100;
                    $refCountPercent = $refCountPercent < 100 ? $refCountPercent : 100;
                    $progressPercent = ($myInvestPercent + $refInvestPercent + $refCountPercent) / 3;
                } ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="invest-badge text-center">
                        <div class="invest-badge__thumb">
                            <div class="invest-badge__thumb__mask <?php if ($nextRanking->id < $userRanking->id) echo 'badge-lock'; ?>" data-progress="<?php echo $nextRanking->id < $userRanking->id ? 0 : $progressPercent; ?>">
                                <img src="<?php echo hyiplab_get_image(hyiplab_file_path('userRanking') . '/' . $userRanking->icon); ?>" alt="image">
                            </div>
                        </div>
                        <h4 class="invest-badge__title">
                            <?php echo esc_html($userRanking->name); ?>
                        </h4>
                        <p class=invest-badge__subtitle><?php esc_html_e('Bonus', HYIPLAB_PLUGIN_NAME); ?> - <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($userRanking->bonus); ?></p>
                        <ul class="invest-badge__list invest-badge__details p-3  invest-badge__details-<?php echo $iteration % 4 == 0 ? 4 : $iteration % 4; ?> <?php echo $iteration % 3 == 0 ? 'invest-badge__detail_one' : 'invest-badge__detail_two'; ?>">
                            <li class="d-flex "><span><?php esc_html_e('Level', HYIPLAB_PLUGIN_NAME); ?> </span>
                                <span>: <?php echo esc_html($userRanking->level); ?></span>
                            </li>
                            <li class="d-flex "><span><?php esc_html_e('Minimum Invest', HYIPLAB_PLUGIN_NAME); ?> </span>
                                <span>: <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($userRanking->minimum_invest); ?></span>
                            </li>
                            <li class="d-flex "><span><?php esc_html_e('No. of Direct Referral', HYIPLAB_PLUGIN_NAME); ?> </span>
                                <span>: <?php echo esc_html($userRanking->min_referral); ?></span>
                            </li>
                            <li class="d-flex "><span><?php esc_html_e('Referral Invest', HYIPLAB_PLUGIN_NAME); ?> </span>
                                <span>: <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($userRanking->min_referral_invest); ?></span>
                            </li>
                            <li class="d-flex "><span><?php esc_html_e('Bonus', HYIPLAB_PLUGIN_NAME); ?> </span>
                                <span>: <?php echo hyiplab_currency('sym'); ?><?php echo hyiplab_show_amount($userRanking->bonus); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php $iteration++;
            } ?>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        var elements = $('.invest-badge__thumb__mask');
        elements.each(function(index, element) {
            let progress = $(element).data('progress');
            element.style.setProperty('--before-height', `${progress}%`);
        });
    })(jQuery);
</script>