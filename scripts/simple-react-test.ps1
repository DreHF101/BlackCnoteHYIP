# Simple BlackCnote React Integration Test
# Version: 1.0.0

Write-Host "Starting BlackCnote React Integration Test..." -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

# Test 1: Check React build files
Write-Host "`nTest 1: Checking React build files..." -ForegroundColor Yellow

$distPath = "blackcnote\wp-content\themes\blackcnote\dist"
$distIndexPath = "$distPath\index.html"
$distAssetsPath = "$distPath\assets"

if (Test-Path $distIndexPath) {
    Write-Host "PASS: React build index.html found" -ForegroundColor Green
    
    $indexContent = Get-Content $distIndexPath -Raw
    if ($indexContent -match '<div id="root">') {
        Write-Host "PASS: React root div found in build" -ForegroundColor Green
    } else {
        Write-Host "FAIL: React root div not found in build" -ForegroundColor Red
    }
} else {
    Write-Host "FAIL: React build index.html not found" -ForegroundColor Red
}

if (Test-Path $distAssetsPath) {
    Write-Host "PASS: React assets directory found" -ForegroundColor Green
    
    $jsFiles = Get-ChildItem "$distAssetsPath\*.js" -ErrorAction SilentlyContinue
    $cssFiles = Get-ChildItem "$distAssetsPath\*.css" -ErrorAction SilentlyContinue
    
    if ($jsFiles) {
        Write-Host "PASS: Found $($jsFiles.Count) JavaScript files" -ForegroundColor Green
    } else {
        Write-Host "FAIL: No JavaScript files found" -ForegroundColor Red
    }
    
    if ($cssFiles) {
        Write-Host "PASS: Found $($cssFiles.Count) CSS files" -ForegroundColor Green
    } else {
        Write-Host "FAIL: No CSS files found" -ForegroundColor Red
    }
} else {
    Write-Host "FAIL: React assets directory not found" -ForegroundColor Red
}

# Test 2: Check theme integration files
Write-Host "`nTest 2: Checking theme integration files..." -ForegroundColor Yellow

$functionsPath = "blackcnote\wp-content\themes\blackcnote\functions.php"
$reactLoaderPath = "blackcnote\wp-content\themes\blackcnote\inc\blackcnote-react-loader.php"
$indexPath = "blackcnote\wp-content\themes\blackcnote\index.php"

if (Test-Path $functionsPath) {
    $functionsContent = Get-Content $functionsPath -Raw
    if ($functionsContent -match 'blackcnote_enqueue_react_app') {
        Write-Host "PASS: React asset enqueuing function found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: React asset enqueuing function not found" -ForegroundColor Red
    }
} else {
    Write-Host "FAIL: functions.php not found" -ForegroundColor Red
}

if (Test-Path $reactLoaderPath) {
    $reactLoaderContent = Get-Content $reactLoaderPath -Raw
    if ($reactLoaderContent -match 'blackcnote_add_react_container') {
        Write-Host "PASS: React container function found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: React container function not found" -ForegroundColor Red
    }
} else {
    Write-Host "FAIL: React loader file not found" -ForegroundColor Red
}

if (Test-Path $indexPath) {
    $indexContent = Get-Content $indexPath -Raw
    if ($indexContent -match 'blackcnote_add_react_container') {
        Write-Host "PASS: React container function call found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: React container function call not found" -ForegroundColor Red
    }
} else {
    Write-Host "FAIL: index.php not found" -ForegroundColor Red
}

# Test 3: Check WordPress accessibility
Write-Host "`nTest 3: Checking WordPress accessibility..." -ForegroundColor Yellow

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 15 -UseBasicParsing
    Write-Host "PASS: WordPress frontend accessible (HTTP $($response.StatusCode))" -ForegroundColor Green
    
    if ($response.Content -match 'blackcnote-react-app') {
        Write-Host "PASS: React app container found in frontend" -ForegroundColor Green
    } else {
        Write-Host "FAIL: React app container not found in frontend" -ForegroundColor Red
    }
    
    if ($response.Content -match 'assets/.*\.js') {
        Write-Host "PASS: React JavaScript assets found in frontend" -ForegroundColor Green
    } else {
        Write-Host "FAIL: React JavaScript assets not found in frontend" -ForegroundColor Red
    }
    
    if ($response.Content -match 'blackCnoteApiSettings') {
        Write-Host "PASS: React API settings found in frontend" -ForegroundColor Green
    } else {
        Write-Host "FAIL: React API settings not found in frontend" -ForegroundColor Red
    }
    
} catch {
    Write-Host "FAIL: WordPress frontend not accessible - $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Check Docker services
Write-Host "`nTest 4: Checking Docker services..." -ForegroundColor Yellow

try {
    $containers = docker ps --filter "name=blackcnote" --format "{{.Names}}" 2>$null
    if ($containers) {
        Write-Host "PASS: Docker containers found:" -ForegroundColor Green
        foreach ($container in $containers) {
            Write-Host "  - $container" -ForegroundColor Green
        }
    } else {
        Write-Host "FAIL: No Docker containers found" -ForegroundColor Red
    }
} catch {
    Write-Host "FAIL: Docker command failed - $($_.Exception.Message)" -ForegroundColor Red
}

# Test 5: Check debug log
Write-Host "`nTest 5: Checking debug log..." -ForegroundColor Yellow

$debugLogPath = "blackcnote\wp-content\debug.log"
if (Test-Path $debugLogPath) {
    $recentLogs = Get-Content $debugLogPath -Tail 10
    $errorLogs = $recentLogs | Where-Object { $_ -match "Warning:|Error:|Fatal error:" }
    
    if ($errorLogs) {
        Write-Host "WARNING: Recent errors in debug log:" -ForegroundColor Yellow
        foreach ($log in $errorLogs) {
            Write-Host "  $($log.Trim())" -ForegroundColor Yellow
        }
    } else {
        Write-Host "PASS: No recent errors in debug log" -ForegroundColor Green
    }
} else {
    Write-Host "INFO: Debug log not found" -ForegroundColor Cyan
}

Write-Host "`n=============================================" -ForegroundColor Cyan
Write-Host "React Integration Test Complete" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan 