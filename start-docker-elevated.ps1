# Docker Desktop Elevated Startup Script
# This script starts Docker Desktop with administrator privileges

param([switch]$Quiet)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    if (-not $Quiet) { Write-Host $Message -ForegroundColor $Color }
}

# Function to check if running as administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Function to wait for service
function Wait-ForService {
    param([string]$Url, [string]$ServiceName, [int]$MaxAttempts = 30)
    Write-ColorOutput "[$ServiceName] Waiting for service..." "Yellow"
    for ($i = 1; $i -le $MaxAttempts; $i++) {
        try {
            $response = Invoke-WebRequest -Uri $Url -TimeoutSec 5 -UseBasicParsing -ErrorAction SilentlyContinue
            if ($response.StatusCode -eq 200) {
                Write-ColorOutput "[$ServiceName] [OK] Ready" "Green"
                return $true
            }
        }
        catch { Write-ColorOutput "[$ServiceName] Attempt $i/$MaxAttempts..." "Yellow" }
        Start-Sleep -Seconds 2
    }
    Write-ColorOutput "[$ServiceName] [ERROR] Failed to start" "Red"
    return $false
}

# Check administrator privileges
if (-not (Test-Administrator)) {
    Write-ColorOutput "[ERROR] This script requires administrator privileges!" "Red"
    Write-ColorOutput "Please run PowerShell as Administrator and try again." "Red"
    exit 1
}

Write-ColorOutput "=== Docker Desktop Elevated Startup ===" "Cyan"
Write-ColorOutput "Starting at: $(Get-Date)" "White"

# Stop Docker Desktop if running
$dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
if ($dockerProcess) {
    Write-ColorOutput "[Docker] Stopping existing Docker Desktop..." "Yellow"
    Stop-Process -Name "Docker Desktop" -Force
    Start-Sleep -Seconds 5
}

# Start Docker Desktop with elevated privileges
Write-ColorOutput "[Docker] Starting Docker Desktop..." "Yellow"
Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized

# Wait for Docker to be ready
Write-ColorOutput "[Docker] Waiting for Docker to be ready..." "Yellow"
$dockerReady = $false
for ($i = 1; $i -le 30; $i++) {
    try {
        $dockerInfo = docker info 2>$null
        if ($LASTEXITCODE -eq 0) {
            $dockerReady = $true
            break
        }
    }
    catch { }
    Start-Sleep -Seconds 2
}

if ($dockerReady) {
    Write-ColorOutput "[Docker] [OK] Docker is ready!" "Green"
    Write-ColorOutput "Docker services are available for BlackCnote." "White"
    
    # Optional: Start BlackCnote services
    $startBlackCnote = Read-Host "`nDo you want to start BlackCnote services now? (Y/n)"
    if ($startBlackCnote -ne "n" -and $startBlackCnote -ne "N") {
        Write-ColorOutput "`nStarting BlackCnote services..." "Yellow"
        
        # Change to project directory
        Set-Location $PSScriptRoot
        
        # Start Docker Compose
        Write-ColorOutput "[BlackCnote] Starting Docker Compose..." "Yellow"
        docker-compose -f config/docker/docker-compose.yml up -d
        
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "[BlackCnote] [OK] Services started successfully!" "Green"
            
            # Wait for services to be ready
            Start-Sleep -Seconds 10
            
            # Check service status
            Write-ColorOutput "`n=== Service Status ===" "Cyan"
            $services = @(
                @{Name="WordPress"; Url="http://localhost:8888"},
                @{Name="React App"; Url="http://localhost:5174"},
                @{Name="PHPMyAdmin"; Url="http://localhost:8080"},
                @{Name="MailHog"; Url="http://localhost:8025"}
            )
            
            foreach ($service in $services) {
                Wait-ForService -Url $service.Url -ServiceName $service.Name -MaxAttempts 5
            }
            
            Write-ColorOutput "`n=== BlackCnote Services Ready ===" "Green"
            Write-ColorOutput "WordPress: http://localhost:8888" "White"
            Write-ColorOutput "WordPress Admin: http://localhost:8888/wp-admin" "White"
            Write-ColorOutput "React App: http://localhost:5174" "White"
            Write-ColorOutput "PHPMyAdmin: http://localhost:8080" "White"
            Write-ColorOutput "MailHog: http://localhost:8025" "White"
        } else {
            Write-ColorOutput "[BlackCnote] [ERROR] Failed to start services" "Red"
        }
    }
} else {
    Write-ColorOutput "[Docker] [ERROR] Docker failed to start properly." "Red"
    Write-ColorOutput "Please check Docker Desktop manually." "Yellow"
    Write-ColorOutput "`nTroubleshooting steps:" "Cyan"
    Write-ColorOutput "1. Open Docker Desktop manually" "White"
    Write-ColorOutput "2. Check Docker Desktop settings" "White"
    Write-ColorOutput "3. Ensure WSL2 integration is enabled" "White"
    Write-ColorOutput "4. Restart Docker Desktop" "White"
}

Write-ColorOutput "`nScript completed at: $(Get-Date)" "White" 