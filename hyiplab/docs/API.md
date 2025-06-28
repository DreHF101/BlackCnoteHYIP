# Hyiplab Plugin API Documentation

## Overview

This document provides comprehensive API documentation for the Hyiplab Plugin services. All services follow a consistent pattern and provide type-safe methods with proper error handling.

## Table of Contents

1. [Financial Services](#financial-services)
2. [Investment Services](#investment-services)
3. [User Management Services](#user-management-services)
4. [Support & Communication Services](#support--communication-services)
5. [System Services](#system-services)
6. [Error Handling](#error-handling)
7. [Validation Rules](#validation-rules)

---

## Financial Services

### WithdrawalService

Handles all withdrawal-related operations including creation, approval, rejection, and user withdrawal management.

#### Methods

##### `getWithdrawalMethods()`
Returns all active withdrawal methods.

**Returns:** `Collection<WithdrawMethod>`

##### `checkHolidayStatus(): array`
Checks if today is a holiday and returns holiday status information.

**Returns:** `array`
```php
[
    'is_holiday' => bool,
    'next_working_day' => string|null
]
```

##### `canWithdrawToday(): bool`
Checks if withdrawals are allowed today (considering holidays).

**Returns:** `bool`

##### `createWithdrawal(int $methodId, float $amount, int $userId): Withdrawal`
Creates a new withdrawal request.

**Parameters:**
- `$methodId` (int): Withdrawal method ID
- `$amount` (float): Withdrawal amount
- `$userId` (int): User ID

**Returns:** `Withdrawal`

**Throws:** `InvalidArgumentException` if amount is invalid or insufficient balance

##### `submitWithdrawal(string $trx, array $formData): Withdrawal`
Submits a withdrawal with form data.

**Parameters:**
- `$trx` (string): Transaction reference
- `$formData` (array): Form submission data

**Returns:** `Withdrawal`

**Throws:** `InvalidArgumentException` if withdrawal not found or insufficient balance

##### `getUserWithdrawals(int $userId, int $paginate = 20)`
Gets user's withdrawal history.

**Parameters:**
- `$userId` (int): User ID
- `$paginate` (int): Number of items per page

**Returns:** `Collection<Withdrawal>`

##### `getWithdrawals(array $filters = [], int $paginate = 20)`
Gets withdrawals with optional filters (admin method).

**Parameters:**
- `$filters` (array): Filter criteria
- `$paginate` (int): Number of items per page

**Returns:** `Collection<Withdrawal>`

##### `approveWithdrawal(int $withdrawalId): Withdrawal`
Approves a withdrawal request.

**Parameters:**
- `$withdrawalId` (int): Withdrawal ID

**Returns:** `Withdrawal`

##### `rejectWithdrawal(int $withdrawalId, string $reason = ''): Withdrawal`
Rejects a withdrawal request.

**Parameters:**
- `$withdrawalId` (int): Withdrawal ID
- `$reason` (string): Rejection reason

**Returns:** `Withdrawal`

---

### DepositService

Handles deposit processing, approval, rejection, and management.

#### Methods

##### `getDeposits(array $filters = [], int $paginate = 20)`
Gets deposits with optional filters.

**Parameters:**
- `$filters` (array): Filter criteria
- `$paginate` (int): Number of items per page

**Returns:** `Collection<Deposit>`

**Filter Options:**
- `status`: Deposit status (0=initiated, 1=approved, 2=pending, 3=rejected)
- `method_code`: Payment gateway code
- `user_id`: User ID

##### `approveDeposit(int $depositId): Deposit`
Approves a deposit.

**Parameters:**
- `$depositId` (int): Deposit ID

**Returns:** `Deposit`

##### `rejectDeposit(int $depositId, string $message): Deposit`
Rejects a deposit with a message.

**Parameters:**
- `$depositId` (int): Deposit ID
- `$message` (string): Rejection message

**Returns:** `Deposit`

##### `updateUserData(Deposit $deposit, bool $isManual = false)`
Updates user balance and creates transaction record.

**Parameters:**
- `$deposit` (Deposit): Deposit instance
- `$isManual` (bool): Whether it's a manual deposit

---

### GatewayService

Manages payment gateway configurations and operations.

#### Methods

##### `updateGateway(int $gatewayId, array $data): bool`
Updates automatic gateway configuration.

**Parameters:**
- `$gatewayId` (int): Gateway ID
- `$data` (array): Gateway configuration data

**Returns:** `bool`

##### `createManualGateway(array $data): Gateway`
Creates a new manual gateway.

**Parameters:**
- `$data` (array): Gateway data

**Returns:** `Gateway`

##### `updateManualGateway(int $gatewayId, array $data): Gateway`
Updates manual gateway configuration.

**Parameters:**
- `$gatewayId` (int): Gateway ID
- `$data` (array): Gateway data

**Returns:** `Gateway`

---

### TransferService

Handles balance transfer operations between users.

#### Methods

##### `transfer(WP_User $sender, Request $request): bool|WP_Error`
Transfers balance from one user to another.

**Parameters:**
- `$sender` (WP_User): Sending user
- `$request` (Request): Transfer request data

**Returns:** `bool|WP_Error`

---

## Investment Services

### InvestmentService

Provides user investment statistics and data retrieval.

#### Methods

##### `getUserInvestmentStats(int $userId): object`
Gets user's investment statistics.

**Parameters:**
- `$userId` (int): User ID

**Returns:** `object`
```php
{
    totalInvest: float,
    activePlan: int
}
```

##### `getUserActiveInvestments(int $userId, int $limit = 5)`
Gets user's active investments.

**Parameters:**
- `$userId` (int): User ID
- `$limit` (int): Number of investments to return

**Returns:** `Collection<Invest>`

##### `getUserTotalProfit(int $userId): float`
Gets user's total profit from investments.

**Parameters:**
- `$userId` (int): User ID

**Returns:** `float`

##### `getUserInvestmentChart(int $userId)`
Gets investment data for charting.

**Parameters:**
- `$userId` (int): User ID

**Returns:** `Collection`

##### `getUserInvestmentLogs(int $userId, int $paginate = 20)`
Gets user's investment logs.

**Parameters:**
- `$userId` (int): User ID
- `$paginate` (int): Number of items per page

**Returns:** `Collection<Invest>`

##### `getInvestmentDetails(int $investmentId, int $userId): array`
Gets detailed investment information.

**Parameters:**
- `$investmentId` (int): Investment ID
- `$userId` (int): User ID

**Returns:** `array`
```php
[
    'invest' => Invest,
    'plan' => object,
    'user' => WP_User,
    'transactions' => Collection<Transaction>
]
```

##### `getUserRankingData(int $userId): array|null`
Gets user ranking information.

**Parameters:**
- `$userId` (int): User ID

**Returns:** `array|null`

##### `isUserRankingEnabled(): bool`
Checks if user ranking is enabled.

**Returns:** `bool`

---

### InvestmentPlanService

Handles investment plan processing and validation.

#### Methods

##### `getActivePlans()`
Gets all active investment plans.

**Returns:** `Collection<Plan>`

##### `validateInvestmentRequest(array $data): array`
Validates investment request data.

**Parameters:**
- `$data` (array): Investment request data

**Returns:** `array` Validation rules

##### `processInvestment(int $userId, array $data): array`
Processes an investment request.

**Parameters:**
- `$userId` (int): User ID
- `$data` (array): Investment data

**Returns:** `array`
```php
[
    'type' => 'immediate|scheduled',
    'message' => string
]
```

**Throws:** `InvalidArgumentException` for validation errors

##### `getPlanById(int $planId): ?Plan`
Gets a plan by ID.

**Parameters:**
- `$planId` (int): Plan ID

**Returns:** `Plan|null`

##### `validatePlanForInvestment(Plan $plan, array $data): void`
Validates plan for investment.

**Parameters:**
- `$plan` (Plan): Plan instance
- `$data` (array): Investment data

**Throws:** `InvalidArgumentException` for validation errors

##### `checkUserBalance(int $userId, float $amount, string $walletType): bool`
Checks if user has sufficient balance.

**Parameters:**
- `$userId` (int): User ID
- `$amount` (float): Required amount
- `$walletType` (string): Wallet type

**Returns:** `bool`

##### `isScheduleInvestEnabled(): bool`
Checks if scheduled investment is enabled.

**Returns:** `bool`

---

### PlanService

Manages investment plan creation and updates (admin).

#### Methods

##### `createPlan(array $data): Plan`
Creates a new investment plan.

**Parameters:**
- `$data` (array): Plan data

**Returns:** `Plan`

**Throws:** `InvalidArgumentException` for validation errors

##### `updatePlan(int $planId, array $data): Plan`
Updates an existing plan.

**Parameters:**
- `$planId` (int): Plan ID
- `$data` (array): Plan data

**Returns:** `Plan`

##### `togglePlanStatus(int $planId): Plan`
Toggles plan active/inactive status.

**Parameters:**
- `$planId` (int): Plan ID

**Returns:** `Plan`

---

### PoolService

Manages pool investment operations.

#### Methods

##### `isPoolEnabled(): bool`
Checks if pool investment is enabled.

**Returns:** `bool`

##### `getActivePools()`
Gets all active pools.

**Returns:** `Collection<Pool>`

##### `getUserPoolInvestments(int $userId, int $paginate = 20)`
Gets user's pool investments.

**Parameters:**
- `$userId` (int): User ID
- `$paginate` (int): Number of items per page

**Returns:** `Collection<PoolInvest>`

##### `processPoolInvestment(int $userId, array $data): PoolInvest`
Processes a pool investment.

**Parameters:**
- `$userId` (int): User ID
- `$data` (array): Investment data

**Returns:** `PoolInvest`

**Throws:** `InvalidArgumentException` for validation errors

---

### StakingService

Handles staking operations.

#### Methods

##### `createStakingInvestment(int $userId, int $stakingId, float $amount, string $wallet): StakingInvest`
Creates a staking investment.

**Parameters:**
- `$userId` (int): User ID
- `$stakingId` (int): Staking pool ID
- `$amount` (float): Investment amount
- `$wallet` (string): Wallet type

**Returns:** `StakingInvest`

---

## User Management Services

### AdminUserService

Manages user operations for administrators.

#### Methods

##### `getUsers(array $filters = [], int $paginate = 20)`
Gets users with optional filters.

**Parameters:**
- `$filters` (array): Filter criteria
- `$paginate` (int): Number of items per page

**Returns:** `Collection<WP_User>`

##### `updateUser(int $userId, array $data): bool`
Updates user information.

**Parameters:**
- `$userId` (int): User ID
- `$data` (array): User data

**Returns:** `bool`

##### `addSubBalance(int $userId, float $amount, string $wallet, string $operation, string $remark): bool`
Adds or subtracts user balance.

**Parameters:**
- `$userId` (int): User ID
- `$amount` (float): Amount to add/subtract
- `$wallet` (string): Wallet type
- `$operation` (string): Operation type (+ or -)
- `$remark` (string): Transaction remark

**Returns:** `bool`

##### `getCountryData(): array`
Gets country data for forms.

**Returns:** `array`

---

### UserService

Handles user profile operations.

#### Methods

##### `updateUserProfile(int $userId, array $data): bool`
Updates user profile information.

**Parameters:**
- `$userId` (int): User ID
- `$data` (array): Profile data

**Returns:** `bool`

##### `changeUserPassword(int $userId, Request $request): bool|WP_Error`
Changes user password.

**Parameters:**
- `$userId` (int): User ID
- `$request` (Request): Password change request

**Returns:** `bool|WP_Error`

---

### DashboardService

Provides user dashboard data and KYC management.

#### Methods

##### `getUserDashboardData(int $userId): array`
Gets comprehensive dashboard data for a user.

**Parameters:**
- `$userId` (int): User ID

**Returns:** `array`
```php
[
    'user' => WP_User,
    'total_invest' => float,
    'total_withdraw' => float,
    'total_deposit' => float,
    'total_transfer' => float,
    'total_commission' => float,
    'deposit_wallet' => float,
    'interest_wallet' => float,
    'kyc_status' => int,
    'kyc_form' => Form|null,
    'promotional_banners' => Collection
]
```

##### `getPromotionalBanners()`
Gets active promotional banners.

**Returns:** `Collection`

##### `isKycEnabled(): bool`
Checks if KYC is enabled.

**Returns:** `bool`

##### `getUserKycStatus(int $userId): int`
Gets user's KYC status.

**Parameters:**
- `$userId` (int): User ID

**Returns:** `int` (0=not submitted, 1=pending, 2=approved, 3=rejected)

##### `getKycForm(): ?Form`
Gets the KYC form configuration.

**Returns:** `Form|null`

##### `submitKycData(int $userId, array $formData): bool`
Submits KYC data for a user.

**Parameters:**
- `$userId` (int): User ID
- `$formData` (array): KYC form data

**Returns:** `bool`

**Throws:** `InvalidArgumentException` for validation errors

##### `getKycValidationRules(): array`
Gets KYC form validation rules.

**Returns:** `array`

##### `getUserKycDetails(int $userId): array`
Gets user's KYC details.

**Parameters:**
- `$userId` (int): User ID

**Returns:** `array`

##### `downloadKycAttachment(string $encryptedFile, int $userId): array`
Downloads KYC attachment.

**Parameters:**
- `$encryptedFile` (string): Encrypted file path
- `$userId` (int): User ID

**Returns:** `array`

---

## Support & Communication Services

### SupportTicketService

Manages support ticket operations for both admin and users.

#### Methods

##### `getTickets(array $filters = [], int $paginate = 20)`
Gets support tickets with filters (admin).

**Parameters:**
- `$filters` (array): Filter criteria
- `$paginate` (int): Number of items per page

**Returns:** `Collection<SupportTicket>`

##### `replyToTicket(int $ticketId, string $message, array $attachments = []): SupportMessage`
Replies to a support ticket (admin).

**Parameters:**
- `$ticketId` (int): Ticket ID
- `$message` (string): Reply message
- `$attachments` (array): File attachments

**Returns:** `SupportMessage`

##### `closeTicket(int $ticketId): SupportTicket`
Closes a support ticket (admin).

**Parameters:**
- `$ticketId` (int): Ticket ID

**Returns:** `SupportTicket`

##### `deleteMessage(int $messageId): bool`
Deletes a support message (admin).

**Parameters:**
- `$messageId` (int): Message ID

**Returns:** `bool`

##### `getTicketWithMessages(int $ticketId): array`
Gets ticket with all messages (admin).

**Parameters:**
- `$ticketId` (int): Ticket ID

**Returns:** `array`
```php
[
    'ticket' => SupportTicket,
    'messages' => Collection<SupportMessage>
]
```

##### `getUserTickets(int $userId, int $paginate = 20)`
Gets user's support tickets.

**Parameters:**
- `$userId` (int): User ID
- `$paginate` (int): Number of items per page

**Returns:** `Collection<SupportTicket>`

##### `createUserTicket(int $userId, array $data): SupportTicket`
Creates a new support ticket (user).

**Parameters:**
- `$userId` (int): User ID
- `$data` (array): Ticket data

**Returns:** `SupportTicket`

**Throws:** `InvalidArgumentException` for validation errors

##### `getUserTicketByTicketNumber(string $ticketNumber, int $userId): SupportTicket`
Gets user ticket by ticket number.

**Parameters:**
- `$ticketNumber` (string): Ticket number
- `$userId` (int): User ID

**Returns:** `SupportTicket`

##### `closeUserTicket(int $ticketId, int $userId): SupportTicket`
Closes a user's ticket.

**Parameters:**
- `$ticketId` (int): Ticket ID
- `$userId` (int): User ID

**Returns:** `SupportTicket`

##### `replyToUserTicket(int $ticketId, int $userId, string $message, array $attachments = []): SupportMessage`
Replies to user's ticket.

**Parameters:**
- `$ticketId` (int): Ticket ID
- `$userId` (int): User ID
- `$message` (string): Reply message
- `$attachments` (array): File attachments

**Returns:** `SupportMessage`

##### `getTicketWithMessagesByTicketNumber(string $ticketNumber, int $userId): array`
Gets user ticket with messages by ticket number.

**Parameters:**
- `$ticketNumber` (string): Ticket number
- `$userId` (int): User ID

**Returns:** `array`
```php
[
    'ticket' => SupportTicket,
    'messages' => Collection<SupportMessage>
]
```

---

### NotificationService

Manages email and SMS notification configurations.

#### Methods

##### `updateGlobalSettings(array $data): void`
Updates global notification settings.

**Parameters:**
- `$data` (array): Global settings data

##### `updateEmailSettings(array $data): void`
Updates email configuration.

**Parameters:**
- `$data` (array): Email settings data

##### `updateSmsSettings(array $data): void`
Updates SMS configuration.

**Parameters:**
- `$data` (array): SMS settings data

##### `updateTemplate(int $templateId, array $data): NotificationTemplate`
Updates notification template.

**Parameters:**
- `$templateId` (int): Template ID
- `$data` (array): Template data

**Returns:** `NotificationTemplate`

##### `testEmail(string $email): void`
Sends test email.

**Parameters:**
- `$email` (string): Email address

##### `testSms(string $mobile): void`
Sends test SMS.

**Parameters:**
- `$mobile` (string): Mobile number

##### `getEmailConfig(): object`
Gets email configuration.

**Returns:** `object`

##### `getSmsConfig(): object`
Gets SMS configuration.

**Returns:** `object`

---

## System Services

### SettingService

Manages system configuration and settings.

#### Methods

##### `updateGeneralSettings(Request $request): void`
Updates general system settings.

**Parameters:**
- `$request` (Request): Settings request

##### `updateSystemConfiguration(Request $request): void`
Updates system configuration.

**Parameters:**
- `$request` (Request): Configuration request

##### `updateLogoAndFavicon(Request $request): array`
Updates logo and favicon.

**Parameters:**
- `$request` (Request): File upload request

**Returns:** `array`

---

### ReportService

Handles transaction and investment reports.

#### Methods

##### `getReports()`
Gets available report types.

**Returns:** `array`

##### `submitReport(string $type, string $message)`
Submits a report.

**Parameters:**
- `$type` (string): Report type
- `$message` (string): Report message

##### `getTransactionReport(array $filters = [], int $paginate = 20)`
Gets transaction report.

**Parameters:**
- `$filters` (array): Filter criteria
- `$paginate` (int): Number of items per page

**Returns:** `Collection<Transaction>`

##### `getInvestmentHistoryReport(int $paginate = 20): array`
Gets investment history report.

**Parameters:**
- `$paginate` (int): Number of items per page

**Returns:** `array`

##### `getTransactionReportByUsername(string $username, int $paginate = 20)`
Gets transaction report by username.

**Parameters:**
- `$username` (string): Username
- `$paginate` (int): Number of items per page

**Returns:** `Collection<Transaction>`

---

### ExtensionService

Manages plugin extensions.

#### Methods

##### `getAllExtensions()`
Gets all available extensions.

**Returns:** `Collection<Extension>`

##### `updateExtension(int $extensionId, array $data): Extension`
Updates extension configuration.

**Parameters:**
- `$extensionId` (int): Extension ID
- `$data` (array): Extension data

**Returns:** `Extension`

##### `toggleExtensionStatus(int $extensionId): Extension`
Toggles extension status.

**Parameters:**
- `$extensionId` (int): Extension ID

**Returns:** `Extension`

##### `getExtensionValidationRules(int $extensionId): array`
Gets extension validation rules.

**Parameters:**
- `$extensionId` (int): Extension ID

**Returns:** `array`

---

### DownloadService

Handles file download operations.

#### Methods

##### `downloadFile(string $encryptedFilePath)`
Downloads an encrypted file.

**Parameters:**
- `$encryptedFilePath` (string): Encrypted file path

---

## Error Handling

All services follow consistent error handling patterns:

### Exception Types

1. **InvalidArgumentException**: Thrown for validation errors
2. **RuntimeException**: Thrown for runtime errors
3. **NotFoundException**: Thrown when resources are not found

### Error Response Format

```php
try {
    $result = $service->method($params);
} catch (InvalidArgumentException $e) {
    // Handle validation errors
    $error = [
        'type' => 'validation',
        'message' => $e->getMessage(),
        'field' => $e->getField() ?? null
    ];
} catch (RuntimeException $e) {
    // Handle runtime errors
    $error = [
        'type' => 'runtime',
        'message' => $e->getMessage()
    ];
}
```

---

## Validation Rules

### Common Validation Patterns

#### Investment Request Validation
```php
[
    'amount' => 'required|numeric|min:1',
    'plan_id' => 'required|integer|exists:plans,id',
    'wallet_type' => 'required|in:deposit_wallet,interest_wallet',
    'invest_time' => 'required|in:invest_now,schedule',
    'compound_interest' => 'nullable|integer|min:0'
]
```

#### Withdrawal Request Validation
```php
[
    'method_id' => 'required|integer|exists:withdraw_methods,id',
    'amount' => 'required|numeric|min:1',
    'wallet_type' => 'required|in:deposit_wallet,interest_wallet'
]
```

#### KYC Form Validation
```php
[
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',
    'email' => 'required|email',
    'phone' => 'required|string|max:20',
    'address' => 'required|string|max:500',
    'city' => 'required|string|max:100',
    'state' => 'required|string|max:100',
    'zip_code' => 'required|string|max:20',
    'country' => 'required|string|max:100',
    'document_type' => 'required|in:passport,national_id,drivers_license',
    'document_number' => 'required|string|max:100',
    'document_front' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    'document_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    'selfie' => 'required|file|mimes:jpg,jpeg,png|max:2048'
]
```

#### Support Ticket Validation
```php
[
    'subject' => 'required|string|max:255',
    'message' => 'required|string|max:5000',
    'priority' => 'required|in:low,medium,high,urgent',
    'category' => 'required|string|max:100',
    'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048'
]
```

---

## Usage Examples

### Basic Service Usage

```php
// Using dependency injection
$withdrawalService = app(WithdrawalService::class);
$withdrawals = $withdrawalService->getUserWithdrawals($userId);

// Creating a withdrawal
try {
    $withdrawal = $withdrawalService->createWithdrawal($methodId, $amount, $userId);
} catch (InvalidArgumentException $e) {
    // Handle validation error
    $error = $e->getMessage();
}
```

### Controller Integration

```php
class WithdrawalController extends Controller
{
    protected WithdrawalService $withdrawalService;

    public function __construct()
    {
        parent::__construct();
        $this->withdrawalService = app(WithdrawalService::class);
    }

    public function index()
    {
        $withdrawals = $this->withdrawalService->getWithdrawals();
        return $this->view('withdrawals.index', compact('withdrawals'));
    }
}
```

### Error Handling in Controllers

```php
public function store(Request $request)
{
    try {
        $withdrawal = $this->withdrawalService->createWithdrawal(
            $request->method_id,
            $request->amount,
            $request->user_id
        );
        
        $notify[] = ['success', 'Withdrawal created successfully'];
        return hyiplab_back($notify);
    } catch (InvalidArgumentException $e) {
        $notify[] = ['error', $e->getMessage()];
        return hyiplab_back($notify);
    }
}
```

---

## Best Practices

1. **Always use dependency injection** for service instantiation
2. **Handle exceptions properly** in controllers
3. **Validate input data** before passing to services
4. **Use type hints** for better code clarity
5. **Follow consistent naming conventions**
6. **Document complex business logic** in service methods
7. **Use service methods for reusable business logic**
8. **Keep controllers thin** and delegate to services
9. **Test service methods** with unit tests
10. **Use proper error messages** for better user experience

---

## Version History

- **v1.0.0**: Initial API documentation
- **v1.1.0**: Added error handling section
- **v1.2.0**: Added validation rules and usage examples
- **v1.3.0**: Added best practices and version history 