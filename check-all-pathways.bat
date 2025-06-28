@echo off
setlocal enabledelayedexpansion

echo.
echo ========================================
echo   BlackCnote Pathway Check Script
echo ========================================
echo.

echo [INFO] Checking all pathways and connections...
echo.

:: Check 1: Docker Status
echo [CHECK 1] Docker Status
docker --version >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Docker is accessible
    docker --version
) else (
    echo ✗ Docker is not accessible
    echo [ERROR] Docker Desktop may not be running
)

echo.

:: Check 2: Docker Compose Status
echo [CHECK 2] Docker Compose Status
docker-compose --version >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Docker Compose is accessible
    docker-compose --version
) else (
    echo ✗ Docker Compose is not accessible
)

echo.

:: Check 3: Running Containers
echo [CHECK 3] Running Containers
docker ps >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Docker containers can be listed
    echo [INFO] Current running containers:
    docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
) else (
    echo ✗ Cannot list Docker containers
)

echo.

:: Check 4: BlackCnote Services Status
echo [CHECK 4] BlackCnote Services Status
if exist "docker-compose.yml" (
    echo ✓ docker-compose.yml found
    docker-compose ps >nul 2>&1
    if %errorLevel% equ 0 (
        echo ✓ Docker Compose can read services
        echo [INFO] Service status:
        docker-compose ps
    ) else (
        echo ✗ Docker Compose cannot read services
    )
) else (
    echo ✗ docker-compose.yml not found
)

echo.

:: Check 5: Port 8888 Status
echo [CHECK 5] Port 8888 Status
netstat -an | findstr :8888 >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Port 8888 is in use
    echo [INFO] Port 8888 details:
    netstat -an | findstr :8888
) else (
    echo ✗ Port 8888 is not in use
    echo [INFO] This means no service is listening on port 8888
)

echo.

:: Check 6: WordPress Container Status
echo [CHECK 6] WordPress Container Status
docker ps | findstr wordpress >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ WordPress container is running
    docker ps | findstr wordpress
) else (
    echo ✗ WordPress container is not running
)

echo.

:: Check 7: Try to Start Services
echo [CHECK 7] Attempting to Start Services
echo [INFO] Starting BlackCnote services...
docker-compose up -d

echo.

:: Check 8: Wait and Check Again
echo [CHECK 8] Waiting for Services to Start
echo [INFO] Waiting 30 seconds for services to initialize...
timeout /t 30 /nobreak >nul

echo [INFO] Checking service status after startup:
docker-compose ps

echo.

:: Check 9: Port 8888 After Startup
echo [CHECK 9] Port 8888 After Startup
netstat -an | findstr :8888 >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Port 8888 is now in use
    echo [INFO] Port 8888 details:
    netstat -an | findstr :8888
) else (
    echo ✗ Port 8888 is still not in use
)

echo.

:: Check 10: Test HTTP Connection
echo [CHECK 10] Testing HTTP Connection
echo [INFO] Testing connection to http://localhost:8888...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:8888
if %errorLevel% equ 0 (
    echo ✓ HTTP connection successful
) else (
    echo ✗ HTTP connection failed
)

echo.

:: Check 11: Check Logs
echo [CHECK 11] Recent Logs
if exist "logs\blackcnote-debug.log" (
    echo [INFO] Recent debug log entries:
    type logs\blackcnote-debug.log | findstr /C:"2025-06-27" | findstr /V "DEBUG" | findstr /V "INFO"
)

echo.

:: Check 12: Docker Logs
echo [CHECK 12] Docker Service Logs
echo [INFO] WordPress container logs:
docker-compose logs wordpress --tail=10

echo.

echo ========================================
echo   Pathway Check Complete
echo ========================================
echo.

echo [SUMMARY]
echo If you see "Port 8888 is now in use" above, try accessing:
echo http://localhost:8888
echo.
echo If port 8888 is still not in use, the services failed to start.
echo Check the Docker logs above for error messages.
echo.
pause 