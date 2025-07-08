# BlackCnote Status Script
# Shows status of all BlackCnote services

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "BLACKCNOTE SERVICES STATUS" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Docker containers:" -ForegroundColor Yellow
docker-compose ps

Write-Host ""
Write-Host "Service URLs:" -ForegroundColor Yellow
Write-Host "- WordPress:      http://localhost:8888" -ForegroundColor White
Write-Host "- WordPress Admin: http://localhost:8888/wp-admin" -ForegroundColor White
Write-Host "- React App:      http://localhost:5174" -ForegroundColor White
Write-Host "- phpMyAdmin:     http://localhost:8080" -ForegroundColor White
Write-Host "- Redis Commander: http://localhost:8081" -ForegroundColor White
Write-Host "- MailHog:        http://localhost:8025" -ForegroundColor White
Write-Host "- Browsersync:    http://localhost:3000" -ForegroundColor White
Write-Host "- Dev Tools:      http://localhost:9229" -ForegroundColor White
Write-Host "- Metrics:        http://localhost:9091" -ForegroundColor White
