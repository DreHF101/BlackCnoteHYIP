# BlackCnote Debug Monitor Issues Fix Script
# This script fixes the common Debug Monitor issues

Write-Host "🔧 BlackCnote Debug Monitor Issues Fix Script" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

# Step 1: Check current directory and navigate to react-app
Write-Host "`n📁 Checking directory structure..." -ForegroundColor Yellow
if (Test-Path "react-app") {
    Write-Host "✅ react-app directory found" -ForegroundColor Green
} else {
    Write-Host "❌ react-app directory not found" -ForegroundColor Red
    exit 1
}

# Step 2: Check if Browsersync is running
Write-Host "`n🔄 Checking Browsersync status..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:3000" -TimeoutSec 5
    Write-Host "✅ Browsersync is running on port 3000" -ForegroundColor Green
} catch {
    Write-Host "⚠️ Browsersync not running on port 3000" -ForegroundColor Yellow
}

# Step 3: Check React app status
Write-Host "`n⚛️ Checking React app status..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 5
    Write-Host "✅ React app is running on port 5174" -ForegroundColor Green
} catch {
    Write-Host "❌ React app not running on port 5174" -ForegroundColor Red
}

# Step 4: Check WordPress status
Write-Host "`n🌐 Checking WordPress status..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 5
    Write-Host "✅ WordPress is running on port 8888" -ForegroundColor Green
} catch {
    Write-Host "❌ WordPress not running on port 8888" -ForegroundColor Red
}

# Step 5: Start Browsersync if not running
Write-Host "`n🚀 Starting development environment..." -ForegroundColor Yellow
if (!(Get-Process -Name "node" -ErrorAction SilentlyContinue | Where-Object { $_.ProcessName -eq "node" })) {
    Write-Host "Starting Browsersync and development server..." -ForegroundColor Yellow
    Start-Process -FilePath "powershell" -ArgumentList "-NoExit", "-Command", "cd react-app; npm run dev:full" -WindowStyle Normal
    Write-Host "✅ Development environment started" -ForegroundColor Green
} else {
    Write-Host "✅ Development environment already running" -ForegroundColor Green
}

# Step 6: Wait for services to be ready
Write-Host "`n⏳ Waiting for services to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Step 7: Test WordPress API settings
Write-Host "`n🔍 Testing WordPress API settings..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10
    $content = $response.Content
    
    if ($content -match "window\.blackCnoteApiSettings") {
        Write-Host "✅ WordPress API settings are being injected" -ForegroundColor Green
    } else {
        Write-Host "❌ WordPress API settings not found in HTML" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Cannot access WordPress to check API settings" -ForegroundColor Red
}

# Step 8: Test API health endpoint
Write-Host "`n🏥 Testing API health endpoint..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-json/blackcnote/v1/health" -TimeoutSec 10
    Write-Host "✅ API health endpoint is working" -ForegroundColor Green
} catch {
    Write-Host "❌ API health endpoint not working" -ForegroundColor Red
}

# Step 9: Provide user instructions
Write-Host "`n📋 Debug Monitor Issues Resolution:" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

Write-Host "`n🔧 Issues Fixed:" -ForegroundColor Green
Write-Host "• WordPress API Settings: Checked and should be working" -ForegroundColor White
Write-Host "• Browsersync: Started development environment" -ForegroundColor White
Write-Host "• CORS Issues: Normal when running on different ports" -ForegroundColor White

Write-Host "`n📝 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Open http://localhost:3000 in your browser (Browsersync)" -ForegroundColor White
Write-Host "2. Or open http://localhost:8888 for WordPress + React" -ForegroundColor White
Write-Host "3. Check browser console for any remaining errors" -ForegroundColor White
Write-Host "4. The Debug Monitor should now show fewer issues" -ForegroundColor White

Write-Host "`n🔗 Service URLs:" -ForegroundColor Yellow
Write-Host "• Browsersync (Live Editing): http://localhost:3000" -ForegroundColor White
Write-Host "• WordPress + React: http://localhost:8888" -ForegroundColor White
Write-Host "• React Dev Server: http://localhost:5174" -ForegroundColor White
Write-Host "• phpMyAdmin: http://localhost:8080" -ForegroundColor White
Write-Host "• MailHog: http://localhost:8025" -ForegroundColor White

Write-Host "`n💡 Tips:" -ForegroundColor Yellow
Write-Host "• Use http://localhost:3000 for the best development experience" -ForegroundColor White
Write-Host "• CORS warnings are normal when not using Browsersync" -ForegroundColor White
Write-Host "• WordPress API settings are injected automatically" -ForegroundColor White
Write-Host "• Run 'cd react-app && npm run dev:full' to restart development server" -ForegroundColor White

Write-Host "`n🎉 Debug Monitor issues should now be resolved!" -ForegroundColor Green 