#!/usr/bin/env pwsh

# BlackCnote Admin Template Override Fix Test Script
# =================================================
# This script tests if the admin template override fix is working

Write-Host "🧪 BlackCnote Admin Template Override Fix Test" -ForegroundColor Cyan
Write-Host "===========================================" -ForegroundColor Cyan

# Test admin page
Write-Host "🔍 Testing WordPress admin page..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-admin/" -UseBasicParsing -TimeoutSec 15
    
    if ($response.StatusCode -eq 200) {
        Write-Host "  ✅ Admin page returned HTTP 200" -ForegroundColor Green
        
        if ($response.Content -match "WordPress") {
            Write-Host "  ✅ Admin page contains 'WordPress' - Normal admin interface" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  Admin page doesn't contain 'WordPress'" -ForegroundColor Yellow
        }
        
        if ($response.Content -match "Loading BlackCnote") {
            Write-Host "  ❌ Admin page still shows React loading - Fix not working" -ForegroundColor Red
        } else {
            Write-Host "  ✅ Admin page doesn't show React loading - Fix working!" -ForegroundColor Green
        }
        
        if ($response.Content -match "wp-admin") {
            Write-Host "  ✅ Admin page contains 'wp-admin' - Correct admin interface" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  Admin page doesn't contain 'wp-admin'" -ForegroundColor Yellow
        }
        
        # Show first 500 characters of response
        Write-Host "  📄 Response preview:" -ForegroundColor Gray
        Write-Host "  $($response.Content.Substring(0, [Math]::Min(500, $response.Content.Length)))" -ForegroundColor Gray
        
    } else {
        Write-Host "  ❌ Admin page returned HTTP $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Error testing admin page: $($_.Exception.Message)" -ForegroundColor Red
}

# Test front-end page
Write-Host "🔍 Testing front-end page..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/" -UseBasicParsing -TimeoutSec 15
    
    if ($response.StatusCode -eq 200) {
        Write-Host "  ✅ Front-end page returned HTTP 200" -ForegroundColor Green
        
        if ($response.Content -match "Loading BlackCnote") {
            Write-Host "  ✅ Front-end page shows React loading - Correct behavior" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  Front-end page doesn't show React loading" -ForegroundColor Yellow
        }
        
        if ($response.Content -match "BlackCnote") {
            Write-Host "  ✅ Front-end page contains 'BlackCnote'" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  Front-end page doesn't contain 'BlackCnote'" -ForegroundColor Yellow
        }
    } else {
        Write-Host "  ❌ Front-end page returned HTTP $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Error testing front-end page: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "🧪 Test completed!" -ForegroundColor Green 