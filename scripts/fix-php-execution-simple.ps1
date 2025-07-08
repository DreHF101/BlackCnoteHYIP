#!/usr/bin/env pwsh

# BlackCnote PHP Execution Fix Script (Simple Version)
# ===================================================
# This script fixes PHP execution issues by regenerating theme files with proper encoding

Write-Host "🔧 BlackCnote PHP Execution Fix Script" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

# Configuration
$ProjectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$ThemeDir = "$ProjectRoot\blackcnote\wp-content\themes\blackcnote"
$BackupDir = "$ProjectRoot\backups\theme-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"

Write-Host "📁 Theme Directory: $ThemeDir" -ForegroundColor Yellow
Write-Host "💾 Backup Directory: $BackupDir" -ForegroundColor Yellow

# Step 1: Check Docker containers
Write-Host "🔍 Checking Docker containers..." -ForegroundColor Yellow
try {
    $containers = docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}"
    if ($containers -match "blackcnote-wordpress") {
        Write-Host "  ✅ WordPress container is running" -ForegroundColor Green
    } else {
        Write-Host "  ❌ WordPress container is not running" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "  ❌ Error checking Docker containers" -ForegroundColor Red
    exit 1
}

# Step 2: Create backup
Write-Host "💾 Creating theme backup..." -ForegroundColor Yellow
if (!(Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
}

try {
    Copy-Item -Path "$ThemeDir\*" -Destination $BackupDir -Recurse -Force
    Write-Host "  ✅ Backup created at: $BackupDir" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Error creating backup" -ForegroundColor Red
    exit 1
}

# Step 3: Fix file encoding for all PHP files
Write-Host "🔧 Fixing file encoding and line endings..." -ForegroundColor Yellow
$phpFiles = Get-ChildItem -Path $ThemeDir -Filter "*.php" -Recurse
$fixedCount = 0

foreach ($file in $phpFiles) {
    try {
        # Read file content
        $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
        
        # Remove BOM if present
        if ($content.StartsWith([char]0xFEFF)) {
            $content = $content.Substring(1)
        }
        
        # Convert line endings to Unix (LF)
        $content = $content -replace "`r`n", "`n"
        
        # Write back with UTF8 encoding without BOM
        [System.IO.File]::WriteAllText($file.FullName, $content, [System.Text.UTF8Encoding]::new($false))
        
        $fixedCount++
        Write-Host "  ✅ Fixed: $($file.Name)" -ForegroundColor Green
    } catch {
        Write-Host "  ❌ Error fixing: $($file.Name)" -ForegroundColor Red
    }
}

Write-Host "  ✅ Fixed encoding for $fixedCount PHP files" -ForegroundColor Green

# Step 4: Set proper permissions in container
Write-Host "🔐 Setting proper permissions in container..." -ForegroundColor Yellow
try {
    docker exec blackcnote-wordpress sh -c "chown -R www-data:www-data /var/www/html/wp-content/themes/blackcnote"
    docker exec blackcnote-wordpress sh -c "find /var/www/html/wp-content/themes/blackcnote -type f -exec chmod 644 {} \;"
    docker exec blackcnote-wordpress sh -c "find /var/www/html/wp-content/themes/blackcnote -type d -exec chmod 755 {} \;"
    docker exec blackcnote-wordpress sh -c "apache2ctl graceful"
    Write-Host "  ✅ Permissions set successfully" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Error setting permissions" -ForegroundColor Red
}

# Step 5: Test PHP execution
Write-Host "🧪 Testing PHP execution..." -ForegroundColor Yellow
try {
    # Create a test PHP file
    $testPhp = "<?php echo 'PHP is working!'; ?>"
    $testFile = "$ThemeDir\test-php.php"
    [System.IO.File]::WriteAllText($testFile, $testPhp, [System.Text.UTF8Encoding]::new($false))
    
    # Wait for file to be available
    Start-Sleep -Seconds 3
    
    # Test the file
    $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-content/themes/blackcnote/test-php.php" -UseBasicParsing -TimeoutSec 10
    
    if ($response.Content -eq "PHP is working!") {
        Write-Host "  ✅ PHP execution test passed" -ForegroundColor Green
        
        # Clean up test file
        Remove-Item $testFile -Force
    } else {
        Write-Host "  ❌ PHP execution test failed" -ForegroundColor Red
        Write-Host "  Response: $($response.Content)" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Error testing PHP execution: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 6: Test WordPress homepage
Write-Host "🏠 Testing WordPress homepage..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/" -UseBasicParsing -TimeoutSec 10
    
    if ($response.Content -match "<!DOCTYPE html>" -and $response.Content -match "BlackCnote") {
        Write-Host "  ✅ WordPress homepage loads correctly" -ForegroundColor Green
        Write-Host "🎉 SUCCESS: PHP execution issues resolved!" -ForegroundColor Green
    } elseif ($response.Content -match "declare\(strict_types=1\)") {
        Write-Host "  ❌ WordPress homepage still shows raw PHP" -ForegroundColor Red
        Write-Host "🔄 Attempting container rebuild..." -ForegroundColor Yellow
        
        # Rebuild WordPress container
        docker-compose build --no-cache wordpress
        docker-compose up -d wordpress
        
        Start-Sleep -Seconds 15
        
        # Test again
        $response2 = Invoke-WebRequest -Uri "http://localhost:8888/" -UseBasicParsing -TimeoutSec 10
        if ($response2.Content -match "<!DOCTYPE html>") {
            Write-Host "  ✅ WordPress homepage now loads correctly after rebuild" -ForegroundColor Green
            Write-Host "🎉 SUCCESS: PHP execution issues resolved!" -ForegroundColor Green
        } else {
            Write-Host "  ❌ WordPress homepage still has issues after rebuild" -ForegroundColor Red
        }
    } else {
        Write-Host "  ⚠️  WordPress homepage response unclear" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  ❌ Error testing WordPress homepage: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "📁 Backup location: $BackupDir" -ForegroundColor Cyan
Write-Host "🔧 Fix completed!" -ForegroundColor Green 