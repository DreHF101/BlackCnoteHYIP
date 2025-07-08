# BlackCnote Performance Monitor
# Monitors system performance and provides optimization recommendations

Write-Host "🔍 BlackCnote Performance Monitor" -ForegroundColor Cyan
Write-Host "===============================" -ForegroundColor Cyan
Write-Host ""

# Check Docker container performance
Write-Host "🐳 Docker Container Performance:" -ForegroundColor Yellow
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}"

Write-Host ""
Write-Host "📊 Service Performance:" -ForegroundColor Yellow

# Check WordPress
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    Write-Host "   ✅ WordPress: Accessible" -ForegroundColor Green
} catch {
    Write-Host "   ❌ WordPress: Not accessible" -ForegroundColor Red
}

# Check React App
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 10 -UseBasicParsing
    Write-Host "   ✅ React App: Accessible" -ForegroundColor Green
} catch {
    Write-Host "   ❌ React App: Not accessible" -ForegroundColor Red
}

# Check phpMyAdmin
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080" -TimeoutSec 5 -UseBasicParsing
    Write-Host "   ✅ phpMyAdmin: Accessible" -ForegroundColor Green
} catch {
    Write-Host "   ❌ phpMyAdmin: Not accessible" -ForegroundColor Red
}

Write-Host ""
Write-Host "🎯 Performance Recommendations:" -ForegroundColor Cyan
Write-Host "   1. Monitor Docker container resources" -ForegroundColor White
Write-Host "   2. Check for slow database queries" -ForegroundColor White
Write-Host "   3. Optimize image caching" -ForegroundColor White
Write-Host "   4. Review React bundle size" -ForegroundColor White
Write-Host "   5. Enable Redis caching" -ForegroundColor White
Write-Host "   6. Optimize database indexes" -ForegroundColor White 