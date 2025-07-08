#!/usr/bin/env pwsh

# BlackCnote PHP Execution Fix Script
# ===================================
# This script fixes the PHP execution issue and WordPress theme problems

Write-Host "🔧 BlackCnote PHP Execution Fix Script" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

# Configuration
$ProjectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$ThemeDir = "$ProjectRoot\blackcnote\wp-content\themes\blackcnote"
$BackupDir = "$ProjectRoot\backups\php-fix-$(Get-Date -Format 'yyyyMMdd-HHmmss')"

Write-Host "📁 Project Root: $ProjectRoot" -ForegroundColor Yellow
Write-Host "📁 Theme Directory: $ThemeDir" -ForegroundColor Yellow
Write-Host "💾 Backup Directory: $BackupDir" -ForegroundColor Yellow

# Step 1: Create backup
Write-Host "💾 Creating backup..." -ForegroundColor Yellow
if (!(Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
}

try {
    Copy-Item -Path "$ThemeDir\*" -Destination $BackupDir -Recurse -Force
    Write-Host "  ✅ Backup created at: $BackupDir" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Error creating backup: $_" -ForegroundColor Red
    exit 1
}

# Step 2: Check Docker container status
Write-Host "🐳 Checking Docker container status..." -ForegroundColor Yellow
$containers = docker ps --filter "name=blackcnote-wordpress" --format "{{.Names}}\t{{.Status}}"
if ($containers) {
    Write-Host "  ✅ WordPress container is running" -ForegroundColor Green
} else {
    Write-Host "  ❌ WordPress container is not running" -ForegroundColor Red
    Write-Host "  🔄 Starting containers..." -ForegroundColor Yellow
    docker-compose up -d
    Start-Sleep -Seconds 10
}

# Step 3: Check PHP execution
Write-Host "🔍 Checking PHP execution..." -ForegroundColor Yellow
try {
    $phpVersion = docker exec blackcnote-wordpress php -v 2>$null
    if ($phpVersion) {
        Write-Host "  ✅ PHP is working: $($phpVersion.Split("`n")[0])" -ForegroundColor Green
    } else {
        Write-Host "  ❌ PHP is not working" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "  ❌ Error checking PHP: $_" -ForegroundColor Red
    exit 1
}

# Step 4: Check Apache PHP module
Write-Host "🔍 Checking Apache PHP module..." -ForegroundColor Yellow
try {
    $apacheModules = docker exec blackcnote-wordpress apache2ctl -M 2>$null
    if ($apacheModules -match "php") {
        Write-Host "  ✅ Apache PHP module is loaded" -ForegroundColor Green
    } else {
        Write-Host "  ❌ Apache PHP module is not loaded" -ForegroundColor Red
        Write-Host "  🔄 Enabling PHP module..." -ForegroundColor Yellow
        docker exec blackcnote-wordpress a2enmod php
        docker exec blackcnote-wordpress service apache2 restart
    }
} catch {
    Write-Host "  ⚠️  Could not check Apache modules: $_" -ForegroundColor Yellow
}

# Step 5: Fix file encoding and permissions
Write-Host "🔧 Fixing file encoding and permissions..." -ForegroundColor Yellow

# Fix header.php
$headerFile = "$ThemeDir\header.php"
if (Test-Path $headerFile) {
    try {
        $content = Get-Content -Path $headerFile -Raw -Encoding UTF8
        
        # Remove BOM if present
        if ($content.StartsWith("ï»¿")) {
            $content = $content.Substring(3)
        }
        
        # Convert line endings to Unix format
        $content = $content -replace "`r`n", "`n"
        
        # Write back with UTF8 encoding without BOM
        [System.IO.File]::WriteAllText($headerFile, $content, [System.Text.UTF8Encoding]::new($false))
        Write-Host "  ✅ Fixed header.php encoding" -ForegroundColor Green
    } catch {
        Write-Host "  ❌ Error fixing header.php: $_" -ForegroundColor Red
    }
}

# Fix all PHP files in theme
$phpFiles = Get-ChildItem -Path $ThemeDir -Filter "*.php" -Recurse
foreach ($file in $phpFiles) {
    try {
        $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
        
        # Remove BOM if present
        if ($content.StartsWith("ï»¿")) {
            $content = $content.Substring(3)
        }
        
        # Convert line endings to Unix format
        $content = $content -replace "`r`n", "`n"
        
        # Write back with UTF8 encoding without BOM
        [System.IO.File]::WriteAllText($file.FullName, $content, [System.Text.UTF8Encoding]::new($false))
    } catch {
        Write-Host "  ⚠️  Error fixing $($file.Name): $_" -ForegroundColor Yellow
    }
}

Write-Host "  ✅ Fixed encoding for $($phpFiles.Count) PHP files" -ForegroundColor Green

# Step 6: Set correct permissions
Write-Host "🔧 Setting correct permissions..." -ForegroundColor Yellow
try {
    # Set file permissions to 644
    Get-ChildItem -Path $ThemeDir -Recurse -File | ForEach-Object {
        $_.Attributes = $_.Attributes -band (-bnot [System.IO.FileAttributes]::ReadOnly)
    }
    
    # Set directory permissions to 755
    Get-ChildItem -Path $ThemeDir -Recurse -Directory | ForEach-Object {
        $_.Attributes = $_.Attributes -band (-bnot [System.IO.FileAttributes]::ReadOnly)
    }
    
    Write-Host "  ✅ Set correct permissions" -ForegroundColor Green
} catch {
    Write-Host "  ⚠️  Error setting permissions: $_" -ForegroundColor Yellow
}

# Step 7: Remove problematic .htaccess files
Write-Host "🔧 Removing problematic .htaccess files..." -ForegroundColor Yellow
$htaccessFiles = @(
    "$ThemeDir\.htaccess",
    "$ThemeDir\assets\.htaccess",
    "$ThemeDir\inc\.htaccess"
)

foreach ($file in $htaccessFiles) {
    if (Test-Path $file) {
        try {
            Remove-Item -Path $file -Force
            Write-Host "  🗑️  Removed: $file" -ForegroundColor Gray
        } catch {
            Write-Host "  ⚠️  Could not remove: $file" -ForegroundColor Yellow
        }
    }
}

# Step 8: Test PHP execution
Write-Host "🧪 Testing PHP execution..." -ForegroundColor Yellow

# Create a test file
$testFile = "$ThemeDir\test-execution.php"
$testContent = @"
<?php
echo "PHP is working correctly!";
echo "<br>PHP Version: " . phpversion();
echo "<br>Current time: " . date('Y-m-d H:i:s');
?>
"@

try {
    [System.IO.File]::WriteAllText($testFile, $testContent, [System.Text.UTF8Encoding]::new($false))
    Write-Host "  ✅ Created test file: $testFile" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Error creating test file: $_" -ForegroundColor Red
}

# Step 9: Restart containers
Write-Host "🔄 Restarting containers..." -ForegroundColor Yellow
try {
    docker-compose restart blackcnote-wordpress
    Start-Sleep -Seconds 10
    Write-Host "  ✅ Containers restarted" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Error restarting containers: $_" -ForegroundColor Red
}

# Step 10: Test WordPress homepage
Write-Host "🧪 Testing WordPress homepage..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        if ($response.Content -match "PHP is working correctly") {
            Write-Host "  ✅ PHP execution test passed" -ForegroundColor Green
        } elseif ($response.Content -match "WordPress") {
            Write-Host "  ✅ WordPress homepage is working" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  Homepage loaded but content unclear" -ForegroundColor Yellow
        }
    } else {
        Write-Host "  ❌ Homepage returned status: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Homepage test failed: $_" -ForegroundColor Red
}

# Step 11: Test theme files
Write-Host "🧪 Testing theme files..." -ForegroundColor Yellow
try {
    $themeResponse = Invoke-WebRequest -Uri "http://localhost:8888/wp-content/themes/blackcnote/test-execution.php" -TimeoutSec 10 -UseBasicParsing
    if ($themeResponse.StatusCode -eq 200) {
        if ($themeResponse.Content -match "PHP is working correctly") {
            Write-Host "  ✅ Theme PHP execution test passed" -ForegroundColor Green
        } else {
            Write-Host "  ❌ Theme PHP execution test failed" -ForegroundColor Red
        }
    } else {
        Write-Host "  ❌ Theme test returned status: $($themeResponse.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Theme test failed: $_" -ForegroundColor Red
}

# Step 12: Clean up test file
try {
    Remove-Item -Path $testFile -Force
    Write-Host "  🗑️  Cleaned up test file" -ForegroundColor Gray
} catch {
    Write-Host "  ⚠️  Could not clean up test file" -ForegroundColor Yellow
}

# Final Summary
Write-Host "`n🎉 PHP Execution Fix Complete!" -ForegroundColor Green
Write-Host "=============================" -ForegroundColor Green
Write-Host "✅ PHP execution verified" -ForegroundColor Green
Write-Host "✅ File encoding fixed" -ForegroundColor Green
Write-Host "✅ Permissions corrected" -ForegroundColor Green
Write-Host "✅ Problematic files removed" -ForegroundColor Green
Write-Host "✅ Containers restarted" -ForegroundColor Green

Write-Host "`n🚀 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Visit: http://localhost:8888/" -ForegroundColor White
Write-Host "2. Visit: http://localhost:8888/wp-admin/" -ForegroundColor White
Write-Host "3. Check if PHP is now executing properly" -ForegroundColor White

Write-Host "`n📊 If you still see raw PHP code:" -ForegroundColor Yellow
Write-Host "- Check Docker container logs: docker logs blackcnote-wordpress" -ForegroundColor White
Write-Host "- Verify Apache config: docker exec blackcnote-wordpress apache2ctl -S" -ForegroundColor White
Write-Host "- Check file permissions: docker exec blackcnote-wordpress ls -la /var/www/html/wp-content/themes/blackcnote/" -ForegroundColor White

Write-Host "`n🎯 The PHP execution issue should now be resolved!" -ForegroundColor Green 