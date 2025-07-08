# BlackCnote Development Environment Stop Script
# =============================================
# This script stops the complete BlackCnote development environment
# including React development server and Docker services

param(
    [switch]$SkipDocker = $false,
    [switch]$SkipReact = $false,
    [switch]$Verbose = $false
)

# Set error action preference
$ErrorActionPreference = "Stop"

# Function to write colored output
function Write-ColorOutput {
    param(
        [string]$Message,
        [string]$Color = "White"
    )
    Write-Host $Message -ForegroundColor $Color
}

# Function to check if a port is in use
function Test-Port {
    param([int]$Port)
    try {
        $connection = Test-NetConnection -ComputerName "localhost" -Port $Port -InformationLevel Quiet -WarningAction SilentlyContinue
        return $connection.TcpTestSucceeded
    }
    catch {
        return $false
    }
}

# Function to kill process on port
function Stop-ProcessOnPort {
    param([int]$Port)
    try {
        $process = Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue | Select-Object -ExpandProperty OwningProcess
        if ($process) {
            Stop-Process -Id $process -Force -ErrorAction SilentlyContinue
            Write-ColorOutput "✅ Killed process on port $Port" "Green"
            return $true
        }
        return $false
    }
    catch {
        Write-ColorOutput "⚠️ Could not kill process on port $Port" "Yellow"
        return $false
    }
}

# Function to check Docker status
function Test-DockerStatus {
    try {
        $dockerVersion = docker --version 2>$null
        if ($dockerVersion) {
            $dockerRunning = docker info 2>$null
            if ($dockerRunning) {
                return $true
            }
        }
        return $false
    }
    catch {
        return $false
    }
}

# Function to stop Docker services
function Stop-DockerServices {
    Write-ColorOutput "🐳 Stopping Docker services..." "Cyan"
    
    if (-not (Test-DockerStatus)) {
        Write-ColorOutput "⚠️ Docker is not running" "Yellow"
        return $true
    }
    
    try {
        # Check if containers are running
        $runningContainers = docker ps --format "table {{.Names}}" | Select-String "blackcnote"
        
        if (-not $runningContainers) {
            Write-ColorOutput "✅ No BlackCnote Docker services are running" "Green"
            return $true
        }
        
        # Stop Docker services
        Write-ColorOutput "🛑 Stopping BlackCnote Docker services..." "Yellow"
        docker-compose down
        
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✅ Docker services stopped successfully" "Green"
            return $true
        } else {
            Write-ColorOutput "❌ Failed to stop Docker services" "Red"
            return $false
        }
    }
    catch {
        Write-ColorOutput "❌ Error stopping Docker services: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to stop React development server
function Stop-ReactDevServer {
    Write-ColorOutput "⚛️ Stopping React development server..." "Cyan"
    
    # Check if React server is running on port 5174
    if (Test-Port 5174) {
        Write-ColorOutput "🛑 Stopping React development server on port 5174..." "Yellow"
        $stopped = Stop-ProcessOnPort 5174
        
        if ($stopped) {
            Write-ColorOutput "✅ React development server stopped successfully" "Green"
            return $true
        } else {
            Write-ColorOutput "❌ Failed to stop React development server" "Red"
            return $false
        }
    } else {
        Write-ColorOutput "✅ React development server is not running" "Green"
        return $true
    }
}

# Function to display final status
function Show-FinalStatus {
    Write-ColorOutput "📊 **Final Status Check**" "Magenta"
    Write-ColorOutput "=======================" "Magenta"
    
    # Check Docker services
    $dockerRunning = Test-DockerStatus
    if ($dockerRunning) {
        $containers = docker ps --format "table {{.Names}}\t{{.Status}}" | Select-String "blackcnote"
        if ($containers) {
            Write-ColorOutput "⚠️ Some Docker services are still running:" "Yellow"
            $containers | ForEach-Object { Write-ColorOutput "   $_" "White" }
        } else {
            Write-ColorOutput "✅ All Docker services stopped" "Green"
        }
    } else {
        Write-ColorOutput "✅ Docker is not running" "Green"
    }
    
    # Check React server
    if (Test-Port 5174) {
        Write-ColorOutput "⚠️ React development server is still running on port 5174" "Yellow"
    } else {
        Write-ColorOutput "✅ React development server stopped" "Green"
    }
    
    # Check other ports
    $ports = @(8888, 8080, 8081, 8025)
    $activePorts = @()
    
    foreach ($port in $ports) {
        if (Test-Port $port) {
            $activePorts += $port
        }
    }
    
    if ($activePorts.Count -gt 0) {
        Write-ColorOutput "⚠️ Some services are still running on ports: $($activePorts -join ', ')" "Yellow"
    } else {
        Write-ColorOutput "✅ All development services stopped" "Green"
    }
}

# Main execution
try {
    Write-ColorOutput "🛑 **BlackCnote Development Environment Shutdown**" "Magenta"
    Write-ColorOutput "===============================================" "Magenta"
    Write-ColorOutput "Stopping complete development environment..." "White"
    Write-ColorOutput ""
    
    $success = $true
    
    # Stop React development server (unless skipped)
    if (-not $SkipReact) {
        $reactSuccess = Stop-ReactDevServer
        if (-not $reactSuccess) {
            $success = $false
        }
    } else {
        Write-ColorOutput "⏭️ Skipping React development server shutdown" "Yellow"
    }
    
    # Stop Docker services (unless skipped)
    if (-not $SkipDocker) {
        $dockerSuccess = Stop-DockerServices
        if (-not $dockerSuccess) {
            $success = $false
        }
    } else {
        Write-ColorOutput "⏭️ Skipping Docker services shutdown" "Yellow"
    }
    
    Write-ColorOutput ""
    
    if ($success) {
        Write-ColorOutput "🎉 **Development environment stopped successfully!**" "Green"
    } else {
        Write-ColorOutput "⚠️ **Development environment stopped with some issues**" "Yellow"
    }
    
    Show-FinalStatus
    
    Write-ColorOutput ""
    Write-ColorOutput "🔄 **To restart the development environment:**" "Cyan"
    Write-ColorOutput "   - Run 'npm run dev:full'" "White"
    Write-ColorOutput "   - Or run './start-dev-simple.ps1'" "White"
}
catch {
    Write-ColorOutput "❌ **Fatal error during shutdown:** $($_.Exception.Message)" "Red"
    exit 1
} 