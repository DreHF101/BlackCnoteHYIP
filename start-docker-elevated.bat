@echo off
REM Docker Desktop Elevated Startup
REM This batch file starts Docker Desktop with administrator privileges

echo ========================================
echo Docker Desktop Elevated Startup
echo ========================================
echo Starting at: %date% %time%
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: This script requires administrator privileges!
    echo Please right-click and select "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo ✅ Running with administrator privileges
echo.

REM Stop Docker Desktop if running
echo Checking if Docker Desktop is already running...
tasklist /FI "IMAGENAME eq Docker Desktop.exe" 2>NUL | find /I /N "Docker Desktop.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo Stopping existing Docker Desktop...
    taskkill /F /IM "Docker Desktop.exe" >nul 2>&1
    timeout /t 5 /nobreak >nul
)

REM Start Docker Desktop with elevated privileges
echo Starting Docker Desktop with elevated privileges...
start "" /B "C:\Program Files\Docker\Docker\Docker Desktop.exe"

REM Wait for Docker to start
echo Waiting for Docker to start...
timeout /t 15 /nobreak >nul

REM Check if Docker is running
echo Checking Docker status...
docker info >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ Docker is running successfully!
    echo.
    echo Docker services are ready.
    echo You can now run BlackCnote startup scripts.
    echo.
    echo Available services:
    echo - WordPress: http://localhost:8888
    echo - React App: http://localhost:5174
    echo - PHPMyAdmin: http://localhost:8080
    echo - MailHog: http://localhost:8025
) else (
    echo ❌ Docker failed to start properly.
    echo Please check Docker Desktop manually.
    echo.
    echo Troubleshooting steps:
    echo 1. Open Docker Desktop manually
    echo 2. Check Docker Desktop settings
    echo 3. Ensure WSL2 integration is enabled
    echo 4. Restart Docker Desktop
)

echo.
echo Script completed at: %date% %time%
pause 