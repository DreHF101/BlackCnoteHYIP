# BlackCnote Script Checker Scheduler
# Creates Windows Task Scheduler tasks for automatic script validation

param(
    [switch]$CreateTask,
    [switch]$RemoveTask,
    [switch]$RunNow,
    [switch]$Verbose
)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    if ($Verbose) {
        Write-Host $Message -ForegroundColor $Color
    }
}

# Function to check if running as administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Function to create scheduled task
function Create-ScriptCheckerTask {
    Write-ColorOutput "Creating BlackCnote Script Checker scheduled task..." "Yellow"
    
    $taskName = "BlackCnoteScriptChecker"
    $scriptPath = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools\debug\check-all-scripts.ps1"
    $projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
    
    try {
        # Remove existing task if it exists
        Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue
        Write-ColorOutput "Removed existing task: $taskName" "Yellow"
        
        # Create task action
        $action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-ExecutionPolicy Bypass -File `"$scriptPath`" -Verbose" -WorkingDirectory $projectRoot
        
        # Create multiple triggers
        $triggers = @()
        
        # Daily trigger at 2 AM
        $dailyTrigger = New-ScheduledTaskTrigger -Daily -At "02:00"
        $triggers += $dailyTrigger
        
        # Weekly trigger on Sundays at 3 AM
        $weeklyTrigger = New-ScheduledTaskTrigger -Weekly -DaysOfWeek Sunday -At "03:00"
        $triggers += $weeklyTrigger
        
        # At startup trigger (delayed by 5 minutes)
        $startupTrigger = New-ScheduledTaskTrigger -AtStartup
        $startupTrigger.Delay = "PT5M"
        $triggers += $startupTrigger
        
        # Create settings
        $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable -WakeToRun
        
        # Create principal (run as SYSTEM with highest privileges)
        $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
        
        # Register the task
        Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $triggers -Settings $settings -Principal $principal -Description "BlackCnote Script Integrity Checker - Validates all scripts for syntax, Unicode, and structural issues" -Force
        
        Write-ColorOutput "[SUCCESS] Created scheduled task: $taskName" "Green"
        Write-ColorOutput "Task will run:" "White"
        Write-ColorOutput "  - Daily at 2:00 AM" "White"
        Write-ColorOutput "  - Weekly on Sundays at 3:00 AM" "White"
        Write-ColorOutput "  - At system startup (5 minute delay)" "White"
        
        return $true
    }
    catch {
        Write-ColorOutput "[ERROR] Failed to create task: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to remove scheduled task
function Remove-ScriptCheckerTask {
    Write-ColorOutput "Removing BlackCnote Script Checker scheduled task..." "Yellow"
    
    $taskName = "BlackCnoteScriptChecker"
    
    try {
        Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
        Write-ColorOutput "[SUCCESS] Removed scheduled task: $taskName" "Green"
        return $true
    }
    catch {
        Write-ColorOutput "[ERROR] Failed to remove task: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to run script checker now
function Invoke-ScriptCheckerNow {
    Write-ColorOutput "Running BlackCnote Script Checker now..." "Yellow"
    
    $scriptPath = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools\debug\check-all-scripts.ps1"
    $projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
    
    if (Test-Path $scriptPath) {
        try {
            Set-Location $projectRoot
            & powershell.exe -ExecutionPolicy Bypass -File $scriptPath -Verbose
            $exitCode = $LASTEXITCODE
            
            Write-ColorOutput "Script checker completed with exit code: $exitCode" "White"
            
            # Display results summary
            $logFile = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\logs\script-check.log"
            $jsonFile = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\logs\script-check.json"
            
            if (Test-Path $jsonFile) {
                $jsonData = Get-Content $jsonFile | ConvertFrom-Json
                Write-ColorOutput "`n=== QUICK SUMMARY ===" "Cyan"
                Write-ColorOutput "Total Files: $($jsonData.Summary.TotalFiles)" "White"
                Write-ColorOutput "Errors: $($jsonData.Summary.ErrorFiles)" "Red"
                Write-ColorOutput "Warnings: $($jsonData.Summary.WarningFiles)" "Yellow"
                Write-ColorOutput "Passed: $($jsonData.Summary.PassFiles)" "Green"
                Write-ColorOutput "Status: $($jsonData.Summary.OverallStatus)" $(if ($jsonData.Summary.OverallStatus -eq "PASS") { "Green" } elseif ($jsonData.Summary.OverallStatus -eq "WARNING") { "Yellow" } else { "Red" })
            }
            
            return $true
        }
        catch {
            Write-ColorOutput "[ERROR] Failed to run script checker: $($_.Exception.Message)" "Red"
            return $false
        }
    } else {
        Write-ColorOutput "[ERROR] Script checker not found at: $scriptPath" "Red"
        return $false
    }
}

# Function to create email notification script
function Create-EmailNotification {
    Write-ColorOutput "Creating email notification script..." "Yellow"
    
    $notificationScript = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools\debug\send-script-check-email.ps1"
    
    $scriptContent = @'
# BlackCnote Script Check Email Notification
# Sends email notifications for script check results

param(
    [string]$SmtpServer = "smtp.gmail.com",
    [int]$SmtpPort = 587,
    [string]$FromEmail = "blackcnote@yourdomain.com",
    [string]$ToEmail = "admin@yourdomain.com",
    [string]$Username = "your-email@yourdomain.com",
    [string]$Password = "your-app-password"
)

$jsonFile = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\logs\script-check.json"
$logFile = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\logs\script-check.log"

if (Test-Path $jsonFile) {
    $data = Get-Content $jsonFile | ConvertFrom-Json
    $summary = $data.Summary
    
    $subject = "BlackCnote Script Check - $($summary.OverallStatus)"
    $body = @"
BlackCnote Script Integrity Check Report
Generated: $($summary.Timestamp)

SUMMARY:
- Total Files: $($summary.TotalFiles)
- Errors: $($summary.ErrorFiles)
- Warnings: $($summary.WarningFiles)
- Passed: $($summary.PassFiles)
- Status: $($summary.OverallStatus)

$(if (Test-Path $logFile) { "Detailed report attached." } else { "No detailed log available." })

This is an automated message from the BlackCnote Debug System.
"@
    
    try {
        $smtp = New-Object System.Net.Mail.SmtpClient($SmtpServer, $SmtpPort)
        $smtp.EnableSsl = $true
        $smtp.Credentials = New-Object System.Net.NetworkCredential($Username, $Password)
        
        $message = New-Object System.Net.Mail.MailMessage($FromEmail, $ToEmail, $subject, $body)
        
        if (Test-Path $logFile) {
            $attachment = New-Object System.Net.Mail.Attachment($logFile)
            $message.Attachments.Add($attachment)
        }
        
        $smtp.Send($message)
        Write-Host "Email notification sent successfully"
    }
    catch {
        Write-Host "Failed to send email: $($_.Exception.Message)"
    }
}
'@
    
    Set-Content -Path $notificationScript -Value $scriptContent -Encoding UTF8
    Write-ColorOutput "[SUCCESS] Created email notification script: $notificationScript" "Green"
    Write-ColorOutput "Please configure SMTP settings in the script before use." "Yellow"
}

# Main execution
Write-ColorOutput "=== BlackCnote Script Checker Scheduler ===" "Cyan"
Write-ColorOutput "Starting at: $(Get-Date)" "White"

# Check if running as administrator
if (-not (Test-Administrator)) {
    Write-ColorOutput "[ERROR] This script requires administrator privileges!" "Red"
    Write-ColorOutput "Please run PowerShell as Administrator and try again." "Red"
    exit 1
}

$success = $true

if ($CreateTask) {
    $success = $success -and (Create-ScriptCheckerTask)
    if ($success) {
        Create-EmailNotification
    }
}

if ($RemoveTask) {
    $success = $success -and (Remove-ScriptCheckerTask)
}

if ($RunNow) {
    $success = $success -and (Invoke-ScriptCheckerNow)
}

if (-not $CreateTask -and -not $RemoveTask -and -not $RunNow) {
    Write-ColorOutput "No action specified. Available options:" "Yellow"
    Write-ColorOutput "  -CreateTask : Create scheduled task for automatic script checking" "White"
    Write-ColorOutput "  -RemoveTask : Remove scheduled task" "White"
    Write-ColorOutput "  -RunNow     : Run script checker immediately" "White"
    Write-ColorOutput "  -Verbose    : Show detailed output" "White"
    Write-ColorOutput ""
    Write-ColorOutput "Example: .\schedule-script-checker.ps1 -CreateTask -Verbose" "Cyan"
}

Write-ColorOutput ""
Write-ColorOutput "Script checker scheduler completed." "Green" 