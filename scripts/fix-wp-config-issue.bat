@echo off
echo ========================================
echo BlackCnote wp-config.php Issue Fix Script
echo ========================================
echo.

echo [1/8] Stopping all Docker containers...
docker-compose -f config/docker/docker-compose.yml --project-directory . down 2>nul
docker stop $(docker ps -q) 2>nul
echo ✓ All containers stopped

echo.
echo [2/8] Removing all containers...
docker rm -f $(docker ps -aq) 2>nul
echo ✓ All containers removed

echo.
echo [3/8] Removing all Docker volumes...
docker volume rm $(docker volume ls -q) 2>nul
docker volume prune -f
echo ✓ All volumes removed

echo.
echo [4/8] Removing all Docker networks...
docker network prune -f
echo ✓ All networks removed

echo.
echo [5/8] Full Docker system cleanup...
docker system prune -a -f --volumes
echo ✓ Docker system cleaned

echo.
echo [6/8] Verifying wp-config.php is a file on host...
if exist "blackcnote\wp-config.php" (
    if exist "blackcnote\wp-config.php\*" (
        echo ! WARNING: wp-config.php is a directory on host!
        echo Removing directory...
        rmdir /s /q "blackcnote\wp-config.php"
        echo Copying file from backup...
        copy "wp-config-docker.php" "blackcnote\wp-config.php"
    ) else (
        echo ✓ wp-config.php is a file on host
    )
) else (
    echo ! wp-config.php not found, copying from backup...
    copy "wp-config-docker.php" "blackcnote\wp-config.php"
)

echo.
echo [7/8] Starting Docker environment fresh...
docker-compose -f config/docker/docker-compose.yml --project-directory . up -d
echo ✓ Docker environment started

echo.
echo [8/8] Waiting for services to initialize...
timeout /t 15 /nobreak >nul

echo.
echo ========================================
echo Verification Steps
echo ========================================

echo Checking WordPress container status...
docker ps --filter "name=blackcnote-wordpress" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo.
echo Checking wp-config.php inside container...
docker exec blackcnote-wordpress ls -la /var/www/html/wp-config.php

echo.
echo Testing WordPress accessibility...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:8888

echo.
echo ========================================
echo Fix Complete!
echo ========================================
echo.
echo If WordPress is still not accessible, check:
echo 1. Docker Desktop is running
echo 2. Port 8888 is not in use by another application
echo 3. Check logs: docker logs blackcnote-wordpress
echo.
pause 