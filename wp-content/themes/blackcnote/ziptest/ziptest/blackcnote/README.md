# BlackCnote Theme

A WordPress theme for BlackCnote plugin integration, featuring Bootstrap 5 styling and responsive design.

## Description

BlackCnote is a modern, responsive WordPress theme built specifically for BlackCnote plugin integration. It provides custom templates for investment plans, user dashboard, and transaction history, with a focus on user experience and security.

## Features

- Seamless integration with BlackCnote plugin
- Responsive design using Bootstrap 5
- Custom templates for investment features
- User dashboard with real-time updates
- Transaction history tracking
- Investment plan management
- Secure payment processing
- Mobile-friendly interface
- RTL language support
- Translation ready

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- BlackCnote plugin

## Installation

1. Upload `blackcnote.zip` to WordPress Admin > Appearance > Themes
2. Activate the theme
3. Ensure BlackCnote plugin is installed and activated

## Usage

### Dashboard Setup

- Template: Dashboard
- Add shortcode: `[blackcnote_dashboard]`

### Investment Plans

- Template: Plans
- Add shortcode: `[blackcnote_plans]`

### Transactions

- Template: Transactions
- Add shortcode: `[blackcnote_transactions]`

## Theme Structure

The `blackcnote.zip` contains:

- Core theme files
- Templates: `template-*.php`
- Override: `blackcnote/dashboard.php`
- Assets: CSS, JS, images
- Translation: `languages/blackcnote.pot`

## Customization

### Theme Options

1. Go to Appearance > Customize
2. Configure theme settings
3. Save changes

### Template Overrides

1. Create `blackcnote` folder in your child theme
2. Copy template files to override
3. Modify as needed

### Styling

1. Use child theme for custom CSS
2. Override Bootstrap variables
3. Add custom styles

## Integration

### 1. Plugin Setup

- Install BlackCnote plugin
- Configure plugin settings
- Test integration

### 2. Database Tables

- `wp_blackcnote_plans`
- `wp_blackcnote_transactions`

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