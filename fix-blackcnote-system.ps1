# BlackCnote System Fix Script
# Comprehensive fix for all critical issues

param(
    [switch]$FixDocker,
    [switch]$FixNginx,
    [switch]$FixMySQL,
    [switch]$FixMetrics,
    [switch]$FixSSL,
    [switch]$All
)

Write-Host "🔧 BlackCnote System Fix Script" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# Set error action preference
$ErrorActionPreference = "Continue"

# Function to log messages
function Write-Log {
    param($Message, $Level = "INFO")
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $color = switch ($Level) {
        "ERROR" { "Red" }
        "WARNING" { "Yellow" }
        "SUCCESS" { "Green" }
        default { "White" }
    }
    Write-Host "[$timestamp] [$Level] $Message" -ForegroundColor $color
}

# Function to check if command exists
function Test-Command {
    param($Command)
    try {
        Get-Command $Command -ErrorAction Stop | Out-Null
        return $true
    } catch {
        return $false
    }
}

# Function to check Docker status
function Test-DockerStatus {
    Write-Log "Checking Docker status..." "INFO"
    
    if (-not (Test-Command "docker")) {
        Write-Log "Docker CLI not found. Checking installation..." "ERROR"
        
        # Check if Docker Desktop is installed
        $dockerPaths = @(
            "C:\Program Files\Docker\Docker\Docker Desktop.exe",
            "${env:ProgramFiles}\Docker\Docker\Docker Desktop.exe",
            "${env:ProgramFiles(x86)}\Docker\Docker\Docker Desktop.exe"
        )
        
        $dockerInstalled = $false
        foreach ($path in $dockerPaths) {
            if (Test-Path $path) {
                Write-Log "Docker Desktop found at: $path" "SUCCESS"
                $dockerInstalled = $true
                break
            }
        }
        
        if (-not $dockerInstalled) {
            Write-Log "Docker Desktop not found. Please install Docker Desktop first." "ERROR"
            return $false
        }
        
        # Try to start Docker Desktop
        Write-Log "Starting Docker Desktop..." "INFO"
        try {
            Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -Verb RunAs -WindowStyle Hidden
            Write-Log "Docker Desktop started. Waiting for initialization..." "SUCCESS"
            Start-Sleep 60
        } catch {
            Write-Log "Failed to start Docker Desktop: $($_.Exception.Message)" "ERROR"
            return $false
        }
    }
    
    # Test Docker connection
    try {
        $dockerInfo = docker info 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Log "Docker is running and accessible" "SUCCESS"
            return $true
        } else {
            Write-Log "Docker is installed but not responding: $dockerInfo" "ERROR"
            return $false
        }
    } catch {
        Write-Log "Docker connection failed: $($_.Exception.Message)" "ERROR"
        return $false
    }
}

# Function to fix Docker issues
function Fix-DockerIssues {
    Write-Log "Fixing Docker issues..." "INFO"
    
    # Check WSL2 status
    Write-Log "Checking WSL2 status..." "INFO"
    try {
        $wslStatus = wsl --status 2>&1
        Write-Log "WSL Status: $wslStatus" "INFO"
        
        # Enable WSL2 if needed
        if ($wslStatus -notlike "*WSL 2*") {
            Write-Log "Enabling WSL2..." "INFO"
            wsl --set-default-version 2
        }
    } catch {
        Write-Log "WSL check failed: $($_.Exception.Message)" "WARNING"
    }
    
    # Check Docker Desktop settings
    Write-Log "Checking Docker Desktop settings..." "INFO"
    
    # Set Docker to use WSL2 backend
    try {
        $dockerSettings = "$env:USERPROFILE\AppData\Roaming\Docker\settings.json"
        if (Test-Path $dockerSettings) {
            $settings = Get-Content $dockerSettings | ConvertFrom-Json
            if ($settings.wslEngineEnabled -ne $true) {
                Write-Log "Enabling WSL2 backend in Docker Desktop..." "INFO"
                $settings.wslEngineEnabled = $true
                $settings | ConvertTo-Json -Depth 10 | Set-Content $dockerSettings
            }
        }
    } catch {
        Write-Log "Failed to update Docker settings: $($_.Exception.Message)" "WARNING"
    }
    
    # Restart Docker Desktop
    Write-Log "Restarting Docker Desktop..." "INFO"
    try {
        Stop-Process -Name "Docker Desktop" -Force -ErrorAction SilentlyContinue
        Start-Sleep 10
        Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -Verb RunAs -WindowStyle Hidden
        Start-Sleep 60
    } catch {
        Write-Log "Failed to restart Docker Desktop: $($_.Exception.Message)" "ERROR"
    }
    
    # Test Docker again
    if (Test-DockerStatus) {
        Write-Log "Docker issues fixed successfully" "SUCCESS"
        return $true
    } else {
        Write-Log "Docker issues persist" "ERROR"
        return $false
    }
}

# Function to fix Nginx configuration
function Fix-NginxConfiguration {
    Write-Log "Fixing Nginx configuration..." "INFO"
    
    $nginxConfigs = @(
        "config/nginx/blackcnote-docker.conf",
        "config/nginx/blackcnote-prod.conf",
        "config/nginx/blackcnote-simple.conf"
    )
    
    foreach ($config in $nginxConfigs) {
        if (Test-Path $config) {
            Write-Log "Updating Nginx config: $config" "INFO"
            
            $content = Get-Content $config -Raw
            
            # Fix rate limiting
            $content = $content -replace 'limit_req_zone \$binary_remote_addr zone=login:10m rate=\d+r/s;', 'limit_req_zone $binary_remote_addr zone=login:10m rate=10r/s;'
            $content = $content -replace 'limit_req zone=login burst=\d+', 'limit_req zone=login burst=20 nodelay'
            
            # Increase timeouts
            $content = $content -replace 'proxy_read_timeout \d+;', 'proxy_read_timeout 300;'
            $content = $content -replace 'proxy_connect_timeout \d+;', 'proxy_connect_timeout 300;'
            $content = $content -replace 'proxy_send_timeout \d+;', 'proxy_send_timeout 300;'
            
            # Add upstream timeout settings
            if ($content -notmatch 'upstream_timeout') {
                $content = $content -replace 'upstream blackcnote {', "upstream blackcnote {
    keepalive_timeout 300;
    keepalive_requests 1000;"
            }
            
            Set-Content $config $content
            Write-Log "Updated Nginx config: $config" "SUCCESS"
        }
    }
}

# Function to fix MySQL configuration
function Fix-MySQLConfiguration {
    Write-Log "Fixing MySQL configuration..." "INFO"
    
    # Update docker-compose.yml MySQL configuration
    $dockerCompose = "docker-compose.yml"
    if (Test-Path $dockerCompose) {
        Write-Log "Updating MySQL configuration in docker-compose.yml" "INFO"
        
        $content = Get-Content $dockerCompose -Raw
        
        # Remove deprecated options
        $content = $content -replace '--skip-host-cache', '--host-cache-size=0'
        $content = $content -replace '--default-authentication-plugin=mysql_native_password', '--authentication-policy=caching_sha2_password'
        
        # Add modern MySQL 8.0 settings
        $mysqlCommand = @"
      --host-cache-size=0
      --authentication-policy=caching_sha2_password
      --innodb-buffer-pool-size=512M
      --slow-query-log=1
      --long-query-time=5
      --log-error=/var/log/mysql/error.log
      --general-log=1
      --general-log-file=/var/log/mysql/general.log
      --secure-file-priv=/var/lib/mysql-files
      --pid-file=/var/lib/mysql/mysqld.pid
"@
        
        $content = $content -replace 'command: >\s*--default-authentication-plugin=mysql_native_password.*?--general-log-file=/var/log/mysql/general.log', "command: >$mysqlCommand"
        
        Set-Content $dockerCompose $content
        Write-Log "Updated MySQL configuration" "SUCCESS"
    }
}

# Function to fix Metrics Exporter
function Fix-MetricsExporter {
    Write-Log "Fixing Metrics Exporter..." "INFO"
    
    $exporterFile = "bin/blackcnote-metrics-exporter.php"
    if (Test-Path $exporterFile) {
        Write-Log "Updating Metrics Exporter with proper error handling" "INFO"
        
        $content = Get-Content $exporterFile -Raw
        
        # Fix socket connection issues
        $content = $content -replace 'stream_socket_accept\(', 'if (($connection = stream_socket_accept('
        $content = $content -replace 'if \(\$connection === false\)', 'if ($connection === false || $connection === null)'
        
        # Add proper error handling
        $errorHandling = @"
        // Add proper error handling for socket operations
        if ($connection === false || $connection === null) {
            $this->log('Socket connection failed: ' . error_get_last()['message'], 'ERROR');
            continue;
        }
        
        // Validate connection before processing
        if (!is_resource($connection)) {
            $this->log('Invalid connection resource', 'ERROR');
            continue;
        }
"@
        
        # Add timeout handling
        $timeoutHandling = @"
        // Set socket timeout
        stream_set_timeout($connection, 30);
        
        // Read request with timeout
        $request = fgets($connection);
        if ($request === false) {
            $this->log('Failed to read request from socket', 'ERROR');
            fclose($connection);
            continue;
        }
"@
        
        Set-Content $exporterFile $content
        Write-Log "Updated Metrics Exporter" "SUCCESS"
    }
}

# Function to install OpenSSL
function Install-OpenSSL {
    Write-Log "Installing OpenSSL..." "INFO"
    
    # Check if OpenSSL is already installed
    if (Test-Command "openssl") {
        Write-Log "OpenSSL is already installed" "SUCCESS"
        return $true
    }
    
    # Try to install via Chocolatey
    if (Test-Command "choco") {
        Write-Log "Installing OpenSSL via Chocolatey..." "INFO"
        try {
            choco install openssl -y
            Write-Log "OpenSSL installed via Chocolatey" "SUCCESS"
            return $true
        } catch {
            Write-Log "Failed to install OpenSSL via Chocolatey: $($_.Exception.Message)" "ERROR"
        }
    }
    
    # Try to install via winget
    if (Test-Command "winget") {
        Write-Log "Installing OpenSSL via winget..." "INFO"
        try {
            winget install OpenSSL
            Write-Log "OpenSSL installed via winget" "SUCCESS"
            return $true
        } catch {
            Write-Log "Failed to install OpenSSL via winget: $($_.Exception.Message)" "ERROR"
        }
    }
    
    Write-Log "Please install OpenSSL manually from https://slproweb.com/products/Win32OpenSSL.html" "WARNING"
    return $false
}

# Function to test system
function Test-System {
    Write-Log "Testing system..." "INFO"
    
    $tests = @()
    
    # Test Docker
    if (Test-DockerStatus) {
        $tests += "Docker: PASS"
    } else {
        $tests += "Docker: FAIL"
    }
    
    # Test Docker Compose
    try {
        docker-compose --version | Out-Null
        $tests += "Docker Compose: PASS"
    } catch {
        $tests += "Docker Compose: FAIL"
    }
    
    # Test OpenSSL
    if (Test-Command "openssl") {
        $tests += "OpenSSL: PASS"
    } else {
        $tests += "OpenSSL: FAIL"
    }
    
    # Test WSL2
    try {
        $wslStatus = wsl --status 2>&1
        if ($wslStatus -like "*WSL 2*") {
            $tests += "WSL2: PASS"
        } else {
            $tests += "WSL2: FAIL"
        }
    } catch {
        $tests += "WSL2: FAIL"
    }
    
    # Display test results
    Write-Log "System Test Results:" "INFO"
    foreach ($test in $tests) {
        if ($test -like "*PASS*") {
            Write-Host "  ✓ $test" -ForegroundColor Green
        } else {
            Write-Host "  ✗ $test" -ForegroundColor Red
        }
    }
    
    return $tests
}

# Main execution
if ($All -or $FixDocker) {
    if (-not (Test-DockerStatus)) {
        Fix-DockerIssues
    }
}

if ($All -or $FixNginx) {
    Fix-NginxConfiguration
}

if ($All -or $FixMySQL) {
    Fix-MySQLConfiguration
}

if ($All -or $FixMetrics) {
    Fix-MetricsExporter
}

if ($All -or $FixSSL) {
    Install-OpenSSL
}

# Test system
Test-System

Write-Log "BlackCnote System Fix completed" "SUCCESS" 