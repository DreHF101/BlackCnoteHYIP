param(
    [Parameter(Mandatory=$true)]
    [ValidateSet('start', 'stop', 'restart', 'status', 'logs', 'clean')]
    [string]$Action
)

$ErrorActionPreference = "Stop"

function Start-Containers {
    Write-Host "Starting Docker containers..." -ForegroundColor Green
    docker-compose up -d
    Write-Host "WordPress is available at: http://localhost:8000" -ForegroundColor Green
    Write-Host "PHPMyAdmin is available at: http://localhost:8080" -ForegroundColor Green
}

function Stop-Containers {
    Write-Host "Stopping Docker containers..." -ForegroundColor Yellow
    docker-compose down
}

function Restart-Containers {
    Write-Host "Restarting Docker containers..." -ForegroundColor Yellow
    docker-compose restart
}

function Get-ContainerStatus {
    Write-Host "Container Status:" -ForegroundColor Cyan
    docker-compose ps
}

function Get-ContainerLogs {
    Write-Host "Container Logs:" -ForegroundColor Cyan
    docker-compose logs --tail=100
}

function Clean-Environment {
    Write-Host "Cleaning Docker environment..." -ForegroundColor Red
    docker-compose down -v
    Remove-Item -Path "wp-content" -Recurse -Force -ErrorAction SilentlyContinue
    Write-Host "Environment cleaned. Run 'start' to create a fresh installation." -ForegroundColor Green
}

switch ($Action) {
    'start' { Start-Containers }
    'stop' { Stop-Containers }
    'restart' { Restart-Containers }
    'status' { Get-ContainerStatus }
    'logs' { Get-ContainerLogs }
    'clean' { Clean-Environment }
} 