@echo off
echo ========================================
echo    BlackCnote Final Test Results
echo ========================================
echo.

echo 1. Testing WordPress Frontend...
curl -s -o nul -w "WordPress Status: %%{http_code}" http://localhost:8888
echo.

echo.
echo 2. Testing React Dev Server...
curl -s -o nul -w "React Status: %%{http_code}" http://localhost:5175
echo.

echo.
echo 3. Testing Browsersync...
curl -s -o nul -w "Browsersync Status: %%{http_code}" http://localhost:3002
echo.

echo.
echo 4. Checking for PHP Errors...
curl -s http://localhost:8888 | findstr "Warning\|Fatal error\|Parse error\|headers already sent" >nul
if %errorlevel% equ 0 (
    echo    [ERROR] PHP errors found in WordPress output
) else (
    echo    [OK] No PHP errors found
)

echo.
echo 5. Checking React Container...
curl -s http://localhost:8888 | findstr "blackcnote-react-app" >nul
if %errorlevel% equ 0 (
    echo    [OK] React container found in WordPress
) else (
    echo    [WARNING] React container not found in WordPress
)

echo.
echo 6. Checking Docker Containers...
docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}" | findstr "blackcnote" >nul
if %errorlevel% equ 0 (
    echo    [OK] Docker containers are running
) else (
    echo    [ERROR] Docker containers not found
)

echo.
echo ========================================
echo    Test Summary
echo ========================================
echo.
echo WordPress: http://localhost:8888
echo React Dev: http://localhost:5175
echo Browsersync: http://localhost:3002
echo.
echo If React container is missing, check:
echo 1. Browser console for JavaScript errors
echo 2. Network tab for failed asset loads
echo 3. Clear browser cache (Ctrl+F5)
echo.
pause 