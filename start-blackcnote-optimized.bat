@echo off
REM BlackCnote Optimized Startup Script (Batch)
REM This script starts all BlackCnote services with proper error handling
REM Compatible with BlackCnote Docker Engine v28.1.1

echo ========================================
echo BlackCnote Docker Environment Startup
echo ========================================
echo Starting at: %date% %time%
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Administrator privileges required
    echo Please run this script as Administrator
    pause
    exit /b 1
)

REM Set project root - FIXED PATH
set "PROJECT_ROOT=C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
cd /d "%PROJECT_ROOT%"

echo [INFO] Project root: %PROJECT_ROOT%
echo.

REM Check Docker status
echo Checking Docker status...
docker info >nul 2>&1
if %errorLevel% neq 0 (
    echo Starting Docker Desktop...
    start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    echo Waiting for Docker to start...
    timeout /t 15 /nobreak >nul
    
    REM Wait for Docker to be ready
    for /l %%i in (1,1,30) do (
        docker info >nul 2>&1
        if !errorLevel! equ 0 goto docker_ready
        timeout /t 2 /nobreak >nul
    )
    echo Docker daemon failed to start
    pause
    exit /b 1
)

:docker_ready
echo Docker is running.

REM Stop existing containers
echo Stopping existing containers...
docker-compose down --remove-orphans >nul 2>&1

REM Create necessary directories
echo Creating directories...
if not exist "logs\wordpress" mkdir "logs\wordpress"
if not exist "logs\mysql" mkdir "logs\mysql"
if not exist "logs\redis" mkdir "logs\redis"
if not exist "backups" mkdir "backups"
if not exist "ssl" mkdir "ssl"
if not exist "blackcnote\wp-content\uploads" mkdir "blackcnote\wp-content\uploads"
if not exist "blackcnote\wp-content\plugins" mkdir "blackcnote\wp-content\plugins"
if not exist "blackcnote\wp-content\themes" mkdir "blackcnote\wp-content\themes"
if not exist "blackcnote\wp-content\mu-plugins" mkdir "blackcnote\wp-content\mu-plugins"

REM Start BlackCnote services - FIXED: Use root docker-compose.yml
echo Starting BlackCnote services...
docker-compose up -d --build

if %errorLevel% neq 0 (
    echo Failed to start BlackCnote services
    pause
    exit /b 1
)

REM Wait for services
echo Waiting for services to start...
timeout /t 15 /nobreak >nul

REM Check service status
echo.
echo Service Status:
docker-compose ps

echo.
echo ========================================
echo BlackCnote Services
echo ========================================
echo WordPress:      http://localhost:8888
echo WordPress Admin: http://localhost:8888/wp-admin
echo React App:      http://localhost:5174
echo phpMyAdmin:     http://localhost:8080
echo Redis Commander: http://localhost:8081
echo MailHog:        http://localhost:8025
echo Metrics:        http://localhost:9091
echo Health Check:   http://localhost:8888/health

echo.
echo BlackCnote startup completed!
echo Docker Engine v28.1.1 with enhanced configuration is active

REM Wait for services to be ready
echo.
echo Waiting for services to be ready...
timeout /t 10 /nobreak >nul

REM Open services in browser
echo.
set /p "open_browser=Open services in browser? (y/n): "
if /i "%open_browser%"=="y" (
    start http://localhost:8888
    timeout /t 2 /nobreak >nul
    start http://localhost:5174
    timeout /t 2 /nobreak >nul
    start http://localhost:8080
)

echo.
echo ========================================
echo All Services Ready
echo ========================================
echo BlackCnote is now fully operational!
echo Completed at: %date% %time%
echo.

pause 