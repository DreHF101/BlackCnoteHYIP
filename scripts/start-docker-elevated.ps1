# BlackCnote Enhanced Docker Startup Script
# This script starts Docker Desktop with elevated privileges and proper configuration

param(
    [switch]$StartBlackCnote,
    [switch]$Verbose,
    [switch]$CheckOnly
)

$ErrorActionPreference = "Stop"

function Write-ColorOutput($ForegroundColor) {
    $fc = $host.UI.RawUI.ForegroundColor
    $host.UI.RawUI.ForegroundColor = $ForegroundColor
    if ($args) {
        Write-Output $args
    }
    $host.UI.RawUI.ForegroundColor = $fc
}

function Write-Success { Write-ColorOutput Green $args }
function Write-Warning { Write-ColorOutput Yellow $args }
function Write-Error { Write-ColorOutput Red $args }
function Write-Info { Write-ColorOutput Cyan $args }

function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

Write-Info "BlackCnote Enhanced Docker Startup Script"
Write-Info "============================================="
Write-Output ""

if (-not (Test-Administrator)) {
    Write-Error "This script must be run as Administrator!"
    Write-Output "Please right-click PowerShell and select 'Run as Administrator'"
    Write-Output "Then run this script again."
    exit 1
}

Write-Success "Running with administrator privileges"
Write-Output ""

$dockerPath = "C:\Program Files\Docker\Docker\Docker Desktop.exe"
if (-not (Test-Path $dockerPath)) {
    Write-Error "Docker Desktop not found at: $dockerPath"
    Write-Output "Please install Docker Desktop first."
    exit 1
}

Write-Success "Docker Desktop found"
Write-Output ""

$daemonConfigPath = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\config\docker\daemon.json"
if (Test-Path $daemonConfigPath) {
    Write-Success "BlackCnote Docker daemon configuration found"
    
    $dockerDaemonPath = "$env:USERPROFILE\.docker\daemon.json"
    $dockerDaemonDir = Split-Path $dockerDaemonPath -Parent
    
    if (-not (Test-Path $dockerDaemonDir)) {
        New-Item -ItemType Directory -Path $dockerDaemonDir -Force | Out-Null
    }
    
    Copy-Item $daemonConfigPath $dockerDaemonPath -Force
    Write-Success "Docker daemon configuration applied"
} else {
    Write-Warning "BlackCnote Docker daemon configuration not found"
    Write-Output "Using default Docker configuration"
}

Write-Output ""

Write-Info "Stopping existing Docker processes..."
try {
    $dockerProcesses = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
    if ($dockerProcesses) {
        $dockerProcesses | Stop-Process -Force
        Start-Sleep -Seconds 3
        Write-Success "Docker processes stopped"
    } else {
        Write-Info "No running Docker processes found"
    }
} catch {
    Write-Warning "Could not stop Docker processes: $($_.Exception.Message)"
}

Write-Output ""

Write-Info "Starting Docker Desktop..."
try {
    $process = Start-Process -FilePath $dockerPath -ArgumentList "--verbose" -PassThru -WindowStyle Hidden
    Write-Success "Docker Desktop started (PID: $($process.Id))"
} catch {
    Write-Error "Failed to start Docker Desktop: $($_.Exception.Message)"
    exit 1
}

Write-Output ""

Write-Info "Waiting for Docker daemon to be ready..."
$maxAttempts = 30
$attempt = 0
$dockerReady = $false

while ($attempt -lt $maxAttempts -and -not $dockerReady) {
    $attempt++
    Write-Output "   Attempt $attempt/$maxAttempts..."
    
    try {
        $null = docker info 2>$null
        if ($LASTEXITCODE -eq 0) {
            $dockerReady = $true
            Write-Success "Docker daemon is ready!"
        } else {
            Start-Sleep -Seconds 2
        }
    } catch {
        Start-Sleep -Seconds 2
    }
}

if (-not $dockerReady) {
    Write-Error "Docker daemon failed to start within $maxAttempts attempts"
    Write-Output "Please check Docker Desktop logs and try again."
    exit 1
}

Write-Output ""

Write-Info "Docker Information:"
try {
    $dockerInfo = docker info 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Output $dockerInfo
    }
} catch {
    Write-Warning "Could not retrieve Docker information"
}

Write-Output ""

Write-Info "Testing Docker functionality..."
try {
    $testResult = docker run --rm hello-world 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Docker functionality test passed"
    } else {
        Write-Warning "Docker functionality test failed: $testResult"
    }
} catch {
    Write-Warning "Could not run Docker functionality test"
}

Write-Output ""

if ($StartBlackCnote) {
    Write-Info "Starting BlackCnote services..."
    
    $projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
    if (Test-Path $projectRoot) {
        Set-Location $projectRoot
        
        if (Test-Path "docker-compose.yml") {
            Write-Info "Starting BlackCnote containers..."
            try {
                docker-compose up -d
                if ($LASTEXITCODE -eq 0) {
                    Write-Success "BlackCnote services started successfully"
                    
                    Start-Sleep -Seconds 5
                    
                    Write-Info "Service Status:"
                    docker-compose ps
                    
                    Write-Output ""
                    Write-Info "Access URLs:"
                    Write-Output "   WordPress: http://localhost:8888"
                    Write-Output "   Admin Panel: http://localhost:8888/wp-admin/"
                    Write-Output "   phpMyAdmin: http://localhost:8080"
                    Write-Output "   React Dev: http://localhost:5174"
                    Write-Output "   MailHog: http://localhost:8025"
                    Write-Output "   Redis Commander: http://localhost:8081"
                } else {
                    Write-Error "Failed to start BlackCnote services"
                }
            } catch {
                Write-Error "Error starting BlackCnote services: $($_.Exception.Message)"
            }
        } else {
            Write-Warning "docker-compose.yml not found in project root"
        }
    } else {
        Write-Warning "BlackCnote project root not found: $projectRoot"
    }
}

Write-Output ""
Write-Success "Docker startup completed successfully!"
Write-Output ""

Write-Info "Useful Commands:"
Write-Output "   Check Docker status: docker info"
Write-Output "   View running containers: docker ps"
Write-Output "   View Docker logs: docker system df"
Write-Output "   Clean up Docker: docker system prune -a"
Write-Output ""

Write-Info "For more information, see:"
Write-Output "   DOCKER-PRIVILEGES-FIX.md"
Write-Output "   DOCKER-SETUP.md"
Write-Output "   BLACKCNOTE-CANONICAL-PATHS.md" 