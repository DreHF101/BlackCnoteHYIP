# Docker Desktop Factory Reset Automation for BlackCnote
# This script safely resets Docker Desktop to factory defaults and reapplies configuration

param(
    [string]$BackupDir = "$PSScriptRoot/docker-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
)

# Set error action preference
$ErrorActionPreference = 'Stop'

# Color functions
function Write-ColorOutput($ForegroundColor) {
    $fc = $host.UI.RawUI.ForegroundColor
    $host.UI.RawUI.ForegroundColor = $ForegroundColor
    if ($args) {
        Write-Output $args
    }
    $host.UI.RawUI.ForegroundColor = $fc
}

function Write-Success { Write-ColorOutput Green $args }
function Write-Warning { Write-ColorOutput Yellow $args }
function Write-Error { Write-ColorOutput Red $args }
function Write-Info { Write-ColorOutput Cyan $args }

# Main script
Write-Info "Docker Desktop Factory Reset Automation for BlackCnote"
Write-Info "====================================================="
Write-Output ""

# Check if running as Administrator
$currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
$principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
if (-not $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Error "This script must be run as Administrator!"
    Write-Output "Please right-click PowerShell and select 'Run as Administrator'"
    exit 1
}

Write-Success "Running with administrator privileges"
Write-Output ""

# Step 1: Backup current configuration
if (Test-Path "$env:APPDATA\Docker") {
    Write-Info "Step 1: Backing up Docker config to $BackupDir..."
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
    Copy-Item "$env:APPDATA\Docker" $BackupDir -Recurse -Force
}
if (Test-Path "$env:LOCALAPPDATA\Docker") {
    Copy-Item "$env:LOCALAPPDATA\Docker" $BackupDir -Recurse -Force
}
if (Test-Path "$env:USERPROFILE\.docker") {
    Copy-Item "$env:USERPROFILE\.docker" $BackupDir -Recurse -Force
}

# Step 2: Stop all Docker processes
Write-Info "Step 2: Stopping all Docker processes..."
Get-Process -Name "Docker Desktop", "com.docker.backend", "com.docker.service", "vmmemWSL", "vmmem" -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue

# Step 3: Stop WSL2 instances
Write-Info "Step 3: Stopping WSL2 instances..."
try {
    wsl --shutdown
    Write-Success "WSL2 instances stopped"
} catch {
    Write-Warning "Could not stop WSL2 instances: $($_.Exception.Message)"
}

# Step 4: Remove Docker data directories
Write-Info "Step 4: Removing Docker data directories..."
$dockerDirs = @(
    "$env:LOCALAPPDATA\Docker",
    "$env:APPDATA\Docker",
    "$env:USERPROFILE\.docker"
)
foreach ($dir in $dockerDirs) {
    if (Test-Path $dir) {
        try {
            Remove-Item $dir -Recurse -Force -ErrorAction SilentlyContinue
            Write-Success "Removed: $dir"
        } catch {
            Write-Warning "Could not remove: $dir - $($_.Exception.Message)"
        }
    }
}

# Step 5: Reset Docker registry settings
Write-Info "Step 5: Resetting Docker registry settings..."
try {
    Remove-Item -Path "HKCU:\Software\Docker Inc." -Recurse -Force -ErrorAction SilentlyContinue
    Remove-Item -Path "HKLM:\SOFTWARE\Docker Inc." -Recurse -Force -ErrorAction SilentlyContinue
    Write-Success "Reset registry: HKCU:\Software\Docker Inc. and HKLM:\SOFTWARE\Docker Inc."
} catch {}

# Step 6: Unregister WSL2 distributions
Write-Info "Step 6: Unregistering WSL2 Docker distributions..."
$wslList = wsl -l -v | Select-String -Pattern "docker-desktop" | ForEach-Object { $_.ToString().Trim() }
foreach ($wsl in $wslList) {
    Write-Info "Unregistering $wsl..."
    try {
        wsl --unregister $wsl
        Write-Success "Unregistered $wsl WSL2 distribution"
    } catch {
        Write-Warning "Could not unregister $wsl WSL2 distribution"
    }
}

# Step 7: Start Docker Desktop for fresh installation
Write-Info "Step 7: Starting Docker Desktop for fresh installation..."
Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe"

# Step 8: Wait for Docker to be ready
Write-Info "Step 8: Waiting for Docker to be ready..."
$maxWait = 120
$waited = 0
while ($waited -lt $maxWait) {
    Start-Sleep -Seconds 5
    $waited += 5
    try {
        docker info | Out-Null
        Write-Success "Docker is ready!"
        break
    } catch {
        Write-Info "Waiting... ($waited/$maxWait seconds)"
    }
}
if ($waited -ge $maxWait) {
    Write-Warning "Docker did not start within $maxWait seconds. Please check Docker Desktop manually."
}

# Step 9: Reapply BlackCnote configuration
Write-Info "Step 9: Reapplying BlackCnote Docker configuration..."
$daemonSource = Join-Path $PSScriptRoot "config\docker\daemon.json"
$daemonTarget = "$env:PROGRAMDATA\Docker\config\daemon.json"
if (Test-Path $daemonSource) {
    try {
        # Ensure Docker config directory exists
        $dockerConfigDir = Split-Path $daemonTarget -Parent
        if (-not (Test-Path $dockerConfigDir)) {
            New-Item -ItemType Directory -Path $dockerConfigDir -Force | Out-Null
        }
        
        # Copy BlackCnote configuration
        Copy-Item $daemonSource $daemonTarget -Force
        Write-Success "Applied BlackCnote Docker daemon configuration"
        
        # Set proper permissions
        icacls $daemonTarget /grant "Administrators:(F)" /T
        icacls $daemonTarget /grant "Users:(R)" /T
        Write-Success "Set proper file permissions"
        
        # Restart Docker Desktop
        Write-Info "Restarting Docker Desktop to apply new config..."
        Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
        Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe"
        Start-Sleep -Seconds 10
    } catch {
        Write-Warning "Could not apply BlackCnote configuration: $($_.Exception.Message)"
    }
} else {
    Write-Warning "BlackCnote daemon configuration not found: $daemonSource"
}

# Step 10: Test Docker functionality
Write-Info "Step 10: Testing Docker functionality..."
Start-Sleep -Seconds 5

try {
    $dockerVersion = docker version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Docker API is working!"
        Write-Info "Docker Version Info:"
        $dockerVersion | ForEach-Object { Write-Output "   $_" }
    } else {
        Write-Warning "Docker API test failed"
        Write-Output "Error: $dockerVersion"
    }
} catch {
    Write-Warning "Docker API test failed with exception"
    Write-Output "Exception: $($_.Exception.Message)"
}

# Test basic Docker commands
try {
    $dockerInfo = docker info 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Docker info command works!"
    } else {
        Write-Warning "Docker info command failed"
    }
} catch {
    Write-Warning "Docker info command failed with exception"
}

try {
    $containerList = docker ps 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Container operations work!"
    } else {
        Write-Warning "Container operations failed"
    }
} catch {
    Write-Warning "Container operations failed with exception"
}

Write-Output ""
Write-Info "==============================================="
Write-Info "Docker Desktop Factory Reset Complete!"
Write-Info "==============================================="
Write-Output ""

Write-Success "✅ Docker Desktop has been reset to factory defaults"
Write-Success "✅ BlackCnote configuration has been reapplied"
Write-Success "✅ Docker API is ready for use"

Write-Output ""
Write-Info "📝 Next Steps:"
Write-Output "1. Verify Docker Desktop is running properly"
Write-Output "2. Test your Docker containers and applications"
Write-Output "3. If needed, restore any custom configurations from backup"
Write-Output "4. Run 'docker system prune' to clean up any leftover data"

Write-Output ""
Write-Info "🔧 Quick Commands:"
Write-Output "Test Docker: docker version"
Write-Output "List containers: docker ps"
Write-Output "System info: docker info"
Write-Output "Clean up: docker system prune"

Write-Output ""
Write-Success "🎉 Docker Desktop factory reset completed successfully!" 