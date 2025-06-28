# BlackCnote Startup Execution Fix
# This script fixes the issue where startup scripts open as text files instead of executing

param(
    [switch]$CreateTask,
    [switch]$RemoveTask,
    [switch]$FixStartupScript,
    [switch]$All
)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { Write-ColorOutput $args "Green" }
function Write-Warning { Write-ColorOutput $args "Yellow" }
function Write-Error { Write-ColorOutput $args "Red" }
function Write-Info { Write-ColorOutput $args "Cyan" }

# Function to check if running as administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Function to create proper startup task
function Create-StartupTask {
    Write-Info "Creating proper BlackCnote startup task..."
    
    try {
        $taskName = "BlackCnoteStartup"
        $projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
        $startupScript = "$projectRoot\scripts\start-docker-enhanced.ps1"
        
        # Remove existing task if it exists
        $existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
        if ($existingTask) {
            Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
            Write-Info "Removed existing task: $taskName"
        }
        
        # Create action to run PowerShell script
        $action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-ExecutionPolicy Bypass -File `"$startupScript`" -Quiet" -WorkingDirectory $projectRoot
        
        # Create trigger (at startup with delay)
        $trigger = New-ScheduledTaskTrigger -AtStartup
        $trigger.Delay = "PT2M"  # 2 minute delay
        
        # Create settings
        $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable -WakeToRun
        
        # Create principal (run as current user with highest privileges)
        $principal = New-ScheduledTaskPrincipal -UserId "$env:USERDOMAIN\$env:USERNAME" -LogonType Interactive -RunLevel Highest
        
        # Register the task
        Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Description "BlackCnote Docker and Services Startup"
        
        Write-Success "Startup task created: $taskName"
        Write-Info "Task will run 2 minutes after Windows startup"
        
        return $true
    }
    catch {
        Write-Error "Failed to create startup task: $($_.Exception.Message)"
        return $false
    }
}

# Function to remove startup task
function Remove-StartupTask {
    Write-Info "Removing BlackCnote startup task..."
    
    try {
        $taskName = "BlackCnoteStartup"
        $existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
        
        if ($existingTask) {
            Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
            Write-Success "Removed startup task: $taskName"
        } else {
            Write-Info "No startup task found to remove"
        }
        
        return $true
    }
    catch {
        Write-Error "Failed to remove startup task: $($_.Exception.Message)"
        return $false
    }
}

# Function to fix startup script execution
function Fix-StartupScriptExecution {
    Write-Info "Fixing startup script execution..."
    
    try {
        $projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
        $startupScript = "$env:USERPROFILE\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\start-blackcnote-docker.bat"
        
        # Remove the problematic startup script
        if (Test-Path $startupScript) {
            Remove-Item $startupScript -Force
            Write-Info "Removed problematic startup script: $startupScript"
        }
        
        # Create a proper .cmd file that will execute correctly
        $cmdStartupScript = "$env:USERPROFILE\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\start-blackcnote-docker.cmd"
        
        # Build the CMD content with proper variable expansion
        $cmdContent = "@echo off`r`n"
        $cmdContent += "REM BlackCnote Docker Startup (CMD Version)`r`n"
        $cmdContent += "set PROJECT_ROOT=$projectRoot`r`n"
        $cmdContent += "cd /d `"%PROJECT_ROOT%`"`r`n"
        $cmdContent += "powershell.exe -ExecutionPolicy Bypass -File `"scripts\start-docker-enhanced.ps1`" -Quiet`r`n"
        
        Set-Content -Path $cmdStartupScript -Value $cmdContent -Encoding ASCII
        Write-Success "Created proper startup script: $cmdStartupScript"
        
        # Also create a VBS wrapper for even better compatibility
        $vbsStartupScript = "$env:USERPROFILE\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\start-blackcnote-docker.vbs"
        
        # Build the VBS content with proper variable expansion
        $vbsContent = "' BlackCnote Docker Startup (VBS Wrapper)`r`n"
        $vbsContent += "Set objShell = CreateObject(`"WScript.Shell`")`r`n"
        $vbsContent += "objShell.CurrentDirectory = `"$projectRoot`"`r`n"
        $vbsContent += "objShell.Run `"powershell.exe -ExecutionPolicy Bypass -File scripts\start-docker-enhanced.ps1 -Quiet`", 0, False`r`n"
        
        Set-Content -Path $vbsStartupScript -Value $vbsContent -Encoding ASCII
        Write-Success "Created VBS wrapper: $vbsStartupScript"
        
        return $true
    }
    catch {
        Write-Error "Failed to fix startup script execution: $($_.Exception.Message)"
        return $false
    }
}

# Main execution
Write-Info "BlackCnote Startup Execution Fix"
Write-Info "================================="
Write-Output ""

# Check administrator privileges
if (-not (Test-Administrator)) {
    Write-Error "This script must be run as Administrator!"
    Write-Output "Please right-click PowerShell and select 'Run as Administrator'"
    Write-Output "Then run this script again."
    exit 1
}

Write-Success "Running with administrator privileges"
Write-Output ""

$success = $true

if ($All -or $CreateTask) {
    $success = $success -and (Create-StartupTask)
}

if ($All -or $RemoveTask) {
    $success = $success -and (Remove-StartupTask)
}

if ($All -or $FixStartupScript) {
    $success = $success -and (Fix-StartupScriptExecution)
}

Write-Output ""

if ($success) {
    Write-Success "Startup execution fix completed successfully!"
    Write-Output ""
    Write-Info "What was fixed:"
    Write-Output "  ✓ Removed problematic startup script that opened as text"
    Write-Output "  ✓ Created Windows Task Scheduler task for proper execution"
    Write-Output "  ✓ Created CMD and VBS wrapper scripts for compatibility"
    Write-Output ""
    Write-Info "Next steps:"
    Write-Output "  1. Restart your computer to test the new startup configuration"
    Write-Output "  2. The script will run automatically 2 minutes after startup"
    Write-Output "  3. Check Task Scheduler to verify the task is created"
    Write-Output ""
    Write-Info "To check task status:"
    Write-Output "  Get-ScheduledTask -TaskName 'BlackCnoteStartup'"
    Write-Output ""
} else {
    Write-Error "Some operations failed. Please check the error messages above."
    exit 1
} }
