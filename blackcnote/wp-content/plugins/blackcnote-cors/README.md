# BlackCnote CORS Handler Plugin

## Overview

The BlackCnote CORS Handler is a comprehensive WordPress plugin designed specifically for handling Cross-Origin Resource Sharing (CORS) in WordPress/React integration scenarios. It's optimized for the BlackCnote live editing theme and provides advanced security, performance, and compatibility features.

## Features

### 🔒 Security
- **Origin Validation**: Validates incoming requests against allowed origins
- **Security Headers**: Implements comprehensive security headers (CSP, XSS Protection, etc.)
- **Nonce Support**: Full WordPress nonce integration for authenticated requests
- **Input Sanitization**: Proper sanitization of all inputs and settings

### ⚡ Performance
- **Early Hook Execution**: Runs at priority 1 to ensure headers are set early
- **Cache Optimization**: Proper cache control headers for API responses
- **Minimal Overhead**: Optimized code with minimal performance impact
- **Memory Efficient**: Efficient memory usage and cleanup

### 🔧 Compatibility
- **WordPress 5.0+**: Full compatibility with modern WordPress versions
- **React Integration**: Optimized for React app integration
- **Live Editing**: Supports BlackCnote live editing features
- **Plugin Compatibility**: Designed to work alongside other plugins

### 🛠️ Development Features
- **Debug Logging**: Comprehensive logging for development and troubleshooting
- **Admin Interface**: User-friendly admin panel for configuration
- **CORS Testing**: Built-in CORS connection testing tool
- **Configuration Management**: Easy settings management

## Installation

1. **Automatic Installation** (Recommended)
   - The plugin is automatically included with the BlackCnote theme
   - No manual installation required

2. **Manual Installation**
   - Upload the `blackcnote-cors` folder to `/wp-content/plugins/`
   - Activate the plugin through the WordPress admin panel

## Configuration

### Default Settings

The plugin comes pre-configured with the following default settings:

```php
// Allowed Origins (Development)
http://localhost:5174  // React dev server
http://localhost:3000  // Browsersync
http://localhost:8888  // WordPress
http://127.0.0.1:5174
http://127.0.0.1:3000
http://127.0.0.1:8888

// Allowed Methods
GET, POST, PUT, DELETE, OPTIONS, PATCH

// Allowed Headers
Content-Type, Authorization, X-WP-Nonce, X-Requested-With, Accept, Origin, Cache-Control, X-File-Name, X-HTTP-Method-Override
```

### Admin Configuration

1. Go to **Settings > BlackCnote CORS** in WordPress admin
2. Configure allowed origins (one per line)
3. Enable/disable debug mode
4. Test CORS connection
5. Save settings

### Programmatic Configuration

You can modify settings programmatically using WordPress filters:

```php
// Add custom origins
add_filter('blackcnote_cors_allowed_origins', function($origins) {
    $origins[] = 'https://yourdomain.com';
    return $origins;
});
```

## API Integration

### React App Usage

The plugin is designed to work seamlessly with React apps. Your React app should:

1. **Use WordPress Injected Settings**:
```javascript
// The plugin ensures these are available
const apiUrl = window.blackCnoteApiSettings?.apiUrl;
const nonce = window.blackCnoteApiSettings?.nonce;
```

2. **Include Proper Headers**:
```javascript
const response = await fetch('/wp-json/blackcnote/v1/endpoint', {
    headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': nonce,
        'X-Requested-With': 'XMLHttpRequest'
    }
});
```

### REST API Endpoints

The plugin supports all standard WordPress REST API endpoints and custom BlackCnote endpoints:

- `/wp-json/blackcnote/v1/*` - BlackCnote custom endpoints
- `/wp-json/wp/v2/*` - WordPress core endpoints
- `/wp-admin/admin-ajax.php` - WordPress AJAX endpoints

## Security Considerations

### Production Deployment

For production environments:

1. **Restrict Origins**: Remove wildcard origins and specify exact domains
2. **Enable HTTPS**: Ensure all origins use HTTPS
3. **Review CSP**: Adjust Content Security Policy as needed
4. **Monitor Logs**: Enable debug logging for security monitoring

### Security Headers

The plugin automatically sets the following security headers:

- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Content-Security-Policy`: Custom policy for React integration

## Troubleshooting

### Common Issues

1. **CORS Errors in Browser Console**
   - Check that the plugin is activated
   - Verify allowed origins include your React app URL
   - Test CORS connection in admin panel

2. **Headers Already Sent Errors**
   - Ensure no output before headers
   - Check for whitespace in PHP files
   - Verify plugin loads early (priority 1)

3. **API Requests Failing**
   - Check WordPress REST API is enabled
   - Verify nonce is being sent correctly
   - Check server error logs

### Debug Mode

Enable debug mode in the admin panel to log CORS requests:

```php
// Debug logs will appear in WordPress debug log
error_log("BlackCnote CORS: Request from origin: http://localhost:5174");
```

### Testing

Use the built-in CORS test tool in the admin panel to verify:

- CORS headers are being set correctly
- API endpoints are accessible
- Preflight requests are handled properly

## Performance Optimization

### Caching

The plugin implements proper cache control:

- API responses: `no-cache, must-revalidate, max-age=0`
- Preflight responses: `max-age=86400` (24 hours)

### Memory Management

- Efficient memory usage
- Proper cleanup on deactivation
- No memory leaks

## Compatibility Matrix

| Component | Version | Status |
|-----------|---------|--------|
| WordPress | 5.0+ | ✅ Full |
| PHP | 7.4+ | ✅ Full |
| React | 16.8+ | ✅ Full |
| BlackCnote Theme | 1.0+ | ✅ Full |
| HyipLab Plugin | 3.0+ | ✅ Full |

## Changelog

### Version 1.0.1
- Enhanced security headers
- Improved admin interface
- Better error handling
- Performance optimizations

### Version 1.0.0
- Initial release
- Basic CORS handling
- Admin configuration
- Security features

## Support

For support and questions:

1. Check the troubleshooting section above
2. Review WordPress debug logs
3. Test CORS connection in admin panel
4. Contact BlackCnote support

## License

This plugin is licensed under the GPL v2 or later.

## Contributing

Contributions are welcome! Please ensure:

1. Code follows WordPress coding standards
2. All features are properly tested
3. Security considerations are addressed
4. Documentation is updated

---

**Note**: This plugin is specifically designed for the BlackCnote theme and WordPress/React integration. For other use cases, consider using a more general CORS plugin. 