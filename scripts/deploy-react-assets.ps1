# BlackCnote React Assets Deployment Script
# This script builds the React app and deploys it to the WordPress theme

Write-Host "BlackCnote React Assets Deployment" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green

# Set paths
$projectRoot = Split-Path -Parent $PSScriptRoot
$reactAppPath = Join-Path $projectRoot "react-app"
$themeDistPath = Join-Path $projectRoot "blackcnote\wp-content\themes\blackcnote\dist"

Write-Host "Project Root: $projectRoot" -ForegroundColor Cyan
Write-Host "React App: $reactAppPath" -ForegroundColor Cyan
Write-Host "Theme Dist: $themeDistPath" -ForegroundColor Cyan

# Check if React app directory exists
if (-not (Test-Path $reactAppPath)) {
    Write-Host "ERROR: React app directory not found: $reactAppPath" -ForegroundColor Red
    exit 1
}

# Navigate to React app directory
Set-Location $reactAppPath
Write-Host "Changed to React app directory" -ForegroundColor Yellow

# Check if node_modules exists
if (-not (Test-Path "node_modules")) {
    Write-Host "Installing npm dependencies..." -ForegroundColor Yellow
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "ERROR: Failed to install npm dependencies" -ForegroundColor Red
        exit 1
    }
}

# Build React app
Write-Host "Building React app..." -ForegroundColor Yellow
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Failed to build React app" -ForegroundColor Red
    exit 1
}

# Check if build was successful
$buildDistPath = Join-Path $reactAppPath "dist"
if (-not (Test-Path $buildDistPath)) {
    Write-Host "ERROR: Build output directory not found: $buildDistPath" -ForegroundColor Red
    exit 1
}

# Create theme dist directory if it doesn't exist
if (-not (Test-Path $themeDistPath)) {
    Write-Host "Creating theme dist directory..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $themeDistPath -Force | Out-Null
}

# Copy build assets to theme
Write-Host "Copying React assets to theme..." -ForegroundColor Yellow
try {
    # Remove existing assets
    if (Test-Path $themeDistPath) {
        Get-ChildItem -Path $themeDistPath -Recurse | Remove-Item -Force -Recurse
    }
    
    # Copy new assets
    Copy-Item -Path "$buildDistPath\*" -Destination $themeDistPath -Recurse -Force
    
    Write-Host "SUCCESS: React assets deployed successfully!" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Failed to copy React assets: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# List deployed files
Write-Host "Deployed files:" -ForegroundColor Cyan
Get-ChildItem -Path $themeDistPath -Recurse | ForEach-Object {
    $relativePath = $_.FullName.Replace($themeDistPath, "").TrimStart("\")
    Write-Host "   $relativePath" -ForegroundColor Gray
}

# Check for required files
$requiredFiles = @("index.html", "assets\index-*.js", "assets\index-*.css")
$missingFiles = @()

foreach ($pattern in $requiredFiles) {
    $files = Get-ChildItem -Path $themeDistPath -Name $pattern -Recurse
    if ($files.Count -eq 0) {
        $missingFiles += $pattern
    }
}

if ($missingFiles.Count -gt 0) {
    Write-Host "WARNING: Some required files may be missing:" -ForegroundColor Yellow
    foreach ($file in $missingFiles) {
        Write-Host "   - $file" -ForegroundColor Yellow
    }
} else {
    Write-Host "SUCCESS: All required files are present" -ForegroundColor Green
}

# Return to original directory
Set-Location $projectRoot

Write-Host "React assets deployment completed!" -ForegroundColor Green
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "   1. Clear your browser cache" -ForegroundColor White
Write-Host "   2. Reload your WordPress site" -ForegroundColor White
Write-Host "   3. Check the browser console for any errors" -ForegroundColor White 