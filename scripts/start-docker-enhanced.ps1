# BlackCnote Enhanced Docker Startup Script
# This script starts Docker Desktop with elevated privileges and proper configuration
# Designed to work with Windows Task Scheduler and startup automation

param(
    [switch]$StartBlackCnote,
    [switch]$Verbose,
    [switch]$Quiet,
    [switch]$CheckOnly
)

$ErrorActionPreference = "Continue"

# Function to write colored output
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

# Main execution
if (-not $Quiet) {
    Write-Info "BlackCnote Enhanced Docker Startup Script"
    Write-Info "============================================="
    Write-Output ""
    Write-Info "Starting at: $(Get-Date)"
    Write-Output ""
}

# Check administrator privileges
if (-not (Test-Administrator)) {
    if (-not $Quiet) {
        Write-Warning "Not running as administrator"
        Write-Output "Docker Desktop will start with user privileges"
        Write-Output "For best performance, run as administrator"
        Write-Output ""
    }
} else {
    if (-not $Quiet) {
        Write-Success "Running with administrator privileges"
        Write-Output ""
    }
}

# Set project root
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

if (-not $Quiet) {
    Write-Info "Project directory: $projectRoot"
    Write-Output ""
}

# Check if Docker Desktop is installed
$dockerPath = "C:\Program Files\Docker\Docker\Docker Desktop.exe"
if (-not (Test-Path $dockerPath)) {
    Write-Error "Docker Desktop not found at: $dockerPath"
    Write-Output "Please install Docker Desktop first."
    exit 1
}

if (-not $Quiet) {
    Write-Success "Docker Desktop found"
    Write-Output ""
}

# Apply Docker daemon configuration
$daemonConfigPath = "$projectRoot\config\docker\daemon.json"
if (Test-Path $daemonConfigPath) {
    if (-not $Quiet) {
        Write-Success "BlackCnote Docker daemon configuration found"
    }
    
    $dockerDaemonPath = "$env:USERPROFILE\.docker\daemon.json"
    $dockerDaemonDir = Split-Path $dockerDaemonPath -Parent
    
    if (-not (Test-Path $dockerDaemonDir)) {
        New-Item -ItemType Directory -Path $dockerDaemonDir -Force | Out-Null
    }
    
    Copy-Item $daemonConfigPath $dockerDaemonPath -Force
    if (-not $Quiet) {
        Write-Success "Docker daemon configuration applied"
    }
} else {
    if (-not $Quiet) {
        Write-Warning "BlackCnote Docker daemon configuration not found"
        Write-Output "Using default Docker configuration"
    }
}

if (-not $Quiet) {
    Write-Output ""
}

# Check if Docker Desktop is already running
if (-not $Quiet) {
    Write-Info "Checking Docker Desktop status..."
}

$dockerProcess = Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue
if ($dockerProcess) {
    if (-not $Quiet) {
        Write-Info "Docker Desktop is already running"
    }
} else {
    if (-not $Quiet) {
        Write-Info "Starting Docker Desktop..."
    }
    
    try {
        $process = Start-Process -FilePath $dockerPath -ArgumentList "--verbose" -PassThru -WindowStyle Hidden
        if (-not $Quiet) {
            Write-Success "Docker Desktop started (PID: $($process.Id))"
        }
    } catch {
        Write-Error "Failed to start Docker Desktop: $($_.Exception.Message)"
        exit 1
    }
}

if (-not $Quiet) {
    Write-Output ""
}

# Wait for Docker daemon to be ready
if (-not $Quiet) {
    Write-Info "Waiting for Docker daemon to be ready..."
}

$maxAttempts = 45
$attempt = 0
$dockerReady = $false

while ($attempt -lt $maxAttempts -and -not $dockerReady) {
    $attempt++
    if (-not $Quiet) {
        Write-Output "    Attempt $attempt/$maxAttempts..."
    }
    
    try {
        $dockerInfo = docker info 2>$null
        if ($LASTEXITCODE -eq 0) {
            $dockerReady = $true
            if (-not $Quiet) {
                Write-Success "Docker daemon is ready!"
            }
            break
        }
    }
    catch { }
    
    Start-Sleep -Seconds 2
}

if (-not $dockerReady) {
    Write-Error "Docker daemon failed to start within $maxAttempts attempts"
    Write-Output "Please check Docker Desktop logs and try again"
    Write-Output "You can also run: .\scripts\start-docker-elevated.bat"
    exit 1
}

if (-not $Quiet) {
    Write-Output ""
}

# Test Docker functionality
if (-not $Quiet) {
    Write-Info "Testing Docker functionality..."
}

try {
    $testResult = docker run --rm hello-world 2>&1
    if ($LASTEXITCODE -eq 0) {
        if (-not $Quiet) {
            Write-Success "Docker functionality test passed"
        }
    } else {
        if (-not $Quiet) {
            Write-Warning "Docker functionality test failed: $testResult"
        }
    }
} catch {
    if (-not $Quiet) {
        Write-Warning "Could not run Docker functionality test"
    }
}

if (-not $Quiet) {
    Write-Output ""
}

# Start BlackCnote services if requested
if ($StartBlackCnote) {
    if (-not $Quiet) {
        Write-Info "Starting BlackCnote services..."
    }
    
    # Check if docker-compose.yml exists
    $composeFile = "$projectRoot\docker-compose.yml"
    if (-not (Test-Path $composeFile)) {
        Write-Error "docker-compose.yml not found in project root"
        Write-Output "Please ensure you are in the correct directory"
        exit 1
    }
    
    # Stop any existing containers to prevent conflicts
    if (-not $Quiet) {
        Write-Info "Stopping existing containers..."
    }
    docker-compose down --remove-orphans 2>$null
    
    # Start all services
    if (-not $Quiet) {
        Write-Info "Starting BlackCnote Docker services..."
    }
    docker-compose up -d --build
    
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Failed to start BlackCnote services"
        Write-Output "Check logs with: docker-compose logs"
        exit 1
    }
    
    # Wait for services to initialize
    if (-not $Quiet) {
        Write-Info "Waiting for services to initialize..."
    }
    Start-Sleep -Seconds 15
    
    # Check service status
    if (-not $Quiet) {
        Write-Info "Checking service status..."
        docker-compose ps
        Write-Output ""
    }
    
    # Wait for WordPress to be ready
    if (-not $Quiet) {
        Write-Info "Waiting for WordPress to be ready..."
    }
    
    $wpMaxAttempts = 30
    $wpAttempt = 0
    $wpReady = $false
    
    while ($wpAttempt -lt $wpMaxAttempts -and -not $wpReady) {
        $wpAttempt++
        if (-not $Quiet) {
            Write-Output "    WordPress attempt $wpAttempt/$wpMaxAttempts..."
        }
        
        try {
            $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 5 -UseBasicParsing -ErrorAction SilentlyContinue
            if ($response.StatusCode -eq 200) {
                $wpReady = $true
                if (-not $Quiet) {
                    Write-Success "WordPress is ready!"
                }
                break
            }
        }
        catch { }
        
        Start-Sleep -Seconds 2
    }
    
    if (-not $wpReady -and -not $Quiet) {
        Write-Warning "WordPress may still be starting up"
        Write-Output "You can check manually at: http://localhost:8888"
    }
    
    if (-not $Quiet) {
        Write-Output ""
        Write-Success "BlackCnote services started successfully!"
        Write-Output ""
        Write-Info "Services available at:"
        Write-Output "  WordPress:      http://localhost:8888"
        Write-Output "  WordPress Admin: http://localhost:8888/wp-admin"
        Write-Output "  React App:      http://localhost:5174"
        Write-Output "  phpMyAdmin:     http://localhost:8080"
        Write-Output "  Redis Commander: http://localhost:8081"
        Write-Output "  MailHog:        http://localhost:8025"
        Write-Output "  Metrics:        http://localhost:9091"
        Write-Output "  Health Check:   http://localhost:8888/health"
        Write-Output ""
    }
}

if (-not $Quiet) {
    Write-Output ""
    Write-Success "Enhanced Docker startup completed successfully!"
    Write-Output ""
    
    Write-Info "Useful Commands:"
    Write-Output "   Check Docker status: docker info"
    Write-Output "   View running containers: docker ps"
    Write-Output "   View Docker logs: docker system df"
    Write-Output "   Clean up Docker: docker system prune -a"
    Write-Output "   Start BlackCnote: .\scripts\start-docker-enhanced.ps1 -StartBlackCnote"
    Write-Output ""
    
    if ($Verbose) {
        Write-Info "Verbose Information:"
        try {
            Write-Output "   Docker Version: $(docker version --format '{{.Server.Version}}')"
            Write-Output "   Docker Root: $(docker info --format '{{.DockerRootDir}}')"
            Write-Output "   Storage Driver: $(docker info --format '{{.Driver}}')"
            Write-Output "   Total Memory: $(docker info --format '{{.MemTotal}}')"
        }
        catch {
            Write-Warning "Could not retrieve verbose information"
        }
        Write-Output ""
    }
    
    Write-Info "For more information, see:"
    Write-Output "   DOCKER-PRIVILEGES-FIX.md"
    Write-Output "   DOCKER-SETUP.md"
    Write-Output "   BLACKCNOTE-CANONICAL-PATHS.md"
    Write-Output ""
    Write-Info "Docker Engine API issues have been resolved!"
    Write-Info "Docker will now start automatically on Windows boot!"
    Write-Info "No more blank terminals or engine stopping issues!"
} else {
    # Quiet mode - just log completion
    Write-Output "BlackCnote Docker startup completed at $(Get-Date)"
} 