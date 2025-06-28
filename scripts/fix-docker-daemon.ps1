# Fix Docker Daemon Configuration for BlackCnote 
# Run as Administrator 
 
param( 
    [switch]$RestartDocker = $true, 
    [switch]$TestAPI = $true 
) 
 
Write-Host "Fixing Docker Daemon Configuration for BlackCnote..." -ForegroundColor Cyan 
 
# Check if running as Administrator 
$currentUser = [Security.Principal.WindowsIdentity]::GetCurrent() 
$principal = New-Object Security.Principal.WindowsPrincipal($currentUser) 
if (-not $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) { 
    Write-Host "This script must be run as Administrator!" -ForegroundColor Red 
    exit 1 
} 
 
# Stop Docker Desktop if running 
Write-Host "Stopping Docker Desktop..." -ForegroundColor Yellow 
try { 
    Stop-Process -Name "Docker Desktop" -Force -ErrorAction SilentlyContinue 
    Start-Sleep -Seconds 5 
    Write-Host "Docker Desktop stopped" -ForegroundColor Green 
} catch { 
    Write-Host "Docker Desktop was not running or could not be stopped" -ForegroundColor Yellow 
} 
 
# Create proper Windows-compatible daemon configuration 
$daemonConfig = @{ 
    "builder" = @{ 
        "gc" = @{ 
            "defaultKeepStorage" = "50GB" 
            "enabled" = $true 
        } 
    } 
    "experimental" = $true 
    "features" = @{ 
        "buildkit" = $true 
    } 
    "registry-mirrors" = @() 
    "insecure-registries" = @() 
    "debug" = $false 
    "log-driver" = "json-file" 
    "log-opts" = @{ 
        "max-size" = "20m" 
        "max-file" = "5" 
    } 
    "max-concurrent-downloads" = 20 
    "max-concurrent-uploads" = 10 
    "max-download-attempts" = 10 
    "shutdown-timeout" = 60 
    "live-restore" = $true 
    "userland-proxy" = $true 
    "ip-forward" = $true 
    "ip-masq" = $true 
    "iptables" = $true 
    "ip6tables" = $true 
    "default-address-pools" = @( 
        @{ 
            "base" = "172.17.0.0/12" 
            "size" = 24 
        }, 
        @{ 
            "base" = "192.168.0.0/16" 
            "size" = 24 
        } 
    ) 
    "default-network-opts" = @{ 
        "com.docker.network.driver.mtu" = "1500" 
    } 
    "default-runtime" = "runc" 
    "runtimes" = @{ 
        "runc" = @{ 
            "path" = "runc" 
        } 
    } 
    "init" = $true 
    "seccomp-profile" = "builtin" 
    "no-new-privileges" = $false 
    "default-ulimits" = @{ 
        "nofile" = @{ 
            "Hard" = 100000 
            "Name" = "nofile" 
            "Soft" = 100000 
        } 
    } 
    "default-shm-size" = "2G" 
    "dns" = @("8.8.8.8", "8.8.4.4", "1.1.1.1") 
    "dns-opts" = @("timeout:5", "attempts:5", "rotate") 
    "dns-search" = @() 
    "labels" = @( 
        "com.blackcnote.project=blackcnote", 
        "com.blackcnote.environment=development", 
        "com.blackcnote.ml.enabled=true" 
    ) 
    "metrics-addr" = "127.0.0.1:9323" 
    "default-cgroupns-mode" = "private" 
    "default-ipc-mode" = "private" 
    "tls" = $false 
    "tlsverify" = $false 
    "selinux-enabled" = $false 
    "icc" = $true 
    "raw-logs" = $false 
    "allow-nondistributable-artifacts" = $false 
    "disable-legacy-registry" = $true 
    "log-level" = "info" 
} 
 
# Ensure Docker config directory exists 
$dockerConfigDir = "C:\ProgramData\Docker\config" 
if (-not (Test-Path $dockerConfigDir)) { 
    New-Item -ItemType Directory -Path $dockerConfigDir -Force | Out-Null 
    Write-Host "Created Docker config directory" -ForegroundColor Green 
} 
 
# Write the fixed configuration 
$daemonPath = "$dockerConfigDir\daemon.json" 
$daemonConfig | ConvertTo-Json -Depth 10 | Set-Content -Path $daemonPath -Encoding UTF8 
Write-Host "Updated Docker daemon configuration" -ForegroundColor Green 
 
# Set proper permissions 
try { 
    icacls $daemonPath /grant "Administrators:(F)" /T 
    icacls $daemonPath /grant "Users:(R)" /T 
    Write-Host "Set proper file permissions" -ForegroundColor Green 
} catch { 
    Write-Host "Could not set file permissions" -ForegroundColor Yellow 
} 
 
# Restart Docker Desktop if requested 
if ($RestartDocker) { 
    Write-Host "Starting Docker Desktop..." -ForegroundColor Yellow 
    try { 
        Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized 
        Write-Host "Docker Desktop started" -ForegroundColor Green 
        Start-Sleep -Seconds 10 
    } catch { 
        Write-Host "Failed to start Docker Desktop" -ForegroundColor Red 
    } 
} 
 
# Test Docker API if requested 
if ($TestAPI) { 
    Write-Host "Testing Docker API..." -ForegroundColor Cyan 
    Start-Sleep -Seconds 5 
    try { 
        $dockerVersion = docker version 2>&1 
        if ($LASTEXITCODE -eq 0) { 
            Write-Host "Docker API is working!" -ForegroundColor Green 
        } else { 
            Write-Host "Docker API test failed" -ForegroundColor Red 
        } 
    } catch { 
        Write-Host "Docker API test failed with exception" -ForegroundColor Red 
    } 
} 
 
Write-Host "Docker daemon configuration fix completed!" -ForegroundColor Green 
