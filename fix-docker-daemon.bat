@echo off
echo ========================================
echo   Docker Daemon Fix for BlackCnote
echo ========================================
echo.

REM Check if running as Administrator
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [INFO] Running with Administrator privileges
) else (
    echo [ERROR] This script must be run as Administrator!
    echo Please right-click this file and select "Run as administrator"
    pause
    exit /b 1
)

echo [INFO] Starting Docker daemon configuration fix...
echo.

REM Run the PowerShell script
powershell.exe -ExecutionPolicy Bypass -File "scripts\fix-docker-daemon.ps1" -RestartDocker -TestAPI

echo.
echo [INFO] Fix completed. Press any key to exit...
pause >nul 