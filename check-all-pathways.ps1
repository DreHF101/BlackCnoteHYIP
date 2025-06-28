# BlackCnote Pathway Check Script
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  BlackCnote Pathway Check Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[INFO] Checking all pathways and connections..." -ForegroundColor Yellow
Write-Host ""

# Check 1: Docker Status
Write-Host "[CHECK 1] Docker Status" -ForegroundColor Green
try {
    $dockerVersion = docker --version
    Write-Host "✓ Docker is accessible" -ForegroundColor Green
    Write-Host $dockerVersion -ForegroundColor White
} catch {
    Write-Host "✗ Docker is not accessible" -ForegroundColor Red
    Write-Host "[ERROR] Docker Desktop may not be running" -ForegroundColor Red
}

Write-Host ""

# Check 2: Docker Compose Status
Write-Host "[CHECK 2] Docker Compose Status" -ForegroundColor Green
try {
    $composeVersion = docker-compose --version
    Write-Host "✓ Docker Compose is accessible" -ForegroundColor Green
    Write-Host $composeVersion -ForegroundColor White
} catch {
    Write-Host "✗ Docker Compose is not accessible" -ForegroundColor Red
}

Write-Host ""

# Check 3: Running Containers
Write-Host "[CHECK 3] Running Containers" -ForegroundColor Green
try {
    $containers = docker ps
    Write-Host "✓ Docker containers can be listed" -ForegroundColor Green
    Write-Host "[INFO] Current running containers:" -ForegroundColor Yellow
    Write-Host $containers -ForegroundColor White
} catch {
    Write-Host "✗ Cannot list Docker containers" -ForegroundColor Red
}

Write-Host ""

# Check 4: BlackCnote Services Status
Write-Host "[CHECK 4] BlackCnote Services Status" -ForegroundColor Green
if (Test-Path "docker-compose.yml") {
    Write-Host "✓ docker-compose.yml found" -ForegroundColor Green
    try {
        $services = docker-compose ps
        Write-Host "✓ Docker Compose can read services" -ForegroundColor Green
        Write-Host "[INFO] Service status:" -ForegroundColor Yellow
        Write-Host $services -ForegroundColor White
    } catch {
        Write-Host "✗ Docker Compose cannot read services" -ForegroundColor Red
    }
} else {
    Write-Host "✗ docker-compose.yml not found" -ForegroundColor Red
}

Write-Host ""

# Check 5: Port 8888 Status
Write-Host "[CHECK 5] Port 8888 Status" -ForegroundColor Green
$port8888 = netstat -an | Select-String ":8888"
if ($port8888) {
    Write-Host "✓ Port 8888 is in use" -ForegroundColor Green
    Write-Host "[INFO] Port 8888 details:" -ForegroundColor Yellow
    Write-Host $port8888 -ForegroundColor White
} else {
    Write-Host "✗ Port 8888 is not in use" -ForegroundColor Red
    Write-Host "[INFO] This means no service is listening on port 8888" -ForegroundColor Yellow
}

Write-Host ""

# Check 6: WordPress Container Status
Write-Host "[CHECK 6] WordPress Container Status" -ForegroundColor Green
$wordpressContainer = docker ps | Select-String "wordpress"
if ($wordpressContainer) {
    Write-Host "✓ WordPress container is running" -ForegroundColor Green
    Write-Host $wordpressContainer -ForegroundColor White
} else {
    Write-Host "✗ WordPress container is not running" -ForegroundColor Red
}

Write-Host ""

# Check 7: Try to Start Services
Write-Host "[CHECK 7] Attempting to Start Services" -ForegroundColor Green
Write-Host "[INFO] Starting BlackCnote services..." -ForegroundColor Yellow
try {
    docker-compose up -d
    Write-Host "✓ Services started successfully" -ForegroundColor Green
} catch {
    Write-Host "✗ Failed to start services" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Write-Host ""

# Check 8: Wait and Check Again
Write-Host "[CHECK 8] Waiting for Services to Start" -ForegroundColor Green
Write-Host "[INFO] Waiting 30 seconds for services to initialize..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

Write-Host "[INFO] Checking service status after startup:" -ForegroundColor Yellow
try {
    $servicesAfter = docker-compose ps
    Write-Host $servicesAfter -ForegroundColor White
} catch {
    Write-Host "✗ Cannot check service status" -ForegroundColor Red
}

Write-Host ""

# Check 9: Port 8888 After Startup
Write-Host "[CHECK 9] Port 8888 After Startup" -ForegroundColor Green
$port8888After = netstat -an | Select-String ":8888"
if ($port8888After) {
    Write-Host "✓ Port 8888 is now in use" -ForegroundColor Green
    Write-Host "[INFO] Port 8888 details:" -ForegroundColor Yellow
    Write-Host $port8888After -ForegroundColor White
} else {
    Write-Host "✗ Port 8888 is still not in use" -ForegroundColor Red
}

Write-Host ""

# Check 10: Test HTTP Connection
Write-Host "[CHECK 10] Testing HTTP Connection" -ForegroundColor Green
Write-Host "[INFO] Testing connection to http://localhost:8888..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    Write-Host "✓ HTTP connection successful (Status: $($response.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "✗ HTTP connection failed" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Write-Host ""

# Check 11: Check Logs
Write-Host "[CHECK 11] Recent Logs" -ForegroundColor Green
if (Test-Path "logs\blackcnote-debug.log") {
    Write-Host "[INFO] Recent debug log entries:" -ForegroundColor Yellow
    try {
        $recentLogs = Get-Content "logs\blackcnote-debug.log" | Select-String "2025-06-27" | Where-Object { $_ -notmatch "DEBUG|INFO" }
        Write-Host $recentLogs -ForegroundColor White
    } catch {
        Write-Host "No recent log entries found" -ForegroundColor Yellow
    }
}

Write-Host ""

# Check 12: Docker Logs
Write-Host "[CHECK 12] Docker Service Logs" -ForegroundColor Green
Write-Host "[INFO] WordPress container logs:" -ForegroundColor Yellow
try {
    $wordpressLogs = docker-compose logs wordpress --tail=10
    Write-Host $wordpressLogs -ForegroundColor White
} catch {
    Write-Host "✗ Cannot retrieve WordPress logs" -ForegroundColor Red
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Pathway Check Complete" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[SUMMARY]" -ForegroundColor Green
Write-Host "If you see 'Port 8888 is now in use' above, try accessing:" -ForegroundColor Yellow
Write-Host "http://localhost:8888" -ForegroundColor Cyan
Write-Host ""
Write-Host "If port 8888 is still not in use, the services failed to start." -ForegroundColor Yellow
Write-Host "Check the Docker logs above for error messages." -ForegroundColor Yellow
Write-Host ""
Read-Host "Press Enter to continue" 