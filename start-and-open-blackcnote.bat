@echo off
echo Starting BlackCnote Services...
echo.

:: Check if Docker is running
docker --version >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: Docker is not running
    echo Please start Docker Desktop first
    pause
    exit /b 1
)

echo Docker is running. Starting services...
echo.

:: Start services
docker-compose up -d

:: Wait for services to start
echo Waiting for services to start...
timeout /t 30 /nobreak >nul

:: Check service status
echo.
echo Service Status:
docker-compose ps

:: Open browser
echo.
echo Opening BlackCnote in browser...
start http://localhost:8888

echo.
echo BlackCnote should now be accessible at:
echo http://localhost:8888
echo.
echo If the page doesn't load, wait a few more minutes for services to fully start.
echo.
pause 