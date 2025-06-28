# BlackCnote Docker Comprehensive Test, Review & Fix
# This script performs complete diagnostics and fixes for Docker WSL2 issues

param([switch]$Force, [switch]$Verbose)

# Set error action preference
$ErrorActionPreference = "Continue"

# Colors for output
$Red = "Red"
$Green = "Green"
$Yellow = "Yellow"
$Blue = "Blue"
$White = "White"
$Cyan = "Cyan"

function Write-ColorOutput {
    param([string]$Message, [string]$Color = $White)
    Write-Host $Message -ForegroundColor $Color
}

function Write-Section {
    param([string]$Title, [string]$Color = $Blue)
    Write-ColorOutput ""
    Write-ColorOutput "==========================================" $Color
    Write-ColorOutput $Title $Color
    Write-ColorOutput "==========================================" $Color
}

function Test-Admin {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

function Test-SystemRequirements {
    Write-Section "SYSTEM REQUIREMENTS CHECK" $Cyan
    
    # Check administrator privileges
    if (Test-Admin) {
        Write-ColorOutput "✓ Running as Administrator" $Green
    } else {
        Write-ColorOutput "✗ Not running as Administrator" $Red
        Write-ColorOutput "Please run PowerShell as Administrator" $Yellow
        return $false
    }
    
    # Check Windows version
    $os = Get-WmiObject -Class Win32_OperatingSystem
    Write-ColorOutput "Windows Version: $($os.Caption) $($os.Version)" $White
    
    # Check WSL availability
    try {
        $wslVersion = wsl --version 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✓ WSL is available" $Green
            Write-ColorOutput "WSL Version: $wslVersion" $White
        } else {
            Write-ColorOutput "✗ WSL is not available" $Red
            return $false
        }
    } catch {
        Write-ColorOutput "✗ WSL is not available: $($_.Exception.Message)" $Red
        return $false
    }
    
    # Check Docker CLI
    try {
        $dockerVersion = docker --version 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✓ Docker CLI is available" $Green
            Write-ColorOutput "Docker Version: $dockerVersion" $White
        } else {
            Write-ColorOutput "✗ Docker CLI is not available" $Red
            return $false
        }
    } catch {
        Write-ColorOutput "✗ Docker CLI is not available: $($_.Exception.Message)" $Red
        return $false
    }
    
    return $true
}

function Test-WSL2Status {
    Write-Section "WSL2 STATUS CHECK" $Cyan
    
    try {
        $distros = wsl --list --verbose 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "WSL2 Distros:" $White
            Write-ColorOutput $distros $White
            
            # Check for docker-desktop distro
            if ($distros -match "docker-desktop") {
                Write-ColorOutput "✓ docker-desktop distro found" $Green
                
                if ($distros -match "docker-desktop.*Running") {
                    Write-ColorOutput "✓ docker-desktop distro is running" $Green
                    return $true
                } else {
                    Write-ColorOutput "⚠ docker-desktop distro is not running" $Yellow
                    return $false
                }
            } else {
                Write-ColorOutput "✗ docker-desktop distro not found" $Red
                return $false
            }
        } else {
            Write-ColorOutput "✗ Failed to list WSL2 distros: $distros" $Red
            return $false
        }
    } catch {
        Write-ColorOutput "✗ WSL2 status check failed: $($_.Exception.Message)" $Red
        return $false
    }
}

function Test-DockerDesktopStatus {
    Write-Section "DOCKER DESKTOP STATUS CHECK" $Cyan
    
    try {
        $dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
        if ($dockerProcess) {
            Write-ColorOutput "✓ Docker Desktop is running" $Green
            Write-ColorOutput "Process ID: $($dockerProcess.Id)" $White
            Write-ColorOutput "Start Time: $($dockerProcess.StartTime)" $White
            return $true
        } else {
            Write-ColorOutput "✗ Docker Desktop is not running" $Red
            return $false
        }
    } catch {
        Write-ColorOutput "✗ Docker Desktop status check failed: $($_.Exception.Message)" $Red
        return $false
    }
}

function Test-DockerConnection {
    Write-Section "DOCKER CONNECTION TEST" $Cyan
    
    try {
        $dockerInfo = docker info 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✓ Docker connection successful" $Green
            Write-ColorOutput "Docker Info:" $White
            $dockerInfo | Select-String -Pattern "Server Version|Operating System|Kernel Version|Total Memory|Containers|Images" | ForEach-Object {
                Write-ColorOutput "  $_" $White
            }
            return $true
        } else {
            Write-ColorOutput "✗ Docker connection failed" $Red
            Write-ColorOutput "Error: $dockerInfo" $Red
            return $false
        }
    } catch {
        Write-ColorOutput "✗ Docker connection test failed: $($_.Exception.Message)" $Red
        return $false
    }
}

function Fix-WSL2 {
    Write-Section "FIXING WSL2" $Yellow
    
    try {
        Write-ColorOutput "Shutting down WSL2..." $Yellow
        wsl --shutdown
        Start-Sleep -Seconds 3
        
        Write-ColorOutput "Updating WSL2..." $Yellow
        wsl --update
        Start-Sleep -Seconds 5
        
        Write-ColorOutput "✓ WSL2 restart completed" $Green
        return $true
    } catch {
        Write-ColorOutput "✗ WSL2 fix failed: $($_.Exception.Message)" $Red
        return $false
    }
}

function Fix-DockerDesktop {
    Write-Section "FIXING DOCKER DESKTOP" $Yellow
    
    try {
        # Check if Docker Desktop is running
        $dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
        if ($dockerProcess) {
            Write-ColorOutput "Docker Desktop is already running" $Green
        } else {
            Write-ColorOutput "Starting Docker Desktop..." $Yellow
            Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized
            Start-Sleep -Seconds 30
            Write-ColorOutput "✓ Docker Desktop started" $Green
        }
        return $true
    } catch {
        Write-ColorOutput "✗ Docker Desktop fix failed: $($_.Exception.Message)" $Red
        return $false
    }
}

function Fix-DockerDesktopWSL2 {
    Write-Section "FIXING DOCKER DESKTOP WSL2 INTEGRATION" $Yellow
    
    try {
        Write-ColorOutput "Starting docker-desktop distro..." $Yellow
        wsl -d docker-desktop
        Start-Sleep -Seconds 10
        
        # Check if it's running
        $distros = wsl --list --verbose
        if ($distros -match "docker-desktop.*Running") {
            Write-ColorOutput "✓ docker-desktop distro is now running" $Green
            return $true
        } else {
            Write-ColorOutput "✗ docker-desktop distro failed to start" $Red
            return $false
        }
    } catch {
        Write-ColorOutput "✗ Docker Desktop WSL2 fix failed: $($_.Exception.Message)" $Red
        return $false
    }
}

function Reset-DockerDesktop {
    Write-Section "RESETTING DOCKER DESKTOP" $Red
    
    try {
        Write-ColorOutput "Stopping Docker Desktop..." $Yellow
        Stop-Process -Name "Docker Desktop" -Force -ErrorAction SilentlyContinue
        Start-Sleep -Seconds 5
        
        Write-ColorOutput "Removing Docker Desktop settings..." $Yellow
        Remove-Item "$env:APPDATA\Docker" -Recurse -Force -ErrorAction SilentlyContinue
        Remove-Item "$env:LOCALAPPDATA\Docker" -Recurse -Force -ErrorAction SilentlyContinue
        
        Write-ColorOutput "Starting Docker Desktop fresh..." $Yellow
        Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized
        Start-Sleep -Seconds 60
        
        Write-ColorOutput "✓ Docker Desktop reset completed" $Green
        return $true
    } catch {
        Write-ColorOutput "✗ Docker Desktop reset failed: $($_.Exception.Message)" $Red
        return $false
    }
}

function Test-DockerWithRetry {
    Write-Section "TESTING DOCKER WITH RETRY" $Cyan
    
    $attempts = 0
    $maxAttempts = 10
    $dockerWorking = $false
    
    while ($attempts -lt $maxAttempts -and -not $dockerWorking) {
        $attempts++
        Write-ColorOutput "Attempt $attempts of $maxAttempts..." $Yellow
        
        if (Test-DockerConnection) {
            $dockerWorking = $true
            break
        }
        
        if ($attempts -lt $maxAttempts) {
            Write-ColorOutput "Waiting 15 seconds before next attempt..." $Yellow
            Start-Sleep -Seconds 15
        }
    }
    
    return $dockerWorking
}

# Main execution
Write-ColorOutput "==========================================" $Blue
Write-ColorOutput "BLACKCNOTE DOCKER COMPREHENSIVE TEST & FIX" $Blue
Write-ColorOutput "==========================================" $Blue
Write-ColorOutput ""

# Phase 1: System Requirements Check
if (-not (Test-SystemRequirements)) {
    Write-ColorOutput "System requirements not met. Exiting." $Red
    exit 1
}

# Phase 2: Initial Status Check
Write-ColorOutput ""
Write-ColorOutput "PHASE 1: INITIAL STATUS CHECK" $Blue
Write-ColorOutput "=============================" $Blue

$wsl2Working = Test-WSL2Status
$dockerDesktopRunning = Test-DockerDesktopStatus
$dockerConnectionWorking = Test-DockerConnection

# Phase 3: Fix Attempts
Write-ColorOutput ""
Write-ColorOutput "PHASE 2: FIX ATTEMPTS" $Blue
Write-ColorOutput "=====================" $Blue

# Fix WSL2 if needed
if (-not $wsl2Working) {
    Write-ColorOutput "WSL2 needs fixing..." $Yellow
    Fix-WSL2
    Start-Sleep -Seconds 5
    $wsl2Working = Test-WSL2Status
}

# Fix Docker Desktop if needed
if (-not $dockerDesktopRunning) {
    Write-ColorOutput "Docker Desktop needs fixing..." $Yellow
    Fix-DockerDesktop
    Start-Sleep -Seconds 10
    $dockerDesktopRunning = Test-DockerDesktopStatus
}

# Fix Docker Desktop WSL2 integration if needed
if ($wsl2Working -and $dockerDesktopRunning -and -not $dockerConnectionWorking) {
    Write-ColorOutput "Docker Desktop WSL2 integration needs fixing..." $Yellow
    Fix-DockerDesktopWSL2
    Start-Sleep -Seconds 5
}

# Phase 4: Final Test with Retry
Write-ColorOutput ""
Write-ColorOutput "PHASE 3: FINAL TEST WITH RETRY" $Blue
Write-ColorOutput "===============================" $Blue

$finalDockerWorking = Test-DockerWithRetry

# Phase 5: Force Reset if Needed
if (-not $finalDockerWorking -and $Force) {
    Write-ColorOutput ""
    Write-ColorOutput "PHASE 4: FORCE RESET" $Red
    Write-ColorOutput "====================" $Red
    
    Reset-DockerDesktop
    Start-Sleep -Seconds 30
    $finalDockerWorking = Test-DockerWithRetry
}

# Phase 6: Final Results
Write-ColorOutput ""
Write-ColorOutput "PHASE 5: FINAL RESULTS" $Blue
Write-ColorOutput "=======================" $Blue

if ($finalDockerWorking) {
    Write-Section "✓ DOCKER IS NOW WORKING!" $Green
    
    Write-ColorOutput "Final WSL2 Status:" $White
    wsl --list --verbose
    
    Write-ColorOutput ""
    Write-ColorOutput "Final Docker Info:" $White
    docker info | Select-String -Pattern "Server Version|Operating System|Kernel Version|Total Memory|Containers|Images"
    
    Write-ColorOutput ""
    Write-ColorOutput "✓ All tests passed! Docker is ready for BlackCnote." $Green
} else {
    Write-Section "✗ DOCKER IS STILL NOT WORKING" $Red
    
    Write-ColorOutput "Troubleshooting recommendations:" $Yellow
    Write-ColorOutput "1. Restart your computer" $White
    Write-ColorOutput "2. Check Windows Defender/Antivirus exclusions" $White
    Write-ColorOutput "3. Verify WSL2 is properly installed: wsl --install" $White
    Write-ColorOutput "4. Check Docker Desktop settings" $White
    Write-ColorOutput "5. Try running this script again with -Force flag" $White
}

Write-ColorOutput ""
Write-ColorOutput "Script completed." $Blue 