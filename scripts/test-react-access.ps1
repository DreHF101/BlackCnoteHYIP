# Test React App Accessibility
# This script tests if the React app is accessible from both inside and outside the container

Write-Host "Testing React App Accessibility" -ForegroundColor Cyan
Write-Host "===============================" -ForegroundColor Cyan
Write-Host ""

# Test if port 5174 is listening
try {
    $connection = Get-NetTCPConnection -LocalPort 5174 -ErrorAction Stop
    Write-Host "SUCCESS: Port 5174 is listening" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Port 5174 is not accessible" -ForegroundColor Red
    exit 1
}

# Test inside container
Write-Host "Testing inside container..." -ForegroundColor Yellow
try {
    $containerResponse = docker exec blackcnote_react curl -s -o /dev/null -w "%{http_code}" http://localhost:5174
    if ($containerResponse -eq "200") {
        Write-Host "SUCCESS: React app is working inside container" -ForegroundColor Green
    } else {
        Write-Host "ERROR: React app not working inside container" -ForegroundColor Red
    }
} catch {
    Write-Host "ERROR: Cannot test inside container: $($_.Exception.Message)" -ForegroundColor Red
}

# Test from host
Write-Host "Testing from host..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -Method Head -TimeoutSec 10 -ErrorAction Stop
    Write-Host "SUCCESS: React app is accessible from host (HTTP $($response.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "ERROR: React app not accessible from host: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "React App URLs:" -ForegroundColor Cyan
Write-Host "   Main App: http://localhost:5174/" -ForegroundColor White
Write-Host "   Alternative: http://127.0.0.1:5174/" -ForegroundColor White
Write-Host ""

# Troubleshooting tips
Write-Host "6. If you can't access the React app:" -ForegroundColor Yellow
Write-Host "   • Clear your browser cache" -ForegroundColor White
Write-Host "   • Try Incognito/Private mode" -ForegroundColor White
Write-Host "   • Try a different browser" -ForegroundColor White
Write-Host "   • Wait 30 seconds for full initialization" -ForegroundColor White
Write-Host "   • Check Windows Firewall settings" -ForegroundColor White
Write-Host ""

Write-Host "=== Test Complete ===" -ForegroundColor Green 