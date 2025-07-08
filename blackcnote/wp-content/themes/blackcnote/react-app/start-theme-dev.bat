@echo off
echo Starting BlackCnote React App from Theme Directory...
echo.

REM Check if Node.js is installed
node --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Node.js is not installed or not in PATH
    echo Please install Node.js from https://nodejs.org/
    pause
    exit /b 1
)

REM Check if npm is installed
npm --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: npm is not installed or not in PATH
    echo Please install npm or use a Node.js installer that includes npm
    pause
    exit /b 1
)

REM Navigate to the React app directory
cd /d "%~dp0"

REM Check if package.json exists
if not exist "package.json" (
    echo Creating package.json from template...
    copy "package.theme.json" "package.json" >nul
    if errorlevel 1 (
        echo ERROR: Failed to create package.json
        pause
        exit /b 1
    )
)

REM Check if node_modules exists, if not install dependencies
if not exist "node_modules" (
    echo Installing dependencies...
    npm install
    if errorlevel 1 (
        echo ERROR: Failed to install dependencies
        pause
        exit /b 1
    )
)

REM Check if vite.config.ts exists, if not copy from template
if not exist "vite.config.ts" (
    echo Creating vite.config.ts from template...
    copy "vite.config.theme.ts" "vite.config.ts" >nul
    if errorlevel 1 (
        echo ERROR: Failed to create vite.config.ts
        pause
        exit /b 1
    )
)

echo.
echo Starting development server on port 5175...
echo React App will be available at: http://localhost:5175
echo WordPress integration will be available at: http://localhost:8888
echo.
echo Press Ctrl+C to stop the server
echo.

REM Start the development server
npm run dev:theme

pause 