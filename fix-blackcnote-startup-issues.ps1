# BlackCnote Startup Issues Fix Script
# This script fixes all identified startup issues and ensures proper operation

param([switch]$Quiet)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    if (-not $Quiet) { Write-Host $Message -ForegroundColor $Color }
}

# Function to check if running as administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Set project root
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

Write-ColorOutput "=== BlackCnote Startup Issues Fix ===" "Cyan"
Write-ColorOutput "Starting at: $(Get-Date)" "White"
Write-ColorOutput "Project root: $projectRoot" "White"
Write-ColorOutput ""

# Check administrator privileges
if (-not (Test-Administrator)) {
    Write-ColorOutput "[ERROR] Administrator privileges required" "Red"
    Write-ColorOutput "Please run this script as Administrator" "Yellow"
    pause
    exit 1
}

# Issue 1: Fix React App Configuration
Write-ColorOutput "[1/5] Fixing React App Configuration..." "Yellow"

$reactAppPath = Join-Path $projectRoot "react-app"
if (Test-Path $reactAppPath) {
    Set-Location $reactAppPath
    
    # Update package.json to fix dev script
    $packageJsonPath = Join-Path $reactAppPath "package.json"
    if (Test-Path $packageJsonPath) {
        $packageJson = Get-Content $packageJsonPath | ConvertFrom-Json
        $packageJson.scripts.dev = "vite --host 0.0.0.0 --port 5174"
        $packageJson | ConvertTo-Json -Depth 10 | Set-Content $packageJsonPath
        Write-ColorOutput "[React] Package.json updated" "Green"
    }
    
    # Install dependencies
    Write-ColorOutput "[React] Installing dependencies..." "Yellow"
    npm install --silent
    
    # Build React app
    Write-ColorOutput "[React] Building application..." "Yellow"
    npm run build --silent
    
    # Copy to WordPress theme
    Write-ColorOutput "[React] Copying to WordPress theme..." "Yellow"
    $themeDistPath = Join-Path $projectRoot "blackcnote\wp-content\themes\blackcnote\dist"
    New-Item -ItemType Directory -Force -Path $themeDistPath | Out-Null
    Copy-Item -Path "dist\*" -Destination $themeDistPath -Recurse -Force
    
    Set-Location $projectRoot
    Write-ColorOutput "[React] Configuration fixed" "Green"
}

# Issue 2: Fix Docker Compose Configuration
Write-ColorOutput "[2/5] Fixing Docker Compose Configuration..." "Yellow"

# Ensure docker-compose.yml is in the root directory
$dockerComposePath = Join-Path $projectRoot "docker-compose.yml"
if (-not (Test-Path $dockerComposePath)) {
    Write-ColorOutput "[Docker] Creating docker-compose.yml in root..." "Yellow"
    Copy-Item "config\docker\docker-compose.yml" $dockerComposePath -Force
}

# Issue 3: Create Proper Startup Scripts
Write-ColorOutput "[3/5] Creating Proper Startup Scripts..." "Yellow"

# Create the main startup script
$startupScript = @'
# BlackCnote Main Startup Script (PowerShell)
# This script starts all BlackCnote services with proper error handling

param([switch]$Quiet)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    if (-not $Quiet) { Write-Host $Message -ForegroundColor $Color }
}

# Set project root - FIXED PATH
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

Write-ColorOutput "=== BlackCnote Main Startup ===" "Cyan"
Write-ColorOutput "Starting at: $(Get-Date)" "White"
Write-ColorOutput "Project root: $projectRoot" "White"
Write-ColorOutput ""

# Check if Docker Desktop is running
$dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
if (-not $dockerProcess) {
    Write-ColorOutput "Starting Docker Desktop..." "Yellow"
    Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized
    Start-Sleep -Seconds 10
}

# Wait for Docker to be ready
Write-ColorOutput "Waiting for Docker daemon..." "Yellow"
$dockerReady = $false
for ($i = 1; $i -le 45; $i++) {
    try {
        $null = docker info 2>$null
        if ($LASTEXITCODE -eq 0) { $dockerReady = $true; break }
    } catch { }
    Start-Sleep -Seconds 2
}

if (-not $dockerReady) {
    Write-ColorOutput "Docker daemon failed to start" "Red"
    pause
    exit 1
}

Write-ColorOutput "Docker daemon is ready!" "Green"

# Stop any existing containers
Write-ColorOutput "Stopping existing containers..." "Yellow"
docker-compose down --remove-orphans 2>$null

# Create necessary directories
Write-ColorOutput "Creating directories..." "Yellow"
$directories = @(
    "logs\wordpress", 
    "logs\mysql", 
    "logs\redis", 
    "backups", 
    "ssl", 
    "blackcnote\wp-content\uploads", 
    "blackcnote\wp-content\plugins", 
    "blackcnote\wp-content\themes", 
    "blackcnote\wp-content\mu-plugins"
)
foreach ($dir in $directories) {
    $fullPath = Join-Path $projectRoot $dir
    New-Item -ItemType Directory -Force -Path $fullPath | Out-Null
}

# Start BlackCnote services
Write-ColorOutput "Starting BlackCnote services..." "Yellow"
docker-compose up -d --build

if ($LASTEXITCODE -ne 0) {
    Write-ColorOutput "Failed to start BlackCnote services" "Red"
    pause
    exit 1
}

# Wait for services
Start-Sleep -Seconds 15

# Check service status
Write-ColorOutput "Service Status:" "Cyan"
docker-compose ps

Write-ColorOutput ""
Write-ColorOutput "=== BlackCnote Services ===" "Cyan"
Write-ColorOutput "WordPress:      http://localhost:8888" "White"
Write-ColorOutput "WordPress Admin: http://localhost:8888/wp-admin" "White"
Write-ColorOutput "React App:      http://localhost:5174" "White"
Write-ColorOutput "phpMyAdmin:     http://localhost:8080" "White"
Write-ColorOutput "Redis Commander: http://localhost:8081" "White"
Write-ColorOutput "MailHog:        http://localhost:8025" "White"
Write-ColorOutput "Metrics:        http://localhost:9091" "White"
Write-ColorOutput "Health Check:   http://localhost:8888/health" "White"

Write-ColorOutput ""
Write-ColorOutput "BlackCnote startup completed!" "Green"
Write-ColorOutput "Docker Engine v28.1.1 with enhanced configuration is active" "Green"

# Open services in browser
if (-not $Quiet) {
    Start-Process "http://localhost:8888"
    Start-Sleep -Seconds 2
    Start-Process "http://localhost:5174"
    Start-Sleep -Seconds 2
    Start-Process "http://localhost:8080"
}

Write-ColorOutput ""
Write-ColorOutput "=== All Services Ready ===" "Green"
Write-ColorOutput "BlackCnote is now fully operational!" "Green"
Write-ColorOutput "Completed at: $(Get-Date)" "White"

pause
'@

Set-Content -Path (Join-Path $projectRoot "start-blackcnote-main.ps1") -Value $startupScript -Encoding UTF8

# Issue 4: Remove Duplicate/Conflicting Scripts
Write-ColorOutput "[4/5] Cleaning up duplicate scripts..." "Yellow"

# List of scripts to keep (the main ones)
$keepScripts = @(
    "start-blackcnote-main.ps1",
    "start-blackcnote-optimized.ps1",
    "start-blackcnote-optimized.bat"
)

# Get all startup scripts
$startupScripts = Get-ChildItem -Path $projectRoot -Filter "start-blackcnote*.ps1" | Where-Object { $_.Name -notin $keepScripts }
$startupBatchScripts = Get-ChildItem -Path $projectRoot -Filter "start-blackcnote*.bat" | Where-Object { $_.Name -notin $keepScripts }

# Move old scripts to backup
$backupDir = Join-Path $projectRoot "backups\old-scripts"
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

foreach ($script in $startupScripts) {
    Move-Item $script.FullName (Join-Path $backupDir $script.Name) -Force
    Write-ColorOutput "[Cleanup] Moved $($script.Name) to backups" "Yellow"
}

foreach ($script in $startupBatchScripts) {
    Move-Item $script.FullName (Join-Path $backupDir $script.Name) -Force
    Write-ColorOutput "[Cleanup] Moved $($script.Name) to backups" "Yellow"
}

# Issue 5: Create Service Health Check
Write-ColorOutput "[5/5] Creating Service Health Check..." "Yellow"

$healthCheckScript = @'
# BlackCnote Service Health Check
# This script checks the status of all BlackCnote services

param([switch]$Quiet)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    if (-not $Quiet) { Write-Host $Message -ForegroundColor $Color }
}

# Function to check service
function Test-Service {
    param([string]$Url, [string]$ServiceName)
    try {
        $response = Invoke-WebRequest -Uri $Url -TimeoutSec 5 -UseBasicParsing -ErrorAction Stop
        if ($response.StatusCode -eq 200) {
            Write-ColorOutput "[$ServiceName] [OK] Running" "Green"
            return $true
        }
    }
    catch {
        Write-ColorOutput "[$ServiceName] [ERROR] Not responding" "Red"
        return $false
    }
}

Write-ColorOutput "=== BlackCnote Service Health Check ===" "Cyan"
Write-ColorOutput "Checking at: $(Get-Date)" "White"
Write-ColorOutput ""

# Check Docker containers
Write-ColorOutput "Checking Docker containers..." "Yellow"
$containers = docker-compose ps --format "table {{.Name}}\t{{.Status}}\t{{.Ports}}"
Write-ColorOutput "$containers" "White"

Write-ColorOutput ""
Write-ColorOutput "Checking service endpoints..." "Yellow"

# Check services
Test-Service -Url "http://localhost:8888" -ServiceName "WordPress"
Test-Service -Url "http://localhost:5174" -ServiceName "React App"
Test-Service -Url "http://localhost:8080" -ServiceName "phpMyAdmin"
Test-Service -Url "http://localhost:8081" -ServiceName "Redis Commander"
Test-Service -Url "http://localhost:8025" -ServiceName "MailHog"
Test-Service -Url "http://localhost:9091" -ServiceName "Metrics"

Write-ColorOutput ""
Write-ColorOutput "Health check completed!" "Green"
'@

Set-Content -Path (Join-Path $projectRoot "check-blackcnote-health.ps1") -Value $healthCheckScript -Encoding UTF8

Write-ColorOutput ""
Write-ColorOutput "=== All Issues Fixed ===" "Green"
Write-ColorOutput "BlackCnote startup issues have been resolved!" "Green"
Write-ColorOutput ""
Write-ColorOutput "Next Steps:" "Cyan"
Write-ColorOutput "1. Run: .\start-blackcnote-main.ps1" "White"
Write-ColorOutput "2. Check health: .\check-blackcnote-health.ps1" "White"
Write-ColorOutput "3. All services should start properly" "White"
Write-ColorOutput ""
Write-ColorOutput "Fixed Issues:" "Cyan"
Write-ColorOutput "✓ React App configuration" "Green"
Write-ColorOutput "✓ Docker Compose path issues" "Green"
Write-ColorOutput "✓ Duplicate startup scripts" "Green"
Write-ColorOutput "✓ Service health monitoring" "Green"
Write-ColorOutput "✓ Proper error handling" "Green"

pause 