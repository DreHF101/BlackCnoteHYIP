#!/usr/bin/env pwsh

Write-Host "🔧 BlackCnote Critical Fixes Test" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# Test 1: Check HYIPLab API Plugin
Write-Host "`n1. Testing HYIPLab API Plugin..." -ForegroundColor Yellow
$hyiplabFile = "blackcnote\wp-content\plugins\blackcnote-hyiplab-api\blackcnote-hyiplab-api.php"
if (Test-Path $hyiplabFile) {
    $content = Get-Content $hyiplabFile -Raw
    if ($content.StartsWith("<?php`ndeclare(strict_types=1);")) {
        Write-Host "   ✅ HYIPLab API plugin is correct" -ForegroundColor Green
    } else {
        Write-Host "   ❌ HYIPLab API plugin has issues" -ForegroundColor Red
        Write-Host "   Content starts with: $($content.Substring(0, 50))" -ForegroundColor Red
    }
} else {
    Write-Host "   ❌ HYIPLab API plugin file not found" -ForegroundColor Red
}

# Test 2: Check CORS Plugin
Write-Host "`n2. Testing CORS Plugin..." -ForegroundColor Yellow
$corsFile = "blackcnote\wp-content\plugins\blackcnote-cors\blackcnote-cors.php"
if (Test-Path $corsFile) {
    $content = Get-Content $corsFile -Raw
    if ($content.StartsWith("<?php`ndeclare(strict_types=1);")) {
        Write-Host "   ✅ CORS plugin is correct" -ForegroundColor Green
    } else {
        Write-Host "   ❌ CORS plugin has issues" -ForegroundColor Red
    }
} else {
    Write-Host "   ❌ CORS plugin file not found" -ForegroundColor Red
}

# Test 3: Check WordPress Accessibility
Write-Host "`n3. Testing WordPress Accessibility..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "   ✅ WordPress is accessible" -ForegroundColor Green
        
        # Check for React container
        $html = $response.Content
        if ($html -match 'id="root" class="blackcnote-react-app"') {
            Write-Host "   ✅ React container found in HTML" -ForegroundColor Green
        } else {
            Write-Host "   ❌ React container not found in HTML" -ForegroundColor Red
        }
        
        # Check for errors in HTML
        if ($html -match 'Warning:|Fatal error:|Parse error:') {
            Write-Host "   ❌ PHP errors found in HTML output" -ForegroundColor Red
            $errors = [regex]::Matches($html, 'Warning:|Fatal error:|Parse error:')
            foreach ($error in $errors) {
                Write-Host "      $($error.Value)" -ForegroundColor Red
            }
        } else {
            Write-Host "   ✅ No PHP errors in HTML output" -ForegroundColor Green
        }
    } else {
        Write-Host "   ❌ WordPress returned status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "   ❌ WordPress not accessible: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Check React Dev Server
Write-Host "`n4. Testing React Dev Server..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 5 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "   ✅ React dev server is accessible" -ForegroundColor Green
    } else {
        Write-Host "   ❌ React dev server returned status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "   ⚠️  React dev server not accessible (may be starting up)" -ForegroundColor Yellow
}

# Test 5: Check Browsersync
Write-Host "`n5. Testing Browsersync..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:3002" -TimeoutSec 5 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "   ✅ Browsersync is accessible on port 3002" -ForegroundColor Green
    } else {
        Write-Host "   ❌ Browsersync returned status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "   ⚠️  Browsersync not accessible on port 3002" -ForegroundColor Yellow
}

# Test 6: Check Docker Containers
Write-Host "`n6. Testing Docker Containers..." -ForegroundColor Yellow
try {
    $containers = docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}"
    if ($containers -match "blackcnote") {
        Write-Host "   ✅ Docker containers are running:" -ForegroundColor Green
        Write-Host $containers -ForegroundColor Gray
    } else {
        Write-Host "   ❌ No BlackCnote Docker containers found" -ForegroundColor Red
    }
} catch {
    Write-Host "   ❌ Docker not accessible: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 7: Check Debug Log
Write-Host "`n7. Testing Debug Log..." -ForegroundColor Yellow
$debugLog = "blackcnote\wp-content\debug.log"
if (Test-Path $debugLog) {
    $recentErrors = Get-Content $debugLog | Select-Object -Last 10
    $strictTypesErrors = $recentErrors | Where-Object { $_ -match "strict_types" }
    $headerErrors = $recentErrors | Where-Object { $_ -match "headers already sent" }
    
    if ($strictTypesErrors) {
        Write-Host "   ❌ Found strict_types errors:" -ForegroundColor Red
        foreach ($error in $strictTypesErrors) {
            Write-Host "      $error" -ForegroundColor Red
        }
    } else {
        Write-Host "   ✅ No strict_types errors found" -ForegroundColor Green
    }
    
    if ($headerErrors) {
        Write-Host "   ❌ Found headers already sent errors:" -ForegroundColor Red
        foreach ($error in $headerErrors) {
            Write-Host "      $error" -ForegroundColor Red
        }
    } else {
        Write-Host "   ✅ No headers already sent errors found" -ForegroundColor Green
    }
} else {
    Write-Host "   ⚠️  Debug log not found" -ForegroundColor Yellow
}

# Summary
Write-Host "`n📋 Test Summary" -ForegroundColor Cyan
Write-Host "=============" -ForegroundColor Cyan
Write-Host "All critical fixes have been tested. Check results above." -ForegroundColor White

# Next Steps
Write-Host "`n🚀 Next Steps:" -ForegroundColor Cyan
Write-Host "1. Clear browser cache (Ctrl+F5)" -ForegroundColor White
Write-Host "2. Open http://localhost:8888" -ForegroundColor White
Write-Host "3. Check browser console for JavaScript errors" -ForegroundColor White
Write-Host "4. If React container missing, check network tab for failed loads" -ForegroundColor White

Write-Host "`n✅ Critical fixes test completed!" -ForegroundColor Green 