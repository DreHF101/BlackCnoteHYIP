# BlackCnote Development Environment Startup Script
# ================================================
# Starts Docker Compose and the React development server

param(
    [switch]$SkipDocker = $false,
    [switch]$SkipReact = $false
)

Write-Host "Starting BlackCnote Development Environment..." -ForegroundColor Cyan

$ErrorActionPreference = 'Stop'

# Start Docker Compose (unless skipped)
if (-not $SkipDocker) {
    Write-Host "Checking Docker..." -ForegroundColor Yellow
    try {
        $dockerVersion = docker --version 2>$null
        if (-not $dockerVersion) { throw "Docker is not installed or not in PATH." }
        $dockerRunning = docker info 2>$null
        if (-not $dockerRunning) { throw "Docker is not running. Please start Docker Desktop." }
        Write-Host "Docker is running. Starting containers..." -ForegroundColor Green
        docker-compose up -d
        if ($LASTEXITCODE -eq 0) {
            Write-Host "Docker containers started." -ForegroundColor Green
        } else {
            Write-Host "Docker Compose failed to start containers." -ForegroundColor Red
        }
    } catch {
        Write-Host ("ERROR: " + $_) -ForegroundColor Red
    }
} else {
    Write-Host "Skipping Docker startup." -ForegroundColor Yellow
}

# Start React development server (unless skipped)
if (-not $SkipReact) {
    if (Test-Path "react-app") {
        Write-Host "Starting React development server..." -ForegroundColor Yellow
        if (-not (Test-Path "react-app/node_modules")) {
            Write-Host "Installing React dependencies..." -ForegroundColor Yellow
            Push-Location react-app
            npm install
            Pop-Location
        }
        Push-Location react-app
        Start-Process -FilePath "npm" -ArgumentList "run", "dev" -WindowStyle Hidden
        Pop-Location
        Write-Host "React dev server started (check your browser or terminal for output)." -ForegroundColor Green
    } else {
        Write-Host "ERROR: 'react-app' directory not found." -ForegroundColor Red
    }
} else {
    Write-Host "Skipping React dev server startup." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Access URLs (if all services started):" -ForegroundColor Cyan
Write-Host "   WordPress:      http://localhost:8888" -ForegroundColor White
Write-Host "   WP Admin:       http://localhost:8888/wp-admin/" -ForegroundColor White
Write-Host "   React App:      http://localhost:5174" -ForegroundColor White
Write-Host "   phpMyAdmin:     http://localhost:8080" -ForegroundColor White
Write-Host "   Redis Commander:http://localhost:8081" -ForegroundColor White
Write-Host "   MailHog:        http://localhost:8025" -ForegroundColor White
Write-Host ""
Write-Host "To stop: Run 'docker-compose down' and close the React dev server window." -ForegroundColor Yellow 