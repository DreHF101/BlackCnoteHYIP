# BlackCnote Enhanced Docker Startup Script (PowerShell)
# Purpose: Automate Docker Desktop startup with enhanced configuration and error handling
# Author: BlackCnote Development Team
# Version: 2.0.0 - Enhanced with Docker settings and error fixes

param(
    [switch]$ForceRestart,
    [switch]$ResetWSL,
    [switch]$Verbose,
    [switch]$Quiet
)

# Set error action preference
$ErrorActionPreference = "Continue"

# Function to write colored output
function Write-ColorOutput {
    param(
        [string]$Message,
        [string]$Color = "White"
    )
    if (-not $Quiet) { Write-Host $Message -ForegroundColor $Color }
}

# Function to check if running as administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Function to configure Docker Desktop settings
function Set-DockerDesktopSettings {
    Write-ColorOutput "🔧 Configuring Docker Desktop settings..." "Yellow"
    
    try {
        $dockerDataPath = "$env:APPDATA\Docker"
        $settingsPath = "$dockerDataPath\settings.json"
        
        # Create Docker data directory if it doesn't exist
        if (-not (Test-Path $dockerDataPath)) {
            New-Item -ItemType Directory -Path $dockerDataPath -Force | Out-Null
        }
        
        # Enhanced Docker Desktop settings
        $dockerSettings = @{
            "features" = @{
                "buildkit" = $true
                "kubernetes" = $false
                "containerd" = $true
                "dockerComposeV2" = $true
                "useDockerContentTrust" = $false
                "experimental" = $false
            }
            "experimental" = $false
            "debug" = $false
            "stackOrchestrator" = "swarm"
            "deprecatedCgroupv1" = $false
            "liveRestore" = $true
            "userlandProxy" = $false
            "maxConcurrentDownloads" = 3
            "maxConcurrentUploads" = 5
            "registryMirrors" = @()
            "insecureRegistries" = @()
            "builder" = @{
                "gc" = @{
                    "enabled" = $true
                    "defaultKeepStorage" = "20GB"
                }
            }
            "runtimes" = @{}
            "defaultRuntime" = "runc"
            "storageDriver" = "overlay2"
            "logDriver" = "json-file"
            "logOpts" = @{
                "max-size" = "10m"
                "max-file" = "3"
            }
            "defaultAddressPools" = @(
                @{
                    "base" = "172.17.0.0/12"
                    "size" = 16
                }
            )
        }
        
        # Convert to JSON and save
        $dockerSettingsJson = $dockerSettings | ConvertTo-Json -Depth 10
        $dockerSettingsJson | Out-File -FilePath $settingsPath -Encoding UTF8 -Force
        
        Write-ColorOutput "✅ Docker Desktop settings configured" "Green"
        return $true
    }
    catch {
        Write-ColorOutput "⚠️  Error configuring Docker settings: $($_.Exception.Message)" "Yellow"
        return $false
    }
}

# Function to configure Docker daemon
function Set-DockerDaemonConfig {
    Write-ColorOutput "🔧 Configuring Docker daemon..." "Yellow"
    
    try {
        $dockerDataPath = "$env:APPDATA\Docker"
        $daemonPath = "$dockerDataPath\daemon.json"
        
        # Enhanced Docker daemon configuration
        $daemonConfig = @{
            "debug" = $false
            "experimental" = $false
            "features" = @{
                "buildkit" = $true
                "containerd" = $true
            }
            "max-concurrent-downloads" = 3
            "max-concurrent-uploads" = 5
            "default-address-pools" = @(
                @{
                    "base" = "172.17.0.0/12"
                    "size" = 16
                }
            )
            "log-driver" = "json-file"
            "log-opts" = @{
                "max-size" = "10m"
                "max-file" = "3"
            }
            "storage-driver" = "overlay2"
            "storage-opts" = @(
                "overlay2.override_kernel_check=true"
            )
            "live-restore" = $true
            "userland-proxy" = $false
            "registry-mirrors" = @()
            "insecure-registries" = @()
            "dns" = @("8.8.8.8", "8.8.4.4")
            "default-ulimits" = @{
                "nofile" = @{
                    "Name" = "nofile"
                    "Hard" = 64000
                    "Soft" = 64000
                }
            }
        }
        
        # Convert to JSON and save
        $daemonConfigJson = $daemonConfig | ConvertTo-Json -Depth 10
        $daemonConfigJson | Out-File -FilePath $daemonPath -Encoding UTF8 -Force
        
        Write-ColorOutput "✅ Docker daemon configured" "Green"
        return $true
    }
    catch {
        Write-ColorOutput "⚠️  Error configuring Docker daemon: $($_.Exception.Message)" "Yellow"
        return $false
    }
}

# Function to stop Docker Desktop
function Stop-DockerDesktop {
    Write-ColorOutput "🛑 Stopping Docker Desktop processes..." "Yellow"
    
    try {
        # Stop Docker Desktop processes gracefully
        $dockerProcesses = Get-Process -Name "*docker*" -ErrorAction SilentlyContinue
        if ($dockerProcesses) {
            $dockerProcesses | ForEach-Object {
                try {
                    $_.CloseMainWindow() | Out-Null
                    Start-Sleep -Seconds 2
                    if (-not $_.HasExited) {
                        $_.Kill()
                    }
                }
                catch {
                    Write-ColorOutput "⚠️  Could not stop process $($_.ProcessName)" "Yellow"
                }
            }
        }
        
        # Stop WSL2 backend
        wsl --shutdown 2>$null
        
        Start-Sleep -Seconds 3
        Write-ColorOutput "✅ Docker Desktop processes stopped" "Green"
    }
    catch {
        Write-ColorOutput "⚠️  Error stopping Docker processes: $($_.Exception.Message)" "Yellow"
    }
}

# Function to start Docker Desktop
function Start-DockerDesktop {
    Write-ColorOutput "🔧 Starting Docker Desktop..." "Yellow"
    
    try {
        # Check if Docker Desktop is already running
        $dockerProcesses = Get-Process -Name "*docker*" -ErrorAction SilentlyContinue
        if ($dockerProcesses) {
            Write-ColorOutput "⚠️  Docker Desktop processes already running. Stopping them first..." "Yellow"
            Stop-DockerDesktop
            Start-Sleep -Seconds 5
        }
        
        # Configure Docker settings before starting
        Set-DockerDesktopSettings
        Set-DockerDaemonConfig
        
        # Start Docker Desktop
        $dockerPath = "${env:ProgramFiles}\Docker\Docker\Docker Desktop.exe"
        if (Test-Path $dockerPath) {
            Write-ColorOutput "🚀 Launching Docker Desktop..." "Green"
            Start-Process -FilePath $dockerPath -WindowStyle Minimized -ArgumentList "--verbose"
        } else {
            Write-ColorOutput "❌ Docker Desktop not found at expected location: $dockerPath" "Red"
            return $false
        }
        
        return $true
    }
    catch {
        Write-ColorOutput "❌ Error starting Docker Desktop: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to reset WSL2
function Reset-WSL2 {
    Write-ColorOutput "🔄 Resetting WSL2..." "Yellow"
    
    try {
        # Shutdown WSL
        wsl --shutdown 2>$null
        Start-Sleep -Seconds 3
        
        # Reset docker-desktop distro
        wsl --unregister docker-desktop 2>$null
        Start-Sleep -Seconds 2
        
        # Reset docker-desktop-data distro
        wsl --unregister docker-desktop-data 2>$null
        Start-Sleep -Seconds 2
        
        Write-ColorOutput "✅ WSL2 reset completed" "Green"
        return $true
    }
    catch {
        Write-ColorOutput "❌ Error resetting WSL2: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to clean Docker Desktop data
function Clear-DockerDesktopData {
    Write-ColorOutput "🧹 Cleaning Docker Desktop data..." "Yellow"
    
    try {
        $dockerDataPath = "$env:APPDATA\Docker"
        $dockerLocalPath = "$env:LOCALAPPDATA\Docker"
        
        if (Test-Path $dockerDataPath) {
            Remove-Item -Path "$dockerDataPath\settings.json" -Force -ErrorAction SilentlyContinue
            Remove-Item -Path "$dockerDataPath\daemon.json" -Force -ErrorAction SilentlyContinue
            Write-ColorOutput "✅ Docker Desktop settings cleared" "Green"
        }
        
        if (Test-Path $dockerLocalPath) {
            Remove-Item -Path "$dockerLocalPath\*" -Recurse -Force -ErrorAction SilentlyContinue
            Write-ColorOutput "✅ Docker Desktop local data cleared" "Green"
        }
        
        return $true
    }
    catch {
        Write-ColorOutput "⚠️  Error cleaning Docker data: $($_.Exception.Message)" "Yellow"
        return $false
    }
}

# Function to wait for Docker engine
function Wait-DockerEngine {
    param([int]$TimeoutSeconds = 120)
    
    Write-ColorOutput "⏳ Waiting for Docker engine to start (timeout: $TimeoutSeconds seconds)..." "Yellow"
    
    $startTime = Get-Date
    $timeout = $startTime.AddSeconds($TimeoutSeconds)
    
    while ((Get-Date) -lt $timeout) {
        try {
            $null = docker info 2>$null
            if ($LASTEXITCODE -eq 0) {
                Write-ColorOutput "✅ Docker engine is ready!" "Green"
                return $true
            }
        }
        catch {
            # Continue waiting
        }
        
        $elapsed = ((Get-Date) - $startTime).TotalSeconds
        $percentComplete = ($elapsed / $TimeoutSeconds) * 100
        Write-Progress -Activity "Waiting for Docker Engine" -Status "Elapsed: $($elapsed.ToString('F1'))s" -PercentComplete $percentComplete
        Start-Sleep -Seconds 2
    }
    
    Write-Progress -Activity "Waiting for Docker Engine" -Completed
    Write-ColorOutput "❌ Docker engine failed to start within timeout period" "Red"
    return $false
}

# Function to verify Docker functionality
function Test-DockerFunctionality {
    Write-ColorOutput "🔍 Testing Docker functionality..." "Yellow"
    
    try {
        # Test basic Docker commands
        $dockerVersion = docker --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ Docker CLI: $dockerVersion" "Green"
        } else {
            Write-ColorOutput "❌ Docker CLI not working" "Red"
            return $false
        }
        
        # Test Docker info
        $dockerInfo = docker info 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ Docker engine: Running" "Green"
        } else {
            Write-ColorOutput "❌ Docker engine: Not responding" "Red"
            return $false
        }
        
        # Test Docker Compose
        $composeVersion = docker compose version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ Docker Compose: $composeVersion" "Green"
        } else {
            Write-ColorOutput "❌ Docker Compose not working" "Red"
            return $false
        }
        
        return $true
    }
    catch {
        Write-ColorOutput "❌ Error testing Docker: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to start BlackCnote services with error handling
function Start-BlackCnoteServices {
    Write-ColorOutput "🚀 Starting BlackCnote services..." "Yellow"
    
    try {
        # Set project root
        $projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
        Set-Location $projectRoot
        
        # Stop any existing containers
        Write-ColorOutput "🛑 Stopping existing containers..." "Yellow"
        docker compose down --remove-orphans 2>$null
        
        # Clear Docker system to prevent concurrent map writes error
        Write-ColorOutput "🧹 Cleaning Docker system..." "Yellow"
        docker system prune -f 2>$null
        
        # Start services with retry logic
        $maxRetries = 3
        $retryCount = 0
        
        while ($retryCount -lt $maxRetries) {
            try {
                Write-ColorOutput "🔄 Attempt $($retryCount + 1) of $maxRetries to start services..." "Yellow"
                
                # Use docker compose instead of docker-compose to avoid concurrent map writes
                docker compose up -d --build
                
                if ($LASTEXITCODE -eq 0) {
                    Write-ColorOutput "✅ BlackCnote services started successfully!" "Green"
                    return $true
                } else {
                    throw "Docker compose failed with exit code $LASTEXITCODE"
                }
            }
            catch {
                $retryCount++
                Write-ColorOutput "⚠️  Attempt $retryCount failed: $($_.Exception.Message)" "Yellow"
                
                if ($retryCount -lt $maxRetries) {
                    Write-ColorOutput "🔄 Retrying in 10 seconds..." "Yellow"
                    Start-Sleep -Seconds 10
                    
                    # Clean up before retry
                    docker compose down --remove-orphans 2>$null
                    docker system prune -f 2>$null
                }
            }
        }
        
        Write-ColorOutput "❌ Failed to start BlackCnote services after $maxRetries attempts" "Red"
        return $false
    }
    catch {
        Write-ColorOutput "❌ Error starting BlackCnote services: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to check service status
function Get-ServiceStatus {
    Write-ColorOutput "📊 Checking service status..." "Yellow"
    
    try {
        docker compose ps
        return $true
    }
    catch {
        Write-ColorOutput "⚠️  Error checking service status: $($_.Exception.Message)" "Yellow"
        return $false
    }
}

# Function to check WSL2 status
function Get-WSL2Status {
    Write-ColorOutput "🔍 Checking WSL2 status..." "Yellow"
    
    try {
        $wslStatus = wsl --status 2>$null
        Write-ColorOutput "WSL2 Status: $wslStatus" "Cyan"
        
        $wslList = wsl --list --verbose 2>$null
        Write-ColorOutput "WSL2 Distributions:" "Cyan"
        Write-Host $wslList
        
        return $true
    }
    catch {
        Write-ColorOutput "❌ Error checking WSL2: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Main execution
Write-ColorOutput "🐳 BlackCnote Enhanced Docker Startup Script" "Cyan"
Write-ColorOutput "===============================================" "Cyan"
Write-ColorOutput "Timestamp: $(Get-Date)" "Gray"
Write-ColorOutput "Version: 2.0.0 - Enhanced with Docker settings and error fixes" "Gray"

# Check if running as administrator
if (-not (Test-Administrator)) {
    Write-ColorOutput "⚠️  Not running as administrator" "Yellow"
    Write-ColorOutput "Docker Desktop will start with user privileges" "Yellow"
    Write-ColorOutput "For best performance, run as administrator" "Yellow"
}

# Set project directory
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Write-ColorOutput "Project directory: $projectRoot" "Cyan"

# Check current Docker status
Write-ColorOutput "📊 Current Docker Status:" "Yellow"
$dockerProcesses = Get-Process -Name "*docker*" -ErrorAction SilentlyContinue
if ($dockerProcesses) {
    Write-ColorOutput "✅ Docker processes found: $($dockerProcesses.Count) processes" "Green"
    $dockerProcesses | ForEach-Object { Write-ColorOutput "  - $($_.ProcessName) (PID: $($_.Id))" "Gray" }
} else {
    Write-ColorOutput "❌ No Docker processes found" "Red"
}

# Check WSL2 status
Get-WSL2Status

# Handle force restart
if ($ForceRestart) {
    Write-ColorOutput "🔄 Force restart requested..." "Yellow"
    Stop-DockerDesktop
    Start-Sleep -Seconds 5
}

# Handle WSL2 reset
if ($ResetWSL) {
    Write-ColorOutput "🔄 WSL2 reset requested..." "Yellow"
    Reset-WSL2
    Clear-DockerDesktopData
}

# Start Docker Desktop
$dockerStarted = Start-DockerDesktop
if (-not $dockerStarted) {
    Write-ColorOutput "❌ Failed to start Docker Desktop" "Red"
    exit 1
}

# Wait for Docker engine
$engineReady = Wait-DockerEngine
if (-not $engineReady) {
    Write-ColorOutput "❌ Docker engine failed to start" "Red"
    Write-ColorOutput "Try running with -ForceRestart or -ResetWSL flags" "Yellow"
    exit 1
}

# Test Docker functionality
$dockerWorking = Test-DockerFunctionality
if (-not $dockerWorking) {
    Write-ColorOutput "❌ Docker functionality test failed" "Red"
    exit 1
}

# Start BlackCnote services
$servicesStarted = Start-BlackCnoteServices
if (-not $servicesStarted) {
    Write-ColorOutput "❌ Failed to start BlackCnote services" "Red"
    Write-ColorOutput "Check logs with: docker compose logs" "Yellow"
    exit 1
}

# Wait for services to be ready
Write-ColorOutput "⏳ Waiting for services to be ready..." "Yellow"
Start-Sleep -Seconds 15

# Check service status
Get-ServiceStatus

# Display service URLs
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