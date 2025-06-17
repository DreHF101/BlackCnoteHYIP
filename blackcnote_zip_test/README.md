# BlackCnote Theme Test

A comprehensive test suite for the BlackCnote WordPress theme, ensuring all functionality works as expected. Optional integration with BlackCnote is supported but not required.

## Overview

The BlackCnote theme is a modern, responsive WordPress theme designed for investment platforms. It features a clean, professional design with no plugin dependencies. Optional integration with BlackCnote is available for advanced features.

## Features

- Responsive design using Bootstrap 5
- Custom templates for investment features
- User dashboard with real-time updates
- Transaction history tracking
- Investment plan management
- Secure payment processing
- Mobile-friendly interface
- RTL language support
- Translation ready
- Optional BlackCnote integration (future-ready)

## Test Structure

The `blackcnote.zip` contains:

- Core theme files
- Templates: `template-*.php`
- Override: `blackcnote/dashboard.php`
- Assets: CSS, JS, images
- Translation: `languages/blackcnote.pot`

## Test Cases

### 1. Theme Installation

- [ ] Theme activates without errors
- [ ] Required files present
- [ ] No PHP errors or warnings
- [ ] Theme options accessible

### 2. Template Testing

- [ ] Dashboard template works
- [ ] Plans template works
- [ ] Transactions template works
- [ ] Custom templates load correctly

### 3. BlackCnote Integration

- Install and activate the BlackCnote plugin
- Configure plugin settings
- Test integration

## Development

### Local Setup

1. Clone repository
2. Install dependencies
3. Run development server

### Testing

```bash
php tests/test-blackcnote.php
```

## Security

This theme is designed for use with the BlackCnote plugin. Investment platforms carry significant risks. Users should:

1. Understand investment risks
2. Verify platform legitimacy
3. Read terms and conditions
4. Check security measures
5. Be aware of local regulations regarding investments

## Support

- Documentation: [Link to docs]
- Support: [Support email]
- GitHub: [Repository link]

## Credits

- Bootstrap 5
- WordPress
- BlackCnote plugin

## License

GPL v2 or later

## Changelog

See [CHANGELOG.md](CHANGELOG.md)

## Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create pull request

## Roadmap

- [ ] Enhanced dashboard features
- [ ] Additional payment gateways
- [ ] Advanced reporting
- [ ] Mobile app integration

## Troubleshooting

### Common Issues

1. Verify BlackCnote plugin is active
2. Check shortcode syntax: `[blackcnote_dashboard]`, `[blackcnote_plans]`, `[blackcnote_transactions]`
3. Clear cache and cookies
4. Check error logs

### Database Tables

- `wp_blackcnote_plans`
- `wp_blackcnote_transactions`

### Template Overrides

- [ ] All required files present in `blackcnote.zip`
- [ ] Template files in correct location
- [ ] Proper file permissions

### 2. BlackCnote Integration

- [ ] Shortcodes working: `[blackcnote_dashboard]`, `[blackcnote_plans]`, `[blackcnote_transactions]`
- [ ] Database tables accessible: `wp_blackcnote_plans`, `wp_blackcnote_transactions`
- [ ] Template override working: `blackcnote/dashboard.php`

## Distribution Checklist

### Theme Files
- [ ] All required files are present
- [ ] File permissions are correct (644 for files, 755 for directories)
- [ ] No development files included
- [ ] No sensitive information in files

### BlackCnote Integration
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
- Integration with BlackCnote plugin
- Custom page templates
- Responsive design
- Security features 

---

**Note:** This theme requires the BlackCnote plugin to function properly. Please ensure the plugin is installed and activated before using the theme.

## Distribution Checklist

Before distributing the theme, ensure all items are completed:

### 1. Theme Files
- [ ] All required files present in `blackcnote.zip`
- [ ] `style.css` has correct theme metadata
- [ ] `functions.php` includes all required functions
- [ ] Template files are properly structured
- [ ] Assets are minified and optimized
- [ ] Translation files are complete
- [ ] Screenshot.png is 1200x900 pixels

### 2. BlackCnote Integration
- [ ] Shortcodes working: `[blackcnote_dashboard]`, `[blackcnote_plans]`, `[blackcnote_transactions]`
- [ ] Database tables accessible: `wp_blackcnote_plans`, `wp_blackcnote_transactions`
- [ ] Template override working: `blackcnote/dashboard.php`
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