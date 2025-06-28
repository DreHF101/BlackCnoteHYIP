@echo off
setlocal enabledelayedexpansion

echo.
echo ========================================
echo   BlackCnote React App Fix Script
echo ========================================
echo.

echo [INFO] Fixing React App with canonical pathway enforcement...
echo.

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

:: Check current directory (canonical pathway)
echo [INFO] Current directory: %CD%
echo [INFO] Canonical pathway: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
echo.

:: Verify canonical pathway
if not "%CD%"=="C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote" (
    echo [ERROR] Not in canonical BlackCnote directory
    echo [ERROR] Please run from: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
    pause
    exit /b 1
)

:: Check React app directory exists
if not exist "react-app" (
    echo [ERROR] React app directory not found
    echo [ERROR] Canonical pathway: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app
    pause
    exit /b 1
)

echo ✓ React app directory found at canonical pathway
echo.

:: Check React app files
echo [INFO] Checking React app configuration...
if exist "react-app\package.json" (
    echo ✓ package.json found
) else (
    echo ✗ package.json missing
    pause
    exit /b 1
)

if exist "react-app\Dockerfile.dev" (
    echo ✓ Dockerfile.dev found
) else (
    echo ✗ Dockerfile.dev missing
    pause
    exit /b 1
)

if exist "react-app\vite.config.ts" (
    echo ✓ vite.config.ts found
) else (
    echo ✗ vite.config.ts missing
    pause
    exit /b 1
)

echo.

:: Stop React app container
echo [INFO] Stopping React app container...
docker-compose -f docker-compose.yml stop react-app
docker-compose -f docker-compose.yml rm -f react-app

:: Clean up any existing React app containers
echo [INFO] Cleaning up existing React app containers...
docker ps -a | findstr react-app
if %errorLevel% equ 0 (
    docker rm -f blackcnote_react
)

echo.

:: Rebuild React app with canonical pathways
echo [INFO] Rebuilding React app with canonical pathways...
echo [INFO] Using canonical pathway: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app

docker-compose -f docker-compose.yml build --no-cache react-app

if %errorLevel% neq 0 (
    echo [ERROR] Failed to build React app
    echo [ERROR] Check the build logs above
    pause
    exit /b 1
)

echo ✓ React app built successfully
echo.

:: Start React app container
echo [INFO] Starting React app container...
docker-compose -f docker-compose.yml up -d react-app

if %errorLevel% neq 0 (
    echo [ERROR] Failed to start React app
    pause
    exit /b 1
)

echo ✓ React app container started
echo.

:: Wait for React app to initialize
echo [INFO] Waiting for React app to initialize...
timeout /t 30 /nobreak >nul

:: Check React app container status
echo [INFO] Checking React app container status...
docker-compose -f docker-compose.yml ps react-app

:: Check port 5174
echo [INFO] Checking port 5174...
netstat -an | findstr :5174 >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Port 5174 is now in use
    echo [INFO] Port 5174 details:
    netstat -an | findstr :5174
) else (
    echo ✗ Port 5174 is still not in use
    echo [WARNING] React app may still be starting up
)

:: Test HTTP connection
echo [INFO] Testing React app HTTP connection...
timeout /t 10 /nobreak >nul
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:5174
if %errorLevel% equ 0 (
    echo ✓ React app HTTP connection successful
) else (
    echo ✗ React app HTTP connection failed
    echo [INFO] React app may need more time to start
)

:: Show React app logs
echo [INFO] React app container logs:
docker-compose -f docker-compose.yml logs react-app --tail=20

:: Open React app in browser
echo [INFO] Opening React app in browser...
start http://localhost:5174

echo.
echo ========================================
echo   React App Fix Complete
echo ========================================
echo.
echo [SUCCESS] React app should now be accessible
echo.
echo [CANONICAL PATHWAYS ENFORCED]
echo React App: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app
echo Build Output: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\dist
echo.
echo [ACCESS URLS]
echo React App: http://localhost:5174
echo WordPress: http://localhost:8888
echo phpMyAdmin: http://localhost:8080
echo Metrics: http://localhost:9091
echo.
echo [TROUBLESHOOTING]
echo If React app still doesn't work:
echo 1. Check logs: docker-compose -f docker-compose.yml logs react-app
echo 2. Restart: docker-compose -f docker-compose.yml restart react-app
echo 3. Rebuild: docker-compose -f docker-compose.yml build --no-cache react-app
echo.
pause 