# Test WordPress Header Output
# Version: 1.0.0

Write-Host "Testing WordPress Header Output" -ForegroundColor Cyan
Write-Host "===============================" -ForegroundColor Cyan

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 15 -UseBasicParsing
    Write-Host "PASS: WordPress frontend accessible" -ForegroundColor Green
    
    # Check for basic HTML structure
    if ($response.Content -match '<!doctype html>') {
        Write-Host "PASS: DOCTYPE found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: DOCTYPE not found" -ForegroundColor Red
    }
    
    if ($response.Content -match '<html') {
        Write-Host "PASS: HTML tag found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: HTML tag not found" -ForegroundColor Red
    }
    
    if ($response.Content -match '<body') {
        Write-Host "PASS: Body tag found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: Body tag not found" -ForegroundColor Red
    }
    
    # Check for React container
    if ($response.Content -match 'id="root"') {
        Write-Host "PASS: Root div found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: Root div not found" -ForegroundColor Red
    }
    
    if ($response.Content -match 'blackcnote-react-app') {
        Write-Host "PASS: blackcnote-react-app class found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: blackcnote-react-app class not found" -ForegroundColor Red
    }
    
    # Check for PHP errors
    if ($response.Content -match 'Warning:|Error:|Fatal error:|Parse error:') {
        Write-Host "FAIL: PHP errors found:" -ForegroundColor Red
        $errors = $response.Content -split "`n" | Where-Object { $_ -match "Warning:|Error:|Fatal error:|Parse error:" }
        foreach ($error in $errors[0..2]) {
            Write-Host "  $($error.Trim())" -ForegroundColor Red
        }
    } else {
        Write-Host "PASS: No PHP errors found" -ForegroundColor Green
    }
    
    # Show first 500 characters of response
    Write-Host "`nFirst 500 characters of response:" -ForegroundColor Yellow
    Write-Host $response.Content.Substring(0, [Math]::Min(500, $response.Content.Length)) -ForegroundColor Gray
    
} catch {
    Write-Host "FAIL: WordPress frontend not accessible - $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n===============================" -ForegroundColor Cyan
Write-Host "Test Complete" -ForegroundColor Cyan 