# BlackCnote Service URLs Registry

## 🚨 **CANONICAL SERVICE URLS - OFFICIAL REGISTRY** 🚨

**This registry contains ALL canonical localhost URLs for BlackCnote services. All development, testing, and deployment MUST use these exact URLs.**

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

## **🏗️ Core Application Services**

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
- **Purpose**: WordPress administration interface
- **Dependencies**: WordPress Frontend
- **Health Check**: `http://localhost:8888/wp-admin/admin-ajax.php`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **3. WordPress REST API**
- **Canonical URL**: `http://localhost:8888/wp-json/`
- **Port**: 8888
- **Container**: `blackcnote-wordpress`
- **Purpose**: WordPress REST API endpoints
- **Dependencies**: WordPress Frontend
- **Health Check**: `http://localhost:8888/wp-json/wp/v2/`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **4. React Development Server**
- **Canonical URL**: `http://localhost:5174`
- **Port**: 5174
- **Container**: `blackcnote-react`
- **Purpose**: React app with hot reload and live editing
- **Dependencies**: Node.js, Vite
- **Health Check**: `http://localhost:5174/`
- **Status**: ✅ **CANONICAL - REQUIRED**

---

## **🗄️ Database & Management Services**

### **5. phpMyAdmin**
- **Canonical URL**: `http://localhost:8080`
- **Port**: 8080
- **Container**: `blackcnote-phpmyadmin`
- **Purpose**: Database management interface
- **Dependencies**: MySQL
- **Health Check**: `http://localhost:8080/`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **6. MySQL Database**
- **Canonical URL**: `mysql://localhost:3306`
- **Port**: 3306
- **Container**: `blackcnote-mysql`
- **Purpose**: WordPress database
- **Dependencies**: None
- **Health Check**: `mysql -h localhost -P 3306 -u root -p`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **7. Redis Cache**
- **Canonical URL**: `redis://localhost:6379`
- **Port**: 6379
- **Container**: `blackcnote-redis`
- **Purpose**: Caching service
- **Dependencies**: None
- **Health Check**: `redis-cli -h localhost -p 6379 ping`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **8. Redis Commander**
- **Canonical URL**: `http://localhost:8081`
- **Port**: 8081
- **Container**: `blackcnote-redis-commander`
- **Purpose**: Redis management UI
- **Dependencies**: Redis
- **Health Check**: `http://localhost:8081/`
- **Status**: ✅ **CANONICAL - REQUIRED**

---

## **🛠️ Development & Testing Services**

### **9. Browsersync**
- **Canonical URL**: `http://localhost:3000`
- **Port**: 3000
- **Container**: `blackcnote-browsersync`
- **Purpose**: Live reloading proxy
- **Dependencies**: WordPress, React
- **Health Check**: `http://localhost:3000/`
- **Status**: ✅ **CANONICAL - REQUIRED**

### **10. Browsersync UI**
- **Canonical URL**: `http://localhost:3001`
- **Port**: 3001
- **Container**: `blackcnote-browsersync`
- **Purpose**: Browsersync control panel
- **Dependencies**: Browsersync
- **Health Check**: `http://localhost:3001/`
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
- **Purpose**: Node.js debugging
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
    
    if curl -f -s "$url" > /dev/null 2>&1; then
        echo "✅ $name: UP"
    else
        echo "❌ $name: DOWN"
    fi
done

echo ""
echo "Database Services:"
if mysql -h localhost -P 3306 -u root -pblackcnote_password -e "SELECT 1;" > /dev/null 2>&1; then
    echo "✅ MySQL: UP"
else
    echo "❌ MySQL: DOWN"
fi

if redis-cli -h localhost -p 6379 ping > /dev/null 2>&1; then
    echo "✅ Redis: UP"
else
    echo "❌ Redis: DOWN"
fi
```

### **PHP Health Check Function**

```php
function blackcnote_verify_services() {
    $services = [
        'wordpress' => [
            'url' => 'http://localhost:8888/health',
            'name' => 'WordPress',
            'required' => true
        ],
        'react' => [
            'url' => 'http://localhost:5174',
            'name' => 'React App',
            'required' => true
        ],
        'phpmyadmin' => [
            'url' => 'http://localhost:8080',
            'name' => 'phpMyAdmin',
            'required' => true
        ],
        'mailhog' => [
            'url' => 'http://localhost:8025',
            'name' => 'MailHog',
            'required' => true
        ],
        'redis_commander' => [
            'url' => 'http://localhost:8081',
            'name' => 'Redis Commander',
            'required' => true
        ],
        'browsersync' => [
            'url' => 'http://localhost:3000',
            'name' => 'Browsersync',
            'required' => true
        ],
        'dev_tools' => [
            'url' => 'http://localhost:9229',
            'name' => 'Dev Tools',
            'required' => false
        ],
        'metrics_exporter' => [
            'url' => 'http://localhost:9091',
            'name' => 'Metrics Exporter',
            'required' => false
        ]
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
# Redis Commander: 8083
```

---

## **📋 Registry Maintenance**

### **Update Procedures**

1. **Add New Service**:
   - Add service to appropriate category
   - Define canonical URL and port
   - Document dependencies
   - Add health check endpoint
   - Update startup order

2. **Modify Existing Service**:
   - Update canonical URL if changed
   - Modify dependencies if needed
   - Update health check endpoint
   - Test connectivity

3. **Remove Service**:
   - Mark as deprecated
   - Remove from startup order
   - Update dependencies
   - Clean up references

### **Version Control**

- **Registry Version**: 1.0
- **Last Updated**: 2024-12-26
- **Next Review**: 2025-01-26
- **Maintainer**: BlackCnote Development Team

---

## **🚨 Emergency Procedures**

### **Service Outage Response**

1. **Check service status**: `docker ps -a`
2. **View service logs**: `docker logs [container-name]`
3. **Restart service**: `docker-compose restart [service-name]`
4. **Verify connectivity**: Run health check script
5. **Update registry**: Document any URL changes

### **Port Conflict Resolution**

1. **Identify conflicting process**: `netstat -tulpn | grep [port]`
2. **Stop conflicting service**: `sudo systemctl stop [service]`
3. **Kill process if needed**: `sudo kill -9 [pid]`
4. **Restart BlackCnote services**: `docker-compose up -d`

---

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