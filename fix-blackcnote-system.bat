@echo off
setlocal enabledelayedexpansion

echo.
echo ========================================
echo   BlackCnote System Fix Script
echo ========================================
echo.

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

echo [INFO] Starting BlackCnote System Fix...
echo.

:: Step 1: Fix Docker Desktop
echo [STEP 1] Fixing Docker Desktop...
echo.

:: Check if Docker Desktop is installed
if exist "C:\Program Files\Docker\Docker\Docker Desktop.exe" (
    echo [INFO] Docker Desktop found
    echo [INFO] Stopping Docker Desktop...
    taskkill /f /im "Docker Desktop.exe" >nul 2>&1
    timeout /t 10 /nobreak >nul
    
    echo [INFO] Starting Docker Desktop with elevated privileges...
    start "" /wait "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    echo [INFO] Waiting for Docker Desktop to initialize...
    timeout /t 60 /nobreak >nul
) else (
    echo [ERROR] Docker Desktop not found
    echo [ERROR] Please install Docker Desktop first
    pause
    exit /b 1
)

:: Step 2: Fix WSL2
echo.
echo [STEP 2] Fixing WSL2...
echo.

:: Enable WSL2
echo [INFO] Enabling WSL2...
wsl --set-default-version 2 >nul 2>&1

:: Check WSL status
echo [INFO] Checking WSL status...
wsl --status

:: Step 3: Fix Nginx Configuration
echo.
echo [STEP 3] Fixing Nginx Configuration...
echo.

:: Update Nginx configs
for %%f in (config\nginx\*.conf) do (
    if exist "%%f" (
        echo [INFO] Updating Nginx config: %%f
        powershell -Command "(Get-Content '%%f') -replace 'limit_req_zone \$binary_remote_addr zone=login:10m rate=\d+r/s;', 'limit_req_zone $binary_remote_addr zone=login:10m rate=10r/s;' | Set-Content '%%f'"
        powershell -Command "(Get-Content '%%f') -replace 'limit_req zone=login burst=\d+', 'limit_req zone=login burst=20 nodelay' | Set-Content '%%f'"
        powershell -Command "(Get-Content '%%f') -replace 'proxy_read_timeout \d+;', 'proxy_read_timeout 300;' | Set-Content '%%f'"
        powershell -Command "(Get-Content '%%f') -replace 'proxy_connect_timeout \d+;', 'proxy_connect_timeout 300;' | Set-Content '%%f'"
        echo [SUCCESS] Updated %%f
    )
)

:: Step 4: Fix MySQL Configuration
echo.
echo [STEP 4] Fixing MySQL Configuration...
echo.

if exist "docker-compose.yml" (
    echo [INFO] Updating MySQL configuration in docker-compose.yml
    powershell -Command "(Get-Content 'docker-compose.yml') -replace '--skip-host-cache', '--host-cache-size=0' | Set-Content 'docker-compose.yml'"
    powershell -Command "(Get-Content 'docker-compose.yml') -replace '--default-authentication-plugin=mysql_native_password', '--authentication-policy=caching_sha2_password' | Set-Content 'docker-compose.yml'"
    echo [SUCCESS] Updated MySQL configuration
)

:: Step 5: Fix Metrics Exporter
echo.
echo [STEP 5] Fixing Metrics Exporter...
echo.

if exist "bin\blackcnote-metrics-exporter.php" (
    echo [INFO] Updating Metrics Exporter with proper error handling
    powershell -Command "(Get-Content 'bin\blackcnote-metrics-exporter.php') -replace 'stream_socket_accept\(', 'if (($connection = stream_socket_accept(' | Set-Content 'bin\blackcnote-metrics-exporter.php'"
    echo [SUCCESS] Updated Metrics Exporter
)

:: Step 6: Install OpenSSL
echo.
echo [STEP 6] Installing OpenSSL...
echo.

:: Check if OpenSSL is installed
openssl version >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] OpenSSL not found, attempting to install...
    
    :: Try winget first
    winget install OpenSSL >nul 2>&1
    if %errorLevel% equ 0 (
        echo [SUCCESS] OpenSSL installed via winget
    ) else (
        :: Try chocolatey
        choco install openssl -y >nul 2>&1
        if %errorLevel% equ 0 (
            echo [SUCCESS] OpenSSL installed via chocolatey
        ) else (
            echo [WARNING] Failed to install OpenSSL automatically
            echo [WARNING] Please install manually from https://slproweb.com/products/Win32OpenSSL.html
        )
    )
) else (
    echo [SUCCESS] OpenSSL is already installed
)

:: Step 7: Test System
echo.
echo [STEP 7] Testing System...
echo.

:: Test Docker
echo [INFO] Testing Docker...
docker --version >nul 2>&1
if %errorLevel% equ 0 (
    echo [SUCCESS] Docker is accessible
) else (
    echo [ERROR] Docker is not accessible
)

:: Test Docker Compose
echo [INFO] Testing Docker Compose...
docker-compose --version >nul 2>&1
if %errorLevel% equ 0 (
    echo [SUCCESS] Docker Compose is accessible
) else (
    echo [ERROR] Docker Compose is not accessible
)

:: Test OpenSSL
echo [INFO] Testing OpenSSL...
openssl version >nul 2>&1
if %errorLevel% equ 0 (
    echo [SUCCESS] OpenSSL is accessible
) else (
    echo [ERROR] OpenSSL is not accessible
)

:: Test WSL2
echo [INFO] Testing WSL2...
wsl --status >nul 2>&1
if %errorLevel% equ 0 (
    echo [SUCCESS] WSL2 is accessible
) else (
    echo [ERROR] WSL2 is not accessible
)

:: Step 8: Start BlackCnote Services
echo.
echo [STEP 8] Starting BlackCnote Services...
echo.

:: Clean up Docker
echo [INFO] Cleaning up Docker...
docker system prune -f >nul 2>&1

:: Start services
echo [INFO] Starting BlackCnote services...
docker-compose up -d

:: Wait for services to start
echo [INFO] Waiting for services to start...
timeout /t 30 /nobreak >nul

:: Check service status
echo [INFO] Checking service status...
docker-compose ps

echo.
echo ========================================
echo   BlackCnote System Fix Completed
echo ========================================
echo.
echo [SUCCESS] All fixes have been applied
echo [INFO] Please check the service status above
echo [INFO] If issues persist, check the logs in the logs/ directory
echo.
pause 