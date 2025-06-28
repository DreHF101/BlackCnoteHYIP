# BlackCnote Docker Troubleshooting Guide

## Common Issues and Solutions

### 1. WordPress Not Accessible
**Symptoms:** Cannot access http://localhost:8888
**Solutions:**
- Check if containers are running: `docker-compose ps`
- Restart WordPress container: `docker-compose restart wordpress`
- Check WordPress logs: `docker-compose logs wordpress`
- Verify wp-config.php has correct URLs

### 2. React App Not Loading
**Symptoms:** Cannot access http://localhost:5174
**Solutions:**
- Check React container: `docker-compose logs react-app`
- Restart React container: `docker-compose restart react-app`
- Verify vite.config.ts proxy settings

### 3. Database Connection Issues
**Symptoms:** Database connection errors
**Solutions:**
- Check MySQL container: `docker-compose logs mysql`
- Run database setup: `php scripts/setup-database.php`
- Verify database credentials in wp-config.php

### 4. File Permission Issues
**Symptoms:** Upload or file access errors
**Solutions:**
- Fix permissions: `chmod -R 755 blackcnote/wp-content/uploads`
- Check Docker volume mounts
- Verify file ownership

### 5. Redirect Loops
**Symptoms:** Infinite redirects in WordPress
**Solutions:**
- Update wp-config.php URLs
- Check .htaccess file
- Clear browser cache
- Verify Apache configuration

## Useful Commands

```bash
# View all logs
docker-compose logs -f

# Restart specific service
docker-compose restart [service-name]

# Rebuild containers
docker-compose build --no-cache

# Reset everything
docker-compose down -v
docker-compose up -d

# Check service status
docker-compose ps

# Access container shell
docker-compose exec [service-name] bash
```

## Debug Mode

Enable debug mode in wp-config.php:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## Support

For additional help, check the logs and run the health check script:
```bash
bash scripts/docker-health-check.sh
```