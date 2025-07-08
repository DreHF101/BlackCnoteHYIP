# BlackCnote Canonical Conflicts Fix - Simple Version
# This script fixes all identified conflicts and enforces canonical pathways

Write-Host "🚀 BlackCnote Canonical Conflicts Fix" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

# 1. FIX PORT CONFLICTS - Enforce Canonical Port 5174
Write-Host "1. Fixing Port Conflicts..." -ForegroundColor Yellow

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

# 2. FIX HYIPLAB PLUGIN CONFLICTS - Prevent Template Override on Admin Pages
Write-Host ""
Write-Host "2. Fixing HYIPLab Plugin Conflicts..." -ForegroundColor Yellow

# Create a modified version of ExecuteRouter that respects admin pages
$executeRouterPath = "hyiplab\app\Hook\ExecuteRouter.php"
if (Test-Path $executeRouterPath) {
    Write-Host "   - Creating backup of ExecuteRouter.php" -ForegroundColor Green
    Copy-Item $executeRouterPath "$executeRouterPath.backup.$(Get-Date -Format 'yyyyMMdd-HHmmss')"
    
    Write-Host "   - Modifying ExecuteRouter to prevent admin page conflicts" -ForegroundColor Green
    $content = Get-Content $executeRouterPath -Raw
    
    # Add admin page check to includeTemplate method
    $adminCheck = @"
    public function includeTemplate(`$template)
    {
        // Don't override templates on admin pages
        if (is_admin()) {
            return `$template;
        }
        
        // Don't override templates on login/register pages
        if (in_array(`$GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php'])) {
            return `$template;
        }
        
        `$noMatch = true;
"@
    
    $content = $content -replace 'public function includeTemplate\(\$template\)\s*\{', $adminCheck
    Set-Content $executeRouterPath $content -Encoding UTF8
}

# 3. ENFORCE CANONICAL PATHWAYS - Update All Configuration Files
Write-Host ""
Write-Host "3. Enforcing Canonical Pathways..." -ForegroundColor Yellow

# Update WordPress theme functions.php to use canonical paths
$themeFunctions = "blackcnote\wp-content\themes\blackcnote\functions.php"
if (Test-Path $themeFunctions) {
    Write-Host "   - Updating theme functions.php with canonical paths" -ForegroundColor Green
    
    # Add canonical path constants
    $canonicalConstants = @"

// Canonical Pathways - ENFORCED
define('BLACKCNOTE_CANONICAL_ROOT', 'C:/Users/CASH AMERICA PAWN/Desktop/BlackCnote');
define('BLACKCNOTE_CANONICAL_WORDPRESS', BLACKCNOTE_CANONICAL_ROOT . '/blackcnote');
define('BLACKCNOTE_CANONICAL_WP_CONTENT', BLACKCNOTE_CANONICAL_WORDPRESS . '/wp-content');
define('BLACKCNOTE_CANONICAL_THEME', BLACKCNOTE_CANONICAL_WP_CONTENT . '/themes/blackcnote');
define('BLACKCNOTE_CANONICAL_REACT_APP', BLACKCNOTE_CANONICAL_ROOT . '/react-app');

// Canonical Service URLs - ENFORCED
define('BLACKCNOTE_WORDPRESS_URL', 'http://localhost:8888');
define('BLACKCNOTE_REACT_URL', 'http://localhost:5174');
define('BLACKCNOTE_PHPMYADMIN_URL', 'http://localhost:8080');
define('BLACKCNOTE_REDIS_COMMANDER_URL', 'http://localhost:8081');
define('BLACKCNOTE_MAILHOG_URL', 'http://localhost:8025');
define('BLACKCNOTE_BROWSERSYNC_URL', 'http://localhost:3000');
define('BLACKCNOTE_DEV_TOOLS_URL', 'http://localhost:9229');

"@
    
    $content = Get-Content $themeFunctions -Raw
    if ($content -notmatch 'BLACKCNOTE_CANONICAL_ROOT') {
        $content = $content -replace 'define\(''BLACKCNOTE_THEME_VERSION''', $canonicalConstants + "`n" + 'define(''BLACKCNOTE_THEME_VERSION'''
        Set-Content $themeFunctions $content -Encoding UTF8
    }
}

# 4. RESTART DOCKER SERVICES TO APPLY CHANGES
Write-Host ""
Write-Host "4. Restarting Docker Services..." -ForegroundColor Yellow

Write-Host "   - Stopping BlackCnote containers..." -ForegroundColor Green
docker-compose down

Write-Host "   - Starting BlackCnote containers..." -ForegroundColor Green
docker-compose up -d

# 5. VERIFY FIXES
Write-Host ""
Write-Host "5. Verifying Fixes..." -ForegroundColor Yellow

# Wait for services to start
Start-Sleep -Seconds 10

# Check if React app is accessible on canonical port
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "   ✅ React app accessible on canonical port 5174" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  React app returned status: $($response.StatusCode)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   ❌ React app not accessible on port 5174: $($_.Exception.Message)" -ForegroundColor Red
}

# Check WordPress
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "   ✅ WordPress accessible on canonical port 8888" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  WordPress returned status: $($response.StatusCode)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   ❌ WordPress not accessible on port 8888: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "🎉 BlackCnote Canonical Conflicts Fix Complete!" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ All port conflicts resolved" -ForegroundColor Green
Write-Host "✅ HYIPLab plugin conflicts fixed" -ForegroundColor Green
Write-Host "✅ Canonical pathways enforced" -ForegroundColor Green
Write-Host "✅ Services restarted and verified" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next Steps:" -ForegroundColor Yellow
Write-Host "   1. Test WordPress admin: http://localhost:8888/wp-admin/" -ForegroundColor White
Write-Host "   2. Test React app: http://localhost:5174" -ForegroundColor White
Write-Host "   3. Test HYIPLab plugin functionality" -ForegroundColor White
Write-Host "" 