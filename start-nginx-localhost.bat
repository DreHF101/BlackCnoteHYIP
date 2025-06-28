@echo off
echo Starting BlackCnote Localhost Nginx Setup...

REM Check if nginx is already running
tasklist /FI "IMAGENAME eq nginx.exe" 2>NUL | find /I /N "nginx.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo Nginx is already running. Stopping it first...
    taskkill /F /IM nginx.exe
    timeout /t 2 /nobreak >nul
)

REM Set the path to the nginx configuration
set CONFIG_PATH=%~dp0config\nginx\localhost.conf

REM Test the configuration
echo Testing nginx configuration...
nginx -t -c "%CONFIG_PATH%"
if %ERRORLEVEL% NEQ 0 (
    echo Configuration test failed!
    pause
    exit /b 1
)

REM Start nginx with the configuration
echo Starting nginx with localhost configuration...
nginx -c "%CONFIG_PATH%"
if %ERRORLEVEL% NEQ 0 (
    echo Failed to start nginx!
    pause
    exit /b 1
)

REM Wait a moment for nginx to start
timeout /t 2 /nobreak >nul

REM Check if nginx is running
tasklist /FI "IMAGENAME eq nginx.exe" 2>NUL | find /I /N "nginx.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo.
    echo Nginx is running successfully!
    echo.
    echo Available URLs:
    echo   WordPress Admin (Port 8888): http://localhost:8888/wp-admin/
    echo   WordPress Admin (Path): http://localhost/blackcnote/wp-admin/
    echo   React App: http://localhost:5174/
    echo   React App (via nginx): http://localhost/
    echo.
    echo To stop nginx, run: taskkill /F /IM nginx.exe
) else (
    echo Nginx failed to start!
    pause
    exit /b 1
)

pause 