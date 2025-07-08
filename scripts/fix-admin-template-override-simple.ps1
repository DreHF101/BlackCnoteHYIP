#!/usr/bin/env pwsh

# BlackCnote Admin Template Override Fix Script (Simple Version)
# =============================================================
# This script fixes the HYIPLab plugin template_include hook that's affecting admin pages

Write-Host "🔧 BlackCnote Admin Template Override Fix Script" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

# Configuration
$ProjectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$PluginDir = "$ProjectRoot\blackcnote\wp-content\plugins\hyiplab"
$BackupDir = "$ProjectRoot\backups\plugin-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"

Write-Host "📁 Plugin Directory: $PluginDir" -ForegroundColor Yellow
Write-Host "💾 Backup Directory: $BackupDir" -ForegroundColor Yellow

# Step 1: Create backup
Write-Host "💾 Creating plugin backup..." -ForegroundColor Yellow
if (!(Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
}

try {
    Copy-Item -Path "$PluginDir\*" -Destination $BackupDir -Recurse -Force
    Write-Host "  ✅ Backup created at: $BackupDir" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Error creating backup" -ForegroundColor Red
    exit 1
}

# Step 2: Fix ExecuteRouter.php
Write-Host "🔧 Fixing ExecuteRouter.php..." -ForegroundColor Yellow
$executeRouterFile = "$PluginDir\app\Hook\ExecuteRouter.php"

if (Test-Path $executeRouterFile) {
    try {
        $content = Get-Content -Path $executeRouterFile -Raw -Encoding UTF8
        
        # Check if the fix is already applied
        if ($content -match "if \(is_admin\(\)\)") {
            Write-Host "  ✅ Fix already applied to ExecuteRouter.php" -ForegroundColor Green
        } else {
            # Add is_admin() check to includeTemplate method
            $oldPattern = 'public function includeTemplate\(\$template\)\s*\{'
            $newContent = $content -replace $oldPattern, "public function includeTemplate(`$template)`n    {`n        // Don't override admin templates`n        if (is_admin()) {`n            return `$template;`n        }`n        `n        "
            
            # Write back with UTF8 encoding without BOM
            [System.IO.File]::WriteAllText($executeRouterFile, $newContent, [System.Text.UTF8Encoding]::new($false))
            Write-Host "  ✅ Fixed ExecuteRouter.php - Added is_admin() check" -ForegroundColor Green
        }
    } catch {
        Write-Host "  ❌ Error fixing ExecuteRouter.php: $_" -ForegroundColor Red
    }
} else {
    Write-Host "  ❌ ExecuteRouter.php not found" -ForegroundColor Red
}

# Step 3: Fix Hook.php to add is_admin() check
Write-Host "🔧 Fixing Hook.php..." -ForegroundColor Yellow
$hookFile = "$PluginDir\app\Hook\Hook.php"

if (Test-Path $hookFile) {
    try {
        $content = Get-Content -Path $hookFile -Raw -Encoding UTF8
        
        # Check if the fix is already applied
        if ($content -match "if \(!is_admin\(\)\)") {
            Write-Host "  ✅ Fix already applied to Hook.php" -ForegroundColor Green
        } else {
            # Wrap the template_include hook with is_admin() check
            $oldPattern = 'add_filter\(\'template_include\', \[new ExecuteRouter, \'includeTemplate\'\], 1000, 1\);'
            $newContent = $content -replace $oldPattern, "        // Only apply template override for front-end pages`n        if (!is_admin()) {`n            add_filter('template_include', [new ExecuteRouter, 'includeTemplate'], 1000, 1);`n        }"
            
            # Write back with UTF8 encoding without BOM
            [System.IO.File]::WriteAllText($hookFile, $newContent, [System.Text.UTF8Encoding]::new($false))
            Write-Host "  ✅ Fixed Hook.php - Added is_admin() check" -ForegroundColor Green
        }
    } catch {
        Write-Host "  ❌ Error fixing Hook.php: $_" -ForegroundColor Red
    }
} else {
    Write-Host "  ❌ Hook.php not found" -ForegroundColor Red
}

# Step 4: Test the fix
Write-Host "🧪 Testing the fix..." -ForegroundColor Yellow

# Check if Docker containers are running
try {
    $containers = docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}"
    if ($containers -match "blackcnote-wordpress") {
        Write-Host "  ✅ WordPress container is running" -ForegroundColor Green
        
        # Test admin page
        try {
            $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-admin/" -UseBasicParsing -TimeoutSec 10
            if ($response.Content -match "wp-admin" -and $response.Content -match "WordPress") {
                Write-Host "  ✅ WordPress admin page loads correctly" -ForegroundColor Green
            } else {
                Write-Host "  ⚠️  WordPress admin page may still have issues" -ForegroundColor Yellow
            }
        } catch {
            Write-Host "  ❌ Error testing admin page: $($_.Exception.Message)" -ForegroundColor Red
        }
        
        # Test front-end page
        try {
            $response = Invoke-WebRequest -Uri "http://localhost:8888/" -UseBasicParsing -TimeoutSec 10
            if ($response.Content -match "BlackCnote" -and $response.Content -match "Loading BlackCnote") {
                Write-Host "  ✅ Front-end page still loads React app correctly" -ForegroundColor Green
            } else {
                Write-Host "  ⚠️  Front-end page may have issues" -ForegroundColor Yellow
            }
        } catch {
            Write-Host "  ❌ Error testing front-end page: $($_.Exception.Message)" -ForegroundColor Red
        }
        
    } else {
        Write-Host "  ❌ WordPress container is not running" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Error checking Docker containers" -ForegroundColor Red
}

Write-Host "📁 Backup location: $BackupDir" -ForegroundColor Cyan
Write-Host "🔧 Admin template override fix completed!" -ForegroundColor Green
Write-Host "💡 Please test the WordPress admin at: http://localhost:8888/wp-admin/" -ForegroundColor Cyan 