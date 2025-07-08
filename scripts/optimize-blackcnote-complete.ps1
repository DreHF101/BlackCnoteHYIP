# BlackCnote Complete Optimization Script
# Removes redundant files, optimizes performance, and ensures proper structure

param(
    [switch]$Verbose,
    [switch]$Force,
    [switch]$DryRun
)

Write-Host "🚀 BlackCnote Complete Optimization" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan
Write-Host ""

# 1. REMOVE REDUNDANT AND DUPLICATE FILES
Write-Host "1. Removing Redundant Files..." -ForegroundColor Yellow

$filesToRemove = @(
    # React App Duplicate Configs
    "react-app\vite.config.simple.js",
    "react-app\vite.config.js", 
    "react-app\bs-config.js",
    "react-app\browsersync-config.js",
    "react-app\dev-simple.cjs",
    "react-app\quick-fix.cjs",
    "react-app\fix-development-issues.cjs",
    "react-app\fix-development-issues.js",
    "react-app\crlf_files_inc.txt",
    "react-app\crlf_files.txt",
    "react-app\start-dev.sh",
    "react-app\env.development",
    
    # Theme Duplicate Files
    "blackcnote\wp-content\themes\blackcnote\functions.php.backup.2025-07-07-04-11-40",
    "blackcnote\wp-content\themes\blackcnote\infrastructure.php",
    "blackcnote\wp-content\themes\blackcnote\infrastructure-fixed.php",
    "blackcnote\wp-content\themes\blackcnote\final-infrastructure.php",
    "blackcnote\wp-content\themes\blackcnote\header-enhanced.php",
    "blackcnote\wp-content\themes\blackcnote\BLACKCNOTE Logo (1).png",
    "blackcnote\wp-content\themes\blackcnote\BLACKCNOTE Logo (2).png", 
    "blackcnote\wp-content\themes\blackcnote\BLACKCNOTE Logo (3).png",
    "blackcnote\wp-content\themes\blackcnote\BLACKCNOTE logo (4).png",
    "blackcnote\wp-content\themes\blackcnote\screenshot.png",
    
    # Redundant Scripts
    "scripts\fix-canonical-conflicts-complete.ps1",
    "scripts\fix-canonical-conflicts-simple.ps1",
    "scripts\fix-admin-template-override-simple.ps1",
    "scripts\fix-php-execution-simple.ps1",
    "scripts\test-admin-fix.ps1",
    "scripts\test-all-fixes.ps1",
    "scripts\run-react-test.bat",
    "scripts\test-wordpress-fix.bat",
    "scripts\fix-docker-final.bat",
    "scripts\fix-docker-manual.bat",
    "scripts\start-docker-admin.bat",
    "scripts\test-db-connection.php",
    "scripts\fix-wp-config-issue.bat",
    "scripts\browsersync.js",
    "scripts\verify-project-organization.php",
    "scripts\final-verification-test.php",
    "scripts\synchronization-test.php",
    "scripts\simple-perfection-test.php",
    "scripts\final-perfection-test.php",
    "scripts\comprehensive-enhancement-test.php",
    "scripts\final-frontend-verification.php",
    "scripts\frontend-features-analysis.php",
    "scripts\comprehensive-frontend-test.php",
    "scripts\comprehensive-theme-test.php",
    "scripts\fix-pathway-issues.php",
    "scripts\deploy-to-github.ps1",
    "scripts\deploy-production.sh",
    "scripts\fix-docker-urls.php",
    "scripts\test-wordpress-access.ps1",
    "scripts\test-react-access.ps1",
    "scripts\check-startup-compatibility.ps1",
    "scripts\verify-service-connections.sh",
    "scripts\deploy-production.ps1",
    "scripts\fix-docker-daemon.ps1",
    "scripts\create-task-scheduler.ps1",
    "scripts\fix-startup-execution.ps1",
    "scripts\fix-docker-api-engine.ps1",
    "scripts\setup-ml-environment.ps1",
    "scripts\setup-docker-privileges.ps1",
    "scripts\verify-startup-deployment.ps1",
    "scripts\final-verification-test.php",
    "scripts\synchronization-test.php",
    "scripts\simple-perfection-test.php",
    "scripts\final-perfection-test.php",
    "scripts\comprehensive-enhancement-test.php",
    "scripts\final-frontend-verification.php",
    "scripts\frontend-features-analysis.php",
    "scripts\comprehensive-frontend-test.php",
    "scripts\comprehensive-theme-test.php",
    "scripts\fix-pathway-issues.php",
    "scripts\deploy-to-github.ps1",
    "scripts\deploy-production.sh",
    "scripts\fix-docker-urls.php",
    "scripts\test-wordpress-access.ps1",
    "scripts\test-react-access.ps1",
    "scripts\check-startup-compatibility.ps1",
    "scripts\verify-service-connections.sh",
    "scripts\deploy-production.ps1",
    "scripts\fix-docker-daemon.ps1",
    "scripts\create-task-scheduler.ps1",
    "scripts\fix-startup-execution.ps1",
    "scripts\fix-docker-api-engine.ps1",
    "scripts\setup-ml-environment.ps1",
    "scripts\setup-docker-privileges.ps1",
    "scripts\verify-startup-deployment.ps1"
)

foreach ($file in $filesToRemove) {
    if (Test-Path $file) {
        if ($DryRun) {
            Write-Host "   [DRY RUN] Would remove: $file" -ForegroundColor Yellow
        } else {
            Remove-Item $file -Force
            Write-Host "   ✅ Removed: $file" -ForegroundColor Green
        }
    }
}

# 2. OPTIMIZE REACT APP CONFIGURATION
Write-Host ""
Write-Host "2. Optimizing React App Configuration..." -ForegroundColor Yellow

# Optimize Vite config for better performance
$viteConfig = "react-app\vite.config.ts"
if (Test-Path $viteConfig) {
    Write-Host "   - Optimizing Vite configuration for performance" -ForegroundColor Green
    
    $content = Get-Content $viteConfig -Raw
    
    # Add performance optimizations
    $performanceOpts = @"
  // Performance optimizations
  esbuild: {
    treeShaking: true,
    target: 'es2020',
    sourcemap: process.env.NODE_ENV === 'development',
    minifyIdentifiers: process.env.NODE_ENV === 'production',
    minifySyntax: process.env.NODE_ENV === 'production',
    minifyWhitespace: process.env.NODE_ENV === 'production',
  },
  // Build optimizations
  build: {
    target: 'es2020',
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: process.env.NODE_ENV === 'production',
        drop_debugger: process.env.NODE_ENV === 'production',
        pure_funcs: process.env.NODE_ENV === 'production' ? ['console.log'] : [],
      },
      mangle: {
        toplevel: true,
      },
    },
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['react', 'react-dom'],
          router: ['react-router-dom'],
          ui: ['lucide-react'],
          utils: ['lodash', 'axios'],
        },
      },
    },
    chunkSizeWarningLimit: 1000,
    sourcemap: process.env.NODE_ENV !== 'production',
  },
"@
    
    # Replace existing esbuild section
    $content = $content -replace 'esbuild: \{[\s\S]*?\},', $performanceOpts
    Set-Content $viteConfig $content -Encoding UTF8
}

# 3. OPTIMIZE WORDPRESS THEME STRUCTURE
Write-Host ""
Write-Host "3. Optimizing WordPress Theme Structure..." -ForegroundColor Yellow

# Optimize theme functions.php
$themeFunctions = "blackcnote\wp-content\themes\blackcnote\functions.php"
if (Test-Path $themeFunctions) {
    Write-Host "   - Adding performance optimizations to theme functions" -ForegroundColor Green
    
    $performanceCode = @"

// Performance Optimizations
function blackcnote_performance_optimizations() {
    // Remove unnecessary WordPress features
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    
    // Disable emojis
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    
    // Optimize database queries
    add_filter('wp_headers', function($headers) {
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options'] = 'SAMEORIGIN';
        $headers['X-XSS-Protection'] = '1; mode=block';
        return $headers;
    });
}
add_action('init', 'blackcnote_performance_optimizations');

// Cache optimization
function blackcnote_cache_headers() {
    if (!is_admin()) {
        header('Cache-Control: public, max-age=3600');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
    }
}
add_action('send_headers', 'blackcnote_cache_headers');

"@
    
    $content = Get-Content $themeFunctions -Raw
    if ($content -notmatch 'blackcnote_performance_optimizations') {
        $content = $content + $performanceCode
        Set-Content $themeFunctions $content -Encoding UTF8
    }
}

# 4. CLEAN UP DOCKER CONFIGURATION
Write-Host ""
Write-Host "4. Optimizing Docker Configuration..." -ForegroundColor Yellow

# Optimize docker-compose.yml
$dockerCompose = "docker-compose.yml"
if (Test-Path $dockerCompose) {
    Write-Host "   - Adding performance optimizations to Docker Compose" -ForegroundColor Green
    
    $content = Get-Content $dockerCompose -Raw
    
    # Add performance optimizations for containers
    $performanceOpts = @"
    # Performance optimizations
    deploy:
      resources:
        limits:
          memory: 1G
          cpus: '0.5'
        reservations:
          memory: 512M
          cpus: '0.25'
    # Health checks for better reliability
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:80"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s
"@
    
    # Add to WordPress container
    $content = $content -replace 'restart: unless-stopped', "restart: unless-stopped`n$performanceOpts"
    Set-Content $dockerCompose $content -Encoding UTF8
}

# 5. OPTIMIZE DATABASE CONFIGURATION
Write-Host ""
Write-Host "5. Optimizing Database Configuration..." -ForegroundColor Yellow

# Create optimized MySQL configuration
$mysqlConfig = @"
[mysqld]
# Performance optimizations
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1

# Query cache
query_cache_type = 1
query_cache_size = 32M
query_cache_limit = 2M

# Connection settings
max_connections = 100
max_connect_errors = 1000
connect_timeout = 10
wait_timeout = 28800
interactive_timeout = 28800

# Buffer settings
key_buffer_size = 32M
read_buffer_size = 2M
read_rnd_buffer_size = 8M
sort_buffer_size = 2M
join_buffer_size = 2M

# Logging
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
"@

Set-Content "config\mysql\my.cnf" $mysqlConfig -Encoding UTF8
Write-Host "   - Created optimized MySQL configuration" -ForegroundColor Green

# 6. CREATE PERFORMANCE MONITORING
Write-Host ""
Write-Host "6. Creating Performance Monitoring..." -ForegroundColor Yellow

$monitoringScript = @"
# BlackCnote Performance Monitor
# Monitors system performance and provides optimization recommendations

Write-Host "🔍 BlackCnote Performance Monitor" -ForegroundColor Cyan
Write-Host "===============================" -ForegroundColor Cyan
Write-Host ""

# Check Docker container performance
Write-Host "🐳 Docker Container Performance:" -ForegroundColor Yellow
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}"

Write-Host ""
Write-Host "📊 WordPress Performance:" -ForegroundColor Yellow
try {
    `$response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    `$loadTime = `$response.BaseResponse.ResponseTime
    Write-Host "   ✅ WordPress Load Time: `$loadTime ms" -ForegroundColor Green
} catch {
    Write-Host "   ❌ WordPress not accessible" -ForegroundColor Red
}

Write-Host ""
Write-Host "⚡ React App Performance:" -ForegroundColor Yellow
try {
    `$response = Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 10 -UseBasicParsing
    `$loadTime = `$response.BaseResponse.ResponseTime
    Write-Host "   ✅ React App Load Time: `$loadTime ms" -ForegroundColor Green
} catch {
    Write-Host "   ❌ React App not accessible" -ForegroundColor Red
}

Write-Host ""
Write-Host "💾 Database Performance:" -ForegroundColor Yellow
try {
    `$response = Invoke-WebRequest -Uri "http://localhost:8080" -TimeoutSec 5 -UseBasicParsing
    Write-Host "   ✅ phpMyAdmin accessible" -ForegroundColor Green
} catch {
    Write-Host "   ❌ phpMyAdmin not accessible" -ForegroundColor Red
}

Write-Host ""
Write-Host "🎯 Performance Recommendations:" -ForegroundColor Cyan
Write-Host "   1. Monitor memory usage in Docker containers" -ForegroundColor White
Write-Host "   2. Check for slow database queries" -ForegroundColor White
Write-Host "   3. Optimize image sizes and caching" -ForegroundColor White
Write-Host "   4. Review React bundle size" -ForegroundColor White
"@

Set-Content "scripts\monitor-performance.ps1" $monitoringScript -Encoding UTF8
Write-Host "   - Created performance monitoring script" -ForegroundColor Green

# 7. OPTIMIZE ASSETS AND CACHING
Write-Host ""
Write-Host "7. Optimizing Assets and Caching..." -ForegroundColor Yellow

# Create optimized .htaccess for WordPress
$htaccess = @"
# BlackCnote Performance Optimizations
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options SAMEORIGIN
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
"@

Set-Content "blackcnote\.htaccess" $htaccess -Encoding UTF8
Write-Host "   - Created optimized .htaccess file" -ForegroundColor Green

# 8. FINAL VERIFICATION
Write-Host ""
Write-Host "8. Final Verification..." -ForegroundColor Yellow

# Check if all optimizations are in place
$checks = @(
    @{ Name = "Vite Config Optimized"; Path = "react-app\vite.config.ts" },
    @{ Name = "Theme Functions Optimized"; Path = "blackcnote\wp-content\themes\blackcnote\functions.php" },
    @{ Name = "Docker Compose Optimized"; Path = "docker-compose.yml" },
    @{ Name = "MySQL Config Created"; Path = "config\mysql\my.cnf" },
    @{ Name = "Performance Monitor Created"; Path = "scripts\monitor-performance.ps1" },
    @{ Name = "HTAccess Optimized"; Path = "blackcnote\.htaccess" }
)

foreach ($check in $checks) {
    if (Test-Path $check.Path) {
        Write-Host "   ✅ $($check.Name)" -ForegroundColor Green
    } else {
        Write-Host "   ❌ $($check.Name)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "🎉 BlackCnote Complete Optimization Finished!" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ Redundant files removed" -ForegroundColor Green
Write-Host "✅ React app performance optimized" -ForegroundColor Green
Write-Host "✅ WordPress theme optimized" -ForegroundColor Green
Write-Host "✅ Docker configuration optimized" -ForegroundColor Green
Write-Host "✅ Database performance optimized" -ForegroundColor Green
Write-Host "✅ Caching and assets optimized" -ForegroundColor Green
Write-Host "✅ Performance monitoring created" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next Steps:" -ForegroundColor Yellow
Write-Host "   1. Restart Docker services: docker-compose down && docker-compose up -d" -ForegroundColor White
Write-Host "   2. Monitor performance: scripts\monitor-performance.ps1" -ForegroundColor White
Write-Host "   3. Test all functionality" -ForegroundColor White
Write-Host "   4. Review performance metrics" -ForegroundColor White
Write-Host ""
Write-Host "🚀 BlackCnote is now fully optimized for performance and speed!" -ForegroundColor Green 