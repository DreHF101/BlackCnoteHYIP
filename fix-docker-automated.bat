@echo off
REM BlackCnote Docker Automated Fix - Batch Wrapper
REM Runs the PowerShell script with proper elevation and error handling

setlocal enabledelayedexpansion

echo ==========================================
echo   BlackCnote Docker Automated Fix
echo   Comprehensive Docker Troubleshooting
echo ==========================================
echo.

REM Check if PowerShell script exists
if not exist "%~dp0fix-docker-automated.ps1" (
    echo [ERROR] PowerShell script not found: %~dp0fix-docker-automated.ps1
    echo [ERROR] Please ensure both .bat and .ps1 files are in the same directory.
    pause
    exit /b 1
)

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [WARNING] This script may require administrator privileges for full functionality.
    echo [WARNING] If you encounter permission issues, right-click and "Run as administrator".
    echo.
)

REM Set PowerShell execution policy for this session
powershell -Command "Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process -Force" >nul 2>&1

echo [INFO] Starting automated Docker fix and diagnostics...
echo [INFO] This may take a few minutes...
echo.

REM Run the PowerShell script
powershell -ExecutionPolicy Bypass -File "%~dp0fix-docker-automated.ps1" %*

REM Capture the exit code
set EXIT_CODE=%errorLevel%

echo.
echo ==========================================
echo   Automated Fix Complete
echo ==========================================

REM Display result based on exit code
if %EXIT_CODE% equ 0 (
    echo [SUCCESS] Docker is fully functional!
    echo [SUCCESS] You can now use Docker commands normally.
) else if %EXIT_CODE% equ 1 (
    echo [WARNING] Docker CLI works but daemon connection failed.
    echo [WARNING] Try restarting Docker Desktop manually.
) else if %EXIT_CODE% equ 2 (
    echo [ERROR] Docker CLI is not working.
    echo [ERROR] Consider reinstalling Docker Desktop.
) else (
    echo [ERROR] Unexpected error occurred (Exit Code: %EXIT_CODE%).
    echo [ERROR] Check the log files for details.
)

echo.
echo [INFO] Check the log files for detailed diagnostics:
echo [INFO]   - Docker Fix: %~dp0logs\docker-automated-fix.log
echo [INFO]   - BlackCnote Debug: %~dp0blackcnote\wp-content\logs\blackcnote-debug.log
echo.

pause
exit /b %EXIT_CODE% 