#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Basic BlackCnote System Activation Test
.DESCRIPTION
    Basic test script to verify BlackCnote system components are activated and functioning.
#>

# Set error action preference
$ErrorActionPreference = "Continue"

# Test results tracking
$TestResults = @{
    Total = 0
    Passed = 0
    Failed = 0
    Warnings = 0
}

function Add-TestResult {
    param($TestName, $Passed, $Message, $Details = "")
    $TestResults.Total++
    
    if ($Passed) {
        $TestResults.Passed++
        Write-Host "[PASS] $TestName - $Message" -ForegroundColor Green
    } else {
        $TestResults.Failed++
        Write-Host "[FAIL] $TestName - $Message" -ForegroundColor Red
    }
    
    if ($Details) {
        Write-Host "   Details: $Details" -ForegroundColor Gray
    }
}

function Add-TestWarning {
    param($TestName, $Message, $Details = "")
    $TestResults.Warnings++
    Write-Host "[WARN] $TestName - $Message" -ForegroundColor Yellow
    
    if ($Details) {
        Write-Host "   Details: $Details" -ForegroundColor Gray
    }
}

# Canonical paths
$CanonicalPaths = @{
    ProjectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
    WordPress = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote"
    Theme = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote"
    ReactApp = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app"
    Scripts = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\scripts"
}

# Service URLs
$ServiceUrls = @{
    WordPress = "http://localhost:8888"
    ReactApp = "http://localhost:5174"
    phpMyAdmin = "http://localhost:8080"
    RedisCommander = "http://localhost:8081"
    MailHog = "http://localhost:8025"
    Browsersync = "http://localhost:3000"
    DevTools = "http://localhost:9229"
}

Write-Host ""
Write-Host "BLACKCNOTE AUTOMATED ACTIVATION TEST" -ForegroundColor Magenta
Write-Host "Starting comprehensive system activation test..." -ForegroundColor White
Write-Host "Timestamp: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Gray

# Test 1: Canonical Paths Verification
Write-Host ""
Write-Host "TESTING CANONICAL PATHS" -ForegroundColor Magenta
foreach ($pathName in $CanonicalPaths.Keys) {
    $path = $CanonicalPaths[$pathName]
    $exists = Test-Path $path
    Add-TestResult -TestName "Canonical Path: $pathName" -Passed $exists -Message "Path verification" -Details $path
}

# Test 2: Docker Services
Write-Host ""
Write-Host "TESTING DOCKER SERVICES" -ForegroundColor Magenta

# Check if Docker is running
try {
    $dockerInfo = docker info 2>$null
    if ($LASTEXITCODE -eq 0) {
        Add-TestResult -TestName "Docker Engine" -Passed $true -Message "Docker is running"
        
        # Check BlackCnote containers
        $containers = docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" 2>$null
        if ($containers) {
            Add-TestResult -TestName "BlackCnote Containers" -Passed $true -Message "Containers are running"
            Write-Host "   Running containers:" -ForegroundColor Gray
            $containers | ForEach-Object { Write-Host "   $_" -ForegroundColor Gray }
        } else {
            Add-TestResult -TestName "BlackCnote Containers" -Passed $false -Message "No BlackCnote containers found"
        }
    } else {
        Add-TestResult -TestName "Docker Engine" -Passed $false -Message "Docker is not running"
    }
} catch {
    Add-TestResult -TestName "Docker Engine" -Passed $false -Message "Docker check failed" -Details $_.Exception.Message
}

# Test 3: Service Connectivity
Write-Host ""
Write-Host "TESTING SERVICE CONNECTIVITY" -ForegroundColor Magenta
foreach ($serviceName in $ServiceUrls.Keys) {
    $url = $ServiceUrls[$serviceName]
    try {
        $response = Invoke-WebRequest -Uri $url -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
        $statusCode = $response.StatusCode
        $isSuccess = $statusCode -eq 200 -or $statusCode -eq 302 -or $statusCode -eq 301
        
        if ($isSuccess) {
            Add-TestResult -TestName "Service: $serviceName" -Passed $true -Message "Service is accessible (HTTP $statusCode)" -Details $url
        } else {
            Add-TestWarning -TestName "Service: $serviceName" -Message "Service responded with HTTP $statusCode" -Details $url
        }
    } catch {
        Add-TestResult -TestName "Service: $serviceName" -Passed $false -Message "Service is not accessible" -Details "$url - $($_.Exception.Message)"
    }
}

# Test 4: WordPress Configuration
Write-Host ""
Write-Host "TESTING WORDPRESS CONFIGURATION" -ForegroundColor Magenta
$wpConfigPath = Join-Path $CanonicalPaths.WordPress "wp-config.php"
if (Test-Path $wpConfigPath) {
    Add-TestResult -TestName "WordPress Config" -Passed $true -Message "wp-config.php exists"
    
    # Check if WordPress is properly configured
    try {
        $wpResponse = Invoke-WebRequest -Uri "$($ServiceUrls.WordPress)/wp-admin/" -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
        if ($wpResponse.StatusCode -eq 200) {
            Add-TestResult -TestName "WordPress Admin" -Passed $true -Message "WordPress admin is accessible"
        } else {
            Add-TestWarning -TestName "WordPress Admin" -Message "WordPress admin responded with HTTP $($wpResponse.StatusCode)"
        }
    } catch {
        Add-TestWarning -TestName "WordPress Admin" -Message "WordPress admin check failed" -Details $_.Exception.Message
    }
} else {
    Add-TestResult -TestName "WordPress Config" -Passed $false -Message "wp-config.php not found"
}

# Test 5: Theme Activation
Write-Host ""
Write-Host "TESTING THEME ACTIVATION" -ForegroundColor Magenta
$themePath = $CanonicalPaths.Theme
if (Test-Path $themePath) {
    Add-TestResult -TestName "Theme Directory" -Passed $true -Message "BlackCnote theme directory exists"
    
    # Check essential theme files
    $essentialFiles = @("style.css", "index.php", "functions.php", "header.php", "footer.php")
    foreach ($file in $essentialFiles) {
        $filePath = Join-Path $themePath $file
        $exists = Test-Path $filePath
        Add-TestResult -TestName "Theme File: $file" -Passed $exists -Message "Theme file verification"
    }
} else {
    Add-TestResult -TestName "Theme Directory" -Passed $false -Message "BlackCnote theme directory not found"
}

# Test 6: Plugin Activation
Write-Host ""
Write-Host "TESTING PLUGIN ACTIVATION" -ForegroundColor Magenta
$pluginsPath = Join-Path $CanonicalPaths.WordPress "wp-content\plugins"
if (Test-Path $pluginsPath) {
    Add-TestResult -TestName "Plugins Directory" -Passed $true -Message "WordPress plugins directory exists"
    
    # Check for BlackCnote plugins
    $blackcnotePlugins = @("blackcnote-debug-system", "hyiplab", "full-content-checker")
    foreach ($plugin in $blackcnotePlugins) {
        $pluginPath = Join-Path $pluginsPath $plugin
        $exists = Test-Path $pluginPath
        Add-TestResult -TestName "Plugin: $plugin" -Passed $exists -Message "Plugin directory verification"
    }
} else {
    Add-TestResult -TestName "Plugins Directory" -Passed $false -Message "WordPress plugins directory not found"
}

# Test 7: React App
Write-Host ""
Write-Host "TESTING REACT APP" -ForegroundColor Magenta
$reactAppPath = $CanonicalPaths.ReactApp
if (Test-Path $reactAppPath) {
    Add-TestResult -TestName "React App Directory" -Passed $true -Message "React app directory exists"
    
    # Check essential React files
    $reactFiles = @("package.json", "src\App.tsx", "src\main.tsx", "index.html")
    foreach ($file in $reactFiles) {
        $filePath = Join-Path $reactAppPath $file
        $exists = Test-Path $filePath
        Add-TestResult -TestName "React File: $file" -Passed $exists -Message "React file verification"
    }
} else {
    Add-TestResult -TestName "React App Directory" -Passed $false -Message "React app directory not found"
}

# Test 8: REST API Endpoints
Write-Host ""
Write-Host "TESTING REST API ENDPOINTS" -ForegroundColor Magenta
$apiEndpoints = @(
    "wp-json/wp/v2/posts",
    "wp-json/wp/v2/pages", 
    "wp-json/blackcnote/v1/homepage",
    "wp-json/blackcnote/v1/plans"
)

foreach ($endpoint in $apiEndpoints) {
    try {
        $apiResponse = Invoke-WebRequest -Uri "$($ServiceUrls.WordPress)/$endpoint" -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
        if ($apiResponse.StatusCode -eq 200) {
            Add-TestResult -TestName "API: $endpoint" -Passed $true -Message "API endpoint is accessible"
        } else {
            Add-TestWarning -TestName "API: $endpoint" -Message "API endpoint responded with HTTP $($apiResponse.StatusCode)"
        }
    } catch {
        Add-TestWarning -TestName "API: $endpoint" -Message "API endpoint check failed" -Details $_.Exception.Message
    }
}

# Generate Test Summary
Write-Host ""
Write-Host "TEST SUMMARY" -ForegroundColor Magenta
Write-Host "============" -ForegroundColor Magenta
Write-Host "Total Tests: $($TestResults.Total)" -ForegroundColor White
Write-Host "PASSED: $($TestResults.Passed)" -ForegroundColor Green
Write-Host "FAILED: $($TestResults.Failed)" -ForegroundColor Red
Write-Host "WARNINGS: $($TestResults.Warnings)" -ForegroundColor Yellow

# Calculate success rate
$successRate = if ($TestResults.Total -gt 0) { [math]::Round(($TestResults.Passed / $TestResults.Total) * 100, 2) } else { 0 }

Write-Host ""
Write-Host "Success Rate: $successRate%" -ForegroundColor $(if ($successRate -ge 90) { "Green" } elseif ($successRate -ge 75) { "Yellow" } else { "Red" })

# Overall status
if ($TestResults.Failed -eq 0 -and $successRate -ge 90) {
    Write-Host ""
    Write-Host "SYSTEM STATUS: FULLY OPERATIONAL" -ForegroundColor Green
    Write-Host "All critical components are activated and functioning properly." -ForegroundColor Green
} elseif ($TestResults.Failed -eq 0) {
    Write-Host ""
    Write-Host "SYSTEM STATUS: OPERATIONAL WITH WARNINGS" -ForegroundColor Yellow
    Write-Host "System is operational but some components have warnings." -ForegroundColor Yellow
} else {
    Write-Host ""
    Write-Host "SYSTEM STATUS: ISSUES DETECTED" -ForegroundColor Red
    Write-Host "Some critical components failed activation tests." -ForegroundColor Red
}

# Recommendations
Write-Host ""
Write-Host "Recommendations:" -ForegroundColor Cyan
if ($TestResults.Failed -gt 0) {
    Write-Host "Review failed tests and fix issues" -ForegroundColor White
}
if ($TestResults.Warnings -gt 0) {
    Write-Host "Address warnings to improve system reliability" -ForegroundColor White
}
Write-Host "Run this test regularly to monitor system health" -ForegroundColor White
Write-Host "Check logs for detailed error information" -ForegroundColor White

Write-Host ""
Write-Host "Automated activation test completed!" -ForegroundColor Magenta 