# Test WordPress Accessibility
# This script tests if WordPress is accessible from the host

Write-Host "Testing WordPress Accessibility" -ForegroundColor Cyan
Write-Host "===============================" -ForegroundColor Cyan
Write-Host ""

# Test if port 8888 is listening
try {
    $connection = Get-NetTCPConnection -LocalPort 8888 -ErrorAction Stop
    Write-Host "SUCCESS: Port 8888 is listening" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Port 8888 is not accessible" -ForegroundColor Red
    exit 1
}

# Test WordPress admin accessibility
Write-Host "Testing WordPress admin..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-admin/" -Method Head -TimeoutSec 10 -ErrorAction Stop
    Write-Host "SUCCESS: WordPress admin is accessible (HTTP $($response.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "ERROR: WordPress admin is not accessible: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "WordPress URLs:" -ForegroundColor Cyan
Write-Host "   Main Site: http://localhost:8888/" -ForegroundColor White
Write-Host "   Admin Panel: http://localhost:8888/wp-admin/" -ForegroundColor White
Write-Host ""

# Troubleshooting tips
Write-Host "5. If you can't access WordPress:" -ForegroundColor Yellow
Write-Host "   • Clear your browser cache" -ForegroundColor White
Write-Host "   • Try Incognito/Private mode" -ForegroundColor White
Write-Host "   • Try a different browser" -ForegroundColor White
Write-Host "   • Wait 30 seconds for full initialization" -ForegroundColor White
Write-Host ""

Write-Host "=== Test Complete ===" -ForegroundColor Green 