# BlackCnote Ubuntu WSL2 Docker Automation
# This script copies the bash automation script into WSL2 and runs it as your WSL user

Write-Host "=== BlackCnote Ubuntu WSL2 Docker Automation ===" -ForegroundColor Cyan

# Path to the bash automation script
$bashScript = "setup-blackcnote-wsl2.sh"

# Copy the bash script into the WSL2 home directory
Write-Host "Copying $bashScript to WSL2 home directory..." -ForegroundColor Yellow
wsl cp /mnt/c/Users/CASH\ AMERICA\ PAWN/Desktop/BlackCnote/$bashScript ~/

# Make the script executable
Write-Host "Making the script executable in WSL2..." -ForegroundColor Yellow
wsl chmod +x ~/setup-blackcnote-wsl2.sh

# Check Docker Desktop WSL2 integration
Write-Host "Checking Docker Desktop WSL2 integration..." -ForegroundColor Yellow
$dockerInfo = wsl docker info 2>&1
if ($dockerInfo -match "WSL") {
    Write-Host "[OK] Docker Desktop is using WSL2 backend" -ForegroundColor Green
} else {
    Write-Host "[WARNING] Docker Desktop may not be using WSL2 backend" -ForegroundColor Yellow
    Write-Host "Please ensure Docker Desktop is set to use WSL2 backend" -ForegroundColor Yellow
}

# Run the script as the default WSL user
Write-Host "Running the setup script in Ubuntu..." -ForegroundColor Yellow
wsl bash ~/setup-blackcnote-wsl2.sh

# Test WordPress accessibility
Write-Host "Testing WordPress accessibility..." -ForegroundColor Yellow
$httpCode = wsl curl -s -o /dev/null -w "%{http_code}" http://localhost:8888
if ($httpCode -eq "200") {
    Write-Host "[OK] WordPress is accessible at http://localhost:8888" -ForegroundColor Green
} else {
    Write-Host "[ERROR] WordPress returned HTTP $httpCode" -ForegroundColor Red
    Write-Host "Volume mapping issues detected. Trying alternative solutions..." -ForegroundColor Yellow
    
    # Try Windows-based Docker Compose as fallback
    Write-Host "Attempting Windows-based Docker Compose..." -ForegroundColor Cyan
    docker-compose -f docker-compose-windows.yml down 2>$null
    docker-compose -f docker-compose-windows.yml up -d
    
    # Test again
    Start-Sleep -Seconds 10
    $httpCode2 = curl -s -o $null -w "%{http_code}" http://localhost:8888 2>$null
    if ($httpCode2 -eq "200") {
        Write-Host "[OK] WordPress is now accessible using Windows Docker Compose!" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] WordPress still not accessible (HTTP $httpCode2)" -ForegroundColor Red
        Write-Host "Troubleshooting steps:" -ForegroundColor Cyan
        Write-Host "1. Restart Docker Desktop" -ForegroundColor White
        Write-Host "2. Ensure WSL2 integration is enabled in Docker Desktop settings" -ForegroundColor White
        Write-Host "3. Try running Docker Compose from Windows PowerShell instead of WSL2" -ForegroundColor White
        Write-Host "4. Check if the blackcnote directory exists in the expected location" -ForegroundColor White
    }
}

Write-Host "Ubuntu setup completed!" -ForegroundColor Green
Write-Host "Check your browser at http://localhost:8888" -ForegroundColor Cyan 