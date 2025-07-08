# BlackCnote React Integration Fix Script
# Automatically fixes issues found by the test script
# Version: 1.0.0

param(
    [switch]$Verbose,
    [switch]$CheckOnly
)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { Write-ColorOutput $args "Green" }
function Write-Warning { Write-ColorOutput $args "Yellow" }
function Write-Error { Write-ColorOutput $args "Red" }
function Write-Info { Write-ColorOutput $args "Cyan" }

Write-Info "=========================================="
Write-Info "BLACKCNOTE REACT INTEGRATION FIX SCRIPT"
Write-Info "=========================================="
Write-Info "Starting automated fixes at: $(Get-Date)"
Write-Info ""

# Step 1: Fix CORS Plugin strict_types Issues
Write-Info "Step 1: Fixing CORS Plugin strict_types Issues..."
Write-Info "=================================================="

$corsPluginPath = "blackcnote\wp-content\plugins\blackcnote-cors\blackcnote-cors.php"
if (Test-Path $corsPluginPath) {
    Write-Info "Found CORS plugin file, checking for strict_types issues..."
    
    $corsContent = Get-Content $corsPluginPath -Raw
    if ($corsContent -match 'declare\(strict_types=1\);') {
        # Check if it's the first statement
        $lines = $corsContent -split "`n"
        $firstNonEmptyLine = $lines | Where-Object { $_.Trim() -ne "" -and $_.Trim() -notmatch '^<\?php' } | Select-Object -First 1
        
        if ($firstNonEmptyLine -match 'declare\(strict_types=1\);') {
            Write-Success "✅ CORS plugin strict_types declaration is already correct"
        } else {
            Write-Warning "⚠️  Fixing CORS plugin strict_types declaration..."
            
            # Remove existing strict_types declaration
            $corsContent = $corsContent -replace 'declare\(strict_types=1\);\s*', ''
            
            # Add it as the first statement after <?php
            $corsContent = $corsContent -replace '^<\?php', "<?php`ndeclare(strict_types=1);"
            
            # Save the fixed file
            Set-Content -Path $corsPluginPath -Value $corsContent -Encoding UTF8
            Write-Success "✅ CORS plugin strict_types declaration fixed"
        }
    } else {
        Write-Warning "⚠️  Adding strict_types declaration to CORS plugin..."
        $corsContent = $corsContent -replace '^<\?php', "<?php`ndeclare(strict_types=1);"
        Set-Content -Path $corsPluginPath -Value $corsContent -Encoding UTF8
        Write-Success "✅ CORS plugin strict_types declaration added"
    }
} else {
    Write-Warning "⚠️  CORS plugin file not found"
}

Write-Info ""

# Step 2: Check and Fix Database Connection
Write-Info "Step 2: Checking Database Connection..."
Write-Info "======================================="

# Check if Docker containers are running
try {
    $containers = docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}" 2>$null
    if ($containers) {
        Write-Success "✅ Docker containers found:"
        Write-Info $containers
        
        # Check MySQL container specifically
        $mysqlContainer = docker ps --filter "name=blackcnote_mysql" --format "{{.Names}}" 2>$null
        if ($mysqlContainer) {
            Write-Success "✅ MySQL container is running"
            
            # Test MySQL connection
            try {
                $mysqlTest = docker exec blackcnote_mysql mysql -u root -proot -e "SELECT 1;" 2>$null
                if ($mysqlTest) {
                    Write-Success "✅ MySQL connection test successful"
                } else {
                    Write-Warning "⚠️  MySQL connection test failed"
                }
            } catch {
                Write-Warning "⚠️  MySQL connection test failed: $($_.Exception.Message)"
            }
        } else {
            Write-Error "❌ MySQL container not running"
        }
    } else {
        Write-Error "❌ No BlackCnote Docker containers found"
    }
} catch {
    Write-Error "❌ Docker command failed: $($_.Exception.Message)"
}

Write-Info ""

# Step 3: Check WordPress Configuration
Write-Info "Step 3: Checking WordPress Configuration..."
Write-Info "============================================="

$wpConfigPath = "blackcnote\wp-config.php"
if (Test-Path $wpConfigPath) {
    Write-Success "✅ wp-config.php found"
    
    $wpConfigContent = Get-Content $wpConfigPath -Raw
    
    # Check database configuration
    if ($wpConfigContent -match "DB_HOST.*localhost") {
        Write-Success "✅ Database host configured for localhost"
    } else {
        Write-Warning "⚠️  Database host not configured for localhost"
    }
    
    if ($wpConfigContent -match "DB_NAME.*blackcnote") {
        Write-Success "✅ Database name configured as blackcnote"
    } else {
        Write-Warning "⚠️  Database name not configured as blackcnote"
    }
    
    # Check debug settings
    if ($wpConfigContent -match "WP_DEBUG.*true") {
        Write-Success "✅ WordPress debug mode enabled"
    } else {
        Write-Warning "⚠️  WordPress debug mode not enabled"
    }
} else {
    Write-Error "❌ wp-config.php not found"
}

Write-Info ""

# Step 4: Restart Docker Services
Write-Info "Step 4: Restarting Docker Services..."
Write-Info "====================================="

if (-not $CheckOnly) {
    Write-Info "Restarting BlackCnote Docker services..."
    
    try {
        # Stop services
        docker-compose -f config/docker/docker-compose.yml down 2>$null
        Write-Success "✅ Docker services stopped"
        
        # Start services
        docker-compose -f config/docker/docker-compose.yml up -d 2>$null
        Write-Success "✅ Docker services started"
        
        # Wait for services to be ready
        Write-Info "Waiting for services to be ready..."
        Start-Sleep -Seconds 30
        
        # Check service status
        $runningContainers = docker ps --filter "name=blackcnote" --format "{{.Names}}" 2>$null
        if ($runningContainers) {
            Write-Success "✅ Services restarted successfully"
            Write-Info "Running containers:"
            Write-Info $runningContainers
        } else {
            Write-Error "❌ Services failed to start"
        }
    } catch {
        Write-Error "❌ Failed to restart services: $($_.Exception.Message)"
    }
} else {
    Write-Info "Check-only mode: Skipping service restart"
}

Write-Info ""

# Step 5: Test WordPress Accessibility
Write-Info "Step 5: Testing WordPress Accessibility..."
Write-Info "==========================================="

Write-Info "Waiting for WordPress to be ready..."
Start-Sleep -Seconds 10

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 30 -UseBasicParsing
    Write-Success "✅ WordPress frontend accessible (HTTP $($response.StatusCode))"
    
    # Check for React container
    if ($response.Content -match 'blackcnote-react-app') {
        Write-Success "✅ React app container found in frontend"
    } else {
        Write-Warning "⚠️  React app container not found in frontend"
        
        # Check for root div
        if ($response.Content -match 'id="root"') {
            Write-Warning "⚠️  Root div found but without blackcnote-react-app class"
        } else {
            Write-Error "❌ Root div not found in frontend"
        }
    }
    
    # Check for React assets
    if ($response.Content -match 'assets/.*\.js') {
        Write-Success "✅ React JavaScript assets found in frontend"
    } else {
        Write-Warning "⚠️  React JavaScript assets not found in frontend"
    }
    
    if ($response.Content -match 'assets/.*\.css') {
        Write-Success "✅ React CSS assets found in frontend"
    } else {
        Write-Warning "⚠️  React CSS assets not found in frontend"
    }
    
    # Check for API settings
    if ($response.Content -match 'blackCnoteApiSettings') {
        Write-Success "✅ React API settings found in frontend"
    } else {
        Write-Warning "⚠️  React API settings not found in frontend"
    }
    
} catch {
    Write-Error "❌ WordPress frontend still not accessible: $($_.Exception.Message)"
}

Write-Info ""

# Step 6: Final Recommendations
Write-Info "Step 6: Final Recommendations"
Write-Info "============================="

Write-Info "🔧 If WordPress is still not accessible:"
Write-Info "   1. Check Docker Desktop is running"
Write-Info "   2. Check if ports 8888, 3306 are available"
Write-Info "   3. Run: docker-compose -f config/docker/docker-compose.yml logs"
Write-Info "   4. Check firewall settings"

Write-Info ""
Write-Info "🔧 If React container is still missing:"
Write-Info "   1. Clear browser cache and try again"
Write-Info "   2. Check browser console for JavaScript errors"
Write-Info "   3. Verify theme is activated in WordPress admin"
Write-Info "   4. Check if any plugins are conflicting"

Write-Info ""
Write-Info "🔧 For database issues:"
Write-Info "   1. Check MySQL container logs: docker logs blackcnote_mysql"
Write-Info "   2. Verify database credentials in wp-config.php"
Write-Info "   3. Check if database exists: docker exec blackcnote_mysql mysql -u root -proot -e 'SHOW DATABASES;'"

Write-Info ""
Write-Info "=========================================="
Write-Info "REACT INTEGRATION FIX COMPLETE"
Write-Info "==========================================" 