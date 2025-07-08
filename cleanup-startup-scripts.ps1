# BlackCnote Startup Script Cleanup
# Removes all conflicting startup scripts and ensures clean environment

param([switch]$Force, [switch]$Backup)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

# Function to check if running as administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

Write-ColorOutput "=== BlackCnote Startup Script Cleanup ===" "Cyan"
Write-ColorOutput "Starting cleanup at: $(Get-Date)" "White"

# Check administrator privileges
if (-not (Test-Administrator)) {
    Write-ColorOutput "[WARNING] Not running as administrator" "Yellow"
    Write-ColorOutput "Some files may not be removable without admin privileges" "Yellow"
}

# Set project root
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

# Create backup directory if requested
if ($Backup) {
    $backupDir = Join-Path $projectRoot "backups\startup-scripts-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
    New-Item -ItemType Directory -Force -Path $backupDir | Out-Null
    Write-ColorOutput "Backup directory created: $backupDir" "Green"
}

# Comprehensive list of startup scripts to remove
$startupScripts = @(
    # Main conflicting scripts
    "start-blackcnote.bat",
    "start-blackcnote.ps1", 
    "start-blackcnote-fixed.bat",
    "start-blackcnote-fixed.ps1",
    "start-blackcnote-simple.ps1",
    "start-blackcnote-windows.ps1",
    "start-blackcnote-ml.bat",
    "start-blackcnote.sh",
    "start-blackcnote-complete.ps1",
    
    # Additional conflicting scripts found
    "start-blackcnote-optimized.bat",
    "start-blackcnote-optimized.ps1",
    "start-and-open-blackcnote.bat",
    
    # Fix scripts that are no longer needed
    "fix-blackcnote-startup-issues.ps1",
    "fix-blackcnote-simple.ps1",
    "fix-blackcnote-system.ps1",
    "fix-blackcnote-system.bat",
    
    # Test scripts
    "test-blackcnote-system.bat",
    "test-docker-diagnostics.ps1",
    "test-docker-diagnostics.bat",
    
    # Docker fix scripts
    "fix-docker-simple.ps1",
    "fix-docker-automated.ps1",
    "fix-docker-automated.bat",
    "fix-docker-wsl2.bat",
    "fix-docker-daemon-automated.bat",
    "docker-comprehensive-test-fix.bat",
    "docker-comprehensive-test-fix.ps1",
    
    # React fix scripts
    "fix-react-app.bat",
    "fix-react-app-canonical.bat",
    "fix-react-app-module-error.bat",
    "fix-react-app-module-error.ps1",
    "debug-react-app-styles.ps1",
    "diagnose-react-app.ps1",
    "simple-react-diagnostic.bat",
    "simple-react-diagnostic.ps1",
    
    # Check scripts
    "check-all-pathways.bat",
    "check-all-pathways.ps1",
    
    # Troubleshooting scripts
    "troubleshoot-blackcnote.ps1",
    "automated-startup-verification.ps1"
)

# List of startup scripts to keep (unified system)
$keepScripts = @(
    "start-blackcnote-unified.bat",
    "start-blackcnote-unified.ps1",
    "cleanup-startup-scripts.ps1"
)

Write-ColorOutput "`n=== Removing Conflicting Startup Scripts ===" "Yellow"

$removedCount = 0
$backedUpCount = 0

foreach ($script in $startupScripts) {
    $scriptPath = Join-Path $projectRoot $script
    if (Test-Path $scriptPath) {
        try {
            if ($Backup) {
                Copy-Item -Path $scriptPath -Destination (Join-Path $backupDir $script) -Force
                $backedUpCount++
                Write-ColorOutput "Backed up: $script" "Cyan"
            }
            
            Remove-Item -Path $scriptPath -Force
            Write-ColorOutput "Removed: $script" "Red"
            $removedCount++
        }
        catch {
            Write-ColorOutput "Failed to remove: $script - $($_.Exception.Message)" "Red"
        }
    }
}

Write-ColorOutput "`n=== Keeping Unified Startup Scripts ===" "Green"

foreach ($script in $keepScripts) {
    $scriptPath = Join-Path $projectRoot $script
    if (Test-Path $scriptPath) {
        Write-ColorOutput "Keeping: $script" "Green"
    } else {
        Write-ColorOutput "Missing: $script" "Yellow"
    }
}

# Stop any running Docker containers
Write-ColorOutput "`n=== Stopping Docker Containers ===" "Yellow"
try {
    docker-compose down --remove-orphans 2>$null
    Write-ColorOutput "Docker containers stopped" "Green"
}
catch {
    Write-ColorOutput "No Docker containers running or Docker not available" "Yellow"
}

# Kill any PowerShell processes running startup scripts
Write-ColorOutput "`n=== Stopping Startup Processes ===" "Yellow"
$processes = Get-Process -Name "powershell" -ErrorAction SilentlyContinue | Where-Object {
    $_.CommandLine -like "*start-blackcnote*" -or $_.CommandLine -like "*BlackCnote*"
}

if ($processes) {
    foreach ($process in $processes) {
        try {
            Stop-Process -Id $process.Id -Force
            Write-ColorOutput "Stopped process: $($process.Id)" "Red"
        }
        catch {
            Write-ColorOutput "Failed to stop process: $($process.Id)" "Red"
        }
    }
} else {
    Write-ColorOutput "No startup processes found" "Green"
}

# Summary
Write-ColorOutput "`n=== Cleanup Summary ===" "Cyan"
Write-ColorOutput "Scripts removed: $removedCount" "White"
Write-ColorOutput "Scripts backed up: $backedUpCount" "White"
Write-ColorOutput "Unified scripts kept: $($keepScripts.Count)" "White"

if ($Backup) {
    Write-ColorOutput "`nBackup location: $backupDir" "Cyan"
}

Write-ColorOutput "`n=== Next Steps ===" "Cyan"
Write-ColorOutput "1. Restart your computer to ensure clean environment" "White"
Write-ColorOutput "2. Use 'start-blackcnote-unified.bat' to start BlackCnote" "White"
Write-ColorOutput "3. Run as administrator for best results" "White"

Write-ColorOutput "`nCleanup completed at: $(Get-Date)" "Green" 