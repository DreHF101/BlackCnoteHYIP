# BlackCnote Docker Restart with URL Fix
# This script restarts Docker with the corrected configuration

Write-Host "BlackCnote Docker Restart with URL Fix" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""

# Stop all containers
Write-Host "Stopping Docker containers..." -ForegroundColor Yellow
docker-compose -f config/docker/docker-compose.yml down

# Wait a moment
Start-Sleep -Seconds 3

# Start containers with fixed configuration
Write-Host "Starting Docker containers with fixed configuration..." -ForegroundColor Yellow
docker-compose -f config/docker/docker-compose.yml up -d

# Wait for containers to be ready
Write-Host "Waiting for containers to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Check container status
Write-Host "Checking container status..." -ForegroundColor Yellow
docker-compose -f config/docker/docker-compose.yml ps

Write-Host ""
Write-Host "Testing WordPress accessibility..." -ForegroundColor Yellow

# Test WordPress
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -Method Head -TimeoutSec 10 -ErrorAction Stop
    Write-Host "SUCCESS: WordPress is accessible at http://localhost:8888" -ForegroundColor Green
} catch {
    Write-Host "ERROR: WordPress is not accessible yet. Waiting..." -ForegroundColor Red
    Start-Sleep -Seconds 10
    
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8888" -Method Head -TimeoutSec 10 -ErrorAction Stop
        Write-Host "SUCCESS: WordPress is now accessible at http://localhost:8888" -ForegroundColor Green
    } catch {
        Write-Host "ERROR: WordPress is still not accessible. Check container logs." -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Running URL fix script..." -ForegroundColor Yellow

# Run the URL fix script
try {
    docker exec -it blackcnote-wordpress php /var/www/html/scripts/fix-docker-urls.php
    Write-Host "SUCCESS: URL fix completed" -ForegroundColor Green
} catch {
    Write-Host "WARNING: URL fix script failed. You may need to run it manually." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "SUCCESS: Docker restart completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Access URLs:" -ForegroundColor Cyan
Write-Host "   WordPress: http://localhost:8888" -ForegroundColor White
Write-Host "   Admin Panel: http://localhost:8888/wp-admin/" -ForegroundColor White
Write-Host "   phpMyAdmin: http://localhost:8080" -ForegroundColor White
Write-Host "   React Dev: http://localhost:5174" -ForegroundColor White
Write-Host "   MailHog: http://localhost:8025" -ForegroundColor White
Write-Host "   Redis Commander: http://localhost:8081" -ForegroundColor White
Write-Host ""
Write-Host "To check logs:" -ForegroundColor Cyan
Write-Host "   docker-compose -f config/docker/docker-compose.yml logs -f" -ForegroundColor Gray
Write-Host "" 