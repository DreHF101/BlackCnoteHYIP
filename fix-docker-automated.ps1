# BlackCnote Docker Automated Fix and Diagnostics
# Comprehensive Docker troubleshooting and testing script

param([switch]$Verbose, [switch]$NoLog, [switch]$Force)

# Set error action preference
$ErrorActionPreference = 'Continue'

# Color functions
function Write-Success { Write-Host $args -ForegroundColor Green }
function Write-Warning { Write-Host $args -ForegroundColor Yellow }
function Write-Error { Write-Host $args -ForegroundColor Red }
function Write-Info { Write-Host $args -ForegroundColor Cyan }

# BlackCnote Debug System Integration
function Write-BlackCnoteLog($message, $level = 'INFO', $context = @{}) {
    if ($NoLog) { return }
    
    $timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
    $logEntry = @{
        timestamp = $timestamp
        level = $level
        message = $message
        component = 'Docker_Automated_Fix'
        context = $context
    }
    
    # Convert to JSON for structured logging
    $jsonLog = $logEntry | ConvertTo-Json -Compress
    
    # Determine log file path based on BlackCnote structure
    $basePath = Split-Path -Parent $PSScriptRoot
    $logFile = Join-Path $basePath 'logs\docker-automated-fix.log'
    $blackcnoteLogFile = Join-Path $basePath 'blackcnote\wp-content\logs\blackcnote-debug.log'
    
    # Ensure logs directory exists
    $logsDir = Split-Path -Parent $logFile
    if (!(Test-Path $logsDir)) {
        New-Item -ItemType Directory -Path $logsDir -Force | Out-Null
    }
    
    # Write to Docker automated fix log
    Add-Content -Path $logFile -Value $jsonLog -Encoding UTF8
    
    # Also write to BlackCnote debug log if it exists
    if (Test-Path $blackcnoteLogFile) {
        Add-Content -Path $blackcnoteLogFile -Value $jsonLog -Encoding UTF8
    }
    
    # Console output based on log level
    switch ($level) {
        'ERROR' { Write-Error $message }
        'WARNING' { Write-Warning $message }
        'INFO' { Write-Info $message }
        default { Write-Output $message }
    }
}

# Test Docker CLI availability
function Test-DockerCLI {
    Write-BlackCnoteLog 'Testing Docker CLI availability' 'INFO'
    
    $dockerPath = "C:\Program Files\Docker\Docker\resources\bin\docker.exe"
    
    if (!(Test-Path $dockerPath)) {
        Write-Error "✗ Docker CLI not found at: $dockerPath"
        Write-BlackCnoteLog 'Docker CLI not found' 'ERROR' @{ path = $dockerPath }
        return $false
    }
    
    try {
        $version = & $dockerPath --version 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Success "✓ Docker CLI found: $version"
            Write-BlackCnoteLog 'Docker CLI found' 'INFO' @{ version = $version }
            return $true
        } else {
            Write-Error "✗ Docker CLI test failed: $version"
            Write-BlackCnoteLog 'Docker CLI test failed' 'ERROR' @{ error = $version }
            return $false
        }
    } catch {
        Write-Error "✗ Exception testing Docker CLI: $($_.Exception.Message)"
        Write-BlackCnoteLog 'Docker CLI test exception' 'ERROR' @{ exception = $_.Exception.Message }
        return $false
    }
}

# Check WSL2 status
function Test-WSL2Status {
    Write-BlackCnoteLog 'Checking WSL2 status' 'INFO'
    
    try {
        $wslList = wsl --list --verbose 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Info "WSL2 Distros:"
            Write-Output $wslList
            
            $dockerDesktopRunning = $wslList -match "docker-desktop.*Running"
            if ($dockerDesktopRunning) {
                Write-Success "✓ docker-desktop WSL2 distro is running"
                Write-BlackCnoteLog 'docker-desktop WSL2 distro is running' 'INFO'
                return $true
            } else {
                Write-Warning "⚠ docker-desktop WSL2 distro is not running"
                Write-BlackCnoteLog 'docker-desktop WSL2 distro is not running' 'WARNING'
                return $false
            }
        } else {
            Write-Error "✗ Failed to get WSL2 status: $wslList"
            Write-BlackCnoteLog 'Failed to get WSL2 status' 'ERROR' @{ error = $wslList }
            return $false
        }
    } catch {
        Write-Error "✗ Exception checking WSL2 status: $($_.Exception.Message)"
        Write-BlackCnoteLog 'WSL2 status check exception' 'ERROR' @{ exception = $_.Exception.Message }
        return $false
    }
}

# Start docker-desktop WSL2 distro
function Start-DockerDesktopWSL {
    Write-BlackCnoteLog 'Starting docker-desktop WSL2 distro' 'INFO'
    
    try {
        Write-Info "Starting docker-desktop WSL2 distro..."
        
        # Start the distro in background
        $process = Start-Process -FilePath "wsl" -ArgumentList "-d", "docker-desktop" -PassThru -WindowStyle Hidden
        
        # Wait for it to start
        Start-Sleep -Seconds 10
        
        # Check if it's running
        $wslList = wsl --list --verbose 2>&1
        $dockerDesktopRunning = $wslList -match "docker-desktop.*Running"
        
        if ($dockerDesktopRunning) {
            Write-Success "✓ docker-desktop WSL2 distro started successfully"
            Write-BlackCnoteLog 'docker-desktop WSL2 distro started successfully' 'INFO'
            return $true
        } else {
            Write-Warning "⚠ docker-desktop WSL2 distro may still be starting..."
            Write-BlackCnoteLog 'docker-desktop WSL2 distro may still be starting' 'WARNING'
            
            # Wait a bit more and check again
            Start-Sleep -Seconds 15
            $wslList = wsl --list --verbose 2>&1
            $dockerDesktopRunning = $wslList -match "docker-desktop.*Running"
            
            if ($dockerDesktopRunning) {
                Write-Success "✓ docker-desktop WSL2 distro is now running"
                Write-BlackCnoteLog 'docker-desktop WSL2 distro is now running' 'INFO'
                return $true
            } else {
                Write-Error "✗ Failed to start docker-desktop WSL2 distro"
                Write-BlackCnoteLog 'Failed to start docker-desktop WSL2 distro' 'ERROR'
                return $false
            }
        }
    } catch {
        Write-Error "✗ Exception starting docker-desktop WSL2 distro: $($_.Exception.Message)"
        Write-BlackCnoteLog 'Exception starting docker-desktop WSL2 distro' 'ERROR' @{ exception = $_.Exception.Message }
        return $false
    }
}

# Test Docker daemon connection
function Test-DockerDaemon {
    Write-BlackCnoteLog 'Testing Docker daemon connection' 'INFO'
    
    $dockerPath = "C:\Program Files\Docker\Docker\resources\bin\docker.exe"
    
    try {
        Write-Info "Testing Docker daemon connection..."
        
        # Test with timeout
        $job = Start-Job -ScriptBlock {
            param($dockerPath)
            & $dockerPath version 2>&1
        } -ArgumentList $dockerPath
        
        # Wait for up to 30 seconds
        $result = Wait-Job -Job $job -Timeout 30
        
        if ($result) {
            $output = Receive-Job -Job $job
            Remove-Job -Job $job
            
            if ($output -match "error during connect") {
                Write-Error "✗ Docker daemon connection failed: $output"
                Write-BlackCnoteLog 'Docker daemon connection failed' 'ERROR' @{ error = $output }
                return $false
            } elseif ($output -match "Server:") {
                Write-Success "✓ Docker daemon connection successful"
                Write-BlackCnoteLog 'Docker daemon connection successful' 'INFO'
                return $true
            } else {
                Write-Warning "⚠ Unexpected Docker response: $output"
                Write-BlackCnoteLog 'Unexpected Docker response' 'WARNING' @{ output = $output }
                return $false
            }
        } else {
            Remove-Job -Job $job -Force
            Write-Error "✗ Docker daemon connection timed out"
            Write-BlackCnoteLog 'Docker daemon connection timed out' 'ERROR'
            return $false
        }
    } catch {
        Write-Error "✗ Exception testing Docker daemon: $($_.Exception.Message)"
        Write-BlackCnoteLog 'Exception testing Docker daemon' 'ERROR' @{ exception = $_.Exception.Message }
        return $false
    }
}

# Check Docker pipes
function Test-DockerPipes {
    Write-BlackCnoteLog 'Checking Docker pipes' 'INFO'
    
    try {
        $pipes = Get-ChildItem -Path '\\.\\pipe\\' -ErrorAction SilentlyContinue | Where-Object {$_.Name -like "*docker*"}
        
        if ($pipes) {
            Write-Success "✓ Found Docker pipes:"
            foreach ($pipe in $pipes) {
                Write-Info "  - $($pipe.Name)"
            }
            Write-BlackCnoteLog 'Found Docker pipes' 'INFO' @{ pipes = $pipes.Name }
            return $true
        } else {
            Write-Warning "⚠ No Docker pipes found"
            Write-BlackCnoteLog 'No Docker pipes found' 'WARNING'
            return $false
        }
    } catch {
        Write-Error "✗ Exception checking Docker pipes: $($_.Exception.Message)"
        Write-BlackCnoteLog 'Exception checking Docker pipes' 'ERROR' @{ exception = $_.Exception.Message }
        return $false
    }
}

# Restart Docker Desktop
function Restart-DockerDesktop {
    Write-BlackCnoteLog 'Restarting Docker Desktop' 'INFO'
    
    try {
        Write-Info "Stopping Docker Desktop..."
        
        # Stop Docker Desktop processes
        Stop-Process -Name "Docker Desktop" -Force -ErrorAction SilentlyContinue
        Stop-Process -Name "com.docker.backend" -Force -ErrorAction SilentlyContinue
        
        # Wait for processes to stop
        Start-Sleep -Seconds 5
        
        Write-Info "Starting Docker Desktop..."
        
        # Start Docker Desktop
        Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe"
        
        # Wait for startup
        Start-Sleep -Seconds 20
        
        Write-Success "✓ Docker Desktop restarted"
        Write-BlackCnoteLog 'Docker Desktop restarted' 'INFO'
        return $true
    } catch {
        Write-Error "✗ Exception restarting Docker Desktop: $($_.Exception.Message)"
        Write-BlackCnoteLog 'Exception restarting Docker Desktop' 'ERROR' @{ exception = $_.Exception.Message }
        return $false
    }
}

# Main execution
try {
    Write-BlackCnoteLog 'Starting automated Docker fix and diagnostics' 'SYSTEM' @{
        script_version = '1.0.0'
        environment = @{
            os = $env:OS
            powershell_version = $PSVersionTable.PSVersion.ToString()
            execution_policy = Get-ExecutionPolicy
            current_user = $env:USERNAME
            computer_name = $env:COMPUTERNAME
            timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
        }
    }
    
    Write-Info '=========================================='
    Write-Info 'BlackCnote Docker Automated Fix'
    Write-Info '=========================================='
    Write-Info 'Timestamp: ' + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
    Write-Info 'Environment: ' + $env:OS
    Write-Info 'PowerShell: ' + $PSVersionTable.PSVersion.ToString()
    Write-Info 'Execution Policy: ' + (Get-ExecutionPolicy)
    Write-Info ''
    
    # Step 1: Test Docker CLI
    Write-Info 'Step 1: Testing Docker CLI...'
    $cliWorking = Test-DockerCLI
    
    if (!$cliWorking) {
        Write-Error "Docker CLI is not working. Cannot proceed."
        exit 2
    }
    
    # Step 2: Check WSL2 status
    Write-Info ''
    Write-Info 'Step 2: Checking WSL2 status...'
    $wslRunning = Test-WSL2Status
    
    # Step 3: Start docker-desktop WSL2 if needed
    if (!$wslRunning) {
        Write-Info ''
        Write-Info 'Step 3: Starting docker-desktop WSL2 distro...'
        $wslStarted = Start-DockerDesktopWSL
        
        if (!$wslStarted -and $Force) {
            Write-Info ''
            Write-Info 'Step 3b: Force restarting Docker Desktop...'
            Restart-DockerDesktop
            Start-Sleep -Seconds 30
            $wslStarted = Start-DockerDesktopWSL
        }
    } else {
        $wslStarted = $true
    }
    
    # Step 4: Test Docker daemon
    Write-Info ''
    Write-Info 'Step 4: Testing Docker daemon connection...'
    $daemonWorking = Test-DockerDaemon
    
    # Step 5: Check Docker pipes
    Write-Info ''
    Write-Info 'Step 5: Checking Docker pipes...'
    $pipesExist = Test-DockerPipes
    
    # Display results
    Write-Info ''
    Write-Info '=========================================='
    Write-Info 'RESULTS SUMMARY'
    Write-Info '=========================================='
    Write-Info 'Docker CLI: ' + (if ($cliWorking) { '✓ Working' } else { '✗ Failed' })
    Write-Info 'WSL2 Status: ' + (if ($wslRunning -or $wslStarted) { '✓ Running' } else { '✗ Failed' })
    Write-Info 'Docker Daemon: ' + (if ($daemonWorking) { '✓ Connected' } else { '✗ Failed' })
    Write-Info 'Docker Pipes: ' + (if ($pipesExist) { '✓ Found' } else { '✗ Missing' })
    
    Write-Info ''
    Write-Info 'Log files:'
    Write-Info '  Docker Fix: ' + (Join-Path (Split-Path -Parent $PSScriptRoot) 'logs\docker-automated-fix.log')
    Write-Info '  BlackCnote Debug: ' + (Join-Path (Split-Path -Parent $PSScriptRoot) 'blackcnote\wp-content\logs\blackcnote-debug.log')
    
    Write-BlackCnoteLog 'Automated Docker fix completed' 'SYSTEM' @{
        cli_working = $cliWorking
        wsl_running = ($wslRunning -or $wslStarted)
        daemon_working = $daemonWorking
        pipes_exist = $pipesExist
    }
    
    # Return exit code based on results
    if ($cliWorking -and $daemonWorking) {
        Write-Success "✓ Docker is fully functional!"
        exit 0
    } elseif ($cliWorking) {
        Write-Warning "⚠ Docker CLI works but daemon connection failed"
        exit 1
    } else {
        Write-Error "✗ Docker CLI is not working"
        exit 2
    }
    
} catch {
    Write-Error 'Critical error in automated Docker fix: ' + $_.Exception.Message
    Write-BlackCnoteLog 'Critical error in automated Docker fix' 'ERROR' @{ exception = $_.Exception.Message }
    exit 4
} 