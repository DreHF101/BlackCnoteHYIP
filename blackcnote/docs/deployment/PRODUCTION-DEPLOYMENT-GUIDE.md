# BlackCnote Production Deployment Guide

## Overview

This guide provides comprehensive instructions for deploying the BlackCnote application to production. The deployment includes WordPress, React frontend, HYIPLab plugin, monitoring, and all supporting services.

## Prerequisites

### System Requirements
- **OS**: Ubuntu 20.04+ / CentOS 8+ / Windows Server 2019+
- **CPU**: 4+ cores recommended
- **RAM**: 8GB+ minimum, 16GB+ recommended
- **Storage**: 50GB+ available space
- **Network**: Stable internet connection

### Software Requirements
- Docker 20.10+
- Docker Compose 2.0+
- Git 2.30+
- OpenSSL (for SSL certificates)

## Pre-Deployment Checklist

### 1. Environment Preparation
- [ ] Server access and permissions verified
- [ ] Domain DNS configured
- [ ] SSL certificates obtained
- [ ] Firewall rules configured
- [ ] Backup strategy planned

### 2. Security Preparation
- [ ] Strong passwords generated
- [ ] SSH keys configured
- [ ] Firewall enabled
- [ ] Security groups configured
- [ ] SSL certificates ready

### 3. Monitoring Setup
- [ ] Prometheus configuration ready
- [ ] Grafana dashboards prepared
- [ ] Alert rules configured
- [ ] Notification channels set up

## Deployment Process

### Step 1: Clone and Prepare Repository

```bash
# Clone the repository
git clone https://github.com/your-org/blackcnote.git
cd blackcnote

# Create necessary directories
mkdir -p backups logs ssl monitoring/grafana/dashboards monitoring/grafana/datasources
```

### Step 2: Configure Environment

#### Update Domain Configuration
Edit `config/nginx/blackcnote-prod.conf`:
```nginx
server_name your-domain.com www.your-domain.com;
```

#### Configure SSL Certificates
Place your SSL certificates in the `ssl/` directory:
- `ssl/blackcnote.crt` - Certificate file
- `ssl/blackcnote.key` - Private key file

#### Update WordPress Configuration
Edit `wp-config.php` for production:
```php
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);
```

### Step 3: Deploy with Docker Compose

#### Option A: Automated Deployment (Recommended)
```bash
# Windows PowerShell
.\scripts\deployment\deploy-production.ps1 -Environment production -Domain your-domain.com

# Linux/Mac
./scripts/deployment/deploy-production.sh production your-domain.com
```

#### Option B: Manual Deployment
```bash
# Stop existing containers
docker-compose down --remove-orphans

# Build and start production services
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

# Verify services are running
docker-compose ps
```

### Step 4: Post-Deployment Configuration

#### WordPress Setup
1. Access WordPress admin: `https://your-domain.com/wp-admin`
2. Complete WordPress installation
3. Configure permalinks
4. Set up user roles and permissions

#### HYIPLab Plugin Configuration
1. Activate HYIPLab plugin
2. Configure payment gateways
3. Set up investment plans
4. Configure email notifications

#### React Frontend Setup
1. Build React application:
   ```bash
   cd react-app
   npm run build
   ```
2. Configure API endpoints
3. Test frontend functionality

### Step 5: Monitoring Configuration

#### Access Monitoring Dashboards
- **Prometheus**: `http://your-domain.com:9090`
- **Grafana**: `http://your-domain.com:3000` (admin/admin)
- **AlertManager**: `http://your-domain.com:9093`

#### Configure Grafana Dashboards
1. Import the BlackCnote dashboard from `monitoring/grafana/dashboards/`
2. Configure data sources
3. Set up alerting rules

#### Set Up Notifications
1. Configure email notifications in AlertManager
2. Set up Slack/Discord webhooks
3. Test alert delivery

## Production Configuration

### Performance Optimization

#### WordPress Optimization
```php
// Add to wp-config.php
define('WP_CACHE', true);
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '1G');

// Enable object caching
define('WP_REDIS_HOST', 'redis');
define('WP_REDIS_PORT', 6379);
```

#### Nginx Optimization
```nginx
# Add to nginx configuration
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

# Enable caching
location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

#### Database Optimization
```sql
-- Optimize MySQL settings
SET GLOBAL innodb_buffer_pool_size = 1073741824; -- 1GB
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL max_connections = 200;
```

### Security Hardening

#### WordPress Security
1. **Limit Login Attempts**: Install security plugin
2. **Two-Factor Authentication**: Enable for admin users
3. **File Permissions**: Set correct file permissions
4. **Database Security**: Use strong passwords, limit access

#### Server Security
1. **Firewall Configuration**:
   ```bash
   # Allow only necessary ports
   ufw allow 22/tcp    # SSH
   ufw allow 80/tcp    # HTTP
   ufw allow 443/tcp   # HTTPS
   ufw allow 8888/tcp  # WordPress
   ufw allow 3000/tcp  # Grafana
   ufw allow 9090/tcp  # Prometheus
   ```

2. **SSL/TLS Configuration**:
   ```nginx
   # Force HTTPS
   if ($scheme != "https") {
       return 301 https://$server_name$request_uri;
   }
   ```

3. **Security Headers**:
   ```nginx
   add_header X-Frame-Options "SAMEORIGIN" always;
   add_header X-XSS-Protection "1; mode=block" always;
   add_header X-Content-Type-Options "nosniff" always;
   add_header Referrer-Policy "no-referrer-when-downgrade" always;
   add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
   ```

## Monitoring and Maintenance

### Health Checks

#### Automated Health Checks
```bash
# Check service health
docker-compose ps

# Check application health
curl -f http://your-domain.com/health

# Check monitoring services
curl -f http://your-domain.com:9090/-/healthy
curl -f http://your-domain.com:3000/api/health
```

#### Manual Health Checks
1. **WordPress**: Check admin panel accessibility
2. **Database**: Verify connection and performance
3. **Cache**: Check Redis connectivity
4. **Frontend**: Test React application
5. **Plugin**: Verify HYIPLab functionality

### Backup Strategy

#### Automated Backups
```bash
# Database backup
docker exec blackcnote_mysql_1 mysqldump -u root -pblackcnote_password blackcnote > backup-$(date +%Y%m%d).sql

# Files backup
tar -czf backup-files-$(date +%Y%m%d).tar.gz blackcnote/ config/ ssl/
```

#### Backup Schedule
- **Database**: Daily automated backups
- **Files**: Weekly full backups
- **Configuration**: Monthly configuration backups
- **SSL Certificates**: Backup before renewal

### Log Management

#### Log Rotation
```bash
# Configure log rotation
sudo logrotate -f /etc/logrotate.d/blackcnote
```

#### Log Monitoring
- Monitor error logs for issues
- Set up log aggregation
- Configure log retention policies

## Troubleshooting

### Common Issues

#### Service Won't Start
```bash
# Check logs
docker-compose logs [service-name]

# Check resource usage
docker stats

# Restart services
docker-compose restart [service-name]
```

#### Performance Issues
1. **Check resource usage**: `docker stats`
2. **Monitor database queries**: Enable slow query log
3. **Check cache hit rates**: Monitor Redis metrics
4. **Analyze network**: Check bandwidth usage

#### Security Issues
1. **Check access logs**: Monitor for suspicious activity
2. **Verify SSL**: Test certificate validity
3. **Review permissions**: Check file and directory permissions
4. **Monitor alerts**: Check Grafana and AlertManager

### Emergency Procedures

#### Service Recovery
```bash
# Restart all services
docker-compose restart

# Restore from backup
docker exec -i blackcnote_mysql_1 mysql -u root -pblackcnote_password blackcnote < backup.sql
```

#### Rollback Procedure
```bash
# Stop current deployment
docker-compose down

# Restore previous version
git checkout [previous-version]
docker-compose up -d
```

## Maintenance Schedule

### Daily Tasks
- [ ] Check service health
- [ ] Review error logs
- [ ] Monitor resource usage
- [ ] Verify backup completion

### Weekly Tasks
- [ ] Update WordPress and plugins
- [ ] Review security logs
- [ ] Check SSL certificate expiration
- [ ] Analyze performance metrics

### Monthly Tasks
- [ ] Full system backup
- [ ] Security audit
- [ ] Performance optimization review
- [ ] Update documentation

### Quarterly Tasks
- [ ] Major version updates
- [ ] Security penetration testing
- [ ] Disaster recovery testing
- [ ] Capacity planning review

## Support and Resources

### Documentation
- [WordPress Codex](https://codex.wordpress.org/)
- [Docker Documentation](https://docs.docker.com/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [Prometheus Documentation](https://prometheus.io/docs/)

### Monitoring Tools
- **Grafana**: Dashboard configuration and alerting
- **Prometheus**: Metrics collection and storage
- **AlertManager**: Alert routing and notification
- **Node Exporter**: System metrics collection

### Security Resources
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [WordPress Security](https://wordpress.org/support/article/hardening-wordpress/)
- [SSL/TLS Best Practices](https://ssl-config.mozilla.org/)

## Conclusion

This production deployment guide provides a comprehensive approach to deploying and maintaining the BlackCnote application. Follow these guidelines to ensure a secure, performant, and reliable production environment.

For additional support or questions, refer to the troubleshooting section or contact the development team. 