<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    <link rel="shortcut icon" href="<?php echo esc_url(hyiplab_asset('global/images/favicon.png')); ?>" type="image/x-icon">
    <style>
        .pb-120 {
            padding-bottom: clamp(40px, 4vw, 40px);
        }

        .pt-120 {
            padding-top: clamp(40px, 4vw, 40px);
        }

        .container {
            max-width: 1140px;
        }

        body.admin-bar .dashboard-sidebar {
            padding-top: 52px;
        }
    </style>
    <?php wp_head(); ?>
</head>

<body <?php body_class('vl-public'); ?>>

    <div class="d-flex flex-wrap">

        <div class="dashboard-sidebar" id="dashboard-sidebar">
            <button class="btn-close dash-sidebar-close d-xl-none"></button>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                <img src="<?php echo hyiplab_asset('global/images/logo.png'); ?>" alt="images">
            </a>
            <div class="bg--lights">
                <div class="profile-info">
                    <p class="fs--13px mb-3 fw-bold"><?php esc_html_e('ACCOUNT BALANCE', HYIPLAB_PLUGIN_NAME); ?></p>
                    <?php
                    $depositWallet = hyiplab_balance(hyiplab_auth()->user->ID, 'deposit_wallet');
                    $interestWallet = hyiplab_balance(hyiplab_auth()->user->ID, 'interest_wallet');
                    ?>
                    <h4 class="usd-balance text--base mb-2 fs--30"><?php echo hyiplab_show_amount($depositWallet) ?> <sub class="top-0 fs--13px"><?php echo hyiplab_currency('text') ?> <small>(<?php esc_html_e('Deposit Wallet', HYIPLAB_PLUGIN_NAME); ?>)</small> </sub></h4>
                    <p class="btc-balance fw-medium fs--18px"><?php echo hyiplab_show_amount($interestWallet) ?> <sub class="top-0 fs--13px"><?php echo hyiplab_currency('text') ?> <small>(<?php esc_html_e('Interest Wallet', HYIPLAB_PLUGIN_NAME); ?>)</small></sub></p>
                    <div class="mt-4 d-flex flex-wrap gap-2">
                        <a href="<?php echo hyiplab_route_link('user.deposit.index') ?>" class="btn btn--base btn--smd"><?php esc_html_e('Deposit', HYIPLAB_PLUGIN_NAME); ?></a>
                        <a href="<?php echo hyiplab_route_link('user.withdraw.index') ?>" class="btn btn--secondary btn--smd"><?php esc_html_e('Withdraw', HYIPLAB_PLUGIN_NAME); ?></a>
                    </div>
                </div>
            </div>
            <ul class="sidebar-menu">

                <?php if (current_user_can('administrator')) { ?>
                    <li>
                        <a href="<?php echo esc_url(home_url('/wp-admin')); ?>">
                            <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/2fa.png')); ?>" alt="icon"> <?php esc_html_e('WP Admin', HYIPLAB_PLUGIN_NAME); ?>
                        </a>
                    </li>
                <?php } ?>

                <li>
                    <a class="<?php hyiplab_menu_active('user.home'); ?>" href="<?php echo hyiplab_route_link('user.home') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/dashboard.png')); ?>" alt="icon"> <?php esc_html_e('Dashboard', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <li>
                    <a class="<?php hyiplab_menu_active(['user.invest.index', 'user.invest.log', 'user.invest.detail']); ?>" href="<?php echo hyiplab_route_link('user.invest.index') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/investment.png')); ?>" alt="icon"> <?php esc_html_e('Investments', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <?php if(get_option('hyiplab_schedule_invest')){ ?>
                <li>
                    <a class="<?php hyiplab_menu_active(['user.schedule.index']); ?>" href="<?php echo hyiplab_route_link('user.schedule.index') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/schedule.png')); ?>" alt="icon"> <?php esc_html_e('Schedule Investment', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <?php } ?>
                <?php if(get_option('hyiplab_staking')){ ?>
                <li>
                    <a class="<?php hyiplab_menu_active(['user.staking.index']); ?>" href="<?php echo hyiplab_route_link('user.staking.index') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/staking.png')); ?>" alt="icon"> <?php esc_html_e('Staking', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <?php } ?>
                <?php if (get_option('hyiplab_pool')) { ?>
                <li>
                    <a class="<?php hyiplab_menu_active(['user.pool.index', 'user.pool.invest']); ?>" href="<?php echo hyiplab_route_link('user.pool.index') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/pool_invest.png')); ?>" alt="icon"> <?php esc_html_e('Pool', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <?php } ?>
                <?php if(get_option('hyiplab_kyc')){ ?>
                <li>
                    <a class="<?php hyiplab_menu_active(['user.kyc.data', 'user.kyc.form']); ?>" href="<?php echo hyiplab_route_link('user.kyc.data') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/wallet.png')); ?>" alt="icon"> <?php esc_html_e('KYC', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <?php } ?>
                <li>
                    <a class="<?php hyiplab_menu_active(['user.deposit.index', 'user.deposit.history', 'user.deposit.confirm', 'user.deposit.manual']); ?>" href="<?php echo hyiplab_route_link('user.deposit.index') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/wallet.png')); ?>" alt="icon"> <?php esc_html_e('Deposit', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <li>
                    <a class="<?php hyiplab_menu_active(['user.withdraw.index', 'user.withdraw.history', 'user.withdraw.preview']); ?>" href="<?php echo hyiplab_route_link('user.withdraw.index') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/withdraw.png')); ?>" alt="icon"> <?php esc_html_e('Withdraw', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <?php if (get_option('hyiplab_balance_transfer')) { ?>
                    <li>
                        <a href="<?php echo hyiplab_route_link('user.transfer.balance'); ?>" class="<?php hyiplab_menu_active('user.transfer.balance'); ?>">
                            <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/balance-transfer.png')); ?>" alt="icon"> <?php esc_html_e('Transfer Balance', HYIPLAB_PLUGIN_NAME); ?>
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <a class="<?php hyiplab_menu_active('user.transaction.index'); ?>" href="<?php echo hyiplab_route_link('user.transaction.index') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/transaction.png')); ?>" alt="icon"> <?php esc_html_e('Transactions', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <?php if (get_option('hyiplab_user_ranking')) { ?>
                    <li>
                        <a href="<?php echo hyiplab_route_link('user.invest.ranking'); ?>" class="<?php hyiplab_menu_active('user.invest.ranking'); ?>">
                            <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/ranking.png')); ?>" alt="icon"> <?php esc_html_e('Ranking', HYIPLAB_PLUGIN_NAME); ?>
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <a class="<?php hyiplab_menu_active('user.referral.index'); ?>" href="<?php echo hyiplab_route_link('user.referral.index') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/referral.png')); ?>" alt="icon"> <?php esc_html_e('Referrals', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <li>
                    <a class="<?php hyiplab_menu_active(['user.ticket.index', 'user.ticket.create', 'user.ticket.view']); ?>" href="<?php echo hyiplab_route_link('user.ticket.index') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/ticket.png')); ?>" alt="icon"> <?php esc_html_e('Support Ticket', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <?php if (get_option('hyiplab_promotional_tool')) { ?>
                <li>
                    <a class="<?php hyiplab_menu_active(['user.promotional.banner']); ?>" href="<?php echo hyiplab_route_link('user.promotional.banner') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/promotion.png')); ?>" alt="icon"> <?php esc_html_e('Promotional Banner', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <?php } ?>
                <li>
                    <a class="<?php hyiplab_menu_active('user.profile.setting'); ?>" href="<?php echo hyiplab_route_link('user.profile.setting') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/profile.png')); ?>" alt="icon"> <?php esc_html_e('Profile Settings', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <li>
                    <a class="<?php hyiplab_menu_active('user.change.password'); ?>" href="<?php echo hyiplab_route_link('user.change.password') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/password.png')); ?>" alt="icon"> <?php esc_html_e('Change Password', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo hyiplab_route_link('user.logout') ?>">
                        <img src="<?php echo esc_url(hyiplab_asset('public/images/icon/logout.png')); ?>" alt="icon"> <?php esc_html_e('Logout', HYIPLAB_PLUGIN_NAME); ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="dashboard-wrapper">

            <div class="dashboard-nav d-flex flex-wrap align-items-center justify-content-between">
                <div class="nav-left d-flex gap-4 align-items-center">
                    <div class="dash-sidebar-toggler d-xl-none" id="dash-sidebar-toggler">
                        <i class="fas fa-bars"></i>
                    </div>
                </div>
                <div class="nav-right d-flex flex-wrap align-items-center gap-3">

                    <ul class="nav-header-link d-flex flex-wrap gap-2">
                        <li>
                            <a class="link" href="javascript:void(0)"><?php echo getViserInitials(hyiplab_auth()->user->display_name) ?></a>
                            <div class="dropdown-wrapper">
                                <div class="dropdown-header">
                                    <h6 class="name text--base"><?php echo hyiplab_auth()->user->display_name ?></h6>
                                    <p class="fs--14px"><?php echo hyiplab_auth()->user->user_login ?></p>
                                </div>
                                <ul class="links">
                                    <li>
                                        <a href="<?php echo hyiplab_route_link('user.profile.setting') ?>">
                                            <i class="las la-user"></i> <?php esc_html_e('Profile Settings', HYIPLAB_PLUGIN_NAME); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo hyiplab_route_link('user.change.password'); ?>">
                                            <i class="las la-key"></i> <?php esc_html_e('Change Password', HYIPLAB_PLUGIN_NAME); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo hyiplab_route_link('user.logout'); ?>">
                                            <i class="las la-sign-out-alt"></i> <?php esc_html_e('Logout', HYIPLAB_PLUGIN_NAME); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="dashboard-container">
                <div class="dashboard-inner">
                    {{yield}}
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            "use strict"
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>

    <?php hyiplab_include('partials/notify'); ?>

    <?php wp_footer(); ?>
</body>

</html>