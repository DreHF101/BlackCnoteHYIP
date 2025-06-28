@echo off
REM BlackCnote Automated Docker Daemon Fix
REM This script ensures the PowerShell script is always correct and runs it

echo ========================================
echo   BlackCnote Automated Docker Fix
echo ========================================
echo.

REM Check if running as Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] This script must be run as Administrator!
    echo Please right-click this file and select "Run as administrator"
    pause
    exit /b 1
)

echo [INFO] Running with Administrator privileges
echo [INFO] Creating/updating PowerShell script...
echo.

REM Create the scripts directory if it doesn't exist
if not exist "scripts" mkdir scripts

REM Create a simpler PowerShell script that avoids complex syntax
echo # Fix Docker Daemon Configuration for BlackCnote > scripts\fix-docker-daemon.ps1
echo # Run as Administrator >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo param( >> scripts\fix-docker-daemon.ps1
echo     [switch]$RestartDocker = $true, >> scripts\fix-docker-daemon.ps1
echo     [switch]$TestAPI = $true >> scripts\fix-docker-daemon.ps1
echo ) >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo Write-Host "Fixing Docker Daemon Configuration for BlackCnote..." -ForegroundColor Cyan >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo # Check if running as Administrator >> scripts\fix-docker-daemon.ps1
echo $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent() >> scripts\fix-docker-daemon.ps1
echo $principal = New-Object Security.Principal.WindowsPrincipal($currentUser) >> scripts\fix-docker-daemon.ps1
echo if (-not $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) { >> scripts\fix-docker-daemon.ps1
echo     Write-Host "This script must be run as Administrator!" -ForegroundColor Red >> scripts\fix-docker-daemon.ps1
echo     exit 1 >> scripts\fix-docker-daemon.ps1
echo } >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo # Stop Docker Desktop if running >> scripts\fix-docker-daemon.ps1
echo Write-Host "Stopping Docker Desktop..." -ForegroundColor Yellow >> scripts\fix-docker-daemon.ps1
echo try { >> scripts\fix-docker-daemon.ps1
echo     Stop-Process -Name "Docker Desktop" -Force -ErrorAction SilentlyContinue >> scripts\fix-docker-daemon.ps1
echo     Start-Sleep -Seconds 5 >> scripts\fix-docker-daemon.ps1
echo     Write-Host "Docker Desktop stopped" -ForegroundColor Green >> scripts\fix-docker-daemon.ps1
echo } catch { >> scripts\fix-docker-daemon.ps1
echo     Write-Host "Docker Desktop was not running or could not be stopped" -ForegroundColor Yellow >> scripts\fix-docker-daemon.ps1
echo } >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo # Create proper Windows-compatible daemon configuration >> scripts\fix-docker-daemon.ps1
echo $daemonConfig = @{ >> scripts\fix-docker-daemon.ps1
echo     "builder" = @{ >> scripts\fix-docker-daemon.ps1
echo         "gc" = @{ >> scripts\fix-docker-daemon.ps1
echo             "defaultKeepStorage" = "50GB" >> scripts\fix-docker-daemon.ps1
echo             "enabled" = $true >> scripts\fix-docker-daemon.ps1
echo         } >> scripts\fix-docker-daemon.ps1
echo     } >> scripts\fix-docker-daemon.ps1
echo     "experimental" = $true >> scripts\fix-docker-daemon.ps1
echo     "features" = @{ >> scripts\fix-docker-daemon.ps1
echo         "buildkit" = $true >> scripts\fix-docker-daemon.ps1
echo     } >> scripts\fix-docker-daemon.ps1
echo     "registry-mirrors" = @() >> scripts\fix-docker-daemon.ps1
echo     "insecure-registries" = @() >> scripts\fix-docker-daemon.ps1
echo     "debug" = $false >> scripts\fix-docker-daemon.ps1
echo     "log-driver" = "json-file" >> scripts\fix-docker-daemon.ps1
echo     "log-opts" = @{ >> scripts\fix-docker-daemon.ps1
echo         "max-size" = "20m" >> scripts\fix-docker-daemon.ps1
echo         "max-file" = "5" >> scripts\fix-docker-daemon.ps1
echo     } >> scripts\fix-docker-daemon.ps1
echo     "max-concurrent-downloads" = 20 >> scripts\fix-docker-daemon.ps1
echo     "max-concurrent-uploads" = 10 >> scripts\fix-docker-daemon.ps1
echo     "max-download-attempts" = 10 >> scripts\fix-docker-daemon.ps1
echo     "shutdown-timeout" = 60 >> scripts\fix-docker-daemon.ps1
echo     "live-restore" = $true >> scripts\fix-docker-daemon.ps1
echo     "userland-proxy" = $true >> scripts\fix-docker-daemon.ps1
echo     "ip-forward" = $true >> scripts\fix-docker-daemon.ps1
echo     "ip-masq" = $true >> scripts\fix-docker-daemon.ps1
echo     "iptables" = $true >> scripts\fix-docker-daemon.ps1
echo     "ip6tables" = $true >> scripts\fix-docker-daemon.ps1
echo     "default-address-pools" = @( >> scripts\fix-docker-daemon.ps1
echo         @{ >> scripts\fix-docker-daemon.ps1
echo             "base" = "172.17.0.0/12" >> scripts\fix-docker-daemon.ps1
echo             "size" = 24 >> scripts\fix-docker-daemon.ps1
echo         }, >> scripts\fix-docker-daemon.ps1
echo         @{ >> scripts\fix-docker-daemon.ps1
echo             "base" = "192.168.0.0/16" >> scripts\fix-docker-daemon.ps1
echo             "size" = 24 >> scripts\fix-docker-daemon.ps1
echo         } >> scripts\fix-docker-daemon.ps1
echo     ) >> scripts\fix-docker-daemon.ps1
echo     "default-network-opts" = @{ >> scripts\fix-docker-daemon.ps1
echo         "com.docker.network.driver.mtu" = "1500" >> scripts\fix-docker-daemon.ps1
echo     } >> scripts\fix-docker-daemon.ps1
echo     "default-runtime" = "runc" >> scripts\fix-docker-daemon.ps1
echo     "runtimes" = @{ >> scripts\fix-docker-daemon.ps1
echo         "runc" = @{ >> scripts\fix-docker-daemon.ps1
echo             "path" = "runc" >> scripts\fix-docker-daemon.ps1
echo         } >> scripts\fix-docker-daemon.ps1
echo     } >> scripts\fix-docker-daemon.ps1
echo     "init" = $true >> scripts\fix-docker-daemon.ps1
echo     "seccomp-profile" = "builtin" >> scripts\fix-docker-daemon.ps1
echo     "no-new-privileges" = $false >> scripts\fix-docker-daemon.ps1
echo     "default-ulimits" = @{ >> scripts\fix-docker-daemon.ps1
echo         "nofile" = @{ >> scripts\fix-docker-daemon.ps1
echo             "Hard" = 100000 >> scripts\fix-docker-daemon.ps1
echo             "Name" = "nofile" >> scripts\fix-docker-daemon.ps1
echo             "Soft" = 100000 >> scripts\fix-docker-daemon.ps1
echo         } >> scripts\fix-docker-daemon.ps1
echo     } >> scripts\fix-docker-daemon.ps1
echo     "default-shm-size" = "2G" >> scripts\fix-docker-daemon.ps1
echo     "dns" = @("8.8.8.8", "8.8.4.4", "1.1.1.1") >> scripts\fix-docker-daemon.ps1
echo     "dns-opts" = @("timeout:5", "attempts:5", "rotate") >> scripts\fix-docker-daemon.ps1
echo     "dns-search" = @() >> scripts\fix-docker-daemon.ps1
echo     "labels" = @( >> scripts\fix-docker-daemon.ps1
echo         "com.blackcnote.project=blackcnote", >> scripts\fix-docker-daemon.ps1
echo         "com.blackcnote.environment=development", >> scripts\fix-docker-daemon.ps1
echo         "com.blackcnote.ml.enabled=true" >> scripts\fix-docker-daemon.ps1
echo     ) >> scripts\fix-docker-daemon.ps1
echo     "metrics-addr" = "127.0.0.1:9323" >> scripts\fix-docker-daemon.ps1
echo     "default-cgroupns-mode" = "private" >> scripts\fix-docker-daemon.ps1
echo     "default-ipc-mode" = "private" >> scripts\fix-docker-daemon.ps1
echo     "tls" = $false >> scripts\fix-docker-daemon.ps1
echo     "tlsverify" = $false >> scripts\fix-docker-daemon.ps1
echo     "selinux-enabled" = $false >> scripts\fix-docker-daemon.ps1
echo     "icc" = $true >> scripts\fix-docker-daemon.ps1
echo     "raw-logs" = $false >> scripts\fix-docker-daemon.ps1
echo     "allow-nondistributable-artifacts" = $false >> scripts\fix-docker-daemon.ps1
echo     "disable-legacy-registry" = $true >> scripts\fix-docker-daemon.ps1
echo     "log-level" = "info" >> scripts\fix-docker-daemon.ps1
echo } >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo # Ensure Docker config directory exists >> scripts\fix-docker-daemon.ps1
echo $dockerConfigDir = "C:\ProgramData\Docker\config" >> scripts\fix-docker-daemon.ps1
echo if (-not (Test-Path $dockerConfigDir)) { >> scripts\fix-docker-daemon.ps1
echo     New-Item -ItemType Directory -Path $dockerConfigDir -Force ^| Out-Null >> scripts\fix-docker-daemon.ps1
echo     Write-Host "Created Docker config directory" -ForegroundColor Green >> scripts\fix-docker-daemon.ps1
echo } >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo # Write the fixed configuration >> scripts\fix-docker-daemon.ps1
echo $daemonPath = "$dockerConfigDir\daemon.json" >> scripts\fix-docker-daemon.ps1
echo $daemonConfig ^| ConvertTo-Json -Depth 10 ^| Set-Content -Path $daemonPath -Encoding UTF8 >> scripts\fix-docker-daemon.ps1
echo Write-Host "Updated Docker daemon configuration" -ForegroundColor Green >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo # Set proper permissions >> scripts\fix-docker-daemon.ps1
echo try { >> scripts\fix-docker-daemon.ps1
echo     icacls $daemonPath /grant "Administrators:(F)" /T >> scripts\fix-docker-daemon.ps1
echo     icacls $daemonPath /grant "Users:(R)" /T >> scripts\fix-docker-daemon.ps1
echo     Write-Host "Set proper file permissions" -ForegroundColor Green >> scripts\fix-docker-daemon.ps1
echo } catch { >> scripts\fix-docker-daemon.ps1
echo     Write-Host "Could not set file permissions" -ForegroundColor Yellow >> scripts\fix-docker-daemon.ps1
echo } >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo # Restart Docker Desktop if requested >> scripts\fix-docker-daemon.ps1
echo if ($RestartDocker) { >> scripts\fix-docker-daemon.ps1
echo     Write-Host "Starting Docker Desktop..." -ForegroundColor Yellow >> scripts\fix-docker-daemon.ps1
echo     try { >> scripts\fix-docker-daemon.ps1
echo         Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized >> scripts\fix-docker-daemon.ps1
echo         Write-Host "Docker Desktop started" -ForegroundColor Green >> scripts\fix-docker-daemon.ps1
echo         Start-Sleep -Seconds 10 >> scripts\fix-docker-daemon.ps1
echo     } catch { >> scripts\fix-docker-daemon.ps1
echo         Write-Host "Failed to start Docker Desktop" -ForegroundColor Red >> scripts\fix-docker-daemon.ps1
echo     } >> scripts\fix-docker-daemon.ps1
echo } >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo # Test Docker API if requested >> scripts\fix-docker-daemon.ps1
echo if ($TestAPI) { >> scripts\fix-docker-daemon.ps1
echo     Write-Host "Testing Docker API..." -ForegroundColor Cyan >> scripts\fix-docker-daemon.ps1
echo     Start-Sleep -Seconds 5 >> scripts\fix-docker-daemon.ps1
echo     try { >> scripts\fix-docker-daemon.ps1
echo         $dockerVersion = docker version 2^>^&1 >> scripts\fix-docker-daemon.ps1
echo         if ($LASTEXITCODE -eq 0) { >> scripts\fix-docker-daemon.ps1
echo             Write-Host "Docker API is working!" -ForegroundColor Green >> scripts\fix-docker-daemon.ps1
echo         } else { >> scripts\fix-docker-daemon.ps1
echo             Write-Host "Docker API test failed" -ForegroundColor Red >> scripts\fix-docker-daemon.ps1
echo         } >> scripts\fix-docker-daemon.ps1
echo     } catch { >> scripts\fix-docker-daemon.ps1
echo         Write-Host "Docker API test failed with exception" -ForegroundColor Red >> scripts\fix-docker-daemon.ps1
echo     } >> scripts\fix-docker-daemon.ps1
echo } >> scripts\fix-docker-daemon.ps1
echo. >> scripts\fix-docker-daemon.ps1
echo Write-Host "Docker daemon configuration fix completed!" -ForegroundColor Green >> scripts\fix-docker-daemon.ps1

echo [INFO] PowerShell script updated successfully
echo [INFO] Running Docker daemon fix...
echo.

REM Run the PowerShell script
powershell.exe -ExecutionPolicy Bypass -File scripts\fix-docker-daemon.ps1 -RestartDocker -TestAPI

echo.
echo [INFO] Automated fix completed!
echo [INFO] Press any key to exit...
pause >nul 