@echo off
REM Final Docker Desktop Fix for BlackCnote
REM This script fixes the WSL2 distribution issues

echo.
echo ========================================
echo   Docker Desktop Final Fix
echo ========================================
echo.

echo Current Status:
echo - Docker CLI: Available
echo - WSL2: Enabled
echo - docker-desktop distro: Stopped
echo - Ubuntu distro: Stopped
echo.

echo Starting WSL2 distributions...
echo.

echo Step 1: Starting Ubuntu distribution...
wsl -d Ubuntu -e echo "Ubuntu started" 2>nul
if %errorlevel% equ 0 (
    echo ✅ Ubuntu distribution started
) else (
    echo ❌ Failed to start Ubuntu
)

echo.
echo Step 2: Starting docker-desktop distribution...
wsl -d docker-desktop -e echo "Docker Desktop started" 2>nul
if %errorlevel% equ 0 (
    echo ✅ docker-desktop distribution started
) else (
    echo ❌ Failed to start docker-desktop
)

echo.
echo Step 3: Starting Docker Desktop...
start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"

echo.
echo Step 4: Waiting for Docker engine...
echo Please wait while Docker Desktop initializes...
echo.

timeout /t 30 /nobreak >nul

echo.
echo Step 5: Testing Docker engine...
docker info 2>nul
if %errorlevel% equ 0 (
    echo ✅ Docker engine is running!
    echo.
    echo 🎉 Docker Desktop is ready for BlackCnote!
    echo.
    echo Next steps:
    echo 1. Run: docker-compose -f config/docker/docker-compose.yml up -d
    echo 2. Access WordPress at: http://localhost:8888
    echo 3. Access React app at: http://localhost:5174
) else (
    echo ❌ Docker engine still not responding
    echo.
    echo Manual steps required:
    echo 1. Open Docker Desktop manually
    echo 2. Go to Settings > Resources > WSL Integration
    echo 3. Enable "Enable integration with my default WSL distro"
    echo 4. Click "Apply & Restart"
    echo 5. Wait for Docker to fully initialize
)

echo.
pause 