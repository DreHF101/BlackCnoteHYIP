# BlackCnote Docker Privileges Fix Script
# This script fixes Docker privilege issues and ensures Docker always runs with elevated permissions
# Run as Administrator

param(
    [switch]$AutoStart,
    [switch]$CreateShortcut,
    [switch]$FixRegistry,
    [switch]$All
)

# Set error action preference
$ErrorActionPreference = "Continue"

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

# Function to create elevated Docker Desktop shortcut
function Create-ElevatedDockerShortcut {
    Write-ColorOutput "[Shortcut] Creating elevated Docker Desktop shortcut..." "Yellow"
    
    $shortcutPath = "$env:USERPROFILE\Desktop\Docker Desktop (Admin).lnk"
    $dockerPath = "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    
    if (Test-Path $dockerPath) {
        $WshShell = New-Object -comObject WScript.Shell
        $Shortcut = $WshShell.CreateShortcut($shortcutPath)
        $Shortcut.TargetPath = $dockerPath
        $Shortcut.WorkingDirectory = "C:\Program Files\Docker\Docker"
        $Shortcut.Description = "Docker Desktop with Administrator Privileges"
        $Shortcut.IconLocation = $dockerPath
        $Shortcut.Save()
        
        # Set shortcut to run as administrator
        $bytes = [System.IO.File]::ReadAllBytes($shortcutPath)
        $bytes[0x15] = $bytes[0x15] -bor 0x20
        [System.IO.File]::WriteAllBytes($shortcutPath, $bytes)
        
        Write-ColorOutput "[Shortcut] [OK] Created: $shortcutPath" "Green"
        return $true
    } else {
        Write-ColorOutput "[Shortcut] [ERROR] Docker Desktop not found at: $dockerPath" "Red"
        return $false
    }
}

# Function to fix Docker registry settings
function Fix-DockerRegistry {
    Write-ColorOutput "[Registry] Fixing Docker registry settings..." "Yellow"
    
    try {
        # Create Docker registry keys if they don't exist
        $dockerKey = "HKLM:\SOFTWARE\Docker Inc.\Docker Desktop"
        if (!(Test-Path $dockerKey)) {
            New-Item -Path $dockerKey -Force | Out-Null
        }
        
        # Set Docker to run with elevated privileges
        Set-ItemProperty -Path $dockerKey -Name "RunAsAdmin" -Value 1 -Type DWord -Force
        
        # Set Docker to start automatically with Windows
        Set-ItemProperty -Path $dockerKey -Name "AutoStart" -Value 1 -Type DWord -Force
        
        # Set Docker to use WSL2 backend
        Set-ItemProperty -Path $dockerKey -Name "UseWSL2" -Value 1 -Type DWord -Force
        
        Write-ColorOutput "[Registry] [OK] Docker registry settings updated" "Green"
        return $true
    }
    catch {
        Write-ColorOutput "[Registry] [ERROR] Failed to update registry: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to create Windows Task Scheduler task for Docker
function Create-DockerTask {
    Write-ColorOutput "[Task] Creating Windows Task Scheduler task for Docker..." "Yellow"
    
    $taskName = "Docker Desktop Elevated"
    $dockerPath = "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    
    try {
        # Remove existing task if it exists
        Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue
        
        # Create task action
        $action = New-ScheduledTaskAction -Execute $dockerPath -WorkingDirectory "C:\Program Files\Docker\Docker"
        
        # Create trigger (at logon)
        $trigger = New-ScheduledTaskTrigger -AtLogOn
        
        # Create settings
        $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable
        
        # Create principal (run as current user with highest privileges)
        $principal = New-ScheduledTaskPrincipal -UserId "$env:USERDOMAIN\$env:USERNAME" -LogonType Interactive -RunLevel Highest
        
        # Register the task
        Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Description "Docker Desktop with Elevated Privileges" -Force
        
        Write-ColorOutput "[Task] [OK] Created scheduled task: $taskName" "Green"
        return $true
    }
    catch {
        Write-ColorOutput "[Task] [ERROR] Failed to create task: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to fix Docker Desktop permissions
function Fix-DockerPermissions {
    Write-ColorOutput "[Permissions] Fixing Docker Desktop permissions..." "Yellow"
    
    $dockerPath = "C:\Program Files\Docker"
    
    if (Test-Path $dockerPath) {
        try {
            # Grant full control to current user
            $currentUser = "$env:USERDOMAIN\$env:USERNAME"
            icacls $dockerPath /grant "${currentUser}:(OI)(CI)F" /T
            
            # Grant full control to Administrators
            icacls $dockerPath /grant "Administrators:(OI)(CI)F" /T
            
            # Grant full control to SYSTEM
            icacls $dockerPath /grant "SYSTEM:(OI)(CI)F" /T
            
            Write-ColorOutput "[Permissions] [OK] Docker permissions updated" "Green"
            return $true
        }
        catch {
            Write-ColorOutput "[Permissions] [ERROR] Failed to update permissions: $($_.Exception.Message)" "Red"
            return $false
        }
    } else {
        Write-ColorOutput "[Permissions] [ERROR] Docker not found at: $dockerPath" "Red"
        return $false
    }
}

# Function to restart Docker services
function Restart-DockerServices {
    Write-ColorOutput "[Services] Restarting Docker services..." "Yellow"
    
    try {
        # Stop Docker Desktop if running
        $dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
        if ($dockerProcess) {
            Write-ColorOutput "[Services] Stopping Docker Desktop..." "Yellow"
            Stop-Process -Name "Docker Desktop" -Force
            Start-Sleep -Seconds 5
        }
        
        # Restart Docker Desktop service
        $dockerService = Get-Service -Name "com.docker.service" -ErrorAction SilentlyContinue
        if ($dockerService) {
            Write-ColorOutput "[Services] Restarting Docker service..." "Yellow"
            Restart-Service -Name "com.docker.service" -Force
        }
        
        # Start Docker Desktop with elevated privileges
        Write-ColorOutput "[Services] Starting Docker Desktop..." "Yellow"
        Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -Verb RunAs -WindowStyle Minimized
        
        Write-ColorOutput "[Services] [OK] Docker services restarted" "Green"
        return $true
    }
    catch {
        Write-ColorOutput "[Services] [ERROR] Failed to restart services: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to create batch file for elevated Docker startup
function Create-ElevatedDockerBatch {
    Write-ColorOutput "[Batch] Creating elevated Docker startup batch file..." "Yellow"
    
    $batchContent = @'
@echo off
REM Docker Desktop Elevated Startup
REM This batch file starts Docker Desktop with administrator privileges

echo Starting Docker Desktop with elevated privileges...
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: This script requires administrator privileges!
    echo Please right-click and select "Run as administrator"
    pause
    exit /b 1
)

REM Start Docker Desktop
echo Starting Docker Desktop...
start "" /B "C:\Program Files\Docker\Docker\Docker Desktop.exe"

REM Wait for Docker to start
echo Waiting for Docker to start...
timeout /t 10 /nobreak >nul

REM Check if Docker is running
echo Checking Docker status...
docker info >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] Docker is running successfully!
    echo.
    echo Docker services are ready.
    echo You can now run BlackCnote startup scripts.
) else (
    echo [ERROR] Docker failed to start properly.
    echo Please check Docker Desktop manually.
)

echo.
pause
'@

    $batchPath = Join-Path $PSScriptRoot "start-docker-elevated.bat"
    Set-Content -Path $batchPath -Value $batchContent -Encoding ASCII
    
    Write-ColorOutput "[Batch] [OK] Created: $batchPath" "Green"
    return $true
}

# Function to create PowerShell script for elevated Docker startup
function Create-ElevatedDockerPowerShell {
    Write-ColorOutput "[PowerShell] Creating elevated Docker startup PowerShell script..." "Yellow"
    
    $psContent = @"
# Docker Desktop Elevated Startup Script
# This script starts Docker Desktop with administrator privileges

param([switch]`$Quiet)

# Function to write colored output
function Write-ColorOutput {
    param([string]`$Message, [string]`$Color = "White")
    if (-not `$Quiet) { Write-Host `$Message -ForegroundColor `$Color }
}

# Function to check if running as administrator
function Test-Administrator {
    `$currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    `$principal = New-Object Security.Principal.WindowsPrincipal(`$currentUser)
    return `$principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Check administrator privileges
if (-not (Test-Administrator)) {
    Write-ColorOutput "[ERROR] This script requires administrator privileges!" "Red"
    Write-ColorOutput "Please run PowerShell as Administrator and try again." "Red"
    exit 1
}

Write-ColorOutput "=== Docker Desktop Elevated Startup ===" "Cyan"
Write-ColorOutput "Starting at: `$(Get-Date)" "White"

# Stop Docker Desktop if running
`$dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
if (`$dockerProcess) {
    Write-ColorOutput "[Docker] Stopping existing Docker Desktop..." "Yellow"
    Stop-Process -Name "Docker Desktop" -Force
    Start-Sleep -Seconds 5
}

# Start Docker Desktop with elevated privileges
Write-ColorOutput "[Docker] Starting Docker Desktop..." "Yellow"
Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized

# Wait for Docker to be ready
Write-ColorOutput "[Docker] Waiting for Docker to be ready..." "Yellow"
`$dockerReady = `$false
for (`$i = 1; `$i -le 30; `$i++) {
    try {
        `$dockerInfo = docker info 2>`$null
        if (`$LASTEXITCODE -eq 0) {
            `$dockerReady = `$true
            break
        }
    }
    catch { }
    Start-Sleep -Seconds 2
}

if (`$dockerReady) {
    Write-ColorOutput "[Docker] [OK] Docker is ready!" "Green"
    Write-ColorOutput "Docker services are available for BlackCnote." "White"
} else {
    Write-ColorOutput "[Docker] [ERROR] Docker failed to start properly." "Red"
    Write-ColorOutput "Please check Docker Desktop manually." "Yellow"
}

Write-ColorOutput "Script completed at: `$(Get-Date)" "White"
"@

    $psPath = Join-Path $PSScriptRoot "start-docker-elevated.ps1"
    Set-Content -Path $psPath -Value $psContent -Encoding UTF8
    
    Write-ColorOutput "[PowerShell] [OK] Created: $psPath" "Green"
    return $true
}

# Main execution
Write-ColorOutput "=== BlackCnote Docker Privileges Fix ===" "Cyan"
Write-ColorOutput "Starting at: $(Get-Date)" "White"

# Check if running as administrator
if (-not (Test-Administrator)) {
    Write-ColorOutput "[ERROR] This script requires administrator privileges!" "Red"
    Write-ColorOutput "Please run PowerShell as Administrator and try again." "Red"
    exit 1
}

$successCount = 0
$totalCount = 0

# Execute fixes based on parameters
if ($All -or $CreateShortcut) {
    $totalCount++
    if (Create-ElevatedDockerShortcut) { $successCount++ }
}

if ($All -or $FixRegistry) {
    $totalCount++
    if (Fix-DockerRegistry) { $successCount++ }
}

if ($All -or $AutoStart) {
    $totalCount++
    if (Create-DockerTask) { $successCount++ }
}

# Always run these fixes
$totalCount++
if (Fix-DockerPermissions) { $successCount++ }

$totalCount++
if (Create-ElevatedDockerBatch) { $successCount++ }

$totalCount++
if (Create-ElevatedDockerPowerShell) { $successCount++ }

# Restart Docker services
$totalCount++
if (Restart-DockerServices) { $successCount++ }

# Summary
Write-ColorOutput "`n=== Fix Summary ===" "Cyan"
Write-ColorOutput "Completed: $successCount/$totalCount fixes" $(if($successCount -eq $totalCount){"Green"}else{"Yellow"})

if ($successCount -eq $totalCount) {
    Write-ColorOutput "`n[SUCCESS] All Docker privilege fixes completed successfully!" "Green"
    Write-ColorOutput "Docker Desktop should now run with elevated privileges." "White"
} else {
    Write-ColorOutput "`n[WARNING] Some fixes failed. Please check the errors above." "Yellow"
}

Write-ColorOutput "`n=== Usage Instructions ===" "Cyan"
Write-ColorOutput "1. Use the created shortcuts to start Docker with elevated privileges" "White"
Write-ColorOutput "2. Run: .\start-docker-elevated.bat (as Administrator)" "White"
Write-ColorOutput "3. Run: .\start-docker-elevated.ps1 (as Administrator)" "White"
Write-ColorOutput "4. Docker Desktop will now start with proper privileges" "White"

Write-ColorOutput "`nScript completed at: $(Get-Date)" "White" 