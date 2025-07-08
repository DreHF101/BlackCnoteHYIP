# BlackCnote Docker Startup Script
# Simple script to apply BlackCnote configuration and start Docker Desktop

Write-Host "=== BLACKCNOTE DOCKER STARTUP ===" -ForegroundColor Cyan
Write-Host ""

# Step 1: Apply BlackCnote Docker configuration
Write-Host "Step 1: Applying BlackCnote Docker configuration..." -ForegroundColor Yellow
$sourceConfig = "config/docker/daemon.json"
$targetConfig = "$env:USERPROFILE\.docker\daemon.json"
$targetDir = Split-Path $targetConfig -Parent

if (-not (Test-Path $targetDir)) {
    New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
}

if (Test-Path $sourceConfig) {
    Copy-Item $sourceConfig $targetConfig -Force
    Write-Host "✅ BlackCnote daemon configuration applied" -ForegroundColor Green
} else {
    Write-Host "❌ BlackCnote daemon configuration not found" -ForegroundColor Red
}

Write-Host ""

# Step 2: Start Docker Desktop
Write-Host "Step 2: Starting Docker Desktop..." -ForegroundColor Yellow
Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Normal
Write-Host "✅ Docker Desktop started" -ForegroundColor Green

Write-Host ""
Write-Host "Docker Desktop is starting. Please wait 2-3 minutes for full initialization." -ForegroundColor Yellow
Write-Host "After initialization, run: docker info" -ForegroundColor White
Write-Host "" 