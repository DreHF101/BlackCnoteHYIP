# BlackCnote Startup Monitor Integration

## **Overview**

The BlackCnote Startup Monitor Integration provides comprehensive monitoring and diagnostics for the entire BlackCnote project startup process. This system combines the PowerShell startup script with the BlackCnote Debug System plugin to provide 24/7 monitoring, real-time health checks, and automated diagnostics.

## **Architecture**

### **Components**

1. **PowerShell Startup Script** (`start-blackcnote-complete.ps1`)
   - Handles Docker Desktop startup and configuration
   - Manages WSL2 setup and optimization
   - Starts all BlackCnote services
   - Provides comprehensive diagnostics and health checking

2. **PHP Startup Monitor** (`bin/blackcnote-startup-monitor.php`)
   - Standalone PHP script for continuous monitoring
   - Integrates with BlackCnote Debug System
   - Provides health reports and metrics
   - Runs as a daemon for 24/7 monitoring

3. **WordPress Plugin Integration** (`blackcnote-debug-system`)
   - Extends the BlackCnote Debug System plugin
   - Provides web-based admin interface
   - REST API endpoints for health monitoring
   - Real-time status updates

### **Integration Points**

```
┌─────────────────────────────────────────────────────────────┐
│                    BlackCnote Project                       │
├─────────────────────────────────────────────────────────────┤
│  PowerShell Startup Script                                  │
│  ├── Docker Management                                      │
│  ├── Service Startup                                        │
│  ├── Health Diagnostics                                     │
│  └── Error Handling                                         │
├─────────────────────────────────────────────────────────────┤
│  PHP Startup Monitor                                        │
│  ├── Continuous Monitoring                                  │
│  ├── Health Reports                                         │
│  ├── Debug System Integration                               │
│  └── Metrics Export                                         │
├─────────────────────────────────────────────────────────────┤
│  WordPress Debug System Plugin                              │
│  ├── Admin Interface                                        │
│  ├── REST API Endpoints                                     │
│  ├── Real-time Status                                       │
│  └── Health Dashboard                                       │
└─────────────────────────────────────────────────────────────┘
```

## **Features**

### **Comprehensive Diagnostics**

#### **Startup Script Health**
- Script existence and accessibility
- PowerShell syntax validation
- File permissions and execution rights
- Last modification tracking

#### **Docker Services Monitoring**
- Docker Desktop process status
- Docker daemon availability
- Container health and status
- Service accessibility via HTTP

#### **WordPress Integration**
- WordPress core accessibility
- Admin panel availability
- REST API functionality
- Plugin and theme status

#### **System Resources**
- Memory usage and limits
- Disk space monitoring
- PHP configuration
- Performance metrics

### **Real-time Monitoring**

#### **Service Health Checks**
- HTTP endpoint monitoring
- Response time tracking
- Status code validation
- Error detection and logging

#### **Container Monitoring**
- Container running status
- Port mapping verification
- Resource usage tracking
- Automatic restart capabilities

#### **File System Monitoring**
- Critical file existence
- Permission validation
- Change detection
- Backup verification

### **Admin Interface**

#### **Web-based Dashboard**
- Real-time health status
- Service availability
- System metrics
- Error reporting

#### **REST API Endpoints**
- Health check endpoints
- Status reporting
- Metrics export
- Service management

## **Installation and Setup**

### **1. Startup Script Enhancement**

The enhanced startup script includes:

```powershell
# Comprehensive Docker diagnostics
function Test-DockerHealth {
    # Docker Desktop status
    # Docker daemon availability
    # Container health
    # System resources
}

# BlackCnote project health check
function Test-BlackCnoteHealth {
    # Project structure validation
    # Required files verification
    # Service port availability
    # File permissions
}

# Health report generation
function Write-HealthReport {
    # Overall health assessment
    # Critical issues identification
    # Recommendations
    # Metrics summary
}
```

### **2. PHP Startup Monitor**

Install the PHP startup monitor:

```bash
# Copy to bin directory
cp bin/blackcnote-startup-monitor.php /path/to/blackcnote/bin/

# Make executable
chmod +x bin/blackcnote-startup-monitor.php

# Test functionality
php bin/blackcnote-startup-monitor.php --health
php bin/blackcnote-startup-monitor.php --status
php bin/blackcnote-startup-monitor.php --daemon
```

### **3. WordPress Plugin Integration**

The BlackCnote Debug System plugin automatically integrates the startup monitor:

```php
// Plugin automatically loads startup monitor
require_once BLACKCNOTE_DEBUG_PLUGIN_DIR . 'includes/class-blackcnote-debug-startup-monitor.php';

// Initialize startup monitor
new BlackCnoteDebugStartupMonitor($this->debug_system);
```

## **Usage**

### **Command Line Interface**

#### **Startup Script**
```powershell
# Basic startup
.\start-blackcnote-complete.ps1

# Diagnostics only
.\start-blackcnote-complete.ps1 -DiagnosticsOnly

# Health check only
.\start-blackcnote-complete.ps1 -HealthCheckOnly

# Debug mode
.\start-blackcnote-complete.ps1 -Debug
```

#### **PHP Monitor**
```bash
# Get health report
php bin/blackcnote-startup-monitor.php --health

# Get status summary
php bin/blackcnote-startup-monitor.php --status

# Run as daemon
php bin/blackcnote-startup-monitor.php --daemon
```

### **Web Interface**

#### **WordPress Admin**
- Navigate to: `http://localhost:8888/wp-admin/admin.php?page=blackcnote-debug-startup`
- View real-time health status
- Monitor service availability
- Export health reports

#### **REST API**
```bash
# Health check
curl http://localhost:8888/wp-json/blackcnote/v1/startup/health

# Status summary
curl http://localhost:8888/wp-json/blackcnote/v1/startup/status
```

## **Health Monitoring**

### **Health Levels**

#### **Healthy (Green)**
- All required services running
- No critical issues
- System resources adequate
- All containers operational

#### **Degraded (Yellow)**
- Some optional services down
- Minor warnings present
- Performance issues detected
- Non-critical containers stopped

#### **Critical (Red)**
- Required services down
- Critical issues detected
- System resources exhausted
- Core containers failed

### **Monitoring Metrics**

#### **Service Metrics**
- Response time
- Availability percentage
- Error rate
- Status changes

#### **System Metrics**
- Memory usage
- Disk space
- CPU utilization
- Network connectivity

#### **Container Metrics**
- Running status
- Resource usage
- Port availability
- Health checks

## **Configuration**

### **Startup Script Configuration**

```powershell
# Health check intervals
$health_check_interval = 300  # 5 minutes

# Service timeout
$service_timeout = 30  # seconds

# Debug mode
$debug_mode = $false

# Production mode
$production_mode = $false
```

### **PHP Monitor Configuration**

```php
// Health check interval (seconds)
private $health_check_interval = 300;

// Service timeout (seconds)
private $service_timeout = 5;

// Log level
private $log_level = 'ALL';

// Cache duration (seconds)
private $cache_duration = 300;
```

### **WordPress Plugin Configuration**

```php
// Admin menu integration
add_submenu_page(
    'blackcnote-debug',
    'Startup Monitor',
    'Startup Monitor',
    'manage_options',
    'blackcnote-debug-startup',
    [$this, 'startup_monitor_page']
);

// REST API endpoints
register_rest_route('blackcnote/v1', '/startup/health', [
    'methods' => 'GET',
    'callback' => [$this, 'rest_startup_health'],
    'permission_callback' => [$this, 'rest_permission_callback']
]);
```

## **Troubleshooting**

### **Common Issues**

#### **Startup Script Not Found**
```powershell
# Check file existence
Test-Path "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\start-blackcnote-complete.ps1"

# Verify permissions
Get-Acl "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\start-blackcnote-complete.ps1"
```

#### **Docker Services Not Starting**
```bash
# Check Docker Desktop
docker info

# Check container status
docker ps -a

# Check logs
docker-compose logs
```

#### **WordPress Integration Issues**
```php
// Check plugin activation
is_plugin_active('blackcnote-debug-system/blackcnote-debug-system.php');

// Check REST API
wp_remote_get(rest_url());

// Check admin access
current_user_can('manage_options');
```

### **Debug Mode**

Enable debug mode for detailed logging:

```powershell
# PowerShell startup script
.\start-blackcnote-complete.ps1 -Debug

# PHP monitor
php bin/blackcnote-startup-monitor.php --daemon

# WordPress plugin
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## **Testing**

### **Integration Test**

Run the comprehensive integration test:

```powershell
# Run full test
.\scripts\testing\test-startup-monitor-integration.ps1

# Generate report
.\scripts\testing\test-startup-monitor-integration.ps1 -GenerateReport

# Skip specific components
.\scripts\testing\test-startup-monitor-integration.ps1 -SkipDocker -SkipWordPress
```

### **Manual Testing**

#### **Startup Script**
```powershell
# Test diagnostics
.\start-blackcnote-complete.ps1 -DiagnosticsOnly

# Test health check
.\start-blackcnote-complete.ps1 -HealthCheckOnly

# Test with debug
.\start-blackcnote-complete.ps1 -Debug
```

#### **PHP Monitor**
```bash
# Test health report
php bin/blackcnote-startup-monitor.php --health

# Test status
php bin/blackcnote-startup-monitor.php --status

# Test daemon mode
php bin/blackcnote-startup-monitor.php --daemon
```

#### **WordPress Plugin**
```bash
# Test admin page
curl http://localhost:8888/wp-admin/admin.php?page=blackcnote-debug-startup

# Test REST API
curl http://localhost:8888/wp-json/blackcnote/v1/startup/health
```

## **Performance Considerations**

### **Monitoring Overhead**

- **Health Check Frequency**: 5-minute intervals minimize overhead
- **Cache Duration**: 5-minute caching reduces redundant checks
- **Timeout Settings**: 5-second timeouts prevent hanging requests
- **Resource Usage**: Minimal memory and CPU impact

### **Optimization**

#### **Caching Strategy**
```php
// Cache health reports
set_transient('blackcnote_startup_health_report', $report, 300);

// Cache service status
wp_cache_set('blackcnote_service_status', $status, '', 60);
```

#### **Async Processing**
```php
// Background health checks
wp_schedule_event(time(), 'every_5_minutes', 'blackcnote_startup_health_check');

// Non-blocking service checks
add_action('wp_ajax_blackcnote_startup_health', [$this, 'ajax_startup_health']);
```

## **Security**

### **Access Control**

#### **WordPress Integration**
```php
// Admin-only access
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}

// Nonce verification
check_ajax_referer('blackcnote_debug_nonce', 'nonce');
```

#### **REST API Security**
```php
// Permission callback
public function rest_permission_callback() {
    return current_user_can('manage_options');
}

// Rate limiting
add_filter('rest_pre_dispatch', [$this, 'rate_limit_requests']);
```

### **Data Protection**

- **Health Reports**: Stored securely with proper permissions
- **Log Files**: Protected from unauthorized access
- **API Endpoints**: Require authentication and authorization
- **Sensitive Data**: Never logged or exposed

## **Maintenance**

### **Regular Tasks**

#### **Health Report Cleanup**
```php
// Clean old health reports
$old_reports = glob(WP_CONTENT_DIR . '/logs/startup-monitor-health-*.json');
foreach ($old_reports as $report) {
    if (filemtime($report) < strtotime('-30 days')) {
        unlink($report);
    }
}
```

#### **Log Rotation**
```bash
# Rotate log files
logrotate /etc/logrotate.d/blackcnote-startup-monitor
```

#### **Cache Cleanup**
```php
// Clear expired transients
wp_clear_scheduled_hook('blackcnote_startup_health_check');
```

### **Updates**

#### **Startup Script Updates**
```powershell
# Backup current script
Copy-Item start-blackcnote-complete.ps1 start-blackcnote-complete.ps1.backup

# Update script
# Test new version
.\start-blackcnote-complete.ps1 -DiagnosticsOnly
```

#### **Plugin Updates**
```php
// WordPress plugin update
wp plugin update blackcnote-debug-system

// Verify integration
is_plugin_active('blackcnote-debug-system/blackcnote-debug-system.php');
```

## **Support**

### **Documentation**

- **This Document**: Complete integration guide
- **API Documentation**: REST API endpoints
- **Troubleshooting Guide**: Common issues and solutions
- **Development Guide**: Contributing to the integration

### **Logs and Monitoring**

#### **Log Locations**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\logs\
├── blackcnote-startup-monitor.log
├── startup-monitor-health.json
└── blackcnote-debug.log
```

#### **Monitoring Endpoints**
```
http://localhost:8888/wp-admin/admin.php?page=blackcnote-debug-startup
http://localhost:8888/wp-json/blackcnote/v1/startup/health
http://localhost:9091/metrics
```

### **Contact**

For support and questions:
- **Documentation**: Check this guide first
- **Logs**: Review log files for errors
- **Testing**: Run integration tests
- **Community**: BlackCnote development team

---

**Last Updated**: December 2024  
**Version**: 1.0.0  
**Status**: Production Ready 