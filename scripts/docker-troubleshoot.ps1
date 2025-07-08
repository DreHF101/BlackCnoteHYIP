# BlackCnote Docker Troubleshooting Script
# Purpose: Diagnose and fix common Docker issues
# Author: BlackCnote Development Team
# Version: 1.0.0

param(
    [switch]$FixAll,
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

# Function to check Docker installation
function Test-DockerInstallation {
    Write-ColorOutput "🔍 Checking Docker installation..." "Yellow"
    
    $issues = @()
    
    # Check Docker Desktop executable
    $dockerPath = "${env:ProgramFiles}\Docker\Docker\Docker Desktop.exe"
    if (-not (Test-Path $dockerPath)) {
        $issues += "Docker Desktop not found at: $dockerPath"
    } else {
        Write-ColorOutput "✅ Docker Desktop executable found" "Green"
    }
    
    # Check Docker CLI
    try {
        $dockerVersion = docker --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ Docker CLI: $dockerVersion" "Green"
        } else {
            $issues += "Docker CLI not working"
        }
    }
    catch {
        $issues += "Docker CLI not available"
    }
    
    # Check Docker Compose
    try {
        $composeVersion = docker compose version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ Docker Compose: $composeVersion" "Green"
        } else {
            $issues += "Docker Compose not working"
        }
    }
    catch {
        $issues += "Docker Compose not available"
    }
    
    return $issues
}

# Function to check Docker service status
function Test-DockerServiceStatus {
    Write-ColorOutput "🔍 Checking Docker service status..." "Yellow"
    
    $issues = @()
    
    # Check Docker processes
    $dockerProcesses = Get-Process -Name "*docker*" -ErrorAction SilentlyContinue
    if ($dockerProcesses) {
        Write-ColorOutput "✅ Docker processes running: $($dockerProcesses.Count)" "Green"
        $dockerProcesses | ForEach-Object { Write-ColorOutput "  - $($_.ProcessName) (PID: $($_.Id))" "Gray" }
    } else {
        $issues += "No Docker processes found"
    }
    
    # Check Docker engine
    try {
        $dockerInfo = docker info 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ Docker engine responding" "Green"
        } else {
            $issues += "Docker engine not responding"
        }
    }
    catch {
        $issues += "Cannot connect to Docker engine"
    }
    
    return $issues
}

# Function to check WSL2 status
function Test-WSL2Status {
    Write-ColorOutput "🔍 Checking WSL2 status..." "Yellow"
    
    $issues = @()
    
    try {
        # Check WSL version
        $wslVersion = wsl --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ WSL version: $wslVersion" "Green"
        } else {
            $issues += "WSL not properly installed"
        }
        
        # Check WSL status
        $wslStatus = wsl --status 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ WSL status: $wslStatus" "Green"
        } else {
            $issues += "WSL status check failed"
        }
        
        # Check WSL distributions
        $wslList = wsl --list --verbose 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ WSL distributions found" "Green"
            Write-Host $wslList
    } else {
            $issues += "WSL distribution check failed"
        }
    }
    catch {
        $issues += "WSL not available"
    }
    
    return $issues
}

# Function to check Docker configuration
function Test-DockerConfiguration {
    Write-ColorOutput "🔍 Checking Docker configuration..." "Yellow"
    
    $issues = @()
    
    # Check Docker settings
    $dockerDataPath = "$env:APPDATA\Docker"
    $settingsPath = "$dockerDataPath\settings.json"
    $daemonPath = "$dockerDataPath\daemon.json"
    
    if (Test-Path $settingsPath) {
        Write-ColorOutput "✅ Docker settings file found" "Green"
    } else {
        $issues += "Docker settings file missing"
    }
    
    if (Test-Path $daemonPath) {
        Write-ColorOutput "✅ Docker daemon config found" "Green"
    } else {
        $issues += "Docker daemon config missing"
    }
    
    return $issues
}

# Function to check system resources
function Test-SystemResources {
    Write-ColorOutput "🔍 Checking system resources..." "Yellow"
    
    $issues = @()
    
    # Check available memory
    $memory = Get-CimInstance -ClassName Win32_OperatingSystem
    $availableMemoryGB = [math]::Round($memory.FreePhysicalMemory / 1MB, 2)
    $totalMemoryGB = [math]::Round($memory.TotalVisibleMemorySize / 1MB, 2)
    
    Write-ColorOutput "Memory: $availableMemoryGB GB available / $totalMemoryGB GB total" "Cyan"
    
    if ($availableMemoryGB -lt 2) {
        $issues += "Low available memory: $availableMemoryGB GB (recommended: 4+ GB)"
    }
    
    # Check disk space
    $systemDrive = Get-WmiObject -Class Win32_LogicalDisk -Filter "DeviceID='C:'"
    $freeSpaceGB = [math]::Round($systemDrive.FreeSpace / 1GB, 2)
    $totalSpaceGB = [math]::Round($systemDrive.Size / 1GB, 2)
    
    Write-ColorOutput "Disk: $freeSpaceGB GB free / $totalSpaceGB GB total" "Cyan"
    
    if ($freeSpaceGB -lt 10) {
        $issues += "Low disk space: $freeSpaceGB GB (recommended: 20+ GB)"
    }
    
    return $issues
}

# Function to check network connectivity
function Test-NetworkConnectivity {
    Write-ColorOutput "🔍 Checking network connectivity..." "Yellow"
    
    $issues = @()
    
    # Test internet connectivity
    try {
        $response = Invoke-WebRequest -Uri "https://www.google.com" -TimeoutSec 10 -UseBasicParsing
        if ($response.StatusCode -eq 200) {
            Write-ColorOutput "✅ Internet connectivity: OK" "Green"
            } else {
            $issues += "Internet connectivity issues"
            }
        }
        catch {
        $issues += "No internet connectivity"
    }
    
    # Test Docker Hub connectivity
    try {
        $response = Invoke-WebRequest -Uri "https://registry-1.docker.io/v2/" -TimeoutSec 10 -UseBasicParsing
        if ($response.StatusCode -eq 200) {
            Write-ColorOutput "✅ Docker Hub connectivity: OK" "Green"
        } else {
            $issues += "Docker Hub connectivity issues"
        }
    }
    catch {
        $issues += "Cannot connect to Docker Hub"
    }
    
    return $issues
}

# Function to fix Docker issues
function Fix-DockerIssues {
    param([array]$Issues)
    
    Write-ColorOutput "🔧 Attempting to fix Docker issues..." "Yellow"
    
    foreach ($issue in $Issues) {
        Write-ColorOutput "Fixing: $issue" "Yellow"
        
        switch -Wildcard ($issue) {
            "*Docker Desktop not found*" {
                Write-ColorOutput "Please install Docker Desktop from https://www.docker.com/products/docker-desktop" "Red"
            }
            "*Docker CLI not*" {
                Write-ColorOutput "Restarting Docker Desktop..." "Yellow"
                Stop-Process -Name "*docker*" -Force -ErrorAction SilentlyContinue
                Start-Sleep -Seconds 5
                Start-Process "${env:ProgramFiles}\Docker\Docker\Docker Desktop.exe"
            }
            "*Docker engine not responding*" {
                Write-ColorOutput "Restarting Docker engine..." "Yellow"
                Stop-Process -Name "*docker*" -Force -ErrorAction SilentlyContinue
                wsl --shutdown 2>$null
                Start-Sleep -Seconds 5
                Start-Process "${env:ProgramFiles}\Docker\Docker\Docker Desktop.exe"
            }
            "*WSL not*" {
                Write-ColorOutput "Installing WSL2..." "Yellow"
                wsl --install
            }
            "*Docker settings file missing*" {
                Write-ColorOutput "Creating Docker settings..." "Yellow"
                $dockerDataPath = "$env:APPDATA\Docker"
                if (-not (Test-Path $dockerDataPath)) {
                    New-Item -ItemType Directory -Path $dockerDataPath -Force | Out-Null
                }
            }
            "*Low available memory*" {
                Write-ColorOutput "Close unnecessary applications to free up memory" "Yellow"
            }
            "*Low disk space*" {
                Write-ColorOutput "Free up disk space by removing unnecessary files" "Yellow"
            }
            "*concurrent map writes*" {
                Write-ColorOutput "Fixing concurrent map writes error..." "Yellow"
                docker system prune -f 2>$null
                docker compose down --remove-orphans 2>$null
                Start-Sleep -Seconds 5
            }
            default {
                Write-ColorOutput "Manual intervention required for: $issue" "Yellow"
            }
        }
    }
}

# Function to generate diagnostic report
function Get-DiagnosticReport {
    Write-ColorOutput "📋 Generating diagnostic report..." "Yellow"
    
    $report = @{
        Timestamp = Get-Date
        System = @{
            OS = (Get-CimInstance -ClassName Win32_OperatingSystem).Caption
            Version = (Get-CimInstance -ClassName Win32_OperatingSystem).Version
            Architecture = (Get-CimInstance -ClassName Win32_ComputerSystem).SystemType
        }
        Docker = @{
            Installation = Test-DockerInstallation
            ServiceStatus = Test-DockerServiceStatus
            Configuration = Test-DockerConfiguration
        }
        WSL2 = Test-WSL2Status
        SystemResources = Test-SystemResources
        Network = Test-NetworkConnectivity
    }
    
    return $report
}

# Main execution
Write-ColorOutput "🔧 BlackCnote Docker Troubleshooting Script" "Cyan"
Write-ColorOutput "=============================================" "Cyan"
Write-ColorOutput "Timestamp: $(Get-Date)" "Gray"

# Collect all issues
$allIssues = @()

Write-ColorOutput ""
$installationIssues = Test-DockerInstallation
$allIssues += $installationIssues

Write-ColorOutput ""
$serviceIssues = Test-DockerServiceStatus
$allIssues += $serviceIssues

Write-ColorOutput ""
$wslIssues = Test-WSL2Status
$allIssues += $wslIssues

Write-ColorOutput ""
$configIssues = Test-DockerConfiguration
$allIssues += $configIssues

Write-ColorOutput ""
$resourceIssues = Test-SystemResources
$allIssues += $resourceIssues

Write-ColorOutput ""
$networkIssues = Test-NetworkConnectivity
$allIssues += $networkIssues

# Display summary
Write-ColorOutput ""
Write-ColorOutput "📊 Diagnostic Summary:" "Cyan"
Write-ColorOutput "=====================" "Cyan"

if ($allIssues.Count -eq 0) {
    Write-ColorOutput "✅ No issues detected! Docker should be working properly." "Green"
} else {
    Write-ColorOutput "❌ Found $($allIssues.Count) issue(s):" "Red"
    foreach ($issue in $allIssues) {
        Write-ColorOutput "  - $issue" "Red"
    }
    
    Write-ColorOutput ""
    Write-ColorOutput "🔧 Recommended actions:" "Yellow"
    
    if ($FixAll) {
        Fix-DockerIssues -Issues $allIssues
    } else {
        Write-ColorOutput "Run with -FixAll flag to attempt automatic fixes" "Yellow"
        Write-ColorOutput "Or run the enhanced startup script: scripts\automate-docker-startup.ps1" "Yellow"
    }
}

# Generate detailed report if verbose
if ($Verbose) {
    Write-ColorOutput ""
    Write-ColorOutput "📋 Detailed Diagnostic Report:" "Cyan"
    $report = Get-DiagnosticReport
    $report | ConvertTo-Json -Depth 10 | Write-ColorOutput "Gray"
}

Write-ColorOutput ""
Write-ColorOutput "Troubleshooting completed!" "Green" 