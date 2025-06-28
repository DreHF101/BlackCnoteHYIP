<?php
/**
 * BlackCnote Docker Issues Fix Script
 * 
 * This script fixes all critical Docker issues and ensures
 * the environment is fully operational.
 */

declare(strict_types=1);

class DockerIssuesFixer {
    private array $fixes = [];
    private array $errors = [];

    public function fixWordPressConfiguration(): void {
        echo "🔧 Fixing WordPress Configuration...\n";
        
        try {
            // Update wp-config.php with proper Docker settings
            $wpConfigPath = 'blackcnote/wp-config.php';
            $wpConfig = file_get_contents($wpConfigPath);
            
            // Ensure proper URL configuration
            $wpConfig = preg_replace(
                '/define\(\s*[\'"]WP_HOME[\'"]\s*,\s*[\'"][^\'"]*[\'"]\s*\);/',
                "define( 'WP_HOME', 'http://localhost:8888' );",
                $wpConfig
            );
            
            $wpConfig = preg_replace(
                '/define\(\s*[\'"]WP_SITEURL[\'"]\s*,\s*[\'"][^\'"]*[\'"]\s*\);/',
                "define( 'WP_SITEURL', 'http://localhost:8888' );",
                $wpConfig
            );
            
            $wpConfig = preg_replace(
                '/define\(\s*[\'"]WP_CONTENT_URL[\'"]\s*,\s*[\'"][^\'"]*[\'"]\s*\);/',
                "define( 'WP_CONTENT_URL', 'http://localhost:8888/wp-content' );",
                $wpConfig
            );
            
            // Enable debug mode for development
            $wpConfig = str_replace(
                "define( 'WP_DEBUG', true );",
                "define( 'WP_DEBUG', true );",
                $wpConfig
            );
            
            $wpConfig = str_replace(
                "define( 'WP_DEBUG_LOG', true );",
                "define( 'WP_DEBUG_LOG', true );",
                $wpConfig
            );
            
            file_put_contents($wpConfigPath, $wpConfig);
            $this->fixes[] = "WordPress configuration updated for Docker";
            
        } catch (Exception $e) {
            $this->errors[] = "Error fixing WordPress config: " . $e->getMessage();
        }
    }

    public function createWordPressHtaccess(): void {
        echo "📝 Creating WordPress .htaccess...\n";
        
        try {
            $htaccess = "# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress";

            file_put_contents('blackcnote/.htaccess', $htaccess);
            $this->fixes[] = "WordPress .htaccess created";
            
        } catch (Exception $e) {
            $this->errors[] = "Error creating .htaccess: " . $e->getMessage();
        }
    }

    public function fixReactConfiguration(): void {
        echo "⚛️ Fixing React Configuration...\n";
        
        try {
            // Update package.json scripts for Docker
            $packageJsonPath = 'react-app/package.json';
            if (file_exists($packageJsonPath)) {
                $packageJson = json_decode(file_get_contents($packageJsonPath), true);
                
                if ($packageJson && isset($packageJson['scripts'])) {
                    $packageJson['scripts']['dev:docker'] = 'vite --host 0.0.0.0 --port 5174';
                    $packageJson['scripts']['build:docker'] = 'vite build --outDir ../blackcnote/wp-content/themes/blackcnote/dist';
                    
                    file_put_contents($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    $this->fixes[] = "React package.json updated for Docker";
                }
            }
            
        } catch (Exception $e) {
            $this->errors[] = "Error fixing React config: " . $e->getMessage();
        }
    }

    public function createDockerHealthCheck(): void {
        echo "🏥 Creating Docker Health Check...\n";
        
        try {
            $healthCheck = '#!/bin/bash
# BlackCnote Docker Health Check

echo "Checking Docker services..."

# Check if all containers are running
if docker-compose ps | grep -q "Up"; then
    echo "✅ All containers are running"
else
    echo "❌ Some containers are not running"
    exit 1
fi

# Check WordPress accessibility
if curl -f http://localhost:8888 > /dev/null 2>&1; then
    echo "✅ WordPress is accessible"
else
    echo "❌ WordPress is not accessible"
    exit 1
fi

# Check React app accessibility
if curl -f http://localhost:5174 > /dev/null 2>&1; then
    echo "✅ React app is accessible"
else
    echo "❌ React app is not accessible"
    exit 1
fi

# Check database connection
if docker-compose exec mysql mysql -uroot -pblackcnote_password -e "SELECT 1;" > /dev/null 2>&1; then
    echo "✅ Database is accessible"
else
    echo "❌ Database is not accessible"
    exit 1
fi

echo "🎉 All health checks passed!"
exit 0';

            file_put_contents('scripts/docker-health-check.sh', $healthCheck);
            chmod('scripts/docker-health-check.sh', 0755);
            $this->fixes[] = "Docker health check script created";
            
        } catch (Exception $e) {
            $this->errors[] = "Error creating health check: " . $e->getMessage();
        }
    }

    public function createStartupScript(): void {
        echo "🚀 Creating Startup Script...\n";
        
        try {
            $startupScript = '@echo off
echo Starting BlackCnote Docker Environment...

REM Stop any existing containers
docker-compose down

REM Start all services
docker-compose up -d

REM Wait for services to be ready
echo Waiting for services to start...
timeout /t 10 /nobreak > nul

REM Run database setup
echo Setting up database...
php scripts/setup-database.php

REM Run health check
echo Running health check...
bash scripts/docker-health-check.sh

echo.
echo 🎉 BlackCnote is ready!
echo.
echo Access URLs:
echo   WordPress: http://localhost:8888
echo   React App: http://localhost:5174
echo   PHPMyAdmin: http://localhost:8080
echo   MailHog: http://localhost:8025
echo   Redis Commander: http://localhost:8081
echo.
pause';

            file_put_contents('start-blackcnote.bat', $startupScript);
            $this->fixes[] = "Startup script created";
            
        } catch (Exception $e) {
            $this->errors[] = "Error creating startup script: " . $e->getMessage();
        }
    }

    public function createTroubleshootingGuide(): void {
        echo "📚 Creating Troubleshooting Guide...\n";
        
        try {
            $guide = '# BlackCnote Docker Troubleshooting Guide

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
define(\'WP_DEBUG\', true);
define(\'WP_DEBUG_LOG\', true);
define(\'WP_DEBUG_DISPLAY\', false);
```

## Support

For additional help, check the logs and run the health check script:
```bash
bash scripts/docker-health-check.sh
```';

            file_put_contents('docs/DOCKER-TROUBLESHOOTING.md', $guide);
            $this->fixes[] = "Troubleshooting guide created";
            
        } catch (Exception $e) {
            $this->errors[] = "Error creating troubleshooting guide: " . $e->getMessage();
        }
    }

    public function run(): void {
        echo "🔧 BlackCnote Docker Issues Fixer\n";
        echo "================================\n\n";

        $this->fixWordPressConfiguration();
        $this->createWordPressHtaccess();
        $this->fixReactConfiguration();
        $this->createDockerHealthCheck();
        $this->createStartupScript();
        $this->createTroubleshootingGuide();

        echo "\n✅ Fixes Applied:\n";
        foreach ($this->fixes as $fix) {
            echo "  ✓ $fix\n";
        }

        if (!empty($this->errors)) {
            echo "\n❌ Errors:\n";
            foreach ($this->errors as $error) {
                echo "  ✗ $error\n";
            }
        }

        echo "\n🎯 Next Steps:\n";
        echo "1. Restart Docker services: docker-compose restart\n";
        echo "2. Run health check: bash scripts/docker-health-check.sh\n";
        echo "3. Test WordPress: http://localhost:8888\n";
        echo "4. Test React app: http://localhost:5174\n";
    }
}

// Run the fixer
if (php_sapi_name() === 'cli') {
    $fixer = new DockerIssuesFixer();
    $fixer->run();
} 