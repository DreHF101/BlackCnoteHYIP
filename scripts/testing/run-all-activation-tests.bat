@echo off
REM BlackCnote Complete Activation Test Suite
REM This script runs all activation tests and provides a comprehensive summary

echo.
echo ========================================
echo   BLACKCNOTE ACTIVATION TEST SUITE
echo ========================================
echo.

REM Get the script directory
set "SCRIPT_DIR=%~dp0"
set "PROJECT_ROOT=%SCRIPT_DIR%..\.."

REM Change to project root directory
cd /d "%PROJECT_ROOT%"

echo Starting comprehensive BlackCnote activation test suite...
echo Project Root: %PROJECT_ROOT%
echo Timestamp: %date% %time%
echo.

REM Create reports directory if it doesn't exist
if not exist "reports" mkdir reports

REM Run PowerShell test
echo.
echo [1/3] Running PowerShell System Test...
echo ----------------------------------------
powershell -ExecutionPolicy Bypass -File "%SCRIPT_DIR%basic-activation-test.ps1" > reports\powershell-test-results.txt 2>&1
if %errorlevel% equ 0 (
    echo ✅ PowerShell test completed successfully
) else (
    echo ❌ PowerShell test completed with errors
)

REM Run PHP test
echo.
echo [2/3] Running PHP Quick Test...
echo ------------------------------
php "%SCRIPT_DIR%quick-activation-test.php" > reports\php-test-results.txt 2>&1
if %errorlevel% equ 0 (
    echo ✅ PHP test completed successfully
) else (
    echo ❌ PHP test completed with errors
)

REM Run Docker status check
echo.
echo [3/3] Running Docker Status Check...
echo -----------------------------------
docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" > reports\docker-status.txt 2>&1
if %errorlevel% equ 0 (
    echo ✅ Docker status check completed successfully
) else (
    echo ❌ Docker status check completed with errors
)

REM Generate summary
echo.
echo ========================================
echo   TEST SUMMARY
echo ========================================
echo.

echo PowerShell Test Results:
echo -----------------------
type reports\powershell-test-results.txt | findstr /C:"Total Tests:" /C:"PASSED:" /C:"FAILED:" /C:"Success Rate:" /C:"SYSTEM STATUS:"

echo.
echo PHP Test Results:
echo ----------------
type reports\php-test-results.txt | findstr /C:"Total Tests:" /C:"PASSED:" /C:"FAILED:" /C:"Success Rate:" /C:"BLACKCNOTE STATUS:"

echo.
echo Docker Container Status:
echo -----------------------
type reports\docker-status.txt

echo.
echo ========================================
echo   RECOMMENDATIONS
echo ========================================
echo.

echo • Review any failed tests and address issues
echo • Check the detailed test reports in the reports/ directory
echo • Monitor system health regularly
echo • Run this test suite after any system changes
echo • Keep Docker containers updated and healthy

echo.
echo ========================================
echo   TEST REPORTS SAVED
echo ========================================
echo.
echo PowerShell Test: reports\powershell-test-results.txt
echo PHP Test: reports\php-test-results.txt
echo Docker Status: reports\docker-status.txt
echo.

echo Test suite completed! Press any key to exit...
pause >nul 