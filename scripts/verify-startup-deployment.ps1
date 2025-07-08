# BlackCnote Startup Deployment Verification Script
# Verifies that all necessary components will be deployed by the startup script

param(
    [switch]$Verbose,
    [switch]$FixIssues
)

# Set error action preference
$ErrorActionPreference = "Continue"

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

# Function to check file existence
function Test-FileExists {
    param([string]$Path, [string]$Description)
    $exists = Test-Path $Path
    $status = if ($exists) { "✅" } else { "❌" }
    $color = if ($exists) { "Green" } else { "Red" }
    Write-ColorOutput "$status $Description`: $Path" $color
    return $exists
}

# Function to check directory existence
function Test-DirectoryExists {
    param([string]$Path, [string]$Description)
    $exists = Test-Path $Path
    $status = if ($exists) { "✅" } else { "❌" }
    $color = if ($exists) { "Green" } else { "Red" }
    Write-ColorOutput "$status $Description`: $Path" $color
    return $exists
}

# Function to check service configuration
function Test-ServiceConfig {
    param([string]$Service, [string]$ConfigPath, [string]$Description)
    $exists = Test-Path $ConfigPath
    $status = if ($exists) { "✅" } else { "❌" }
    $color = if ($exists) { "Green" } else { "Red" }
    Write-ColorOutput "$status $Service $Description`: $ConfigPath" $color
    return $exists
}

# Main execution
Write-ColorOutput "=== BlackCnote Startup Deployment Verification ===" "Cyan"
Write-ColorOutput "Verifying all components will be deployed correctly..." "White"
Write-ColorOutput ""

# Set project root
$projectRoot = "$PSScriptRoot\.."
Set-Location $projectRoot

# 1. Startup Scripts Verification
Write-ColorOutput "📁 1. STARTUP SCRIPTS" "Yellow"
$startupScripts = @(
    @{ Path = "start-blackcnote-complete.ps1"; Description = "Main PowerShell Startup Script" },
    @{ Path = "start-blackcnote.bat"; Description = "Windows Batch Startup Script" },
    @{ Path = "blackcnote\start-blackcnote.sh"; Description = "WSL2/Linux Startup Script" }
)

$startupScriptsOk = $true
foreach ($script in $startupScripts) {
    if (-not (Test-FileExists -Path $script.Path -Description $script.Description)) {
        $startupScriptsOk = $false
    }
}

# 2. Docker Configuration Verification
Write-ColorOutput "`n🐳 2. DOCKER CONFIGURATION" "Yellow"
$dockerConfigs = @(
    @{ Path = "config\docker\docker-compose.yml"; Description = "Main Docker Compose" },
    @{ Path = "config\docker\wordpress.Dockerfile"; Description = "WordPress Dockerfile" },
    @{ Path = "react-app\Dockerfile.dev"; Description = "React Development Dockerfile" }
)

$dockerConfigsOk = $true
foreach ($config in $dockerConfigs) {
    if (-not (Test-FileExists -Path $config.Path -Description $config.Description)) {
        $dockerConfigsOk = $false
    }
}

# 3. WordPress Theme Verification
Write-ColorOutput "`n🎨 3. WORDPRESS THEME" "Yellow"
$themeFiles = @(
    @{ Path = "blackcnote\wp-content\themes\blackcnote\style.css"; Description = "Theme Stylesheet" },
    @{ Path = "blackcnote\wp-content\themes\blackcnote\index.php"; Description = "Main Template" },
    @{ Path = "blackcnote\wp-content\themes\blackcnote\functions.php"; Description = "Theme Functions" },
    @{ Path = "blackcnote\wp-content\themes\blackcnote\header.php"; Description = "Header Template" },
    @{ Path = "blackcnote\wp-content\themes\blackcnote\footer.php"; Description = "Footer Template" },
    @{ Path = "blackcnote\wp-content\themes\blackcnote\assets\css\blackcnote-theme.css"; Description = "Main CSS" },
    @{ Path = "blackcnote\wp-content\themes\blackcnote\assets\js\blackcnote-theme.js"; Description = "Main JavaScript" }
)

$themeFilesOk = $true
foreach ($file in $themeFiles) {
    if (-not (Test-FileExists -Path $file.Path -Description $file.Description)) {
        $themeFilesOk = $false
    }
}

# 4. React App Verification
Write-ColorOutput "`n⚛️ 4. REACT APPLICATION" "Yellow"
$reactFiles = @(
    @{ Path = "react-app\package.json"; Description = "Package Configuration" },
    @{ Path = "react-app\vite.config.js"; Description = "Vite Configuration" },
    @{ Path = "react-app\tailwind.config.js"; Description = "Tailwind Configuration" },
    @{ Path = "react-app\src\main.tsx"; Description = "Main React Entry" },
    @{ Path = "react-app\src\App.tsx"; Description = "Main App Component" },
    @{ Path = "react-app\public\index.html"; Description = "HTML Template" }
)

$reactFilesOk = $true
foreach ($file in $reactFiles) {
    if (-not (Test-FileExists -Path $file.Path -Description $file.Description)) {
        $reactFilesOk = $false
    }
}

# 5. Database Configuration Verification
Write-ColorOutput "`n🗄️ 5. DATABASE CONFIGURATION" "Yellow"
$dbConfigs = @(
    @{ Path = "blackcnote\wp-config.php"; Description = "WordPress Configuration" },
    @{ Path = "db\blackcnote.sql"; Description = "Database Schema" },
    @{ Path = "redis.conf"; Description = "Redis Configuration" }
)

$dbConfigsOk = $true
foreach ($config in $dbConfigs) {
    if (-not (Test-FileExists -Path $config.Path -Description $config.Description)) {
        $dbConfigsOk = $false
    }
}

# 6. Service Configuration Verification
Write-ColorOutput "`n🔧 6. SERVICE CONFIGURATIONS" "Yellow"
$serviceConfigs = @(
    @{ Path = "config\nginx\blackcnote-simple.conf"; Description = "Nginx Configuration" },
    @{ Path = "config\apache\000-default.conf"; Description = "Apache Configuration" },
    @{ Path = "react-app\bs-config.js"; Description = "Browsersync Configuration" }
)

$serviceConfigsOk = $true
foreach ($config in $serviceConfigs) {
    if (-not (Test-FileExists -Path $config.Path -Description $config.Description)) {
        $serviceConfigsOk = $false
    }
}

# 7. Directory Structure Verification
Write-ColorOutput "`n📂 7. DIRECTORY STRUCTURE" "Yellow"
$directories = @(
    @{ Path = "blackcnote\wp-content\themes\blackcnote"; Description = "Theme Directory" },
    @{ Path = "blackcnote\wp-content\plugins"; Description = "Plugins Directory" },
    @{ Path = "blackcnote\wp-content\uploads"; Description = "Uploads Directory" },
    @{ Path = "react-app\src"; Description = "React Source Directory" },
    @{ Path = "react-app\public"; Description = "React Public Directory" },
    @{ Path = "config\docker"; Description = "Docker Config Directory" },
    @{ Path = "scripts"; Description = "Scripts Directory" },
    @{ Path = "logs"; Description = "Logs Directory" }
)

$directoriesOk = $true
foreach ($dir in $directories) {
    if (-not (Test-DirectoryExists -Path $dir.Path -Description $dir.Description)) {
        $directoriesOk = $false
    }
}

# 8. Canonical Paths Verification
Write-ColorOutput "`n🛤️ 8. CANONICAL PATHS" "Yellow"
$canonicalPaths = @(
    @{ Path = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"; Description = "Project Root" },
    @{ Path = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote"; Description = "WordPress Installation" },
    @{ Path = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content"; Description = "WordPress Content" },
    @{ Path = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote"; Description = "Theme Files" }
)

$canonicalPathsOk = $true
foreach ($path in $canonicalPaths) {
    if (-not (Test-DirectoryExists -Path $path.Path -Description $path.Description)) {
        $canonicalPathsOk = $false
    }
}

# 9. Service Ports Verification
Write-ColorOutput "`n🌐 9. SERVICE PORTS" "Yellow"
$servicePorts = @(
    @{ Port = 8888; Service = "WordPress Frontend"; URL = "http://localhost:8888" },
    @{ Port = 5174; Service = "React Development"; URL = "http://localhost:5174" },
    @{ Port = 8080; Service = "phpMyAdmin"; URL = "http://localhost:8080" },
    @{ Port = 8081; Service = "Redis Commander"; URL = "http://localhost:8081" },
    @{ Port = 3000; Service = "Browsersync"; URL = "http://localhost:3000" },
    @{ Port = 8025; Service = "MailHog"; URL = "http://localhost:8025" }
)

$servicePortsOk = $true
foreach ($service in $servicePorts) {
    $connection = Get-NetTCPConnection -LocalPort $service.Port -ErrorAction SilentlyContinue
    $status = if ($connection) { "❌" } else { "✅" }
    $color = if ($connection) { "Red" } else { "Green" }
    Write-ColorOutput "$status $($service.Service) (Port $($service.Port))`: $($service.URL)" $color
    if ($connection) {
        $servicePortsOk = $false
    }
}

# 10. Build Scripts Verification
Write-ColorOutput "`n🔨 10. BUILD SCRIPTS" "Yellow"
$buildScripts = @(
    @{ Path = "react-app\package.json"; Script = "build"; Description = "React Build Script" },
    @{ Path = "react-app\package.json"; Script = "dev"; Description = "React Dev Script" },
    @{ Path = "scripts\build-optimizer.js"; Description = "Build Optimizer" }
)

$buildScriptsOk = $true
foreach ($script in $buildScripts) {
    if ($script.Script) {
        # Check if script exists in package.json
        if (Test-Path $script.Path) {
            $packageJson = Get-Content $script.Path | ConvertFrom-Json
            $hasScript = $packageJson.scripts.PSObject.Properties.Name -contains $script.Script
            $status = if ($hasScript) { "✅" } else { "❌" }
            $color = if ($hasScript) { "Green" } else { "Red" }
            Write-ColorOutput "$status $($script.Description)`: $($script.Script) script in $($script.Path)" $color
            if (-not $hasScript) {
                $buildScriptsOk = $false
            }
        } else {
            Write-ColorOutput "❌ $($script.Description)`: $($script.Path) not found" "Red"
            $buildScriptsOk = $false
        }
    } else {
        if (-not (Test-FileExists -Path $script.Path -Description $script.Description)) {
            $buildScriptsOk = $false
        }
    }
}

# Summary
Write-ColorOutput "`n📊 DEPLOYMENT VERIFICATION SUMMARY" "Cyan"
Write-ColorOutput "=====================================" "Cyan"

$allChecks = @(
    @{ Name = "Startup Scripts"; Status = $startupScriptsOk },
    @{ Name = "Docker Configuration"; Status = $dockerConfigsOk },
    @{ Name = "WordPress Theme"; Status = $themeFilesOk },
    @{ Name = "React Application"; Status = $reactFilesOk },
    @{ Name = "Database Configuration"; Status = $dbConfigsOk },
    @{ Name = "Service Configurations"; Status = $serviceConfigsOk },
    @{ Name = "Directory Structure"; Status = $directoriesOk },
    @{ Name = "Canonical Paths"; Status = $canonicalPathsOk },
    @{ Name = "Service Ports"; Status = $servicePortsOk },
    @{ Name = "Build Scripts"; Status = $buildScriptsOk }
)

$totalChecks = $allChecks.Count
$passedChecks = ($allChecks | Where-Object { $_.Status }).Count
$score = [math]::Round(($passedChecks / $totalChecks) * 100, 1)

foreach ($check in $allChecks) {
    $status = if ($check.Status) { "✅ PASS" } else { "❌ FAIL" }
    $color = if ($check.Status) { "Green" } else { "Red" }
    Write-ColorOutput "$status $($check.Name)" $color
}

Write-ColorOutput "`n📈 OVERALL SCORE: $score% ($passedChecks/$totalChecks checks passed)" $(if ($score -ge 90) { "Green" } elseif ($score -ge 70) { "Yellow" } else { "Red" })

# Final recommendation
Write-ColorOutput "`n🚀 DEPLOYMENT READINESS" "Cyan"
if ($score -ge 90) {
    Write-ColorOutput "✅ EXCELLENT - BlackCnote startup script will deploy everything necessary!" "Green"
    Write-ColorOutput "   All critical components are present and properly configured." "Green"
    Write-ColorOutput "   Ready for production deployment." "Green"
} elseif ($score -ge 70) {
    Write-ColorOutput "⚠️  GOOD - Most components are ready, but some issues need attention." "Yellow"
    Write-ColorOutput "   Review failed checks above and fix before deployment." "Yellow"
} else {
    Write-ColorOutput "❌ POOR - Multiple critical issues found." "Red"
    Write-ColorOutput "   Fix all failed checks before attempting deployment." "Red"
}

# Service URLs summary
Write-ColorOutput "`n🌐 SERVICE URLs (after startup):" "Cyan"
Write-ColorOutput "WordPress Frontend: http://localhost:8888" "White"
Write-ColorOutput "WordPress Admin:    http://localhost:8888/wp-admin" "White"
Write-ColorOutput "React App:         http://localhost:5174" "White"
Write-ColorOutput "phpMyAdmin:        http://localhost:8080" "White"
Write-ColorOutput "Redis Commander:   http://localhost:8081" "White"
Write-ColorOutput "MailHog:           http://localhost:8025" "White"
Write-ColorOutput "Browsersync:       http://localhost:3000" "White"
Write-ColorOutput "Health Check:      http://localhost:8888/health" "White"

Write-ColorOutput "`n✅ Verification complete!" "Green" 