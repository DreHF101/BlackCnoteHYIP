<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="<?php menu_page_url(HYIPLAB_PLUGIN_NAME); ?>" class="sidebar__main-logo">
                <img src="<?php echo hyiplab_asset('global/images/logo_light.png'); ?>" alt="logo">
            </a>
        </div>

        <div class="sidebar__menu-wrapper position-relative" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">

                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.hyiplab', dashboard: true) ?>">
                    <a href="<?php menu_page_url(HYIPLAB_PLUGIN_NAME) ?>" class="nav-link">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title"><?php esc_html_e('Dashboard', HYIPLAB_PLUGIN_NAME) ?></span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active(['admin.time.index', 'admin.plan.index'], 3); ?>">
                        <i class="menu-icon las la-clipboard-check"></i>
                        <span class="menu-title"><?php esc_html_e('Plan Manage', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                    <div class="sidebar-submenu <?php hyiplab_menu_active(['admin.time.index', 'admin.plan.index'], 2); ?>">
                        <ul>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.time.index'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.time.index'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Time Manage', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.plan.index'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.plan.index'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Plan Manage', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active(['admin.staking.time.index', 'admin.staking.time.status'], 3); ?>">
                        <i class="menu-icon las la-chart-line"></i>
                        <span class="menu-title"><?php esc_html_e('Manage Staking', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                    <div class="sidebar-submenu <?php hyiplab_menu_active(['admin.staking.time.index', 'admin.staking.time.status'], 2); ?>">
                        <ul>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.staking.time.index'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.staking.time.index'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Plan', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.staking.time.status'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.staking.time.status'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Staking Invest', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active(['admin.pool.index', 'admin.pool.invest'], 3); ?>">
                        <i class="menu-icon las la-cubes"></i>
                        <span class="menu-title"><?php esc_html_e('Manage Pool', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                    <div class="sidebar-submenu <?php hyiplab_menu_active(['admin.pool.index', 'admin.pool.invest'], 2); ?>">
                        <ul>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.pool.index'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.pool.index'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Plan', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.pool.invest'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.pool.invest'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Pool Invest', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.promotion.index'); ?>">
                    <a href="<?php echo hyiplab_route_link('admin.promotion.index'); ?>" class="nav-link">
                        <i class="menu-icon las la-ad"></i>
                        <span class="menu-title"><?php esc_html_e('Promotion Tool', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>
                <?php if (get_option('hyiplab_user_ranking')) { ?>

                    <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.ranking.index'); ?>">
                        <a href="<?php echo hyiplab_route_link('admin.ranking.index'); ?>" class="nav-link">
                            <i class="menu-icon las la-medal"></i>
                            <span class="menu-title"><?php esc_html_e('User Ranking', HYIPLAB_PLUGIN_NAME); ?></span>
                        </a>
                    </li>

                <?php } ?>

                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.referrals.index'); ?>">
                    <a href="<?php echo hyiplab_route_link('admin.referrals.index'); ?>" class="nav-link">
                        <i class="menu-icon las la-tree"></i>
                        <span class="menu-title"><?php esc_html_e('Manage Referral', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>

                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.invest.report.dashboard'); ?>">
                    <a href="<?php echo hyiplab_route_link('admin.invest.report.dashboard'); ?>" class="nav-link">
                        <i class="menu-icon las la-signal"></i>
                        <span class="menu-title"><?php esc_html_e('Investment Report', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">

                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active(['admin.users.all', 'admin.users.active', 'admin.users.banned', 'admin.users.pending.kyc', 'admin.users.unverified.kyc', 'admin.users.detail', 'admin.users.kyc.data'], 3); ?>">
                        <i class="menu-icon las la-users"></i>
                        <span class="menu-title"><?php esc_html_e('Manage Users', HYIPLAB_PLUGIN_NAME); ?></span>
                        <?php if (0 < pending_kyc_count()) { ?>
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        <?php } ?>
                    </a>

                    <div class="sidebar-submenu <?php hyiplab_menu_active(['admin.users.all', 'admin.users.active', 'admin.users.banned', 'admin.users.pending.kyc', 'admin.users.unverified.kyc', 'admin.users.detail', 'admin.users.kyc.data'], 2); ?>">
                        <ul>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active(['admin.users.all', 'admin.users.detail', 'admin.users.kyc.data']); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.users.all'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('All Users', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active(['admin.users.active']); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.users.active'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Active Users', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.users.banned'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.users.banned'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Banned Users', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.users.pending.kyc'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.users.pending.kyc'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('KYC Pending', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <?php if (pending_kyc_count()) { ?>
                                        <span class="menu-badge pill bg--danger ms-auto"><?php echo esc_html(pending_kyc_count()); ?></span>
                                    <?php } ?>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.users.unverified.kyc'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.users.unverified.kyc'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('KYC Unverified', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <?php if (unverified_kyc_count()) { ?>
                                        <span class="menu-badge pill bg--danger ms-auto"><?php echo esc_html(unverified_kyc_count()); ?></span>
                                    <?php } ?>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active([
                                                            'admin.gateway.automatic',
                                                            'admin.gateway.automatic.edit',
                                                            'admin.gateway.manual',
                                                            'admin.gateway.manual.create',
                                                            'admin.gateway.manual.edit'
                                                        ], 3) ?>">
                        <i class="menu-icon las la-credit-card"></i>
                        <span class="menu-title"><?php esc_html_e('Payment Gateways', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                    <div class="sidebar-submenu <?php hyiplab_menu_active([
                                                    'admin.gateway.automatic',
                                                    'admin.gateway.automatic.edit',
                                                    'admin.gateway.manual',
                                                    'admin.gateway.manual.create',
                                                    'admin.gateway.manual.edit'
                                                ], 2) ?>">
                        <ul>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active(['admin.gateway.automatic', 'admin.gateway.automatic.edit']) ?>">
                                <a href="<?php echo hyiplab_route_link('admin.gateway.automatic') ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Automatic Gateways', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active(['admin.gateway.manual', 'admin.gateway.manual.create', 'admin.gateway.manual.edit']) ?>">
                                <a href="<?php echo hyiplab_route_link('admin.gateway.manual') ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Manual Gateways', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>


                <li class="sidebar-menu-item sidebar-dropdown">

                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active(['admin.deposit.pending', 'admin.deposit.approved', 'admin.deposit.successful', 'admin.deposit.rejected', 'admin.deposit.initiated', 'admin.deposit.list', 'admin.deposit.details'], 3); ?>">
                        <i class="menu-icon las la-file-invoice-dollar"></i>
                        <span class="menu-title"><?php esc_html_e('Deposits', HYIPLAB_PLUGIN_NAME); ?></span>
                        <?php if (0 < pending_deposit_count()) { ?>
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        <?php } ?>
                    </a>

                    <div class="sidebar-submenu <?php hyiplab_menu_active(['admin.deposit.pending', 'admin.deposit.approved', 'admin.deposit.successful', 'admin.deposit.rejected', 'admin.deposit.initiated', 'admin.deposit.list', 'admin.deposit.details'], 2); ?>">
                        <ul>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.deposit.pending'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.deposit.pending'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Pending Deposits', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <?php if (pending_deposit_count()) { ?>
                                        <span class="menu-badge pill bg--danger ms-auto"><?php echo esc_html(pending_deposit_count()); ?></span>
                                    <?php } ?>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.deposit.approved'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.deposit.approved'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Approved Deposits', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.deposit.successful'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.deposit.successful'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Successful Deposits', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.deposit.rejected'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.deposit.rejected'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Rejected Deposits', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.deposit.initiated'); ?>">

                                <a href="<?php echo hyiplab_route_link('admin.deposit.initiated'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Initiated Deposits', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.deposit.list'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.deposit.list'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('All Deposits', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active(['admin.withdraw.method.index', 'admin.withdraw.method.create', 'admin.withdraw.method.edit', 'admin.withdraw.pending', 'admin.withdraw.approved', 'admin.withdraw.rejected', 'admin.withdraw.log', 'admin.withdraw.detail'], 3); ?>">
                        <i class="menu-icon la la-bank"></i>
                        <span class="menu-title"><?php esc_html_e('Withdrawals', HYIPLAB_PLUGIN_NAME); ?></span>
                        <?php if (0 < pending_withdraw_count()) { ?>
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        <?php } ?>
                    </a>
                    <div class="sidebar-submenu <?php hyiplab_menu_active(['admin.withdraw.method.index', 'admin.withdraw.method.create', 'admin.withdraw.method.edit', 'admin.withdraw.pending', 'admin.withdraw.approved', 'admin.withdraw.rejected', 'admin.withdraw.log', 'admin.withdraw.detail'], 2); ?>">
                        <ul>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active(['admin.withdraw.method.index', 'admin.withdraw.method.create', 'admin.withdraw.method.edit']); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.withdraw.method.index'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Withdrawal Methods', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.withdraw.pending'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.withdraw.pending'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Pending Withdrawals', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <?php if (pending_withdraw_count()) { ?>
                                        <span class="menu-badge pill bg--danger ms-auto"><?php echo esc_html(pending_withdraw_count()); ?></span>
                                    <?php } ?>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.withdraw.approved'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.withdraw.approved'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Approved Withdrawals', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.withdraw.rejected'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.withdraw.rejected'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Rejected Withdrawals', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.withdraw.log'); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.withdraw.log'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('All Withdrawals', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active(['admin.ticket.pending', 'admin.ticket.closed', 'admin.ticket.answered', 'admin.ticket.index', 'admin.ticket.view'], 3); ?>">
                        <i class="menu-icon la la-ticket"></i>
                        <span class="menu-title"><?php esc_html_e('Support Ticket', HYIPLAB_PLUGIN_NAME); ?></span>
                        <?php if (0 < pending_ticket_count()) { ?>
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        <?php } ?>
                    </a>
                    <div class="sidebar-submenu <?php hyiplab_menu_active(['admin.ticket.pending', 'admin.ticket.closed', 'admin.ticket.answered', 'admin.ticket.index', 'admin.ticket.view'], 2); ?> ">
                        <ul>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.ticket.pending'); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.ticket.pending'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Pending Ticket', HYIPLAB_PLUGIN_NAME); ?></span>
                                    <?php if (pending_ticket_count()) { ?>
                                        <span class="menu-badge pill bg--danger ms-auto"><?php echo esc_html(pending_ticket_count()); ?></span>
                                    <?php } ?>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.ticket.closed'); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.ticket.closed'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Closed Ticket', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.ticket.answered'); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.ticket.answered'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Answered Ticket', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.ticket.index'); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.ticket.index'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('All Ticket', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active(['admin.report.transaction', 'admin.report.invest.history'], 3); ?>">
                        <i class="menu-icon la la-list"></i>
                        <span class="menu-title"><?php esc_html_e('Report', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                    <div class="sidebar-submenu <?php hyiplab_menu_active(['admin.report.transaction', 'admin.report.invest.history'], 2); ?> ">
                        <ul>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.report.transaction'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.report.transaction'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Transaction Log', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.report.invest.history'); ?>">
                                <a href="<?php echo hyiplab_route_link('admin.report.invest.history'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Invest History', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header"><?php esc_html_e('Settings', HYIPLAB_PLUGIN_NAME); ?></li>

                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.setting.index'); ?>">
                    <a href="<?php echo hyiplab_route_link('admin.setting.index'); ?>" class="nav-link">
                        <i class="menu-icon las la-life-ring"></i>
                        <span class="menu-title"><?php esc_html_e('General Setting', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>

                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.kyc.index'); ?>">
                    <a href="<?php echo hyiplab_route_link('admin.kyc.index'); ?>" class="nav-link">
                        <i class="menu-icon las la-user-check"></i>
                        <span class="menu-title"><?php esc_html_e('KYC Setting', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>
                
                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.holiday.index'); ?>">
                    <a href="<?php echo hyiplab_route_link('admin.holiday.index'); ?>" class="nav-link">
                        <i class="menu-icon la la-toggle-off"></i>
                        <span class="menu-title"><?php esc_html_e('Holiday Setting', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>

                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.setting.system.configuration'); ?>">
                    <a href="<?php echo hyiplab_route_link('admin.setting.system.configuration'); ?>" class="nav-link">
                        <i class="menu-icon las la-cog"></i>
                        <span class="menu-title"><?php esc_html_e('System Configuration', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>

                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.extension.index'); ?>">
                    <a href="<?php echo hyiplab_route_link('admin.extension.index'); ?>" class="nav-link">
                        <i class="menu-icon las la-cogs"></i>
                        <span class="menu-title"><?php esc_html_e('Extensions', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>

                <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.setting.logo.icon') ?>">
                    <a href="<?php echo hyiplab_route_link('admin.setting.logo.icon') ?>" class="nav-link">
                        <i class="menu-icon las la-images"></i>
                        <span class="menu-title"><?php esc_html_e('Logo & Favicon', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="<?php hyiplab_menu_active(['admin.setting.notification.global', 'admin.setting.notification.email', 'admin.setting.notification.sms', 'admin.setting.notification.templates', 'admin.setting.notification.template.edit'], 3); ?>">
                        <i class="menu-icon las la-bell"></i>
                        <span class="menu-title"><?php esc_html_e('Notification Setting', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                    <div class="sidebar-submenu <?php hyiplab_menu_active(['admin.setting.notification.global', 'admin.setting.notification.email', 'admin.setting.notification.sms', 'admin.setting.notification.templates', 'admin.setting.notification.template.edit', 'admin.setting.notification.template.push'], 2); ?> ">
                        <ul>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.setting.notification.global'); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.setting.notification.global'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Global Template', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.setting.notification.email'); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.setting.notification.email'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Email Setting', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active('admin.setting.notification.sms'); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.setting.notification.sms'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('SMS Setting', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active(['admin.setting.notification.template.push']); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.setting.notification.template.push'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Push Notification Setting', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item <?php hyiplab_menu_active(['admin.setting.notification.templates', 'admin.setting.notification.template.edit']); ?> ">
                                <a href="<?php echo hyiplab_route_link('admin.setting.notification.templates'); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php esc_html_e('Notification Templates', HYIPLAB_PLUGIN_NAME); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item  <?php hyiplab_menu_active('admin.request.report') ?>">
                    <a href="<?php echo hyiplab_route_link('admin.request.report') ?>" class="nav-link" data-default-url="<?php hyiplab_route_link('admin.request.report') ?>">
                        <i class="menu-icon las la-bug"></i>
                        <span class="menu-title"><?php esc_html_e('Report & Request', HYIPLAB_PLUGIN_NAME); ?></span>
                    </a>
                </li>

            </ul>
            <div class="text-center mb-5 text-uppercase">
                <span class="text--primary"><?php esc_html_e(hyiplab_system_details()['name'], HYIPLAB_PLUGIN_NAME) ?></span>
                <span class="text--success"><?php esc_html_e('V', HYIPLAB_ROOT) ?><?php esc_html_e(hyiplab_system_details()['version'], HYIPLAB_PLUGIN_NAME) ?></span>
            </div>
        </div>
    </div>
    <a href="<?php echo admin_url(); ?>" class="back-dashboard__button btn btn--info"> <i class="fab fa-wordpress"></i> <?php esc_html_e('Back To WordPress', HYIPLAB_PLUGIN_NAME); ?></a>
</div>
<!-- sidebar end -->


<script>
    jQuery(document).ready(function($) {
        "use strict";
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    });
</script>