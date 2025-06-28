# BlackCnote Docker Privileges Setup Script
# This script permanently configures Docker to run with elevated privileges

param(
    [switch]$All,
    [switch]$CreateShortcut,
    [switch]$CreateTask,
    [switch]$FixRegistry,
    [switch]$FixPermissions,
    [switch]$Verbose
)

# Set error action preference
$ErrorActionPreference = "Stop"

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

# Check if running as administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Function to fix registry settings
function Fix-DockerRegistry {
    Write-Info "Fixing Docker registry settings..."
    
    try {
        # Create Docker registry path if it doesn't exist
        $dockerRegPath = "HKLM:\SOFTWARE\Docker Inc.\Docker Desktop"
        if (-not (Test-Path $dockerRegPath)) {
            New-Item -Path $dockerRegPath -Force | Out-Null
        }
        
        # Set Docker to run with elevated privileges
        Set-ItemProperty -Path $dockerRegPath -Name "RunAsAdmin" -Value 1 -Type DWord
        Write-Success "Set Docker to run with elevated privileges"
        
        # Enable automatic startup
        Set-ItemProperty -Path $dockerRegPath -Name "AutoStart" -Value 1 -Type DWord
        Write-Success "Enabled automatic Docker startup"
        
        # Enable WSL2 backend
        Set-ItemProperty -Path $dockerRegPath -Name "UseWSL2" -Value 1 -Type DWord
        Write-Success "Enabled WSL2 backend"
        
        # Set Docker Desktop to start minimized
        Set-ItemProperty -Path $dockerRegPath -Name "StartMinimized" -Value 1 -Type DWord
        Write-Success "Set Docker Desktop to start minimized"
        
        # Disable experimental features for stability
        Set-ItemProperty -Path $dockerRegPath -Name "Experimental" -Value 0 -Type DWord
        Write-Success "Disabled experimental features for stability"
        
        # Set Docker Desktop to use Linux containers by default
        Set-ItemProperty -Path $dockerRegPath -Name "UseWindowsContainers" -Value 0 -Type DWord
        Write-Success "Set to use Linux containers by default"
        
        Write-Success "Registry settings fixed successfully"
        
    } catch {
        Write-Error "Failed to fix registry settings: $($_.Exception.Message)"
        return $false
    }
    
    return $true
}

# Function to fix file permissions
function Fix-DockerPermissions {
    Write-Info "Fixing Docker file permissions..."
    
    try {
        $dockerPath = "C:\Program Files\Docker"
        
        if (Test-Path $dockerPath) {
            # Grant full control to current user
            $currentUser = "$env:USERDOMAIN\$env:USERNAME"
            icacls $dockerPath /grant "${currentUser}:(OI)(CI)F" /T
            Write-Success "Granted full control to current user"
            
            # Grant full control to Administrators
            icacls $dockerPath /grant "Administrators:(OI)(CI)F" /T
            Write-Success "Granted full control to Administrators"
            
            # Grant full control to SYSTEM
            icacls $dockerPath /grant "SYSTEM:(OI)(CI)F" /T
            Write-Success "Granted full control to SYSTEM"
            
            # Set proper inheritance
            icacls $dockerPath /inheritance:r
            icacls $dockerPath /grant:r "Administrators:(OI)(CI)F"
            icacls $dockerPath /grant:r "SYSTEM:(OI)(CI)F"
            icacls $dockerPath /grant:r "${currentUser}:(OI)(CI)F"
            Write-Success "Set proper inheritance"
            
        } else {
            Write-Warning "Docker installation not found at: $dockerPath"
        }
        
        # Fix Docker Desktop user directory permissions
        $dockerUserPath = "$env:USERPROFILE\.docker"
        if (Test-Path $dockerUserPath) {
            icacls $dockerUserPath /grant "${currentUser}:(OI)(CI)F" /T
            Write-Success "Fixed Docker user directory permissions"
        }
        
        Write-Success "File permissions fixed successfully"
        
    } catch {
        Write-Error "Failed to fix file permissions: $($_.Exception.Message)"
        return $false
    }
    
    return $true
}

# Function to create desktop shortcut
function Create-DockerShortcut {
    Write-Info "Creating Docker desktop shortcut with elevated privileges..."
    
    try {
        $shortcutPath = "$env:USERPROFILE\Desktop\BlackCnote Docker.lnk"
        $dockerPath = "C:\Program Files\Docker\Docker\Docker Desktop.exe"
        $projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
        
        if (-not (Test-Path $dockerPath)) {
            Write-Error "Docker Desktop not found at: $dockerPath"
            return $false
        }
        
        $WshShell = New-Object -comObject WScript.Shell
        $Shortcut = $WshShell.CreateShortcut($shortcutPath)
        $Shortcut.TargetPath = $dockerPath
        $Shortcut.WorkingDirectory = $projectRoot
        $Shortcut.Arguments = "--verbose"
        $Shortcut.Description = "Start BlackCnote Docker with elevated privileges"
        $Shortcut.IconLocation = $dockerPath
        $Shortcut.Save()
        
        # Set shortcut to run as administrator
        $bytes = [System.IO.File]::ReadAllBytes($shortcutPath)
        $bytes[0x15] = $bytes[0x15] -bor 0x20
        [System.IO.File]::WriteAllBytes($shortcutPath, $bytes)
        
        Write-Success "Desktop shortcut created: $shortcutPath"
        
    } catch {
        Write-Error "Failed to create desktop shortcut: $($_.Exception.Message)"
        return $false
    }
    
    return $true
}

# Function to create Windows Task Scheduler task
function Create-DockerTask {
    Write-Info "Creating Windows Task Scheduler task for Docker..."
    
    try {
        $taskName = "BlackCnoteDockerStartup"
        $dockerPath = "C:\Program Files\Docker\Docker\Docker Desktop.exe"
        $projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
        
        if (-not (Test-Path $dockerPath)) {
            Write-Error "Docker Desktop not found at: $dockerPath"
            return $false
        }
        
        # Remove existing task if it exists
        $existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
        if ($existingTask) {
            Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
            Write-Info "Removed existing task: $taskName"
        }
        
        # Create action
        $action = New-ScheduledTaskAction -Execute $dockerPath -Argument "--verbose" -WorkingDirectory $projectRoot
        
        # Create trigger (at startup)
        $trigger = New-ScheduledTaskTrigger -AtStartup -Delay (New-TimeSpan -Seconds 30)
        
        # Create settings
        $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable
        
        # Create principal (run with highest privileges)
        $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
        
        # Register the task
        Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Description "Start BlackCnote Docker Desktop at system startup"
        
        Write-Success "Task Scheduler task created: $taskName"
        
    } catch {
        Write-Error "Failed to create Task Scheduler task: $($_.Exception.Message)"
        return $false
    }
    
    return $true
}

# Function to copy Docker daemon configuration
function Copy-DockerDaemonConfig {
    Write-Info "Copying Docker daemon configuration..."
    
    try {
        $sourceConfig = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\config\docker\daemon.json"
        $targetConfig = "$env:USERPROFILE\.docker\daemon.json"
        $targetDir = Split-Path $targetConfig -Parent
        
        if (Test-Path $sourceConfig) {
            if (-not (Test-Path $targetDir)) {
                New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
            }
            
            Copy-Item $sourceConfig $targetConfig -Force
            Write-Success "Docker daemon configuration copied"
        } else {
            Write-Warning "Source daemon configuration not found: $sourceConfig"
        }
        
    } catch {
        Write-Error "Failed to copy Docker daemon configuration: $($_.Exception.Message)"
        return $false
    }
    
    return $true
}

# Function to create startup script
function Create-StartupScript {
    Write-Info "Creating enhanced startup script..."
    
    try {
        $startupScript = "$env:USERPROFILE\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\start-blackcnote-docker.bat"
        $projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
        
        $scriptContent = @"
@echo off
REM BlackCnote Enhanced Docker Startup Script
REM This script starts Docker Desktop with the new enhanced configuration
REM Compatible with BlackCnote Docker Engine v28.1.1
REM Created: $(Get-Date)

echo ========================================
echo    BlackCnote Enhanced Docker Startup
echo ========================================
echo Starting at: %date% %time%
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [WARNING] Not running as administrator
    echo Docker Desktop will start with user privileges
    echo For best performance, run as administrator
    echo.
)

REM Set project directory
set PROJECT_ROOT=$projectRoot
cd /d "%PROJECT_ROOT%"
echo [INFO] Project directory: %PROJECT_ROOT%
echo.

REM Check if Docker Desktop is already running
echo [INFO] Checking Docker Desktop status...
tasklist /FI "IMAGENAME eq Docker Desktop.exe" 2>NUL | find /I /N "Docker Desktop.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [INFO] Docker Desktop is already running
    goto :startServices
)

REM Start Docker Desktop with enhanced configuration
echo [INFO] Starting Docker Desktop with enhanced configuration...
start "" /min "C:\Program Files\Docker\Docker\Docker Desktop.exe" --verbose

REM Wait for Docker to be ready with enhanced timeout
echo [INFO] Waiting for Docker daemon to be ready...
set /a attempts=0
set /a maxAttempts=45
set dockerReady=false

:waitLoop
set /a attempts+=1
echo    Attempt %attempts%/%maxAttempts%...

docker info >nul 2>&1
if %errorLevel% equ 0 (
    set dockerReady=true
    echo [SUCCESS] Docker daemon is ready!
    goto :startServices
) else (
    if %attempts% lss %maxAttempts% (
        timeout /t 2 /nobreak >nul
        goto :waitLoop
    ) else (
        echo [ERROR] Docker daemon failed to start within %maxAttempts% attempts
        echo [ERROR] Please check Docker Desktop logs and try again
        echo [ERROR] You can also run: .\scripts\start-docker-elevated.bat
        pause
        exit /b 1
    )
)

:startServices
echo.
echo [INFO] Starting BlackCnote services...

REM Check if docker-compose.yml exists
if not exist "docker-compose.yml" (
    echo [ERROR] docker-compose.yml not found in project root
    echo [ERROR] Please ensure you are in the correct directory
    pause
    exit /b 1
)

REM Stop any existing containers to prevent conflicts
echo [INFO] Stopping existing containers...
docker-compose down --remove-orphans >nul 2>&1

REM Start all services with enhanced configuration
echo [INFO] Starting BlackCnote Docker services...
docker-compose up -d --build

REM Check if services started successfully
if %errorLevel% neq 0 (
    echo [ERROR] Failed to start BlackCnote services
    echo [ERROR] Check logs with: docker-compose logs
    pause
    exit /b 1
)

REM Wait for services to be ready
echo [INFO] Waiting for services to initialize...
timeout /t 15 /nobreak >nul

REM Check service status
echo [INFO] Checking service status...
docker-compose ps

REM Wait for WordPress to be ready
echo [INFO] Waiting for WordPress to be ready...
set /a wpAttempts=0
set /a wpMaxAttempts=30

:waitWordPress
set /a wpAttempts+=1
echo    WordPress attempt %wpAttempts%/%wpMaxAttempts%...

curl -f http://localhost:8888 >nul 2>&1
if %errorLevel% equ 0 (
    echo [SUCCESS] WordPress is ready!
    goto :showStatus
) else (
    if %wpAttempts% lss %wpMaxAttempts% (
        timeout /t 2 /nobreak >nul
        goto :waitWordPress
    ) else (
        echo [WARNING] WordPress may still be starting up
        echo [WARNING] You can check manually at: http://localhost:8888
    )
)

:showStatus
echo.
echo ========================================
echo    BlackCnote Services Started!
echo ========================================
echo.
echo [SERVICES] Available at:
echo   WordPress:      http://localhost:8888
echo   WordPress Admin: http://localhost:8888/wp-admin
echo   React App:      http://localhost:5174
echo   phpMyAdmin:     http://localhost:8080
echo   Redis Commander: http://localhost:8081
echo   MailHog:        http://localhost:8025
echo   Metrics:        http://localhost:9091
echo   Health Check:   http://localhost:8888/health
echo.

REM Check if user wants to open browser
set /p OPEN_BROWSER="Open WordPress in browser? (y/N): "
if /i "%OPEN_BROWSER%"=="y" (
    echo [INFO] Opening WordPress in browser...
    start http://localhost:8888
    timeout /t 2 /nobreak >nul
    start http://localhost:5174
    timeout /t 2 /nobreak >nul
    start http://localhost:8080
)

echo.
echo [INFO] BlackCnote startup completed at: %date% %time%
echo [INFO] Docker Engine v28.1.1 with enhanced configuration is active
echo [INFO] All services are running with optimized settings
echo.
echo [HELP] Useful commands:
echo   Check status: docker-compose ps
echo   View logs: docker-compose logs -f
echo   Stop services: docker-compose down
echo   Restart services: docker-compose restart
echo.
echo [INFO] For manual startup, use: .\scripts\start-docker-elevated.bat
echo [INFO] For complete setup, use: .\start-blackcnote-complete.ps1
echo.
"@
        
        Set-Content -Path $startupScript -Value $scriptContent -Encoding ASCII
        Write-Success "Enhanced startup script created: $startupScript"
        
        # Also create a PowerShell version for better compatibility
        $psStartupScript = "$env:USERPROFILE\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\start-blackcnote-docker.ps1"
        
        $psScriptContent = @"
# BlackCnote Enhanced Docker Startup Script (PowerShell)
# This script starts Docker Desktop with the new enhanced configuration
# Compatible with BlackCnote Docker Engine v28.1.1

param([switch]`$Quiet)

# Function to write colored output
function Write-ColorOutput {
    param([string]`$Message, [string]`$Color = "White")
    if (-not `$Quiet) { Write-Host `$Message -ForegroundColor `$Color }
}

# Set project root
`$projectRoot = "$projectRoot"
Set-Location `$projectRoot

Write-ColorOutput "=== BlackCnote Enhanced Docker Startup ===" "Cyan"
Write-ColorOutput "Starting at: `$(Get-Date)" "White"
Write-ColorOutput "Project root: `$projectRoot" "White"
Write-ColorOutput ""

# Check if Docker Desktop is running
`$dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
if (-not `$dockerProcess) {
    Write-ColorOutput "Starting Docker Desktop with enhanced configuration..." "Yellow"
    Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -ArgumentList "--verbose" -WindowStyle Minimized
    Start-Sleep -Seconds 5
}

# Wait for Docker to be ready
Write-ColorOutput "Waiting for Docker daemon..." "Yellow"
`$dockerReady = `$false
for (`$i = 1; `$i -le 45; `$i++) {
    try {
        `$null = docker info 2>`$null
        if (`$LASTEXITCODE -eq 0) { `$dockerReady = `$true; break }
    } catch { }
    Start-Sleep -Seconds 2
}

if (-not `$dockerReady) {
    Write-ColorOutput "Docker daemon failed to start" "Red"
    exit 1
}

Write-ColorOutput "Docker daemon is ready!" "Green"

# Start BlackCnote services
Write-ColorOutput "Starting BlackCnote services..." "Yellow"
docker-compose down --remove-orphans 2>`$null
docker-compose up -d --build

if (`$LASTEXITCODE -ne 0) {
    Write-ColorOutput "Failed to start BlackCnote services" "Red"
    exit 1
}

# Wait for services
Start-Sleep -Seconds 15

# Check service status
Write-ColorOutput "Service Status:" "Cyan"
docker-compose ps

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
"@
        
        Set-Content -Path $psStartupScript -Value $psScriptContent -Encoding UTF8
        Write-Success "PowerShell startup script created: $psStartupScript"
        
    } catch {
        Write-Error "Failed to create startup script: $($_.Exception.Message)"
        return $false
    }
    
    return $true
}

# Main script
Write-Info "BlackCnote Docker Privileges Setup Script"
Write-Info "============================================="
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

# Main execution
$success = $true

if ($All -or $FixRegistry) {
    $success = $success -and (Fix-DockerRegistry)
}

if ($All -or $FixPermissions) {
    $success = $success -and (Fix-DockerPermissions)
}

if ($All -or $CreateShortcut) {
    $success = $success -and (Create-DockerShortcut)
}

if ($All -or $CreateTask) {
    $success = $success -and (Create-DockerTask)
}

if ($All) {
    $success = $success -and (Copy-DockerDaemonConfig)
    $success = $success -and (Create-StartupScript)
}

Write-Output ""

if ($success) {
    Write-Success "Docker privileges setup completed successfully!"
    Write-Output ""
    
    if ($All) {
        Write-Info "What was configured:"
        Write-Output "   Registry settings for elevated privileges"
        Write-Output "   File permissions for Docker directories"
        Write-Output "   Desktop shortcut with elevated privileges"
        Write-Output "   Task Scheduler task for automatic startup"
        Write-Output "   Docker daemon configuration"
        Write-Output "   Startup script for automatic service launch"
        Write-Output ""
        
        Write-Info "Next Steps:"
        Write-Output "   1. Restart your computer"
        Write-Output "   2. Docker Desktop will start automatically"
        Write-Output "   3. BlackCnote services will start automatically"
        Write-Output "   4. Access WordPress at: http://localhost:8888"
        Write-Output ""
        
        Write-Info "Manual Commands:"
        Write-Output "   Start Docker: Right-click 'BlackCnote Docker' shortcut"
        Write-Output "   Check status: docker info"
        Write-Output "   View services: docker-compose ps"
        Write-Output "   View logs: docker-compose logs -f"
        Write-Output ""
    }
} else {
    Write-Error "Docker privileges setup failed!"
    Write-Output "Please check the error messages above and try again."
}

Write-Info "For more information, see:"
Write-Output "   DOCKER-PRIVILEGES-FIX.md"
Write-Output "   DOCKER-SETUP.md"
Write-Output "   BLACKCNOTE-CANONICAL-PATHS.md" 