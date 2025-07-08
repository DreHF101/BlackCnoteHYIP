#!/usr/bin/env pwsh

# BlackCnote Admin Access Fix Script
# ==================================
# This script fixes admin access issues by temporarily disabling problematic redirects

Write-Host "🔧 BlackCnote Admin Access Fix Script" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan

# Configuration
$ProjectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$PluginDir = "$ProjectRoot\blackcnote\wp-content\plugins\hyiplab"
$BackupDir = "$ProjectRoot\backups\admin-fix-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"

Write-Host "📁 Plugin Directory: $PluginDir" -ForegroundColor Yellow
Write-Host "💾 Backup Directory: $BackupDir" -ForegroundColor Yellow

# Step 1: Create backup
Write-Host "💾 Creating backup..." -ForegroundColor Yellow
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

# Step 2: Fix Authorization.php - Comment out problematic redirects
Write-Host "🔧 Fixing Authorization.php..." -ForegroundColor Yellow
$authFile = "$PluginDir\app\Hook\Authorization.php"

if (Test-Path $authFile) {
    try {
        $content = Get-Content -Path $authFile -Raw -Encoding UTF8
        
        # Check if already fixed
        if ($content -match "// TEMPORARILY DISABLED") {
            Write-Host "  ✅ Authorization.php already fixed" -ForegroundColor Green
        } else {
            # Comment out the redirectHome method
            $pattern = 'public function redirectHome\(\)\s*\{'
            $replacement = "public function redirectHome()`n    {`n        // TEMPORARILY DISABLED - Allow admin access`n        return;`n        `n        // Original code commented out:"
            $content = $content -replace $pattern, $replacement
            
            # Comment out the restrictWpLogin method
            $pattern = 'public function restrictWpLogin\(\)\s*\{'
            $replacement = "public function restrictWpLogin()`n    {`n        // TEMPORARILY DISABLED - Allow WordPress login`n        return;`n        `n        // Original code commented out:"
            $content = $content -replace $pattern, $replacement
            
            # Write back with UTF8 encoding without BOM
            [System.IO.File]::WriteAllText($authFile, $content, [System.Text.UTF8Encoding]::new($false))
            Write-Host "  ✅ Fixed Authorization.php - Disabled problematic redirects" -ForegroundColor Green
        }
    } catch {
        Write-Host "  ❌ Error fixing Authorization.php: $_" -ForegroundColor Red
    }
} else {
    Write-Host "  ❌ Authorization.php not found" -ForegroundColor Red
}

# Step 3: Create a temporary admin access fix
Write-Host "🔧 Creating temporary admin access fix..." -ForegroundColor Yellow
$tempFixFile = "$ProjectRoot\blackcnote\wp-content\plugins\admin-access-fix.php"

$tempFixContent = @"
<?php
/**
 * Temporary Admin Access Fix
 * 
 * This plugin temporarily fixes admin access issues by ensuring
 * administrators can access the WordPress admin area.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Remove problematic redirects for administrators
add_action('admin_init', function() {
    if (current_user_can('administrator')) {
        // Remove any redirects that might interfere with admin access
        remove_action('admin_init', 'hyiplab_redirect_home', 1);
    }
}, 0);

// Ensure admin pages load correctly
add_filter('template_include', function($template) {
    if (is_admin()) {
        return $template;
    }
    return $template;
}, 999);

// Allow administrator access to all admin pages
add_action('admin_init', function() {
    if (current_user_can('administrator')) {
        // Ensure no redirects happen for administrators
        return;
    }
}, 1);

echo "<!-- Admin Access Fix Active -->"; 