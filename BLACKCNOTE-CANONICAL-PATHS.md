# BlackCnote Canonical Directory Pathways

## 🚨 **CRITICAL - CANONICAL PATHWAYS** 🚨

**All BlackCnote development, deployment, documentation, and scripts MUST use these canonical pathways. These are the ONLY valid paths for the BlackCnote project.**

---

## **Primary Canonical Pathways**

### **0. BlackCnote Project Root Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\
```
- **Purpose**: Main BlackCnote project root directory
- **Contains**: All project files, Docker configs, scripts, docs, themes, plugins
- **Usage**: Primary development and deployment directory for the entire BlackCnote project
- **Status**: ✅ **CANONICAL PROJECT ROOT**

### **1. BlackCnote Theme Directory (WordPress Installation)**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\
```
- **Purpose**: Complete WordPress installation directory
- **Contains**: WordPress core files, wp-config.php, wp-admin/, wp-includes/
- **Usage**: Main WordPress installation for the BlackCnote project

### **2. WordPress Content Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\
```
- **Purpose**: All WordPress content (themes, plugins, uploads)
- **Contains**: themes/, plugins/, uploads/, mu-plugins/, logs/
- **Usage**: EXCLUSIVE wp-content directory for all BlackCnote WordPress content

### **3. BlackCnote Theme Files Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\
```
- **Purpose**: Custom BlackCnote theme files
- **Contains**: style.css, index.php, functions.php, assets/, inc/, etc.
- **Usage**: Main theme development and customization

---

## **🌐 CANONICAL GITHUB REPOSITORY**

### **BlackCnote Official GitHub Repository**
```
https://github.com/DreHF101/BlackCnoteHYIP.git
```
- **Purpose**: Official source of truth for all BlackCnote code, documentation, and configuration
- **Usage**: All environments must sync with this repository for version control, collaboration, and deployment
- **Status**: ✅ **CANONICAL GITHUB REPO**

---

## **🌐 CANONICAL SERVICE URLS - LOCALHOST PATHWAYS**

**All BlackCnote services MUST use these canonical localhost URLs for development and testing.**

### **Core Application Services**

| Service | Canonical URL | Port | Purpose | Status |
|---------|---------------|------|---------|--------|
| **WordPress Frontend** | `http://localhost:8888` | 8888 | Main WordPress site | ✅ **CANONICAL** |
| **WordPress Admin** | `http://localhost:8888/wp-admin/` | 8888 | WordPress administration | ✅ **CANONICAL** |
| **WordPress API** | `http://localhost:8888/wp-json/` | 8888 | WordPress REST API | ✅ **CANONICAL** |
| **React Development Server** | `http://localhost:5174` | 5174 | React app with hot reload | ✅ **CANONICAL** |

### **Database & Management Services**

| Service | Canonical URL | Port | Purpose | Status |
|---------|---------------|------|---------|--------|
| **phpMyAdmin** | `http://localhost:8080` | 8080 | Database management | ✅ **CANONICAL** |
| **MySQL Database** | `mysql://localhost:3306` | 3306 | WordPress database | ✅ **CANONICAL** |
| **Redis Cache** | `redis://localhost:6379` | 6379 | Caching service | ✅ **CANONICAL** |
| **Redis Commander** | `http://localhost:8081` | 8081 | Redis management UI | ✅ **CANONICAL** |

### **Development & Testing Services**

| Service | Canonical URL | Port | Purpose | Status |
|---------|---------------|------|---------|--------|
| **Browsersync** | `http://localhost:3000` | 3000 | Live reloading proxy | ✅ **CANONICAL** |
| **Browsersync UI** | `http://localhost:3001` | 3001 | Browsersync control panel | ✅ **CANONICAL** |
| **MailHog** | `http://localhost:8025` | 8025 | Email testing interface | ✅ **CANONICAL** |
| **MailHog SMTP** | `smtp://localhost:1025` | 1025 | SMTP testing | ✅ **CANONICAL** |
| **Dev Tools** | `http://localhost:9229` | 9229 | Node.js debugging | ✅ **CANONICAL** |

### **Monitoring & Health Services**

| Service | Canonical URL | Port | Purpose | Status |
|---------|---------------|------|---------|--------|
| **Health Check** | `http://localhost:8888/health` | 8888 | Service health status | ✅ **CANONICAL** |
| **Debug System** | `http://localhost:8888/wp-admin/admin.php?page=blackcnote-debug` | 8888 | Debug system interface | ✅ **CANONICAL** |
| **Metrics Exporter** | `http://localhost:9091` | 9091 | Prometheus metrics | ✅ **CANONICAL** |

---

## **🔗 Service Connection Maintenance**

### **Required Service Dependencies**

```yaml
# Service dependency order for startup
1. MySQL (3306) → WordPress (8888)
2. Redis (6379) → WordPress (8888)
3. WordPress (8888) → React (5174)
4. React (5174) → Browsersync (3000)
5. All Services → Health Check (8888/health)
```

### **Connection Verification Scripts**

```bash
# Verify all services are accessible
curl -f http://localhost:8888/health
curl -f http://localhost:5174
curl -f http://localhost:8083
curl -f http://localhost:8025
curl -f http://localhost:8081
```

### **Service Health Monitoring**

```php
// WordPress health check endpoint
add_action('rest_api_init', function () {
    register_rest_route('blackcnote/v1', '/health', [
        'methods' => 'GET',
        'callback' => 'blackcnote_health_check',
        'permission_callback' => '__return_true'
    ]);
});

function blackcnote_health_check() {
    $services = [
        'wordpress' => 'http://localhost:8888',
        'react' => 'http://localhost:5174',
        'mysql' => 'mysql://localhost:3306',
        'redis' => 'redis://localhost:6379',
        'phpmyadmin' => 'http://localhost:8083',
        'mailhog' => 'http://localhost:8025',
        'browsersync' => 'http://localhost:3000'
    ];
    
    return [
        'status' => 'healthy',
        'services' => $services,
        'timestamp' => current_time('mysql')
    ];
}
```

---

## **Project Structure Overview**

```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\          # 🏠 PROJECT ROOT
├── 📁 blackcnote/                                      # WordPress Installation
│   ├── 📁 wp-content/
│   │   ├── 📁 themes/blackcnote/                       # Main Theme
│   │   ├── 📁 plugins/                                 # WordPress Plugins
│   │   └── 📁 uploads/                                 # Media Uploads
│   ├── 📁 wp-admin/                                    # WordPress Admin
│   ├── 📁 wp-includes/                                 # WordPress Core
│   └── 📄 wp-config.php                                # WordPress Config
├── 📁 hyiplab/                                         # Investment Platform
├── 📁 react-app/                                       # React Frontend
├── 📁 config/                                          # Configuration Files
│   ├── 📁 docker/                                      # Docker Configs
│   └── 📁 nginx/                                       # Nginx Configs
├── 📁 scripts/                                         # Automation Scripts
├── 📁 docs/                                            # Documentation
├── 📁 tools/                                           # Development Tools
├── 📁 bin/                                             # Binary Scripts
└── 📁 logs/                                            # Application Logs
```

## **Docker Configuration**

### **Volume Mapping**
The Docker Compose configuration uses these canonical paths:

```yaml
volumes:
  # Main WordPress installation
  - "C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote:/var/www/html:delegated"
  # Development tools
  - "./scripts:/var/www/html/scripts:delegated"
  # Logs
  - "./logs:/var/www/html/logs:delegated"
```

### **Container Access**
- **WordPress Container**: `/var/www/html` maps to `blackcnote/`
- **File Watcher**: Monitors `blackcnote/wp-content` exclusively
- **Browsersync**: Serves from `blackcnote/` directory

## **Development Scripts**

### **Packaging Scripts**
- **PowerShell**: `create-blackcnote-zip.ps1`
- **Batch**: `create-blackcnote-zip.bat`
- **Both scripts use**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote`

### **Debug System**
- **Daemon**: `bin/blackcnote-debug-daemon.php`
- **Monitors**: `blackcnote/wp-content` directory exclusively
- **Logs**: All file changes and system status

## **❌ DEPRECATED/INVALID PATHS**

**NEVER use these paths for BlackCnote development:**

- `wordpress/wp-content/` - Legacy directory, not used
- `wp-content/` (root level) - Not the canonical directory
- Any other `wp-content` directories outside the `blackcnote/` folder

## **✅ CORRECT USAGE EXAMPLES**

### **WordPress Functions**
```php
// ✅ CORRECT - Use WordPress functions
define('BLACKCNOTE_THEME_DIR', get_template_directory());
define('BLACKCNOTE_THEME_URI', get_template_directory_uri());
define('BLACKCNOTE_ASSETS_URI', BLACKCNOTE_THEME_URI . '/assets');

// ✅ CORRECT - Include files
require_once BLACKCNOTE_THEME_DIR . '/inc/blackcnote-react-loader.php';
```

### **File Operations**
```php
// ✅ CORRECT - Use WordPress constants
$upload_dir = wp_upload_dir();
$plugin_dir = WP_PLUGIN_DIR;
$content_dir = WP_CONTENT_DIR;
```

### **Development Scripts**
```powershell
# ✅ CORRECT - Use canonical paths
$ThemePath = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote"
$OutputFile = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\BlackCnote-Theme-Complete.zip"
```

### **Service URLs in Code**
```php
// ✅ CORRECT - Use canonical service URLs
define('BLACKCNOTE_WORDPRESS_URL', 'http://localhost:8888');
define('BLACKCNOTE_REACT_URL', 'http://localhost:5174');
define('BLACKCNOTE_PHPMYADMIN_URL', 'http://localhost:8083');
define('BLACKCNOTE_MAILHOG_URL', 'http://localhost:8025');
define('BLACKCNOTE_REDIS_COMMANDER_URL', 'http://localhost:8082');
define('BLACKCNOTE_BROWSERSYNC_URL', 'http://localhost:3000');
```

## **🔧 Configuration Files**

### **Docker Compose**
- **File**: `config/docker/docker-compose.yml`
- **Status**: ✅ Uses canonical paths
- **Volume Mapping**: Correctly maps `blackcnote/` to `/var/www/html`

### **Nginx Configuration**
- **File**: `config/nginx/blackcnote-docker.conf`
- **Status**: ✅ Serves from `blackcnote/` directory
- **Document Root**: `/var/www/html` (maps to `blackcnote/`)

### **WordPress Configuration**
- **File**: `blackcnote/wp-config.php`
- **Status**: ✅ Located in canonical theme directory
- **Database**: Configured for BlackCnote project

## **📋 Verification Checklist**

### **Before Development**
- [ ] Confirm you're working in `blackcnote/wp-content/themes/blackcnote/`
- [ ] Verify Docker containers are using canonical paths
- [ ] Check that debug system is monitoring correct directory
- [ ] Ensure all scripts reference canonical paths
- [ ] Verify all service URLs are accessible

### **Before Deployment**
- [ ] Verify packaging scripts use canonical paths
- [ ] Check Docker volume mappings
- [ ] Confirm WordPress configuration points to correct directories
- [ ] Test that all features work with canonical paths
- [ ] Verify all service connections are maintained

### **Before Committing**
- [ ] No hardcoded paths to deprecated directories
- [ ] All includes use WordPress functions or canonical paths
- [ ] Documentation references correct paths
- [ ] Scripts use canonical paths
- [ ] All service URLs use canonical localhost pathways

## **🚀 Quick Reference**

| **Component** | **Canonical Path** | **Service URL** |
|---------------|-------------------|-----------------|
| WordPress Installation | `blackcnote/` | `http://localhost:8888` |
| WordPress Content | `blackcnote/wp-content/` | `http://localhost:8888/wp-content` |
| Theme Files | `blackcnote/wp-content/themes/blackcnote/` | `http://localhost:8888` |
| Plugins | `blackcnote/wp-content/plugins/` | `http://localhost:8888/wp-admin/plugins.php` |
| Uploads | `blackcnote/wp-content/uploads/` | `http://localhost:8888/wp-content/uploads` |
| Logs | `blackcnote/wp-content/logs/` | `http://localhost:8888/wp-admin/admin.php?page=blackcnote-debug` |
| React App | `react-app/` | `http://localhost:5174` |
| Database Management | - | `http://localhost:8083` |
| Email Testing | - | `http://localhost:8025` |
| Cache Management | - | `http://localhost:8082` |
| Live Reloading | - | `http://localhost:3000` |

## **📞 Support**

If you encounter path-related issues:

1. **Check this document** for correct canonical paths
2. **Verify Docker configuration** in `config/docker/docker-compose.yml`
3. **Review debug logs** in `blackcnote/wp-content/logs/`
4. **Check script documentation** for path references
5. **Verify service connectivity** using the health check endpoints

## **Troubleshooting Port Conflicts and Performance**

### **Port Conflicts**
- If you get errors about ports 5174, 3000, or 3001 being in use, run:
  - `npx kill-port 5174 3000 3001`
  - Or manually: `netstat -ano | findstr :5174` and `taskkill /PID [PID] /F`
- If Browsersync starts on 3002 or 3003, it means 3000/3001 were busy
- If you get a permission error, try running your terminal as administrator
- If all else fails, reboot your machine

### **Docker Resource Allocation**
- For best performance, allocate at least 2 CPUs and 4GB RAM in Docker Desktop → Settings → Resources
- Restart Docker after changing resources

**Last Updated**: December 2024  
**Version**: 2.0  
**Status**: ✅ **ACTIVE - ALL COMPONENTS USING CANONICAL PATHS AND URLS** 