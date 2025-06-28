# BlackCnote Production Deployment Script
# This script handles the complete production deployment process

param(
    [string]$Environment = "production",
    [string]$Domain = "localhost",
    [switch]$SkipSSL = $false,
    [switch]$SkipBackup = $false,
    [switch]$Force = $false
)

Write-Host "[START] Starting BlackCnote Production Deployment..." -ForegroundColor Green
Write-Host "Environment: $Environment" -ForegroundColor Cyan
Write-Host "Domain: $Domain" -ForegroundColor Cyan

# Set error action preference
$ErrorActionPreference = "Stop"

# Configuration
$ProjectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$BackupDir = Join-Path $ProjectRoot "backups"
$LogDir = Join-Path $ProjectRoot "logs"
$SSLDir = Join-Path $ProjectRoot "ssl"

# Create necessary directories
$Directories = @($BackupDir, $LogDir, $SSLDir)
foreach ($Dir in $Directories) {
    if (!(Test-Path $Dir)) {
        New-Item -ItemType Directory -Path $Dir -Force | Out-Null
        Write-Host "Created directory: $Dir" -ForegroundColor Yellow
    }
}

# Function to log messages
function Write-Log {
    param([string]$Message, [string]$Level = "INFO")
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogMessage = "[$Timestamp] [$Level] $Message"
    Write-Host $LogMessage -ForegroundColor $(if ($Level -eq "ERROR") { "Red" } elseif ($Level -eq "WARNING") { "Yellow" } else { "White" })
    
    # Write to log file
    $LogFile = Join-Path $LogDir "deployment-$(Get-Date -Format 'yyyy-MM-dd').log"
    Add-Content -Path $LogFile -Value $LogMessage
}

# Function to check if Docker is running
function Test-DockerRunning {
    try {
        docker version | Out-Null
        return $true
    }
    catch {
        return $false
    }
}

# Function to create backup
function New-Backup {
    if ($SkipBackup) {
        Write-Log "Skipping backup as requested" "WARNING"
        return
    }
    
    Write-Log "Creating backup before deployment..."
    $BackupFile = Join-Path $BackupDir "blackcnote-backup-$(Get-Date -Format 'yyyy-MM-dd-HHmmss').sql"
    
    try {
        # Backup database if running
        if (Test-DockerRunning) {
            docker exec blackcnote_mysql_1 mysqldump -u root -pblackcnote_password blackcnote > $BackupFile
            Write-Log "Database backup created: $BackupFile" "INFO"
        }
    }
    catch {
        Write-Log "Warning: Could not create database backup: $($_.Exception.Message)" "WARNING"
    }
}

# Function to generate SSL certificates
function New-SSLCertificates {
    if ($SkipSSL) {
        Write-Log "Skipping SSL certificate generation as requested" "WARNING"
        return
    }
    
    Write-Log "Generating SSL certificates for $Domain..."
    
    try {
        # Create self-signed certificates for development
        if ($Domain -eq "localhost") {
            $CertFile = Join-Path $SSLDir "blackcnote.crt"
            $KeyFile = Join-Path $SSLDir "blackcnote.key"
            
            # Generate self-signed certificate
            openssl req -x509 -nodes -days 365 -newkey rsa:2048 `
                -keyout $KeyFile -out $CertFile `
                -subj "/C=US/ST=State/L=City/O=BlackCnote/CN=$Domain"
            
            Write-Log "Self-signed SSL certificates generated" "INFO"
        }
        else {
            Write-Log "For production domains, please obtain SSL certificates from a trusted CA" "WARNING"
            Write-Log "Place certificates in: $SSLDir" "INFO"
        }
    }
    catch {
        Write-Log "Warning: Could not generate SSL certificates: $($_.Exception.Message)" "WARNING"
    }
}

# Function to update configuration files
function Update-Configurations {
    Write-Log "Updating configuration files for production..."
    
    # Update wp-config.php for production
    $WpConfig = Join-Path $ProjectRoot "wp-config.php"
    if (Test-Path $WpConfig) {
        $Content = Get-Content $WpConfig -Raw
        $Content = $Content -replace "define\(\s*'WP_DEBUG'\s*,\s*true\s*\);", "define( 'WP_DEBUG', false );"
        $Content = $Content -replace "define\(\s*'WP_DEBUG_DISPLAY'\s*,\s*true\s*\);", "define( 'WP_DEBUG_DISPLAY', false );"
        $Content = $Content -replace "define\(\s*'SCRIPT_DEBUG'\s*,\s*true\s*\);", "define( 'SCRIPT_DEBUG', false );"
        Set-Content -Path $WpConfig -Value $Content
        Write-Log "Updated wp-config.php for production" "INFO"
    }
    
    # Update Nginx configuration
    $NginxConfig = Join-Path $ProjectRoot "config/nginx/blackcnote-prod.conf"
    if (Test-Path $NginxConfig) {
        $Content = Get-Content $NginxConfig -Raw
        $Content = $Content -replace "server_name localhost;", "server_name $Domain;"
        Set-Content -Path $NginxConfig -Value $Content
        Write-Log "Updated Nginx configuration for domain: $Domain" "INFO"
    }
}

# Function to deploy with Docker Compose
function Start-ProductionDeployment {
    Write-Log "Starting production deployment with Docker Compose..."
    
    try {
        # Stop existing containers
        Write-Log "Stopping existing containers..."
        docker-compose down --remove-orphans
        
        # Build and start production services
        Write-Log "Building and starting production services..."
        docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
        
        # Wait for services to be ready
        Write-Log "Waiting for services to be ready..."
        Start-Sleep -Seconds 30
        
        # Check service health
        Write-Log "Checking service health..."
        $Services = @("wordpress", "mysql", "redis", "nginx-proxy", "prometheus", "grafana")
        foreach ($Service in $Services) {
            $Health = docker inspect --format='{{.State.Health.Status}}' "blackcnote_${Service}_1" 2>$null
            if ($Health -eq "healthy") {
                Write-Log "Service $Service is healthy" "INFO"
            }
            else {
                Write-Log "Service $Service health status: $Health" "WARNING"
            }
        }
        
        Write-Log "Production deployment completed successfully!" "INFO"
    }
    catch {
        Write-Log "Error during deployment: $($_.Exception.Message)" "ERROR"
        throw
    }
}

# Function to run post-deployment checks
function Test-ProductionDeployment {
    Write-Log "Running post-deployment checks..."
    
    $Checks = @(
        @{ Name = "WordPress Frontend"; URL = "http://localhost:8888" },
        @{ Name = "WordPress Admin"; URL = "http://localhost:8888/wp-admin" },
        @{ Name = "Prometheus"; URL = "http://localhost:9090" },
        @{ Name = "Grafana"; URL = "http://localhost:3000" },
        @{ Name = "AlertManager"; URL = "http://localhost:9093" }
    )
    
    foreach ($Check in $Checks) {
        try {
            $Response = Invoke-WebRequest -Uri $Check.URL -TimeoutSec 10 -UseBasicParsing
            if ($Response.StatusCode -eq 200) {
                Write-Log "$($Check.Name) is accessible" "INFO"
            }
            else {
                Write-Log "$($Check.Name) returned status: $($Response.StatusCode)" "WARNING"
            }
        }
        catch {
            Write-Log "$($Check.Name) is not accessible: $($_.Exception.Message)" "WARNING"
        }
    }
}

# Function to display deployment summary
function Show-DeploymentSummary {
    Write-Host ""
    Write-Host "🎉 BlackCnote Production Deployment Summary" -ForegroundColor Green
    Write-Host "=============================================" -ForegroundColor Green
    Write-Host "Application URLs:" -ForegroundColor Cyan
    Write-Host "  • WordPress Frontend: http://localhost:8888" -ForegroundColor White
    Write-Host "  • WordPress Admin: http://localhost:8888/wp-admin" -ForegroundColor White
    Write-Host "  • React App: http://localhost:3001" -ForegroundColor White
    Write-Host ""
    Write-Host "Monitoring URLs:" -ForegroundColor Cyan
    Write-Host "  • Prometheus: http://localhost:9090" -ForegroundColor White
    Write-Host "  • Grafana: http://localhost:3000 (admin/admin)" -ForegroundColor White
    Write-Host "  • AlertManager: http://localhost:9093" -ForegroundColor White
    Write-Host ""
    Write-Host "Management Commands:" -ForegroundColor Cyan
    Write-Host "  • View logs: docker-compose logs -f" -ForegroundColor White
    Write-Host "  • Stop services: docker-compose down" -ForegroundColor White
    Write-Host "  • Restart services: docker-compose restart" -ForegroundColor White
    Write-Host ""
    Write-Host "Next Steps:" -ForegroundColor Cyan
    Write-Host "  1. Configure your domain DNS to point to this server" -ForegroundColor White
    Write-Host "  2. Update SSL certificates for your domain" -ForegroundColor White
    Write-Host "  3. Configure email settings in WordPress" -ForegroundColor White
    Write-Host "  4. Set up automated backups" -ForegroundColor White
    Write-Host "  5. Configure monitoring alerts" -ForegroundColor White
    Write-Host ""
}

# Main deployment process
try {
    Write-Log "Starting BlackCnote production deployment..."
    
    # Check prerequisites
    if (!(Test-DockerRunning)) {
        throw "Docker is not running. Please start Docker and try again."
    }
    
    # Create backup
    New-Backup
    
    # Generate SSL certificates
    New-SSLCertificates
    
    # Update configurations
    Update-Configurations
    
    # Deploy services
    Start-ProductionDeployment
    
    # Run health checks
    Test-ProductionDeployment
    
    # Show summary
    Show-DeploymentSummary
    
    Write-Host "[SUCCESS] BlackCnote Production Deployment Summary" -ForegroundColor Green
    
    Write-Log "Production deployment completed successfully!" "INFO"
}
catch {
    Write-Log "Deployment failed: $($_.Exception.Message)" "ERROR"
    Write-Host "[ERROR] Deployment failed. Check logs for details." -ForegroundColor Red
    exit 1
} 