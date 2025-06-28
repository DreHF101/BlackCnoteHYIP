# BlackCnote Windows Startup Script
Write-Host "[BlackCnote] Detected Windows environment."

# Set project root
$projectRoot = "$PSScriptRoot"
cd $projectRoot

# Start Docker Compose (Windows)
Write-Host "[BlackCnote] Starting Docker Compose (Windows)..."
docker-compose -f config/docker/docker-compose.yml up -d

Write-Host "[BlackCnote] All services are up!" 