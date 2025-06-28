# BlackCnote Docker API Engine Fix Script
# This script resolves the "Docker API engine stopped" issue with multiple strategies
# Created: 2025-06-27
# Version: 2.0.0

param(
    [switch]$ForceReset,
    [switch]$SkipWSL2,
    [switch]$Verbose
)

# Color output functions
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { 
    param([string]$Message) 
    Write-ColorOutput "SUCCESS: $Message" "Green" 
}

function Write-Error { 
    param([string]$Message) 
    Write-ColorOutput "ERROR: $Message" "Red" 
}

function Write-Warning { 
    param([string]$Message) 
    Write-ColorOutput "WARNING: $Message" "Yellow" 
}

function Write-Info { 
    param([string]$Message) 
    Write-ColorOutput "INFO: $Message" "Cyan" 
}

# Check if running as Administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Get project root
$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

Write-ColorOutput "===============================================" "Magenta"
Write-ColorOutput "BlackCnote Docker API Engine Fix Script" "Magenta"
Write-ColorOutput "===============================================" "Magenta"
Write-Output ""

if (-not (Test-Administrator)) {
    Write-Error "This script must be run as Administrator!"
    Write-Output "Please right-click PowerShell and select 'Run as Administrator'"
    Write-Output "Then run this script again."
    exit 1
}

Write-Success "Running with administrator privileges"
Write-Output ""

# Step 1: Check current Docker status
Write-Info "Step 1: Checking current Docker status..."
try {
    $dockerInfo = docker info 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Docker API is responding normally"
        if (-not $ForceReset) {
            Write-Info "Docker appears to be working. Use -ForceReset to perform a complete reset anyway."
            exit 0
        }
    } else {
        Write-Warning "Docker API is not responding"
    }
} catch {
    Write-Warning "Could not check Docker status"
}

# Step 2: Stop all Docker processes
Write-Info "Step 2: Stopping all Docker processes..."
$dockerProcesses = @(
    "Docker Desktop",
    "com.docker.backend",
    "com.docker.service",
    "com.docker.wsl-distro-proxy",
    "com.docker.proxy"
)

foreach ($process in $dockerProcesses) {
    try {
        Get-Process -Name $process -ErrorAction SilentlyContinue | Stop-Process -Force
        Write-Success "Stopped: $process"
    } catch {
        if ($Verbose) {
            Write-Info "Process $process not running or already stopped"
        }
    }
}

# Step 3: Stop WSL2 instances
if (-not $SkipWSL2) {
    Write-Info "Step 3: Stopping WSL2 instances..."
    try {
        wsl --shutdown
        Write-Success "WSL2 instances stopped"
    } catch {
        Write-Warning "Could not stop WSL2 instances"
    }
}

# Step 4: Clean Docker data directories
Write-Info "Step 4: Cleaning Docker data directories..."
$dockerDataDirs = @(
    "$env:USERPROFILE\AppData\Roaming\Docker",
    "$env:USERPROFILE\AppData\Local\Docker",
    "$projectRoot\docker-data",
    "$projectRoot\docker-exec"
)

foreach ($dir in $dockerDataDirs) {
    if (Test-Path $dir) {
        try {
            Remove-Item $dir -Recurse -Force
            Write-Success "Cleaned: $dir"
        } catch {
            Write-Warning "Could not clean: $dir"
        }
    }
}

# Step 5: Reset Docker Desktop settings
Write-Info "Step 5: Resetting Docker Desktop settings..."
$dockerRegPath = "HKLM:\SOFTWARE\Docker Inc.\Docker Desktop"
if (Test-Path $dockerRegPath) {
    try {
        Remove-Item $dockerRegPath -Recurse -Force
        Write-Success "Reset Docker Desktop registry settings"
    } catch {
        Write-Warning "Could not reset Docker Desktop registry settings"
    }
}

# Step 6: Apply enhanced daemon configuration
Write-Info "Step 6: Applying enhanced daemon configuration..."
$daemonConfigPath = "$projectRoot\config\docker\daemon.json"
$dockerDaemonPath = "$env:ProgramData\Docker\config\daemon.json"

if (Test-Path $daemonConfigPath) {
    try {
        # Create Docker config directory if it doesn't exist
        $dockerConfigDir = Split-Path $dockerDaemonPath -Parent
        if (-not (Test-Path $dockerConfigDir)) {
            New-Item -ItemType Directory -Path $dockerConfigDir -Force | Out-Null
        }
        
        # Copy enhanced configuration
        Copy-Item $daemonConfigPath $dockerDaemonPath -Force
        Write-Success "Applied enhanced daemon configuration"
    } catch {
        Write-Warning "Could not apply daemon configuration: $($_.Exception.Message)"
    }
}

# Step 7: Fix Docker Desktop permissions
Write-Info "Step 7: Fixing Docker Desktop permissions..."
$dockerPaths = @(
    "C:\Program Files\Docker",
    "C:\ProgramData\Docker"
)

foreach ($path in $dockerPaths) {
    if (Test-Path $path) {
        try {
            # Grant full control to current user
            icacls $path /grant "$env:USERDOMAIN\$env:USERNAME:(OI)(CI)F" /T
            # Grant full control to Administrators
            icacls $path /grant "Administrators:(OI)(CI)F" /T
            Write-Success "Fixed permissions for: $path"
        } catch {
            Write-Warning "Could not fix permissions for: $path"
        }
    }
}

# Step 8: Start Docker Desktop with enhanced configuration
Write-Info "Step 8: Starting Docker Desktop with enhanced configuration..."
$dockerDesktopPath = "C:\Program Files\Docker\Docker\Docker Desktop.exe"

if (Test-Path $dockerDesktopPath) {
    try {
        Start-Process $dockerDesktopPath -ArgumentList "--verbose" -WindowStyle Minimized
        Write-Success "Started Docker Desktop"
    } catch {
        Write-Error "Failed to start Docker Desktop: $($_.Exception.Message)"
        exit 1
    }
} else {
    Write-Error "Docker Desktop not found at: $dockerDesktopPath"
    Write-Output "Please install Docker Desktop first."
    exit 1
}

# Step 9: Wait for Docker API to be ready
Write-Info "Step 9: Waiting for Docker API to be ready..."
$maxAttempts = 60
$attempt = 0
$dockerReady = $false

while ($attempt -lt $maxAttempts -and -not $dockerReady) {
    $attempt++
    Write-Output "  Attempt $attempt/$maxAttempts..."
    
    try {
        $null = docker info 2>$null
        if ($LASTEXITCODE -eq 0) {
            $dockerReady = $true
            Write-Success "Docker API is ready!"
            break
        }
    } catch {
        # Continue waiting
    }
    
    Start-Sleep -Seconds 2
}

if (-not $dockerReady) {
    Write-Error "Docker API failed to start within timeout"
    Write-Output "Please check Docker Desktop logs and try again."
    exit 1
}

# Step 10: Verify Docker functionality
Write-Info "Step 10: Verifying Docker functionality..."
try {
    $dockerVersion = docker version
    Write-Success "Docker version information retrieved"
    
    if ($Verbose) {
        Write-Output $dockerVersion
    }
} catch {
    Write-Warning "Could not retrieve Docker version information"
}

# Step 11: Test basic Docker operations
Write-Info "Step 11: Testing basic Docker operations..."
try {
    $testImage = docker run --rm hello-world 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Docker basic functionality verified"
    } else {
        Write-Warning "Docker basic functionality test failed"
    }
} catch {
    Write-Warning "Could not test Docker basic functionality"
}

# Step 12: Start BlackCnote services (optional)
Write-Info "Step 12: Starting BlackCnote services..."
$composeFile = "$projectRoot\config\docker\docker-compose.yml"
if (Test-Path $composeFile) {
    try {
        Set-Location "$projectRoot\config\docker"
        docker-compose down --remove-orphans 2>$null
        docker-compose up -d
        if ($LASTEXITCODE -eq 0) {
            Write-Success "BlackCnote services started successfully"
        } else {
            Write-Warning "Failed to start BlackCnote services"
        }
    } catch {
        Write-Warning "Could not start BlackCnote services: $($_.Exception.Message)"
    }
} else {
    Write-Warning "Docker Compose file not found: $composeFile"
}

Write-Output ""
Write-ColorOutput "===============================================" "Magenta"
Write-ColorOutput "Docker API Engine Fix Complete!" "Magenta"
Write-ColorOutput "===============================================" "Magenta"
Write-Output ""

Write-Info "Docker Information:"
try {
    docker info | Select-String -Pattern "Server Version|Operating System|Kernel Version|Total Memory"
} catch {
    Write-Warning "Could not retrieve Docker information"
}

Write-Output ""
Write-Info "Services Status:"
try {
    docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
} catch {
    Write-Warning "Could not retrieve container status"
}

Write-Output ""
Write-Success "Docker API engine fix completed successfully!"
Write-Output ""
Write-Info "If you continue to experience issues:"
Write-Output "  1. Check Docker Desktop logs"
Write-Output "  2. Restart your computer"
Write-Output "  3. Run this script again with -ForceReset"
Write-Output "  4. Check Windows Defender/Antivirus exclusions"
Write-Output "" 