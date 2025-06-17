# BlackCnote Theme Troubleshooting Guide

## Common Issues and Solutions

### 1. Theme Installation Issues

#### Theme Not Appearing in WordPress Admin
- Verify the theme folder is named `blackcnote-theme`
- Check if the theme is in the correct directory: `/wp-content/themes/`
- Ensure all required files are present
- Check file permissions (644 for files, 755 for directories)
- Clear browser cache

#### Installation Fails
- Check PHP version (requires 7.4+)
- Verify WordPress version (requires 5.0+)
- Check server memory limit
- Ensure proper file permissions

### 2. Display Issues

#### Missing Styles
- Verify Bootstrap is loading
- Check custom CSS file exists
- Clear browser cache
- Check for CSS conflicts with plugins

#### Layout Problems
- Verify viewport meta tag
- Check responsive breakpoints
- Test on different devices
- Clear browser cache

### 3. Functionality Issues

#### Menus Not Working
- Verify menu location is assigned
- Check menu items are added
- Clear browser cache
- Check for JavaScript conflicts

#### Widgets Not Showing
- Verify sidebar is active
- Check widget area registration
- Clear browser cache
- Check for plugin conflicts

### 4. Performance Issues

#### Slow Loading
- Optimize images
- Minify CSS/JS
- Enable caching
- Check server response time

#### High Resource Usage
- Check PHP memory limit
- Optimize database
- Review active plugins
- Monitor server resources

## Debug Mode

To enable WordPress debug mode:
1. Edit wp-config.php
2. Add these lines:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## More Information
See README.md for full documentation and legal notes. 