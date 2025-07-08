@echo off
REM Manual Docker Desktop Fix for BlackCnote
REM This script provides step-by-step instructions to fix Docker Desktop

echo.
echo ========================================
echo   Docker Desktop Manual Fix Guide
echo ========================================
echo.

echo Current Docker Status:
docker --version 2>nul
if %errorlevel% equ 0 (
    echo Docker CLI: Available
) else (
    echo Docker CLI: Not found
)

echo.
echo ========================================
echo   STEP-BY-STEP FIX INSTRUCTIONS
echo ========================================
echo.

echo STEP 1: Stop Docker Desktop
echo - Right-click Docker Desktop icon in system tray
echo - Select "Quit Docker Desktop"
echo - Wait 30 seconds
echo.

echo STEP 2: Check WSL2 Status
echo - Open PowerShell as Administrator
echo - Run: wsl --status
echo - Run: wsl --list --verbose
echo.

echo STEP 3: Restart Docker Desktop
echo - Start Docker Desktop from Start Menu
echo - Wait for it to fully initialize (1-2 minutes)
echo.

echo STEP 4: Verify WSL2 Integration
echo - Open Docker Desktop
echo - Go to Settings ^> Resources ^> WSL Integration
echo - Enable "Enable integration with my default WSL distro"
echo - Click "Apply ^& Restart"
echo.

echo STEP 5: Test Docker
echo - Open PowerShell or Command Prompt
echo - Run: docker info
echo - Run: docker run hello-world
echo.

echo STEP 6: Start BlackCnote
echo - Navigate to your BlackCnote project directory
echo - Run: docker-compose -f config/docker/docker-compose.yml up -d
echo - Access WordPress at: http://localhost:8888
echo - Access React app at: http://localhost:5174
echo.

echo ========================================
echo   TROUBLESHOOTING OPTIONS
echo ========================================
echo.

echo If Docker still won't start:
echo 1. Restart your computer
echo 2. Run Windows Update
echo 3. Check Windows Event Viewer for errors
echo 4. Factory reset Docker Desktop (Settings ^> Troubleshoot)
echo 5. Reinstall Docker Desktop
echo.

echo ========================================
echo   QUICK COMMANDS
echo ========================================
echo.

echo To check Docker status: docker info
echo To check WSL2 status: wsl --status
echo To shutdown WSL2: wsl --shutdown
echo To list WSL2 distros: wsl --list --verbose
echo.

pause 