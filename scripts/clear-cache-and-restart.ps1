# BlackCnote Cache Clear and Restart Script
# This script clears cache and restarts services to resolve React loading issues

Write-Host "🔄 BlackCnote Cache Clear and Restart Script" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

# Step 1: Stop all BlackCnote containers
Write-Host "`n📦 Stopping BlackCnote containers..." -ForegroundColor Yellow
docker stop $(docker ps -q --filter "name=blackcnote") 2>$null

# Step 2: Clear Docker cache
Write-Host "🧹 Clearing Docker cache..." -ForegroundColor Yellow
docker system prune -f

# Step 3: Start all BlackCnote containers
Write-Host "🚀 Starting BlackCnote containers..." -ForegroundColor Yellow
docker-compose up -d

# Step 4: Wait for services to be ready
Write-Host "⏳ Waiting for services to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Step 5: Check service status
Write-Host "`n🔍 Checking service status..." -ForegroundColor Green
docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

# Step 6: Test WordPress
Write-Host "`n🌐 Testing WordPress..." -ForegroundColor Green
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10
    Write-Host "✅ WordPress is accessible" -ForegroundColor Green
} catch {
    Write-Host "❌ WordPress is not accessible" -ForegroundColor Red
}

# Step 7: Test React App
Write-Host "`n⚛️ Testing React App..." -ForegroundColor Green
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 10
    Write-Host "✅ React App is accessible" -ForegroundColor Green
} catch {
    Write-Host "❌ React App is not accessible" -ForegroundColor Red
}

# Step 8: Test API Health
Write-Host "`n🏥 Testing API Health..." -ForegroundColor Green
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-json/blackcnote/v1/health" -TimeoutSec 10
    Write-Host "✅ API Health endpoint is working" -ForegroundColor Green
} catch {
    Write-Host "❌ API Health endpoint is not working" -ForegroundColor Red
}

Write-Host "`n🎉 Cache clear and restart completed!" -ForegroundColor Cyan
Write-Host "`n📋 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Clear your browser cache (Ctrl+Shift+R)" -ForegroundColor White
Write-Host "2. Open http://localhost:8888 in a new incognito/private window" -ForegroundColor White
Write-Host "3. Check browser console for any errors" -ForegroundColor White
Write-Host "4. If issues persist, check the logs with: docker logs blackcnote_wordpress" -ForegroundColor White

Write-Host "`n🔗 Service URLs:" -ForegroundColor Yellow
Write-Host "• WordPress: http://localhost:8888" -ForegroundColor White
Write-Host "• React App: http://localhost:5174" -ForegroundColor White
Write-Host "• phpMyAdmin: http://localhost:8080" -ForegroundColor White
Write-Host "• MailHog: http://localhost:8025" -ForegroundColor White 