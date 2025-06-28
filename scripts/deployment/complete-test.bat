@echo off
echo BlackCnote Complete Test Suite
echo =============================
echo.

echo Step 1: Setting up environment...
powershell -ExecutionPolicy Bypass -File "scripts\setup-complete-environment.ps1"
if %errorlevel% neq 0 (
    echo Environment setup failed!
    pause
    exit /b 1
)

echo.
echo Step 2: Running standalone debug test...
php scripts\standalone-debug-test.php
if %errorlevel% neq 0 (
    echo Standalone debug test failed!
    pause
    exit /b 1
)

echo.
echo Step 3: Testing WordPress connection...
php scripts\test-wp-connection.php
if %errorlevel% neq 0 (
    echo WordPress connection test failed!
    pause
    exit /b 1
)

echo.
echo Step 4: Running security audit...
php scripts\standalone-security-audit.php
if %errorlevel% neq 0 (
    echo Security audit failed!
    pause
    exit /b 1
)

echo.
echo Step 5: Testing debug plugin activation...
php hyiplab\tools\activate-debug-system.php
if %errorlevel% neq 0 (
    echo Debug plugin activation failed!
    pause
    exit /b 1
)

echo.
echo ========================================
echo All tests completed successfully!
echo BlackCnote Debug Plugin is fully operational.
echo ========================================
pause 