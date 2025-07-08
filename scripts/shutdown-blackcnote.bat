@echo off
echo ========================================
echo    BlackCnote Shutdown Script
echo ========================================
echo.

echo 1. Stopping React Dev Server...
taskkill /F /PID 15572 2>nul
if %errorlevel% equ 0 (
    echo    [OK] React dev server stopped
) else (
    echo    [INFO] React dev server not running
)

echo.
echo 2. Stopping Browsersync...
taskkill /F /IM node.exe 2>nul
if %errorlevel% equ 0 (
    echo    [OK] Browsersync stopped
) else (
    echo    [INFO] Browsersync not running
)

echo.
echo 3. Stopping Docker Containers...
docker stop $(docker ps -q --filter "name=blackcnote") 2>nul
if %errorlevel% equ 0 (
    echo    [OK] Docker containers stopped
) else (
    echo    [INFO] No Docker containers running
)

echo.
echo 4. Checking for any remaining processes...
netstat -ano | findstr ":5176\|:3006\|:8888" >nul
if %errorlevel% equ 0 (
    echo    [WARNING] Some services may still be running
) else (
    echo    [OK] All services stopped
)

echo.
echo ========================================
echo    Shutdown Complete
echo ========================================
echo.
echo All BlackCnote services have been stopped.
echo.
echo Shutting down computer in 10 seconds...
echo Press Ctrl+C to cancel shutdown.
echo.
timeout /t 10 /nobreak >nul

echo.
echo Shutting down now...
shutdown /s /t 0 