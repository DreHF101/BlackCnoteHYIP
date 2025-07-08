# BlackCnote Unified Startup Script
# Canonical, robust, and fully automated
param(
    [switch]$ForceRebuild,
    [switch]$SkipReact,
    [switch]$NoBrowser,
    [switch]$Debug
)

$ErrorActionPreference = 'Stop'
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$dockerComposeFile = "$projectRoot\docker-compose.yml"
$logFile = "$projectRoot\logs\startup-log-$(Get-Date -Format 'yyyyMMdd-HHmmss').txt"

function Log {
    param([string]$msg, [string]$color = 'White')
    Write-Host $msg -ForegroundColor $color
    Add-Content -Path $logFile -Value $msg
}

function Wait-For-Service {
    param([string]$Url, [string]$Name, [int]$TimeoutSec = 120)
    $start = Get-Date
    while ((Get-Date) - $start -lt (New-TimeSpan -Seconds $TimeoutSec)) {
        try {
            $response = Invoke-WebRequest -Uri $Url -TimeoutSec 5 -UseBasicParsing
            if ($response.StatusCode -eq 200) {
                Log "âœ… $Name is healthy at $Url" 'Green'
                return $true
            }
        } catch {}
        Log "Waiting for $Name at $Url..." 'Yellow'
        Start-Sleep -Seconds 3
    }
    Log "âŒ $Name did not become healthy in time." 'Red'
    return $false
}

# 1. Start Docker Desktop and wait for engine
Log "==========================================" 'Cyan'
Log "BLACKCNOTE UNIFIED STARTUP SYSTEM" 'Cyan'
Log "==========================================" 'Cyan'
Log ""
Log "Step 1: Opening Docker Desktop..." 'Yellow'
Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Normal
Log "Docker Desktop started. Waiting for engine to initialize..." 'Green'

$maxWait = 300
$waitCount = 0
$dockerReady = $false
while ($waitCount -lt $maxWait -and -not $dockerReady) {
    Start-Sleep -Seconds 5
    $waitCount += 5
    try {
        $dockerInfo = docker info 2>&1
        if ($dockerInfo -notmatch "error during connect") {
            $dockerReady = $true
            Log "Docker engine is ready!" 'Green'
            break
        }
    } catch {}
    if ($waitCount % 30 -eq 0) {
        Log "Still waiting for Docker engine... ($waitCount seconds)" 'Yellow'
    }
}

if (-not $dockerReady) {
    Log "âŒ Docker engine did not start in time. Please check Docker Desktop manually." 'Red'
    exit 1
}

# 2. Stop existing containers
Log "Step 2: Stopping existing containers..." 'Yellow'
try {
    docker-compose down --remove-orphans 2>$null
    Log "Existing containers stopped" 'Green'
} catch {
    Log "No existing containers to stop" 'Yellow'
}

# 3. Start services
Log "Step 3: Starting BlackCnote services..." 'Yellow'
if ($ForceRebuild) {
    docker-compose up -d --build
} else {
    docker-compose up -d
}

if ($LASTEXITCODE -ne 0) {
    Log "âŒ Failed to start services" 'Red'
    exit 1
}

Log "Services started successfully" 'Green'

# 4. Wait for services to be healthy
Log "Step 4: Waiting for services to be healthy..." 'Yellow'

$services = @(
    @{ Url = "http://localhost:8888"; Name = "WordPress" },
    @{ Url = "http://localhost:8080"; Name = "phpMyAdmin" },
    @{ Url = "http://localhost:8081"; Name = "Redis Commander" },
    @{ Url = "http://localhost:8025"; Name = "MailHog" }
)

if (-not $SkipReact) {
    $services += @{ Url = "http://localhost:5174"; Name = "React App" }
}

foreach ($service in $services) {
    Wait-For-Service -Url $service.Url -Name $service.Name
}

# 5. Open browser if requested
if (-not $NoBrowser) {
    Log "Step 5: Opening services in browser..." 'Yellow'
    Start-Process "http://localhost:8888"
    Start-Sleep -Seconds 2
    Start-Process "http://localhost:5174"
    Start-Sleep -Seconds 2
    Start-Process "http://localhost:8080"
}

# 6. Display status
Log "==========================================" 'Cyan'
Log "BLACKCNOTE SERVICES STATUS" 'Cyan'
Log "==========================================" 'Cyan'
Log ""
Log "âœ… WordPress: http://localhost:8888" 'Green'
Log "âœ… WordPress Admin: http://localhost:8888/wp-admin" 'Green'
Log "âœ… React App: http://localhost:5174" 'Green'
Log "âœ… phpMyAdmin: http://localhost:8080" 'Green'
Log "âœ… Redis Commander: http://localhost:8081" 'Green'
Log "âœ… MailHog: http://localhost:8025" 'Green'
Log "âœ… Browsersync: http://localhost:3000" 'Green'
Log "âœ… Dev Tools: http://localhost:9229" 'Green'
Log "âœ… Metrics: http://localhost:9091" 'Green'
Log ""
Log "ðŸŽ‰ BlackCnote is ready!" 'Green'
Log "==========================================" 'Cyan'
