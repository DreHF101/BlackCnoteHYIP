# HYIP Theme for WordPress

A professional WordPress theme designed specifically for HYIP (High-Yield Investment Program) websites, featuring seamless integration with the HYIPLab plugin.

## Features

- Seamless integration with HYIPLab plugin
- Custom investment dashboard
- Investment plans management with categories
- Transaction history tracking
- REST API endpoints for plans
- Responsive design with Bootstrap 5
- RTL support
- Translation ready
- Custom post types and taxonomies
- Performance optimized with transients
- Security best practices

### Widgets
- **Featured Plans Widget**: Display featured investment plans in any widget area
  - Customizable title
  - Cached plan data for better performance
  - Responsive Bootstrap 5 card layout

### Analytics
- **Google Analytics Integration**: Track site usage with Google Analytics
  - Support for both Universal Analytics (UA) and Google Analytics 4 (GA4)
  - Easy configuration through WordPress Customizer
  - Privacy-friendly implementation

### Email Notifications
- **Transaction Notifications**: Automatic email notifications for HYIPLab transactions
  - Sent on transaction completion, failure, and pending status
  - Customizable email templates
  - Includes transaction details and dashboard link

## Investment Approval Workflow

The theme includes a robust investment approval system that allows administrators to review and approve/reject new investments. This feature helps maintain control over investment activities and ensures compliance with regulations.

### Features
- Automatic status change to 'pending' for new investments
- Admin notification emails for pending investments
- Approval/rejection actions in the admin dashboard
- User notifications for investment status changes
- Activity logging for all approval actions

### Usage
1. When a user makes a new investment, it is automatically set to 'pending' status
2. Administrators receive an email notification with investment details
3. Admins can approve or reject the investment from the dashboard
4. Users receive email notifications about their investment status
5. All actions are logged in the activity log

## Database Backup System

The theme includes an automated database backup system to ensure data safety and compliance with data protection regulations.

### Features
- Configurable backup frequency (daily, weekly, monthly)
- Automatic backup file creation
- Backup retention management
- Email notifications for backup completion
- Secure backup storage in wp-content directory

### Configuration
1. Enable automatic backups in theme settings
2. Set backup frequency (daily, weekly, monthly)
3. Configure backup retention period (1-365 days)
4. Set notification email address
5. Backups are stored in `wp-content/hyip-backups/`

### Backup Contents
- All WordPress tables
- Custom theme tables
- Transaction data
- User data
- Settings and configurations

### Security
- Backups are stored in a secure directory
- Files are not publicly accessible
- Automatic cleanup of old backups
- Email notifications for monitoring

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- HYIPLab plugin (required)

## Installation

1. Download the `hyip-theme.zip` file
2. Go to WordPress admin panel > Appearance > Themes
3. Click "Add New" and then "Upload Theme"
4. Select the `hyip-theme.zip` file and click "Install Now"
5. Activate the theme

## Automatic Setup

The theme includes an automatic setup feature that creates essential pages and configurations:

1. Home page with welcome message
2. Investment Plans page with shortcode
3. Dashboard page with shortcode
4. Transactions page with shortcode
5. Primary menu with all pages
6. Front page setting

To disable automatic setup:
1. Go to Appearance > HYIP Settings
2. Uncheck "Auto Setup"
3. Click "Save Changes"

## Manual Setup

If you prefer to set up pages manually:

1. Create the following pages:
   - Home
   - Investment Plans
   - Dashboard
   - Transactions

2. Add the following shortcodes to respective pages:
   - `[hyiplab_dashboard]` for Dashboard
   - `[hyiplab_plans]` for Investment Plans
   - `[hyiplab_transactions]` for Transactions

3. Set up the primary menu:
   - Go to Appearance > Menus
   - Create a new menu
   - Add all pages
   - Set as Primary Menu

4. Set the front page:
   - Go to Settings > Reading
   - Set "Your homepage displays" to "A static page"
   - Select "Home" as the homepage

## Plan Categories

The theme includes a custom taxonomy for organizing investment plans:

1. Go to Investment Plans > Categories
2. Add new categories as needed
3. Assign categories to plans
4. Filter plans by category in the admin panel

## REST API

The theme provides REST API endpoints for accessing investment plans:

### Endpoints

- `GET /hyip/v1/plans`
  - Lists all investment plans
  - Parameters:
    - `category` (optional): Filter by plan category
    - `status` (optional): Filter by plan status
  - Authentication: Required (read capability)

- `GET /hyip/v1/plans/{id}`
  - Retrieves a specific plan by ID
  - Parameters:
    - `id` (required): Plan ID
  - Authentication: Required (read capability)

### Example Usage

```php
// Get all plans
$response = wp_remote_get(rest_url('hyip/v1/plans'), [
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode('username:password')
    ]
]);

// Get plans by category
$response = wp_remote_get(rest_url('hyip/v1/plans?category=premium'), [
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode('username:password')
    ]
]);
```

### Security

- All endpoints require authentication
- Data is sanitized and validated
- Sensitive information is restricted to authorized users

## Theme Settings

Access theme settings at Appearance > HYIP Settings:

1. Auto Setup: Enable/disable automatic page creation
2. Custom Logo: Upload or specify custom logo URL
3. API Endpoint: Configure HYIPLab API endpoint
4. Cache Duration: Set investment plans cache duration

## Development

### Testing

1. Install PHPUnit:
   ```bash
   composer require --dev phpunit/phpunit
   ```

2. Run tests:
   ```bash
   ./vendor/bin/phpunit tests/test-hyip-theme.php
   ```

### Theme Validation

Run the validation script to check theme compliance:
```bash
php validate.php
```

### Building Distribution Package

1. Run the packaging script:
   ```powershell
   .\package.ps1
   ```

2. The script will:
   - Create a temporary directory
   - Copy required files
   - Exclude development files
   - Create `hyip-theme.zip`
   - Clean up temporary files

## Legal Notice

**IMPORTANT**: HYIPs (High-Yield Investment Programs) are high-risk investments. This theme is provided for educational purposes only. Users are responsible for complying with all applicable laws and regulations in their jurisdiction.

## Support

For support:
1. Check the [documentation](https://github.com/yourusername/hyip-theme/wiki)
2. Open an [issue](https://github.com/yourusername/hyip-theme/issues)
3. Contact support at support@example.com

## License

This theme is licensed under the GNU General Public License v2 or later. See the [LICENSE.txt](LICENSE.txt) file for details.

## Credits

- [Bootstrap](https://getbootstrap.com/) - Frontend framework
- [WordPress](https://wordpress.org/) - CMS platform
- [HYIPLab](https://hyiplab.com/) - Investment management plugin 