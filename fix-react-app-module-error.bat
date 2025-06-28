@echo off
echo ========================================
echo   React App Module Error Fix
echo ========================================
echo.

echo [INFO] Fixing React App module configuration errors...
echo [INFO] ES module syntax has been applied to config files
echo.

set COMPOSE_FILE="C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\docker-compose.yml"

echo [INFO] Stopping React app container...
docker-compose -f %COMPOSE_FILE% stop react-app
if %errorlevel% neq 0 (
    echo [ERROR] Failed to stop React app container
    pause
    exit /b 1
)

echo [INFO] Removing React app container...
docker-compose -f %COMPOSE_FILE% rm -f react-app
if %errorlevel% neq 0 (
    echo [ERROR] Failed to remove React app container
    pause
    exit /b 1
)

echo [INFO] Rebuilding React app with fixed configuration...
docker-compose -f %COMPOSE_FILE% build --no-cache react-app
if %errorlevel% neq 0 (
    echo [ERROR] Failed to rebuild React app
    pause
    exit /b 1
)

echo [INFO] Starting React app container...
docker-compose -f %COMPOSE_FILE% up -d react-app
if %errorlevel% neq 0 (
    echo [ERROR] Failed to start React app container
    pause
    exit /b 1
)

echo [INFO] Waiting for React app to initialize...
timeout /t 10 /nobreak >nul

echo [INFO] Checking React app status...
docker-compose -f %COMPOSE_FILE% ps react-app

echo [INFO] Testing React app connection...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:5174

echo.
echo ========================================
echo   React App Module Error Fix Complete
echo ========================================
echo.
echo [SUCCESS] React app should now be working properly
echo.
echo [ACCESS URLS]
echo React App: http://localhost:5174
echo WordPress: http://localhost:8888
echo phpMyAdmin: http://localhost:8080
echo Metrics: http://localhost:9091
echo.
echo [TROUBLESHOOTING]
echo If React app still doesn't work:
echo 1. Check logs: docker-compose -f %COMPOSE_FILE% logs react-app
echo 2. Restart: docker-compose -f %COMPOSE_FILE% restart react-app
echo.
pause 