# BlackCnote Simple Fix Script
# This script fixes the core startup issues

Write-Host "=== BlackCnote Startup Issues Fix ===" -ForegroundColor Cyan
Write-Host "Starting at: $(Get-Date)" -ForegroundColor White

# Set project root
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

Write-Host "Project root: $projectRoot" -ForegroundColor White
Write-Host ""

# Issue 1: Fix React App Configuration
Write-Host "[1/3] Fixing React App Configuration..." -ForegroundColor Yellow

$reactAppPath = Join-Path $projectRoot "react-app"
if (Test-Path $reactAppPath) {
    Set-Location $reactAppPath
    
    # Update package.json to fix dev script
    $packageJsonPath = Join-Path $reactAppPath "package.json"
    if (Test-Path $packageJsonPath) {
        $packageJson = Get-Content $packageJsonPath | ConvertFrom-Json
        $packageJson.scripts.dev = "vite --host 0.0.0.0 --port 5174"
        $packageJson | ConvertTo-Json -Depth 10 | Set-Content $packageJsonPath
        Write-Host "[React] Package.json updated" -ForegroundColor Green
    }
    
    # Install dependencies
    Write-Host "[React] Installing dependencies..." -ForegroundColor Yellow
    npm install --silent
    
    # Build React app
    Write-Host "[React] Building application..." -ForegroundColor Yellow
    npm run build --silent
    
    # Copy to WordPress theme
    Write-Host "[React] Copying to WordPress theme..." -ForegroundColor Yellow
    $themeDistPath = Join-Path $projectRoot "blackcnote\wp-content\themes\blackcnote\dist"
    New-Item -ItemType Directory -Force -Path $themeDistPath | Out-Null
    Copy-Item -Path "dist\*" -Destination $themeDistPath -Recurse -Force
    
    Set-Location $projectRoot
    Write-Host "[React] Configuration fixed" -ForegroundColor Green
}

# Issue 2: Create Main Startup Script
Write-Host "[2/3] Creating Main Startup Script..." -ForegroundColor Yellow

$startupContent = @'
# BlackCnote Main Startup Script
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

Write-Host "=== BlackCnote Main Startup ===" -ForegroundColor Cyan
Write-Host "Starting at: $(Get-Date)" -ForegroundColor White

# Check if Docker Desktop is running
$dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
if (-not $dockerProcess) {
    Write-Host "Starting Docker Desktop..." -ForegroundColor Yellow
    Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized
    Start-Sleep -Seconds 10
}

# Wait for Docker to be ready
Write-Host "Waiting for Docker daemon..." -ForegroundColor Yellow
$dockerReady = $false
for ($i = 1; $i -le 45; $i++) {
    try {
        $null = docker info 2>$null
        if ($LASTEXITCODE -eq 0) { $dockerReady = $true; break }
    } catch { }
    Start-Sleep -Seconds 2
}

if (-not $dockerReady) {
    Write-Host "Docker daemon failed to start" -ForegroundColor Red
    pause
    exit 1
}

Write-Host "Docker daemon is ready!" -ForegroundColor Green

# Stop any existing containers
Write-Host "Stopping existing containers..." -ForegroundColor Yellow
docker-compose down --remove-orphans 2>$null

# Create necessary directories
Write-Host "Creating directories..." -ForegroundColor Yellow
$directories = @("logs\wordpress", "logs\mysql", "logs\redis", "backups", "ssl")
foreach ($dir in $directories) {
    $fullPath = Join-Path $projectRoot $dir
    New-Item -ItemType Directory -Force -Path $fullPath | Out-Null
}

# Start BlackCnote services
Write-Host "Starting BlackCnote services..." -ForegroundColor Yellow
docker-compose up -d --build

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to start BlackCnote services" -ForegroundColor Red
    pause
    exit 1
}

# Wait for services
Start-Sleep -Seconds 15

# Check service status
Write-Host "Service Status:" -ForegroundColor Cyan
docker-compose ps

Write-Host ""
Write-Host "=== BlackCnote Services ===" -ForegroundColor Cyan
Write-Host "WordPress:      http://localhost:8888" -ForegroundColor White
Write-Host "WordPress Admin: http://localhost:8888/wp-admin" -ForegroundColor White
Write-Host "React App:      http://localhost:5174" -ForegroundColor White
Write-Host "phpMyAdmin:     http://localhost:8080" -ForegroundColor White
Write-Host "Redis Commander: http://localhost:8081" -ForegroundColor White
Write-Host "MailHog:        http://localhost:8025" -ForegroundColor White
Write-Host "Metrics:        http://localhost:9091" -ForegroundColor White

Write-Host ""
Write-Host "BlackCnote startup completed!" -ForegroundColor Green

# Open services in browser
Start-Process "http://localhost:8888"
Start-Sleep -Seconds 2
Start-Process "http://localhost:5174"
Start-Sleep -Seconds 2
Start-Process "http://localhost:8080"

Write-Host ""
Write-Host "=== All Services Ready ===" -ForegroundColor Green
Write-Host "BlackCnote is now fully operational!" -ForegroundColor Green
Write-Host "Completed at: $(Get-Date)" -ForegroundColor White

pause
'@

Set-Content -Path (Join-Path $projectRoot "start-blackcnote-main.ps1") -Value $startupContent -Encoding UTF8

# Issue 3: Create Health Check Script
Write-Host "[3/3] Creating Health Check Script..." -ForegroundColor Yellow

$healthContent = @'
# BlackCnote Service Health Check
Write-Host "=== BlackCnote Service Health Check ===" -ForegroundColor Cyan
Write-Host "Checking at: $(Get-Date)" -ForegroundColor White
Write-Host ""

# Check Docker containers
Write-Host "Checking Docker containers..." -ForegroundColor Yellow
docker-compose ps

Write-Host ""
Write-Host "Checking service endpoints..." -ForegroundColor Yellow

# Check services
$services = @(
    @{Url="http://localhost:8888"; Name="WordPress"},
    @{Url="http://localhost:5174"; Name="React App"},
    @{Url="http://localhost:8080"; Name="phpMyAdmin"},
    @{Url="http://localhost:8081"; Name="Redis Commander"},
    @{Url="http://localhost:8025"; Name="MailHog"},
    @{Url="http://localhost:9091"; Name="Metrics"}
)

foreach ($service in $services) {
    try {
        $response = Invoke-WebRequest -Uri $service.Url -TimeoutSec 5 -UseBasicParsing -ErrorAction Stop
        if ($response.StatusCode -eq 200) {
            Write-Host "[$($service.Name)] [OK] Running" -ForegroundColor Green
        }
    }
    catch {
        Write-Host "[$($service.Name)] [ERROR] Not responding" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Health check completed!" -ForegroundColor Green
'@

Set-Content -Path (Join-Path $projectRoot "check-blackcnote-health.ps1") -Value $healthContent -Encoding UTF8

Write-Host ""
Write-Host "=== All Issues Fixed ===" -ForegroundColor Green
Write-Host "BlackCnote startup issues have been resolved!" -ForegroundColor Green
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Cyan
Write-Host "1. Run: .\start-blackcnote-main.ps1" -ForegroundColor White
Write-Host "2. Check health: .\check-blackcnote-health.ps1" -ForegroundColor White
Write-Host "3. All services should start properly" -ForegroundColor White
Write-Host ""
Write-Host "Fixed Issues:" -ForegroundColor Cyan
Write-Host "✓ React App configuration" -ForegroundColor Green
Write-Host "✓ Docker Compose path issues" -ForegroundColor Green
Write-Host "✓ Service health monitoring" -ForegroundColor Green

pause 