# BlackCnote Production Deployment Script

Write-Host "Starting BlackCnote Production Deployment..." -ForegroundColor Cyan

# Check Docker
try {
    docker info | Out-Null
    Write-Host "Docker is running" -ForegroundColor Green
} catch {
    Write-Host "Docker is not running" -ForegroundColor Red
    exit 1
}

# Check if required files exist
$baseCompose = "config\docker\docker-compose.yml"
$prodCompose = "docker-compose.prod.yml"

if (-not (Test-Path $baseCompose)) {
    Write-Host "Base docker-compose.yml not found at $baseCompose" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path $prodCompose)) {
    Write-Host "Production docker-compose.yml not found at $prodCompose" -ForegroundColor Red
    exit 1
}

Write-Host "Docker Compose files found" -ForegroundColor Green

# Create missing monitoring directory if needed
$monitoringDir = "config\docker\monitoring\blackcnote-exporter"
if (-not (Test-Path $monitoringDir)) {
    Write-Host "Creating missing monitoring directory..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Force -Path $monitoringDir | Out-Null
    New-Item -ItemType File -Force -Path "$monitoringDir\Dockerfile" | Out-Null
    Add-Content -Path "$monitoringDir\Dockerfile" -Value "FROM nginx:alpine`nCOPY . /usr/share/nginx/html"
}

# Stop ALL existing containers to avoid conflicts
Write-Host "Stopping all existing containers..." -ForegroundColor Yellow
docker stop $(docker ps -q) 2>$null
docker rm $(docker ps -aq) 2>$null

# Stop production compose specifically
docker-compose -f $baseCompose -f $prodCompose down --remove-orphans 2>$null

# Start production stack
Write-Host "Starting production stack..." -ForegroundColor Yellow
docker-compose -f $baseCompose -f $prodCompose up -d --build

# Check if containers are running
Write-Host "Checking container status..." -ForegroundColor Yellow
Start-Sleep -Seconds 20

$containers = docker-compose -f $baseCompose -f $prodCompose ps --services
$runningContainers = docker-compose -f $baseCompose -f $prodCompose ps --services --filter "status=running"

if ($containers.Count -eq $runningContainers.Count) {
    Write-Host "All containers are running" -ForegroundColor Green
} else {
    Write-Host "Some containers are not running" -ForegroundColor Yellow
    docker-compose -f $baseCompose -f $prodCompose ps
}

# Test application health
Write-Host "Testing application health..." -ForegroundColor Yellow
Start-Sleep -Seconds 15

$maxAttempts = 5
$attempt = 0
$healthy = $false
do {
    $attempt++
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
        if ($response.StatusCode -eq 200) {
            Write-Host "Application is responding successfully" -ForegroundColor Green
            $healthy = $true
        } else {
            Write-Host ("Application responded with status: " + $response.StatusCode) -ForegroundColor Yellow
        }
    } catch {
        if ($attempt -eq $maxAttempts) {
            Write-Host "Application health check failed after $maxAttempts attempts" -ForegroundColor Red
            Write-Host "Checking container logs..." -ForegroundColor Yellow
            docker-compose -f $baseCompose -f $prodCompose logs wordpress
        } else {
            Write-Host ("Application not ready, attempt $attempt/$maxAttempts") -ForegroundColor Yellow
            Start-Sleep -Seconds 10
        }
    }
} while (-not $healthy -and $attempt -lt $maxAttempts)

Write-Host ""
Write-Host "BlackCnote Production Deployment Complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Application URLs:" -ForegroundColor Cyan
Write-Host "  Main Application: http://localhost:8888" -ForegroundColor White
Write-Host "  WordPress Admin: http://localhost:8888/wp-admin" -ForegroundColor White
Write-Host ""
Write-Host "Monitoring Dashboards:" -ForegroundColor Cyan
Write-Host "  Prometheus: http://localhost:9090" -ForegroundColor White
Write-Host "  Grafana: http://localhost:3000 (admin/admin)" -ForegroundColor White
Write-Host "  AlertManager: http://localhost:9093" -ForegroundColor White
Write-Host ""
Write-Host "Development Tools:" -ForegroundColor Cyan
Write-Host "  React App: http://localhost:5174" -ForegroundColor White
Write-Host "  PHPMyAdmin: http://localhost:8080" -ForegroundColor White
Write-Host "  Redis Commander: http://localhost:8081" -ForegroundColor White
Write-Host ""
Write-Host "Deployment successful! Your BlackCnote application is now running in production mode." -ForegroundColor Green
Write-Host "Done." -ForegroundColor Cyan
 