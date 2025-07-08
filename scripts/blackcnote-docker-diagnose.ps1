# BlackCnote Docker Diagnose Script
# Automates health checks, log collection, and service URL testing

$ErrorActionPreference = 'Stop'
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$logFile = "$projectRoot\logs\docker-diagnose-$(Get-Date -Format 'yyyyMMdd-HHmmss').txt"

function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
    Add-Content -Path $logFile -Value $Message
}

function Start-BlackCnoteServices {
    Write-ColorOutput "[INFO] Starting BlackCnote Docker services..." "Cyan"
    docker-compose up -d --build
    if ($LASTEXITCODE -ne 0) {
        Write-ColorOutput "[ERROR] Failed to start Docker services." "Red"
        exit 1
    }
    Write-ColorOutput "[OK] Docker services started." "Green"
}

function Wait-For-Containers {
    Write-ColorOutput "[INFO] Waiting for containers to start..." "Cyan"
    Start-Sleep -Seconds 15
}

function Check-ContainerStatus {
    Write-ColorOutput "[INFO] Checking container status..." "Cyan"
    $containers = @(
        "blackcnote-wordpress",
        "blackcnote-mysql",
        "blackcnote-redis",
        "blackcnote-react",
        "blackcnote-phpmyadmin",
        "blackcnote-redis-commander",
        "blackcnote-mailhog",
        "blackcnote-browsersync",
        "blackcnote-dev-tools",
        "blackcnote-debug-exporter",
        "blackcnote-file-watcher"
    )
    $status = @{}
    $allRunning = $true
    foreach ($container in $containers) {
        $info = docker ps -a --filter "name=$container" --format "{{.Status}}"
        if ($info -match "Up") {
            Write-ColorOutput "[OK] ${container}: $info" "Green"
            $status[$container] = "running"
        } else {
            Write-ColorOutput "[ERROR] ${container}: $info" "Red"
            $status[$container] = "error"
            $allRunning = $false
        }
    }
    return $status
}

function Collect-ContainerLogs {
    param([hashtable]$status)
    Write-ColorOutput "[INFO] Collecting logs for failed containers..." "Cyan"
    foreach ($container in $status.Keys) {
        if ($status[$($container)] -eq "error") {
            Write-ColorOutput "[LOGS] ${container}:" "Yellow"
            try {
                $logs = docker logs $container 2>&1
                Add-Content -Path $logFile -Value "[LOGS] ${container}:`n$logs`n"
                Write-Host $logs
            } catch {
                Write-ColorOutput "[ERROR] Could not get logs for ${container}" "Red"
            }
        }
    }
}

function Test-ServiceURLs {
    Write-ColorOutput "[INFO] Testing service URLs..." "Cyan"
    $services = @{
        "WordPress" = "http://localhost:8888"
        "React App" = "http://localhost:5174"
        "phpMyAdmin" = "http://localhost:8080"
        "Redis Commander" = "http://localhost:8081"
        "MailHog" = "http://localhost:8025"
        "Browsersync" = "http://localhost:3000"
        "Dev Tools" = "http://localhost:9229"
    }
    foreach ($service in $services.GetEnumerator()) {
        try {
            $response = Invoke-WebRequest -Uri $service.Value -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
            if ($response.StatusCode -eq 200) {
                Write-ColorOutput "[OK] $($service.Key): $($service.Value)" "Green"
            } else {
                Write-ColorOutput "[WARNING] $($service.Key): $($service.Value) (Status: $($response.StatusCode))" "Yellow"
            }
        } catch {
            Write-ColorOutput "[ERROR] $($service.Key): $($service.Value)" "Red"
        }
    }
}

# Main execution
Write-ColorOutput "==========================================" "Cyan"
Write-ColorOutput "BLACKCNOTE DOCKER DIAGNOSE" "Cyan"
Write-ColorOutput "==========================================" "Cyan"
Write-ColorOutput "Timestamp: $(Get-Date)" "Gray"
Write-ColorOutput ""

Start-BlackCnoteServices
Wait-For-Containers
$status = Check-ContainerStatus
Collect-ContainerLogs -status $status
Test-ServiceURLs

Write-ColorOutput "" "White"
Write-ColorOutput "[SUMMARY] Diagnose complete. See $logFile for details." "Cyan" 