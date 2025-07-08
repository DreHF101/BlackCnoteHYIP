#!/usr/bin/env pwsh

Write-Host "🔧 BlackCnote Comprehensive Test" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# Test 1: Check WordPress for Headers Error
Write-Host "`n1. Testing WordPress Headers Error..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        $html = $response.Content
        if ($html -match "headers already sent") {
            Write-Host "   ❌ Headers already sent error found" -ForegroundColor Red
        } else {
            Write-Host "   ✅ No headers already sent error" -ForegroundColor Green
        }
        
        if ($html -match "blackcnote-react-app") {
            Write-Host "   ✅ React container found" -ForegroundColor Green
        } else {
            Write-Host "   ❌ React container not found" -ForegroundColor Red
        }
    } else {
        Write-Host "   ❌ WordPress returned status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "   ❌ WordPress not accessible: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: Check React Dev Server
Write-Host "`n2. Testing React Dev Server..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5176" -TimeoutSec 5 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "   ✅ React dev server accessible on port 5176" -ForegroundColor Green
    } else {
        Write-Host "   ❌ React dev server returned status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "   ⚠️  React dev server not accessible on port 5176" -ForegroundColor Yellow
}

# Test 3: Check Browsersync
Write-Host "`n3. Testing Browsersync..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:3006" -TimeoutSec 5 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "   ✅ Browsersync accessible on port 3006" -ForegroundColor Green
    } else {
        Write-Host "   ❌ Browsersync returned status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "   ⚠️  Browsersync not accessible on port 3006" -ForegroundColor Yellow
}

# Test 4: Check Docker Containers
Write-Host "`n4. Testing Docker Containers..." -ForegroundColor Yellow
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

# Test 5: Check PHP Files for BOM Issues
Write-Host "`n5. Testing PHP Files for BOM Issues..." -ForegroundColor Yellow
$phpFiles = @(
    "blackcnote\wp-content\themes\blackcnote\functions.php",
    "blackcnote\wp-content\themes\blackcnote\inc\menu-registration.php",
    "blackcnote\wp-content\themes\blackcnote\inc\full-content-checker.php",
    "blackcnote\wp-content\themes\blackcnote\inc\blackcnote-react-loader.php",
    "blackcnote\wp-content\plugins\blackcnote-cors\blackcnote-cors.php",
    "blackcnote\wp-content\plugins\blackcnote-hyiplab-api\blackcnote-hyiplab-api.php"
)

$bomIssues = 0
foreach ($file in $phpFiles) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw
        if ($content.StartsWith("<?php`ndeclare(strict_types=1);")) {
            Write-Host "   ✅ $($file.Split('\')[-1])" -ForegroundColor Green
        } else {
            Write-Host "   ❌ $($file.Split('\')[-1]) - BOM issue" -ForegroundColor Red
            $bomIssues++
        }
    } else {
        Write-Host "   ⚠️  $($file.Split('\')[-1]) - File not found" -ForegroundColor Yellow
    }
}

if ($bomIssues -eq 0) {
    Write-Host "   ✅ All PHP files are correct" -ForegroundColor Green
} else {
    Write-Host "   ❌ $bomIssues PHP files have BOM issues" -ForegroundColor Red
}

# Test 6: Check Port Usage
Write-Host "`n6. Testing Port Usage..." -ForegroundColor Yellow
$ports = @(8888, 5176, 3006, 3007)
foreach ($port in $ports) {
    $result = netstat -ano | findstr ":$port"
    if ($result) {
        Write-Host "   ✅ Port $port is in use" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  Port $port is not in use" -ForegroundColor Yellow
    }
}

# Summary
Write-Host "`n📋 Test Summary" -ForegroundColor Cyan
Write-Host "=============" -ForegroundColor Cyan
Write-Host "All critical tests completed. Check results above for any issues." -ForegroundColor White

# Next Steps
Write-Host "`n🚀 Next Steps:" -ForegroundColor Cyan
Write-Host "1. Clear browser cache (Ctrl+F5)" -ForegroundColor White
Write-Host "2. Open http://localhost:8888" -ForegroundColor White
Write-Host "3. Check browser console for JavaScript errors" -ForegroundColor White
Write-Host "4. If React container missing, check network tab for failed loads" -ForegroundColor White

Write-Host "`n✅ Comprehensive test completed!" -ForegroundColor Green 