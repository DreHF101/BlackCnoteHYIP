# BlackCnote Admin Notice System

## Overview

The BlackCnote Admin Notice System provides persistent, interactive notifications in the WordPress admin when script checker detects issues. This system ensures administrators are immediately aware of critical problems while providing flexible dismissal options.

## Features

### 🚨 **Alert Levels**
- **ERROR**: Critical issues requiring immediate attention (red border)
- **WARNING**: Issues that should be reviewed (yellow border)
- **INFO**: Informational messages (blue border, disabled by default)

### ⏰ **Dismissal Options**
- **24 Hours**: Dismiss alert for 24 hours
- **1 Week**: Dismiss alert for 1 week
- **Standard Dismiss**: Uses WordPress's built-in dismiss functionality

### 🎨 **Visual Design**
- Modern, responsive design with hover effects
- Color-coded borders based on alert level
- Smooth animations and transitions
- Mobile-friendly layout

### 🔧 **Interactive Features**
- AJAX-powered dismiss functionality
- Real-time status updates
- Success/error feedback
- Automatic refresh capabilities

## Implementation

### PHP Integration

The admin notice system is integrated into the `BlackCnote_Debug_Admin` class:

```php
// Display notices on all admin pages
add_action('admin_notices', [$this, 'display_script_checker_notices']);

// Handle dismiss AJAX requests
add_action('wp_ajax_blackcnote_dismiss_script_alert', [$this, 'ajax_dismiss_script_alert']);
```

### JavaScript Functionality

The system uses `admin-notices.js` for interactive features:

```javascript
// Handle dismiss button clicks
$(document).on('click', '.blackcnote-script-alert .dismiss-alert', function(e) {
    // AJAX request to dismiss alert
});

// Handle standard WordPress dismiss
$(document).on('click', '.blackcnote-script-alert .notice-dismiss', function(e) {
    // Track dismissal
});
```

### CSS Styling

Custom styles provide modern appearance:

```css
.blackcnote-script-alert {
    border-left: 4px solid #dc3232;
    transition: all 0.2s ease-in-out;
    animation: slideInDown 0.3s ease-out;
}

.blackcnote-script-alert:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
    transform: translateY(-1px);
}
```

## Configuration

### Alert Settings

Configure alert behavior in `script-checker-config.json`:

```json
{
  "admin_notices": {
    "enabled": true,
    "show_on_all_pages": true,
    "dismiss_options": {
      "24_hours": 24,
      "1_week": 168,
      "1_month": 720
    },
    "alert_levels": {
      "error": {
        "show_notice": true,
        "icon": "dashicons-dismiss",
        "color": "#dc3232"
      }
    }
  }
}
```

### User Preferences

Dismissal preferences are stored per user:

```php
// Store dismissal time
update_user_meta(get_current_user_id(), 'blackcnote_script_alert_dismissed_until', $dismiss_until);

// Check if dismissed
$dismissed_until = get_user_meta(get_current_user_id(), 'blackcnote_script_alert_dismissed_until', true);
```

## Usage

### For Administrators

1. **View Alerts**: Alerts appear automatically on admin pages when issues are detected
2. **Dismiss Alerts**: Click dismiss buttons to hide alerts temporarily
3. **View Details**: Click "View Details" to go to the script checker page
4. **Customize**: Configure dismissal duration (24 hours or 1 week)

### For Developers

1. **Add New Alert Types**: Extend the alert levels in configuration
2. **Customize Styling**: Modify CSS in `get_admin_notice_styles()`
3. **Add Actions**: Extend JavaScript for additional functionality
4. **Integration**: Use the system for other plugin alerts

## Alert Triggers

### Script Checker Integration

Alerts are triggered when the script checker finds issues:

```php
$results = $this->script_checker->getResults();
$status = $results['summary']['OverallStatus'];

if ($status === 'ERROR' || $status === 'WARNING') {
    // Display appropriate alert
}
```

### Custom Triggers

You can trigger alerts programmatically:

```php
// Set user meta to show alert
update_user_meta($user_id, 'blackcnote_show_custom_alert', true);

// Check in display function
if (get_user_meta(get_current_user_id(), 'blackcnote_show_custom_alert', true)) {
    // Display custom alert
}
```

## Security

### AJAX Security

All AJAX requests are secured with nonces:

```php
check_ajax_referer('blackcnote_script_checker_nonce', 'nonce');
```

### Capability Checks

Only users with `manage_options` capability can dismiss alerts:

```php
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}
```

### Data Sanitization

All user input is properly sanitized:

```php
$dismiss_hours = intval($_POST['dismiss_hours'] ?? 24);
```

## Performance

### Efficient Loading

- Styles are loaded inline to avoid additional HTTP requests
- JavaScript is loaded only when needed
- AJAX requests are optimized for minimal overhead

### Caching

- Alert status is cached in user meta
- Configuration is cached in WordPress options
- Results are cached to avoid repeated checks

## Troubleshooting

### Alerts Not Showing

1. **Check Configuration**: Ensure `admin_notices.enabled` is `true`
2. **Check Permissions**: Verify user has `manage_options` capability
3. **Check Dismissal**: Verify alert hasn't been dismissed
4. **Check Script Checker**: Ensure script checker is working properly

### Dismiss Not Working

1. **Check JavaScript**: Verify `admin-notices.js` is loading
2. **Check AJAX**: Verify AJAX URL and nonce are correct
3. **Check Console**: Look for JavaScript errors
4. **Check Network**: Verify AJAX requests are successful

### Styling Issues

1. **Check CSS**: Verify styles are being applied
2. **Check Conflicts**: Look for theme/plugin conflicts
3. **Check Responsive**: Test on mobile devices
4. **Check Browser**: Test in different browsers

## Customization

### Adding New Alert Types

1. **Update Configuration**: Add new alert level to config
2. **Update PHP**: Add logic to display new alert type
3. **Update CSS**: Add styles for new alert type
4. **Update JavaScript**: Add handling for new alert type

### Modifying Dismissal Options

1. **Update Configuration**: Modify dismiss options
2. **Update PHP**: Add new dismissal durations
3. **Update JavaScript**: Add new dismiss buttons
4. **Update UI**: Modify button text and behavior

### Styling Customization

1. **Override CSS**: Add custom styles to theme
2. **Modify PHP**: Update `get_admin_notice_styles()`
3. **Use Filters**: Add WordPress filters for customization
4. **Theme Integration**: Integrate with theme styling

## Best Practices

### Alert Design

- Keep messages concise and actionable
- Use appropriate alert levels
- Provide clear next steps
- Include relevant timestamps

### User Experience

- Don't overwhelm users with too many alerts
- Provide easy dismissal options
- Include helpful action buttons
- Use consistent styling

### Performance

- Minimize AJAX requests
- Cache results appropriately
- Use efficient queries
- Optimize CSS and JavaScript

### Security

- Always verify nonces
- Check user capabilities
- Sanitize all input
- Validate all data

## Future Enhancements

### Planned Features

- **Email Integration**: Send alerts via email
- **Slack Integration**: Send alerts to Slack
- **SMS Alerts**: Send critical alerts via SMS
- **Escalation**: Automatic escalation for critical issues
- **Analytics**: Track alert effectiveness
- **Customization**: User-configurable alert preferences

### API Extensions

- **REST API**: Programmatic alert management
- **Webhook Support**: External system integration
- **Event System**: Custom event triggers
- **Plugin API**: Third-party plugin integration

---

**Last Updated**: June 27, 2025  
**Version**: 1.0.0  
**Compatibility**: WordPress 5.0+, PHP 7.4+ 