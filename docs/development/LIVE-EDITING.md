# BlackCnote Live Editing Development Guide

## 🚀 Overview

BlackCnote now features a comprehensive Docker-based development environment with advanced live editing capabilities. This setup provides instant feedback, hot reloading, and seamless development workflow for both WordPress and React applications.

## ✨ Features

### 🔥 Live Editing Capabilities
- **Real-time file watching** across all components
- **Hot reloading** for React components
- **Browsersync synchronization** for multi-browser testing
- **Instant WordPress updates** without manual refresh
- **Development toolbar** with service monitoring
- **File change notifications** and logging

### 🐳 Docker Services
- **WordPress** with enhanced debugging
- **React Development Server** with Vite
- **MySQL Database** with performance optimization
- **Redis Cache** with development configuration
- **Browsersync** for live synchronization
- **PHPMyAdmin** for database management
- **MailHog** for email testing
- **Redis Commander** for cache management

### 🛠️ Development Tools
- **Service health monitoring**
- **Cache management**
- **Error tracking and logging**
- **Performance monitoring**
- **Quick action buttons**
- **Keyboard shortcuts**

## 🏗️ Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   WordPress     │    │   React App     │    │   Browsersync   │
│   (Port 8888)   │◄──►│   (Port 5174)   │◄──►│   (Port 3000)   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     MySQL       │    │     Redis       │    │   File Watcher  │
│   (Port 3306)   │    │   (Port 6379)   │    │   (Background)  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## 🚀 Quick Start

### Prerequisites
- Docker Desktop installed and running
- Docker Compose installed
- Git (for version control)

### 1. Clone and Setup
```bash
# Clone the repository
git clone <repository-url>
cd BlackCnote

# Run the development setup script
# For Linux/Mac:
./scripts/start-dev-environment.sh

# For Windows:
scripts\start-dev-environment.bat
```

### 2. Access Services
After setup, you can access:

| Service | URL | Description |
|---------|-----|-------------|
| WordPress | http://localhost:8888 | Main WordPress site |
| React App | http://localhost:5174 | React development server |
| Browsersync | http://localhost:3000 | Live sync proxy |
| Browsersync UI | http://localhost:3001 | Browsersync control panel |
| PHPMyAdmin | http://localhost:8080 | Database management |
| MailHog | http://localhost:8025 | Email testing interface |
| Redis Commander | http://localhost:8081 | Redis cache management |

## 🔧 Development Workflow

### WordPress Development

#### File Structure
```
blackcnote/
├── wp-content/
│   ├── themes/          # WordPress themes
│   ├── plugins/         # WordPress plugins
│   ├── mu-plugins/      # Must-use plugins
│   └── uploads/         # Media uploads
├── wp-config.php        # WordPress configuration
└── ...                  # Other WordPress files
```

#### Live Editing Features
- **Theme files** are watched and changes are reflected immediately
- **Plugin files** are monitored for instant updates
- **PHP files** trigger automatic reloading
- **CSS/JS files** are hot-reloaded without page refresh

#### Development Tools
- **Development toolbar** in WordPress admin
- **Service status indicators**
- **Cache management buttons**
- **Quick action shortcuts**

### React Development

#### File Structure
```
react-app/
├── src/                 # React source files
├── public/              # Static assets
├── package.json         # Dependencies
└── vite.config.js       # Vite configuration
```

#### Live Editing Features
- **Component hot reloading**
- **CSS/SCSS hot reloading**
- **TypeScript support**
- **Fast refresh enabled**

#### Development Commands
```bash
# Start React development server
npm run dev

# Build for production
npm run build

# Run tests
npm run test
```

## 🎯 Live Editing Features

### 1. File Watching
The system monitors changes in:
- WordPress theme files
- WordPress plugin files
- React component files
- CSS/SCSS files
- JavaScript files
- Configuration files

### 2. Hot Reloading
- **WordPress**: PHP files trigger automatic page refresh
- **React**: Components update without page reload
- **CSS**: Styles update instantly
- **JavaScript**: Scripts reload automatically

### 3. Browsersync Integration
- **Multi-browser synchronization**
- **Scroll position sync**
- **Form input sync**
- **Click synchronization**
- **Live reload across devices**

### 4. Development Indicators
- **Visual development mode indicator**
- **Service status monitoring**
- **File change notifications**
- **Error tracking and display**

## 🛠️ Development Tools

### WordPress Admin Tools

#### Development Toolbar
Located in the top-right corner of WordPress admin:
- Service status indicators
- Cache management
- Quick action buttons
- File change notifications

#### Quick Actions Panel
Available on admin pages:
- Open React App
- Open Browsersync
- Restart Services
- Clear Cache

#### Keyboard Shortcuts
- `Ctrl+Shift+R`: Reload page
- `Ctrl+Shift+D`: Toggle development info
- `Ctrl+Shift+C`: Clear cache

### Frontend Development Tools

#### Development Banner
Shows on frontend pages in development mode:
- Development mode indicator
- Service status
- Quick access buttons

#### Context Menu
Right-click anywhere on frontend for:
- Reload page
- Clear cache
- Open React app
- Open Browsersync
- Toggle development info

## 📊 Monitoring and Logging

### Service Health Monitoring
- **Real-time status checking**
- **Automatic health checks**
- **Service restart capabilities**
- **Performance monitoring**

### File Change Logging
- **Change detection and logging**
- **Timestamp tracking**
- **File path recording**
- **Change type identification**

### Error Tracking
- **JavaScript error capture**
- **PHP error logging**
- **Performance monitoring**
- **Memory usage tracking**

## 🔧 Configuration

### Environment Variables
```bash
# Development environment
NODE_ENV=development
WORDPRESS_DEBUG=1
CHOKIDAR_USEPOLLING=true
WATCHPACK_POLLING=true
FAST_REFRESH=true
```

### Docker Configuration
```yaml
# Enhanced volume mounting for live editing
volumes:
  - ./blackcnote:/var/www/html:delegated
  - ./react-app/src:/app/src:delegated
  - ./scripts:/var/www/html/scripts:delegated
  - ./logs:/var/www/html/logs:delegated
```

### WordPress Configuration
```php
// Development settings
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', true);
define('SCRIPT_DEBUG', true);
define('SAVEQUERIES', true);
define('WP_CACHE', false);
define('FS_METHOD', 'direct');
```

## 🚀 Advanced Features

### 1. Multi-Environment Support
- **Development**: Full debugging and live editing
- **Staging**: Production-like with debugging
- **Production**: Optimized for performance

### 2. Performance Optimization
- **Redis caching** for development
- **MySQL optimization** for faster queries
- **File watching optimization** with delegated volumes
- **Memory management** for large projects

### 3. Security Features
- **Development-only features** disabled in production
- **Secure database connections**
- **Environment-specific configurations**
- **Access control for development tools**

### 4. Backup and Recovery
- **Automatic backups** before major changes
- **Database snapshots**
- **File system backups**
- **Quick restore capabilities**

## 🔍 Troubleshooting

### Common Issues

#### Services Not Starting
```bash
# Check Docker status
docker info

# Check container logs
docker-compose logs

# Restart services
docker-compose down && docker-compose up -d
```

#### File Changes Not Detected
```bash
# Check file permissions
chmod -R 755 ./blackcnote

# Restart file watcher
docker-compose restart file-watcher

# Check volume mounts
docker-compose exec wordpress ls -la /var/www/html
```

#### Performance Issues
```bash
# Clear all cache
docker-compose exec wordpress wp cache flush

# Restart Redis
docker-compose restart redis

# Check memory usage
docker stats
```

### Debug Mode
Enable enhanced debugging:
```bash
# Set debug environment
export WORDPRESS_DEBUG=1
export NODE_ENV=development

# Restart services
docker-compose down && docker-compose up -d
```

## 📚 Best Practices

### 1. Development Workflow
- **Use the development toolbar** for quick actions
- **Monitor service status** regularly
- **Clear cache** when making major changes
- **Check logs** for errors and warnings

### 2. File Organization
- **Keep theme files** in `blackcnote/wp-content/themes/`
- **Organize plugins** in `blackcnote/wp-content/plugins/`
- **Store React components** in `react-app/src/`
- **Use consistent naming** conventions

### 3. Performance
- **Monitor memory usage** during development
- **Clear cache** periodically
- **Optimize database queries** in development
- **Use Redis caching** effectively

### 4. Security
- **Never commit sensitive data** to version control
- **Use environment variables** for configuration
- **Regularly update dependencies**
- **Monitor for security vulnerabilities**

## 🎉 Conclusion

The BlackCnote live editing development environment provides a powerful, efficient, and user-friendly development experience. With instant feedback, comprehensive tooling, and seamless integration between WordPress and React, developers can focus on building great features without the overhead of manual refresh cycles.

For additional support or feature requests, please refer to the project documentation or contact the development team. 