# BlackCnote Docker Issues and Solutions

## Current Issues Identified

### 1. WordPress Redirect Loop (CRITICAL)
**Problem:** WordPress container has infinite redirect loops when accessing `/blackcnote/` paths
**Symptoms:** 
- `AH00124: Request exceeded the limit of 10 internal redirects`
- 500 errors on WordPress admin access
- WordPress not accessible via browser

**Root Cause:** WordPress configuration mismatch between Docker and localhost settings

### 2. React App Connection Failures (CRITICAL)
**Problem:** React app cannot connect to WordPress backend
**Symptoms:**
- `Error: connect ECONNREFUSED 127.0.0.1:8888`
- Continuous proxy errors in React logs
- React app cannot communicate with WordPress

**Root Cause:** React app configured to connect to localhost instead of Docker service

### 3. WordPress Configuration Issues (HIGH)
**Problem:** WordPress wp-config.php has mixed localhost/Docker settings
**Symptoms:**
- Database connection works (configured for Docker)
- URLs configured for Docker but redirects failing
- Apache configuration conflicts

## Solutions

### Solution 1: Fix WordPress Configuration

#### Update wp-config.php
```php
// Remove conflicting URL configurations
// define( 'WP_HOME', 'http://localhost:8888/blackcnote' );
// define( 'WP_SITEURL', 'http://localhost:8888/blackcnote' );

// Let WordPress auto-detect URLs
define( 'WP_HOME', 'http://localhost:8888' );
define( 'WP_SITEURL', 'http://localhost:8888' );

// Ensure proper database configuration
define( 'DB_HOST', 'mysql' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'blackcnote_password' );
define( 'DB_NAME', 'blackcnote' );
```

#### Update Apache Configuration
Create `config/apache/000-default.conf`:
```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Handle WordPress pretty permalinks
    <Directory /var/www/html>
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.php$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.php [L]
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### Solution 2: Fix React App Configuration

#### Update React Vite Configuration
Update `react-app/vite.config.ts`:
```typescript
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    port: 5174,
    proxy: {
      // Proxy to WordPress container
      '/wp-admin/admin-ajax.php': {
        target: 'http://wordpress:80',
        changeOrigin: true,
        secure: false
      },
      '/wp-json': {
        target: 'http://wordpress:80',
        changeOrigin: true,
        secure: false
      },
      '/wp-content': {
        target: 'http://wordpress:80',
        changeOrigin: true,
        secure: false
      }
    }
  }
})
```

#### Update React Environment Variables
Update `react-app/.env.development`:
```env
VITE_WORDPRESS_URL=http://localhost:8888
VITE_API_BASE_URL=http://localhost:8888/wp-json
VITE_AJAX_URL=http://localhost:8888/wp-admin/admin-ajax.php
```

### Solution 3: Update Docker Compose Configuration

#### Fix WordPress Service
Update `docker-compose.yml` WordPress service:
```yaml
wordpress:
  image: wordpress:6.8-apache
  container_name: blackcnote-wordpress
  restart: unless-stopped
  environment:
    WORDPRESS_DB_HOST: mysql
    WORDPRESS_DB_USER: root
    WORDPRESS_DB_PASSWORD: blackcnote_password
    WORDPRESS_DB_NAME: blackcnote
    WORDPRESS_DEBUG: 1
    WORDPRESS_CONFIG_EXTRA: |
      define('WP_HOME', 'http://localhost:8888');
      define('WP_SITEURL', 'http://localhost:8888');
      define('WP_DEBUG', true);
      define('WP_DEBUG_LOG', true);
      define('WP_DEBUG_DISPLAY', false);
  volumes:
    - ./blackcnote:/var/www/html
    - ./config/apache:/etc/apache2/sites-available
    - ./logs:/var/www/html/logs
  ports:
    - "8888:80"
  depends_on:
    - mysql
  networks:
    - blackcnote-network
```

#### Fix React Service
Update `docker-compose.yml` React service:
```yaml
react-app:
  build:
    context: ./react-app
    dockerfile: Dockerfile
  container_name: blackcnote-react
  restart: unless-stopped
  environment:
    NODE_ENV: development
    VITE_WORDPRESS_URL: http://wordpress:80
    VITE_API_BASE_URL: http://wordpress:80/wp-json
    VITE_AJAX_URL: http://wordpress:80/wp-admin/admin-ajax.php
  volumes:
    - ./react-app:/app
    - /app/node_modules
  ports:
    - "5174:5174"
  depends_on:
    - wordpress
  networks:
    - blackcnote-network
```

### Solution 4: Create WordPress Installation Script

Create `scripts/install-wordpress.php`:
```php
<?php
/**
 * WordPress Installation Script for Docker
 */

// Check if WordPress is already installed
if (file_exists('blackcnote/wp-config.php')) {
    echo "WordPress configuration already exists.\n";
    exit(0);
}

// Copy WordPress files
echo "Installing WordPress...\n";
system('cp -r blackcnote/* /var/www/html/');

// Set proper permissions
system('chown -R www-data:www-data /var/www/html/');
system('chmod -R 755 /var/www/html/');

// Create wp-config.php
$config = file_get_contents('blackcnote/wp-config.php');
$config = str_replace(
    ["define( 'WP_HOME', 'http://localhost:8888/blackcnote' );", "define( 'WP_SITEURL', 'http://localhost:8888/blackcnote' );"],
    ["define( 'WP_HOME', 'http://localhost:8888' );", "define( 'WP_SITEURL', 'http://localhost:8888' );"],
    $config
);
file_put_contents('/var/www/html/wp-config.php', $config);

echo "WordPress installation completed.\n";
```

## Implementation Steps

### Step 1: Stop Current Containers
```bash
docker-compose down
```

### Step 2: Apply Configuration Fixes
1. Update `blackcnote/wp-config.php`
2. Create Apache configuration
3. Update React Vite configuration
4. Update Docker Compose files

### Step 3: Rebuild and Start
```bash
docker-compose build --no-cache
docker-compose up -d
```

### Step 4: Install WordPress
```bash
docker exec blackcnote-wordpress php /var/www/html/scripts/install-wordpress.php
```

### Step 5: Test All Services
```bash
# Test WordPress
curl -I http://localhost:8888

# Test React App
curl -I http://localhost:5174

# Test API
curl http://localhost:8888/wp-json
```

## Expected Results

After implementing these fixes:

1. **WordPress** should be accessible at `http://localhost:8888`
2. **React App** should be accessible at `http://localhost:5174`
3. **API calls** should work between React and WordPress
4. **No more redirect loops** or connection errors
5. **All services** should communicate properly within Docker network

## Verification Commands

```bash
# Check container status
docker-compose ps

# Check WordPress logs
docker-compose logs wordpress

# Check React logs
docker-compose logs react-app

# Test database connection
docker exec blackcnote-mysql mysql -u root -pblackcnote_password -e "USE blackcnote; SHOW TABLES;"

# Test WordPress API
curl http://localhost:8888/wp-json/wp/v2/posts
```

## Troubleshooting

### If WordPress still has redirect issues:
1. Clear browser cache
2. Check Apache error logs: `docker-compose logs wordpress`
3. Verify .htaccess file exists and is readable
4. Check file permissions

### If React still can't connect:
1. Verify Docker network: `docker network ls`
2. Check container IPs: `docker inspect blackcnote-wordpress`
3. Test internal connectivity: `docker exec blackcnote-react ping wordpress`

### If database connection fails:
1. Check MySQL container: `docker-compose logs mysql`
2. Verify database exists: `docker exec blackcnote-mysql mysql -u root -pblackcnote_password -e "SHOW DATABASES;"`
3. Check WordPress database settings in wp-config.php 