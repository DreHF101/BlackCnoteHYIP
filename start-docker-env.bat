@echo off
REM BlackCnote Full Docker Environment Startup Script
REM This script starts ALL BlackCnote Docker services reliably
REM Created: 2025-06-25
REM Version: 1.4

echo ========================================
echo BlackCnote FULL Docker Environment Startup
echo ========================================
echo Starting at: %date% %time%
echo.

REM Check if Docker is running
echo Checking Docker status...
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker is not running!
    echo Please start Docker Desktop and try again.
    echo.
    pause
    exit /b 1
)
echo Docker is running.

REM Change to the Docker Compose directory
echo Changing to Docker Compose directory...
cd /d "%~dp0config\docker"
if %errorlevel% neq 0 (
    echo ERROR: Could not navigate to config\docker directory!
    pause
    exit /b 1
)
echo Current directory: %cd%

REM Stop existing containers
echo Stopping existing containers (if any)...
docker compose down
if %errorlevel% neq 0 (
    echo WARNING: Could not stop existing containers, continuing...
)

REM Start all services
echo Starting ALL Docker services...
docker compose up -d --remove-orphans
if %errorlevel% neq 0 (
    echo ERROR: Failed to start Docker services!
    pause
    exit /b 1
)

REM Wait for services to initialize
echo Waiting for services to initialize...
timeout /t 15 /nobreak >nul

REM Check service status
echo Checking service status...
docker compose ps

REM Check critical containers
echo.
echo Checking critical containers...
docker ps --format "table {{.Names}}\t{{.Status}}" | findstr "blackcnote"

echo.
echo ========================================
echo Docker Environment Started Successfully!
echo ========================================
echo.
echo Services available at:
echo - WordPress:    http://localhost:8888
echo - React App:    http://localhost:5174
echo - PHPMyAdmin:   http://localhost:8080
echo - Redis Cmdr:   http://localhost:8081
echo - MailHog:      http://localhost:8025
echo.
echo To stop services, run: docker compose down
echo To view logs, run:   docker compose logs -f
echo.
echo Startup completed at: %date% %time%
echo.
timeout /t 5 /nobreak >nul 