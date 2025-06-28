# BlackCnote Docker Privileges Setup Script
# This script configures Docker to always run with elevated privileges
# Run as Administrator

param(
    [switch]$CreateShortcut,
    [switch]$CreateTask,
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

# Function to create startup script in Windows startup folder
function Create-StartupScript {
    Write-ColorOutput "[Startup] Creating startup script..." "Yellow"
    
    $startupFolder = "$env:APPDATA\Microsoft\Windows\Start Menu\Programs\Startup"
    $startupScript = Join-Path $startupFolder "Docker-Desktop-Elevated.bat"
    
    $scriptContent = @"
@echo off
REM Docker Desktop Elevated Startup
REM This script starts Docker Desktop with administrator privileges

REM Check if running as administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Starting Docker Desktop with elevated privileges...
    powershell -Command "Start-Process 'C:\Program Files\Docker\Docker\Docker Desktop.exe' -Verb RunAs -WindowStyle Minimized"
) else (
    echo Starting Docker Desktop...
    start "" /B "C:\Program Files\Docker\Docker\Docker Desktop.exe"
)
"@

    try {
        Set-Content -Path $startupScript -Value $scriptContent -Encoding ASCII
        Write-ColorOutput "[Startup] [OK] Created startup script: $startupScript" "Green"
        return $true
    }
    catch {
        Write-ColorOutput "[Startup] [ERROR] Failed to create startup script: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Main execution
Write-ColorOutput "=== BlackCnote Docker Privileges Setup ===" "Cyan"
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

if ($All -or $CreateTask) {
    $totalCount++
    if (Create-DockerTask) { $successCount++ }
}

# Always run these fixes
$totalCount++
if (Fix-DockerPermissions) { $successCount++ }

$totalCount++
if (Create-StartupScript) { $successCount++ }

# Summary
Write-ColorOutput "`n=== Setup Summary ===" "Cyan"
Write-ColorOutput "Completed: $successCount/$totalCount configurations" $(if($successCount -eq $totalCount){"Green"}else{"Yellow"})

if ($successCount -eq $totalCount) {
    Write-ColorOutput "`n[SUCCESS] Docker privileges setup completed successfully!" "Green"
    Write-ColorOutput "Docker Desktop will now run with elevated privileges." "White"
} else {
    Write-ColorOutput "`n[WARNING] Some configurations failed. Please check the errors above." "Yellow"
}

Write-ColorOutput "`n=== Usage Instructions ===" "Cyan"
Write-ColorOutput "1. Use the created shortcuts to start Docker with elevated privileges" "White"
Write-ColorOutput "2. Run: .\start-docker-elevated.bat (as Administrator)" "White"
Write-ColorOutput "3. Run: .\start-docker-elevated.ps1 (as Administrator)" "White"
Write-ColorOutput "4. Docker Desktop will now start with proper privileges" "White"
Write-ColorOutput "5. The startup script will run automatically when Windows starts" "White"

Write-ColorOutput "`nScript completed at: $(Get-Date)" "White" 