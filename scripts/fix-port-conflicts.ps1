# BlackCnote Port Conflicts Fix
# Fixes React app port conflicts to use canonical port 5174

Write-Host "🚀 Fixing BlackCnote Port Conflicts..." -ForegroundColor Cyan

# Fix theme React app port configuration
$themeViteConfig = "blackcnote\wp-content\themes\blackcnote\react-app\vite.config.theme.ts"
if (Test-Path $themeViteConfig) {
    Write-Host "   - Updating theme Vite config to use canonical port 5174" -ForegroundColor Green
    $content = Get-Content $themeViteConfig -Raw
    $content = $content -replace 'port: 5175, // Different port to avoid conflicts', 'port: 5174, // Canonical React port'
    $content = $content -replace 'port: 5176,', 'port: 5178,'
    Set-Content $themeViteConfig $content -Encoding UTF8
}

# Fix theme Vite config.ts
$themeViteConfigTs = "blackcnote\wp-content\themes\blackcnote\react-app\vite.config.ts"
if (Test-Path $themeViteConfigTs) {
    Write-Host "   - Updating theme vite.config.ts to use canonical port 5174" -ForegroundColor Green
    $content = Get-Content $themeViteConfigTs -Raw
    $content = $content -replace 'port: 5175, // Different port to avoid conflicts', 'port: 5174, // Canonical React port'
    $content = $content -replace 'port: 5176,', 'port: 5178,'
    Set-Content $themeViteConfigTs $content -Encoding UTF8
}

# Fix theme package.json
$themePackageJson = "blackcnote\wp-content\themes\blackcnote\react-app\package.json"
if (Test-Path $themePackageJson) {
    Write-Host "   - Updating theme package.json to use canonical port 5174" -ForegroundColor Green
    $content = Get-Content $themePackageJson -Raw
    $content = $content -replace '"dev:theme": "vite --config vite.config.theme.ts --port 5175"', '"dev:theme": "vite --config vite.config.theme.ts --port 5174"'
    Set-Content $themePackageJson $content -Encoding UTF8
}

Write-Host "✅ Port conflicts fixed!" -ForegroundColor Green
Write-Host "   - All React apps now use canonical port 5174" -ForegroundColor White
Write-Host "   - HMR ports updated to avoid conflicts" -ForegroundColor White 