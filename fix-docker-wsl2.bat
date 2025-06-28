@echo off
setlocal enabledelayedexpansion

echo ==========================================
echo BlackCnote Docker WSL2 Fix
echo ==========================================
echo.

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ⚠ This script requires administrator privileges
    echo Please right-click and select "Run as administrator"
    pause
    exit /b 1
)

:: Check if WSL is available
wsl --version >nul 2>&1
if %errorLevel% neq 0 (
    echo ✗ WSL is not installed or not available
    echo Please install WSL2 first: wsl --install
    pause
    exit /b 1
)

:: Check if Docker CLI is available
docker --version >nul 2>&1
if %errorLevel% neq 0 (
    echo ✗ Docker CLI is not installed or not in PATH
    echo Please install Docker Desktop first
    pause
    exit /b 1
)

echo Step 1: Checking current WSL2 status...
wsl --list --verbose
echo.

echo Step 2: Restarting WSL2...
echo Shutting down WSL2...
wsl --shutdown
timeout /t 3 /nobreak >nul
echo Restarting WSL2...
wsl --update
echo ✓ WSL2 restarted successfully
echo.

echo Step 3: Starting Docker Desktop...
tasklist /FI "IMAGENAME eq Docker Desktop.exe" 2>NUL | find /I /N "Docker Desktop.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo ✓ Docker Desktop is already running
) else (
    echo Starting Docker Desktop...
    start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    echo ✓ Docker Desktop started successfully
)
echo.

echo Step 4: Waiting for Docker Desktop to initialize...
echo This may take up to 2 minutes...
timeout /t 30 /nobreak >nul
echo.

echo Step 5: Fixing Docker Desktop WSL2 integration...
wsl --list --verbose | findstr "docker-desktop" >nul
if %errorLevel% equ 0 (
    echo ✓ docker-desktop distro found
    echo Starting docker-desktop distro...
    wsl -d docker-desktop
    timeout /t 5 /nobreak >nul
    wsl --list --verbose | findstr "docker-desktop.*Running" >nul
    if %errorLevel% equ 0 (
        echo ✓ docker-desktop distro is now running
    ) else (
        echo ✗ docker-desktop distro failed to start
    )
) else (
    echo ⚠ docker-desktop distro not found, Docker Desktop may need reinstallation
)
echo.

echo Step 6: Testing Docker connection...
set attempts=0
set maxAttempts=5
set dockerWorking=0

:testLoop
set /a attempts+=1
echo Attempt %attempts% of %maxAttempts%...

docker info >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Docker connection successful
    set dockerWorking=1
    goto :dockerTestComplete
) else (
    echo ✗ Docker connection failed
)

if %attempts% lss %maxAttempts% (
    echo Waiting 10 seconds before next attempt...
    timeout /t 10 /nobreak >nul
    goto :testLoop
)

:dockerTestComplete
if %dockerWorking% equ 1 (
    echo.
    echo ==========================================
    echo ✓ Docker is now working properly!
    echo ==========================================
    echo.
    echo Final status check:
    wsl --list --verbose
    echo.
    echo Docker info:
    docker info | findstr "Server Version Operating System Kernel Version Total Memory"
) else (
    echo.
    echo ==========================================
    echo ✗ Docker is still not working
    echo ==========================================
    echo.
    if "%1"=="-Force" (
        echo Attempting Docker Desktop reset...
        echo Stopping Docker Desktop...
        taskkill /F /IM "Docker Desktop.exe" >nul 2>&1
        timeout /t 5 /nobreak >nul
        echo Resetting Docker Desktop settings...
        rmdir /s /q "%APPDATA%\Docker" >nul 2>&1
        rmdir /s /q "%LOCALAPPDATA%\Docker" >nul 2>&1
        echo Starting Docker Desktop...
        start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
        timeout /t 30 /nobreak >nul
        echo ✓ Docker Desktop reset completed
        echo Please wait a few minutes and test Docker again
    ) else (
        echo Try running with -Force flag to reset Docker Desktop:
        echo fix-docker-wsl2.bat -Force
    )
)

echo.
echo Script completed.
pause 