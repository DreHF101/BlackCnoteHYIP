# BlackCnote Canonical Paths Verification Script
# Purpose: Verify all canonical pathways and service URLs for BlackCnote project
# Author: BlackCnote Development Team
# Version: 2.0.0

param(
    [switch]$Verbose,
    [switch]$Quiet
)

# Set error action preference
$ErrorActionPreference = "Continue"

# Function to write colored output
function Write-ColorOutput {
    param(
        [string]$Message,
        [string]$Color = "White"
    )
    if (-not $Quiet) { Write-Host $Message -ForegroundColor $Color }
}

# Function to verify canonical paths
function Test-CanonicalPaths {
    Write-ColorOutput "Verifying BlackCnote canonical paths..." "Yellow"
    
    $canonicalPaths = @{
        "Project Root" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
        "WordPress Installation" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote"
        "WordPress Content" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content"
        "Theme Directory" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote"
        "React App" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app"
        "Docker Config" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\config\docker"
        "Scripts" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\scripts"
        "Tools" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools"
    }
    
    $allPathsValid = $true
    
    foreach ($path in $canonicalPaths.GetEnumerator()) {
        if (Test-Path $path.Value) {
            Write-ColorOutput "[OK] $($path.Key) - $($path.Value)" "Green"
        } else {
            Write-ColorOutput "[ERROR] $($path.Key): $($path.Value)" "Red"
            $allPathsValid = $false
        }
    }
    
    return $allPathsValid
}

# Function to verify canonical URLs
function Test-CanonicalURLs {
    Write-ColorOutput "Verifying BlackCnote canonical URLs..." "Yellow"
    
    $canonicalURLs = @{
        "WordPress Frontend" = "http://localhost:8888"
        "WordPress Admin" = "http://localhost:8888/wp-admin"
        "React App" = "http://localhost:5174"
        "phpMyAdmin" = "http://localhost:8080"
        "Redis Commander" = "http://localhost:8081"
        "MailHog" = "http://localhost:8025"
        "Browsersync" = "http://localhost:3000"
        "Dev Tools" = "http://localhost:9229"
    }
    
    $allURLsValid = $true
    
    foreach ($url in $canonicalURLs.GetEnumerator()) {
        try {
            $response = Invoke-WebRequest -Uri $url.Value -TimeoutSec 5 -UseBasicParsing -ErrorAction Stop
            Write-ColorOutput "[OK] $($url.Key) - $($url.Value)" "Green"
        } catch {
            Write-ColorOutput "[ERROR] $($url.Key) - $($url.Value)" "Red"
            $allURLsValid = $false
        }
    }
    
    return $allURLsValid
}

# Function to verify Docker configuration
function Test-DockerConfiguration {
    Write-ColorOutput "Verifying Docker configuration..." "Yellow"
    
    $dockerConfigs = @{
        "Docker Settings" = "$env:APPDATA\Docker\settings.json"
        "Docker Daemon" = "$env:APPDATA\Docker\daemon.json"
        "WSL Config" = "$env:USERPROFILE\.wslconfig"
    }
    
    $allConfigsValid = $true
    
    foreach ($config in $dockerConfigs.GetEnumerator()) {
        if (Test-Path $config.Value) {
            Write-ColorOutput "[OK] $($config.Key): $($config.Value)" "Green"
        } else {
            Write-ColorOutput "[ERROR] $($config.Key): $($config.Value)" "Red"
            $allConfigsValid = $false
        }
    }
    
    return $allConfigsValid
}

# Function to verify WordPress configuration
function Test-WordPressConfiguration {
    Write-ColorOutput "Verifying WordPress configuration..." "Yellow"
    
    $wpConfigs = @{
        "wp-config.php" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-config.php"
        "wp-content" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content"
        "wp-admin" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-admin"
        "wp-includes" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-includes"
        "Theme Directory" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote"
        "Plugins Directory" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\plugins"
    }
    
    $allWPConfigsValid = $true
    
    foreach ($config in $wpConfigs.GetEnumerator()) {
        if (Test-Path $config.Value) {
            Write-ColorOutput "[OK] $($config.Key): $($config.Value)" "Green"
        } else {
            Write-ColorOutput "[ERROR] $($config.Key): $($config.Value)" "Red"
            $allWPConfigsValid = $false
        }
    }
    
    return $allWPConfigsValid
}

# Function to verify Docker containers
function Test-DockerContainers {
    Write-ColorOutput "Verifying Docker containers..." "Yellow"
    
    $expectedContainers = @(
        "blackcnote-wordpress",
        "blackcnote-mysql",
        "blackcnote-redis",
        "blackcnote-phpmyadmin",
        "blackcnote-redis-commander",
        "blackcnote-mailhog",
        "blackcnote-react",
        "blackcnote-browsersync",
        "blackcnote-dev-tools"
    )
    
    $allContainersRunning = $true
    
    try {
        $runningContainers = docker ps --format "{{.Names}}" 2>$null
        
        foreach ($container in $expectedContainers) {
            if ($runningContainers -contains $container) {
                Write-ColorOutput "[OK] $($container): Running" "Green"
            } else {
                Write-ColorOutput "[ERROR] $($container): Stopped" "Red"
                $allContainersRunning = $false
            }
        }
    } catch {
        Write-ColorOutput "[ERROR] Docker not running or not accessible" "Red"
        $allContainersRunning = $false
    }
    
    return $allContainersRunning
}

# Main execution
Write-ColorOutput "BlackCnote Canonical Paths Verification Script" "Cyan"
Write-ColorOutput "=============================================" "Cyan"
Write-ColorOutput "Timestamp: $(Get-Date)" "Gray"
Write-ColorOutput "Version: 2.0.0" "Gray"

# Verify canonical paths
Write-ColorOutput ""
Write-ColorOutput "1. Verifying canonical paths..." "Yellow"
$pathsValid = Test-CanonicalPaths

# Verify canonical URLs
Write-ColorOutput ""
Write-ColorOutput "2. Verifying canonical URLs..." "Yellow"
$urlsValid = Test-CanonicalURLs

# Verify Docker configuration
Write-ColorOutput ""
Write-ColorOutput "3. Verifying Docker configuration..." "Yellow"
$dockerResult = Test-DockerConfiguration

# Verify WordPress configuration
Write-ColorOutput ""
Write-ColorOutput "4. Verifying WordPress configuration..." "Yellow"
$wordPressResult = Test-WordPressConfiguration

# Verify Docker containers
Write-ColorOutput ""
Write-ColorOutput "5. Verifying Docker containers..." "Yellow"
$containersResult = Test-DockerContainers

# Summary
Write-ColorOutput ""
Write-ColorOutput "=== Verification Summary ===" "Cyan"
Write-ColorOutput "Canonical Paths: $(if ($pathsValid) { '[OK] Valid' } else { '[ERROR] Issues Found' })" $(if ($pathsValid) { 'Green' } else { 'Red' })
Write-ColorOutput "Service URLs: $(if ($urlsValid) { '[OK] All Accessible' } else { '[ERROR] Some Unreachable' })" $(if ($urlsValid) { 'Green' } else { 'Red' })
Write-ColorOutput "Docker Config: $(if ($dockerResult) { '[OK] Configured' } else { '[ERROR] Missing' })" $(if ($dockerResult) { 'Green' } else { 'Red' })
Write-ColorOutput "WordPress Config: $(if ($wordPressResult) { '[OK] Valid' } else { '[ERROR] Issues Found' })" $(if ($wordPressResult) { 'Green' } else { 'Red' })
Write-ColorOutput "Docker Containers: $(if ($containersResult) { '[OK] All Running' } else { '[ERROR] Some Stopped' })" $(if ($containersResult) { 'Green' } else { 'Red' })

Write-ColorOutput ""
Write-ColorOutput "=== BlackCnote Canonical Status ===" "Cyan"
$overallStatus = $pathsValid -and $urlsValid -and $dockerResult -and $wordPressResult -and $containersResult
Write-ColorOutput "Overall Status: $(if ($overallStatus) { '[OK] FULLY OPERATIONAL' } else { '[ERROR] REQUIRES ATTENTION' })" $(if ($overallStatus) { 'Green' } else { 'Red' })

Write-ColorOutput ""
Write-ColorOutput "=== Canonical Paths ===" "Cyan"
Write-ColorOutput "Project Root: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote" "White"
Write-ColorOutput "WordPress: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote" "White"
Write-ColorOutput "Theme: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote" "White"

Write-ColorOutput ""
Write-ColorOutput "=== Service URLs ===" "Cyan"
Write-ColorOutput "WordPress:      http://localhost:8888" "White"
Write-ColorOutput "React App:      http://localhost:5174" "White"
Write-ColorOutput "phpMyAdmin:     http://localhost:8080" "White"
Write-ColorOutput "Redis Commander: http://localhost:8081" "White"
Write-ColorOutput "MailHog:        http://localhost:8025" "White"
Write-ColorOutput "Browsersync:    http://localhost:3000" "White"
Write-ColorOutput "Dev Tools:      http://localhost:9229" "White"

if (-not $overallStatus) {
    Write-ColorOutput ""
    Write-ColorOutput "[WARNING] Some issues were found. Please review and fix any problems." "Yellow"
    Write-ColorOutput "Run: scripts\configure-docker-desktop.ps1 to fix Docker configuration issues." "Yellow"
} else {
    Write-ColorOutput ""
    Write-ColorOutput "[SUCCESS] All BlackCnote canonical pathways and services are properly configured!" "Green"
} 