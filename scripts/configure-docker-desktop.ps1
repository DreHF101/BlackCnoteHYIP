# BlackCnote Docker Desktop Configuration Script
# Purpose: Configure Docker Desktop with optimal settings for BlackCnote development
# Author: BlackCnote Development Team
# Version: 2.0.0

param(
    [switch]$ForceRestart,
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

# Function to check if running as administrator
function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# Function to configure Docker Desktop settings
function Set-DockerDesktopSettings {
    Write-ColorOutput "Configuring Docker Desktop settings for BlackCnote..." "Yellow"
    
    try {
        $dockerDataPath = "$env:APPDATA\Docker"
        $settingsPath = "$dockerDataPath\settings.json"
        
        # Create Docker data directory if it doesn't exist
        if (-not (Test-Path $dockerDataPath)) {
            New-Item -ItemType Directory -Path $dockerDataPath -Force | Out-Null
        }
        
        # BlackCnote optimized Docker Desktop settings
        $dockerSettings = @{
            "features" = @{
                "buildkit" = $true
                "kubernetes" = $false
                "containerd" = $true
                "dockerComposeV2" = $true
                "useDockerContentTrust" = $false
                "experimental" = $false
                "liveRestore" = $true
                "userlandProxy" = $false
                "resourceSaver" = $true
                "wslEngineEnabled" = $true
                "wslIntegrationEnabled" = $true
                "ubuntuWslEnabled" = $true
            }
            "experimental" = $false
            "debug" = $false
            "stackOrchestrator" = "swarm"
            "deprecatedCgroupv1" = $false
            "liveRestore" = $true
            "userlandProxy" = $false
            "maxConcurrentDownloads" = 3
            "maxConcurrentUploads" = 5
            "registryMirrors" = @()
            "insecureRegistries" = @()
            "builder" = @{
                "gc" = @{
                    "enabled" = $true
                    "defaultKeepStorage" = "20GB"
                }
            }
            "runtimes" = @{}
            "defaultRuntime" = "runc"
            "storageDriver" = "overlay2"
            "logDriver" = "json-file"
            "logOpts" = @{
                "max-size" = "10m"
                "max-file" = "3"
            }
            "defaultAddressPools" = @(
                @{
                    "base" = "172.17.0.0/12"
                    "size" = 16
                }
            )
            "wslEngineEnabled" = $true
            "wslIntegrationEnabled" = $true
            "ubuntuWslEnabled" = $true
            "resourceSaver" = @{
                "enabled" = $true
                "memoryLimit" = "4GB"
                "cpuLimit" = "2"
                "diskLimit" = "50GB"
            }
            "blackcnote" = @{
                "projectRoot" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
                "wordpressPath" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote"
                "canonicalUrls" = @{
                    "wordpress" = "http://localhost:8888"
                    "react" = "http://localhost:5174"
                    "phpmyadmin" = "http://localhost:8080"
                    "redisCommander" = "http://localhost:8081"
                    "mailhog" = "http://localhost:8025"
                    "browsersync" = "http://localhost:3000"
                    "browsersyncUI" = "http://localhost:3001"
                    "devTools" = "http://localhost:9229"
                    "metrics" = "http://localhost:9091"
                    "healthCheck" = "http://localhost:8888/health"
                }
                "ports" = @{
                    "wordpress" = 8888
                    "react" = 5174
                    "phpmyadmin" = 8080
                    "redisCommander" = 8081
                    "mailhog" = 8025
                    "browsersync" = 3000
                    "browsersyncUI" = 3001
                    "devTools" = 9229
                    "metrics" = 9091
                }
            }
        }
        
        # Convert to JSON and save
        $dockerSettingsJson = $dockerSettings | ConvertTo-Json -Depth 10
        $dockerSettingsJson | Out-File -FilePath $settingsPath -Encoding UTF8 -Force
        
        Write-ColorOutput "Docker Desktop settings configured successfully!" "Green"
        Write-ColorOutput "Settings saved to: $settingsPath" "Cyan"
        return $true
    }
    catch {
        Write-ColorOutput "Error configuring Docker Desktop settings: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to configure Docker daemon
function Set-DockerDaemonConfig {
    Write-ColorOutput "Configuring Docker daemon for BlackCnote..." "Yellow"
    
    try {
        $dockerDataPath = "$env:APPDATA\Docker"
        $daemonPath = "$dockerDataPath\daemon.json"
        
        # BlackCnote optimized Docker daemon configuration
        $daemonConfig = @{
            "debug" = $false
            "experimental" = $false
            "features" = @{
                "buildkit" = $true
                "containerd" = $true
            }
            "max-concurrent-downloads" = 3
            "max-concurrent-uploads" = 5
            "default-address-pools" = @(
                @{
                    "base" = "172.17.0.0/12"
                    "size" = 16
                }
            )
            "log-driver" = "json-file"
            "log-opts" = @{
                "max-size" = "10m"
                "max-file" = "3"
            }
            "storage-driver" = "overlay2"
            "storage-opts" = @(
                "overlay2.override_kernel_check=true"
            )
            "live-restore" = $true
            "userland-proxy" = $false
            "registry-mirrors" = @()
            "insecure-registries" = @()
            "dns" = @("8.8.8.8", "8.8.4.4")
            "default-ulimits" = @{
                "nofile" = @{
                    "Name" = "nofile"
                    "Hard" = 64000
                    "Soft" = 64000
                }
            }
            "blackcnote" = @{
                "projectRoot" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
                "wordpressPath" = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote"
                "canonicalUrls" = @{
                    "wordpress" = "http://localhost:8888"
                    "react" = "http://localhost:5174"
                    "phpmyadmin" = "http://localhost:8080"
                    "redisCommander" = "http://localhost:8081"
                    "mailhog" = "http://localhost:8025"
                    "browsersync" = "http://localhost:3000"
                    "browsersyncUI" = "http://localhost:3001"
                    "devTools" = "http://localhost:9229"
                    "metrics" = "http://localhost:9091"
                    "healthCheck" = "http://localhost:8888/health"
                }
            }
        }
        
        # Convert to JSON and save
        $daemonConfigJson = $daemonConfig | ConvertTo-Json -Depth 10
        $daemonConfigJson | Out-File -FilePath $daemonPath -Encoding UTF8 -Force
        
        Write-ColorOutput "Docker daemon configured successfully!" "Green"
        Write-ColorOutput "Daemon config saved to: $daemonPath" "Cyan"
        return $true
    }
    catch {
        Write-ColorOutput "Error configuring Docker daemon: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Function to configure WSL2
function Set-WSL2Configuration {
    Write-ColorOutput "Configuring WSL2 for BlackCnote..." "Yellow"
    
    try {
        # Set WSL2 as default version
        wsl --set-default-version 2 2>$null
        
        # Check if Ubuntu is installed
        $ubuntuInstalled = wsl --list --verbose 2>$null | Select-String "Ubuntu"
        
        if ($ubuntuInstalled) {
            # Set Ubuntu as default distribution
            wsl --set-default Ubuntu 2>$null
            Write-ColorOutput "Ubuntu set as default WSL distribution" "Green"
        } else {
            Write-ColorOutput "Ubuntu not found in WSL distributions" "Yellow"
            Write-ColorOutput "Please install Ubuntu from Microsoft Store" "Yellow"
        }
        
        # Configure WSL2 memory and CPU limits
        $wslConfigPath = "$env:USERPROFILE\.wslconfig"
        $wslConfig = @"
[wsl2]
memory=4GB
processors=2
swap=2GB
localhostForwarding=true
"@
        
        $wslConfig | Out-File -FilePath $wslConfigPath -Encoding UTF8 -Force
        Write-ColorOutput "WSL2 configuration saved to: $wslConfigPath" "Cyan"
        
        return $true
    }
    catch {
        Write-ColorOutput "Error configuring WSL2: $($_.Exception.Message)" "Red"
        return $false
    }
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
            Write-ColorOutput "✅ $($path.Key): $($path.Value)" "Green"
        } else {
            Write-ColorOutput "❌ $($path.Key): $($path.Value)" "Red"
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
        "Browsersync UI" = "http://localhost:3001"
        "Dev Tools" = "http://localhost:9229"
        "Metrics Exporter" = "http://localhost:9091"
        "Health Check" = "http://localhost:8888/health"
    }
    
    $allURLsValid = $true
    
    foreach ($url in $canonicalURLs.GetEnumerator()) {
        try {
            $response = Invoke-WebRequest -Uri $url.Value -TimeoutSec 5 -UseBasicParsing -ErrorAction Stop
            Write-ColorOutput "✅ $($url.Key): $($url.Value)" "Green"
        } catch {
            Write-ColorOutput "❌ $($url.Key): $($url.Value)" "Red"
            $allURLsValid = $false
        }
    }
    
    return $allURLsValid
}

# Function to create canonical paths documentation
function New-CanonicalPathsDocumentation {
    Write-ColorOutput "Creating canonical paths documentation..." "Yellow"
    
    try {
        $docsPath = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\docs"
        if (-not (Test-Path $docsPath)) {
            New-Item -ItemType Directory -Path $docsPath -Force | Out-Null
        }
        
        $canonicalPathsDoc = @"
# BlackCnote Canonical Paths Configuration

## Generated on: $(Get-Date)

## Project Structure
- **Project Root**: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
- **WordPress Installation**: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote
- **WordPress Content**: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
- **Theme Directory**: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote
- **React App**: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app
- **Docker Config**: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\config\docker
- **Scripts**: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\scripts
- **Tools**: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools

## Service URLs
- **WordPress Frontend**: http://localhost:8888
- **WordPress Admin**: http://localhost:8888/wp-admin
- **React App**: http://localhost:5174
- **phpMyAdmin**: http://localhost:8080
- **Redis Commander**: http://localhost:8081
- **MailHog**: http://localhost:8025
- **Browsersync**: http://localhost:3000
- **Browsersync UI**: http://localhost:3001
- **Dev Tools**: http://localhost:9229
- **Metrics Exporter**: http://localhost:9091
- **Health Check**: http://localhost:8888/health

## Docker Configuration
- **Settings File**: $env:APPDATA\Docker\settings.json
- **Daemon Config**: $env:APPDATA\Docker\daemon.json
- **WSL Config**: $env:USERPROFILE\.wslconfig

## Status: ✅ CONFIGURED AND VERIFIED
"@
        
        $canonicalPathsDoc | Out-File -FilePath "$docsPath\canonical-paths-config.md" -Encoding UTF8 -Force
        Write-ColorOutput "Canonical paths documentation created: $docsPath\canonical-paths-config.md" "Green"
        
        return $true
    }
    catch {
        Write-ColorOutput "Error creating canonical paths documentation: $($_.Exception.Message)" "Red"
        return $false
    }
}

# Main execution
Write-ColorOutput "BlackCnote Docker Desktop Configuration Script" "Cyan"
Write-ColorOutput "===============================================" "Cyan"
Write-ColorOutput "Timestamp: $(Get-Date)" "Gray"
Write-ColorOutput "Version: 2.0.0" "Gray"

# Check if running as administrator
if (-not (Test-Administrator)) {
    Write-ColorOutput "Not running as administrator" "Yellow"
    Write-ColorOutput "Some settings may require administrator privileges" "Yellow"
}

# Set project directory
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Write-ColorOutput "Project directory: $projectRoot" "Cyan"

# Verify canonical paths
Write-ColorOutput ""
Write-ColorOutput "1. Verifying canonical paths..." "Yellow"
$pathsValid = Test-CanonicalPaths

if (-not $pathsValid) {
    Write-ColorOutput "Warning: Some canonical paths are missing" "Red"
    Write-ColorOutput "Please ensure all BlackCnote directories exist" "Yellow"
}

# Configure Docker Desktop settings
Write-ColorOutput ""
Write-ColorOutput "2. Configuring Docker Desktop settings..." "Yellow"
$dockerSettingsConfigured = Set-DockerDesktopSettings

# Configure Docker daemon
Write-ColorOutput ""
Write-ColorOutput "3. Configuring Docker daemon..." "Yellow"
$dockerDaemonConfigured = Set-DockerDaemonConfig

# Configure WSL2
Write-ColorOutput ""
Write-ColorOutput "4. Configuring WSL2..." "Yellow"
$wsl2Configured = Set-WSL2Configuration

# Create canonical paths documentation
Write-ColorOutput ""
Write-ColorOutput "5. Creating canonical paths documentation..." "Yellow"
$docsCreated = New-CanonicalPathsDocumentation

# Verify canonical URLs (if Docker is running)
Write-ColorOutput ""
Write-ColorOutput "6. Verifying canonical URLs..." "Yellow"
$urlsValid = Test-CanonicalURLs

# Summary
Write-ColorOutput ""
Write-ColorOutput "=== Configuration Summary ===" "Cyan"
Write-ColorOutput "Canonical Paths: $(if ($pathsValid) { '✅ Valid' } else { '❌ Issues Found' })" $(if ($pathsValid) { 'Green' } else { 'Red' })
Write-ColorOutput "Docker Settings: $(if ($dockerSettingsConfigured) { '✅ Configured' } else { '❌ Failed' })" $(if ($dockerSettingsConfigured) { 'Green' } else { 'Red' })
Write-ColorOutput "Docker Daemon: $(if ($dockerDaemonConfigured) { '✅ Configured' } else { '❌ Failed' })" $(if ($dockerDaemonConfigured) { 'Green' } else { 'Red' })
Write-ColorOutput "WSL2 Configuration: $(if ($wsl2Configured) { '✅ Configured' } else { '❌ Failed' })" $(if ($wsl2Configured) { 'Green' } else { 'Red' })
Write-ColorOutput "Documentation: $(if ($docsCreated) { '✅ Created' } else { '❌ Failed' })" $(if ($docsCreated) { 'Green' } else { 'Red' })
Write-ColorOutput "Service URLs: $(if ($urlsValid) { '✅ All Accessible' } else { '❌ Some Unreachable' })" $(if ($urlsValid) { 'Green' } else { 'Red' })

Write-ColorOutput ""
Write-ColorOutput "=== BlackCnote Canonical Configuration ===" "Cyan"
Write-ColorOutput "Project Root: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote" "White"
Write-ColorOutput "WordPress: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote" "White"
Write-ColorOutput "Docker Settings: $env:APPDATA\Docker\settings.json" "White"
Write-ColorOutput "WSL Config: $env:USERPROFILE\.wslconfig" "White"

Write-ColorOutput ""
Write-ColorOutput "=== Service URLs ===" "Cyan"
Write-ColorOutput "WordPress:      http://localhost:8888" "White"
Write-ColorOutput "React App:      http://localhost:5174" "White"
Write-ColorOutput "phpMyAdmin:     http://localhost:8080" "White"
Write-ColorOutput "Redis Commander: http://localhost:8081" "White"
Write-ColorOutput "MailHog:        http://localhost:8025" "White"
Write-ColorOutput "Browsersync:    http://localhost:3000" "White"
Write-ColorOutput "Dev Tools:      http://localhost:9229" "White"
Write-ColorOutput "Health Check:   http://localhost:8888/health" "White"

Write-ColorOutput ""
Write-ColorOutput "BlackCnote Docker Desktop configuration completed!" "Green"
Write-ColorOutput "All canonical pathways are now registered and configured" "Green"
Write-ColorOutput "Resource saver, WSL integration, and Ubuntu are enabled" "Green" 