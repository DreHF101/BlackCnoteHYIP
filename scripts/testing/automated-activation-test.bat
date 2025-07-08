@echo off
REM BlackCnote Automated Activation Test
REM This batch file runs the comprehensive system activation test

echo.
echo ========================================
echo   BLACKCNOTE AUTOMATED ACTIVATION TEST
echo ========================================
echo.

REM Check if PowerShell is available
powershell -Command "Get-Host" >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PowerShell is not available or not working properly.
    echo Please ensure PowerShell is installed and accessible.
    pause
    exit /b 1
)

REM Get the script directory
set "SCRIPT_DIR=%~dp0"
set "PROJECT_ROOT=%SCRIPT_DIR%..\.."

REM Change to project root directory
cd /d "%PROJECT_ROOT%"

echo Starting automated activation test...
echo Project Root: %PROJECT_ROOT%
echo Timestamp: %date% %time%
echo.

REM Run the PowerShell test script
powershell -ExecutionPolicy Bypass -File "%SCRIPT_DIR%automated-activation-test.ps1" %*

REM Check the exit code
if %errorlevel% equ 0 (
    echo.
    echo ✅ Test completed successfully!
) else (
    echo.
    echo ❌ Test completed with errors or warnings.
)

echo.
echo Press any key to exit...
pause >nul 