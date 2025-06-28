@echo off
setlocal enabledelayedexpansion

echo.
echo ========================================
echo   BlackCnote System Test Script
echo ========================================
echo.

echo [INFO] Testing BlackCnote System Components...
echo.

:: Test 1: Check Docker
echo [TEST 1] Docker Status
docker --version >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Docker is accessible
    docker info | findstr "Server Version"
) else (
    echo ✗ Docker is not accessible
)

echo.

:: Test 2: Check Docker Compose
echo [TEST 2] Docker Compose Status
docker-compose --version >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Docker Compose is accessible
    docker-compose --version
) else (
    echo ✗ Docker Compose is not accessible
)

echo.

:: Test 3: Check WSL2
echo [TEST 3] WSL2 Status
wsl --status >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ WSL2 is accessible
    wsl --status
) else (
    echo ✗ WSL2 is not accessible
)

echo.

:: Test 4: Check OpenSSL
echo [TEST 4] OpenSSL Status
openssl version >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ OpenSSL is accessible
    openssl version
) else (
    echo ✗ OpenSSL is not accessible
)

echo.

:: Test 5: Check BlackCnote Services
echo [TEST 5] BlackCnote Services Status
if exist "docker-compose.yml" (
    echo [INFO] Checking Docker Compose services...
    docker-compose ps
) else (
    echo ✗ docker-compose.yml not found
)

echo.

:: Test 6: Check Configuration Files
echo [TEST 6] Configuration Files
if exist "config\nginx\blackcnote-docker.conf" (
    echo ✓ Nginx configuration exists
) else (
    echo ✗ Nginx configuration missing
)

if exist "docker-compose.yml" (
    echo ✓ Docker Compose configuration exists
) else (
    echo ✗ Docker Compose configuration missing
)

if exist "bin\blackcnote-metrics-exporter.php" (
    echo ✓ Metrics Exporter exists
) else (
    echo ✗ Metrics Exporter missing
)

echo.

:: Test 7: Check Logs Directory
echo [TEST 7] Logs Directory
if exist "logs" (
    echo ✓ Logs directory exists
    dir logs /b
) else (
    echo ✗ Logs directory missing
)

echo.

:: Test 8: Test WordPress Access
echo [TEST 8] WordPress Access Test
echo [INFO] Testing WordPress at http://localhost:8888...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:8888
if %errorLevel% equ 0 (
    echo ✓ WordPress is accessible
) else (
    echo ✗ WordPress is not accessible
)

echo.

:: Test 9: Test phpMyAdmin Access
echo [TEST 9] phpMyAdmin Access Test
echo [INFO] Testing phpMyAdmin at http://localhost:8080...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:8080
if %errorLevel% equ 0 (
    echo ✓ phpMyAdmin is accessible
) else (
    echo ✗ phpMyAdmin is not accessible
)

echo.

:: Test 10: Test Metrics Exporter
echo [TEST 10] Metrics Exporter Test
echo [INFO] Testing Metrics Exporter at http://localhost:9091...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:9091
if %errorLevel% equ 0 (
    echo ✓ Metrics Exporter is accessible
) else (
    echo ✗ Metrics Exporter is not accessible
)

echo.

:: Test 11: Check Recent Logs
echo [TEST 11] Recent Logs Check
if exist "logs\php-errors.log" (
    echo [INFO] Recent PHP Errors:
    type logs\php-errors.log | findstr /C:"2025-06-27"
)

if exist "logs\nginx\blackcnote_error.log" (
    echo [INFO] Recent Nginx Errors:
    type logs\nginx\blackcnote_error.log | findstr /C:"2025/06/27"
)

echo.

:: Test 12: System Resources
echo [TEST 12] System Resources
echo [INFO] Available disk space:
wmic logicaldisk get size,freespace,caption

echo [INFO] Memory usage:
wmic OS get TotalVisibleMemorySize,FreePhysicalMemory /format:table

echo.

echo ========================================
echo   Test Results Summary
echo ========================================
echo.
echo [INFO] All tests completed
echo [INFO] Check the results above for any failures
echo [INFO] If services are not running, start them with:
echo [INFO]   docker-compose up -d
echo.
pause 