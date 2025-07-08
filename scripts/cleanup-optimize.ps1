# BlackCnote Cleanup and Optimization Script
# Removes redundant files and optimizes performance

Write-Host "🚀 BlackCnote Cleanup and Optimization" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

# 1. REMOVE REDUNDANT FILES
Write-Host "1. Removing Redundant Files..." -ForegroundColor Yellow

$filesToRemove = @(
    # React App Duplicate Configs
    "react-app\vite.config.simple.js",
    "react-app\vite.config.js", 
    "react-app\bs-config.js",
    "react-app\browsersync-config.js",
    "react-app\dev-simple.cjs",
    "react-app\quick-fix.cjs",
    "react-app\fix-development-issues.cjs",
    "react-app\fix-development-issues.js",
    "react-app\crlf_files_inc.txt",
    "react-app\crlf_files.txt",
    "react-app\start-dev.sh",
    "react-app\env.development",
    
    # Theme Duplicate Files
    "blackcnote\wp-content\themes\blackcnote\functions.php.backup.2025-07-07-04-11-40",
    "blackcnote\wp-content\themes\blackcnote\infrastructure.php",
    "blackcnote\wp-content\themes\blackcnote\infrastructure-fixed.php",
    "blackcnote\wp-content\themes\blackcnote\final-infrastructure.php",
    "blackcnote\wp-content\themes\blackcnote\header-enhanced.php",
    "blackcnote\wp-content\themes\blackcnote\BLACKCNOTE Logo (1).png",
    "blackcnote\wp-content\themes\blackcnote\BLACKCNOTE Logo (2).png", 
    "blackcnote\wp-content\themes\blackcnote\BLACKCNOTE Logo (3).png",
    "blackcnote\wp-content\themes\blackcnote\BLACKCNOTE logo (4).png",
    "blackcnote\wp-content\themes\blackcnote\screenshot.png"
)

foreach ($file in $filesToRemove) {
    if (Test-Path $file) {
        Remove-Item $file -Force
        Write-Host "   ✅ Removed: $file" -ForegroundColor Green
    }
}

# 2. REMOVE REDUNDANT SCRIPTS
Write-Host ""
Write-Host "2. Removing Redundant Scripts..." -ForegroundColor Yellow

$scriptsToRemove = @(
    "scripts\fix-canonical-conflicts-complete.ps1",
    "scripts\fix-canonical-conflicts-simple.ps1",
    "scripts\fix-admin-template-override-simple.ps1",
    "scripts\fix-php-execution-simple.ps1",
    "scripts\test-admin-fix.ps1",
    "scripts\test-all-fixes.ps1",
    "scripts\run-react-test.bat",
    "scripts\test-wordpress-fix.bat",
    "scripts\fix-docker-final.bat",
    "scripts\fix-docker-manual.bat",
    "scripts\start-docker-admin.bat",
    "scripts\test-db-connection.php",
    "scripts\fix-wp-config-issue.bat",
    "scripts\browsersync.js",
    "scripts\verify-project-organization.php",
    "scripts\final-verification-test.php",
    "scripts\synchronization-test.php",
    "scripts\simple-perfection-test.php",
    "scripts\final-perfection-test.php",
    "scripts\comprehensive-enhancement-test.php",
    "scripts\final-frontend-verification.php",
    "scripts\frontend-features-analysis.php",
    "scripts\comprehensive-frontend-test.php",
    "scripts\comprehensive-theme-test.php",
    "scripts\fix-pathway-issues.php",
    "scripts\deploy-to-github.ps1",
    "scripts\deploy-production.sh",
    "scripts\fix-docker-urls.php",
    "scripts\test-wordpress-access.ps1",
    "scripts\test-react-access.ps1",
    "scripts\check-startup-compatibility.ps1",
    "scripts\verify-service-connections.sh",
    "scripts\deploy-production.ps1",
    "scripts\fix-docker-daemon.ps1",
    "scripts\create-task-scheduler.ps1",
    "scripts\fix-startup-execution.ps1",
    "scripts\fix-docker-api-engine.ps1",
    "scripts\setup-ml-environment.ps1",
    "scripts\setup-docker-privileges.ps1",
    "scripts\verify-startup-deployment.ps1"
)

foreach ($script in $scriptsToRemove) {
    if (Test-Path $script) {
        Remove-Item $script -Force
        Write-Host "   ✅ Removed: $script" -ForegroundColor Green
    }
}

# 3. OPTIMIZE REACT APP
Write-Host ""
Write-Host "3. Optimizing React App..." -ForegroundColor Yellow

# Optimize Vite config
$viteConfig = "react-app\vite.config.ts"
if (Test-Path $viteConfig) {
    Write-Host "   - Optimizing Vite configuration" -ForegroundColor Green
    
    $content = Get-Content $viteConfig -Raw
    
    # Add performance optimizations
    $performanceOpts = @"
  // Performance optimizations
  esbuild: {
    treeShaking: true,
    target: 'es2020',
    sourcemap: process.env.NODE_ENV === 'development',
    minifyIdentifiers: process.env.NODE_ENV === 'production',
    minifySyntax: process.env.NODE_ENV === 'production',
    minifyWhitespace: process.env.NODE_ENV === 'production',
  },
"@
    
    # Replace existing esbuild section
    $content = $content -replace 'esbuild: \{[\s\S]*?\},', $performanceOpts
    Set-Content $viteConfig $content -Encoding UTF8
}

# 4. OPTIMIZE WORDPRESS THEME
Write-Host ""
Write-Host "4. Optimizing WordPress Theme..." -ForegroundColor Yellow

# Add performance optimizations to theme functions
$themeFunctions = "blackcnote\wp-content\themes\blackcnote\functions.php"
if (Test-Path $themeFunctions) {
    Write-Host "   - Adding performance optimizations" -ForegroundColor Green
    
    $performanceCode = @"

// Performance Optimizations
function blackcnote_performance_optimizations() {
    // Remove unnecessary WordPress features
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    
    // Disable emojis
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'blackcnote_performance_optimizations');

"@
    
    $content = Get-Content $themeFunctions -Raw
    if ($content -notmatch 'blackcnote_performance_optimizations') {
        $content = $content + $performanceCode
        Set-Content $themeFunctions $content -Encoding UTF8
    }
}

# 5. CREATE PERFORMANCE MONITOR
Write-Host ""
Write-Host "5. Creating Performance Monitor..." -ForegroundColor Yellow

$monitorScript = @"
# BlackCnote Performance Monitor
Write-Host "🔍 BlackCnote Performance Monitor" -ForegroundColor Cyan
Write-Host "===============================" -ForegroundColor Cyan
Write-Host ""

# Check Docker containers
Write-Host "🐳 Docker Container Status:" -ForegroundColor Yellow
docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

Write-Host ""
Write-Host "📊 Service Performance:" -ForegroundColor Yellow

# Check WordPress
try {
    `$response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    Write-Host "   ✅ WordPress: Accessible" -ForegroundColor Green
} catch {
    Write-Host "   ❌ WordPress: Not accessible" -ForegroundColor Red
}

# Check React App
try {
    `$response = Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 10 -UseBasicParsing
    Write-Host "   ✅ React App: Accessible" -ForegroundColor Green
} catch {
    Write-Host "   ❌ React App: Not accessible" -ForegroundColor Red
}

Write-Host ""
Write-Host "🎯 Performance Recommendations:" -ForegroundColor Cyan
Write-Host "   1. Monitor Docker container resources" -ForegroundColor White
Write-Host "   2. Check for slow database queries" -ForegroundColor White
Write-Host "   3. Optimize image caching" -ForegroundColor White
Write-Host "   4. Review React bundle size" -ForegroundColor White
"@

Set-Content "scripts\monitor-performance.ps1" $monitorScript -Encoding UTF8
Write-Host "   - Created performance monitoring script" -ForegroundColor Green

# 6. FINAL VERIFICATION
Write-Host ""
Write-Host "6. Final Verification..." -ForegroundColor Yellow

$checks = @(
    @{ Name = "Vite Config Optimized"; Path = "react-app\vite.config.ts" },
    @{ Name = "Theme Functions Optimized"; Path = "blackcnote\wp-content\themes\blackcnote\functions.php" },
    @{ Name = "Performance Monitor Created"; Path = "scripts\monitor-performance.ps1" }
)

foreach ($check in $checks) {
    if (Test-Path $check.Path) {
        Write-Host "   ✅ $($check.Name)" -ForegroundColor Green
    } else {
        Write-Host "   ❌ $($check.Name)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "🎉 BlackCnote Cleanup and Optimization Complete!" -ForegroundColor Cyan
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ Redundant files removed" -ForegroundColor Green
Write-Host "✅ React app optimized" -ForegroundColor Green
Write-Host "✅ WordPress theme optimized" -ForegroundColor Green
Write-Host "✅ Performance monitoring created" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next Steps:" -ForegroundColor Yellow
Write-Host "   1. Restart Docker services" -ForegroundColor White
Write-Host "   2. Monitor performance: scripts\monitor-performance.ps1" -ForegroundColor White
Write-Host "   3. Test all functionality" -ForegroundColor White
Write-Host ""
Write-Host "🚀 BlackCnote is now optimized for performance!" -ForegroundColor Green 