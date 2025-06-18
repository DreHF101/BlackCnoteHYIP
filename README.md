# BlackCnote Theme

A modern WordPress theme with investment functionality. Optional integration with BlackCnoteLab is supported but not required.

## Features

- Modern, responsive design
- Dark mode support
- Investment calculator
- Transaction history
- User dashboard
- WooCommerce integration
- RTL support
- Translation ready
- Security features
- Performance optimized
- SEO friendly
- Accessibility compliant
- No external dependencies. Optional integration with BlackCnoteLab is available for advanced features.

## Requirements

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+
- Modern web browser
- Local development environment
- WooCommerce (optional)

## Installation

1. Upload `blackcnote-theme.zip` to WordPress Admin > Appearance > Themes
2. Activate the theme
3. Configure theme settings
4. Set up required pages
5. Customize as needed

## Page Setup

### Dashboard Page
- Template: BlackCnote Dashboard

### Investment Plans Page
- Template: BlackCnote Plans

### Transactions Page
- Template: BlackCnote Transactions

## Theme Structure

The `blackcnote-theme.zip` contains:

- Templates: `template-blackcnote-*.php`
- Assets: CSS, JS, images
- Translation: `languages/blackcnote-theme.pot`
- Documentation: README.md, CHANGELOG.md
- Tests: `tests/test-blackcnote-theme.php`

## Configuration

### Required Pages

- Dashboard
- Investment Plans
- Transactions

## Usage

### 1. Basic Setup

1. Install and activate the theme
2. Configure theme options
3. Set up required pages
4. Customize appearance
5. Test functionality

### 2. Investment Features

1. Configure investment plans
2. Set up payment gateways
3. Test transactions
4. Monitor performance
5. Review security

### 3. BlackCnoteLab Integration

- Install and activate the BlackCnoteLab plugin
- Configure plugin settings
- Test integration features
- Monitor performance
- Review security

## Development

### Setup

1. Clone the repository
2. Install dependencies
3. Configure development environment
4. Run tests
5. Build assets

### Testing

1. Run unit tests
2. Perform integration tests
3. Test responsive design
4. Verify accessibility
5. Check performance

### Deployment

1. Build production assets
2. Run final tests
3. Create deployment package
4. Deploy to staging
5. Deploy to production

## Troubleshooting

### Common Issues

1. Verify BlackCnoteLab plugin is active
2. Check shortcode syntax: `[blackcnotelab_dashboard]`, `[blackcnotelab_plans]`, `[blackcnotelab_transactions]`
3. Review error logs
4. Check file permissions
5. Verify database tables

### Database Tables

- `wp_blackcnotelab_plans`
- `wp_blackcnotelab_transactions`

### File Structure

```
blackcnote/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── inc/
├── languages/
├── template-parts/
└── templates/
```

## Security

### Best Practices

1. Keep WordPress updated
2. Use strong passwords
3. Enable two-factor authentication
4. Regular security audits
5. Monitor for suspicious activity

### File Permissions

- Directories: 755
- Files: 644
- wp-config.php: 600

## Performance

### Optimization

1. Enable caching
2. Optimize images
3. Minify assets
4. Use CDN
5. Monitor performance

### Testing

1. Run performance tests
2. Check load times
3. Verify caching
4. Test responsiveness
5. Monitor resources

## Support Resources

### Documentation

- [Theme Documentation](https://blackcnote.com/docs)
- [API Reference](https://blackcnote.com/api)
- [FAQ](https://blackcnote.com/faq)
- [Support Forum](https://blackcnote.com/support)
- [GitHub Issues](https://github.com/blackcnote/theme/issues)

### Contact

- Email: support@blackcnote.com
- Twitter: @blackcnote
- GitHub: github.com/blackcnote
- Forum: blackcnote.com/forum
- Discord: discord.gg/blackcnote

## License

This theme is licensed under the GPL v2 or later. See the LICENSE file for details.

**Note:** This theme is designed to function independently without requiring any additional plugins. Enjoy a seamless experience with built-in features and customization options. 