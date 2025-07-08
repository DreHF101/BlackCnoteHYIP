# BlackCnote React App Theme Development Starter
# This script starts the React app from the WordPress theme directory

Write-Host "Starting BlackCnote React App from Theme Directory..." -ForegroundColor Green
Write-Host ""

# Check if Node.js is installed
try {
    $nodeVersion = node --version 2>$null
    if ($LASTEXITCODE -ne 0) {
        throw "Node.js not found"
    }
    Write-Host "Node.js version: $nodeVersion" -ForegroundColor Cyan
} catch {
    Write-Host "ERROR: Node.js is not installed or not in PATH" -ForegroundColor Red
    Write-Host "Please install Node.js from https://nodejs.org/" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

# Check if npm is installed
try {
    $npmVersion = npm --version 2>$null
    if ($LASTEXITCODE -ne 0) {
        throw "npm not found"
    }
    Write-Host "npm version: $npmVersion" -ForegroundColor Cyan
} catch {
    Write-Host "ERROR: npm is not installed or not in PATH" -ForegroundColor Red
    Write-Host "Please install npm or use a Node.js installer that includes npm" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

# Navigate to the React app directory
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $scriptDir

Write-Host "Working directory: $(Get-Location)" -ForegroundColor Cyan

# Check if package.json exists, if not create from template
if (-not (Test-Path "package.json")) {
    Write-Host "Creating package.json from template..." -ForegroundColor Yellow
    try {
        Copy-Item "package.theme.json" "package.json" -Force
        Write-Host "package.json created successfully" -ForegroundColor Green
    } catch {
        Write-Host "ERROR: Failed to create package.json" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
}

# Check if node_modules exists, if not install dependencies
if (-not (Test-Path "node_modules")) {
    Write-Host "Installing dependencies..." -ForegroundColor Yellow
    try {
        npm install
        if ($LASTEXITCODE -ne 0) {
            throw "npm install failed"
        }
        Write-Host "Dependencies installed successfully" -ForegroundColor Green
    } catch {
        Write-Host "ERROR: Failed to install dependencies" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
}

# Check if vite.config.ts exists, if not copy from template
if (-not (Test-Path "vite.config.ts")) {
    Write-Host "Creating vite.config.ts from template..." -ForegroundColor Yellow
    try {
        Copy-Item "vite.config.theme.ts" "vite.config.ts" -Force
        Write-Host "vite.config.ts created successfully" -ForegroundColor Green
    } catch {
        Write-Host "ERROR: Failed to create vite.config.ts" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
}

Write-Host ""
Write-Host "Starting development server on port 5175..." -ForegroundColor Green
Write-Host "React App will be available at: http://localhost:5175" -ForegroundColor Cyan
Write-Host "WordPress integration will be available at: http://localhost:8888" -ForegroundColor Cyan
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host ""

# Start the development server
try {
    npm run dev:theme
} catch {
    Write-Host "ERROR: Failed to start development server" -ForegroundColor Red
    Write-Host "Error details: $_" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
} 