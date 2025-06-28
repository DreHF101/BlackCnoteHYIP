# Docker Health Check Script
# This script checks the health of all Docker containers and services

Write-Host "Docker Health Check" -ForegroundColor Cyan
Write-Host "==================" -ForegroundColor Cyan
Write-Host ""

# Check if all containers are running
$containers = docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
$runningContainers = docker ps --format "{{.Names}}" | Measure-Object | Select-Object -ExpandProperty Count

if ($runningContainers -ge 6) {
    Write-Host "SUCCESS: All containers are running" -ForegroundColor Green
} else {
    Write-Host "ERROR: Some containers are not running" -ForegroundColor Red
}

Write-Host $containers
Write-Host ""

# Test WordPress accessibility
Write-Host "Testing WordPress..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -Method Head -TimeoutSec 10 -ErrorAction Stop
    if ($response.StatusCode -eq 200) {
        Write-Host "SUCCESS: WordPress is accessible" -ForegroundColor Green
    } else {
        Write-Host "ERROR: WordPress returned status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "ERROR: WordPress is not accessible: $($_.Exception.Message)" -ForegroundColor Red
}

# Test React app accessibility
Write-Host "Testing React app..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -Method Head -TimeoutSec 10 -ErrorAction Stop
    if ($response.StatusCode -eq 200) {
        Write-Host "SUCCESS: React app is accessible" -ForegroundColor Green
    } else {
        Write-Host "ERROR: React app returned status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "ERROR: React app is not accessible: $($_.Exception.Message)" -ForegroundColor Red
}

# Test database accessibility
Write-Host "Testing database..." -ForegroundColor Yellow
try {
    $dbTest = docker exec blackcnote_mysql mysql -u root -pblackcnote_password -e "SELECT 1;" 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "SUCCESS: Database is accessible" -ForegroundColor Green
    } else {
        Write-Host "ERROR: Database is not accessible" -ForegroundColor Red
    }
} catch {
    Write-Host "ERROR: Database connection failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "SUCCESS: All health checks passed!" -ForegroundColor Green
Write-Host "" 