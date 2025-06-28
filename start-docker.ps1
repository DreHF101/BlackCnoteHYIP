# BlackCnote Docker Startup Script (Enhanced for WSL2)
Write-Host "Starting BlackCnote Docker Environment..." -ForegroundColor Green

# Check if WSL2 is available
$wslAvailable = $false
try {
    wsl --list --verbose | Out-Null
    $wslAvailable = $true
} catch {
    $wslAvailable = $false
}

if ($wslAvailable) {
    $useWSL2 = $true
    $response = Read-Host "WSL2 is available. Run BlackCnote in WSL2 for best performance? (Y/n)"
    if ($response -ne "n" -and $response -ne "N") {
        Write-Host "Launching WSL2 startup script..." -ForegroundColor Cyan
        wsl -d Ubuntu -- bash -c "cd ~/blackcnote && chmod +x ./start-blackcnote.sh && ./start-blackcnote.sh"
        exit 0
    } else {
        Write-Host "Continuing with legacy Windows Docker Compose..." -ForegroundColor Yellow
    }
}

# Legacy Windows Docker Compose startup
# Check if Docker is running
try {
    docker version | Out-Null
    Write-Host "Docker is running" -ForegroundColor Green
} catch {
    Write-Host "Docker is not running. Please start Docker Desktop first." -ForegroundColor Red
    exit 1
}

# Check if Docker Compose is available
try {
    docker-compose --version | Out-Null
    Write-Host "Docker Compose is available" -ForegroundColor Green
} catch {
    Write-Host "Docker Compose is not available. Please install Docker Compose." -ForegroundColor Red
    exit 1
}

# Create necessary directories
Write-Host "Creating directories..." -ForegroundColor Yellow
if (!(Test-Path "blackcnote/wp-content/uploads")) {
    New-Item -ItemType Directory -Path "blackcnote/wp-content/uploads" -Force | Out-Null
}
if (!(Test-Path "blackcnote/wp-content/plugins")) {
    New-Item -ItemType Directory -Path "blackcnote/wp-content/plugins" -Force | Out-Null
}
if (!(Test-Path "blackcnote/wp-content/themes")) {
    New-Item -ItemType Directory -Path "blackcnote/wp-content/themes" -Force | Out-Null
}
if (!(Test-Path "blackcnote/wp-content/mu-plugins")) {
    New-Item -ItemType Directory -Path "blackcnote/wp-content/mu-plugins" -Force | Out-Null
}

# Copy database dump if it exists
if (Test-Path "hyiplab/db/hyiplab.sql") {
    Write-Host "Copying database dump..." -ForegroundColor Yellow
    Copy-Item "hyiplab/db/hyiplab.sql" "db/blackcnote.sql" -Force
}

# Stop any existing containers
Write-Host "Stopping existing containers..." -ForegroundColor Yellow
docker-compose down 2>$null

# Build and start containers
Write-Host "Building and starting Docker containers..." -ForegroundColor Yellow
docker-compose up -d --build

# Wait for services to be ready
Write-Host "Waiting for services to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

Write-Host "=== BlackCnote Services Started ===" -ForegroundColor Green
Write-Host "WordPress:      http://localhost:8888" -ForegroundColor White
Write-Host "WordPress Admin: http://localhost:8888/wp-admin" -ForegroundColor White
Write-Host "React App:      http://localhost:5174" -ForegroundColor White
Write-Host "phpMyAdmin:     http://localhost:8080" -ForegroundColor White
Write-Host "MailHog:        http://localhost:8025" -ForegroundColor White
Write-Host "Redis Commander:http://localhost:8081" -ForegroundColor White
Write-Host "Prometheus:     http://localhost:9090" -ForegroundColor White
Write-Host "Grafana:        http://localhost:3000 (admin/admin)" -ForegroundColor White
Write-Host "Browsersync:    http://localhost:3000" -ForegroundColor White
Write-Host "Metrics Exporter: http://localhost:9091" -ForegroundColor White
Write-Host "All services are up!" -ForegroundColor Green 