# BlackCnote Startup Compatibility Checker
# This script checks for conflicts between the new Docker engine and existing startup scripts

param(
    [switch]$Fix,
    [switch]$Verbose
)

# Set error action preference
$ErrorActionPreference = "Continue"

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
Write-Info "BlackCnote Startup Compatibility Checker"
Write-Info "========================================="
Write-Output ""

$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$conflicts = @()
$warnings = @()
$recommendations = @()

# Check 1: Docker daemon configuration
Write-Info "Checking Docker daemon configuration..."
$daemonConfig = "$env:USERPROFILE\.docker\daemon.json"
$sourceConfig = "$projectRoot\config\docker\daemon.json"

if (Test-Path $daemonConfig) {
    try {
        $currentConfig = Get-Content $daemonConfig | ConvertFrom-Json
        $sourceConfigContent = Get-Content $sourceConfig | ConvertFrom-Json
        
        # Check for BlackCnote-specific settings
        if ($currentConfig.labels -and $currentConfig.labels -contains "com.blackcnote.project=blackcnote") {
            Write-Success "Docker daemon configuration is compatible"
        } else {
            $conflicts += "Docker daemon configuration missing BlackCnote labels"
            $recommendations += "Run: .\scripts\setup-docker-privileges.ps1 -All"
        }
    } catch {
        $warnings += "Could not parse Docker daemon configuration"
    }
} else {
    $conflicts += "Docker daemon configuration not found"
    $recommendations += "Run: .\scripts\setup-docker-privileges.ps1 -All"
}

# Check 2: Startup scripts
Write-Info "Checking startup scripts..."
$startupScripts = @(
    "$env:USERPROFILE\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\start-blackcnote-docker.bat",
    "$env:USERPROFILE\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\start-blackcnote-docker.ps1",
    "$projectRoot\start-blackcnote-complete.ps1",
    "$projectRoot\start-blackcnote.bat",
    "$projectRoot\scripts\start-docker-elevated.bat",
    "$projectRoot\scripts\start-docker-elevated.ps1"
)

foreach ($script in $startupScripts) {
    if (Test-Path $script) {
        Write-Success "Found: $script"
    } else {
        $warnings += "Missing startup script: $script"
    }
}

# Check 3: Task Scheduler tasks
Write-Info "Checking Task Scheduler tasks..."
$taskName = "BlackCnoteDockerStartup"
$existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue

if ($existingTask) {
    Write-Success "Task Scheduler task found: $taskName"
} else {
    $warnings += "Task Scheduler task not found: $taskName"
    $recommendations += "Run: .\scripts\setup-docker-privileges.ps1 -CreateTask"
}

# Check 4: Docker Desktop registry settings
Write-Info "Checking Docker Desktop registry settings..."
$dockerRegPath = "HKLM:\SOFTWARE\Docker Inc.\Docker Desktop"

if (Test-Path $dockerRegPath) {
    try {
        $runAsAdmin = Get-ItemProperty -Path $dockerRegPath -Name "RunAsAdmin" -ErrorAction SilentlyContinue
        $autoStart = Get-ItemProperty -Path $dockerRegPath -Name "AutoStart" -ErrorAction SilentlyContinue
        $useWSL2 = Get-ItemProperty -Path $dockerRegPath -Name "UseWSL2" -ErrorAction SilentlyContinue
        
        if ($runAsAdmin.RunAsAdmin -eq 1) {
            Write-Success "Docker Desktop configured to run as administrator"
        } else {
            $conflicts += "Docker Desktop not configured to run as administrator"
        }
        
        if ($autoStart.AutoStart -eq 1) {
            Write-Success "Docker Desktop configured for automatic startup"
        } else {
            $warnings += "Docker Desktop not configured for automatic startup"
        }
        
        if ($useWSL2.UseWSL2 -eq 1) {
            Write-Success "Docker Desktop configured to use WSL2"
        } else {
            $warnings += "Docker Desktop not configured to use WSL2"
        }
    } catch {
        $warnings += "Could not read Docker Desktop registry settings"
    }
} else {
    $conflicts += "Docker Desktop registry settings not found"
}

# Check 5: Docker Compose configuration
Write-Info "Checking Docker Compose configuration..."
$composeFiles = @(
    "$projectRoot\docker-compose.yml",
    "$projectRoot\config\docker\docker-compose.yml",
    "$projectRoot\config\docker\docker-compose.override.yml"
)

foreach ($file in $composeFiles) {
    if (Test-Path $file) {
        Write-Success "Found: $file"
    } else {
        $warnings += "Missing Docker Compose file: $file"
    }
}

# Check 6: Port conflicts
Write-Info "Checking for port conflicts..."
$portsToCheck = @(8888, 5174, 8080, 8081, 9091, 3000, 3001, 8025)

foreach ($port in $portsToCheck) {
    $connection = Get-NetTCPConnection -LocalPort $port -ErrorAction SilentlyContinue
    if ($connection) {
        $process = Get-Process -Id $connection.OwningProcess -ErrorAction SilentlyContinue
        if ($process) {
            $warnings += "Port $port is in use by: $($process.ProcessName) (PID: $($process.Id))"
        }
    }
}

# Check 7: Docker Desktop process
Write-Info "Checking Docker Desktop process..."
$dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue

if ($dockerProcess) {
    Write-Success "Docker Desktop is running (PID: $($dockerProcess.Id))"
} else {
    $warnings += "Docker Desktop is not running"
}

# Check 8: Docker functionality
Write-Info "Checking Docker functionality..."
try {
    $dockerInfo = docker info 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Docker daemon is responding"
    } else {
        $conflicts += "Docker daemon is not responding"
    }
} catch {
    $conflicts += "Docker command not available"
}

# Summary
Write-Output ""
Write-Info "=== Compatibility Check Summary ==="

if ($conflicts.Count -eq 0 -and $warnings.Count -eq 0) {
    Write-Success "All startup scripts are compatible with the new Docker engine!"
    Write-Output "No conflicts or warnings found."
} else {
    if ($conflicts.Count -gt 0) {
        Write-Error "Conflicts found:"
        foreach ($conflict in $conflicts) {
            Write-Error "   - $conflict"
        }
        Write-Output ""
    }
    if ($warnings.Count -gt 0) {
        Write-Warning "Warnings:"
        foreach ($warning in $warnings) {
            Write-Warning "   - $warning"
        }
        Write-Output ""
    }
    if ($recommendations.Count -gt 0) {
        Write-Info "Recommendations:"
        foreach ($recommendation in $recommendations) {
            Write-Info "   - $recommendation"
        }
        Write-Output ""
    }
}

Write-Output ""
Write-Info "Startup Script Priority:"
Write-Output "1. Enhanced startup script (created by setup-docker-privileges.ps1)"
Write-Output "2. start-blackcnote-complete.ps1 (comprehensive setup)"
Write-Output "3. start-docker-elevated.bat (manual Docker startup)"
Write-Output "4. start-blackcnote.bat (legacy startup)"

Write-Output ""
Write-Info "Usage Instructions:"
Write-Output "Automatic startup: Scripts in Windows Startup folder"
Write-Output "Manual startup: .\scripts\start-docker-elevated.bat"
Write-Output "Complete setup: .\start-blackcnote-complete.ps1"
Write-Output "Check compatibility: .\scripts\check-startup-compatibility.ps1"

Write-Output ""
Write-Info "For more information, see:"
Write-Output "   DOCKER-PRIVILEGES-FIX.md"
Write-Output "   DOCKER-SETUP.md"
Write-Output "   BLACKCNOTE-CANONICAL-PATHS.md"

if ($Fix) {
    Write-Info "Applying Fixes..."
    if ($conflicts.Count -gt 0) {
        Write-Info "Running Docker privileges setup to fix conflicts..."
        & "$projectRoot\scripts\setup-docker-privileges.ps1" -All
    }
    if ($warnings -contains "Docker Desktop is not running") {
        Write-Info "Starting Docker Desktop..."
        Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized
    }
}

exit 0 