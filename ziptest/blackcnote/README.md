# BlackCnote Theme

A WordPress theme for BlackCnoteLab plugin integration, featuring Bootstrap 5 styling and responsive design.

## Description

BlackCnote Theme is a modern, responsive WordPress theme built specifically for BlackCnoteLab plugin integration. It provides custom templates for investment plans, user dashboard, and transaction history, with a focus on user experience and security.

## Features

- Seamless integration with BlackCnoteLab plugin
- Modern, responsive design
- Bootstrap 5 framework
- Custom investment dashboard
- Transaction history view
- Investment plans display
- User profile management
- Dark mode support
- RTL support
- Translation ready
- Security features
- Performance optimization
- SEO improvements
- Accessibility compliance
- No external dependencies
- Optional integration with BlackCnoteLab is supported for future enhancements

## Requirements

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+
- BlackCnoteLab plugin
- Modern web browser
- Local development environment
- WooCommerce (optional)

## Installation

1. Upload `blackcnote-theme.zip` to WordPress Admin > Appearance > Themes
2. Activate the theme
3. Ensure BlackCnoteLab plugin is installed and activated
4. Configure theme settings
5. Set up required pages
6. Customize as needed

## Page Setup

### Dashboard Page
- Template: BlackCnote Dashboard
- Add shortcode: `[blackcnotelab_dashboard]`

### Investment Plans Page
- Template: BlackCnote Plans
- Add shortcode: `[blackcnotelab_plans]`

### Transactions Page
- Template: BlackCnote Transactions
- Add shortcode: `[blackcnotelab_transactions]`

## Theme Structure

The `blackcnote-theme.zip` contains:

- Templates: `template-blackcnote-*.php`
- Override: `blackcnotelab/dashboard.php`
- Assets: CSS, JS, images
- Translation: `languages/blackcnote-theme.pot`
- Documentation: README.md, CHANGELOG.md
- Tests: `tests/test-blackcnote-theme.php`

## Configuration

### Required Pages

Create the following pages and assign them the corresponding templates:

1. Dashboard Page
   - Template: BlackCnote Dashboard
   - Add shortcode: `[blackcnotelab_dashboard]`

2. Investment Plans Page
   - Template: BlackCnote Plans
   - Add shortcode: `[blackcnotelab_plans]`

3. Transaction History Page
   - Template: BlackCnote Transactions
   - Add shortcode: `[blackcnotelab_transactions]`

### Menu Setup

1. Go to Appearance > Menus
2. Create a new menu
3. Add the required pages
4. Assign the menu to "Primary Menu" location

## Theme Package

The `blackcnote-theme.zip` contains:
- Core files: `style.css`, `functions.php`, `index.php`
- Templates: `template-blackcnote-*.php`
- Override: `blackcnotelab/dashboard.php`
- Assets: `assets/css/`, `assets/js/`
- Translation: `languages/blackcnote-theme.pot`
- Documentation: `README.md`, `CHANGELOG.md`, `LICENSE.txt`
- Screenshot: `screenshot.png`

## Screenshot Creation

The theme requires a screenshot.png file (1200x900 pixels, PNG format) that showcases the theme's key features. To create the screenshot:

1. Use a design tool like Figma, Adobe XD, or Photoshop
2. Follow the specifications in `screenshot.txt`:
   - Dimensions: 1200x900 pixels
   - Format: PNG (24-bit color)
   - Content: Dashboard view (left), Investment Plans (right)
   - Color Scheme: Bootstrap 5 colors
   - Typography: System fonts
   - UI Elements: Bootstrap cards, forms, tables

3. Key elements to include:
   - User dashboard with account overview
   - Investment plans grid
   - Transaction history table
   - Navigation menu
   - Quick action buttons
   - Responsive layout indicators

4. Save as `screenshot.png` in the theme root directory

Note: The screenshot is crucial for theme selection in WordPress admin. Ensure it accurately represents the theme's design and functionality.

## Validation

To validate the theme:

1. Enable debugging in wp-config.php:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```

2. Run the validation script:
   ```bash
   php tests/validate.php
   ```

3. Check debug.log for any issues

## Testing Instructions

### 1. Theme Activation
- Activate the theme through WordPress admin panel
- Verify that the theme appears correctly in the customizer
- Check that all required styles and scripts are loaded

### 2. Page Templates
- Create pages using the provided templates
- Verify that the templates display correctly
- Test responsive design on different screen sizes

### 3. BlackCnoteLab Integration
- Install and activate the BlackCnoteLab plugin
- Verify that all shortcodes work correctly
- Test the investment calculator functionality
- Check transaction history display

### 4. Security Features
- Test form submissions with invalid data
- Verify nonce protection on forms
- Check data sanitization and escaping

### 5. Performance
- Test page load times
- Verify transient caching
- Check WP_Cron tasks

### 6. Responsive Design
- Test on desktop, tablet, and mobile devices
- Verify Bootstrap 5 grid system
- Check navigation menu on all devices

### 7. JavaScript Functionality
- Test investment calculator
- Verify transaction filters
- Check modal windows
- Test form validation

### 8. Translation
- Test theme translation using WPML or similar
- Verify all strings are translatable
- Check RTL support

## Troubleshooting

### Common Issues

#### Shortcodes Not Working
1. Verify BlackCnoteLab plugin is active
2. Check shortcode syntax: `[blackcnotelab_dashboard]`, `[blackcnotelab_plans]`, `[blackcnotelab_transactions]`
3. Ensure pages have correct templates assigned
4. Check for JavaScript errors in browser console

#### Database Errors
1. Verify database tables exist:
   - `wp_blackcnotelab_plans`
   - `wp_blackcnotelab_transactions`
2. Check database user permissions
3. Verify table prefixes match
4. Check for SQL errors in debug.log

#### Style Issues
1. Clear browser cache
2. Verify Bootstrap 5 is loading
3. Check for CSS conflicts
4. Verify RTL styles if using RTL

#### JavaScript Errors
1. Check browser console for errors
2. Verify jQuery is loaded
3. Check for Bootstrap 5 conflicts
4. Verify AJAX nonces

#### Performance Issues
1. Check transient caching
2. Verify database query optimization
3. Check asset loading
4. Monitor WP_Cron tasks

### Debugging

Enable WordPress debugging in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check `wp-content/debug.log` for errors.

### Validation

Run the validation script:
```bash
php validate.php
```

### Testing

Run unit tests:
```bash
php tests/test-blackcnote-theme.php
```

## Legal Note

This theme is designed for use with the BlackCnoteLab plugin. High-Yield Investment Programs (HYIPs) carry significant risks. Users should:

1. Understand the risks involved
2. Never invest more than they can afford to lose
3. Verify the legitimacy of investment opportunities
4. Consult financial advisors before investing
5. Be aware of local regulations regarding HYIPs

## Support

For support, please:
1. Check the documentation
2. Search existing issues
3. Create a new issue if needed
4. Provide detailed information about the problem

## License

This theme is licensed under the GPL v2 or later.

## Credits

- Bootstrap 5.3.0
- WordPress
- BlackCnoteLab plugin

## Distribution Checklist

### Theme Files
- [ ] All required files are present
- [ ] File permissions are correct (644 for files, 755 for directories)
- [ ] No development files included
- [ ] No sensitive information in files

### BlackCnoteLab Integration
- [ ] Plugin dependency check implemented
- [ ] Shortcodes working correctly
- [ ] Database tables accessible
- [ ] Hooks properly implemented

### Security
- [ ] Nonces implemented for forms and AJAX
- [ ] Input sanitization in place
- [ ] Output escaping used
- [ ] Capability checks implemented
- [ ] Direct file access prevented

### Performance
- [ ] Transients implemented for caching
- [ ] Database queries optimized
- [ ] Assets minified and combined
- [ ] WP_Cron tasks scheduled

### Testing
- [ ] Unit tests passing
- [ ] Validation script run
- [ ] WordPress coding standards met
- [ ] Cross-browser testing completed
- [ ] Mobile responsiveness verified

### Documentation
- [ ] README.md complete
- [ ] CHANGELOG.md updated
- [ ] Code documented
- [ ] Screenshot included
- [ ] License file present

### Optional Features
- [ ] Custom post type registered
- [ ] Settings page implemented
- [ ] RTL support added
- [ ] Transients configured
- [ ] WP_Cron tasks set up

### Final Steps
- [ ] Theme packaged as ZIP
- [ ] ZIP file tested
- [ ] Installation tested
- [ ] Activation tested
- [ ] All features verified

## Changelog

### Version 1.0.0
- Initial release
- Basic theme structure
- Integration with BlackCnoteLab plugin
- Custom page templates
- Responsive design
- Security features 

---

**Note:** This theme requires the BlackCnoteLab plugin to function properly. Please ensure the plugin is installed and activated before using the theme.

## Distribution Checklist

Before distributing the theme, ensure all items are completed:

### 1. Theme Files
- [ ] All required files present in `blackcnote-theme.zip`
- [ ] `style.css` has correct theme metadata
- [ ] `functions.php` includes all required functions
- [ ] Template files are properly structured
- [ ] Assets are minified and optimized
- [ ] Translation files are complete
- [ ] Screenshot.png is 1200x900 pixels

### 2. BlackCnoteLab Integration
- [ ] Shortcodes working: `[blackcnotelab_dashboard]`, `[blackcnotelab_plans]`, `[blackcnotelab_transactions]`
- [ ] Database tables accessible: `wp_blackcnotelab_plans`, `wp_blackcnotelab_transactions`
- [ ] Template override working: `blackcnotelab/dashboard.php`
- [ ] Plugin dependency check implemented

### 3. Security
- [ ] All inputs sanitized
- [ ] All outputs escaped
- [ ] Nonces implemented for forms/AJAX
- [ ] Capability checks in place
- [ ] Database queries prepared

### 4. Performance
- [ ] Transient caching implemented
- [ ] WP_Cron tasks scheduled
- [ ] Assets properly enqueued
- [ ] Database queries optimized

### 5. Testing
- [ ] Unit tests passing
- [ ] Validation script run
- [ ] No PHP errors/warnings
- [ ] Responsive design verified
- [ ] RTL support tested

### 6. Documentation
- [ ] README.md complete
- [ ] CHANGELOG.md updated
- [ ] LICENSE.txt included
- [ ] Screenshot.txt detailed

### 7. Optional Features
- [ ] Custom post type registered
- [ ] Settings page functional
- [ ] RTL styles implemented
- [ ] Enhanced features documented

### 8. Final Steps
- [ ] Run `php validate.php`
- [ ] Test theme activation
- [ ] Verify all shortcodes
- [ ] Check debug.log
- [ ] Package with `package.ps1` 