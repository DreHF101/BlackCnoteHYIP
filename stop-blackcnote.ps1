# BlackCnote Stop Script
# Stops all BlackCnote services

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "BLACKCNOTE STOP SCRIPT" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Stopping all BlackCnote services..." -ForegroundColor Yellow
docker-compose down --remove-orphans

Write-Host "All services stopped" -ForegroundColor Green
Write-Host ""
Write-Host "To start services again, run: .\start-blackcnote.bat" -ForegroundColor White
