@echo off
setlocal enabledelayedexpansion

echo ==========================================
echo BLACKCNOTE DOCKER DESKTOP DISTRO FIX
echo ==========================================
echo.

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo WARNING: This script requires administrator privileges
    echo Please right-click and select "Run as administrator"
    pause
    exit /b 1
)

echo Step 1: Checking current WSL2 status...
wsl --list --verbose
echo.

echo Step 2: Stopping Docker Desktop completely...
taskkill /F /IM "Docker Desktop.exe" >nul 2>&1
timeout /t 5 /nobreak >nul
echo Docker Desktop stopped.
echo.

echo Step 3: Shutting down WSL2 completely...
wsl --shutdown
timeout /t 5 /nobreak >nul
echo WSL2 shutdown completed.
echo.

echo Step 4: Starting Docker Desktop fresh...
start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
echo Docker Desktop starting...
echo.

echo Step 5: Waiting for Docker Desktop to initialize...
echo This may take up to 2 minutes...
timeout /t 60 /nobreak >nul
echo.

echo Step 6: Checking WSL2 status after Docker Desktop start...
wsl --list --verbose
echo.

echo Step 7: Testing Docker connection...
set attempts=0
set maxAttempts=10
set dockerWorking=0

:testLoop
set /a attempts+=1
echo Attempt %attempts% of %maxAttempts%...

docker info >nul 2>&1
if %errorLevel% equ 0 (
    echo SUCCESS: Docker connection working!
    set dockerWorking=1
    goto :dockerTestComplete
) else (
    echo FAILED: Docker connection failed
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
    echo SUCCESS: DOCKER IS NOW WORKING!
    echo ==========================================
    echo.
    echo Final WSL2 Status:
    wsl --list --verbose
    echo.
    echo Final Docker Info:
    docker info | findstr "Server Version Operating System Kernel Version Total Memory"
    echo.
    echo All tests passed! Docker is ready for BlackCnote.
) else (
    echo.
    echo ==========================================
    echo FAILED: DOCKER IS STILL NOT WORKING
    echo ==========================================
    echo.
    echo The docker-desktop distro may need to be recreated.
    echo Try these manual steps:
    echo 1. Open Docker Desktop manually
    echo 2. Go to Settings -^> General
    echo 3. Check "Use WSL 2 based engine"
    echo 4. Apply and restart Docker Desktop
    echo 5. Run this script again
)

echo.
echo Script completed.
pause 