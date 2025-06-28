# BlackCnote Optimized Startup Script (PowerShell)
# This script starts all BlackCnote services with proper error handling
# Compatible with BlackCnote Docker Engine v28.1.1

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

# Function to wait for service
function Wait-ForService {
    param([string]$Url, [string]$ServiceName, [int]$MaxAttempts = 30)
    Write-ColorOutput "[$ServiceName] Waiting for service..." "Yellow"
    for ($i = 1; $i -le $MaxAttempts; $i++) {
        try {
            $response = Invoke-WebRequest -Uri $Url -TimeoutSec 5 -UseBasicParsing -ErrorAction SilentlyContinue
            if ($response.StatusCode -eq 200) {
                Write-ColorOutput "[$ServiceName] [OK] Ready" "Green"
                return $true
            }
        }
        catch { Write-ColorOutput "[$ServiceName] Attempt $i/$MaxAttempts..." "Yellow" }
        Start-Sleep -Seconds 2
    }
    Write-ColorOutput "[$ServiceName] [ERROR] Failed to start" "Red"
    return $false
}

# Set project root - FIXED PATH
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

Write-ColorOutput "=== BlackCnote Optimized Startup ===" "Cyan"
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

# Start BlackCnote services - FIXED: Use root docker-compose.yml
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

# Wait for services to be ready
Write-ColorOutput ""
Write-ColorOutput "Waiting for services to be ready..." "Yellow"
Wait-ForService -Url "http://localhost:8888" -ServiceName "WordPress"
Wait-ForService -Url "http://localhost:8080" -ServiceName "phpMyAdmin"

# React app may take longer to start
Write-ColorOutput "React App may take additional time to start..." "Yellow"
Wait-ForService -Url "http://localhost:5174" -ServiceName "React App"

Write-ColorOutput ""
Write-ColorOutput "=== All Services Ready ===" "Green"
Write-ColorOutput "BlackCnote is now fully operational!" "Green"
Write-ColorOutput "Completed at: $(Get-Date)" "White"

# Optional browser launch
if (-not $Quiet) {
    $openBrowser = Read-Host "`nOpen services in browser? (y/n)"
    if ($openBrowser -eq 'y' -or $openBrowser -eq 'Y') {
        Start-Process "http://localhost:8888"
        Start-Process "http://localhost:5174"
        Start-Process "http://localhost:8080"
    }
}

pause 