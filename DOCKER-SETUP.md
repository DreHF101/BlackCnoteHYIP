# BlackCnote Docker Development Environment

## Overview

This Docker setup provides a complete development environment for the BlackCnote WordPress project with multiple services optimized for development and production.

## Service Architecture

### Core Services

| Service | Container Name | Host Port | Internal Port | URL | Purpose |
|---------|----------------|-----------|---------------|-----|---------|
| **Nginx Proxy** | `blackcnote-nginx-proxy` | 8888 | 80 | `http://localhost:8888` | Reverse proxy, SSL termination |
| **WordPress** | `blackcnote-wordpress` | - | 80 | `http://wordpress:80` | WordPress application |
| **MySQL** | `blackcnote-mysql` | 3306 | 3306 | `mysql://localhost:3306` | Database |
| **Redis** | `blackcnote-redis` | 6379 | 6379 | `redis://localhost:6379` | Caching |
| **phpMyAdmin** | `blackcnote-phpmyadmin` | 8080 | 80 | `http://localhost:8080` | Database management |

### Development Services

| Service | Container Name | Host Port | Internal Port | URL | Purpose |
|---------|----------------|-----------|---------------|-----|---------|
| **React Dev Server** | `blackcnote-react` | 5174 | 5174 | `http://localhost:5174` | React development |
| **Browsersync** | `blackcnote-browsersync` | 3000-3001 | 3000-3001 | `http://localhost:3000` | Live reloading |
| **MailHog** | `blackcnote-mailhog` | 8025, 1025 | 8025, 1025 | `http://localhost:8025` | Email testing |
| **Redis Commander** | `blackcnote-redis-commander` | 8081 | 8081 | `http://localhost:8081` | Redis management |
| **Dev Tools** | `blackcnote-dev-tools` | 9229 | 9229 | `http://localhost:9229` | Node.js debugging |
| **File Watcher** | `blackcnote-file-watcher` | - | - | - | File change monitoring |

## Quick Start

### Development Environment

```bash
# Start all services
docker-compose up -d

# Start with development overrides
docker-compose -f docker-compose.yml -f docker-compose.override.yml up -d

# View logs
docker-compose logs -f

# Stop all services
docker-compose down
```

### Production Environment

```bash
# Start production services
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Start with custom domain (update URLs in docker-compose.prod.yml first)
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

## Access URLs

### Development URLs

- **Main Site**: `http://localhost:8888/blackcnote`
- **Admin Panel**: `http://localhost:8888/blackcnote/wp-admin/`
- **WordPress API**: `http://localhost:8888/blackcnote/wp-json/`
- **phpMyAdmin**: `http://localhost:8080`
- **MailHog**: `http://localhost:8025`
- **Redis Commander**: `http://localhost:8081`
- **React Dev Server**: `http://localhost:5174`
- **Browsersync**: `http://localhost:3000`
- **Health Check**: `http://localhost:8888/health`

### Production URLs

- **Main Site**: `https://blackcnote.com`
- **Admin Panel**: `https://blackcnote.com/wp-admin/`
- **Static Assets**: `https://static.blackcnote.com`
- **Health Check**: `https://blackcnote.com/health`

## Configuration Files

### Docker Compose Files

- `docker-compose.yml` - Base configuration
- `docker-compose.override.yml` - Development overrides (auto-loaded)
- `docker-compose.prod.yml` - Production configuration

### Nginx Configurations

- `config/nginx/blackcnote-docker.conf` - Development proxy
- `config/nginx/blackcnote-prod.conf` - Production with SSL
- `config/nginx/blackcnote.conf` - Standalone production

### WordPress Configuration

- `blackcnote/wp-config.php` - WordPress configuration
- Environment variables in Docker Compose override WordPress settings

## Environment Variables

### Development Environment

```yaml
WP_HOME: http://localhost:8888/blackcnote
WP_SITEURL: http://localhost:8888/blackcnote
WP_CONTENT_URL: http://localhost:8888/blackcnote/wp-content
WP_DEBUG: true
WP_DEBUG_LOG: true
WP_CACHE: false
```

### Production Environment

```yaml
WP_HOME: https://blackcnote.com
WP_SITEURL: https://blackcnote.com
WP_CONTENT_URL: https://blackcnote.com/wp-content
WP_DEBUG: false
WP_CACHE: true
FORCE_SSL_ADMIN: true
```

## Database Management

### Access Database

```bash
# Connect to MySQL container
docker exec -it blackcnote-mysql mysql -u root -p

# Access phpMyAdmin
# Open http://localhost:8080 in browser
# Username: root
# Password: blackcnote_password
```

### Database Backup/Restore

```bash
# Backup database
docker exec blackcnote-mysql mysqldump -u root -p blackcnote > backup.sql

# Restore database
docker exec -i blackcnote-mysql mysql -u root -p blackcnote < backup.sql
```

## Development Workflow

### 1. Start Development Environment

```bash
docker-compose up -d
```

### 2. Access Services

- WordPress: `http://localhost:8888/blackcnote`
- React Dev: `http://localhost:5174`
- Database: `http://localhost:8080`

### 3. File Changes

- WordPress files: `./blackcnote/`
- React files: `./react-app/src/`
- Changes are automatically detected and reloaded

### 4. Debugging

- WordPress logs: `docker-compose logs wordpress`
- React logs: `docker-compose logs react-app`
- File watcher logs: `docker-compose logs file-watcher`

## Production Deployment

### 1. Update Configuration

Edit `docker-compose.prod.yml`:
```yaml
WP_HOME: https://yourdomain.com
WP_SITEURL: https://yourdomain.com
```

### 2. SSL Certificates

Place SSL certificates in `./ssl/`:
- `blackcnote.crt` - SSL certificate
- `blackcnote.key` - SSL private key

### 3. Deploy

```bash
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

## Troubleshooting

### Common Issues

1. **Port Conflicts**
   ```bash
   # Check what's using a port
   netstat -ano | findstr :8888
   
   # Kill process using port
   taskkill /PID <PID> /F
   ```

2. **Container Won't Start**
   ```bash
   # Check container logs
   docker-compose logs <service-name>
   
   # Rebuild containers
   docker-compose down
   docker-compose build --no-cache
   docker-compose up -d
   ```

3. **WordPress URL Issues**
   ```bash
   # Update database URLs
   docker exec -it blackcnote-wordpress wp search-replace 'old-url' 'new-url' --allow-root
   ```

4. **Permission Issues**
   ```bash
   # Fix file permissions
   docker exec -it blackcnote-wordpress chown -R www-data:www-data /var/www/html
   ```

### Health Checks

```bash
# Check all services
docker-compose ps

# Test WordPress
curl -I http://localhost:8888/blackcnote

# Test health endpoint
curl http://localhost:8888/health
```

## Performance Optimization

### Development

- File watching with polling enabled
- Debug mode active
- Caching disabled
- Live reload enabled

### Production

- Gzip compression enabled
- Static file caching
- Rate limiting
- SSL/TLS optimization
- Security headers

## Security Features

- Rate limiting on API and login endpoints
- Security headers (XSS, CSRF protection)
- File access restrictions
- SSL/TLS in production
- WordPress security hardening

## Monitoring

### Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f wordpress

# Last 100 lines
docker-compose logs --tail=100 wordpress
```

### Metrics

- Nginx access logs: `./logs/nginx/`
- WordPress debug logs: `./blackcnote/wp-content/debug.log`
- MySQL slow query logs: Available in container
- Redis logs: Available in container

## Backup Strategy

### Automated Backups

```bash
# Database backup
docker exec blackcnote-mysql mysqldump -u root -p blackcnote > backup_$(date +%Y%m%d_%H%M%S).sql

# File backup
tar -czf backup_$(date +%Y%m%d_%H%M%S).tar.gz blackcnote/
```

### Restore

```bash
# Restore database
docker exec -i blackcnote-mysql mysql -u root -p blackcnote < backup.sql

# Restore files
tar -xzf backup.tar.gz
```

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review container logs
3. Verify configuration files
4. Test individual services

## Version History

- **v1.0** - Initial Docker setup
- **v1.1** - Added development overrides
- **v1.2** - Added production configuration
- **v1.3** - Enhanced security and performance
- **v1.4** - Added comprehensive documentation 