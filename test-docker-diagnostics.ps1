# BlackCnote Docker Diagnostics System
# Enhanced Docker testing and monitoring integrated with BlackCnote Debug System

param([switch]$Verbose, [switch]$NoLog)

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
        component = 'Docker_Diagnostics'
        context = $context
    }
    
    # Convert to JSON for structured logging
    $jsonLog = $logEntry | ConvertTo-Json -Compress
    
    # Determine log file path based on BlackCnote structure
    $basePath = Split-Path -Parent $PSScriptRoot
    $logFile = Join-Path $basePath 'logs\docker-diagnostics.log'
    $blackcnoteLogFile = Join-Path $basePath 'blackcnote\wp-content\logs\blackcnote-debug.log'
    
    # Ensure logs directory exists
    $logsDir = Split-Path -Parent $logFile
    if (!(Test-Path $logsDir)) {
        New-Item -ItemType Directory -Path $logsDir -Force | Out-Null
    }
    
    # Write to Docker diagnostics log
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

# Test Docker Version Command
function Test-DockerVersion {
    Write-BlackCnoteLog 'Testing docker version command' 'INFO'
    
    $result = @{
        success = $false
        output = ''
        error = ''
        exit_code = -1
        timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
    }
    
    try {
        Write-Info 'Running: docker version'
        
        # Capture both stdout and stderr
        $process = Start-Process -FilePath 'docker' -ArgumentList 'version' -Wait -PassThru -NoNewWindow -RedirectStandardOutput 'temp_stdout.txt' -RedirectStandardError 'temp_stderr.txt'
        
        $result.exit_code = $process.ExitCode
        $result.output = if (Test-Path 'temp_stdout.txt') { Get-Content 'temp_stdout.txt' -Raw } else { '' }
        $result.error = if (Test-Path 'temp_stderr.txt') { Get-Content 'temp_stderr.txt' -Raw } else { '' }
        
        # Clean up temp files
        if (Test-Path 'temp_stdout.txt') { Remove-Item 'temp_stdout.txt' -Force }
        if (Test-Path 'temp_stderr.txt') { Remove-Item 'temp_stderr.txt' -Force }
        
        $result.success = ($process.ExitCode -eq 0)
        
        if ($result.success) {
            Write-Success '✓ Docker version command successful'
            Write-BlackCnoteLog 'Docker version test passed' 'INFO' @{ exit_code = $result.exit_code }
        } else {
            Write-Error '✗ Docker version command failed (Exit Code: ' + $result.exit_code + ')'
            Write-BlackCnoteLog 'Docker version test failed' 'ERROR' @{ 
                exit_code = $result.exit_code
                error = $result.error
            }
        }
        
    } catch {
        $result.error = $_.Exception.Message
        Write-Error '✗ Exception during docker version test: ' + $_.Exception.Message
        Write-BlackCnoteLog 'Docker version test exception' 'ERROR' @{ exception = $_.Exception.Message }
    }
    
    return $result
}

# Test Docker Info Command
function Test-DockerInfo {
    Write-BlackCnoteLog 'Testing docker info command' 'INFO'
    
    $result = @{
        success = $false
        output = ''
        error = ''
        exit_code = -1
        timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
    }
    
    try {
        Write-Info 'Running: docker info'
        
        # Capture both stdout and stderr
        $process = Start-Process -FilePath 'docker' -ArgumentList 'info' -Wait -PassThru -NoNewWindow -RedirectStandardOutput 'temp_stdout.txt' -RedirectStandardError 'temp_stderr.txt'
        
        $result.exit_code = $process.ExitCode
        $result.output = if (Test-Path 'temp_stdout.txt') { Get-Content 'temp_stdout.txt' -Raw } else { '' }
        $result.error = if (Test-Path 'temp_stderr.txt') { Get-Content 'temp_stderr.txt' -Raw } else { '' }
        
        # Clean up temp files
        if (Test-Path 'temp_stdout.txt') { Remove-Item 'temp_stdout.txt' -Force }
        if (Test-Path 'temp_stderr.txt') { Remove-Item 'temp_stderr.txt' -Force }
        
        $result.success = ($process.ExitCode -eq 0)
        
        if ($result.success) {
            Write-Success '✓ Docker info command successful'
            Write-BlackCnoteLog 'Docker info test passed' 'INFO' @{ exit_code = $result.exit_code }
        } else {
            Write-Error '✗ Docker info command failed (Exit Code: ' + $result.exit_code + ')'
            Write-BlackCnoteLog 'Docker info test failed' 'ERROR' @{ 
                exit_code = $result.exit_code
                error = $result.error
            }
        }
        
    } catch {
        $result.error = $_.Exception.Message
        Write-Error '✗ Exception during docker info test: ' + $_.Exception.Message
        Write-BlackCnoteLog 'Docker info test exception' 'ERROR' @{ exception = $_.Exception.Message }
    }
    
    return $result
}

# Check Docker Processes
function Test-DockerProcesses {
    Write-BlackCnoteLog 'Checking Docker processes' 'INFO'
    
    $result = @{
        docker_desktop_running = $false
        docker_backend_running = $false
        processes = @()
        timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
    }
    
    try {
        $dockerProcesses = Get-Process -Name '*Docker*', '*docker*' -ErrorAction SilentlyContinue
        
        foreach ($process in $dockerProcesses) {
            $result.processes += @{
                name = $process.ProcessName
                id = $process.Id
                cpu = $process.CPU
                memory = $process.WorkingSet64
                start_time = $process.StartTime
            }
            
            if ($process.ProcessName -like '*Docker Desktop*') {
                $result.docker_desktop_running = $true
            }
            if ($process.ProcessName -like '*com.docker.backend*') {
                $result.docker_backend_running = $true
            }
        }
        
        if ($result.docker_desktop_running) {
            Write-Success '✓ Docker Desktop is running'
        } else {
            Write-Warning '⚠ Docker Desktop is not running'
        }
        
        if ($result.docker_backend_running) {
            Write-Success '✓ Docker backend is running'
        } else {
            Write-Warning '⚠ Docker backend is not running'
        }
        
        Write-BlackCnoteLog 'Docker processes check completed' 'INFO' @{
            docker_desktop_running = $result.docker_desktop_running
            docker_backend_running = $result.docker_backend_running
            process_count = $result.processes.Count
        }
        
    } catch {
        Write-Error '✗ Exception checking Docker processes: ' + $_.Exception.Message
        Write-BlackCnoteLog 'Docker processes check exception' 'ERROR' @{ exception = $_.Exception.Message }
    }
    
    return $result
}

# Main execution
try {
    Write-BlackCnoteLog 'Starting comprehensive Docker diagnostics' 'SYSTEM' @{
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
    Write-Info 'BlackCnote Docker Diagnostics System'
    Write-Info '=========================================='
    Write-Info 'Timestamp: ' + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
    Write-Info 'Environment: ' + $env:OS
    Write-Info 'PowerShell: ' + $PSVersionTable.PSVersion.ToString()
    Write-Info 'Execution Policy: ' + (Get-ExecutionPolicy)
    Write-Info ''
    
    # Run all tests
    $versionResult = Test-DockerVersion
    $infoResult = Test-DockerInfo
    $processResult = Test-DockerProcesses
    
    # Display results
    Write-Info ''
    Write-Info '=========================================='
    Write-Info 'RESULTS SUMMARY'
    Write-Info '=========================================='
    Write-Info 'Docker Version: ' + (if ($versionResult.success) { '✓ PASSED' } else { '✗ FAILED' })
    Write-Info 'Docker Info: ' + (if ($infoResult.success) { '✓ PASSED' } else { '✗ FAILED' })
    Write-Info 'Docker Desktop: ' + (if ($processResult.docker_desktop_running) { '✓ Running' } else { '✗ Not Running' })
    Write-Info 'Docker Backend: ' + (if ($processResult.docker_backend_running) { '✓ Running' } else { '✗ Not Running' })
    
    Write-Info ''
    Write-Info 'Log files:'
    Write-Info '  Docker Diagnostics: ' + (Join-Path (Split-Path -Parent $PSScriptRoot) 'logs\docker-diagnostics.log')
    Write-Info '  BlackCnote Debug: ' + (Join-Path (Split-Path -Parent $PSScriptRoot) 'blackcnote\wp-content\logs\blackcnote-debug.log')
    
    Write-BlackCnoteLog 'Docker diagnostics completed' 'SYSTEM' @{
        version_success = $versionResult.success
        info_success = $infoResult.success
        docker_desktop_running = $processResult.docker_desktop_running
        docker_backend_running = $processResult.docker_backend_running
    }
    
    # Return exit code based on results
    if ($versionResult.success -and $infoResult.success -and $processResult.docker_desktop_running) {
        exit 0
    } elseif ($versionResult.success -or $infoResult.success) {
        exit 1
    } else {
        exit 2
    }
    
} catch {
    Write-Error 'Critical error in Docker diagnostics: ' + $_.Exception.Message
    Write-BlackCnoteLog 'Critical error in Docker diagnostics' 'ERROR' @{ exception = $_.Exception.Message }
    exit 4
}
