@echo off
setlocal enabledelayedexpansion

echo ==========================================
echo BLACKCNOTE DOCKER WSL2 DISK CONFIGURATION
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

echo Step 1: Stopping Docker Desktop...
taskkill /F /IM "Docker Desktop.exe" >nul 2>&1
timeout /t 5 /nobreak >nul
echo Docker Desktop stopped.
echo.

echo Step 2: Creating Docker WSL2 disk image location...
set DOCKER_DISK_PATH=C:\DockerWSL2
if not exist "%DOCKER_DISK_PATH%" (
    mkdir "%DOCKER_DISK_PATH%"
    echo Created directory: %DOCKER_DISK_PATH%
) else (
    echo Directory already exists: %DOCKER_DISK_PATH%
)
echo.

echo Step 3: Creating Docker Desktop configuration...
set DOCKER_CONFIG_PATH=%USERPROFILE%\.docker
if not exist "%DOCKER_CONFIG_PATH%" (
    mkdir "%DOCKER_CONFIG_PATH%"
    echo Created Docker config directory: %DOCKER_CONFIG_PATH%
) else (
    echo Docker config directory already exists: %DOCKER_CONFIG_PATH%
)
echo.

echo Step 4: Creating daemon.json with WSL2 disk configuration...
set DAEMON_JSON=%DOCKER_CONFIG_PATH%\daemon.json

echo Creating daemon.json with WSL2 disk configuration...
(
echo {
echo   "data-root": "%DOCKER_DISK_PATH%",
echo   "storage-driver": "overlay2",
echo   "features": {
echo     "buildkit": true
echo   },
echo   "experimental": false,
echo   "debug": false,
echo   "log-driver": "json-file",
echo   "log-opts": {
echo     "max-size": "10m",
echo     "max-file": "3"
echo   }
echo }
) > "%DAEMON_JSON%"

echo Created daemon.json at: %DAEMON_JSON%
echo.

echo Step 5: Setting proper permissions...
icacls "%DOCKER_DISK_PATH%" /grant "Users:(OI)(CI)F" /T >nul 2>&1
icacls "%DOCKER_DISK_PATH%" /grant "Administrators:(OI)(CI)F" /T >nul 2>&1
echo Set permissions on Docker disk directory.
echo.

echo Step 6: Creating WSL2 configuration...
set WSL_CONFIG_PATH=%USERPROFILE%\.wslconfig
echo Creating .wslconfig file...
(
echo [wsl2]
echo memory=4GB
echo processors=2
echo localhostForwarding=true
echo kernelCommandLine=cgroup_enable=1 cgroup_memory=1 cgroup_v2=1
) > "%WSL_CONFIG_PATH%"

echo Created .wslconfig at: %WSL_CONFIG_PATH%
echo.

echo Step 7: Restarting WSL2...
echo Shutting down WSL2...
wsl --shutdown
timeout /t 5 /nobreak >nul
echo WSL2 shutdown completed.
echo.

echo Step 8: Starting Docker Desktop with new configuration...
start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
echo Docker Desktop starting with new disk configuration...
echo.

echo Step 9: Waiting for Docker Desktop to initialize...
echo This may take up to 3 minutes with new disk configuration...
timeout /t 120 /nobreak >nul
echo.

echo Step 10: Testing Docker connection...
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
    echo Waiting 15 seconds before next attempt...
    timeout /t 15 /nobreak >nul
    goto :testLoop
)

:dockerTestComplete

if %dockerWorking% equ 1 (
    echo.
    echo ==========================================
    echo SUCCESS: DOCKER WSL2 DISK CONFIGURATION COMPLETE!
    echo ==========================================
    echo.
    echo Configuration Summary:
    echo - Docker Disk Location: %DOCKER_DISK_PATH%
    echo - Docker Config: %DAEMON_JSON%
    echo - WSL2 Config: %WSL_CONFIG_PATH%
    echo.
    echo Final WSL2 Status:
    wsl --list --verbose
    echo.
    echo Final Docker Info:
    docker info | findstr "Server Version Operating System Kernel Version Total Memory Data Root"
    echo.
    echo Docker is now configured with dedicated disk location!
    echo All Docker data will be stored in: %DOCKER_DISK_PATH%
) else (
    echo.
    echo ==========================================
    echo FAILED: DOCKER STILL NOT WORKING
    echo ==========================================
    echo.
    echo Configuration was applied but Docker connection failed.
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