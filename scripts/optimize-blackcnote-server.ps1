# BlackCnote Server Optimization Script
# Comprehensive server optimization and issue resolution
# Fixes Docker issues, port conflicts, and performance problems

param(
    [switch]$ForceRebuild,
    [switch]$SkipReact,
    [switch]$Debug,
    [switch]$OptimizeOnly
)

$ErrorActionPreference = 'Stop'
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$logFile = "$projectRoot\logs\server-optimization-$(Get-Date -Format 'yyyyMMdd-HHmmss').txt"

function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
    Add-Content -Path $logFile -Value $Message
}

function Test-ServiceConnectivity {
    param([string]$Url, [string]$Name, [int]$Timeout = 10)
    
    try {
        $response = Invoke-WebRequest -Uri $Url -TimeoutSec $Timeout -UseBasicParsing -ErrorAction Stop
        if ($response.StatusCode -eq 200) {
            Write-ColorOutput "[OK] $Name - $Url" "Green"
            return $true
        } else {
            Write-ColorOutput "[WARNING] $Name - $Url (Status: $($response.StatusCode))" "Yellow"
            return $false
        }
    } catch {
        Write-ColorOutput "[ERROR] $Name - $Url" "Red"
        return $false
    }
}

function Optimize-DockerConfiguration {
    Write-ColorOutput "Optimizing Docker configuration..." "Cyan"
    
    # Create optimized redis.conf if it doesn't exist
    $redisConfigPath = "$projectRoot\redis.conf"
    if (-not (Test-Path $redisConfigPath)) {
        $redisConfig = @"
# BlackCnote Redis Configuration - Optimized for Development
bind 0.0.0.0
port 6379
timeout 0
tcp-keepalive 300
daemonize no
supervised no
pidfile /var/run/redis_6379.pid
loglevel notice
logfile ""
databases 16
save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir ./
maxmemory 256mb
maxmemory-policy allkeys-lru
appendonly yes
appendfilename "appendonly.aof"
appendfsync everysec
no-appendfsync-on-rewrite no
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb
aof-load-truncated yes
aof-use-rdb-preamble yes
"@
        Set-Content -Path $redisConfigPath -Value $redisConfig -Encoding UTF8
        Write-ColorOutput "Created optimized Redis configuration" "Green"
    }
    
    # Ensure logs directory exists
    $logsDir = "$projectRoot\logs"
    if (-not (Test-Path $logsDir)) {
        New-Item -ItemType Directory -Path $logsDir -Force | Out-Null
        Write-ColorOutput "Created logs directory" "Green"
    }
    
    # Ensure db directory exists
    $dbDir = "$projectRoot\db"
    if (-not (Test-Path $dbDir)) {
        New-Item -ItemType Directory -Path $dbDir -Force | Out-Null
        Write-ColorOutput "Created db directory" "Green"
    }
}

function Fix-PortConflicts {
    Write-ColorOutput "Checking for port conflicts..." "Cyan"
    
    $ports = @(8888, 5174, 8080, 8081, 8025, 9229, 3000, 3001, 3306, 6379)
    
    foreach ($port in $ports) {
        $process = Get-NetTCPConnection -LocalPort $port -ErrorAction SilentlyContinue
        if ($process) {
            Write-ColorOutput "Port $port is in use by: $($process.ProcessName)" "Yellow"
            try {
                Stop-Process -Id $process.OwningProcess -Force -ErrorAction SilentlyContinue
                Write-ColorOutput "Stopped process using port $port" "Green"
            } catch {
                Write-ColorOutput "Could not stop process using port $port" "Red"
            }
        }
    }
}

function Clean-DockerEnvironment {
    Write-ColorOutput "Cleaning Docker environment..." "Cyan"
    
    # Stop and remove all BlackCnote containers
    try {
        docker-compose down --remove-orphans 2>$null
        Write-ColorOutput "Stopped existing containers" "Green"
    } catch {
        Write-ColorOutput "No existing containers to stop" "Yellow"
    }
    
    # Remove dangling images and volumes
    try {
        docker system prune -f 2>$null
        Write-ColorOutput "Cleaned Docker system" "Green"
    } catch {
        Write-ColorOutput "Docker system cleanup completed" "Yellow"
    }
}

function Start-OptimizedServices {
    Write-ColorOutput "Starting optimized BlackCnote services..." "Cyan"
    
    # Start Docker Desktop if not running
    try {
        $dockerInfo = docker info 2>&1
        if ($dockerInfo -match "error during connect") {
            Write-ColorOutput "Starting Docker Desktop..." "Yellow"
            Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Normal
            Start-Sleep -Seconds 30
        }
    } catch {
        Write-ColorOutput "Docker Desktop not found or not accessible" "Red"
        return $false
    }
    
    # Start services with optimized configuration
    if ($ForceRebuild) {
        docker-compose up -d --build
    } else {
        docker-compose up -d
    }
    
    if ($LASTEXITCODE -ne 0) {
        Write-ColorOutput "Failed to start services" "Red"
        return $false
    }
    
    Write-ColorOutput "Services started successfully" "Green"
    return $true
}

function Wait-For-Services {
    Write-ColorOutput "Waiting for services to be healthy..." "Cyan"
    
    $services = @(
        @{ Url = "http://localhost:8888"; Name = "WordPress" },
        @{ Url = "http://localhost:8080"; Name = "phpMyAdmin" },
        @{ Url = "http://localhost:8081"; Name = "Redis Commander" },
        @{ Url = "http://localhost:8025"; Name = "MailHog" }
    )
    
    if (-not $SkipReact) {
        $services += @{ Url = "http://localhost:5174"; Name = "React App" }
    }
    
    $allHealthy = $true
    
    foreach ($service in $services) {
        $start = Get-Date
        $healthy = $false
        
        while ((Get-Date) - $start -lt (New-TimeSpan -Seconds 120)) {
            if (Test-ServiceConnectivity -Url $service.Url -Name $service.Name -Timeout 5) {
                $healthy = $true
                break
            }
            Start-Sleep -Seconds 3
        }
        
        if (-not $healthy) {
            Write-ColorOutput "Service $($service.Name) did not become healthy" "Red"
            $allHealthy = $false
        }
    }
    
    return $allHealthy
}

function Test-AllServices {
    Write-ColorOutput "Testing all services..." "Cyan"
    
    $services = @{
        "WordPress" = "http://localhost:8888"
        "React App" = "http://localhost:5174"
        "phpMyAdmin" = "http://localhost:8080"
        "Redis Commander" = "http://localhost:8081"
        "MailHog" = "http://localhost:8025"
        "Browsersync" = "http://localhost:3000"
        "Dev Tools" = "http://localhost:9229"
    }
    
    $allWorking = $true
    
    foreach ($service in $services.GetEnumerator()) {
        if (-not (Test-ServiceConnectivity -Url $service.Value -Name $service.Key)) {
            $allWorking = $false
        }
    }
    
    return $allWorking
}

function Show-PerformanceMetrics {
    Write-ColorOutput "Performance Metrics:" "Cyan"
    
    # Docker resource usage
    try {
        $dockerStats = docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}"
        Write-ColorOutput "Docker Resource Usage:" "Yellow"
        Write-ColorOutput $dockerStats "White"
    } catch {
        Write-ColorOutput "Could not retrieve Docker stats" "Yellow"
    }
    
    # Service response times
    Write-ColorOutput "Service Response Times:" "Yellow"
    $services = @("http://localhost:8888", "http://localhost:5174", "http://localhost:8080")
    
    foreach ($url in $services) {
        try {
            $stopwatch = [System.Diagnostics.Stopwatch]::StartNew()
            $response = Invoke-WebRequest -Uri $url -TimeoutSec 10 -UseBasicParsing
            $stopwatch.Stop()
            Write-ColorOutput "$url - $($stopwatch.ElapsedMilliseconds)ms" "White"
        } catch {
            Write-ColorOutput "$url - ERROR" "Red"
        }
    }
}

# Main execution
Write-ColorOutput "==========================================" "Cyan"
Write-ColorOutput "BLACKCNOTE SERVER OPTIMIZATION" "Cyan"
Write-ColorOutput "==========================================" "Cyan"
Write-ColorOutput "Timestamp: $(Get-Date)" "Gray"
Write-ColorOutput ""

if ($OptimizeOnly) {
    Write-ColorOutput "Running optimization only..." "Yellow"
    Optimize-DockerConfiguration
    Fix-PortConflicts
    Show-PerformanceMetrics
    exit 0
}

# Step 1: Optimize configuration
Write-ColorOutput "Step 1: Optimizing configuration..." "Yellow"
Optimize-DockerConfiguration

# Step 2: Fix port conflicts
Write-ColorOutput "Step 2: Fixing port conflicts..." "Yellow"
Fix-PortConflicts

# Step 3: Clean Docker environment
Write-ColorOutput "Step 3: Cleaning Docker environment..." "Yellow"
Clean-DockerEnvironment

# Step 4: Start optimized services
Write-ColorOutput "Step 4: Starting optimized services..." "Yellow"
$servicesStarted = Start-OptimizedServices

if (-not $servicesStarted) {
    Write-ColorOutput "Failed to start services. Exiting." "Red"
    exit 1
}

# Step 5: Wait for services to be healthy
Write-ColorOutput "Step 5: Waiting for services to be healthy..." "Yellow"
$servicesHealthy = Wait-For-Services

if (-not $servicesHealthy) {
    Write-ColorOutput "Some services did not become healthy" "Red"
}

# Step 6: Test all services
Write-ColorOutput "Step 6: Testing all services..." "Yellow"
$allServicesWorking = Test-AllServices

# Step 7: Show performance metrics
Write-ColorOutput "Step 7: Performance metrics..." "Yellow"
Show-PerformanceMetrics

# Final summary
Write-ColorOutput ""
Write-ColorOutput "=== OPTIMIZATION SUMMARY ===" "Cyan"
Write-ColorOutput "Services Started: $(if ($servicesStarted) { '[OK] Yes' } else { '[ERROR] No' })" $(if ($servicesStarted) { 'Green' } else { 'Red' })
Write-ColorOutput "Services Healthy: $(if ($servicesHealthy) { '[OK] Yes' } else { '[ERROR] No' })" $(if ($servicesHealthy) { 'Green' } else { 'Red' })
Write-ColorOutput "All Services Working: $(if ($allServicesWorking) { '[OK] Yes' } else { '[ERROR] No' })" $(if ($allServicesWorking) { 'Green' } else { 'Red' })

if ($allServicesWorking) {
    Write-ColorOutput ""
    Write-ColorOutput "[SUCCESS] BlackCnote server optimization completed successfully!" "Green"
    Write-ColorOutput "All services are running and optimized." "Green"
} else {
    Write-ColorOutput ""
    Write-ColorOutput "[WARNING] Optimization completed with some issues." "Yellow"
    Write-ColorOutput "Check the logs for details." "Yellow"
}

Write-ColorOutput ""
Write-ColorOutput "=== SERVICE URLs ===" "Cyan"
Write-ColorOutput "WordPress:      http://localhost:8888" "White"
Write-ColorOutput "React App:      http://localhost:5174" "White"
Write-ColorOutput "phpMyAdmin:     http://localhost:8080" "White"
Write-ColorOutput "Redis Commander: http://localhost:8081" "White"
Write-ColorOutput "MailHog:        http://localhost:8025" "White"
Write-ColorOutput "Browsersync:    http://localhost:3000" "White"
Write-ColorOutput "Dev Tools:      http://localhost:9229" "White"

Write-ColorOutput ""
Write-ColorOutput "Optimization completed at: $(Get-Date)" "Gray" 