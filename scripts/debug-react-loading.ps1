# BlackCnote React Loading Debug Script
# This script helps identify and troubleshoot React loading issues

Write-Host "BlackCnote React Loading Debug" -ForegroundColor Green
Write-Host "=============================" -ForegroundColor Green

# Set paths
$projectRoot = Split-Path -Parent $PSScriptRoot
$themePath = Join-Path $projectRoot "blackcnote\wp-content\themes\blackcnote"
$themeDistPath = Join-Path $themePath "dist"
$functionsPath = Join-Path $themePath "functions.php"

Write-Host "Checking file paths and configuration..." -ForegroundColor Cyan

# Check if theme directory exists
if (-not (Test-Path $themePath)) {
    Write-Host "ERROR: Theme directory not found: $themePath" -ForegroundColor Red
    exit 1
}

# Check if functions.php exists
if (-not (Test-Path $functionsPath)) {
    Write-Host "ERROR: functions.php not found: $functionsPath" -ForegroundColor Red
    exit 1
}

# Check if dist directory exists
if (-not (Test-Path $themeDistPath)) {
    Write-Host "ERROR: React dist directory not found: $themeDistPath" -ForegroundColor Red
    Write-Host "Run: scripts\deploy-react-assets.ps1" -ForegroundColor Yellow
    exit 1
}

# Check for required React files
$requiredFiles = @{
    "index.html" = "Main HTML file"
    "assets\index-*.js" = "React JavaScript bundle"
    "assets\index-*.css" = "React CSS styles"
}

$missingFiles = @()

foreach ($pattern in $requiredFiles.Keys) {
    $files = Get-ChildItem -Path $themeDistPath -Name $pattern -Recurse
    if ($files.Count -eq 0) {
        $missingFiles += $pattern
        Write-Host "MISSING: $($requiredFiles[$pattern]) ($pattern)" -ForegroundColor Red
    } else {
        Write-Host "FOUND: $($requiredFiles[$pattern]) - $($files -join ', ')" -ForegroundColor Green
    }
}

if ($missingFiles.Count -gt 0) {
    Write-Host "ERROR: Missing required React files. Run: scripts\deploy-react-assets.ps1" -ForegroundColor Red
    exit 1
}

# Check functions.php for React configuration
Write-Host "`nChecking functions.php configuration..." -ForegroundColor Cyan

$functionsContent = Get-Content $functionsPath -Raw
$checks = @{
    "wp_enqueue_script.*blackcnote-react-main" = "React script enqueued"
    "wp_add_inline_script.*blackcnote-react-main" = "React config injected"
    "window\.blackCnoteApiSettings" = "React config variable"
    "blackcnote_should_render_wp_header_footer" = "Header/footer toggle function"
}

foreach ($pattern in $checks.Keys) {
    if ($functionsContent -match $pattern) {
        Write-Host "FOUND: $($checks[$pattern])" -ForegroundColor Green
    } else {
        Write-Host "MISSING: $($checks[$pattern])" -ForegroundColor Red
    }
}

# Check WordPress configuration
Write-Host "`nChecking WordPress configuration..." -ForegroundColor Cyan

$wpConfigPath = Join-Path $projectRoot "blackcnote\wp-config.php"
if (Test-Path $wpConfigPath) {
    $wpConfigContent = Get-Content $wpConfigPath -Raw
    if ($wpConfigContent -match "DB_NAME.*blackcnote") {
        Write-Host "FOUND: WordPress database configured for BlackCnote" -ForegroundColor Green
    } else {
        Write-Host "WARNING: WordPress database may not be configured for BlackCnote" -ForegroundColor Yellow
    }
} else {
    Write-Host "ERROR: wp-config.php not found" -ForegroundColor Red
}

# Check Docker services
Write-Host "`nChecking Docker services..." -ForegroundColor Cyan

try {
    $dockerPs = docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
    if ($dockerPs -match "blackcnote") {
        Write-Host "Docker services running:" -ForegroundColor Green
        Write-Host $dockerPs -ForegroundColor Gray
    } else {
        Write-Host "WARNING: No BlackCnote Docker services found" -ForegroundColor Yellow
        Write-Host "Run: .\automate-docker-startup.bat" -ForegroundColor Yellow
    }
} catch {
    Write-Host "ERROR: Docker not available or not running" -ForegroundColor Red
}

# Provide troubleshooting steps
Write-Host "`nTroubleshooting Steps:" -ForegroundColor Cyan
Write-Host "1. Clear browser cache and cookies" -ForegroundColor White
Write-Host "2. Check browser console for JavaScript errors" -ForegroundColor White
Write-Host "3. Verify WordPress site is accessible at http://localhost:8888" -ForegroundColor White
Write-Host "4. Check if React app loads at http://localhost:5174" -ForegroundColor White
Write-Host "5. Verify theme settings in WordPress admin: Appearance > BlackCnote Settings" -ForegroundColor White

Write-Host "`nCommon Issues and Solutions:" -ForegroundColor Cyan
Write-Host "- If you see 'Configuration settings are missing': React config not injected" -ForegroundColor Yellow
Write-Host "- If you see 'Loading...' forever: React assets not loading or mounting" -ForegroundColor Yellow
Write-Host "- If you see blank page: Check browser console for errors" -ForegroundColor Yellow
Write-Host "- If header/footer missing: Check theme settings toggle" -ForegroundColor Yellow

Write-Host "`nDebug completed!" -ForegroundColor Green 