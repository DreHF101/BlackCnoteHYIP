# React App Diagnostic Script
# This script will help diagnose why the React app is returning 404

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  React App Diagnostic" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Set canonical path
$COMPOSE_FILE = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\docker-compose.yml"

Write-Host "[INFO] Checking Docker containers..." -ForegroundColor Yellow
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

Write-Host ""
Write-Host "[INFO] Checking React app container specifically..." -ForegroundColor Yellow
docker-compose -f $COMPOSE_FILE ps react-app

Write-Host ""
Write-Host "[INFO] Checking React app logs..." -ForegroundColor Yellow
docker-compose -f $COMPOSE_FILE logs --tail=20 react-app

Write-Host ""
Write-Host "[INFO] Testing different URLs..." -ForegroundColor Yellow

$urls = @(
    "http://localhost:5174/",
    "http://localhost:5174/index.html",
    "http://localhost:5174/src/main.tsx"
)

foreach ($url in $urls) {
    try {
        $response = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 5
        Write-Host "✓ $url - Status: $($response.StatusCode)" -ForegroundColor Green
    } catch {
        $statusCode = $_.Exception.Response.StatusCode.value__
        Write-Host "✗ $url - Status: $statusCode" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "[INFO] Checking if port 5174 is listening..." -ForegroundColor Yellow
$portCheck = netstat -an | findstr ":5174"
if ($portCheck) {
    Write-Host "✓ Port 5174 is listening:" -ForegroundColor Green
    Write-Host $portCheck -ForegroundColor White
} else {
    Write-Host "✗ Port 5174 is not listening" -ForegroundColor Red
}

Write-Host ""
Write-Host "[INFO] Checking React app files in container..." -ForegroundColor Yellow
docker-compose -f $COMPOSE_FILE exec react-app ls -la /app/

Write-Host ""
Write-Host "[INFO] Checking if index.html exists in container..." -ForegroundColor Yellow
docker-compose -f $COMPOSE_FILE exec react-app cat /app/index.html

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Diagnostic Complete" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "[NEXT STEPS]" -ForegroundColor Yellow
Write-Host "1. If container is not running: docker-compose -f $COMPOSE_FILE up -d react-app" -ForegroundColor White
Write-Host "2. If logs show errors: Check the error messages above" -ForegroundColor White
Write-Host "3. If port is not listening: Restart the container" -ForegroundColor White
Write-Host "4. If files are missing: Rebuild the container" -ForegroundColor White
Write-Host ""
Read-Host "Press Enter to exit" 