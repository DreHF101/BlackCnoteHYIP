@echo off
REM BlackCnote Task Scheduler Creator (Batch Version)
REM This script automatically creates a Windows Task Scheduler task for BlackCnote startup

echo ========================================
echo    BlackCnote Task Scheduler Creator
echo ========================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator!
    echo.
    echo Right-click this file and select "Run as administrator"
    echo Then run this script again.
    echo.
    pause
    exit /b 1
)

echo SUCCESS: Running with administrator privileges
echo.

REM Run the PowerShell script
echo Creating Task Scheduler task...
powershell.exe -ExecutionPolicy Bypass -File "scripts\create-task-scheduler.ps1"

echo.
echo Task Scheduler setup completed!
echo.
pause 