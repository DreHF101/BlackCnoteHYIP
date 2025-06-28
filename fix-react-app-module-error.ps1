# React App Module Error Fix Script
# Run this script as Administrator

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  React App Module Error Fix" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Set canonical path
$COMPOSE_FILE = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\docker-compose.yml"

Write-Host "[INFO] Fixing React App module configuration errors..." -ForegroundColor Yellow
Write-Host "[INFO] ES module syntax has been applied to config files" -ForegroundColor Yellow
Write-Host ""

# Check if docker-compose.yml exists
if (-not (Test-Path $COMPOSE_FILE)) {
    Write-Host "[ERROR] docker-compose.yml not found at: $COMPOSE_FILE" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "[INFO] Stopping React app container..." -ForegroundColor Yellow
try {
    docker-compose -f $COMPOSE_FILE stop react-app
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[ERROR] Failed to stop React app container" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
} catch {
    Write-Host "[ERROR] Failed to stop React app container: $_" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "[INFO] Removing React app container..." -ForegroundColor Yellow
try {
    docker-compose -f $COMPOSE_FILE rm -f react-app
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[ERROR] Failed to remove React app container" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
} catch {
    Write-Host "[ERROR] Failed to remove React app container: $_" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "[INFO] Rebuilding React app with fixed configuration..." -ForegroundColor Yellow
try {
    docker-compose -f $COMPOSE_FILE build --no-cache react-app
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[ERROR] Failed to rebuild React app" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
} catch {
    Write-Host "[ERROR] Failed to rebuild React app: $_" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "[INFO] Starting React app container..." -ForegroundColor Yellow
try {
    docker-compose -f $COMPOSE_FILE up -d react-app
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[ERROR] Failed to start React app container" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
} catch {
    Write-Host "[ERROR] Failed to start React app container: $_" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "[INFO] Waiting for React app to initialize..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

Write-Host "[INFO] Checking React app status..." -ForegroundColor Yellow
docker-compose -f $COMPOSE_FILE ps react-app

Write-Host "[INFO] Testing React app connection..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -UseBasicParsing -TimeoutSec 5
    Write-Host "HTTP Status: $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "HTTP Status: Connection failed - React app may still be starting" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  React App Module Error Fix Complete" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "[SUCCESS] React app should now be working properly" -ForegroundColor Green
Write-Host ""
Write-Host "[ACCESS URLS]" -ForegroundColor Cyan
Write-Host "React App: http://localhost:5174" -ForegroundColor White
Write-Host "WordPress: http://localhost:8888" -ForegroundColor White
Write-Host "phpMyAdmin: http://localhost:8080" -ForegroundColor White
Write-Host "Metrics: http://localhost:9091" -ForegroundColor White
Write-Host ""
Write-Host "[TROUBLESHOOTING]" -ForegroundColor Cyan
Write-Host "If React app still doesn't work:" -ForegroundColor White
Write-Host "1. Check logs: docker-compose -f $COMPOSE_FILE logs react-app" -ForegroundColor White
Write-Host "2. Restart: docker-compose -f $COMPOSE_FILE restart react-app" -ForegroundColor White
Write-Host ""
Read-Host "Press Enter to exit" 