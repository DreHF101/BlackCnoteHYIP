# BlackCnote Theme & HYIPLab Plugin Integration Guide

## Overview

This document provides comprehensive guidance on the integration between the BlackCnote WordPress theme and the HYIPLab investment plugin. The integration enables seamless functionality where the theme's frontend components can display real investment data from the HYIPLab plugin.

## Table of Contents

1. [Integration Features](#integration-features)
2. [Shortcodes](#shortcodes)
3. [Widgets](#widgets)
4. [API Integration](#api-integration)
5. [Styling & Customization](#styling--customization)
6. [Testing & Verification](#testing--verification)
7. [Troubleshooting](#troubleshooting)
8. [Development Guidelines](#development-guidelines)

## Integration Features

### Core Integration Points

- **Automatic Detection**: Theme automatically detects HYIPLab plugin availability
- **Fallback Support**: Graceful degradation when plugin is not active
- **Data Synchronization**: Real-time data from plugin models
- **User Context Awareness**: Different content for logged-in vs anonymous users
- **Responsive Design**: Mobile-friendly integration components

### Supported Data Types

- Investment Plans
- User Statistics
- Transaction History
- Platform Statistics
- User Testimonials (based on investment success)

## Shortcodes

### Available Shortcodes

#### 1. `[blackcnote_invest]`
Displays the main investment interface with calculator and plans.

**Parameters:**
- `limit` (int): Number of plans to display (default: 6)
- `show_calculator` (string): Show investment calculator (default: 'true')
- `show_plans` (string): Show investment plans (default: 'true')

**Example:**
```php
[blackcnote_invest limit="3" show_calculator="true" show_plans="true"]
```

#### 2. `[blackcnote_plan_comparison]`
Displays a comparison of investment plans.

**Parameters:**
- `category` (string): Filter by plan category
- `limit` (int): Number of plans to compare (default: 3)
- `show_features` (string): Show plan features (default: 'true')

**Example:**
```php
[blackcnote_plan_comparison category="premium" limit="4" show_features="true"]
```

#### 3. `[blackcnote_user_stats]`
Displays user-specific investment statistics.

**Parameters:**
- `show_balance` (string): Show current balance (default: 'true')
- `show_investments` (string): Show active investments (default: 'true')
- `show_earnings` (string): Show total earnings (default: 'true')

**Example:**
```php
[blackcnote_user_stats show_balance="true" show_investments="true" show_earnings="true"]
```

#### 4. `[hyiplab_plans]` (Plugin Shortcode)
Original HYIPLab shortcode for displaying plans.

**Parameters:**
- `limit` (int): Number of plans to display
- `ids` (string): Comma-separated plan IDs

**Example:**
```php
[hyiplab_plans limit="6" ids="1,2,3"]
```

### Shortcode Usage Examples

#### Basic Investment Page
```php
[blackcnote_invest]
```

#### Advanced Investment Page with Custom Settings
```php
[blackcnote_invest limit="4" show_calculator="true" show_plans="true"]
[blackcnote_user_stats show_balance="true" show_investments="true"]
```

#### Plan Comparison Section
```php
[blackcnote_plan_comparison limit="3" show_features="true"]
```

## Widgets

### Enhanced Widgets

#### 1. Investment Calculator Widget
**Features:**
- Dual mode: Custom calculator and real plan integration
- Real-time calculations
- Plan selection with validation
- Responsive design

**Configuration:**
- Title customization
- Calculator mode selection
- Plan integration options

#### 2. Investment Statistics Widget
**Features:**
- Global platform statistics
- User-specific statistics (when logged in)
- Recent investment plans
- Real-time data from HYIPLab

**Configuration:**
- Show/hide global stats
- Show/hide user stats
- Show/hide recent plans
- Number of plans to display

#### 3. Testimonial Widget
**Features:**
- HYIPLab user testimonials (based on investment success)
- WordPress testimonials fallback
- Investment statistics display
- Verified investor badges

**Configuration:**
- Testimonial source selection
- Number of testimonials
- HYIPLab vs WordPress priority

### Widget Installation

1. **Go to Appearance > Widgets**
2. **Add widgets to desired areas:**
   - Investment Calculator Widget
   - Investment Statistics Widget
   - Testimonial Widget

3. **Configure widget settings:**
   - Set titles
   - Choose display options
   - Configure integration preferences

## API Integration

### Data Access Methods

#### 1. Direct Model Access
```php
// Check if HYIPLab is active
if (class_exists('Hyiplab\Lib\VerifiedPlugin')) {
    // Access HYIPLab models
    $plans = \Hyiplab\Models\Plan::where('status', 1)->get();
    $investments = \Hyiplab\Models\Invest::where('user_id', $user_id)->get();
}
```

#### 2. Helper Functions
```php
// Get user balance
$balance = hyiplab_balance($user_id, 'interest_wallet');

// Format amounts
$formatted_amount = hyiplab_show_amount($amount);

// Get plan details
$plan = get_hyiplab_plan($plan_id);
```

#### 3. Service Layer Integration
```php
// Use theme services for data access
$investment_service = new BlackCnote_Investment_Service();
$user_stats = $investment_service->get_user_statistics($user_id);
```

### Error Handling

```php
try {
    $plans = \Hyiplab\Models\Plan::where('status', 1)->get();
} catch (Exception $e) {
    // Handle errors gracefully
    $plans = [];
    error_log('HYIPLab integration error: ' . $e->getMessage());
}
```

## Styling & Customization

### CSS Variables

The theme uses CSS custom properties for consistent styling:

```css
:root {
    --primary-color: #667eea;
    --primary-dark: #5a6fd8;
    --success: #28a745;
    --warning: #ffc107;
    --danger: #dc3545;
    --text-primary: #2d3748;
    --text-secondary: #718096;
    --border-color: #e2e8f0;
    --background-light: #f8fafc;
}
```

### Custom Styling

#### 1. Override Default Styles
```css
/* Custom investment widget styling */
.blackcnote-invest-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
}

/* Custom plan card styling */
.plan-card {
    border: 2px solid var(--primary-color);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}
```

#### 2. Responsive Design
```css
@media (max-width: 768px) {
    .plan-comparison-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
```

### Theme Customization

#### 1. Custom Functions
```php
// Add to functions.php
function custom_investment_styles() {
    wp_add_inline_style('blackcnote-style', '
        .custom-investment-style {
            background: #f0f4ff;
            border-radius: 15px;
        }
    ');
}
add_action('wp_enqueue_scripts', 'custom_investment_styles');
```

#### 2. Custom Shortcode Attributes
```php
// Extend shortcode functionality
add_filter('shortcode_atts_blackcnote_invest', function($atts) {
    $atts['custom_theme'] = 'dark';
    return $atts;
});
```

## Testing & Verification

### Integration Testing

#### 1. Manual Testing Checklist

- [ ] HYIPLab plugin is active
- [ ] Shortcodes render correctly
- [ ] Widgets display data
- [ ] User authentication works
- [ ] Responsive design functions
- [ ] Error handling works
- [ ] Performance is acceptable

#### 2. Automated Testing

Run the integration test:
```php
// Add ?test_shortcodes=1 to any page URL
// Must be logged in as administrator
```

#### 3. Test Scenarios

**Scenario 1: HYIPLab Active**
- All shortcodes work
- Widgets show real data
- User stats display correctly

**Scenario 2: HYIPLab Inactive**
- Fallback content displays
- No errors occur
- Graceful degradation

**Scenario 3: User Not Logged In**
- Login prompts display
- Public content shows
- No sensitive data exposed

### Performance Testing

#### 1. Database Queries
- Monitor query count
- Check for N+1 queries
- Optimize with caching

#### 2. Page Load Times
- Test with various data volumes
- Monitor memory usage
- Check for bottlenecks

## Troubleshooting

### Common Issues

#### 1. Shortcodes Not Working
**Symptoms:** Shortcodes display as text instead of rendered content

**Solutions:**
- Check if shortcodes are registered
- Verify HYIPLab plugin is active
- Check for JavaScript errors
- Review theme functions.php

#### 2. Widgets Not Displaying Data
**Symptoms:** Widgets show empty or fallback content

**Solutions:**
- Verify HYIPLab models exist
- Check database connectivity
- Review error logs
- Test with sample data

#### 3. Styling Issues
**Symptoms:** Components look broken or unstyled

**Solutions:**
- Check CSS file loading
- Verify CSS variables
- Test responsive breakpoints
- Clear cache

#### 4. Performance Problems
**Symptoms:** Slow page loading or timeouts

**Solutions:**
- Enable caching
- Optimize database queries
- Reduce widget count
- Use lazy loading

### Debug Mode

Enable debug mode for detailed error information:

```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Error Logging

Check error logs for integration issues:

```php
// Custom error logging
function log_integration_error($message, $context = []) {
    error_log(sprintf(
        '[BlackCnote Integration] %s - Context: %s',
        $message,
        json_encode($context)
    ));
}
```

## Development Guidelines

### Code Standards

#### 1. PHP Standards
- Follow PSR-12 coding standards
- Use proper namespacing
- Implement error handling
- Add documentation blocks

#### 2. JavaScript Standards
- Use ES6+ features
- Implement proper error handling
- Follow accessibility guidelines
- Optimize for performance

#### 3. CSS Standards
- Use CSS custom properties
- Implement responsive design
- Follow BEM methodology
- Optimize for performance

### Security Considerations

#### 1. Data Sanitization
```php
// Always sanitize output
echo esc_html($user_data);
echo esc_attr($attribute_value);
wp_kses_post($html_content);
```

#### 2. User Permissions
```php
// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}
```

#### 3. Nonce Verification
```php
// Verify nonces for forms
if (!wp_verify_nonce($_POST['_wpnonce'], 'action_name')) {
    wp_die('Security check failed');
}
```

### Performance Optimization

#### 1. Caching
```php
// Implement caching for expensive operations
$cached_data = wp_cache_get('investment_stats');
if (false === $cached_data) {
    $cached_data = expensive_calculation();
    wp_cache_set('investment_stats', $cached_data, '', 3600);
}
```

#### 2. Database Optimization
```php
// Use efficient queries
$plans = \Hyiplab\Models\Plan::select(['id', 'name', 'return_rate'])
    ->where('status', 1)
    ->limit(10)
    ->get();
```

#### 3. Asset Optimization
```php
// Minimize and combine assets
wp_enqueue_script('blackcnote-integration', 
    get_template_directory_uri() . '/assets/js/integration.min.js',
    ['jquery'],
    '1.0.0',
    true
);
```

### Extension Development

#### 1. Creating Custom Shortcodes
```php
function custom_investment_shortcode($atts) {
    $atts = shortcode_atts([
        'type' => 'plans',
        'limit' => 5
    ], $atts);
    
    // Implementation
    return $output;
}
add_shortcode('custom_investment', 'custom_investment_shortcode');
```

#### 2. Creating Custom Widgets
```php
class Custom_Investment_Widget extends WP_Widget {
    // Widget implementation
}
add_action('widgets_init', function() {
    register_widget('Custom_Investment_Widget');
});
```

#### 3. Hooks and Filters
```php
// Add custom hooks
do_action('blackcnote_before_investment_display', $context);

// Add custom filters
$modified_data = apply_filters('blackcnote_investment_data', $data, $context);
```

## Conclusion

The BlackCnote theme and HYIPLab plugin integration provides a powerful, flexible, and user-friendly investment platform. By following this guide, developers can effectively implement, customize, and extend the integration to meet specific requirements.

For additional support or questions, refer to the theme documentation or contact the development team.

---

**Version:** 1.0.0  
**Last Updated:** December 2024  
**Compatibility:** WordPress 5.0+, HYIPLab Plugin 1.0+ 