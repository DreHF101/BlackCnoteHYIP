# BlackCnote Seamless Integration Test Script
# Tests the complete WordPress/React integration

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "BLACKCNOTE SEAMLESS INTEGRATION TEST" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { Write-ColorOutput $args "Green" }
function Write-Warning { Write-ColorOutput $args "Yellow" }
function Write-Error { Write-ColorOutput $args "Red" }
function Write-Info { Write-ColorOutput $args "Cyan" }

# Test 1: Check WordPress frontend accessibility
Write-Info "Test 1: Checking WordPress frontend accessibility..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Success "✅ WordPress frontend is accessible"
        
        # Check for React app container
        if ($response.Content -match "blackcnote-react-app") {
            Write-Success "✅ React app container found in WordPress frontend"
        } else {
            Write-Warning "⚠️ React app container not found in WordPress frontend"
        }
        
        # Check for React app scripts
        if ($response.Content -match "assets.*\.js") {
            Write-Success "✅ React app scripts found in WordPress frontend"
        } else {
            Write-Warning "⚠️ React app scripts not found in WordPress frontend"
        }
        
        # Check for React app styles
        if ($response.Content -match "assets.*\.css") {
            Write-Success "✅ React app styles found in WordPress frontend"
        } else {
            Write-Warning "⚠️ React app styles not found in WordPress frontend"
        }
        
    } else {
        Write-Error "❌ WordPress frontend returned status code: $($response.StatusCode)"
    }
} catch {
    Write-Error "❌ WordPress frontend not accessible: $($_.Exception.Message)"
}

# Test 2: Check WordPress admin
Write-Info "`nTest 2: Checking WordPress admin..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-admin/" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Success "✅ WordPress admin is accessible"
    } else {
        Write-Error "❌ WordPress admin returned status code: $($response.StatusCode)"
    }
} catch {
    Write-Error "❌ WordPress admin not accessible: $($_.Exception.Message)"
}

# Test 3: Check WordPress REST API
Write-Info "`nTest 3: Checking WordPress REST API..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-json/" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Success "✅ WordPress REST API is accessible"
    } else {
        Write-Error "❌ WordPress REST API returned status code: $($response.StatusCode)"
    }
} catch {
    Write-Error "❌ WordPress REST API not accessible: $($_.Exception.Message)"
}

# Test 4: Check BlackCnote API
Write-Info "`nTest 4: Checking BlackCnote API..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-json/blackcnote/v1/health" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Success "✅ BlackCnote API is accessible"
    } else {
        Write-Error "❌ BlackCnote API returned status code: $($response.StatusCode)"
    }
} catch {
    Write-Error "❌ BlackCnote API not accessible: $($_.Exception.Message)"
}

# Test 5: Check Docker containers
Write-Info "`nTest 5: Checking Docker containers..."
$containers = @("blackcnote-wordpress", "blackcnote-react", "blackcnote-mysql", "blackcnote-phpmyadmin")
foreach ($container in $containers) {
    $status = docker ps --filter "name=$container" --format "table {{.Names}}\t{{.Status}}"
    if ($status -match $container) {
        Write-Success "✅ $container is running"
    } else {
        Write-Error "❌ $container is not running"
    }
}

# Test 6: Check React app files in WordPress theme
Write-Info "`nTest 6: Checking React app files in WordPress theme..."
$themeDistPath = "blackcnote/wp-content/themes/blackcnote/dist"
if (Test-Path $themeDistPath) {
    Write-Success "✅ React dist directory exists in WordPress theme"
    
    $cssFiles = Get-ChildItem -Path "$themeDistPath/assets" -Filter "*.css" -ErrorAction SilentlyContinue
    $jsFiles = Get-ChildItem -Path "$themeDistPath/assets" -Filter "*.js" -ErrorAction SilentlyContinue
    
    if ($cssFiles) {
        Write-Success "✅ React CSS files found: $($cssFiles.Count) files"
    } else {
        Write-Warning "⚠️ No React CSS files found"
    }
    
    if ($jsFiles) {
        Write-Success "✅ React JS files found: $($jsFiles.Count) files"
    } else {
        Write-Warning "⚠️ No React JS files found"
    }
} else {
    Write-Error "❌ React dist directory not found in WordPress theme"
}

# Test 7: Check WordPress theme files
Write-Info "`nTest 7: Checking WordPress theme files..."
$themeFiles = @(
    "blackcnote/wp-content/themes/blackcnote/functions.php",
    "blackcnote/wp-content/themes/blackcnote/index.php",
    "blackcnote/wp-content/themes/blackcnote/inc/blackcnote-react-loader.php"
)

foreach ($file in $themeFiles) {
    if (Test-Path $file) {
        Write-Success "✅ $file exists"
    } else {
        Write-Error "❌ $file not found"
    }
}

# Test 8: Performance test
Write-Info "`nTest 8: Performance test..."
$stopwatch = [System.Diagnostics.Stopwatch]::StartNew()
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -UseBasicParsing -TimeoutSec 10
    $stopwatch.Stop()
    $responseTime = $stopwatch.ElapsedMilliseconds
    
    if ($responseTime -lt 2000) {
        Write-Success "✅ WordPress frontend loads fast: ${responseTime}ms"
    } elseif ($responseTime -lt 5000) {
        Write-Warning "⚠️ WordPress frontend loads slowly: ${responseTime}ms"
    } else {
        Write-Error "❌ WordPress frontend loads very slowly: ${responseTime}ms"
    }
} catch {
    Write-Error "❌ Performance test failed: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "INTEGRATION TEST COMPLETED" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "🌐 Access your BlackCnote application:" -ForegroundColor Cyan
Write-Host "   WordPress Frontend: http://localhost:8888" -ForegroundColor White
Write-Host "   WordPress Admin: http://localhost:8888/wp-admin/" -ForegroundColor White
Write-Host "   phpMyAdmin: http://localhost:8080" -ForegroundColor White
Write-Host ""
Write-Host "If the React app is not loading in WordPress frontend," -ForegroundColor Yellow
Write-Host "check the browser console for JavaScript errors." -ForegroundColor Yellow
Write-Host ""
Write-Host "The React app should now be seamlessly integrated with WordPress!" -ForegroundColor Green 