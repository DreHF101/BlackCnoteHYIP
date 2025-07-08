#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Automated BlackCnote System Activation Test
.DESCRIPTION
    Comprehensive test script to verify all BlackCnote system components are properly activated and functioning.
    Tests WordPress, React app, plugins, live sync, and all services.
.PARAMETER Verbose
    Enable verbose output for detailed testing information
.PARAMETER SkipDocker
    Skip Docker service checks (useful for testing without Docker)
.EXAMPLE
    .\automated-activation-test.ps1
.EXAMPLE
    .\automated-activation-test.ps1 -Verbose
#>

param(
    [switch]$Verbose,
    [switch]$SkipDocker
)

# Set error action preference
$ErrorActionPreference = "Continue"

# Color functions for output
function Write-Success { param($Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Error { param($Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Warning { param($Message) Write-Host "⚠️ $Message" -ForegroundColor Yellow }
function Write-Info { param($Message) Write-Host "ℹ️ $Message" -ForegroundColor Cyan }
function Write-Header { param($Message) Write-Host "`n🔍 $Message" -ForegroundColor Magenta }

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
        Write-Success "$TestName - $Message"
    } else {
        $TestResults.Failed++
        Write-Error "$TestName - $Message"
    }
    
    if ($Details -and $Verbose) {
        Write-Host "   Details: $Details" -ForegroundColor Gray
    }
}

function Add-TestWarning {
    param($TestName, $Message, $Details = "")
    $TestResults.Warnings++
    Write-Warning "$TestName - $Message"
    
    if ($Details -and $Verbose) {
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

Write-Header "BLACKCNOTE AUTOMATED ACTIVATION TEST"
Write-Host "Starting comprehensive system activation test..." -ForegroundColor White
Write-Host "Timestamp: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Gray

# Test 1: Canonical Paths Verification
Write-Header "TESTING CANONICAL PATHS"
foreach ($pathName in $CanonicalPaths.Keys) {
    $path = $CanonicalPaths[$pathName]
    $exists = Test-Path $path
    Add-TestResult -TestName "Canonical Path: $pathName" -Passed $exists -Message "Path verification" -Details $path
}

# Test 2: Docker Services (if not skipped)
if (-not $SkipDocker) {
    Write-Header "TESTING DOCKER SERVICES"
    
    # Check if Docker is running
    try {
        $dockerInfo = docker info 2>$null
        if ($LASTEXITCODE -eq 0) {
            Add-TestResult -TestName "Docker Engine" -Passed $true -Message "Docker is running"
            
            # Check BlackCnote containers
            $containers = docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" 2>$null
            if ($containers) {
                Add-TestResult -TestName "BlackCnote Containers" -Passed $true -Message "Containers are running"
                if ($Verbose) {
                    Write-Host "   Running containers:" -ForegroundColor Gray
                    $containers | ForEach-Object { Write-Host "   $_" -ForegroundColor Gray }
                }
            } else {
                Add-TestResult -TestName "BlackCnote Containers" -Passed $false -Message "No BlackCnote containers found"
            }
        } else {
            Add-TestResult -TestName "Docker Engine" -Passed $false -Message "Docker is not running"
        }
    } catch {
        Add-TestResult -TestName "Docker Engine" -Passed $false -Message "Docker check failed" -Details $_.Exception.Message
    }
} else {
    Write-Info "Skipping Docker service checks as requested"
}

# Test 3: Service Connectivity
Write-Header "TESTING SERVICE CONNECTIVITY"
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
Write-Header "TESTING WORDPRESS CONFIGURATION"
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
Write-Header "TESTING THEME ACTIVATION"
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
    
    # Check if theme is active via WordPress API
    try {
        $themeResponse = Invoke-WebRequest -Uri "$($ServiceUrls.WordPress)/wp-json/wp/v2/themes" -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
        if ($themeResponse.StatusCode -eq 200) {
            Add-TestResult -TestName "Theme API" -Passed $true -Message "WordPress theme API is accessible"
        } else {
            Add-TestWarning -TestName "Theme API" -Message "Theme API responded with HTTP $($themeResponse.StatusCode)"
        }
    } catch {
        Add-TestWarning -TestName "Theme API" -Message "Theme API check failed" -Details $_.Exception.Message
    }
} else {
    Add-TestResult -TestName "Theme Directory" -Passed $false -Message "BlackCnote theme directory not found"
}

# Test 6: Plugin Activation
Write-Header "TESTING PLUGIN ACTIVATION"
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
    
    # Check plugin API
    try {
        $pluginResponse = Invoke-WebRequest -Uri "$($ServiceUrls.WordPress)/wp-json/wp/v2/plugins" -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
        if ($pluginResponse.StatusCode -eq 200) {
            Add-TestResult -TestName "Plugin API" -Passed $true -Message "WordPress plugin API is accessible"
        } else {
            Add-TestWarning -TestName "Plugin API" -Message "Plugin API responded with HTTP $($pluginResponse.StatusCode)"
        }
    } catch {
        Add-TestWarning -TestName "Plugin API" -Message "Plugin API check failed" -Details $_.Exception.Message
    }
} else {
    Add-TestResult -TestName "Plugins Directory" -Passed $false -Message "WordPress plugins directory not found"
}

# Test 7: React App
Write-Header "TESTING REACT APP"
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
    
    # Check React app accessibility
    try {
        $reactResponse = Invoke-WebRequest -Uri $ServiceUrls.ReactApp -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
        if ($reactResponse.StatusCode -eq 200) {
            Add-TestResult -TestName "React App" -Passed $true -Message "React app is accessible"
        } else {
            Add-TestWarning -TestName "React App" -Message "React app responded with HTTP $($reactResponse.StatusCode)"
        }
    } catch {
        Add-TestWarning -TestName "React App" -Message "React app check failed" -Details $_.Exception.Message
    }
} else {
    Add-TestResult -TestName "React App Directory" -Passed $false -Message "React app directory not found"
}

# Test 8: Live Sync API
Write-Header "TESTING LIVE SYNC API"
try {
    $liveSyncResponse = Invoke-WebRequest -Uri "$($ServiceUrls.WordPress)/wp-json/blackcnote/v1/live-sync/status" -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
    if ($liveSyncResponse.StatusCode -eq 200) {
        $liveSyncData = $liveSyncResponse.Content | ConvertFrom-Json
        if ($liveSyncData.enabled) {
            Add-TestResult -TestName "Live Sync API" -Passed $true -Message "Live sync is enabled and accessible"
        } else {
            Add-TestWarning -TestName "Live Sync API" -Message "Live sync API accessible but disabled"
        }
    } else {
        Add-TestWarning -TestName "Live Sync API" -Message "Live sync API responded with HTTP $($liveSyncResponse.StatusCode)"
    }
} catch {
    Add-TestWarning -TestName "Live Sync API" -Message "Live sync API check failed" -Details $_.Exception.Message
}

# Test 9: REST API Endpoints
Write-Header "TESTING REST API ENDPOINTS"
$apiEndpoints = @(
    "wp-json/wp/v2/posts",
    "wp-json/wp/v2/pages", 
    "wp-json/blackcnote/v1/homepage",
    "wp-json/blackcnote/v1/plans",
    "wp-json/blackcnote/v1/content"
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

# Test 10: Database Connectivity
Write-Header "TESTING DATABASE CONNECTIVITY"
try {
    $dbResponse = Invoke-WebRequest -Uri $ServiceUrls.phpMyAdmin -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
    if ($dbResponse.StatusCode -eq 200) {
        Add-TestResult -TestName "Database (phpMyAdmin)" -Passed $true -Message "Database management interface is accessible"
    } else {
        Add-TestWarning -TestName "Database (phpMyAdmin)" -Message "Database interface responded with HTTP $($dbResponse.StatusCode)"
    }
} catch {
    Add-TestWarning -TestName "Database (phpMyAdmin)" -Message "Database interface check failed" -Details $_.Exception.Message
}

# Test 11: Email System
Write-Header "TESTING EMAIL SYSTEM"
try {
    $emailResponse = Invoke-WebRequest -Uri $ServiceUrls.MailHog -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
    if ($emailResponse.StatusCode -eq 200) {
        Add-TestResult -TestName "Email System (MailHog)" -Passed $true -Message "Email testing interface is accessible"
    } else {
        Add-TestWarning -TestName "Email System (MailHog)" -Message "Email interface responded with HTTP $($emailResponse.StatusCode)"
    }
} catch {
    Add-TestWarning -TestName "Email System (MailHog)" -Message "Email interface check failed" -Details $_.Exception.Message
}

# Test 12: Development Tools
Write-Header "TESTING DEVELOPMENT TOOLS"
try {
    $devToolsResponse = Invoke-WebRequest -Uri $ServiceUrls.DevTools -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
    if ($devToolsResponse.StatusCode -eq 200) {
        Add-TestResult -TestName "Development Tools" -Passed $true -Message "Development tools interface is accessible"
    } else {
        Add-TestWarning -TestName "Development Tools" -Message "Dev tools interface responded with HTTP $($devToolsResponse.StatusCode)"
    }
} catch {
    Add-TestWarning -TestName "Development Tools" -Message "Dev tools interface check failed" -Details $_.Exception.Message
}

# Generate Test Summary
Write-Header "TEST SUMMARY"
Write-Host "`n📊 Test Results:" -ForegroundColor White
Write-Host "   Total Tests: $($TestResults.Total)" -ForegroundColor White
Write-Host "   ✅ Passed: $($TestResults.Passed)" -ForegroundColor Green
Write-Host "   ❌ Failed: $($TestResults.Failed)" -ForegroundColor Red
Write-Host "   ⚠️ Warnings: $($TestResults.Warnings)" -ForegroundColor Yellow

# Calculate success rate
$successRate = if ($TestResults.Total -gt 0) { [math]::Round(($TestResults.Passed / $TestResults.Total) * 100, 2) } else { 0 }

Write-Host "`n📈 Success Rate: $successRate%" -ForegroundColor $(if ($successRate -ge 90) { "Green" } elseif ($successRate -ge 75) { "Yellow" } else { "Red" })

# Overall status
if ($TestResults.Failed -eq 0 -and $successRate -ge 90) {
    Write-Host "`n🎉 SYSTEM STATUS: FULLY OPERATIONAL" -ForegroundColor Green
    Write-Host "All critical components are activated and functioning properly." -ForegroundColor Green
} elseif ($TestResults.Failed -eq 0) {
    Write-Host "`n✅ SYSTEM STATUS: OPERATIONAL WITH WARNINGS" -ForegroundColor Yellow
    Write-Host "System is operational but some components have warnings." -ForegroundColor Yellow
} else {
    Write-Host "`n❌ SYSTEM STATUS: ISSUES DETECTED" -ForegroundColor Red
    Write-Host "Some critical components failed activation tests." -ForegroundColor Red
}

# Recommendations
Write-Host "`n💡 Recommendations:" -ForegroundColor Cyan
if ($TestResults.Failed -gt 0) {
    Write-Host "   • Review failed tests and fix issues" -ForegroundColor White
}
if ($TestResults.Warnings -gt 0) {
    Write-Host "   • Address warnings to improve system reliability" -ForegroundColor White
}
Write-Host "   • Run this test regularly to monitor system health" -ForegroundColor White
Write-Host "   • Check logs for detailed error information" -ForegroundColor White

# Save test results to file
$testReportPath = Join-Path $CanonicalPaths.ProjectRoot "reports\activation-test-$(Get-Date -Format 'yyyyMMdd-HHmmss').json"
$testReport = @{
    Timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
    Results = $TestResults
    SuccessRate = $successRate
    CanonicalPaths = $CanonicalPaths
    ServiceUrls = $ServiceUrls
}

# Ensure reports directory exists
$reportsDir = Split-Path $testReportPath -Parent
if (-not (Test-Path $reportsDir)) {
    New-Item -ItemType Directory -Path $reportsDir -Force | Out-Null
}

$testReport | ConvertTo-Json -Depth 10 | Out-File -FilePath $testReportPath -Encoding UTF8
Write-Host "`n📄 Test report saved to: $testReportPath" -ForegroundColor Gray

Write-Host "`n🏁 Automated activation test completed!" -ForegroundColor Magenta 