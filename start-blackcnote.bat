@echo off
REM BlackCnote Windows Startup Batch File
REM This file launches the PowerShell startup script with administrator privileges

echo ========================================
echo    BlackCnote Windows Startup
echo ========================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [INFO] Running with administrator privileges
) else (
    echo [ERROR] This script requires administrator privileges
    echo Please right-click and select "Run as administrator"
    pause
    exit /b 1
)

REM Set the project directory
set PROJECT_DIR=%~dp0
cd /d "%PROJECT_DIR%"

echo [INFO] Project directory: %PROJECT_DIR%
echo.

REM Launch the PowerShell startup script
echo [INFO] Launching BlackCnote startup script...
powershell.exe -ExecutionPolicy Bypass -File "start-blackcnote-complete.ps1"

REM Check if the script completed successfully
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
    echo - Health Check:   http://localhost:8888/health
    echo.
    
    REM Ask if user wants to open browser
    set /p OPEN_BROWSER="Would you like to open the services in your browser? (y/n): "
    if /i "%OPEN_BROWSER%"=="y" (
        start http://localhost:8888
        timeout /t 2 /nobreak >nul
        start http://localhost:5174
        timeout /t 2 /nobreak >nul
        start http://localhost:8080
    )
) else (
    echo.
    echo ========================================
    echo    BlackCnote Startup Failed!
    echo ========================================
    echo.
    echo Please check the error messages above.
    echo You can also run the PowerShell script directly:
    echo powershell.exe -ExecutionPolicy Bypass -File "start-blackcnote-complete.ps1"
)

echo.
echo Press any key to exit...
pause >nul