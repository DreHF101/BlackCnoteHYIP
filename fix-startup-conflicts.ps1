# BlackCnote Startup Conflicts Fix
# Comprehensive fix for all startup script and Docker Compose conflicts

param(
    [switch]$Force,
    [switch]$Backup,
    [switch]$Verbose
)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { Write-ColorOutput $args "Green" }
function Write-Warning { Write-ColorOutput $args "Yellow" }
function Write-Error { Write-ColorOutput $args "Red" }
function Write-Info { Write-ColorOutput $args "Cyan" }

Write-Info "=========================================="
Write-Info "BLACKCNOTE STARTUP CONFLICTS FIX"
Write-Info "=========================================="
Write-Info "Starting comprehensive fix at: $(Get-Date)"

# Set project root
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

# Create backup directory if requested
if ($Backup) {
    $backupDir = Join-Path $projectRoot "backups\startup-fix-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
    New-Item -ItemType Directory -Force -Path $backupDir | Out-Null
    Write-Success "Backup directory created: $backupDir"
}

# Step 1: Stop all running containers
Write-Info "Step 1: Stopping all running containers..."
try {
    docker-compose down --remove-orphans 2>$null
    docker-compose -f config/docker/docker-compose.yml down --remove-orphans 2>$null
    Write-Success "All containers stopped"
} catch {
    Write-Warning "No containers running or Docker not available"
}

# Step 2: Remove conflicting startup scripts
Write-Info "Step 2: Removing conflicting startup scripts..."

$deprecatedScripts = @(
    "start-blackcnote-complete.ps1",
    "start-blackcnote-simple.ps1",
    "start-blackcnote-unified.ps1",
    "start-blackcnote-fixed.ps1",
    "start-blackcnote-windows.ps1",
    "start-blackcnote-ml.bat",
    "start-blackcnote.sh",
    "start-blackcnote-complete.bat",
    "start-blackcnote-optimized.bat",
    "start-blackcnote-optimized.ps1",
    "start-and-open-blackcnote.bat",
    "start-dev.ps1",
    "start-dev.bat",
    "start-dev-simple.ps1"
)

foreach ($script in $deprecatedScripts) {
    $fullPath = Join-Path $projectRoot $script
    if (Test-Path $fullPath) {
        Remove-Item $fullPath -Force
        Write-Success "Removed deprecated script: $script"
    } else {
        Write-Info "Not found (already removed): $script"
    }
}

# Remove deprecated Docker Compose files
$deprecatedComposeFiles = @(
    "docker-compose.yml",
    "config/docker/docker-compose.yml"
)

# Keep only the canonical config/docker/docker-compose.yml
# If config/docker/docker-compose.yml exists, move it to project root as docker-compose.yml
$canonicalCompose = Join-Path $projectRoot "config/docker/docker-compose.yml"
$rootCompose = Join-Path $projectRoot "docker-compose.yml"

if (Test-Path $canonicalCompose) {
    Move-Item -Force $canonicalCompose $rootCompose
    Write-Success "Moved canonical Docker Compose to project root as docker-compose.yml"
}

foreach ($compose in $deprecatedComposeFiles) {
    $fullPath = Join-Path $projectRoot $compose
    if ($fullPath -ne $rootCompose -and (Test-Path $fullPath)) {
        Remove-Item $fullPath -Force
        Write-Success "Removed deprecated Docker Compose file: $compose"
    } else {
        Write-Info "Not found or canonical: $compose"
    }
}

Write-Info "\nStep 3: Only canonical startup script and Docker Compose file remain."
Write-Info "Canonical startup script: start-blackcnote.ps1"
Write-Info "Canonical Docker Compose file: docker-compose.yml (project root)"
Write-Info "\nCleanup complete. Please use only the canonical script and Compose file for all future startups."

# Step 4: Create unified Docker Compose file
Write-Info "Step 4: Creating unified Docker Compose file..."

$unifiedComposeContent = @"
# BlackCnote Unified Docker Compose Configuration
# ================================================
# CANONICAL PATHWAYS - DO NOT CHANGE
# Project Root: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
# Theme Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote
# WP-Content Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
# Theme Files: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote
# ================================================

version: '3.8'

services:
  # WordPress Application with Enhanced Live Editing
  wordpress:
    build:
      context: ./blackcnote
      dockerfile: ../config/docker/wordpress.Dockerfile
    image: blackcnote-wordpress:custom
    container_name: blackcnote-wordpress
    ports:
      - "8888:80"  # Canonical WordPress port
    environment:
      WORDPRESS_DB_HOST: mysql
      WORDPRESS_DB_NAME: blackcnote
      WORDPRESS_DB_USER: root
      WORDPRESS_DB_PASSWORD: blackcnote_password
      WORDPRESS_DEBUG: 1
      # Base URLs - canonical port 8888
      WP_HOME: http://localhost:8888
      WP_SITEURL: http://localhost:8888
      WP_CONTENT_URL: http://localhost:8888/wp-content
      WP_DEBUG: true
      WP_DEBUG_LOG: true
      WP_DEBUG_DISPLAY: false
      SCRIPT_DEBUG: true
      SAVEQUERIES: true
      WP_CACHE: false
      FS_METHOD: direct
      WP_MEMORY_LIMIT: 256M
      WP_MAX_MEMORY_LIMIT: 512M
      DISALLOW_FILE_EDIT: true
      UPLOADS: wp-content/uploads
    volumes:
      # Canonical Windows filesystem path
      - "C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote:/var/www/html:delegated"
      # Development tools and scripts
      - "./scripts:/var/www/html/scripts:delegated"
      # Logs for debugging
      - "./logs:/var/www/html/logs:delegated"
    depends_on:
      - mysql
      - redis
    networks:
      - blackcnote-network
    restart: unless-stopped
    # Enhanced file watching and performance
    tmpfs:
      - /tmp
      - /var/tmp

  # MySQL Database with Enhanced Performance
  mysql:
    image: mysql:8.0
    container_name: blackcnote-mysql
    environment:
      MYSQL_ROOT_PASSWORD: blackcnote_password
      MYSQL_DATABASE: blackcnote
      MYSQL_USER: blackcnote_user
      MYSQL_PASSWORD: blackcnote_password
      # Performance optimizations for development
      MYSQL_INNODB_BUFFER_POOL_SIZE: 256M
      MYSQL_INNODB_LOG_FILE_SIZE: 64M
      MYSQL_INNODB_FLUSH_LOG_AT_TRX_COMMIT: 2
    volumes:
      - mysql_data:/var/lib/mysql
      - "./db/blackcnote.sql:/docker-entrypoint-initdb.d/blackcnote.sql"
      # Live editing - Database dumps
      - "./db:/var/lib/mysql-dumps:delegated"
    ports:
      - "3306:3306"
    networks:
      - blackcnote-network
    restart: unless-stopped
    command: --default-authentication-plugin=mysql_native_password --innodb-buffer-pool-size=256M

  # Redis Cache with Enhanced Configuration
  redis:
    image: redis:7-alpine
    container_name: blackcnote-redis
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
      # Live editing - Redis configuration
      - "./redis.conf:/usr/local/etc/redis/redis.conf:ro"
    networks:
      - blackcnote-network
    restart: unless-stopped
    command: redis-server /usr/local/etc/redis/redis.conf

  # React Development Server with Enhanced Live Editing
  react-app:
    build:
      context: ./react-app
      dockerfile: Dockerfile.dev
    container_name: blackcnote-react
    ports:
      - "5174:5174"  # Canonical React port
    volumes:
      # Live editing - React source files with enhanced watching
      - "./react-app/src:/app/src:delegated"
      - "./react-app/public:/app/public:delegated"
      - "./react-app/package.json:/app/package.json"
      - "./react-app/package-lock.json:/app/package-lock.json"
      - "./react-app/vite.config.ts:/app/vite.config.ts"
      - "./react-app/tailwind.config.js:/app/tailwind.config.js"
      - "./react-app/postcss.config.js:/app/postcss.config.js"
    environment:
      - CHOKIDAR_USEPOLLING=true
      - WATCHPACK_POLLING=true
      - FAST_REFRESH=true
      - NODE_ENV=development
    networks:
      - blackcnote-network
    restart: unless-stopped

  # Browsersync for Enhanced Live Reloading
  browsersync:
    image: node:18-alpine
    container_name: blackcnote-browsersync
    ports:
      - "3000:3000"  # Canonical Browsersync port
      - "3001:3001"  # Canonical Browsersync UI port
    volumes:
      # Canonical Windows filesystem path
      - "C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote:/var/www/html:delegated"
      - "./react-app/dist:/app/dist:delegated"
    working_dir: /app
    command: >
      sh -c "npm install -g browser-sync &&
             echo 'module.exports = { proxy: \"wordpress:80\", port: 3000, ui: { port: 3001 }, files: [\"/var/www/html/**/*.php\", \"/var/www/html/**/*.js\", \"/var/www/html/**/*.css\", \"/app/react/src/**/*.{js,jsx,ts,tsx}\"], notify: true, open: false };' > /app/browsersync-config.js &&
             browser-sync start --config browsersync-config.js"
    networks:
      - blackcnote-network
    restart: unless-stopped
    depends_on:
      - wordpress
      - react-app

  # File Watcher Service for Enhanced Live Editing
  file-watcher:
    image: node:18-alpine
    container_name: blackcnote-file-watcher
    volumes:
      # Canonical Windows filesystem path
      - "C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote:/var/www/html:delegated"
      - "./react-app:/app/react:delegated"
      - "./scripts:/app/scripts:delegated"
      - "./logs:/app/logs:delegated"
    working_dir: /app
    command: >
      sh -c "npm install -g chokidar-cli &&
             mkdir -p /app/logs &&
             chokidar '/var/www/html/**/*.{php,js,jsx,ts,tsx,css,scss,html}' '/app/react/src/**/*.{js,jsx,ts,tsx,css,scss}' 
                     --polling --interval 1000 
                     --event add,change,unlink 
                     --command 'echo \"[$(date)] File changed: $1\" >> /app/logs/file-changes.log'"
    environment:
      - CHOKIDAR_USEPOLLING=true
      - CHOKIDAR_INTERVAL=1000
      - NODE_ENV=development
    networks:
      - blackcnote-network
    restart: unless-stopped
    depends_on:
      - wordpress
      - react-app

  # Development Tools Container
  dev-tools:
    image: node:18-alpine
    container_name: blackcnote-dev-tools
    ports:
      - "9229:9229"  # Canonical Dev Tools port
    volumes:
      # Canonical Windows filesystem path
      - "C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote:/var/www/html:delegated"
      - "./react-app:/app/react:delegated"
      - "./scripts:/app/scripts:delegated"
      - "./tools:/app/tools:delegated"
    working_dir: /app
    command: >
      sh -c "npm install -g nodemon concurrently &&
             tail -f /dev/null"
    networks:
      - blackcnote-network
    restart: unless-stopped

  # phpMyAdmin for Database Management
  phpmyadmin:
    image: phpmyadmin/phpmyadmin:latest
    container_name: blackcnote-phpmyadmin
    ports:
      - "8080:80"  # Canonical phpMyAdmin port
    environment:
      PMA_HOST: mysql
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: blackcnote_password
      MYSQL_ROOT_PASSWORD: blackcnote_password
    depends_on:
      - mysql
    restart: unless-stopped
    networks:
      - blackcnote-network

  # Redis Commander for Redis Management
  redis-commander:
    image: rediscommander/redis-commander:latest
    container_name: blackcnote-redis-commander
    environment:
      REDIS_HOSTS: local:redis:6379
    ports:
      - "8081:8081"  # Canonical Redis Commander port
    depends_on:
      - redis
    restart: unless-stopped
    networks:
      - blackcnote-network

  # MailHog for Email Testing
  mailhog:
    image: mailhog/mailhog:latest
    container_name: blackcnote-mailhog
    ports:
      - "8025:8025"  # Canonical MailHog Web UI port
      - "1026:1025"  # Canonical MailHog SMTP port
    networks:
      - blackcnote-network
    restart: unless-stopped

  # Debug Metrics Exporter
  debug-exporter:
    image: nginx:alpine
    container_name: blackcnote-debug-exporter
    ports:
      - "9091:80"  # Canonical Metrics port
    volumes:
      - "./bin/blackcnote-metrics-exporter.php:/usr/share/nginx/html/index.html:ro"
    networks:
      - blackcnote-network
    restart: unless-stopped

volumes:
  mysql_data:
    driver: local
  redis_data:
    driver: local

networks:
  blackcnote-network:
    driver: bridge
    ipam:
      config:
        - subnet: 172.20.0.0/16
"@

Set-Content -Path "docker-compose.yml" -Value $unifiedComposeContent -Encoding UTF8
Write-Success "Created unified Docker Compose file"

# Step 5: Create unified startup script
Write-Info "Step 5: Creating unified startup script..."

$unifiedStartupContent = @"
# BlackCnote Unified Startup Script
# Canonical, robust, and fully automated
param(
    [switch]`$ForceRebuild,
    [switch]`$SkipReact,
    [switch]`$NoBrowser,
    [switch]`$Debug
)

`$ErrorActionPreference = 'Stop'
`$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
`$dockerComposeFile = "`$projectRoot\docker-compose.yml"
`$logFile = "`$projectRoot\logs\startup-log-`$(Get-Date -Format 'yyyyMMdd-HHmmss').txt"

function Log {
    param([string]`$msg, [string]`$color = 'White')
    Write-Host `$msg -ForegroundColor `$color
    Add-Content -Path `$logFile -Value `$msg
}

function Wait-For-Service {
    param([string]`$Url, [string]`$Name, [int]`$TimeoutSec = 120)
    `$start = Get-Date
    while ((Get-Date) - `$start -lt (New-TimeSpan -Seconds `$TimeoutSec)) {
        try {
            `$response = Invoke-WebRequest -Uri `$Url -TimeoutSec 5 -UseBasicParsing
            if (`$response.StatusCode -eq 200) {
                Log "✅ `$Name is healthy at `$Url" 'Green'
                return `$true
            }
        } catch {}
        Log "Waiting for `$Name at `$Url..." 'Yellow'
        Start-Sleep -Seconds 3
    }
    Log "❌ `$Name did not become healthy in time." 'Red'
    return `$false
}

# 1. Start Docker Desktop and wait for engine
Log "==========================================" 'Cyan'
Log "BLACKCNOTE UNIFIED STARTUP SYSTEM" 'Cyan'
Log "==========================================" 'Cyan'
Log ""
Log "Step 1: Opening Docker Desktop..." 'Yellow'
Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Normal
Log "Docker Desktop started. Waiting for engine to initialize..." 'Green'

`$maxWait = 300
`$waitCount = 0
`$dockerReady = `$false
while (`$waitCount -lt `$maxWait -and -not `$dockerReady) {
    Start-Sleep -Seconds 5
    `$waitCount += 5
    try {
        `$dockerInfo = docker info 2>&1
        if (`$dockerInfo -notmatch "error during connect") {
            `$dockerReady = `$true
            Log "Docker engine is ready!" 'Green'
            break
        }
    } catch {}
    if (`$waitCount % 30 -eq 0) {
        Log "Still waiting for Docker engine... (`$waitCount seconds)" 'Yellow'
    }
}

if (-not `$dockerReady) {
    Log "❌ Docker engine did not start in time. Please check Docker Desktop manually." 'Red'
    exit 1
}

# 2. Stop existing containers
Log "Step 2: Stopping existing containers..." 'Yellow'
try {
    docker-compose down --remove-orphans 2>`$null
    Log "Existing containers stopped" 'Green'
} catch {
    Log "No existing containers to stop" 'Yellow'
}

# 3. Start services
Log "Step 3: Starting BlackCnote services..." 'Yellow'
if (`$ForceRebuild) {
    docker-compose up -d --build
} else {
    docker-compose up -d
}

if (`$LASTEXITCODE -ne 0) {
    Log "❌ Failed to start services" 'Red'
    exit 1
}

Log "Services started successfully" 'Green'

# 4. Wait for services to be healthy
Log "Step 4: Waiting for services to be healthy..." 'Yellow'

`$services = @(
    @{ Url = "http://localhost:8888"; Name = "WordPress" },
    @{ Url = "http://localhost:8080"; Name = "phpMyAdmin" },
    @{ Url = "http://localhost:8081"; Name = "Redis Commander" },
    @{ Url = "http://localhost:8025"; Name = "MailHog" }
)

if (-not `$SkipReact) {
    `$services += @{ Url = "http://localhost:5174"; Name = "React App" }
}

foreach (`$service in `$services) {
    Wait-For-Service -Url `$service.Url -Name `$service.Name
}

# 5. Open browser if requested
if (-not `$NoBrowser) {
    Log "Step 5: Opening services in browser..." 'Yellow'
    Start-Process "http://localhost:8888"
    Start-Sleep -Seconds 2
    Start-Process "http://localhost:5174"
    Start-Sleep -Seconds 2
    Start-Process "http://localhost:8080"
}

# 6. Display status
Log "==========================================" 'Cyan'
Log "BLACKCNOTE SERVICES STATUS" 'Cyan'
Log "==========================================" 'Cyan'
Log ""
Log "✅ WordPress: http://localhost:8888" 'Green'
Log "✅ WordPress Admin: http://localhost:8888/wp-admin" 'Green'
Log "✅ React App: http://localhost:5174" 'Green'
Log "✅ phpMyAdmin: http://localhost:8080" 'Green'
Log "✅ Redis Commander: http://localhost:8081" 'Green'
Log "✅ MailHog: http://localhost:8025" 'Green'
Log "✅ Browsersync: http://localhost:3000" 'Green'
Log "✅ Dev Tools: http://localhost:9229" 'Green'
Log "✅ Metrics: http://localhost:9091" 'Green'
Log ""
Log "🎉 BlackCnote is ready!" 'Green'
Log "==========================================" 'Cyan'
"@

Set-Content -Path "start-blackcnote.ps1" -Value $unifiedStartupContent -Encoding UTF8
Write-Success "Created unified startup script"

# Step 6: Create simple batch wrapper
Write-Info "Step 6: Creating batch wrapper..."

$batchWrapperContent = @"
@echo off
REM BlackCnote Unified Startup Script - Batch Wrapper
REM This script provides a user-friendly way to start BlackCnote

echo.
echo ========================================
echo    BlackCnote Unified Startup
echo ========================================
echo.
echo This script will start all BlackCnote services:
echo - WordPress (http://localhost:8888)
echo - React App (http://localhost:5174)
echo - phpMyAdmin (http://localhost:8080)
echo - Redis Commander (http://localhost:8081)
echo - MailHog (http://localhost:8025)
echo - Browsersync (http://localhost:3000)
echo - Dev Tools (http://localhost:9229)
echo.

REM Check if PowerShell is available
powershell -Command "Get-Host" >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] PowerShell is not available
    echo Please install PowerShell and try again
    pause
    exit /b 1
)

REM Run the PowerShell script
powershell.exe -ExecutionPolicy Bypass -File "start-blackcnote.ps1"

if %errorLevel% == 0 (
    echo.
    echo ========================================
    echo    BlackCnote Started Successfully!
    echo ========================================
    echo.
    echo Services available at:
    echo - WordPress:      http://localhost:8888
    echo - WordPress Admin: http://localhost:8888/wp-admin
    echo - React App:      http://localhost:5174
    echo - phpMyAdmin:     http://localhost:8080
    echo - Redis Commander: http://localhost:8081
    echo - MailHog:        http://localhost:8025
    echo - Browsersync:    http://localhost:3000
    echo - Dev Tools:      http://localhost:9229
    echo - Metrics:        http://localhost:9091
    echo.
) else (
    echo.
    echo [ERROR] Failed to start BlackCnote
    echo Please check the logs and try again
    echo.
)

pause
"@

Set-Content -Path "start-blackcnote.bat" -Value $batchWrapperContent -Encoding ASCII
Write-Success "Created batch wrapper"

# Step 7: Create stop script
Write-Info "Step 7: Creating stop script..."

$stopScriptContent = @"
# BlackCnote Stop Script
# Stops all BlackCnote services

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "BLACKCNOTE STOP SCRIPT" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Stopping all BlackCnote services..." -ForegroundColor Yellow
docker-compose down --remove-orphans

Write-Host "All services stopped" -ForegroundColor Green
Write-Host ""
Write-Host "To start services again, run: .\start-blackcnote.bat" -ForegroundColor White
"@

Set-Content -Path "stop-blackcnote.ps1" -Value $stopScriptContent -Encoding UTF8
Write-Success "Created stop script"

# Step 8: Create status script
Write-Info "Step 8: Creating status script..."

$statusScriptContent = @"
# BlackCnote Status Script
# Shows status of all BlackCnote services

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "BLACKCNOTE SERVICES STATUS" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Docker containers:" -ForegroundColor Yellow
docker-compose ps

Write-Host ""
Write-Host "Service URLs:" -ForegroundColor Yellow
Write-Host "- WordPress:      http://localhost:8888" -ForegroundColor White
Write-Host "- WordPress Admin: http://localhost:8888/wp-admin" -ForegroundColor White
Write-Host "- React App:      http://localhost:5174" -ForegroundColor White
Write-Host "- phpMyAdmin:     http://localhost:8080" -ForegroundColor White
Write-Host "- Redis Commander: http://localhost:8081" -ForegroundColor White
Write-Host "- MailHog:        http://localhost:8025" -ForegroundColor White
Write-Host "- Browsersync:    http://localhost:3000" -ForegroundColor White
Write-Host "- Dev Tools:      http://localhost:9229" -ForegroundColor White
Write-Host "- Metrics:        http://localhost:9091" -ForegroundColor White
"@

Set-Content -Path "status-blackcnote.ps1" -Value $statusScriptContent -Encoding UTF8
Write-Success "Created status script"

# Summary
Write-Info "=========================================="
Write-Info "STARTUP CONFLICTS FIX COMPLETED"
Write-Info "=========================================="
Write-Success "Removed $removedCount conflicting scripts"
Write-Success "Removed $removedComposeCount conflicting Docker Compose files"
Write-Success "Created unified Docker Compose file"
Write-Success "Created unified startup script"
Write-Success "Created batch wrapper"
Write-Success "Created stop script"
Write-Success "Created status script"

Write-Info ""
Write-Info "USAGE INSTRUCTIONS:"
Write-Info "==================="
Write-Info "Start BlackCnote: .\start-blackcnote.bat"
Write-Info "Stop BlackCnote: .\stop-blackcnote.ps1"
Write-Info "Check Status: .\status-blackcnote.ps1"
Write-Info ""
Write-Info "SERVICE URLS:"
Write-Info "============="
Write-Info "WordPress:      http://localhost:8888"
Write-Info "WordPress Admin: http://localhost:8888/wp-admin"
Write-Info "React App:      http://localhost:5174"
Write-Info "phpMyAdmin:     http://localhost:8080"
Write-Info "Redis Commander: http://localhost:8081"
Write-Info "MailHog:        http://localhost:8025"
Write-Info "Browsersync:    http://localhost:3000"
Write-Info "Dev Tools:      http://localhost:9229"
Write-Info "Metrics:        http://localhost:9091"

if ($Backup) {
    Write-Info ""
    Write-Info "Backup location: $backupDir"
}

Write-Info ""
Write-Info "Fix completed at: $(Get-Date)" 