@echo off
REM BlackCnote Docker Desktop Startup Script (Administrator Required)
REM This script must be run as Administrator to properly manage Docker processes

echo.
echo ========================================
echo   BlackCnote Docker Desktop Startup
echo ========================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: This script must be run as Administrator
    echo Right-click on this file and select "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo Running as Administrator - OK
echo.

REM Stop Docker Desktop processes
echo Stopping Docker Desktop processes...
taskkill /f /im "Docker Desktop.exe" >nul 2>&1
taskkill /f /im "com.docker.backend.exe" >nul 2>&1
taskkill /f /im "com.docker.build.exe" >nul 2>&1
taskkill /f /im "com.docker.service.exe" >nul 2>&1
taskkill /f /im "com.docker.wsl-distro-proxy.exe" >nul 2>&1

REM Shutdown WSL
echo Shutting down WSL...
wsl --shutdown >nul 2>&1
timeout /t 3 /nobreak >nul

REM Start Docker Desktop
echo Starting Docker Desktop...
start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"

echo.
echo Docker Desktop is starting...
echo Please wait for it to fully initialize (this may take 1-2 minutes)
echo.
echo Once Docker Desktop is running:
echo 1. Check that the Docker icon in the system tray shows "Docker Desktop is running"
echo 2. Open Docker Desktop and verify WSL2 integration is enabled
echo 3. Run: docker-compose -f config/docker/docker-compose.yml up -d
echo 4. Access WordPress at: http://localhost:8888
echo 5. Access React app at: http://localhost:5174
echo.

pause 