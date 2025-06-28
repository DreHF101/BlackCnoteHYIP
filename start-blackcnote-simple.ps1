# Simple BlackCnote Startup Script
Write-Host "Starting BlackCnote Services..." -ForegroundColor Green

# Check if Docker is running
try {
    $dockerVersion = docker --version
    Write-Host "Docker is running: $dockerVersion" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Docker is not running" -ForegroundColor Red
    Write-Host "Please start Docker Desktop first" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

# Start services
Write-Host "Starting Docker Compose services..." -ForegroundColor Yellow
docker-compose up -d

# Wait for services
Write-Host "Waiting for services to start..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Check status
Write-Host "Service Status:" -ForegroundColor Cyan
docker-compose ps

# Open browser
Write-Host "Opening BlackCnote in browser..." -ForegroundColor Green
Start-Process "http://localhost:8888"

Write-Host ""
Write-Host "BlackCnote should now be accessible at:" -ForegroundColor Green
Write-Host "http://localhost:8888" -ForegroundColor Cyan
Write-Host ""
Write-Host "If the page doesn't load, wait a few more minutes for services to fully start." -ForegroundColor Yellow
Write-Host ""
Read-Host "Press Enter to continue" 