# BlackCnote React Integration Test Script
# Tests React app integration logic and asset loading
# Version: 1.0.1

param(
    [switch]$Fix,
    [switch]$Verbose,
    [switch]$CheckOnly
)

trap {
    Write-Host "[FATAL] $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host "[INFO] Starting BlackCnote React Integration Test Script..." -ForegroundColor Cyan
Write-Host "[INFO] Script path: $($MyInvocation.MyCommand.Path)" -ForegroundColor Cyan
Write-Host "[INFO] Current directory: $(Get-Location)" -ForegroundColor Cyan

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { Write-ColorOutput $args "Green" }
function Write-Warning { Write-ColorOutput $args "Yellow" }
function Write-Error { Write-ColorOutput $args "Red" }
function Write-Info { Write-ColorOutput $args "Cyan" }

# Set project root
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

Write-Info "=========================================="
Write-Info "BLACKCNOTE REACT INTEGRATION TEST SCRIPT"
Write-Info "=========================================="
Write-Info "Starting React integration test at: $(Get-Date)"
Write-Info ""

# Step 1: Check React build files
Write-Info "Step 1: Checking React build files..."
Write-Info "====================================="

$distPath = "blackcnote\wp-content\themes\blackcnote\dist"
$distIndexPath = "$distPath\index.html"
$distAssetsPath = "$distPath\assets"

if (Test-Path $distIndexPath) {
    Write-Success "✅ React build index.html found"
    
    # Check index.html content
    $indexContent = Get-Content $distIndexPath -Raw
    if ($indexContent -match '<div id="root">') {
        Write-Success "✅ React root div found in build"
    } else {
        Write-Warning "⚠️  React root div not found in build index.html"
    }
} else {
    Write-Error "❌ React build index.html not found at: $distIndexPath"
}

if (Test-Path $distAssetsPath) {
    Write-Success "✅ React assets directory found"
    
    # Check for JS and CSS files
    $jsFiles = Get-ChildItem "$distAssetsPath\*.js" -ErrorAction SilentlyContinue
    $cssFiles = Get-ChildItem "$distAssetsPath\*.css" -ErrorAction SilentlyContinue
    
    if ($jsFiles) {
        Write-Success "✅ Found $($jsFiles.Count) JavaScript files"
        foreach ($file in $jsFiles) {
            Write-Info "   - $($file.Name)"
        }
    } else {
        Write-Warning "⚠️  No JavaScript files found in assets"
    }
    
    if ($cssFiles) {
        Write-Success "✅ Found $($cssFiles.Count) CSS files"
        foreach ($file in $cssFiles) {
            Write-Info "   - $($file.Name)"
        }
    } else {
        Write-Warning "⚠️  No CSS files found in assets"
    }
} else {
    Write-Error "❌ React assets directory not found at: $distAssetsPath"
}

Write-Info ""

# Step 2: Check theme files for React integration logic
Write-Info "Step 2: Checking theme React integration logic..."
Write-Info "=================================================="

# Check functions.php for React asset enqueuing
$functionsPath = "blackcnote\wp-content\themes\blackcnote\functions.php"
if (Test-Path $functionsPath) {
    $functionsContent = Get-Content $functionsPath -Raw
    if ($functionsContent -match 'blackcnote_enqueue_react_app') {
        Write-Success "✅ React asset enqueuing function found in functions.php"
    } else {
        Write-Error "❌ React asset enqueuing function not found in functions.php"
    }
    
    if ($functionsContent -match 'wp_enqueue_script') {
        Write-Success "✅ Script enqueuing found in functions.php"
    } else {
        Write-Warning "⚠️  Script enqueuing not found in functions.php"
    }
    
    if ($functionsContent -match 'blackCnoteApiSettings') {
        Write-Success "✅ React API settings injection found in functions.php"
    } else {
        Write-Warning "⚠️  React API settings injection not found in functions.php"
    }
} else {
    Write-Error "❌ functions.php not found"
}

# Check React loader file
$reactLoaderPath = "blackcnote\wp-content\themes\blackcnote\inc\blackcnote-react-loader.php"
if (Test-Path $reactLoaderPath) {
    $reactLoaderContent = Get-Content $reactLoaderPath -Raw
    if ($reactLoaderContent -match 'blackcnote_add_react_container') {
        Write-Success "✅ React container function found in loader"
    } else {
        Write-Error "❌ React container function not found in loader"
    }
    
    if ($reactLoaderContent -match 'id="root"') {
        Write-Success "✅ React root div found in loader"
    } else {
        Write-Error "❌ React root div not found in loader"
    }
    
    if ($reactLoaderContent -match 'blackcnote-react-app') {
        Write-Success "✅ React app class found in loader"
    } else {
        Write-Error "❌ React app class not found in loader"
    }
} else {
    Write-Error "❌ React loader file not found"
}

Write-Info ""

# Step 3: Check index.php for React container output
Write-Info "Step 3: Checking index.php for React container output..."
Write-Info "========================================================"

$indexPath = "blackcnote\wp-content\themes\blackcnote\index.php"
if (Test-Path $indexPath) {
    $indexContent = Get-Content $indexPath -Raw
    if ($indexContent -match 'blackcnote_add_react_container') {
        Write-Success "✅ React container function call found in index.php"
    } else {
        Write-Error "❌ React container function call not found in index.php"
    }
    
    if ($indexContent -match 'get_header') {
        Write-Success "✅ Header inclusion found in index.php"
    } else {
        Write-Warning "⚠️  Header inclusion not found in index.php"
    }
    
    if ($indexContent -match 'get_footer') {
        Write-Success "✅ Footer inclusion found in index.php"
    } else {
        Write-Warning "⚠️  Footer inclusion not found in index.php"
    }
} else {
    Write-Error "❌ index.php not found"
}

Write-Info ""

# Step 4: Test WordPress frontend and check for React container
Write-Info "Step 4: Testing WordPress frontend for React container..."
Write-Info "========================================================="

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    Write-Success "✅ WordPress frontend accessible (HTTP $($response.StatusCode))"
    
    # Check for React container
    if ($response.Content -match 'blackcnote-react-app') {
        Write-Success "✅ React app container found in frontend"
    } else {
        Write-Error "❌ React app container NOT found in frontend"
        
        # Check for root div
        if ($response.Content -match 'id="root"') {
            Write-Warning "⚠️  Root div found but without blackcnote-react-app class"
        } else {
            Write-Error "❌ Root div not found in frontend"
        }
    }
    
    # Check for React assets
    if ($response.Content -match 'assets/.*\.js') {
        Write-Success "✅ React JavaScript assets found in frontend"
    } else {
        Write-Warning "⚠️  React JavaScript assets not found in frontend"
    }
    
    if ($response.Content -match 'assets/.*\.css') {
        Write-Success "✅ React CSS assets found in frontend"
    } else {
        Write-Warning "⚠️  React CSS assets not found in frontend"
    }
    
    # Check for API settings
    if ($response.Content -match 'blackCnoteApiSettings') {
        Write-Success "✅ React API settings found in frontend"
    } else {
        Write-Warning "⚠️  React API settings not found in frontend"
    }
    
    # Check for PHP errors
    if ($response.Content -match 'Warning:|Error:|Fatal error:') {
        Write-Error "❌ PHP errors found in frontend:"
        $errors = $response.Content -split "`n" | Where-Object { $_ -match "Warning:|Error:|Fatal error:" }
        foreach ($error in $errors[0..4]) { # Show first 5 errors
            Write-Error "  $($error.Trim())"
        }
    } else {
        Write-Success "✅ No PHP errors found in frontend"
    }
    
} catch {
    Write-Error "❌ WordPress frontend not accessible: $($_.Exception.Message)"
}

Write-Info ""

# Step 5: Check WordPress debug log
Write-Info "Step 5: Checking WordPress debug log..."
Write-Info "======================================="

$debugLogPath = "blackcnote\wp-content\debug.log"
if (Test-Path $debugLogPath) {
    $recentLogs = Get-Content $debugLogPath -Tail 20
    $errorLogs = $recentLogs | Where-Object { $_ -match "Warning:|Error:|Fatal error:" }
    
    if ($errorLogs) {
        Write-Warning "⚠️  Recent errors in debug log:"
        foreach ($log in $errorLogs) {
            Write-Warning "  $($log.Trim())"
        }
    } else {
        Write-Success "✅ No recent errors in debug log"
    }
} else {
    Write-Info "ℹ️  Debug log not found (debug logging may be disabled)"
}

Write-Info ""

# Step 6: Test React assets accessibility
Write-Info "Step 6: Testing React assets accessibility..."
Write-Info "============================================="

if (Test-Path $distIndexPath) {
    try {
        $assetResponse = Invoke-WebRequest -Uri "http://localhost:8888/wp-content/themes/blackcnote/dist/index.html" -TimeoutSec 10 -UseBasicParsing
        Write-Success "✅ React build index.html accessible via WordPress (HTTP $($assetResponse.StatusCode))"
    } catch {
        Write-Warning "⚠️  React build index.html not accessible via WordPress: $($_.Exception.Message)"
    }
}

if (Test-Path $distAssetsPath) {
    $jsFiles = Get-ChildItem "$distAssetsPath\*.js" -ErrorAction SilentlyContinue
    if ($jsFiles) {
        $testJsFile = $jsFiles[0]
        try {
            $jsResponse = Invoke-WebRequest -Uri "http://localhost:8888/wp-content/themes/blackcnote/dist/assets/$($testJsFile.Name)" -TimeoutSec 10 -UseBasicParsing
            Write-Success "✅ React JavaScript assets accessible via WordPress (HTTP $($jsResponse.StatusCode))"
        } catch {
            Write-Warning "⚠️  React JavaScript assets not accessible via WordPress: $($_.Exception.Message)"
        }
    }
}

Write-Info ""

# Step 7: Generate recommendations
Write-Info "Step 7: Recommendations"
Write-Info "========================"

Write-Info "🔧 If React container is missing:"
Write-Info "   1. Check if blackcnote_add_react_container function is being called in index.php"
Write-Info "   2. Check if the function is properly defined in blackcnote-react-loader.php"
Write-Info "   3. Check if there are any conditional statements blocking the output"
Write-Info "   4. Check if the theme is properly activated"

Write-Info ""
Write-Info "🔧 If React assets are missing:"
Write-Info "   1. Check if blackcnote_enqueue_react_app function is being called"
Write-Info "   2. Check if the dist directory has the correct files"
Write-Info "   3. Check if wp_enqueue_script calls are working"
Write-Info "   4. Check browser console for 404 errors"

Write-Info ""
Write-Info "🔧 If API settings are missing:"
Write-Info "   1. Check if wp_add_inline_script function is being called"
Write-Info "   2. Check if the main JS handle is correctly identified"
Write-Info "   3. Check if wp_json_encode function is working properly"

Write-Info ""
Write-Info "=========================================="
Write-Info "REACT INTEGRATION TEST COMPLETE"
Write-Info "==========================================" 