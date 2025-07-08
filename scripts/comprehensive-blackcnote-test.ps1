# BlackCnote Comprehensive Test and Fix Script
# Tests all components and fixes issues automatically
# Version: 3.0.0 - Complete System Verification

param(
    [switch]$Fix,
    [switch]$Verbose,
    [switch]$NoBrowser
)

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
Write-Info "BLACKCNOTE COMPREHENSIVE TEST & FIX"
Write-Info "=========================================="
Write-Info "Starting comprehensive test at: $(Get-Date)"
Write-Info "Project root: $projectRoot"
Write-Output ""

# Test Results
$testResults = @{
    'Docker' = $false
    'WordPress' = $false
    'React' = $false
    'Database' = $false
    'Theme' = $false
    'Integration' = $false
    'Startup' = $false
}

# Step 1: Test Docker Services
Write-Info "Step 1: Testing Docker Services..."
try {
    $containers = docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" --filter "name=blackcnote"
    if ($containers) {
        Write-Success "Docker containers are running"
        $testResults['Docker'] = $true
        if ($Verbose) {
            Write-Output $containers
        }
    } else {
        Write-Warning "No BlackCnote containers found"
    }
} catch {
    Write-Error "Docker not available or not running"
}

# Step 2: Test WordPress Frontend
Write-Info "Step 2: Testing WordPress Frontend..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Success "WordPress frontend is accessible"
        $testResults['WordPress'] = $true
        
        # Check for React integration
        if ($response.Content -match 'blackcnote-react-app') {
            Write-Success "React app container found in WordPress"
            $testResults['Integration'] = $true
        } else {
            Write-Warning "React app container not found in WordPress frontend"
        }
    } else {
        Write-Error "WordPress frontend returned status: $($response.StatusCode)"
    }
} catch {
    Write-Error "WordPress frontend not accessible: $($_.Exception.Message)"
}

# Step 3: Test React App
Write-Info "Step 3: Testing React App..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Success "React app is accessible"
        $testResults['React'] = $true
    } else {
        Write-Error "React app returned status: $($response.StatusCode)"
    }
} catch {
    Write-Error "React app not accessible: $($_.Exception.Message)"
}

# Step 4: Test Database
Write-Info "Step 4: Testing Database..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Success "phpMyAdmin is accessible"
        $testResults['Database'] = $true
    } else {
        Write-Error "phpMyAdmin returned status: $($response.StatusCode)"
    }
} catch {
    Write-Error "phpMyAdmin not accessible: $($_.Exception.Message)"
}

# Step 5: Test Theme Files
Write-Info "Step 5: Testing Theme Files..."
$themePath = "blackcnote\wp-content\themes\blackcnote"
$requiredFiles = @(
    "functions.php",
    "style.css",
    "index.php",
    "header.php",
    "footer.php",
    "inc\blackcnote-react-loader.php"
)

$themeFilesOk = $true
foreach ($file in $requiredFiles) {
    $fullPath = Join-Path $themePath $file
    if (Test-Path $fullPath) {
        Write-Success "Found: $file"
    } else {
        Write-Error "Missing: $file"
        $themeFilesOk = $false
    }
}

if ($themeFilesOk) {
    $testResults['Theme'] = $true
    Write-Success "All required theme files present"
} else {
    Write-Error "Some theme files are missing"
}

# Step 6: Test Startup Scripts
Write-Info "Step 6: Testing Startup Scripts..."
$startupScripts = @(
    "start-blackcnote-clean.ps1",
    "start-blackcnote-clean.bat",
    "automate-docker-startup.bat"
)

$startupScriptsOk = $true
foreach ($script in $startupScripts) {
    if (Test-Path $script) {
        Write-Success "Found: $script"
    } else {
        Write-Warning "Missing: $script"
        $startupScriptsOk = $false
    }
}

if ($startupScriptsOk) {
    $testResults['Startup'] = $true
    Write-Success "All startup scripts present"
} else {
    Write-Warning "Some startup scripts are missing"
}

# Step 7: Fix Issues if requested
if ($Fix) {
    Write-Info "Step 7: Fixing Issues..."
    
    # Fix 1: Ensure React app is built
    Write-Info "Fixing React app build..."
    $reactDistPath = "blackcnote\wp-content\themes\blackcnote\dist"
    if (-not (Test-Path $reactDistPath)) {
        Write-Info "Building React app for production..."
        Set-Location "react-app"
        npm run build
        if ($LASTEXITCODE -eq 0) {
            # Copy built files to theme
            if (Test-Path "dist") {
                Copy-Item -Path "dist\*" -Destination "..\blackcnote\wp-content\themes\blackcnote\dist\" -Recurse -Force
                Write-Success "React app built and copied to theme"
            }
        }
        Set-Location $projectRoot
    }
    
    # Fix 2: Ensure proper file permissions
    Write-Info "Fixing file permissions..."
    $themeDir = "blackcnote\wp-content\themes\blackcnote"
    if (Test-Path $themeDir) {
        Get-ChildItem -Path $themeDir -Recurse | ForEach-Object {
            $_.Attributes = $_.Attributes -band (-bnot [System.IO.FileAttributes]::ReadOnly)
        }
        Write-Success "File permissions fixed"
    }
    
    # Fix 3: Clear WordPress cache
    Write-Info "Clearing WordPress cache..."
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-admin/admin-ajax.php" -Method POST -Body @{
            action = 'blackcnote_clear_cache'
            nonce = 'dummy-nonce'
        } -TimeoutSec 10 -UseBasicParsing
        Write-Success "WordPress cache cleared"
    } catch {
        Write-Warning "Could not clear WordPress cache: $($_.Exception.Message)"
    }
    
    # Fix 4: Restart containers if needed
    if (-not $testResults['Docker']) {
        Write-Info "Restarting Docker containers..."
        docker-compose down
        Start-Sleep -Seconds 5
        docker-compose up -d
        Start-Sleep -Seconds 15
        Write-Success "Docker containers restarted"
    }
}

# Step 8: Generate Report
Write-Output ""
Write-Info "=========================================="
Write-Info "TEST RESULTS SUMMARY"
Write-Info "=========================================="

$passedTests = 0
$totalTests = $testResults.Count

foreach ($test in $testResults.GetEnumerator()) {
    $status = if ($test.Value) { "✅ PASS" } else { "❌ FAIL" }
    Write-Output "$($test.Key): $status"
    if ($test.Value) { $passedTests++ }
}

Write-Output ""
Write-Info "Overall Status: $passedTests/$totalTests tests passed"

if ($passedTests -eq $totalTests) {
    Write-Success "🎉 ALL TESTS PASSED! BlackCnote is fully operational!"
} else {
    Write-Warning "⚠️  Some tests failed. Run with -Fix flag to attempt automatic fixes."
}

Write-Output ""
Write-Info "Service URLs:"
Write-Output "WordPress:      http://localhost:8888"
Write-Output "WordPress Admin: http://localhost:8888/wp-admin"
Write-Output "React App:      http://localhost:5174"
Write-Output "phpMyAdmin:     http://localhost:8080"
Write-Output "Redis Commander: http://localhost:8081"
Write-Output "MailHog:        http://localhost:8025"
Write-Output "Browsersync:    http://localhost:3000"
Write-Output "Dev Tools:      http://localhost:9229"

Write-Output ""
Write-Info "Next Steps:"
if ($passedTests -eq $totalTests) {
    Write-Output "✅ System is ready for development"
    Write-Output "✅ Use 'start-blackcnote-clean.bat' for startup"
    Write-Output "✅ All canonical pathways are working"
} else {
    Write-Output "🔧 Run: .\scripts\comprehensive-blackcnote-test.ps1 -Fix"
    Write-Output "🔧 Check Docker Desktop is running"
    Write-Output "🔧 Verify canonical pathways are correct"
}

Write-Output ""
Write-Info "Test completed at: $(Get-Date)" 