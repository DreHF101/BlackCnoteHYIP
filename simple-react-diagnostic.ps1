# Simple React App Diagnostic Script

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  React App Diagnostic" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Set canonical path
$COMPOSE_FILE = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\docker-compose.yml"

Write-Host "[INFO] Checking Docker containers..." -ForegroundColor Yellow
docker ps

Write-Host ""
Write-Host "[INFO] Checking React app container specifically..." -ForegroundColor Yellow
docker-compose -f $COMPOSE_FILE ps react-app

Write-Host ""
Write-Host "[INFO] Checking React app logs..." -ForegroundColor Yellow
docker-compose -f $COMPOSE_FILE logs --tail=10 react-app

Write-Host ""
Write-Host "[INFO] Testing URLs..." -ForegroundColor Yellow

Write-Host "Testing http://localhost:5174/ ..." -ForegroundColor White
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174/" -UseBasicParsing -TimeoutSec 5
    Write-Host "✓ http://localhost:5174/ - Status: $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "✗ http://localhost:5174/ - Failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "Testing http://localhost:5174/index.html ..." -ForegroundColor White
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174/index.html" -UseBasicParsing -TimeoutSec 5
    Write-Host "✓ http://localhost:5174/index.html - Status: $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "✗ http://localhost:5174/index.html - Failed: $($_.Exception.Message)" -ForegroundColor Red
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
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Diagnostic Complete" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "[NEXT STEPS]" -ForegroundColor Yellow
Write-Host "1. If container is not running: docker-compose -f $COMPOSE_FILE up -d react-app" -ForegroundColor White
Write-Host "2. If logs show errors: Check the error messages above" -ForegroundColor White
Write-Host "3. If port is not listening: Restart the container" -ForegroundColor White
Write-Host ""
Read-Host "Press Enter to exit" 