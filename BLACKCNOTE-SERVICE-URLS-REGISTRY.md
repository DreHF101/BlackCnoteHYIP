# BlackCnote Service URLs Registry

## 🚨 **CRITICAL - CANONICAL SERVICE URLS** 🚨

**All BlackCnote services MUST use these canonical URLs. These are the ONLY valid service endpoints for the BlackCnote project.**

---

## **📋 Service Registry Overview**

| Service Category | Count | Status | Last Verified |
|------------------|-------|--------|---------------|
| Core Application | 4 | ✅ Active | 2024-12-26 |
| Database & Management | 4 | ✅ Active | 2024-12-26 |
| Development & Testing | 5 | ✅ Active | 2024-12-26 |
| Monitoring & Health | 3 | ✅ Active | 2024-12-26 |
| **Total Services** | **16** | ✅ **All Active** | **2024-12-26** |

---

## **🌐 Core Application Services**

### **1. WordPress Frontend**
- **Canonical URL**: `http://localhost:8888`
- **Port**: 8888
- **Container**: `blackcnote-wordpress`
- **Purpose**: Main WordPress site frontend
- **Dependencies**: MySQL, Redis
- **Health Check**: `http://localhost:8888/health`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **2. WordPress Admin**
- **Canonical URL**: `http://localhost:8888/wp-admin/`
- **Port**: 8888
- **Container**: `blackcnote-wordpress`
- **Purpose**: WordPress administration panel
- **Dependencies**: MySQL, Redis
- **Health Check**: `http://localhost:8888/wp-admin/admin-ajax.php`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **3. WordPress REST API**
- **Canonical URL**: `http://localhost:8888/wp-json/`
- **Port**: 8888
- **Container**: `blackcnote-wordpress`
- **Purpose**: WordPress REST API endpoints
- **Dependencies**: MySQL, Redis
- **Health Check**: `http://localhost:8888/wp-json/wp/v2/`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **4. React Development Server**
- **Canonical URL**: `http://localhost:5174`
- **Port**: 5174
- **Container**: `blackcnote-react`
- **Purpose**: React app with hot module replacement
- **Dependencies**: WordPress
- **Health Check**: `http://localhost:5174`
- **Status**: ✅ **CANONICAL - REQUIRED**

---

## **🗄️ Database & Management Services**

### **5. phpMyAdmin**
- **Canonical URL**: `http://localhost:8080`
- **Port**: 8080
- **Container**: `blackcnote-phpmyadmin`
- **Purpose**: Database management interface
- **Dependencies**: MySQL
- **Health Check**: `http://localhost:8080`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **6. MySQL Database**
- **Canonical URL**: `mysql://localhost:3306`
- **Port**: 3306
- **Container**: `blackcnote-mysql`
- **Purpose**: WordPress database
- **Dependencies**: None
- **Health Check**: `telnet localhost 3306`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **7. Redis Cache**
- **Canonical URL**: `redis://localhost:6379`
- **Port**: 6379
- **Container**: `blackcnote-redis`
- **Purpose**: Caching service
- **Dependencies**: None
- **Health Check**: `telnet localhost 6379`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **8. Redis Commander**
- **Canonical URL**: `http://localhost:8081`
- **Port**: 8081
- **Container**: `blackcnote-redis-commander`
- **Purpose**: Redis management interface
- **Dependencies**: Redis
- **Health Check**: `http://localhost:8081`
- **Status**: ✅ **CANONICAL - REQUIRED**

---

## **🔄 Development & Testing Services**

### **9. Browsersync**
- **Canonical URL**: `http://localhost:3000`
- **Port**: 3000
- **Container**: `blackcnote-browsersync`
- **Purpose**: Live reloading proxy
- **Dependencies**: WordPress, React
- **Health Check**: `http://localhost:3000`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **10. Browsersync UI**
- **Canonical URL**: `http://localhost:3001`
- **Port**: 3001
- **Container**: `blackcnote-browsersync`
- **Purpose**: Browsersync control panel
- **Dependencies**: WordPress, React
- **Health Check**: `http://localhost:3001`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **11. MailHog**
- **Canonical URL**: `http://localhost:8025`
- **Port**: 8025
- **Container**: `blackcnote-mailhog`
- **Purpose**: Email testing interface
- **Dependencies**: None
- **Health Check**: `http://localhost:8025/`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **12. MailHog SMTP**
- **Canonical URL**: `smtp://localhost:1025`
- **Port**: 1025
- **Container**: `blackcnote-mailhog`
- **Purpose**: SMTP testing
- **Dependencies**: None
- **Health Check**: `telnet localhost 1025`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **13. Dev Tools**
- **Canonical URL**: `http://localhost:9229`
- **Port**: 9229
- **Container**: `blackcnote-dev-tools`
- **Purpose**: Development tools dashboard
- **Dependencies**: None
- **Health Check**: `http://localhost:9229/`
- **Status**: ✅ **CANONICAL - REQUIRED**

---

## **📊 Monitoring & Health Services**

### **14. Health Check**
- **Canonical URL**: `http://localhost:8888/health`
- **Port**: 8888
- **Container**: `blackcnote-wordpress`
- **Purpose**: Service health status
- **Dependencies**: WordPress
- **Health Check**: `curl -f http://localhost:8888/health`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **15. Debug System**
- **Canonical URL**: `http://localhost:8888/wp-admin/admin.php?page=blackcnote-debug`
- **Port**: 8888
- **Container**: `blackcnote-wordpress`
- **Purpose**: Debug system interface
- **Dependencies**: WordPress Admin
- **Health Check**: `http://localhost:8888/wp-admin/admin.php?page=blackcnote-debug`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **16. Metrics Exporter**
- **Canonical URL**: `http://localhost:9091`
- **Port**: 9091
- **Container**: `blackcnote-metrics-exporter`
- **Purpose**: Prometheus metrics
- **Dependencies**: None
- **Health Check**: `http://localhost:9091/metrics`
- **Status**: ✅ **CANONICAL - REQUIRED**

---

## **🔗 Service Dependencies & Startup Order**

```yaml
# Required startup order for BlackCnote services
startup_order:
  1. mysql:
      service: blackcnote-mysql
      url: mysql://localhost:3306
      dependencies: []
      
  2. redis:
      service: blackcnote-redis
      url: redis://localhost:6379
      dependencies: []
      
  3. wordpress:
      service: blackcnote-wordpress
      url: http://localhost:8888
      dependencies: [mysql, redis]
      
  4. phpmyadmin:
      service: blackcnote-phpmyadmin
      url: http://localhost:8080
      dependencies: [mysql]
      
  5. redis-commander:
      service: blackcnote-redis-commander
      url: http://localhost:8081
      dependencies: [redis]
      
  6. react:
      service: blackcnote-react
      url: http://localhost:5174
      dependencies: [wordpress]
      
  7. browsersync:
      service: blackcnote-browsersync
      url: http://localhost:3000
      dependencies: [wordpress, react]
      
  8. mailhog:
      service: blackcnote-mailhog
      url: http://localhost:8025
      dependencies: []
      
  9. dev-tools:
      service: blackcnote-dev-tools
      url: http://localhost:9229
      dependencies: []
      
  10. metrics-exporter:
      service: blackcnote-metrics-exporter
      url: http://localhost:9091
      dependencies: []
```

---

## **🔍 Connection Verification**

### **Automated Health Check Script**

```bash
#!/bin/bash
# BlackCnote Service Health Check

echo "🔍 BlackCnote Service Health Check"
echo "=================================="

services=(
    "WordPress:http://localhost:8888/health"
    "React:http://localhost:5174"
    "phpMyAdmin:http://localhost:8080"
    "MailHog:http://localhost:8025"
    "Redis Commander:http://localhost:8081"
    "Browsersync:http://localhost:3000"
    "Browsersync UI:http://localhost:3001"
    "Dev Tools:http://localhost:9229"
    "Metrics Exporter:http://localhost:9091"
)

for service in "${services[@]}"; do
    IFS=':' read -r name url <<< "$service"
    if curl -f -s "$url" > /dev/null; then
        echo "✅ $name: $url"
    else
        echo "❌ $name: $url"
    fi
done
```

### **PowerShell Health Check**

```powershell
# BlackCnote Service Health Check (PowerShell)
$services = @(
    @{Name = "WordPress"; Url = "http://localhost:8888/health"},
    @{Name = "React"; Url = "http://localhost:5174"},
    @{Name = "phpMyAdmin"; Url = "http://localhost:8080"},
    @{Name = "MailHog"; Url = "http://localhost:8025"},
    @{Name = "Redis Commander"; Url = "http://localhost:8081"},
    @{Name = "Browsersync"; Url = "http://localhost:3000"},
    @{Name = "Browsersync UI"; Url = "http://localhost:3001"},
    @{Name = "Dev Tools"; Url = "http://localhost:9229"},
    @{Name = "Metrics Exporter"; Url = "http://localhost:9091"}
)

foreach ($service in $services) {
    try {
        $response = Invoke-WebRequest -Uri $service.Url -TimeoutSec 5 -UseBasicParsing
        Write-Host "✅ $($service.Name): $($service.Url)" -ForegroundColor Green
    } catch {
        Write-Host "❌ $($service.Name): $($service.Url)" -ForegroundColor Red
    }
}
```

### **PHP Health Check**

```php
<?php
// BlackCnote Service Health Check (PHP)
function check_blackcnote_services() {
    $services = [
        'wordpress' => 'http://localhost:8888/health',
        'react' => 'http://localhost:5174',
        'phpmyadmin' => 'http://localhost:8080',
        'mailhog' => 'http://localhost:8025',
        'redis_commander' => 'http://localhost:8081',
        'browsersync' => 'http://localhost:3000',
        'browsersync_ui' => 'http://localhost:3001',
        'dev_tools' => 'http://localhost:9229',
        'metrics_exporter' => 'http://localhost:9091'
    ];
    
    $results = [];
    
    foreach ($services as $key => $service) {
        $response = wp_remote_get($service['url'], [
            'timeout' => 5,
            'sslverify' => false
        ]);
        
        $status = is_wp_error($response) ? 'down' : 'up';
        $results[$key] = [
            'name' => $service['name'],
            'url' => $service['url'],
            'status' => $status,
            'required' => $service['required'],
            'error' => is_wp_error($response) ? $response->get_error_message() : null
        ];
    }
    
    return $results;
}
?>
```

---

## **🛠️ Maintenance Procedures**

### **Service Restart Procedures**

```bash
# Restart all services
docker-compose -f config/docker/docker-compose-wsl2.yml restart

# Restart specific service
docker-compose -f config/docker/docker-compose-wsl2.yml restart [service-name]

# Restart with health check
docker-compose -f config/docker/docker-compose-wsl2.yml up -d --force-recreate
```

### **URL Configuration Updates**

```php
// WordPress URL configuration constants
define('BLACKCNOTE_WORDPRESS_URL', 'http://localhost:8888');
define('BLACKCNOTE_REACT_URL', 'http://localhost:5174');
define('BLACKCNOTE_PHPMYADMIN_URL', 'http://localhost:8080');
define('BLACKCNOTE_MAILHOG_URL', 'http://localhost:8025');
define('BLACKCNOTE_REDIS_COMMANDER_URL', 'http://localhost:8081');
define('BLACKCNOTE_BROWSERSYNC_URL', 'http://localhost:3000');
define('BLACKCNOTE_BROWSERSYNC_UI_URL', 'http://localhost:3001');
define('BLACKCNOTE_DEV_TOOLS_URL', 'http://localhost:9229');
define('BLACKCNOTE_METRICS_EXPORTER_URL', 'http://localhost:9091');
```

### **Port Conflict Resolution**

```bash
# Check for port conflicts
netstat -tulpn | grep -E ':(8888|5174|8080|8025|8081|3000|3001|9229|9091)'

# Kill process using specific port
sudo kill -9 $(sudo lsof -t -i:8888)

# Alternative port mapping (if needed)
# WordPress: 8889
# React: 5175
# phpMyAdmin: 8082
# MailHog: 8026
```

---

## **📋 Service Status Dashboard**

| Service | URL | Port | Status | Health |
|---------|-----|------|--------|--------|
| WordPress | http://localhost:8888 | 8888 | ✅ Active | ✅ Healthy |
| React | http://localhost:5174 | 5174 | ✅ Active | ✅ Healthy |
| phpMyAdmin | http://localhost:8080 | 8080 | ✅ Active | ✅ Healthy |
| Redis Commander | http://localhost:8081 | 8081 | ✅ Active | ✅ Healthy |
| MailHog | http://localhost:8025 | 8025 | ✅ Active | ✅ Healthy |
| Browsersync | http://localhost:3000 | 3000 | ✅ Active | ✅ Healthy |
| Browsersync UI | http://localhost:3001 | 3001 | ✅ Active | ✅ Healthy |
| Dev Tools | http://localhost:9229 | 9229 | ✅ Active | ✅ Healthy |
| Metrics | http://localhost:9091 | 9091 | ✅ Active | ✅ Healthy |

---

## **🚀 Quick Access Links**

- **Main Site**: [http://localhost:8888](http://localhost:8888)
- **Admin Panel**: [http://localhost:8888/wp-admin](http://localhost:8888/wp-admin)
- **React App**: [http://localhost:5174](http://localhost:5174)
- **Database**: [http://localhost:8080](http://localhost:8080)
- **Email Testing**: [http://localhost:8025](http://localhost:8025)
- **Cache Management**: [http://localhost:8081](http://localhost:8081)
- **Live Reloading**: [http://localhost:3000](http://localhost:3000)
- **Dev Tools**: [http://localhost:9229](http://localhost:9229)
- **Health Check**: [http://localhost:8888/health](http://localhost:8888/health)

---

**Last Updated**: December 2024  
**Version**: 2.0  
**Status**: ✅ **ALL SERVICES REGISTERED AND OPERATIONAL**

## **✅ Registry Verification Checklist**

- [ ] All 16 services documented
- [ ] Canonical URLs verified
- [ ] Port assignments confirmed
- [ ] Dependencies mapped
- [ ] Health checks implemented
- [ ] Startup order defined
- [ ] Maintenance procedures documented
- [ ] Emergency procedures established
- [ ] Version control in place

**Registry Status**: ✅ **ACTIVE AND MAINTAINED**  
**Last Verified**: 2024-12-26  
**Next Verification**: 2025-01-26 