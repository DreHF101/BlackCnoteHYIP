@echo off
REM BlackCnote Enhanced Docker Startup Script (Batch Wrapper)
REM Purpose: Wrapper for PowerShell startup script to avoid notepad popup
REM Author: BlackCnote Development Team
REM Version: 2.0.0

echo ========================================
echo    BlackCnote Enhanced Docker Startup
echo ========================================
echo.

REM Check if PowerShell is available
powershell -Command "Get-Host" >nul 2>&1
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
if not exist "scripts\automate-docker-startup.ps1" (
    echo ERROR: PowerShell startup script not found
    echo Expected location: scripts\automate-docker-startup.ps1
    pause
    exit /b 1
)

echo Starting BlackCnote Docker services...
echo.

REM Execute PowerShell script with proper parameters
powershell -ExecutionPolicy Bypass -File "scripts\automate-docker-startup.ps1" %*

REM Check if PowerShell script executed successfully
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Docker startup failed
    echo Check the output above for details
    echo.
    echo Troubleshooting tips:
    echo 1. Try running as Administrator
    echo 2. Use -ForceRestart flag: automate-docker-startup.bat -ForceRestart
    echo 3. Use -ResetWSL flag: automate-docker-startup.bat -ResetWSL
    echo 4. Check Docker Desktop is installed and updated
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
echo - Metrics:        http://localhost:9091
echo.
echo Press any key to exit...
pause >nul 