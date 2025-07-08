# BlackCnote React Container Diagnostic Script
# Version: 1.0.0

Write-Host "BlackCnote React Container Diagnostic" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

# Test 1: Check if React container is in the HTML source
Write-Host "`nTest 1: Checking HTML source for React container..." -ForegroundColor Yellow

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 15 -UseBasicParsing
    Write-Host "PASS: WordPress frontend accessible" -ForegroundColor Green
    
    # Check for root div
    if ($response.Content -match 'id="root"') {
        Write-Host "PASS: Root div found in HTML source" -ForegroundColor Green
        
        # Extract the root div content
        $rootMatch = [regex]::Match($response.Content, '<div id="root"[^>]*>(.*?)</div>', [System.Text.RegularExpressions.RegexOptions]::Singleline)
        if ($rootMatch.Success) {
            Write-Host "PASS: Root div content found:" -ForegroundColor Green
            Write-Host "Content: $($rootMatch.Groups[1].Value.Trim())" -ForegroundColor Gray
        } else {
            Write-Host "FAIL: Root div content not found" -ForegroundColor Red
        }
    } else {
        Write-Host "FAIL: Root div not found in HTML source" -ForegroundColor Red
    }
    
    # Check for blackcnote-react-app class
    if ($response.Content -match 'class="[^"]*blackcnote-react-app[^"]*"') {
        Write-Host "PASS: blackcnote-react-app class found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: blackcnote-react-app class not found" -ForegroundColor Red
    }
    
    # Check for React assets
    if ($response.Content -match 'assets/.*\.js') {
        Write-Host "PASS: React JavaScript assets found" -ForegroundColor Green
        $jsMatches = [regex]::Matches($response.Content, 'assets/[^"]*\.js')
        foreach ($match in $jsMatches) {
            Write-Host "  JS: $($match.Value)" -ForegroundColor Gray
        }
    } else {
        Write-Host "FAIL: React JavaScript assets not found" -ForegroundColor Red
    }
    
    # Check for API settings
    if ($response.Content -match 'blackCnoteApiSettings') {
        Write-Host "PASS: React API settings found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: React API settings not found" -ForegroundColor Red
    }
    
    # Check for any PHP errors
    if ($response.Content -match 'Warning:|Error:|Fatal error:') {
        Write-Host "FAIL: PHP errors found in output" -ForegroundColor Red
        $errors = $response.Content -split "`n" | Where-Object { $_ -match "Warning:|Error:|Fatal error:" }
        foreach ($error in $errors[0..2]) {
            Write-Host "  $($error.Trim())" -ForegroundColor Red
        }
    } else {
        Write-Host "PASS: No PHP errors found" -ForegroundColor Green
    }
    
} catch {
    Write-Host "FAIL: WordPress frontend not accessible - $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: Check if React assets are accessible
Write-Host "`nTest 2: Checking React assets accessibility..." -ForegroundColor Yellow

$distPath = "blackcnote\wp-content\themes\blackcnote\dist"
if (Test-Path "$distPath\index.html") {
    try {
        $assetResponse = Invoke-WebRequest -Uri "http://localhost:8888/wp-content/themes/blackcnote/dist/index.html" -TimeoutSec 10 -UseBasicParsing
        Write-Host "PASS: React build index.html accessible via WordPress" -ForegroundColor Green
    } catch {
        Write-Host "FAIL: React build index.html not accessible via WordPress" -ForegroundColor Red
    }
}

$jsFiles = Get-ChildItem "$distPath\assets\*.js" -ErrorAction SilentlyContinue
if ($jsFiles) {
    $testJsFile = $jsFiles[0]
    try {
        $jsResponse = Invoke-WebRequest -Uri "http://localhost:8888/wp-content/themes/blackcnote/dist/assets/$($testJsFile.Name)" -TimeoutSec 10 -UseBasicParsing
        Write-Host "PASS: React JavaScript assets accessible via WordPress" -ForegroundColor Green
    } catch {
        Write-Host "FAIL: React JavaScript assets not accessible via WordPress" -ForegroundColor Red
    }
}

# Test 3: Check theme activation
Write-Host "`nTest 3: Checking theme activation..." -ForegroundColor Yellow

try {
    $adminResponse = Invoke-WebRequest -Uri "http://localhost:8888/wp-admin/" -TimeoutSec 10 -UseBasicParsing
    if ($adminResponse.Content -match 'blackcnote') {
        Write-Host "PASS: BlackCnote theme appears to be active" -ForegroundColor Green
    } else {
        Write-Host "WARNING: BlackCnote theme may not be active" -ForegroundColor Yellow
    }
} catch {
    Write-Host "FAIL: Cannot access WordPress admin" -ForegroundColor Red
}

# Test 4: Check browser console simulation
Write-Host "`nTest 4: Checking for potential JavaScript issues..." -ForegroundColor Yellow

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 15 -UseBasicParsing
    
    # Check for common JavaScript issues
    if ($response.Content -match 'console\.error') {
        Write-Host "WARNING: Console errors may be present" -ForegroundColor Yellow
    }
    
    if ($response.Content -match '404') {
        Write-Host "WARNING: 404 errors may be present" -ForegroundColor Yellow
    }
    
    if ($response.Content -match 'CORS') {
        Write-Host "WARNING: CORS errors may be present" -ForegroundColor Yellow
    }
    
    # Check if React is trying to initialize
    if ($response.Content -match 'DOMContentLoaded') {
        Write-Host "PASS: DOMContentLoaded event listener found" -ForegroundColor Green
    } else {
        Write-Host "FAIL: DOMContentLoaded event listener not found" -ForegroundColor Red
    }
    
} catch {
    Write-Host "FAIL: Cannot check for JavaScript issues" -ForegroundColor Red
}

Write-Host "`n=====================================" -ForegroundColor Cyan
Write-Host "Diagnostic Complete" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

Write-Host "`nNext Steps:" -ForegroundColor Yellow
Write-Host "1. Open http://localhost:8888 in your browser" -ForegroundColor White
Write-Host "2. Open browser Developer Tools (F12)" -ForegroundColor White
Write-Host "3. Check Console tab for JavaScript errors" -ForegroundColor White
Write-Host "4. Check Network tab for failed requests" -ForegroundColor White
Write-Host "5. Check Elements tab to see if React container is present" -ForegroundColor White 