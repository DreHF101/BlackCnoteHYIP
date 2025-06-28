# 🐳 BlackCnote Docker Live Editing Guide

## 🚀 Quick Start

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop all services
docker-compose down
```

## 📝 Live Editing Workflow

### WordPress Development
- **Theme Files**: Edit `./blackcnote/wp-content/themes/` - changes reflect immediately
- **Plugin Files**: Edit `./blackcnote/wp-content/plugins/` - changes reflect immediately
- **MU Plugins**: Edit `./blackcnote/wp-content/mu-plugins/` - changes reflect immediately
- **Core Files**: Edit `./blackcnote/` - changes reflect immediately

### React Development
- **Source Files**: Edit `./react-app/src/` - hot reloading enabled
- **Public Files**: Edit `./react-app/public/` - changes reflect immediately
- **Package.json**: Edit `./react-app/package.json` - restart container to apply

## 🔧 Development Commands

### WordPress
```bash
# Access WordPress container
docker exec -it blackcnote-wordpress bash

# View WordPress logs
docker logs blackcnote-wordpress

# Install WordPress plugins
docker exec -it blackcnote-wordpress wp plugin install plugin-name --activate

# Update WordPress
docker exec -it blackcnote-wordpress wp core update
```

### React
```bash
# Access React container
docker exec -it blackcnote-react sh

# Install new npm packages
docker exec -it blackcnote-react npm install package-name

# Rebuild React container
docker-compose build react-app
docker-compose up -d react-app
```

### Database
```bash
# Access MySQL
docker exec -it blackcnote-mysql mysql -u root -p

# Backup database
docker exec blackcnote-mysql mysqldump -u root -p blackcnote > backup.sql

# Restore database
docker exec -i blackcnote-mysql mysql -u root -p blackcnote < backup.sql
```

## 🌐 Access Points

- **WordPress**: http://localhost:8888
- **React App**: http://localhost:5174
- **PHPMyAdmin**: http://localhost:8080
- **MailHog**: http://localhost:8025
- **Redis**: localhost:6379

## 🔄 Live Editing Features

### ✅ What Works with Live Editing:
- PHP files (themes, plugins, core)
- CSS/SCSS files
- JavaScript files
- HTML templates
- Configuration files
- React components
- React styles
- Package.json (with restart)

### ⚠️ What Requires Restart:
- Dockerfile changes
- docker-compose.yml changes
- New system packages
- Major configuration changes

## 🛠️ Troubleshooting

### Container Issues
```bash
# Rebuild all containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# Reset volumes (WARNING: deletes data)
docker-compose down -v
docker-compose up -d
```

### Permission Issues
```bash
# Fix WordPress permissions
docker exec -it blackcnote-wordpress chown -R www-data:www-data /var/www/html

# Fix upload directory
docker exec -it blackcnote-wordpress chmod -R 777 /var/www/html/wp-content/uploads
```

### Performance Issues
```bash
# View resource usage
docker stats

# Clean up unused resources
docker system prune -a
```

## 📊 Monitoring

```bash
# View all container status
docker-compose ps

# View real-time logs
docker-compose logs -f wordpress
docker-compose logs -f react-app
docker-compose logs -f mysql

# Monitor resource usage
docker stats --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}"
```

## 🔐 Security Notes

- Default passwords are for development only
- Change passwords for production
- Use environment variables for sensitive data
- Regularly update Docker images 