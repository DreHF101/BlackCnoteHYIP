# BlackCnote WordPress wp-content Directory

This directory contains the WordPress content for the BlackCnote investment platform, exclusively using the `blackcnote/wp-content` directory structure.

## 📁 Directory Structure

```
blackcnote/wp-content/
├── plugins/                    # WordPress plugins
│   ├── hyiplab/               # HYIPLab investment platform plugin
│   └── blackcnote-debug-system/ # BlackCnote Debug System plugin
├── themes/                    # WordPress themes
│   └── blackcnote/           # BlackCnote custom theme
├── logs/                     # Application logs
├── views/                    # Custom view files
├── index.php                 # WordPress security file
└── object-cache.php.disabled # Disabled object cache
```

## 🧩 Plugins

### HYIPLab Plugin (`plugins/hyiplab/`)
- **Purpose**: Premium HYIP investment platform by ViserLab
- **Version**: 3.0
- **Features**:
  - Investment management system
  - User registration and authentication
  - Payment processing integration
  - Admin dashboard
  - Multi-level marketing features
  - Advanced security features

### BlackCnote Debug System Plugin (`plugins/blackcnote-debug-system/`)
- **Purpose**: Advanced debugging and monitoring system
- **Version**: 1.0.0
- **Features**:
  - 24/7 system monitoring
  - File change detection
  - System health checks
  - Prometheus metrics export
  - WordPress admin interface
  - Real-time logging
  - Docker container monitoring

#### Plugin Structure:
```
blackcnote-debug-system/
├── blackcnote-debug-system.php  # Main plugin file
├── includes/                    # Core classes
│   ├── class-blackcnote-debug-system.php
│   ├── class-blackcnote-debug-admin.php
│   └── class-blackcnote-debug-metrics.php
├── admin/                       # Admin interface
│   └── views/
│       ├── main-page.php
│       ├── status-page.php
│       └── metrics-page.php
└── assets/                      # Frontend assets
    ├── css/
    │   └── admin.css
    └── js/
        └── admin.js
```

## 🎨 Themes

### BlackCnote Theme (`themes/blackcnote/`)
- **Purpose**: Custom WordPress theme for BlackCnote platform
- **Features**:
  - Modern, responsive design
  - Investment platform integration
  - Custom post types and taxonomies
  - Advanced customization options
  - SEO optimized
  - Performance optimized

#### Theme Structure:
```
blackcnote/
├── functions.php              # Theme functions
├── style.css                  # Main stylesheet
├── index.php                  # Main template
├── header.php                 # Header template
├── footer.php                 # Footer template
├── inc/                       # Theme includes
├── admin/                     # Admin customizations
├── js/                        # JavaScript files
├── template-parts/            # Template parts
└── index.php                  # Security file
```

## 📊 Monitoring & Debugging

### Debug System Features
- **Real-time Monitoring**: 24/7 system health monitoring
- **File Change Detection**: Monitors changes in `blackcnote/wp-content`
- **Metrics Export**: Prometheus-compatible metrics at `http://localhost:9091/metrics`
- **Admin Interface**: WordPress admin dashboard integration
- **Log Management**: Comprehensive logging system

### Metrics Available
- System memory usage
- Disk space monitoring
- Database query statistics
- Plugin and theme status
- Docker container health
- File change tracking
- Error and warning counts

### Access Points
- **WordPress Admin**: `/wp-admin/admin.php?page=blackcnote-debug`
- **Metrics Dashboard**: `http://localhost:9091/`
- **Prometheus Metrics**: `http://localhost:9091/metrics`

## 🔧 Configuration

### WordPress Configuration
- **Content Directory**: Exclusively uses `blackcnote/wp-content`
- **Debug Mode**: Enabled for development
- **Memory Limits**: Optimized for performance
- **Security**: Enhanced security measures

### Plugin Configuration
- **HYIPLab**: Configured for investment platform operations
- **Debug System**: Automatic monitoring and alerting
- **Logging**: Comprehensive error and system logging

## 🚀 Deployment

### Docker Integration
- **WordPress Container**: Serves content from `blackcnote/wp-content`
- **Debug Daemon**: 24/7 monitoring service
- **Metrics Exporter**: Prometheus metrics endpoint
- **Nginx Proxy**: Reverse proxy configuration

### Port Configuration
- **WordPress**: `http://localhost:8888`
- **React App**: `http://localhost:5174`
- **Metrics**: `http://localhost:9091`

## 📝 Logging

### Log Files
- **Debug Log**: `logs/blackcnote-debug.log`
- **WordPress Log**: `logs/wordpress/`
- **Plugin Logs**: Individual plugin logging

### Log Levels
- **ERROR**: Critical system errors
- **WARNING**: System warnings
- **INFO**: General information
- **DEBUG**: Debug information
- **SYSTEM**: System-level events

## 🔒 Security

### Security Measures
- **Directory Protection**: `index.php` files in all directories
- **File Permissions**: Proper file and directory permissions
- **Input Validation**: All user inputs validated and sanitized
- **Nonce Verification**: WordPress nonce verification
- **Access Control**: Role-based access control

### Security Features
- **HTTPS Support**: SSL/TLS encryption
- **Firewall Rules**: Network-level protection
- **Backup System**: Automated backup system
- **Monitoring**: 24/7 security monitoring

## 🛠️ Maintenance

### Regular Tasks
- **Log Rotation**: Automatic log file rotation
- **Database Optimization**: Regular database maintenance
- **Plugin Updates**: Security and feature updates
- **Theme Updates**: Performance and security updates
- **Backup Verification**: Regular backup testing

### Monitoring
- **System Health**: Continuous health monitoring
- **Performance Metrics**: Real-time performance tracking
- **Error Tracking**: Comprehensive error monitoring
- **Security Alerts**: Immediate security notifications

## 📚 Documentation

### Additional Resources
- **Development Guide**: `docs/development/`
- **Deployment Guide**: `docs/deployment/`
- **Troubleshooting**: `docs/troubleshooting/`
- **API Documentation**: `docs/api/`

### Support
- **Debug System**: Integrated debugging tools
- **Monitoring**: Real-time system monitoring
- **Logs**: Comprehensive logging system
- **Metrics**: Performance and health metrics

---

**Last Updated**: June 26, 2025
**Version**: 1.0.0
**Status**: Production Ready 