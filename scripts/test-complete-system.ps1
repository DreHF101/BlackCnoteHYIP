#!/usr/bin/env pwsh

# BlackCnote Complete System Test Script
# ======================================
# This script tests all BlackCnote system components

Write-Host "🧪 BlackCnote Complete System Test" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# Test 1: WordPress Frontend
Write-Host "🔍 Test 1: WordPress Frontend (Port 8888)..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/" -UseBasicParsing -TimeoutSec 10
    
    if ($response.StatusCode -eq 200) {
        Write-Host "  ✅ WordPress frontend returned HTTP 200" -ForegroundColor Green
        
        if ($response.Content -match "Loading BlackCnote") {
            Write-Host "  ✅ React container is present" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  React container not found" -ForegroundColor Yellow
        }
        
        if ($response.Content -match "BlackCnote") {
            Write-Host "  ✅ BlackCnote theme is loading" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  BlackCnote theme not found" -ForegroundColor Yellow
        }
        
        if ($response.Content -match "declare\(strict_types=1\)") {
            Write-Host "  ❌ PHP still showing raw code" -ForegroundColor Red
        } else {
            Write-Host "  ✅ PHP is executing correctly" -ForegroundColor Green
        }
    } else {
        Write-Host "  ❌ WordPress frontend returned HTTP $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Error testing WordPress frontend: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: React App
Write-Host "🔍 Test 2: React App (Port 5174)..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174/" -UseBasicParsing -TimeoutSec 10
    
    if ($response.StatusCode -eq 200) {
        Write-Host "  ✅ React app returned HTTP 200" -ForegroundColor Green
        
        if ($response.Content -match "BlackCnote") {
            Write-Host "  ✅ React app contains BlackCnote content" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  React app content unclear" -ForegroundColor Yellow
        }
    } else {
        Write-Host "  ❌ React app returned HTTP $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Error testing React app: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 3: WordPress Admin
Write-Host "🔍 Test 3: WordPress Admin (Port 8888/wp-admin/)..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-admin/" -UseBasicParsing -TimeoutSec 10
    
    if ($response.StatusCode -eq 200) {
        Write-Host "  ✅ WordPress admin returned HTTP 200" -ForegroundColor Green
        
        if ($response.Content -match "WordPress") {
            Write-Host "  ✅ WordPress admin interface is loading" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  WordPress admin interface unclear" -ForegroundColor Yellow
        }
        
        if ($response.Content -match "Loading BlackCnote") {
            Write-Host "  ❌ Admin still showing React loading" -ForegroundColor Red
        } else {
            Write-Host "  ✅ Admin is not using theme template" -ForegroundColor Green
        }
        
        if ($response.Content -match "wp-admin") {
            Write-Host "  ✅ Admin contains wp-admin elements" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  Admin wp-admin elements unclear" -ForegroundColor Yellow
        }
    } else {
        Write-Host "  ❌ WordPress admin returned HTTP $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Error testing WordPress admin: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Docker Containers
Write-Host "🔍 Test 4: Docker Containers..." -ForegroundColor Yellow
try {
    $containers = docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}"
    
    if ($containers -match "blackcnote-wordpress") {
        Write-Host "  ✅ WordPress container is running" -ForegroundColor Green
    } else {
        Write-Host "  ❌ WordPress container is not running" -ForegroundColor Red
    }
    
    if ($containers -match "blackcnote-react") {
        Write-Host "  ✅ React container is running" -ForegroundColor Green
    } else {
        Write-Host "  ❌ React container is not running" -ForegroundColor Red
    }
    
    if ($containers -match "blackcnote-mysql") {
        Write-Host "  ✅ MySQL container is running" -ForegroundColor Green
    } else {
        Write-Host "  ❌ MySQL container is not running" -ForegroundColor Red
    }
    
} catch {
    Write-Host "  ❌ Error checking Docker containers: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 5: Admin User
Write-Host "🔍 Test 5: Admin User..." -ForegroundColor Yellow
try {
    $result = docker exec blackcnote-wordpress php /var/www/html/scripts/create-admin-user.php
    if ($result -match "already exists") {
        Write-Host "  ✅ Administrator user exists" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️  Admin user status unclear" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  ❌ Error checking admin user: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "🧪 Complete system test finished!" -ForegroundColor Green
Write-Host "💡 Access your BlackCnote system:" -ForegroundColor Cyan
Write-Host "   🌐 Frontend: http://localhost:8888" -ForegroundColor White
Write-Host "   ⚛️  React App: http://localhost:5174" -ForegroundColor White
Write-Host "   🔧 Admin: http://localhost:8888/wp-admin/" -ForegroundColor White
Write-Host "   👤 Login: admin / password" -ForegroundColor White 