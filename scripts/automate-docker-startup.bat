@echo off
REM BlackCnote Docker Desktop Automation Script (Batch Wrapper)
REM Purpose: Automate Docker Desktop startup and fix engine connection issues
REM Author: BlackCnote Development Team
REM Version: 1.0.0

echo.
echo ========================================
echo   BlackCnote Docker Desktop Automation
echo ========================================
echo.

REM Check if PowerShell is available
powershell -Command "Write-Host 'PowerShell available'" >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PowerShell is not available on this system.
    echo Please install PowerShell and try again.
    pause
    exit /b 1
)

REM Check if script file exists
if not exist "%~dp0automate-docker-startup.ps1" (
    echo ERROR: PowerShell script not found: automate-docker-startup.ps1
    echo Please ensure the script is in the same directory as this batch file.
    pause
    exit /b 1
)

REM Parse command line arguments
set "PS_ARGS="
if "%1"=="-ForceRestart" set "PS_ARGS=-ForceRestart"
if "%1"=="-ResetWSL" set "PS_ARGS=-ResetWSL"
if "%1"=="-Verbose" set "PS_ARGS=-Verbose"
if "%2"=="-ForceRestart" set "PS_ARGS=%PS_ARGS% -ForceRestart"
if "%2"=="-ResetWSL" set "PS_ARGS=%PS_ARGS% -ResetWSL"
if "%2"=="-Verbose" set "PS_ARGS=%PS_ARGS% -Verbose"
if "%3"=="-ForceRestart" set "PS_ARGS=%PS_ARGS% -ForceRestart"
if "%3"=="-ResetWSL" set "PS_ARGS=%PS_ARGS% -ResetWSL"
if "%3"=="-Verbose" set "PS_ARGS=%PS_ARGS% -Verbose"

REM Show usage if help requested
if "%1"=="-h" goto :usage
if "%1"=="--help" goto :usage
if "%1"=="/?" goto :usage

REM Execute PowerShell script
echo Starting Docker Desktop automation...
echo.
powershell -ExecutionPolicy Bypass -File "%~dp0automate-docker-startup.ps1" %PS_ARGS%

REM Check exit code
if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo   Automation completed successfully!
    echo ========================================
    echo.
    echo Next steps:
    echo 1. Open your BlackCnote project directory
    echo 2. Run: docker-compose -f config/docker/docker-compose.yml up -d
    echo 3. Access WordPress at: http://localhost:8888
    echo 4. Access React app at: http://localhost:5174
    echo.
) else (
    echo.
    echo ========================================
    echo   Automation failed with errors!
    echo ========================================
    echo.
    echo Troubleshooting options:
    echo - Try running with -ForceRestart flag
    echo - Try running with -ResetWSL flag
    echo - Check Docker Desktop settings manually
    echo - Restart your computer and try again
    echo.
)

pause
exit /b %errorlevel%

:usage
echo.
echo USAGE: automate-docker-startup.bat [OPTIONS]
echo.
echo OPTIONS:
echo   -ForceRestart    Force restart Docker Desktop processes
echo   -ResetWSL        Reset WSL2 distributions and Docker data
echo   -Verbose         Enable verbose output
echo   -h, --help, /?   Show this help message
echo.
echo EXAMPLES:
echo   automate-docker-startup.bat
echo   automate-docker-startup.bat -ForceRestart
echo   automate-docker-startup.bat -ResetWSL
echo   automate-docker-startup.bat -ForceRestart -ResetWSL
echo.
pause
exit /b 0 