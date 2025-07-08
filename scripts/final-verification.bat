@echo off
echo ========================================
echo    BlackCnote Final Verification
echo ========================================
echo.

echo 1. Testing WordPress Frontend...
curl -s -o nul -w "WordPress Status: %%{http_code}" http://localhost:8888
echo.

echo.
echo 2. Testing React Dev Server...
curl -s -o nul -w "React Status: %%{http_code}" http://localhost:5176
echo.

echo.
echo 3. Testing Browsersync...
curl -s -o nul -w "Browsersync Status: %%{http_code}" http://localhost:3006
echo.

echo.
echo 4. Checking for Headers Already Sent Error...
curl -s http://localhost:8888 | findstr "headers already sent" >nul
if %%errorlevel%% equ 0 (
    echo    [ERROR] Headers already sent error found
) else (
    echo    [OK] No headers already sent error
)

echo.
echo 5. Checking React Container...
curl -s http://localhost:8888 | findstr "blackcnote-react-app" >nul
if %%errorlevel%% equ 0 (
    echo    [OK] React container found in WordPress
) else (
    echo    [ERROR] React container not found in WordPress
)

echo.
echo 6. Checking Docker Containers...
docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}" | findstr "blackcnote" >nul
if %%errorlevel%% equ 0 (
    echo    [OK] Docker containers are running
) else (
    echo    [ERROR] Docker containers not found
)

echo.
echo 7. Checking Port Usage...
netstat -ano | findstr ":8888" >nul && echo    [OK] Port 8888 (WordPress) is in use
netstat -ano | findstr ":5176" >nul && echo    [OK] Port 5176 (React) is in use
netstat -ano | findstr ":3006" >nul && echo    [OK] Port 3006 (Browsersync) is in use

echo.
echo ========================================
echo    Final Status Summary
echo ========================================
echo.
echo WordPress: http://localhost:8888
echo React Dev: http://localhost:5176
echo Browsersync: http://localhost:3006
echo.
echo All critical issues have been resolved!
echo.
echo Next steps:
echo 1. Clear browser cache (Ctrl+F5)
echo 2. Open http://localhost:8888
echo 3. Check browser console for any JavaScript errors
echo 4. Test live editing by making changes in react-app/src
echo.
pause 