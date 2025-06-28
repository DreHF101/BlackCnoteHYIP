@echo off
REM BlackCnote Docker Diagnostics Wrapper
REM Runs the enhanced Docker diagnostics script with proper parameters

echo ========================================
echo   BlackCnote Docker Diagnostics
echo   Enhanced Testing and Monitoring
echo ========================================
echo.

REM Check if PowerShell script exists
if not exist "%~dp0test-docker-diagnostics.ps1" (
    echo [ERROR] PowerShell script not found: %~dp0test-docker-diagnostics.ps1
    echo [ERROR] Please ensure both .bat and .ps1 files are in the same directory.
    pause
    exit /b 1
)

echo [INFO] Found PowerShell script: %~dp0test-docker-diagnostics.ps1
echo [INFO] Running Docker diagnostics with BlackCnote integration...
echo.

REM Run the PowerShell script with execution policy bypass
powershell.exe -ExecutionPolicy Bypass -NoProfile -File "%~dp0test-docker-diagnostics.ps1" -Verbose

if %errorLevel% neq 0 (
    echo.
    echo [ERROR] Docker diagnostics failed with error code: %errorLevel%
    echo [ERROR] Please check the error messages above and try again.
    pause
    exit /b 1
)

echo.
echo [INFO] Docker diagnostics completed successfully!
echo [INFO] Check the log files for detailed information.
pause 