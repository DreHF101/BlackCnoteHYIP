# BlackCnote Theme Activation System

## Overview

The BlackCnote Theme Activation System is a comprehensive WordPress theme setup that automatically activates all BlackCnote components, features, widgets, and functionality when the theme is activated. This system ensures that all investment platform features are immediately available without manual configuration.

## Activation Trigger

The system is triggered when the BlackCnote theme is activated using the `after_switch_theme` WordPress hook:

```php
add_action('after_switch_theme', 'blackcnote_theme_activation');
```

## Components Activated

### 1. Essential Pages
The system automatically creates the following pages:

- **Home** (`/`) - Main landing page
- **About Us** (`/about/`) - Company information
- **Investment Services** (`/services/`) - Service offerings
- **Contact Us** (`/contact/`) - Contact information
- **Privacy Policy** (`/privacy-policy/`) - Legal compliance
- **Terms of Service** (`/terms-of-service/`) - Legal compliance
- **Investment Dashboard** (`/investment-dashboard/`) - User dashboard
- **Investment Plans** (`/investment-plans/`) - Available plans

### 2. Custom Post Types
Two custom post types are registered:

#### Investment Plans (`investment_plans`)
- **Purpose**: Manage investment opportunities and plans
- **Features**: Title, content, featured images, excerpts
- **URL Structure**: `/investment-plans/`
- **Admin Icon**: Chart line icon

#### Market News (`market_news`)
- **Purpose**: Publish market updates and news
- **Features**: Title, content, featured images, excerpts
- **URL Structure**: `/market-news/`
- **Admin Icon**: Megaphone icon

### 3. Custom Taxonomies
Two custom taxonomies are created:

#### Investment Categories (`investment_category`)
- **Hierarchical**: Yes
- **Associated Post Type**: `investment_plans`
- **URL Structure**: `/investment-category/`

#### Market Categories (`market_category`)
- **Hierarchical**: Yes
- **Associated Post Type**: `market_news`
- **URL Structure**: `/market-category/`

### 4. Custom Widgets
Four custom widgets are automatically registered and added to the sidebar:

#### Investment Statistics Widget
- **Purpose**: Display investment metrics
- **Data**: Total investments, portfolio value, active plans, success rate
- **Location**: Sidebar

#### Recent Investments Widget
- **Purpose**: Show recent investment activities
- **Data**: Latest 5 investment plans
- **Location**: Sidebar

#### Market News Widget
- **Purpose**: Display latest market updates
- **Data**: Latest 3 market news posts
- **Location**: Sidebar

#### Quick Links Widget
- **Purpose**: Provide navigation shortcuts
- **Links**: Dashboard, Investment Plans, Market News, Contact, Account Settings
- **Location**: Sidebar

### 5. Navigation Menus
Two navigation menus are created:

#### Primary Navigation Menu
- **Location**: Main site navigation
- **Items**: Home, About Us, Investment Services, Investment Plans, Dashboard, Contact Us

#### Footer Menu
- **Location**: Footer navigation
- **Items**: Privacy Policy, Terms of Service, Support, FAQ

### 6. Widget Areas
Five widget areas are registered:

- **Sidebar** (`sidebar-1`) - Main sidebar
- **Footer Widget Area 1** (`footer-1`) - First footer column
- **Footer Widget Area 2** (`footer-2`) - Second footer column
- **Footer Widget Area 3** (`footer-3`) - Third footer column
- **Investment Dashboard Sidebar** (`dashboard-sidebar`) - Dashboard sidebar

### 7. Content Categories
Five default post categories are created:

- **Investment News** - Latest investment market news and updates
- **Trading Tips** - Professional trading advice and strategies
- **Market Analysis** - In-depth market analysis and reports
- **Investment Opportunities** - Featured investment opportunities
- **Company Updates** - BlackCnote company news and updates

### 8. Sample Content
Three sample posts are created:

1. **Welcome to BlackCnote Investment Platform** (Company Updates)
2. **Getting Started with Investment** (Trading Tips)
3. **Market Trends and Analysis** (Market Analysis)

### 9. Required Plugins
The system automatically activates these plugins:

- **BlackCnote Debug System** (`blackcnote-debug-system/blackcnote-debug-system.php`)
- **HyipLab Investment Platform** (`hyiplab/hyiplab.php`)

### 10. Theme Options
Default theme options are set:

```php
$default_options = array(
    'blackcnote_theme_color' => '#1a1a1a',
    'blackcnote_accent_color' => '#007cba',
    'blackcnote_logo_url' => get_template_directory_uri() . '/images/blackcnote-logo.png',
    'blackcnote_footer_text' => '© ' . date('Y') . ' BlackCnote. All rights reserved.',
    'blackcnote_analytics_code' => '',
    'blackcnote_investment_enabled' => true,
    'blackcnote_dashboard_enabled' => true,
    'blackcnote_market_data_enabled' => true,
    'blackcnote_notifications_enabled' => true,
    'blackcnote_security_level' => 'high',
    'blackcnote_auto_backup' => true,
    'blackcnote_debug_mode' => false
);
```

### 11. WordPress Settings
The system configures WordPress settings:

- **Permalinks**: `/%postname%/`
- **Image Sizes**: Custom thumbnail, medium, and large sizes
- **Comments**: Enabled on pages
- **Timezone**: America/Chicago
- **Posts per page**: 10
- **RSS posts**: 10

## File Structure

```
blackcnote/wp-content/themes/blackcnote/
├── functions.php (Main activation system)
├── inc/
│   ├── widgets.php (Custom widgets)
│   ├── template-functions.php
│   ├── template-tags.php
│   ├── customizer.php
│   └── jetpack.php
├── admin/
│   └── admin-functions.php (Admin interface)
├── template-parts/
├── js/
└── style.css
```

## Activation Process

1. **Theme Activation Triggered**
   - User activates BlackCnote theme in WordPress admin

2. **Activation Flag Set**
   - `blackcnote_theme_activated` option set to `true`
   - `blackcnote_activation_date` recorded

3. **Pages Created**
   - Essential pages created with proper templates
   - Home page set as front page

4. **Widgets Setup**
   - Custom widgets registered
   - Default widgets added to sidebar

5. **Plugins Activated**
   - Required plugins automatically activated

6. **Options Configured**
   - Theme options set to defaults
   - WordPress settings optimized

7. **Menus Created**
   - Navigation menus created and assigned
   - Menu items added

8. **Content Generated**
   - Categories created
   - Sample posts published

9. **Custom Post Types**
   - Investment plans and market news post types registered

10. **Taxonomies Created**
    - Investment and market categories registered

11. **Rewrite Rules Flushed**
    - Permalinks updated

12. **Logging**
    - Activation logged to error log

## Manual Activation

If you need to manually trigger the activation system:

```php
// Run this in WordPress admin or via WP-CLI
blackcnote_theme_activation();
```

## Customization

### Adding New Components

To add new components to the activation system:

1. **Add to `blackcnote_theme_activation()` function**
2. **Create corresponding setup function**
3. **Update this documentation**

### Modifying Default Content

Edit the arrays in these functions:
- `blackcnote_create_default_pages()` - Modify pages
- `blackcnote_setup_default_content()` - Modify categories and posts
- `blackcnote_setup_default_options()` - Modify theme options

### Adding New Widgets

1. **Create widget class in `inc/widgets.php`**
2. **Register widget in `blackcnote_register_widgets()`**
3. **Add to `blackcnote_setup_default_widgets()`**

## Troubleshooting

### Common Issues

1. **Widgets Not Appearing**
   - Check if `inc/widgets.php` is included
   - Verify widget registration in `widgets_init` hook

2. **Pages Not Created**
   - Check user permissions
   - Verify page creation function

3. **Plugins Not Activated**
   - Ensure plugin files exist
   - Check plugin paths

4. **Custom Post Types Not Working**
   - Flush rewrite rules
   - Check post type registration

### Debug Mode

Enable debug mode to see activation process:

```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check the debug log for activation messages.

## Security Considerations

- All user inputs are sanitized
- Nonces are used for form submissions
- File permissions are properly set
- Direct access to files is prevented

## Performance Optimization

- Widgets are loaded only when needed
- CSS is minified and cached
- Database queries are optimized
- Image sizes are pre-configured

## Future Enhancements

Planned improvements:
- Additional widget types
- More custom post types
- Enhanced admin interface
- Advanced customization options
- Integration with external APIs

## Support

For issues with the BlackCnote Theme Activation System:
1. Check the WordPress debug log
2. Verify all required files are present
3. Ensure proper file permissions
4. Contact the development team

---

**Last Updated**: June 26, 2025
**Version**: 1.0.0
**Compatibility**: WordPress 6.8+, PHP 8.1+ 