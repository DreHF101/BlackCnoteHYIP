# BlackCnote Final Verification Script
# Tests all services and confirms full operational status

param(
    [switch]$Verbose
)

# Function to write colored output
function Write-ColorOutput {
    param(
        [string]$Message,
        [string]$Color = "White"
    )
    
    $colors = @{
        "Red" = "Red"
        "Green" = "Green"
        "Yellow" = "Yellow"
        "Cyan" = "Cyan"
        "Gray" = "Gray"
        "White" = "White"
    }
    
    if ($colors.ContainsKey($Color)) {
        Write-Host $Message -ForegroundColor $colors[$Color]
    } else {
        Write-Host $Message
    }
}

# Function to test service connectivity
function Test-ServiceConnectivity {
    param([string]$Name, [string]$Url, [int]$Timeout = 10)
    
    try {
        $response = Invoke-WebRequest -Uri $Url -TimeoutSec $Timeout -UseBasicParsing -ErrorAction Stop
        if ($response.StatusCode -eq 200) {
            Write-ColorOutput "[OK] $Name - $Url" "Green"
            return $true
        } else {
            Write-ColorOutput "[WARNING] $Name - $Url (Status: $($response.StatusCode))" "Yellow"
            return $false
        }
    } catch {
        Write-ColorOutput "[ERROR] $Name - $Url" "Red"
        return $false
    }
}

# Function to test Docker containers
function Test-DockerContainers {
    Write-ColorOutput "Testing Docker containers..." "Cyan"
    
    $containers = @(
        "blackcnote-wordpress",
        "blackcnote-react", 
        "blackcnote-mysql",
        "blackcnote-redis",
        "blackcnote-phpmyadmin",
        "blackcnote-redis-commander",
        "blackcnote-mailhog",
        "blackcnote-browsersync",
        "blackcnote-dev-tools",
        "blackcnote-debug-exporter",
        "blackcnote-file-watcher"
    )
    
    $allRunning = $true
    
    try {
        $runningContainers = docker ps --format "{{.Names}}" 2>$null
        
        foreach ($container in $containers) {
            if ($runningContainers -contains $container) {
                Write-ColorOutput "[OK] $($container): Running" "Green"
            } else {
                Write-ColorOutput "[ERROR] $($container): Stopped" "Red"
                $allRunning = $false
            }
        }
    } catch {
        Write-ColorOutput "[ERROR] Docker not accessible" "Red"
        $allRunning = $false
    }
    
    return $allRunning
}

# Function to test canonical paths
function Test-CanonicalPaths {
    Write-ColorOutput "Testing canonical paths..." "Cyan"
    
    $paths = @{
        "Project Root" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
        "WordPress" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote"
        "Theme" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote"
        "React App" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app"
        "Scripts" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\scripts"
        "Tools" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools"
    }
    
    $allValid = $true
    
    foreach ($path in $paths.GetEnumerator()) {
        if (Test-Path $path.Value) {
            Write-ColorOutput "[OK] $($path.Key): $($path.Value)" "Green"
        } else {
            Write-ColorOutput "[ERROR] $($path.Key): $($path.Value)" "Red"
            $allValid = $false
        }
    }
    
    return $allValid
}

# Function to test service URLs
function Test-ServiceURLs {
    Write-ColorOutput "Testing service URLs..." "Cyan"
    
    $services = @{
        "WordPress Frontend" = "http://localhost:8888"
        "React App" = "http://localhost:5174"
        "phpMyAdmin" = "http://localhost:8080"
        "Redis Commander" = "http://localhost:8081"
        "MailHog" = "http://localhost:8025"
        "Browsersync" = "http://localhost:3000"
        "Debug Exporter" = "http://localhost:9091"
    }
    
    $allAccessible = $true
    
    foreach ($service in $services.GetEnumerator()) {
        $result = Test-ServiceConnectivity -Name $service.Key -Url $service.Value
        if (-not $result) {
            $allAccessible = $false
        }
    }
    
    return $allAccessible
}

# Main execution
Write-ColorOutput "BlackCnote Final Verification" "Cyan"
Write-ColorOutput "============================" "Cyan"
Write-ColorOutput "Timestamp: $(Get-Date)" "Gray"
Write-ColorOutput "Version: 2.0.0" "Gray"
Write-ColorOutput ""

# Test all components
$pathsValid = Test-CanonicalPaths
Write-ColorOutput ""
$containersRunning = Test-DockerContainers
Write-ColorOutput ""
$servicesAccessible = Test-ServiceURLs

# Final summary
Write-ColorOutput ""
Write-ColorOutput "=== FINAL VERIFICATION SUMMARY ===" "Cyan"
Write-ColorOutput "Canonical Paths: $(if ($pathsValid) { '[OK] All Valid' } else { '[ERROR] Issues Found' })" $(if ($pathsValid) { 'Green' } else { 'Red' })
Write-ColorOutput "Docker Containers: $(if ($containersRunning) { '[OK] All Running' } else { '[ERROR] Some Stopped' })" $(if ($containersRunning) { 'Green' } else { 'Red' })
Write-ColorOutput "Service URLs: $(if ($servicesAccessible) { '[OK] All Accessible' } else { '[ERROR] Some Unreachable' })" $(if ($servicesAccessible) { 'Green' } else { 'Red' })

Write-ColorOutput ""
$overallStatus = $pathsValid -and $containersRunning -and $servicesAccessible
Write-ColorOutput "=== BLACKCNOTE SYSTEM STATUS ===" "Cyan"
Write-ColorOutput "Overall Status: $(if ($overallStatus) { '[OK] FULLY OPERATIONAL' } else { '[ERROR] REQUIRES ATTENTION' })" $(if ($overallStatus) { 'Green' } else { 'Red' })

if ($overallStatus) {
    Write-ColorOutput ""
    Write-ColorOutput "[SUCCESS] BlackCnote canonical pathways system is fully operational!" "Green"
    Write-ColorOutput "All services are running and accessible." "Green"
    Write-ColorOutput "All canonical paths are valid." "Green"
    Write-ColorOutput "All Docker containers are running." "Green"
} else {
    Write-ColorOutput ""
    Write-ColorOutput "[WARNING] Some issues were found. Please review and fix any problems." "Yellow"
    Write-ColorOutput "Run: scripts\verify-canonical-paths.ps1 for detailed diagnostics." "Yellow"
}

Write-ColorOutput ""
Write-ColorOutput "=== CANONICAL SERVICE URLs ===" "Cyan"
Write-ColorOutput "WordPress:      http://localhost:8888" "White"
Write-ColorOutput "React App:      http://localhost:5174" "White"
Write-ColorOutput "phpMyAdmin:     http://localhost:8080" "White"
Write-ColorOutput "Redis Commander: http://localhost:8081" "White"
Write-ColorOutput "MailHog:        http://localhost:8025" "White"
Write-ColorOutput "Browsersync:    http://localhost:3000" "White"
Write-ColorOutput "Dev Tools:      http://localhost:9229" "White"

Write-ColorOutput ""
Write-ColorOutput "Verification completed at: $(Get-Date)" "Gray" 