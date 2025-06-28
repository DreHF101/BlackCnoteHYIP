# BlackCnote Complete Windows Startup Script
# Integrates WSL2, Docker, React, and all BlackCnote services
# Run as Administrator for full functionality

param(
    [switch]$SkipWSL2,
    [switch]$SkipDocker,
    [switch]$SkipReact,
    [switch]$ForceRebuild,
    [switch]$Quiet
)

# Set error action preference
$ErrorActionPreference = "Continue"

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

# Function to free port
function Stop-ProcessOnPort {
    param([int]$Port)
    $connection = Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue
    if ($connection) {
        $process = Get-Process -Id $connection.OwningProcess -ErrorAction SilentlyContinue
        if ($process) {
            Write-ColorOutput "[Port $Port] Killing process: $($process.ProcessName)" "Yellow"
            Stop-Process -Id $process.Id -Force -ErrorAction SilentlyContinue
            Start-Sleep -Seconds 2
        }
    }
}

# Main execution
Write-ColorOutput "=== BlackCnote Complete Windows Startup ===" "Cyan"
Write-ColorOutput "Starting at: $(Get-Date)" "White"

# Check administrator privileges
if (-not (Test-Administrator)) {
    Write-ColorOutput "[ERROR] Administrator privileges required" "Red"
    exit 1
}

# Set project root
$projectRoot = "$PSScriptRoot"
Set-Location $projectRoot
Write-ColorOutput "[INFO] Project root: $projectRoot" "White"

# 1. WSL2 Setup
if (-not $SkipWSL2) {
    Write-ColorOutput "[1/6] Setting up WSL2..." "Yellow"
    
    # Enable WSL2 features
    $wslFeature = (Get-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux).State
    $vmFeature = (Get-WindowsOptionalFeature -Online -FeatureName VirtualMachinePlatform).State
    
    if ($wslFeature -ne 'Enabled') {
        Write-ColorOutput "[WSL2] Enabling Windows Subsystem for Linux..." "Yellow"
        Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux -NoRestart -All
    }
    
    if ($vmFeature -ne 'Enabled') {
        Write-ColorOutput "[WSL2] Enabling Virtual Machine Platform..." "Yellow"
        Enable-WindowsOptionalFeature -Online -FeatureName VirtualMachinePlatform -NoRestart -All
    }
    
    # Set WSL2 as default
    wsl --set-default-version 2 2>$null
    
    # Check Ubuntu installation
    $installed = wsl --list --verbose 2>$null | Select-String -Pattern "Ubuntu"
    if (-not $installed) {
        Write-ColorOutput "[WSL2] Installing Ubuntu..." "Yellow"
        wsl --install -d Ubuntu --no-launch
        Write-ColorOutput "[WSL2] Ubuntu installation initiated. Complete setup and re-run." "Red"
        pause
        exit
    }
    
    # Start WSL2
    wsl --shutdown 2>$null
    Start-Sleep -Seconds 3
    wsl -d Ubuntu -e echo "WSL2 ready" 2>$null
}

# 2. Docker Setup
if (-not $SkipDocker) {
    Write-ColorOutput "[2/6] Setting up Docker..." "Yellow"
    
    # Start Docker Desktop
    $dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
    if (-not $dockerProcess) {
        Write-ColorOutput "[Docker] Starting Docker Desktop..." "Yellow"
        Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized
        Start-Sleep -Seconds 10
    }
    
    # Wait for Docker
    Write-ColorOutput "[Docker] Waiting for Docker..." "Yellow"
    $dockerReady = $false
    for ($i = 1; $i -le 30; $i++) {
        try {
            $dockerInfo = docker info 2>$null
            if ($LASTEXITCODE -eq 0) { $dockerReady = $true; break }
        }
        catch { }
        Start-Sleep -Seconds 2
    }
    
    if (-not $dockerReady) {
        Write-ColorOutput "[Docker] Docker failed to start" "Red"
        exit 1
    }
    Write-ColorOutput "[Docker] [OK] Ready" "Green"
}

# 3. Clean up
Write-ColorOutput "[3/6] Cleaning up..." "Yellow"
docker-compose down --volumes --remove-orphans 2>$null

# Free ports
$portsToCheck = @(8888, 5174, 8080, 8081, 9091, 3000, 3001, 8025)
foreach ($port in $portsToCheck) {
    $connection = Get-NetTCPConnection -LocalPort $port -ErrorAction SilentlyContinue
    if ($connection) {
        Stop-ProcessOnPort -Port $port
    }
}

# 4. React Setup
if (-not $SkipReact) {
    Write-ColorOutput "[4/6] Setting up React..." "Yellow"
    
    $reactAppPath = Join-Path $projectRoot "react-app"
    if (Test-Path $reactAppPath) {
        Set-Location $reactAppPath
        
        if ($ForceRebuild) {
            Write-ColorOutput "[React] Cleaning for fresh build..." "Yellow"
            Remove-Item -Recurse -Force node_modules, package-lock.json, dist, .vite -ErrorAction SilentlyContinue
        }
        
        Write-ColorOutput "[React] Installing dependencies..." "Yellow"
        npm install --silent
        
        Write-ColorOutput "[React] Building application..." "Yellow"
        npm run build --silent
        
        Write-ColorOutput "[React] Copying to WordPress theme..." "Yellow"
        $themeDistPath = Join-Path $projectRoot "blackcnote\wp-content\themes\blackcnote\dist"
        New-Item -ItemType Directory -Force -Path $themeDistPath | Out-Null
        Copy-Item -Path "dist\*" -Destination $themeDistPath -Recurse -Force
        
        Set-Location $projectRoot
    }
}

# 5. Create directories
Write-ColorOutput "[5/6] Creating directories..." "Yellow"
$directories = @("logs\wordpress", "logs\mysql", "logs\redis", "backups", "ssl", "blackcnote\wp-content\uploads", "blackcnote\wp-content\plugins", "blackcnote\wp-content\themes", "blackcnote\wp-content\mu-plugins")
foreach ($dir in $directories) {
    $fullPath = Join-Path $projectRoot $dir
    New-Item -ItemType Directory -Force -Path $fullPath | Out-Null
}

# 6. Start services
Write-ColorOutput "[6/6] Starting services..." "Yellow"

if ($ForceRebuild) {
    docker-compose build --no-cache
}

docker-compose up -d --build

# Wait for services
Start-Sleep -Seconds 10
Wait-ForService -Url "http://localhost:8888" -ServiceName "WordPress"
Start-Sleep -Seconds 5
Wait-ForService -Url "http://localhost:5174" -ServiceName "React App"
Start-Sleep -Seconds 5
Wait-ForService -Url "http://localhost:8080" -ServiceName "phpMyAdmin"

# Final status
Write-ColorOutput "`n=== BlackCnote Services ===" "Cyan"
Write-ColorOutput "WordPress:      http://localhost:8888" "White"
Write-ColorOutput "WordPress Admin: http://localhost:8888/wp-admin" "White"
Write-ColorOutput "React App:      http://localhost:5174" "White"
Write-ColorOutput "phpMyAdmin:     http://localhost:8080" "White"
Write-ColorOutput "Metrics:        http://localhost:9091" "White"
Write-ColorOutput "Health Check:   http://localhost:8888/health" "White"

Write-ColorOutput "`n[SUCCESS] BlackCnote startup complete!" "Green"
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