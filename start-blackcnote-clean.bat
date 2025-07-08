@echo off
REM BlackCnote Clean Startup Script (Batch Wrapper)
REM Purpose: Clean wrapper for PowerShell startup script to avoid notepad popup
REM Version: 3.0.0 - Clean and Simplified

echo ========================================
echo    BlackCnote Clean Startup System
echo ========================================
echo.

REM Check if PowerShell is available
powershell -Command "Write-Host 'PowerShell available'" >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PowerShell is not available
    echo Please install PowerShell and try again
    pause
    exit /b 1
)

REM Set the project directory
set "PROJECT_DIR=C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"

REM Check if project directory exists
if not exist "%PROJECT_DIR%" (
    echo ERROR: Project directory not found: %PROJECT_DIR%
    echo Please ensure the BlackCnote project is in the correct location
    pause
    exit /b 1
)

REM Change to project directory
cd /d "%PROJECT_DIR%"

REM Check if PowerShell script exists
if not exist "start-blackcnote-clean.ps1" (
    echo ERROR: PowerShell startup script not found
    echo Expected location: start-blackcnote-clean.ps1
    pause
    exit /b 1
)

echo Starting BlackCnote services...
echo.

REM Execute PowerShell script with proper parameters
powershell -ExecutionPolicy Bypass -File "start-blackcnote-clean.ps1" %*

REM Check if PowerShell script executed successfully
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Docker startup failed
    echo Check the output above for details
    echo.
    echo Troubleshooting tips:
    echo 1. Try running as Administrator
    echo 2. Check Docker Desktop is installed and updated
    echo 3. Ensure WSL2 is enabled
    echo.
    pause
    exit /b 1
)

echo.
echo ========================================
echo    Startup completed successfully!
echo ========================================
echo.
echo Services available at:
echo - WordPress:      http://localhost:8888
echo - WordPress Admin: http://localhost:8888/wp-admin
echo - React App:      http://localhost:5174
echo - phpMyAdmin:     http://localhost:8080
echo - Redis Commander: http://localhost:8081
echo - MailHog:        http://localhost:8025
echo - Browsersync:    http://localhost:3000
echo - Dev Tools:      http://localhost:9229
echo - Metrics:        http://localhost:9091
echo.
echo Press any key to exit...
pause >nul 