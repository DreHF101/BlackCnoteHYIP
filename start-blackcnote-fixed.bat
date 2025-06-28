@echo off
setlocal enabledelayedexpansion

echo.
echo ========================================
echo   BlackCnote Fixed Startup Script
echo ========================================
echo.

echo [INFO] Starting BlackCnote with explicit configuration...
echo.

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

:: Check current directory
echo [INFO] Current directory: %CD%
echo [INFO] Checking for docker-compose.yml...

:: Check if docker-compose.yml exists
if exist "docker-compose.yml" (
    echo ✓ docker-compose.yml found in current directory
) else (
    echo ✗ docker-compose.yml not found in current directory
    echo [ERROR] Please run this script from the BlackCnote root directory
    pause
    exit /b 1
)

:: Check Docker status
echo [INFO] Checking Docker status...
docker --version >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Docker is not accessible
    echo [ERROR] Please start Docker Desktop first
    pause
    exit /b 1
)
echo ✓ Docker is accessible

:: Stop any existing containers
echo [INFO] Stopping any existing containers...
docker-compose -f docker-compose.yml down >nul 2>&1

:: Start services with explicit file specification
echo [INFO] Starting BlackCnote services...
echo [INFO] Using configuration file: %CD%\docker-compose.yml
docker-compose -f docker-compose.yml up -d

:: Check if services started successfully
if %errorLevel% equ 0 (
    echo ✓ Services started successfully
) else (
    echo ✗ Failed to start services
    echo [ERROR] Check the error messages above
    pause
    exit /b 1
)

:: Wait for services to start
echo [INFO] Waiting for services to initialize...
timeout /t 60 /nobreak >nul

:: Check service status
echo [INFO] Checking service status...
docker-compose -f docker-compose.yml ps

:: Check port 8888
echo [INFO] Checking port 8888...
netstat -an | findstr :8888 >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Port 8888 is now in use
    echo [INFO] Port 8888 details:
    netstat -an | findstr :8888
) else (
    echo ✗ Port 8888 is still not in use
    echo [WARNING] Services may still be starting up
)

:: Test HTTP connection
echo [INFO] Testing HTTP connection...
timeout /t 10 /nobreak >nul
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:8888
if %errorLevel% equ 0 (
    echo ✓ HTTP connection successful
) else (
    echo ✗ HTTP connection failed
    echo [INFO] Services may need more time to start
)

:: Open browser
echo [INFO] Opening BlackCnote in browser...
start http://localhost:8888

echo.
echo ========================================
echo   BlackCnote Startup Complete
echo ========================================
echo.
echo [SUCCESS] BlackCnote services are starting up
echo.
echo [ACCESS URLs]
echo WordPress:     http://localhost:8888
echo phpMyAdmin:    http://localhost:8080
echo Metrics:       http://localhost:9091
echo React App:     http://localhost:5174
echo.
echo [INFO] If the page doesn't load immediately, wait 2-3 minutes
echo [INFO] for all services to fully initialize.
echo.
echo [COMMANDS]
echo View logs:      docker-compose -f docker-compose.yml logs -f
echo Stop services:  docker-compose -f docker-compose.yml down
echo Restart:        docker-compose -f docker-compose.yml restart
echo.
pause 