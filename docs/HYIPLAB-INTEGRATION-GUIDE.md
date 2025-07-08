# HYIPLab Integration Guide for BlackCnote Theme

## Overview

The BlackCnote theme includes HYIPLab, a premium investment platform that provides comprehensive investment management features. This guide explains how to set up, configure, and demonstrate the HYIPLab features within your WordPress theme.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Feature Overview](#feature-overview)
3. [Installation & Setup](#installation--setup)
4. [Demo Content](#demo-content)
5. [Theme Integration](#theme-integration)
6. [Customization](#customization)
7. [Troubleshooting](#troubleshooting)

## Quick Start

### Prerequisites
- WordPress 6.0+
- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+
- Composer (for dependency management)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-repo/BlackCnote.git
   cd BlackCnote
   ```

2. **Install Dependencies**
   ```bash
   # Install HYIPLab dependencies
   cd hyiplab
   composer install
   
   # Install React app dependencies
   cd ../react-app
   npm install
   ```

3. **Start Development Environment**
   ```bash
   # From project root
   npm run dev:full
   ```

4. **Access Your Site**
   - WordPress: http://localhost:8888
   - React App: http://localhost:5174
   - Admin: http://localhost:8888/wp-admin

## Feature Overview

### Core HYIPLab Features

#### 1. Investment Plans Management
- **Location**: `hyiplab/app/Models/Plan.php`
- **Features**:
  - Create and manage investment plans
  - Set profit percentages and durations
  - Configure minimum/maximum investment amounts
  - Plan status management (active/inactive)

#### 2. User Investment System
- **Location**: `hyiplab/app/Models/Investment.php`
- **Features**:
  - User investment tracking
  - Profit calculation and distribution
  - Investment status management
  - Transaction history

#### 3. Transaction Management
- **Location**: `hyiplab/app/Models/Transaction.php`
- **Features**:
  - Deposit and withdrawal tracking
  - Transaction status management
  - Payment gateway integration
  - Transaction history and reporting

#### 4. User Dashboard
- **Location**: `hyiplab/views/user/dashboard.php`
- **Features**:
  - Investment overview
  - Profit/loss tracking
  - Transaction history
  - Account management

#### 5. Admin Panel
- **Location**: `hyiplab/views/admin/`
- **Features**:
  - User management
  - Investment plan management
  - Transaction monitoring
  - System statistics

### Theme Integration Features

#### 1. Investment Plans Page
- **Template**: `page-plans.php`
- **Features**:
  - Display available investment plans
  - Plan comparison
  - Investment form integration

#### 2. User Dashboard
- **Template**: `page-dashboard.php`
- **Features**:
  - User investment overview
  - Quick actions
  - Recent transactions

#### 3. Transaction History
- **Template**: `template-blackcnote-transactions.php`
- **Features**:
  - Complete transaction history
  - Filtering and sorting
  - Export functionality

## Installation & Setup

### 1. Database Setup

The HYIPLab plugin will automatically create required database tables on activation. However, you can also run the setup manually:

```php
// In WordPress admin or via WP-CLI
wp hyiplab setup-database
```

### 2. Plugin Activation

1. Go to WordPress Admin → Plugins
2. Find "HYIPLab" in the plugins list
3. Click "Activate"

### 3. Initial Configuration

1. **General Settings**
   - Go to HYIPLab → Settings
   - Configure site currency
   - Set default investment limits
   - Configure email notifications

2. **Payment Gateways**
   - Navigate to HYIPLab → Payment Methods
   - Configure your preferred payment gateways
   - Set up API keys and webhooks

3. **Investment Plans**
   - Go to HYIPLab → Investment Plans
   - Create your first investment plan
   - Set profit rates and durations

### 4. Theme Integration

The theme automatically integrates with HYIPLab when the plugin is active. Key integration points:

```php
// Check if HYIPLab is active
if (function_exists('hyiplab_system_instance')) {
    // HYIPLab features are available
    $hyiplab = hyiplab_system_instance();
    // Use HYIPLab functionality
}
```

## Demo Content

### 1. Sample Investment Plans

Create these demo plans to showcase the platform:

```php
// Example investment plans for demo
$demo_plans = [
    [
        'name' => 'Starter Plan',
        'min_amount' => 100,
        'max_amount' => 1000,
        'profit_rate' => 2.5,
        'duration' => 30,
        'description' => 'Perfect for beginners'
    ],
    [
        'name' => 'Premium Plan',
        'min_amount' => 1000,
        'max_amount' => 10000,
        'profit_rate' => 5.0,
        'duration' => 60,
        'description' => 'For serious investors'
    ],
    [
        'name' => 'VIP Plan',
        'min_amount' => 10000,
        'max_amount' => 100000,
        'profit_rate' => 8.0,
        'duration' => 90,
        'description' => 'Exclusive VIP benefits'
    ]
];
```

### 2. Demo Users

Create sample users to demonstrate the platform:

- **Demo Investor**: Regular user with sample investments
- **Demo Admin**: Administrator with full access
- **Demo Support**: Support staff account

### 3. Sample Transactions

Create realistic transaction history:

- Deposits from various payment methods
- Investment activations
- Profit distributions
- Withdrawal requests

## Theme Integration

### 1. Navigation Integration

The theme automatically adds HYIPLab menu items when the plugin is active:

```php
// In functions.php
function blackcnote_hyiplab_navigation() {
    if (function_exists('hyiplab_system_instance')) {
        // Add HYIPLab menu items
        wp_nav_menu([
            'theme_location' => 'hyiplab-menu',
            'container' => 'nav',
            'container_class' => 'hyiplab-navigation'
        ]);
    }
}
```

### 2. Shortcode Integration

Use these shortcodes to display HYIPLab content:

```php
// Investment plans display
[hyiplab_plans]

// User dashboard
[hyiplab_dashboard]

// Transaction history
[hyiplab_transactions]

// Investment form
[hyiplab_invest_form]
```

### 3. Widget Integration

Available widgets:

- Investment Plans Widget
- User Stats Widget
- Recent Transactions Widget
- Investment Calculator Widget

### 4. API Integration

The theme provides REST API endpoints for HYIPLab:

```php
// Get investment plans
GET /wp-json/hyiplab/v1/plans

// Get user investments
GET /wp-json/hyiplab/v1/investments

// Create new investment
POST /wp-json/hyiplab/v1/investments
```

## Customization

### 1. Styling Customization

Customize HYIPLab appearance through theme CSS:

```css
/* HYIPLab specific styles */
.hyiplab-container {
    background: var(--theme-background);
    border-radius: 8px;
    padding: 20px;
}

.hyiplab-plan-card {
    border: 1px solid var(--theme-border);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.hyiplab-plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
```

### 2. Template Customization

Override HYIPLab templates by copying them to your theme:

```
your-theme/
├── hyiplab/
│   ├── views/
│   │   ├── user/
│   │   │   └── dashboard.php
│   │   └── admin/
│   │       └── plans.php
```

### 3. Functionality Extension

Extend HYIPLab functionality through WordPress hooks:

```php
// Add custom investment validation
add_filter('hyiplab_investment_validation', function($validation, $investment_data) {
    // Your custom validation logic
    return $validation;
}, 10, 2);

// Customize profit calculation
add_filter('hyiplab_profit_calculation', function($profit, $investment) {
    // Your custom profit calculation
    return $profit;
}, 10, 2);
```

## Troubleshooting

### Common Issues

#### 1. Plugin Not Activating
- **Cause**: Missing dependencies
- **Solution**: Run `composer install` in the hyiplab directory

#### 2. Database Tables Not Created
- **Cause**: Insufficient database permissions
- **Solution**: Check database user permissions and run setup manually

#### 3. Theme Integration Not Working
- **Cause**: Plugin not properly activated
- **Solution**: Ensure HYIPLab plugin is active in WordPress admin

#### 4. Payment Gateway Issues
- **Cause**: Incorrect API configuration
- **Solution**: Verify payment gateway settings in HYIPLab admin

### Debug Mode

Enable debug mode for troubleshooting:

```php
// In wp-config.php
define('HYIPLAB_DEBUG', true);
define('HYIPLAB_LOG_LEVEL', 'debug');
```

### Support

For additional support:

1. Check the HYIPLab documentation in `hyiplab/docs/`
2. Review the example files in `hyiplab/examples/`
3. Check the WordPress error logs
4. Contact support with detailed error information

## Performance Optimization

### 1. Caching

Enable caching for better performance:

```php
// Enable HYIPLab caching
define('HYIPLAB_CACHE_ENABLED', true);
define('HYIPLAB_CACHE_DURATION', 3600);
```

### 2. Database Optimization

Regular database maintenance:

```sql
-- Optimize HYIPLab tables
OPTIMIZE TABLE wp_hyiplab_investments;
OPTIMIZE TABLE wp_hyiplab_transactions;
OPTIMIZE TABLE wp_hyiplab_plans;
```

### 3. Asset Optimization

Minify and combine HYIPLab assets:

```php
// In functions.php
function blackcnote_optimize_hyiplab_assets() {
    if (function_exists('hyiplab_system_instance')) {
        // Optimize HYIPLab CSS/JS
        wp_enqueue_style('hyiplab-optimized', get_template_directory_uri() . '/assets/hyiplab-optimized.css');
    }
}
```

## Security Considerations

### 1. User Permissions

Ensure proper user role management:

```php
// Check user capabilities
if (current_user_can('hyiplab_manage_investments')) {
    // Allow investment management
}
```

### 2. Data Validation

Always validate user input:

```php
// Sanitize investment data
$investment_data = sanitize_text_field($_POST['investment_amount']);
$investment_data = intval($investment_data);
```

### 3. API Security

Secure API endpoints:

```php
// Verify nonce for AJAX requests
if (!wp_verify_nonce($_POST['nonce'], 'hyiplab_investment_nonce')) {
    wp_die('Security check failed');
}
```

---

This integration guide provides a comprehensive overview of how HYIPLab works with the BlackCnote theme. For specific implementation details, refer to the individual template files and the HYIPLab documentation. 