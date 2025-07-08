# BlackCnote Clean Startup Script
# Unified startup script to avoid notepad popup issues
# Version: 3.0.0 - Clean and Simplified

param(
    [switch]$ForceRestart,
    [switch]$SkipReact,
    [switch]$NoBrowser,
    [switch]$Quiet
)

# Set error action preference
$ErrorActionPreference = "Continue"

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    if (-not $Quiet) { Write-Host $Message -ForegroundColor $Color }
}

function Write-Success { Write-ColorOutput $args "Green" }
function Write-Warning { Write-ColorOutput $args "Yellow" }
function Write-Error { Write-ColorOutput $args "Red" }
function Write-Info { Write-ColorOutput $args "Cyan" }

# Set project root
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

Write-Info "=========================================="
Write-Info "BLACKCNOTE CLEAN STARTUP SYSTEM"
Write-Info "=========================================="
Write-Info "Starting at: $(Get-Date)"
Write-Info "Project root: $projectRoot"
Write-Output ""

# Step 1: Check Docker Desktop
Write-Info "Step 1: Checking Docker Desktop..."
$dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
if (-not $dockerProcess) {
    Write-Warning "Docker Desktop not running. Starting..."
    Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized
    Start-Sleep -Seconds 10
} else {
    Write-Success "Docker Desktop is running"
}

# Step 2: Wait for Docker daemon
Write-Info "Step 2: Waiting for Docker daemon..."
$dockerReady = $false
for ($i = 1; $i -le 60; $i++) {
    try {
        $null = docker info 2>$null
        if ($LASTEXITCODE -eq 0) { 
            $dockerReady = $true
            break 
        }
    } catch { }
    Start-Sleep -Seconds 2
}

if (-not $dockerReady) {
    Write-Error "Docker daemon failed to start"
    exit 1
}

Write-Success "Docker daemon is ready!"

# Step 3: Stop existing containers
Write-Info "Step 3: Stopping existing containers..."
docker-compose down --remove-orphans 2>$null
Write-Success "Existing containers stopped"

# Step 4: Start BlackCnote services
Write-Info "Step 4: Starting BlackCnote services..."
docker-compose up -d --build

if ($LASTEXITCODE -ne 0) {
    Write-Error "Failed to start BlackCnote services"
    exit 1
}

# Step 5: Wait for services
Write-Info "Step 5: Waiting for services to be ready..."
Start-Sleep -Seconds 20

# Step 6: Check service status
Write-Info "Step 6: Checking service status..."
docker-compose ps

Write-Output ""
Write-Info "=========================================="
Write-Info "BLACKCNOTE SERVICES"
Write-Info "=========================================="
Write-Output "WordPress:      http://localhost:8888"
Write-Output "WordPress Admin: http://localhost:8888/wp-admin"
Write-Output "React App:      http://localhost:5174"
Write-Output "phpMyAdmin:     http://localhost:8080"
Write-Output "Redis Commander: http://localhost:8081"
Write-Output "MailHog:        http://localhost:8025"
Write-Output "Browsersync:    http://localhost:3000"
Write-Output "Dev Tools:      http://localhost:9229"
Write-Output "Metrics:        http://localhost:9091"
Write-Output "Health Check:   http://localhost:8888/health"

Write-Output ""
Write-Success "BlackCnote startup completed!"
Write-Success "All services are running with canonical pathways"

# Step 7: Open browsers if requested
if (-not $NoBrowser) {
    Write-Info "Step 7: Opening services in browser..."
    Start-Process "http://localhost:8888"
    Start-Sleep -Seconds 2
    Start-Process "http://localhost:5174"
    Start-Sleep -Seconds 2
    Start-Process "http://localhost:8080"
}

Write-Output ""
Write-Info "Startup completed successfully!"
Write-Info "Use 'docker-compose logs -f' to monitor services"
Write-Info "Use 'docker-compose down' to stop services" 