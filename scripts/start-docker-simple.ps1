# Simple Docker Desktop Startup Script
# BlackCnote Development Team

Write-Host "🐳 Starting Docker Desktop for BlackCnote..." -ForegroundColor Cyan

# Stop existing Docker processes
Write-Host "🛑 Stopping existing Docker processes..." -ForegroundColor Yellow
Get-Process -Name "*docker*" -ErrorAction SilentlyContinue | Stop-Process -Force
Get-Process -Name "*Docker*" -ErrorAction SilentlyContinue | Stop-Process -Force

# Shutdown WSL
Write-Host "🔄 Shutting down WSL..." -ForegroundColor Yellow
wsl --shutdown 2>$null
Start-Sleep -Seconds 3

# Start Docker Desktop
Write-Host "🚀 Starting Docker Desktop..." -ForegroundColor Green
$dockerPath = "${env:ProgramFiles}\Docker\Docker\Docker Desktop.exe"
if (Test-Path $dockerPath) {
    Start-Process -FilePath $dockerPath -WindowStyle Normal
    Write-Host "✅ Docker Desktop launched successfully!" -ForegroundColor Green
} else {
    Write-Host "❌ Docker Desktop not found at: $dockerPath" -ForegroundColor Red
    exit 1
}

# Wait for Docker engine
Write-Host "⏳ Waiting for Docker engine to start..." -ForegroundColor Yellow
$timeout = 120
$elapsed = 0

while ($elapsed -lt $timeout) {
    try {
        $null = docker info 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Docker engine is ready!" -ForegroundColor Green
            break
        }
    }
    catch {
        # Continue waiting
    }
    
    Start-Sleep -Seconds 2
    $elapsed += 2
    Write-Progress -Activity "Waiting for Docker Engine" -Status "Elapsed: ${elapsed}s" -PercentComplete (($elapsed / $timeout) * 100)
}

Write-Progress -Activity "Waiting for Docker Engine" -Completed

if ($elapsed -ge $timeout) {
    Write-Host "❌ Docker engine failed to start within timeout" -ForegroundColor Red
    Write-Host "💡 Try manually opening Docker Desktop and checking settings" -ForegroundColor Yellow
    exit 1
}

# Test Docker functionality
Write-Host "🔍 Testing Docker functionality..." -ForegroundColor Yellow
$dockerVersion = docker --version 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Docker CLI: $dockerVersion" -ForegroundColor Green
} else {
    Write-Host "❌ Docker CLI not working" -ForegroundColor Red
    exit 1
}

Write-Host "🎉 Docker Desktop is ready for BlackCnote development!" -ForegroundColor Green
Write-Host "📋 Next steps:" -ForegroundColor Cyan
Write-Host "  1. Run: docker-compose -f config/docker/docker-compose.yml up -d" -ForegroundColor White
Write-Host "  2. Access WordPress at: http://localhost:8888" -ForegroundColor White
Write-Host "  3. Access React app at: http://localhost:5174" -ForegroundColor White 