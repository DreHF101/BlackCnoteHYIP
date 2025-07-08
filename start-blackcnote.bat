@echo off
REM BlackCnote Unified Startup Script - Batch Wrapper
REM This script provides a user-friendly way to start BlackCnote

echo.
echo ========================================
echo    BlackCnote Unified Startup
echo ========================================
echo.
echo This script will start all BlackCnote services:
echo - WordPress (http://localhost:8888)
echo - React App (http://localhost:5174)
echo - phpMyAdmin (http://localhost:8080)
echo - Redis Commander (http://localhost:8081)
echo - MailHog (http://localhost:8025)
echo - Browsersync (http://localhost:3000)
echo - Dev Tools (http://localhost:9229)
echo.

REM Check if PowerShell is available
powershell -Command "Get-Host" >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] PowerShell is not available
    echo Please install PowerShell and try again
    pause
    exit /b 1
)

REM Run the PowerShell script
powershell.exe -ExecutionPolicy Bypass -File "start-blackcnote.ps1"

if %errorLevel% == 0 (
    echo.
    echo ========================================
    echo    BlackCnote Started Successfully!
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
) else (
    echo.
    echo [ERROR] Failed to start BlackCnote
    echo Please check the logs and try again
    echo.
)

pause
