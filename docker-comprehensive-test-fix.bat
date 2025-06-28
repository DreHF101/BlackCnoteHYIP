@echo off
setlocal enabledelayedexpansion

echo ==========================================
echo BLACKCNOTE DOCKER COMPREHENSIVE TEST & FIX
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

echo PHASE 1: INITIAL STATUS CHECK
echo =============================
echo.

echo Checking WSL2 status...
wsl --list --verbose
echo.

echo Checking Docker Desktop status...
tasklist /FI "IMAGENAME eq Docker Desktop.exe" 2>NUL | find /I /N "Docker Desktop.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo ✓ Docker Desktop is running
) else (
    echo ✗ Docker Desktop is not running
)
echo.

echo Testing Docker connection...
docker info >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Docker connection successful
    set dockerWorking=1
) else (
    echo ✗ Docker connection failed
    set dockerWorking=0
)
echo.

echo PHASE 2: FIX ATTEMPTS
echo =====================
echo.

:: Fix WSL2
echo Fixing WSL2...
echo Shutting down WSL2...
wsl --shutdown
timeout /t 3 /nobreak >nul
echo Updating WSL2...
wsl --update
timeout /t 5 /nobreak >nul
echo ✓ WSL2 restart completed
echo.

:: Fix Docker Desktop
echo Fixing Docker Desktop...
tasklist /FI "IMAGENAME eq Docker Desktop.exe" 2>NUL | find /I /N "Docker Desktop.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo Docker Desktop is already running
) else (
    echo Starting Docker Desktop...
    start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    timeout /t 30 /nobreak >nul
    echo ✓ Docker Desktop started
)
echo.

:: Fix Docker Desktop WSL2 integration
echo Fixing Docker Desktop WSL2 integration...
wsl --list --verbose | findstr "docker-desktop" >nul
if %errorLevel% equ 0 (
    echo ✓ docker-desktop distro found
    echo Starting docker-desktop distro...
    wsl -d docker-desktop
    timeout /t 10 /nobreak >nul
    wsl --list --verbose | findstr "docker-desktop.*Running" >nul
    if %errorLevel% equ 0 (
        echo ✓ docker-desktop distro is now running
    ) else (
        echo ✗ docker-desktop distro failed to start
    )
) else (
    echo ⚠ docker-desktop distro not found
)
echo.

echo PHASE 3: FINAL TEST WITH RETRY
echo ===============================
echo.

set attempts=0
set maxAttempts=10
set finalDockerWorking=0

:testLoop
set /a attempts+=1
echo Attempt %attempts% of %maxAttempts%...

docker info >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Docker connection successful
    set finalDockerWorking=1
    goto :dockerTestComplete
) else (
    echo ✗ Docker connection failed
)

if %attempts% lss %maxAttempts% (
    echo Waiting 15 seconds before next attempt...
    timeout /t 15 /nobreak >nul
    goto :testLoop
)

:dockerTestComplete

:: Force reset if needed
if %finalDockerWorking% equ 0 (
    if "%1"=="-Force" (
        echo.
        echo PHASE 4: FORCE RESET
        echo ====================
        echo.
        echo Resetting Docker Desktop...
        echo Stopping Docker Desktop...
        taskkill /F /IM "Docker Desktop.exe" >nul 2>&1
        timeout /t 5 /nobreak >nul
        echo Removing Docker Desktop settings...
        rmdir /s /q "%APPDATA%\Docker" >nul 2>&1
        rmdir /s /q "%LOCALAPPDATA%\Docker" >nul 2>&1
        echo Starting Docker Desktop fresh...
        start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
        timeout /t 60 /nobreak >nul
        
        echo Testing Docker after reset...
        set attempts=0
        :resetTestLoop
        set /a attempts+=1
        echo Reset attempt %attempts% of 5...
        
        docker info >nul 2>&1
        if %errorLevel% equ 0 (
            echo ✓ Docker connection successful after reset
            set finalDockerWorking=1
            goto :finalResults
        ) else (
            echo ✗ Docker connection still failed
        )
        
        if %attempts% lss 5 (
            echo Waiting 15 seconds before next attempt...
            timeout /t 15 /nobreak >nul
            goto :resetTestLoop
        )
    )
)

:finalResults
echo.
echo PHASE 5: FINAL RESULTS
echo =======================
echo.

if %finalDockerWorking% equ 1 (
    echo ==========================================
    echo ✓ DOCKER IS NOW WORKING!
    echo ==========================================
    echo.
    echo Final WSL2 Status:
    wsl --list --verbose
    echo.
    echo Final Docker Info:
    docker info | findstr "Server Version Operating System Kernel Version Total Memory Containers Images"
    echo.
    echo ✓ All tests passed! Docker is ready for BlackCnote.
) else (
    echo ==========================================
    echo ✗ DOCKER IS STILL NOT WORKING
    echo ==========================================
    echo.
    echo Troubleshooting recommendations:
    echo 1. Restart your computer
    echo 2. Check Windows Defender/Antivirus exclusions
    echo 3. Verify WSL2 is properly installed: wsl --install
    echo 4. Check Docker Desktop settings
    echo 5. Try running this script again with -Force flag
)

echo.
echo Script completed.
pause 