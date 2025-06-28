# BlackCnote Task Scheduler Creator
# This script automatically creates a Windows Task Scheduler task for BlackCnote startup

Write-Host "BlackCnote Task Scheduler Creator" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")

if (-not $isAdmin) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Please right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    Write-Host "Then run this script again." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Press any key to exit..." -ForegroundColor Gray
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
    exit 1
}

Write-Host "SUCCESS: Running with administrator privileges" -ForegroundColor Green
Write-Host ""

# Define task parameters
$taskName = "BlackCnoteStartup"
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$startupScript = "$projectRoot\scripts\start-docker-enhanced.ps1"

Write-Host "Creating Task Scheduler task..." -ForegroundColor Yellow

try {
    # Remove existing task if it exists
    $existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
    if ($existingTask) {
        Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
        Write-Host "Removed existing task: $taskName" -ForegroundColor Yellow
    }
    
    # Create action
    $action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-ExecutionPolicy Bypass -File `"$startupScript`" -Quiet" -WorkingDirectory $projectRoot
    
    # Create trigger (at startup with 2-minute delay)
    $trigger = New-ScheduledTaskTrigger -AtStartup
    $trigger.Delay = "PT2M"
    
    # Create settings
    $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable -WakeToRun
    
    # Create principal (run as current user with highest privileges)
    $principal = New-ScheduledTaskPrincipal -UserId "$env:USERDOMAIN\$env:USERNAME" -LogonType Interactive -RunLevel Highest
    
    # Register the task
    Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Description "BlackCnote Docker and Services Startup"
    
    Write-Host ""
    Write-Host "SUCCESS: Task Scheduler task created!" -ForegroundColor Green
    Write-Host "Task Name: $taskName" -ForegroundColor White
    Write-Host "Will run: 2 minutes after Windows startup" -ForegroundColor White
    Write-Host "Script: $startupScript" -ForegroundColor White
    Write-Host ""
    Write-Host "To verify the task:" -ForegroundColor Cyan
    Write-Host "  Get-ScheduledTask -TaskName '$taskName'" -ForegroundColor Gray
    Write-Host ""
    Write-Host "To test the task:" -ForegroundColor Cyan
    Write-Host "  Start-ScheduledTask -TaskName '$taskName'" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Next step: Restart your computer to test automatic startup" -ForegroundColor Yellow
    
} catch {
    Write-Host ""
    Write-Host "ERROR: Failed to create Task Scheduler task" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Alternative: Use the .cmd file in Startup folder" -ForegroundColor Yellow
    Write-Host "Location: $env:APPDATA\Microsoft\Windows\Start Menu\Programs\Startup\start-blackcnote-docker.cmd" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Press any key to exit..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown") 