# BlackCnote Canonical Services Test
# Tests all services after startup script cleanup

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "BLACKCNOTE CANONICAL SERVICES TEST" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Test URLs
$services = @{
    "WordPress Frontend" = "http://localhost:8888"
    "WordPress Admin" = "http://localhost:8888/wp-admin/"
    "React App" = "http://localhost:5174"
    "phpMyAdmin" = "http://localhost:8080"
    "Redis Commander" = "http://localhost:8081"
    "MailHog" = "http://localhost:8025"
    "Browsersync" = "http://localhost:3000"
    "Dev Tools" = "http://localhost:9229"
    "Debug Exporter" = "http://localhost:9091"
}

# Test each service
$results = @()
foreach ($service in $services.GetEnumerator()) {
    $name = $service.Key
    $url = $service.Value
    
    try {
        $response = Invoke-WebRequest -Uri $url -TimeoutSec 10 -UseBasicParsing
        if ($response.StatusCode -eq 200) {
            Write-Host "✅ $name - $url" -ForegroundColor Green
            $results += "✅ $name - OPERATIONAL"
        } else {
            Write-Host "⚠️  $name - $url (Status: $($response.StatusCode))" -ForegroundColor Yellow
            $results += "⚠️  $name - Status: $($response.StatusCode)"
        }
    } catch {
        Write-Host "❌ $name - $url (Error: $($_.Exception.Message))" -ForegroundColor Red
        $results += "❌ $name - ERROR: $($_.Exception.Message)"
    }
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "TEST RESULTS SUMMARY" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

foreach ($result in $results) {
    if ($result -like "✅*") {
        Write-Host $result -ForegroundColor Green
    } elseif ($result -like "⚠️*") {
        Write-Host $result -ForegroundColor Yellow
    } else {
        Write-Host $result -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "CANONICAL STARTUP VERIFICATION" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

# Check canonical files
$canonicalFiles = @{
    "start-blackcnote.ps1" = "Canonical Startup Script"
    "docker-compose.yml" = "Canonical Docker Compose"
}

foreach ($file in $canonicalFiles.GetEnumerator()) {
    $path = $file.Key
    $description = $file.Value
    
    if (Test-Path $path) {
        Write-Host "✅ $description - $path" -ForegroundColor Green
    } else {
        Write-Host "❌ $description - $path (MISSING)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "DOCKER CONTAINERS STATUS" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

Write-Host ""
Write-Host "🎉 CANONICAL STARTUP SYSTEM VERIFICATION COMPLETE!" -ForegroundColor Green
Write-Host "Use 'start-blackcnote.ps1' for all future startups." -ForegroundColor White 