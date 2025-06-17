<?php

use Hyiplab\BackOffice\Router\Router;
use Hyiplab\Controllers\Admin\AdminController;
use Hyiplab\Controllers\Admin\AutomaticGatewayController;
use Hyiplab\Controllers\Admin\DepositController;
use Hyiplab\Controllers\Admin\ExtensionController;
use Hyiplab\Controllers\Admin\GeneralSettingController;
use Hyiplab\Controllers\Admin\InvestReportController;
use Hyiplab\Controllers\Admin\ManualGatewayController;
use Hyiplab\Controllers\Admin\NotificationController;
use Hyiplab\Controllers\Admin\PlanController;
use Hyiplab\Controllers\Admin\RankingController;
use Hyiplab\Controllers\Admin\ReferralController;
use Hyiplab\Controllers\Admin\ReportController;
use Hyiplab\Controllers\Admin\SupportTicketController;
use Hyiplab\Controllers\Admin\TimeController;
use Hyiplab\Controllers\Admin\UserController;
use Hyiplab\Controllers\Admin\WithdrawalController;
use Hyiplab\Controllers\Admin\WithdrawMethodController;
use Hyiplab\Controllers\Admin\StakingPoolController;
use Hyiplab\Controllers\Admin\PromotionalToolController;
use Hyiplab\Controllers\Admin\KycController;
use Hyiplab\Controllers\Admin\HolidayController;

$router = new Router;

$router->router([
    'admin.hyiplab' => [
        'method'       => 'get',
        'query_string' => HYIPLAB_PLUGIN_NAME,
        'middleware'   => ['checkPlugin'],
        'action'       => [AdminController::class, 'dashboard'],
    ],
    'admin.download.attachment' => [
        'method'       => 'get',
        'query_string' => 'download_attachment',
        'middleware'   => ['checkPlugin'],
        'action'       => [AdminController::class, 'download'],
    ],

    //payment gateway
    'admin.gateway.automatic' => [
        'method'       => 'get',
        'query_string' => 'automatic_gateway_index',
        'middleware'   => ['checkPlugin'],
        'action'       => [AutomaticGatewayController::class, 'index'],
    ],
    'admin.gateway.automatic.status' => [
        'method'       => 'post',
        'query_string' => 'automatic_gateway_automatic_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [AutomaticGatewayController::class, 'status'],
    ],
    'admin.gateway.automatic.edit' => [
        'method'       => 'get',
        'query_string' => 'automatic_gateway_automatic_edit',
        'middleware'   => ['checkPlugin'],
        'action'       => [AutomaticGatewayController::class, 'edit'],
    ],
    'admin.gateway.automatic.update' => [
        'method'       => 'post',
        'query_string' => 'automatic_gateway_automatic_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [AutomaticGatewayController::class, 'update'],
    ],
    'admin.gateway.automatic.currency.remove' => [
        'method'       => 'post',
        'query_string' => 'automatic_gateway_currency_remove',
        'middleware'   => ['checkPlugin'],
        'action'       => [AutomaticGatewayController::class, 'currencyRemove'],
    ],
    'admin.gateway.manual' => [
        'method'       => 'get',
        'query_string' => 'manual_gateway_index',
        'middleware'   => ['checkPlugin'],
        'action'       => [ManualGatewayController::class, 'index'],
    ],
    'admin.gateway.manual.create' => [
        'method'       => 'get',
        'query_string' => 'manual_gateway_create',
        'middleware'   => ['checkPlugin'],
        'action'       => [ManualGatewayController::class, 'create'],
    ],
    'admin.gateway.manual.store' => [
        'method'       => 'post',
        'query_string' => 'manual_gateway_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [ManualGatewayController::class, 'store'],
    ],
    'admin.gateway.manual.edit' => [
        'method'       => 'get',
        'query_string' => 'manual_gateway_edit',
        'middleware'   => ['checkPlugin'],
        'action'       => [ManualGatewayController::class, 'edit'],
    ],
    'admin.gateway.manual.update' => [
        'method'       => 'post',
        'query_string' => 'manual_gateway_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [ManualGatewayController::class, 'update'],
    ],
    'admin.gateway.manual.status' => [
        'method'       => 'post',
        'query_string' => 'manual_gateway_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [ManualGatewayController::class, 'status'],
    ],

    //deposit
    'admin.deposit.pending' => [
        'method'       => 'get',
        'query_string' => 'deposit_pending',
        'middleware'   => ['checkPlugin'],
        'action'       => [DepositController::class, 'pending'],
    ],
    'admin.deposit.approved' => [
        'method'       => 'get',
        'query_string' => 'deposit_approved',
        'middleware'   => ['checkPlugin'],
        'action'       => [DepositController::class, 'approved'],
    ],
    'admin.deposit.successful' => [
        'method'       => 'get',
        'query_string' => 'deposit_successful',
        'middleware'   => ['checkPlugin'],
        'action'       => [DepositController::class, 'successful'],
    ],
    'admin.deposit.rejected' => [
        'method'       => 'get',
        'query_string' => 'deposit_rejected',
        'middleware'   => ['checkPlugin'],
        'action'       => [DepositController::class, 'rejected'],
    ],
    'admin.deposit.initiated' => [
        'method'       => 'get',
        'query_string' => 'deposit_initiated',
        'middleware'   => ['checkPlugin'],
        'action'       => [DepositController::class, 'initiated'],
    ],
    'admin.deposit.list' => [
        'method'       => 'get',
        'query_string' => 'deposit_list',
        'middleware'   => ['checkPlugin'],
        'action'       => [DepositController::class, 'deposit'],
    ],
    'admin.deposit.details' => [
        'method'       => 'get',
        'query_string' => 'deposit_details',
        'middleware'   => ['checkPlugin'],
        'action'       => [DepositController::class, 'detail'],
    ],
    'admin.deposit.approve' => [
        'method'       => 'post',
        'query_string' => 'deposit_approve',
        'middleware'   => ['checkPlugin'],
        'action'       => [DepositController::class, 'approve'],
    ],
    'admin.deposit.reject' => [
        'method'       => 'post',
        'query_string' => 'deposit_reject',
        'middleware'   => ['checkPlugin'],
        'action'       => [DepositController::class, 'reject'],
    ],

    //withdraw
    'admin.withdraw.method.index' => [
        'method'       => 'get',
        'query_string' => 'withdraw_method_index',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawMethodController::class, 'methods'],
    ],
    'admin.withdraw.method.create' => [
        'method'       => 'get',
        'query_string' => 'withdraw_method_create',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawMethodController::class, 'create'],
    ],
    'admin.withdraw.method.store' => [
        'method'       => 'post',
        'query_string' => 'withdraw_method_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawMethodController::class, 'store'],
    ],
    'admin.withdraw.method.edit' => [
        'method'       => 'get',
        'query_string' => 'withdraw_method_edit',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawMethodController::class, 'edit'],
    ],
    'admin.withdraw.method.update' => [
        'method'       => 'post',
        'query_string' => 'withdraw_method_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawMethodController::class, 'update'],
    ],
    'admin.withdraw.method.status' => [
        'method'       => 'post',
        'query_string' => 'withdraw_method_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawMethodController::class, 'status'],
    ],
    'admin.withdraw.pending' => [
        'method'       => 'get',
        'query_string' => 'withdraw_pending',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawalController::class, 'pending'],
    ],
    'admin.withdraw.approved' => [
        'method'       => 'get',
        'query_string' => 'withdraw_approved',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawalController::class, 'approved'],
    ],
    'admin.withdraw.rejected' => [
        'method'       => 'get',
        'query_string' => 'withdraw_rejected',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawalController::class, 'rejected'],
    ],
    'admin.withdraw.log' => [
        'method'       => 'get',
        'query_string' => 'withdraw_log',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawalController::class, 'log'],
    ],
    'admin.withdraw.detail' => [
        'method'       => 'get',
        'query_string' => 'withdraw_detail',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawalController::class, 'detail'],
    ],
    'admin.withdraw.reject' => [
        'method'       => 'post',
        'query_string' => 'withdraw_reject',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawalController::class, 'reject'],
    ],
    'admin.withdraw.approve' => [
        'method'       => 'post',
        'query_string' => 'withdraw_approve',
        'middleware'   => ['checkPlugin'],
        'action'       => [WithdrawalController::class, 'approve'],
    ],

    //Ticket
    'admin.ticket.index' => [
        'method'       => 'get',
        'query_string' => 'ticket_index',
        'middleware'   => ['checkPlugin'],
        'action'       => [SupportTicketController::class, 'index'],
    ],
    'admin.ticket.pending' => [
        'method'       => 'get',
        'query_string' => 'ticket_pending',
        'middleware'   => ['checkPlugin'],
        'action'       => [SupportTicketController::class, 'pending'],
    ],
    'admin.ticket.closed' => [
        'method'       => 'get',
        'query_string' => 'ticket_closed',
        'middleware'   => ['checkPlugin'],
        'action'       => [SupportTicketController::class, 'closed'],
    ],
    'admin.ticket.answered' => [
        'method'       => 'get',
        'query_string' => 'ticket_answered',
        'middleware'   => ['checkPlugin'],
        'action'       => [SupportTicketController::class, 'answered'],
    ],
    'admin.ticket.view' => [
        'method'       => 'get',
        'query_string' => 'ticket_view',
        'middleware'   => ['checkPlugin'],
        'action'       => [SupportTicketController::class, 'viewTicket'],
    ],
    'admin.ticket.reply' => [
        'method'       => 'post',
        'query_string' => 'ticket_reply',
        'middleware'   => ['checkPlugin'],
        'action'       => [SupportTicketController::class, 'reply'],
    ],
    'admin.ticket.delete' => [
        'method'       => 'post',
        'query_string' => 'ticket_delete',
        'middleware'   => ['checkPlugin'],
        'action'       => [SupportTicketController::class, 'delete'],
    ],
    'admin.ticket.close' => [
        'method'       => 'post',
        'query_string' => 'ticket_close',
        'middleware'   => ['checkPlugin'],
        'action'       => [SupportTicketController::class, 'close'],
    ],
    'admin.ticket.download' => [
        'method'       => 'get',
        'query_string' => 'ticket_download',
        'middleware'   => ['checkPlugin'],
        'action'       => [SupportTicketController::class, 'download'],
    ],

    //report
    'admin.report.transaction' => [
        'method'       => 'get',
        'query_string' => 'report_transaction',
        'middleware'   => ['checkPlugin'],
        'action'       => [ReportController::class, 'transaction'],
    ],
    'admin.report.invest.history' => [
        'method'       => 'get',
        'query_string' => 'report_invest_history',
        'middleware'   => ['checkPlugin'],
        'action'       => [ReportController::class, 'investHistory'],
    ],

    //setting
    'admin.setting.index' => [
        'method'       => 'get',
        'query_string' => 'setting_index',
        'middleware'   => ['checkPlugin'],
        'action'       => [GeneralSettingController::class, 'index'],
    ],
    'admin.setting.store' => [
        'method'       => 'post',
        'query_string' => 'setting_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [GeneralSettingController::class, 'store'],
    ],
    'admin.setting.system.configuration' => [
        'method'       => 'get',
        'query_string' => 'setting_system_configuration',
        'middleware'   => ['checkPlugin'],
        'action'       => [GeneralSettingController::class, 'systemConfiguration'],
    ],
    'admin.setting.system.configuration.store' => [
        'method'       => 'post',
        'query_string' => 'setting_system_configuration_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [GeneralSettingController::class, 'systemConfigurationStore'],
    ],
    'admin.setting.system.configuration.store' => [
        'method'       => 'post',
        'query_string' => 'setting_system_configuration_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [GeneralSettingController::class, 'systemConfigurationStore'],
    ],

    //logo and favicon
    'admin.setting.logo.icon' => [
        'method'       => 'get',
        'query_string' => 'setting_logo_icon',
        'middleware'   => ['checkPlugin'],
        'action'       => [GeneralSettingController::class, 'logoIcon'],
    ],
    'admin.setting.logo.icon.submit' => [
        'method'       => 'post',
        'query_string' => 'setting_logo_icon_submit',
        'middleware'   => ['checkPlugin'],
        'action'       => [GeneralSettingController::class, 'logoIconSubmit'],
    ],

    //notification setting
    'admin.setting.notification.global' => [
        'method'       => 'get',
        'query_string' => 'notification_global',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'global'],
    ],
    'admin.setting.notification.global.update' => [
        'method'       => 'post',
        'query_string' => 'notification_global_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'globalUpdate'],
    ],
    'admin.setting.notification.email' => [
        'method'       => 'get',
        'query_string' => 'email_setting',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'emailSetting'],
    ],
    'admin.setting.notification.email.update' => [
        'method'       => 'post',
        'query_string' => 'email_setting_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'emailSettingUpdate'],
    ],
    'admin.setting.notification.sms' => [
        'method'       => 'get',
        'query_string' => 'sms_setting',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'smsSetting'],
    ],
    'admin.setting.notification.sms.update' => [
        'method'       => 'post',
        'query_string' => 'sms_setting_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'smsSettingUpdate'],
    ],
    'admin.setting.notification.templates' => [
        'method'       => 'get',
        'query_string' => 'template_setting',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'templates'],
    ],
    'admin.setting.notification.template.edit' => [
        'method'       => 'get',
        'query_string' => 'template_edit',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'templateEdit'],
    ],
    'admin.setting.notification.template.update' => [
        'method'       => 'post',
        'query_string' => 'template_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'templateUpdate'],
    ],
    'admin.setting.notification.template.push' => [
        'method'       => 'get',
        'query_string' => 'template_push_notification',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'templatePushNotification'],
    ],
    'admin.setting.notification.template.push.edit' => [
        'method'       => 'get',
        'query_string' => 'template_push_notification_edit',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'templatePushNotificationEdit'],
    ],
    'admin.setting.notification.template.push.update' => [
        'method'       => 'post',
        'query_string' => 'template_push_notification_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'templatePushNotificationUpdate'],
    ],
    'admin.setting.notification.email.test' => [
        'method'       => 'post',
        'query_string' => 'email_test',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'emailTest'],
    ],
    'admin.setting.notification.sms.test' => [
        'method'       => 'post',
        'query_string' => 'sms_test',
        'middleware'   => ['checkPlugin'],
        'action'       => [NotificationController::class, 'smsTest'],
    ],

    //extension
    'admin.extension.index' => [
        'method'       => 'get',
        'query_string' => 'extension_index',
        'middleware'   => ['checkPlugin'],
        'action'       => [ExtensionController::class, 'index'],
    ],
    'admin.extension.update' => [
        'method'       => 'post',
        'query_string' => 'extension_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [ExtensionController::class, 'update'],
    ],
    'admin.extension.status' => [
        'method'       => 'post',
        'query_string' => 'extension_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [ExtensionController::class, 'status'],
    ],

    //bug report and request
    'admin.request.report' => [
        'method'       => 'get',
        'query_string' => 'report_and_request',
        'middleware'   => ['checkPlugin'],
        'action'       => [AdminController::class, 'requestReport'],
    ],
    'admin.request.report.submit' => [
        'method'       => 'post',
        'query_string' => 'report_and_request_submit',
        'middleware'   => ['checkPlugin'],
        'action'       => [AdminController::class, 'requestReportSubmit'],
    ],

    // Manage Users
    'admin.users.all' => [
        'method'       => 'get',
        'query_string' => 'users_all',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'allUsers'],
    ],
    'admin.users.active' => [
        'method'       => 'get',
        'query_string' => 'users_active',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'activeUsers'],
    ],
    'admin.users.banned' => [
        'method'       => 'get',
        'query_string' => 'users_banned',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'bannedUsers'],
    ],
    'admin.users.pending.kyc' => [
        'method'       => 'get',
        'query_string' => 'users_kyc_pending',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'kycPendingUsers'],
    ],
    'admin.users.unverified.kyc' => [
        'method'       => 'get',
        'query_string' => 'users_kyc_unverified',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'kycUnverifiedUsers'],
    ],
    'admin.users.detail' => [
        'method'       => 'get',
        'query_string' => 'users_detail',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'userDetail'],
    ],
    'admin.users.update' => [
        'method'       => 'post',
        'query_string' => 'users_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'userUpdate'],
    ],
    'admin.users.balance.add.sub' => [
        'method'       => 'post',
        'query_string' => 'users_add_balance',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'userAddSubBalance'],
    ],
    'admin.users.ban' => [
        'method'       => 'post',
        'query_string' => 'users_ban',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'userBan'],
    ],
    'admin.users.kyc.data' => [
        'method'       => 'get',
        'query_string' => 'kyc_data',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'KycData'],
    ],
    'admin.users.kyc.approve' => [
        'method'       => 'post',
        'query_string' => 'kyc_approve',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'KycApprove'],
    ],
    'admin.users.kyc.reject' => [
        'method'       => 'post',
        'query_string' => 'kyc_reject',
        'middleware'   => ['checkPlugin'],
        'action'       => [UserController::class, 'KycReject'],
    ],
    // Plan Manage
    'admin.time.index' => [
        'method'       => 'get',
        'query_string' => 'times',
        'middleware'   => ['checkPlugin'],
        'action'       => [TimeController::class, 'index'],
    ],
    'admin.time.store' => [
        'method'       => 'post',
        'query_string' => 'time_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [TimeController::class, 'store'],
    ],
    'admin.time.update' => [
        'method'       => 'post',
        'query_string' => 'time_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [TimeController::class, 'update'],
    ],
    'admin.time.status' => [
        'method'       => 'post',
        'query_string' => 'time_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [TimeController::class, 'status'],
    ],
    'admin.plan.index' => [
        'method'       => 'get',
        'query_string' => 'plans',
        'middleware'   => ['checkPlugin'],
        'action'       => [PlanController::class, 'index'],
    ],
    'admin.plan.store' => [
        'method'       => 'post',
        'query_string' => 'plan_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [PlanController::class, 'store'],
    ],
    'admin.plan.update' => [
        'method'       => 'post',
        'query_string' => 'plan_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [PlanController::class, 'update'],
    ],
    'admin.plan.status' => [
        'method'       => 'post',
        'query_string' => 'plan_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [PlanController::class, 'status'],
    ],

    // manage staking
    'admin.staking.time.index' => [
        'method'       => 'get',
        'query_string' => 'stack_times',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'staking'],
    ],
    'admin.staking.time.store' => [
        'method'       => 'post',
        'query_string' => 'stak_time_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'store'],
    ],
    'admin.staking.time.update' => [
        'method'       => 'post',
        'query_string' => 'stak_time_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'update'],
    ],
    'admin.staking.time.status' => [
        'method'       => 'post',
        'query_string' => 'stak_time_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'status'],
    ],
    'admin.staking.time.status' => [
        'method'       => 'get',
        'query_string' => 'stak_invest',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'stakingInvest'],
    ],
    
    // manage pool 
    'admin.pool.index' => [
        'method'       => 'get',
        'query_string' => 'pool',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'pool'],
    ],
    'admin.pool.store' => [
        'method'       => 'post',
        'query_string' => 'pool_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'savePool'],
    ],
    'admin.pool.status' => [
        'method'       => 'post',
        'query_string' => 'pool_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'poolStatus'],
    ],
    'admin.pool.dispatch' => [
        'method'       => 'post',
        'query_string' => 'pool_dispatch',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'dispatchPool'],
    ],
    'admin.pool.invest' => [
        'method'       => 'get',
        'query_string' => 'pool_invest',
        'middleware'   => ['checkPlugin'],
        'action'       => [StakingPoolController::class, 'poolInvest'],
    ],

    // promotion tool

    'admin.promotion.index' => [
        'method'       => 'get',
        'query_string' => 'promotion',
        'middleware'   => ['checkPlugin'],
        'action'       => [PromotionalToolController::class, 'promotion'],
    ],
    'admin.promotion.store' => [
        'method'       => 'post',
        'query_string' => 'promotion_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [PromotionalToolController::class, 'savePromotionl'],
    ],
    'admin.promotion.delete' => [
        'method'       => 'post',
        'query_string' => 'promotion_delete',
        'middleware'   => ['checkPlugin'],
        'action'       => [PromotionalToolController::class, 'promotionDelete'],
    ],

    // KYC Settings
    'admin.kyc.index' => [
        'method'       => 'get',
        'query_string' => 'kyc',
        'middleware'   => ['checkPlugin'],
        'action'       => [KycController::class, 'index'],
    ],
    'admin.kyc.store' => [
        'method'       => 'post',
        'query_string' => 'kyc_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [KycController::class, 'saveKyc'],
    ],

    // holidays
    'admin.holiday.index' => [
        'method'       => 'get',
        'query_string' => 'holiday',
        'middleware'   => ['checkPlugin'],
        'action'       => [HolidayController::class, 'index'],
    ],
    'admin.holiday.store' => [
        'method'       => 'post',
        'query_string' => 'holiday_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [HolidayController::class, 'saveHoliday'],
    ],
    'admin.holiday.delete' => [
        'method'       => 'post',
        'query_string' => 'holiday_delete',
        'middleware'   => ['checkPlugin'],
        'action'       => [HolidayController::class, 'deleteHoliday'],
    ],
    'admin.offday.setting' => [
        'method'       => 'post',
        'query_string' => 'offday_setting',
        'middleware'   => ['checkPlugin'],
        'action'       => [HolidayController::class, 'saveOffDaySetting'],
    ],
    

    //User Ranking
    'admin.ranking.index' => [
        'method'       => 'get',
        'query_string' => 'ranking',
        'middleware'   => ['checkPlugin'],
        'action'       => [RankingController::class, 'index'],
    ],
    'admin.ranking.store' => [
        'method'       => 'post',
        'query_string' => 'ranking_store',
        'middleware'   => ['checkPlugin'],
        'action'       => [RankingController::class, 'store'],
    ],
    'admin.ranking.status' => [
        'method'       => 'post',
        'query_string' => 'ranking_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [RankingController::class, 'status'],
    ],

    // Referral
    'admin.referrals.index' => [
        'method'       => 'get',
        'query_string' => 'referrals',
        'middleware'   => ['checkPlugin'],
        'action'       => [ReferralController::class, 'index'],
    ],
    'admin.referrals.status' => [
        'method'       => 'get',
        'query_string' => 'referral_status',
        'middleware'   => ['checkPlugin'],
        'action'       => [ReferralController::class, 'status'],
    ],
    'admin.referrals.update' => [
        'method'       => 'post',
        'query_string' => 'referral_update',
        'middleware'   => ['checkPlugin'],
        'action'       => [ReferralController::class, 'update'],
    ],
    'admin.invest.report.dashboard' => [
        'method'       => 'get',
        'query_string' => 'invest_report_dashboard',
        'middleware'   => ['checkPlugin'],
        'action'       => [InvestReportController::class, 'dashboard'],
    ],
]);
