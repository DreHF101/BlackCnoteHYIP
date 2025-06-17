<?php

use Hyiplab\BackOffice\Router\Router;
use Hyiplab\Controllers\ActivationController;
use Hyiplab\Controllers\Gateway\Authorize\ProcessController;
use Hyiplab\Controllers\Gateway\Blockchain\ProcessController as BlockchainProcessController;
use Hyiplab\Controllers\Gateway\BTCPay\ProcessController as BTCPayProcessController;
use Hyiplab\Controllers\Gateway\Cashmaal\ProcessController as CashmaalProcessController;
use Hyiplab\Controllers\Gateway\CoinbaseCommerce\ProcessController as CoinbaseCommerceProcessController;
use Hyiplab\Controllers\Gateway\Coingate\ProcessController as CoingateProcessController;
use Hyiplab\Controllers\Gateway\Coinpayments\ProcessController as CoinpaymentsProcessController;
use Hyiplab\Controllers\Gateway\CoinpaymentsFiat\ProcessController as CoinpaymentsFiatProcessController;
use Hyiplab\Controllers\Gateway\Flutterwave\ProcessController as FlutterwaveProcessController;
use Hyiplab\Controllers\Gateway\Instamojo\ProcessController as InstamojoProcessController;
use Hyiplab\Controllers\Gateway\MercadoPago\ProcessController as MercadoPagoProcessController;
use Hyiplab\Controllers\Gateway\Mollie\ProcessController as MollieProcessController;
use Hyiplab\Controllers\Gateway\NMI\ProcessController as NMIProcessController;
use Hyiplab\Controllers\Gateway\NowPaymentsCheckout\ProcessController as NowPaymentsCheckoutProcessController;
use Hyiplab\Controllers\Gateway\NowPaymentsHosted\ProcessController as NowPaymentsHostedProcessController;
use Hyiplab\Controllers\Gateway\Payeer\ProcessController as PayeerProcessController;
use Hyiplab\Controllers\Gateway\Paypal\ProcessController as PaypalProcessController;
use Hyiplab\Controllers\Gateway\PaypalSdk\ProcessController as PaypalSdkProcessController;
use Hyiplab\Controllers\Gateway\Paystack\ProcessController as PaystackProcessController;
use Hyiplab\Controllers\Gateway\Paytm\ProcessController as PaytmProcessController;
use Hyiplab\Controllers\Gateway\PerfectMoney\ProcessController as PerfectMoneyProcessController;
use Hyiplab\Controllers\Gateway\Skrill\ProcessController as SkrillProcessController;
use Hyiplab\Controllers\Gateway\Stripe\ProcessController as StripeProcessController;
use Hyiplab\Controllers\Gateway\StripeJs\ProcessController as StripeJsProcessController;
use Hyiplab\Controllers\Gateway\StripeV3\ProcessController as StripeV3ProcessController;
use Hyiplab\Controllers\Gateway\VoguePay\ProcessController as VoguePayProcessController;
use Hyiplab\Controllers\User\Auth\ForgotPasswordController;
use Hyiplab\Controllers\User\Auth\LoginController;
use Hyiplab\Controllers\User\Auth\RegisterController;
use Hyiplab\Controllers\User\DashboardController;
use Hyiplab\Controllers\User\DepositController;
use Hyiplab\Controllers\User\InvestController;
use Hyiplab\Controllers\User\PlanController;
use Hyiplab\Controllers\User\ProfileController;
use Hyiplab\Controllers\User\ReferralController;
use Hyiplab\Controllers\User\TicketController;
use Hyiplab\Controllers\User\TransactionController;
use Hyiplab\Controllers\User\WithdrawController;
use Hyiplab\Controllers\User\ScheduleController;
use Hyiplab\Controllers\User\StakingController;
use Hyiplab\Controllers\User\PoolController;

$router = new Router;

$router->router([
    'user.login' => [
        'method'     => 'get',
        'uri'        => 'login',
        'middleware' => ['authorized', 'checkPlugin'],
        'action'     => [LoginController::class, 'login'],
    ]
]);

$router->router([
    'user.logout' => [
        'method'     => 'get',
        'uri'        => 'logout',
        'middleware' => ['auth', 'checkPlugin'],
        'action'     => [LoginController::class, 'logout'],
    ]
]);

$router->router([
    'user.forget.password' => [
        'method'     => 'any',
        'uri'        => 'forgot',
        'middleware' => ['authorized', 'checkPlugin'],
        'action'     => [ForgotPasswordController::class, 'forgot'],
    ]
]);

$router->router([
    'user.register' => [
        'method'     => 'any',
        'uri'        => 'register',
        'middleware' => ['authorized', 'allow_registration', 'checkPlugin'],
        'action'     => [RegisterController::class, 'showRegisterForm'],
    ]
]);

$router->router([
    'user.home' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DashboardController::class, 'index'],
    ]
]);

$router->router([
    'user.inactive' => [
        'method'     => 'get',
        'uri'        => 'user-inactive',
        'middleware' => ['auth', 'checkPlugin'],
        'action'     => [DashboardController::class, 'inactive'],
    ]
]);

//deposit
$router->router([
    'user.deposit.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/deposit',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DepositController::class, 'index'],
    ]
]);

$router->router([
    'user.deposit.insert' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/deposit-insert',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DepositController::class, 'insert'],
    ]
]);

$router->router([
    'user.deposit.manual' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/deposit-manual',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DepositController::class, 'manual'],
    ]
]);

$router->router([
    'user.deposit.manual.update' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/deposit-manual-update',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DepositController::class, 'manualUpdate'],
    ]
]);

$router->router([
    'user.deposit.confirm' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/deposit-confirm',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DepositController::class, 'confirm'],
    ]
]);

$router->router([
    'user.deposit.history' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/deposit-history',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DepositController::class, 'history'],
    ]
]);

$router->router([
    'user.transfer.balance' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/transfer-balance',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DashboardController::class, 'transferBalance'],
    ]
]);
$router->router([
    'user.transfer.balance.submit' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/transfer-balance-submit',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DashboardController::class, 'transferBalanceSubmit'],
    ]
]);

// ======================================================= IPN ==========================================================
$router->router([
    'ipn.stripe' => [
        'method'     => 'post',
        'uri'        => 'ipn/stripe',
        'middleware' => ['checkPlugin'],
        'action'     => [StripeProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.authorize' => [
        'method'     => 'post',
        'uri'        => 'ipn/authorize',
        'middleware' => ['checkPlugin'],
        'action'     => [ProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.blockchain' => [
        'method'     => 'any',
        'uri'        => 'ipn/blockchain',
        'middleware' => ['checkPlugin'],
        'action'     => [BlockchainProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.cashmaal' => [
        'method'     => 'any',
        'uri'        => 'ipn/cashmaal',
        'middleware' => ['checkPlugin'],
        'action'     => [CashmaalProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.coinbasecommerce' => [
        'method'     => 'any',
        'uri'        => 'ipn/coinbasecommerce',
        'middleware' => ['checkPlugin'],
        'action'     => [CoinbaseCommerceProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.coingate' => [
        'method'     => 'any',
        'uri'        => 'ipn/coingate',
        'middleware' => ['checkPlugin'],
        'action'     => [CoingateProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.coinpayments' => [
        'method'     => 'any',
        'uri'        => 'ipn/coinpayments',
        'middleware' => ['checkPlugin'],
        'action'     => [CoinpaymentsProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.coinpaymentsfiat' => [
        'method'     => 'any',
        'uri'        => 'ipn/coinpaymentsfiat',
        'middleware' => ['checkPlugin'],
        'action'     => [CoinpaymentsFiatProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.flutterwave' => [
        'method'     => 'any',
        'uri'        => 'ipn/flutterwave',
        'middleware' => ['checkPlugin'],
        'action'     => [FlutterwaveProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.instamojo' => [
        'method'     => 'any',
        'uri'        => 'ipn/instamojo',
        'middleware' => ['checkPlugin'],
        'action'     => [InstamojoProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.mercadopago' => [
        'method'     => 'any',
        'uri'        => 'ipn/mercadopago',
        'middleware' => ['checkPlugin'],
        'action'     => [MercadoPagoProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.mollie' => [
        'method'     => 'any',
        'uri'        => 'ipn/mollie',
        'middleware' => ['checkPlugin'],
        'action'     => [MollieProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.nmi' => [
        'method'     => 'any',
        'uri'        => 'ipn/nmi',
        'middleware' => ['checkPlugin'],
        'action'     => [NMIProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.payeer' => [
        'method'     => 'any',
        'uri'        => 'ipn/payeer',
        'middleware' => ['checkPlugin'],
        'action'     => [PayeerProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.paypal' => [
        'method'     => 'any',
        'uri'        => 'ipn/paypal',
        'middleware' => ['checkPlugin'],
        'action'     => [PaypalProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.paypalsdk' => [
        'method'     => 'any',
        'uri'        => 'ipn/paypalsdk',
        'middleware' => ['checkPlugin'],
        'action'     => [PaypalSdkProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.paystack' => [
        'method'     => 'any',
        'uri'        => 'ipn/paystack',
        'middleware' => ['checkPlugin'],
        'action'     => [PaystackProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.paytm' => [
        'method'     => 'any',
        'uri'        => 'ipn/paytm',
        'middleware' => ['checkPlugin'],
        'action'     => [PaytmProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.perfectmoney' => [
        'method'     => 'any',
        'uri'        => 'ipn/perfectmoney',
        'middleware' => ['checkPlugin'],
        'action'     => [PerfectMoneyProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.skrill' => [
        'method'     => 'any',
        'uri'        => 'ipn/skrill',
        'middleware' => ['checkPlugin'],
        'action'     => [SkrillProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.stripejs' => [
        'method'     => 'any',
        'uri'        => 'ipn/stripejs',
        'middleware' => ['checkPlugin'],
        'action'     => [StripeJsProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.stripev3' => [
        'method'     => 'any',
        'uri'        => 'ipn/stripev3',
        'middleware' => ['checkPlugin'],
        'action'     => [StripeV3ProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.voguepay' => [
        'method'     => 'any',
        'uri'        => 'ipn/voguepay',
        'middleware' => ['checkPlugin'],
        'action'     => [VoguePayProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.btcpay' => [
        'method'     => 'any',
        'uri'        => 'ipn/btcpay',
        'middleware' => ['checkPlugin'],
        'action'     => [BTCPayProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.nowpaymentscheckout' => [
        'method'     => 'any',
        'uri'        => 'ipn/nowpaymentscheckout',
        'middleware' => ['checkPlugin'],
        'action'     => [NowPaymentsCheckoutProcessController::class, 'ipn']
    ]
]);
$router->router([
    'ipn.nowpaymentshosted' => [
        'method'     => 'any',
        'uri'        => 'ipn/nowpaymentshosted',
        'middleware' => ['checkPlugin'],
        'action'     => [NowPaymentsHostedProcessController::class, 'ipn']
    ]
]);
// ======================================================= IPN ==========================================================

$router->router([
    'user.withdraw.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/withdraw',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [WithdrawController::class, 'index'],
    ]
]);

$router->router([
    'user.withdraw.insert' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/withdraw-insert',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [WithdrawController::class, 'insert'],
    ]
]);

$router->router([
    'user.withdraw.preview' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/withdraw-preview',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [WithdrawController::class, 'preview'],
    ]
]);

$router->router([
    'user.withdraw.submit' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/withdraw-submit',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [WithdrawController::class, 'submit'],
    ]
]);

$router->router([
    'user.withdraw.history' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/withdraw-history',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [WithdrawController::class, 'history'],
    ]
]);

$router->router([
    'user.transaction.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/transactions',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [TransactionController::class, 'index'],
    ]
]);

$router->router([
    'user.change.password' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/change-password',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [ProfileController::class, 'changePassword'],
    ]
]);

$router->router([
    'user.change.password.update' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/change-password-update',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [ProfileController::class, 'changePasswordUpdate'],
    ]
]);

$router->router([
    'user.profile.setting' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/profile-setting',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [ProfileController::class, 'profileSetting'],
    ]
]);

$router->router([
    'user.profile.setting.update' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/profile-setting-update',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [ProfileController::class, 'profileSettingUpdate'],
    ]
]);

$router->router([
    'user.ticket.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/tickets',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [TicketController::class, 'myTicket'],
    ]
]);

$router->router([
    'user.ticket.create' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/ticket-create',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [TicketController::class, 'createTicket'],
    ]
]);

$router->router([
    'user.ticket.store' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/ticket-store',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [TicketController::class, 'storeTicket'],
    ]
]);

$router->router([
    'user.ticket.view' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/ticket-view',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [TicketController::class, 'viewTicket'],
    ]
]);

$router->router([
    'user.ticket.close' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/ticket-close',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [TicketController::class, 'closeTicket'],
    ]
]);

$router->router([
    'user.ticket.reply' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/ticket-reply',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [TicketController::class, 'replyTicket'],
    ]
]);

$router->router([
    'user.ticket.download' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/ticket-download',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [TicketController::class, 'downloadTicket'],
    ]
]);

$router->router([
    'user.invest.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/invest',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [InvestController::class, 'index'],
    ]
]);
$router->router([
    'user.invest.log' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/invest-log',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [InvestController::class, 'log'],
    ]
]);
$router->router([
    'user.invest.detail' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/invest-detail',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [InvestController::class, 'detail'],
    ]
]);
$router->router([
    'user.invest.ranking' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/ranking',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [InvestController::class, 'ranking'],
    ]
]);
$router->router([
    'user.plan.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/plan',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [PlanController::class, 'index'],
    ]
]);
$router->router([
    'user.plan.invest' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/plan-invest',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [PlanController::class, 'invest'],
    ]
]);
$router->router([
    'user.referral.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/referrals',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [ReferralController::class, 'index'],
    ]
]);


$router->router([
    'user.schedule.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/schedule',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [ScheduleController::class, 'index'],
    ]
]);
$router->router([
    'user.schedule.status' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/schedule-status',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [ScheduleController::class, 'scheduleInvestStatus'],
    ]
]);

$router->router([
    'user.staking.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/staking',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [StakingController::class, 'staking'],
    ]
]);
$router->router([
    'user.staking.save' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/staking-save',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [StakingController::class, 'stakingSave'],
    ]
]);

$router->router([
    'user.pool.index' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/pool',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [PoolController::class, 'pool'],
    ]
]);
$router->router([
    'user.pool.invest' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/pool-invest',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [PoolController::class, 'poolInvest'],
    ]
]);

$router->router([
    'user.pool.store' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/pool-store',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [PoolController::class, 'poolInvestStore'],
    ]
]);
$router->router([
    'user.promotional.banner' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/promotional-banners',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DashboardController::class, 'promotionalBanner'],
    ]
]);

$router->router([
    'user.kyc.form' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/kyc-form',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DashboardController::class, 'kycForm'],
    ]
]);
$router->router([
    'user.kyc.data' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/kyc-data',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DashboardController::class, 'kycData'],
    ]
]);
$router->router([
    'user.kyc.submit' => [
        'method'     => 'post',
        'uri'        => 'user-dashboard/kyc-submit',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DashboardController::class, 'kycSubmit'],
    ]
]);
$router->router([
    'user.attachment.download' => [
        'method'     => 'get',
        'uri'        => 'user-dashboard/download-kyc-attachment',
        'middleware' => ['auth', 'CheckUser', 'checkPlugin'],
        'action'     => [DashboardController::class, 'downloadKycAttachment'],
    ]
]);


// activation routes
$router->router([
    'plugin.activation' => [
        'method'     => 'get',
        'uri'        => HYIPLAB_PLUGIN_NAME . '-activation',
        'action'     => [ActivationController::class, 'activate'],
    ]
]);
