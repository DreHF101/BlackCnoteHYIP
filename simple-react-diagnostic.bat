@echo off
echo ========================================
echo   React App Diagnostic
echo ========================================
echo.

set COMPOSE_FILE="C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\docker-compose.yml"

echo [INFO] Checking Docker containers...
docker ps

echo.
echo [INFO] Checking React app container specifically...
docker-compose -f %COMPOSE_FILE% ps react-app

echo.
echo [INFO] Checking React app logs...
docker-compose -f %COMPOSE_FILE% logs --tail=10 react-app

echo.
echo [INFO] Testing URLs...
echo Testing http://localhost:5174/ ...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:5174/' -UseBasicParsing -TimeoutSec 5; Write-Host '✓ http://localhost:5174/ - Status:' $response.StatusCode -ForegroundColor Green } catch { Write-Host '✗ http://localhost:5174/ - Failed:' $_.Exception.Message -ForegroundColor Red }"

echo Testing http://localhost:5174/index.html ...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:5174/index.html' -UseBasicParsing -TimeoutSec 5; Write-Host '✓ http://localhost:5174/index.html - Status:' $response.StatusCode -ForegroundColor Green } catch { Write-Host '✗ http://localhost:5174/index.html - Failed:' $_.Exception.Message -ForegroundColor Red }"

echo.
echo [INFO] Checking if port 5174 is listening...
netstat -an | findstr ":5174"

echo.
echo ========================================
echo   Diagnostic Complete
echo ========================================
echo.
echo [NEXT STEPS]
echo 1. If container is not running: docker-compose -f %COMPOSE_FILE% up -d react-app
echo 2. If logs show errors: Check the error messages above
echo 3. If port is not listening: Restart the container
echo.
pause 