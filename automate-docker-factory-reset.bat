@echo off
REM Docker Desktop Factory Reset Automation for BlackCnote
REM This script safely resets Docker Desktop to factory defaults

echo ========================================
echo   Docker Desktop Factory Reset
echo   BlackCnote Automation
echo ========================================
echo.

REM Check if running as Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] This script must be run as Administrator!
    echo Please right-click this file and select "Run as administrator"
    pause
    exit /b 1
)

echo [INFO] Running with Administrator privileges
echo.
echo [WARNING] This will completely reset Docker Desktop to factory defaults.
echo [WARNING] All Docker containers, images, and settings will be removed.
echo [WARNING] A backup will be created before proceeding.
echo.
echo [INFO] The process will:
echo   1. Create a backup of current Docker configuration
echo   2. Stop all Docker processes
echo   3. Remove Docker data directories
echo   4. Reset Docker registry settings
echo   5. Unregister WSL2 Docker distributions
echo   6. Start Docker Desktop fresh
echo   7. Reapply BlackCnote configuration
echo   8. Test Docker functionality
echo.
echo [INFO] This may take 10-15 minutes to complete.
echo.

set /p confirm="Do you want to continue? (y/N): "
if /i not "%confirm%"=="y" (
    echo [INFO] Factory reset cancelled.
    pause
    exit /b 0
)

echo.
echo [INFO] Starting Docker Desktop factory reset...
echo [INFO] This may take several minutes. Please be patient.
echo.

REM Check if PowerShell script exists
if not exist "%~dp0automate-docker-factory-reset.ps1" (
    echo [ERROR] PowerShell script not found: %~dp0automate-docker-factory-reset.ps1
    echo [ERROR] Please ensure both .bat and .ps1 files are in the same directory.
    pause
    exit /b 1
)

echo [INFO] Found PowerShell script: %~dp0automate-docker-factory-reset.ps1
echo [INFO] Running PowerShell script with execution policy bypass...
echo.

REM Run the PowerShell script with full path and execution policy bypass
powershell.exe -ExecutionPolicy Bypass -NoProfile -File "%~dp0automate-docker-factory-reset.ps1"

if %errorLevel% neq 0 (
    echo.
    echo [ERROR] PowerShell script failed with error code: %errorLevel%
    echo [ERROR] Please check the error messages above and try again.
    pause
    exit /b 1
)

echo.
echo [INFO] Factory reset completed!
echo [INFO] Press any key to exit...
pause >nul 