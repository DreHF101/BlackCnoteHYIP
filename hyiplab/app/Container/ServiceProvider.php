<?php

namespace Hyiplab\Container;

use Hyiplab\Container\Container;
use Hyiplab\Services\WithdrawalService;
use Hyiplab\Services\DepositService;
use Hyiplab\Services\GatewayService;
use Hyiplab\Services\InvestmentService;
use Hyiplab\Services\InvestmentPlanService;
use Hyiplab\Services\PlanService;
use Hyiplab\Services\SupportTicketService;
use Hyiplab\Services\ReportService;
use Hyiplab\Services\NotificationService;
use Hyiplab\Services\ExtensionService;
use Hyiplab\Services\AdminUserService;
use Hyiplab\Services\SettingService;
use Hyiplab\Services\DashboardService;
use Hyiplab\Services\PoolService;
use Hyiplab\Services\StakingService;
use Hyiplab\Services\UserService;
use Hyiplab\Services\TransferService;
use Hyiplab\Services\DownloadService;

class ServiceProvider
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register all services in the container
     */
    public function register(): void
    {
        // Financial Services
        $this->container->singleton(WithdrawalService::class);
        $this->container->singleton(DepositService::class);
        $this->container->singleton(GatewayService::class);
        $this->container->singleton(TransferService::class);

        // Investment Services
        $this->container->singleton(InvestmentService::class);
        $this->container->singleton(InvestmentPlanService::class);
        $this->container->singleton(PlanService::class);
        $this->container->singleton(PoolService::class);
        $this->container->singleton(StakingService::class);

        // User Management Services
        $this->container->singleton(AdminUserService::class);
        $this->container->singleton(UserService::class);
        $this->container->singleton(DashboardService::class);

        // Support & Communication Services
        $this->container->singleton(SupportTicketService::class);
        $this->container->singleton(NotificationService::class);

        // System Services
        $this->container->singleton(SettingService::class);
        $this->container->singleton(ReportService::class);
        $this->container->singleton(ExtensionService::class);
        $this->container->singleton(DownloadService::class);

        // Register service interfaces (for future use)
        $this->registerInterfaces();
    }

    /**
     * Register service interfaces for dependency injection
     */
    private function registerInterfaces(): void
    {
        // Example: If we had interfaces, we would bind them here
        // $this->container->bind(WithdrawalServiceInterface::class, WithdrawalService::class);
        // $this->container->bind(DepositServiceInterface::class, DepositService::class);
    }

    /**
     * Boot the services (run any initialization code)
     */
    public function boot(): void
    {
        // Any service bootstrapping can go here
        // For example, loading configurations, setting up event listeners, etc.
    }
} 