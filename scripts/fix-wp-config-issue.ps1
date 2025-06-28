# BlackCnote wp-config.php Issue Fix Script
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "BlackCnote wp-config.php Issue Fix Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Stop all Docker containers
Write-Host "[1/8] Stopping all Docker containers..." -ForegroundColor Yellow
try {
    docker-compose -f config/docker/docker-compose.yml --project-directory . down 2>$null
    docker stop $(docker ps -q) 2>$null
    Write-Host "✓ All containers stopped" -ForegroundColor Green
} catch {
    Write-Host "! Error stopping containers: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Step 2: Remove all containers
Write-Host "[2/8] Removing all containers..." -ForegroundColor Yellow
try {
    docker rm -f $(docker ps -aq) 2>$null
    Write-Host "✓ All containers removed" -ForegroundColor Green
} catch {
    Write-Host "! Error removing containers: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Step 3: Remove all Docker volumes
Write-Host "[3/8] Removing all Docker volumes..." -ForegroundColor Yellow
try {
    docker volume rm $(docker volume ls -q) 2>$null
    docker volume prune -f
    Write-Host "✓ All volumes removed" -ForegroundColor Green
} catch {
    Write-Host "! Error removing volumes: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Step 4: Remove all Docker networks
Write-Host "[4/8] Removing all Docker networks..." -ForegroundColor Yellow
try {
    docker network prune -f
    Write-Host "✓ All networks removed" -ForegroundColor Green
} catch {
    Write-Host "! Error removing networks: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Step 5: Full Docker system cleanup
Write-Host "[5/8] Full Docker system cleanup..." -ForegroundColor Yellow
try {
    docker system prune -a -f --volumes
    Write-Host "✓ Docker system cleaned" -ForegroundColor Green
} catch {
    Write-Host "! Error cleaning Docker system: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Step 6: Verify wp-config.php is a file on host
Write-Host "[6/8] Verifying wp-config.php is a file on host..." -ForegroundColor Yellow
$wpConfigPath = "blackcnote\wp-config.php"
$wpConfigDockerPath = "wp-config-docker.php"

if (Test-Path $wpConfigPath) {
    if (Test-Path $wpConfigPath -PathType Container) {
        Write-Host "! WARNING: wp-config.php is a directory on host!" -ForegroundColor Red
        Write-Host "Removing directory..." -ForegroundColor Yellow
        Remove-Item $wpConfigPath -Recurse -Force
        Write-Host "Copying file from backup..." -ForegroundColor Yellow
        Copy-Item $wpConfigDockerPath $wpConfigPath -Force
    } else {
        Write-Host "✓ wp-config.php is a file on host" -ForegroundColor Green
    }
} else {
    Write-Host "! wp-config.php not found, copying from backup..." -ForegroundColor Yellow
    Copy-Item $wpConfigDockerPath $wpConfigPath -Force
}

Write-Host ""

# Step 7: Start Docker environment fresh
Write-Host "[7/8] Starting Docker environment fresh..." -ForegroundColor Yellow
try {
    docker-compose -f config/docker/docker-compose.yml --project-directory . up -d
    Write-Host "✓ Docker environment started" -ForegroundColor Green
} catch {
    Write-Host "! Error starting Docker environment: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Step 8: Wait for services to initialize
Write-Host "[8/8] Waiting for services to initialize..." -ForegroundColor Yellow
Start-Sleep -Seconds 15

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Verification Steps" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# Check WordPress container status
Write-Host "Checking WordPress container status..." -ForegroundColor Yellow
try {
    docker ps --filter "name=blackcnote-wordpress" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
} catch {
    Write-Host "! Error checking container status: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Check wp-config.php inside container
Write-Host "Checking wp-config.php inside container..." -ForegroundColor Yellow
try {
    docker exec blackcnote-wordpress ls -la /var/www/html/wp-config.php
} catch {
    Write-Host "! Error checking wp-config.php: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test WordPress accessibility
Write-Host "Testing WordPress accessibility..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -Method Head -TimeoutSec 10 -ErrorAction SilentlyContinue
    if ($response.StatusCode -eq 200) {
        Write-Host "✓ WordPress is accessible (HTTP 200)" -ForegroundColor Green
    } else {
        Write-Host "! WordPress returned HTTP $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "! WordPress is not accessible: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Fix Complete!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "If WordPress is still not accessible, check:" -ForegroundColor Yellow
Write-Host "1. Docker Desktop is running" -ForegroundColor White
Write-Host "2. Port 8888 is not in use by another application" -ForegroundColor White
Write-Host "3. Check logs: docker logs blackcnote-wordpress" -ForegroundColor White
Write-Host "" 