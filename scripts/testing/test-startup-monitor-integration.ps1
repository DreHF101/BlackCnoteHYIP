# BlackCnote Startup Monitor Integration Test
# Tests the integration between startup script and BlackCnote Debug System

param(
    [switch]$Verbose,
    [switch]$SkipDocker,
    [switch]$SkipWordPress,
    [switch]$SkipReact,
    [switch]$GenerateReport
)

# Set error action preference
$ErrorActionPreference = "Continue"

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

# Function to test startup script
function Test-StartupScript {
    Write-ColorOutput "[TEST] Testing startup script..." "Yellow"
    
    $startupScript = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\start-blackcnote-complete.ps1"
    
    $results = @{
        Exists = Test-Path $startupScript
        Readable = $false
        Executable = $false
        Size = 0
        LastModified = $null
        SyntaxValid = $false
    }
    
    if ($results.Exists) {
        $results.Readable = (Get-Item $startupScript).IsReadOnly -eq $false
        $results.Size = (Get-Item $startupScript).Length
        $results.LastModified = (Get-Item $startupScript).LastWriteTime
        
        # Test PowerShell syntax
        try {
            $null = [System.Management.Automation.PSParser]::Tokenize((Get-Content $startupScript -Raw), [ref]$null)
            $results.SyntaxValid = $true
        }
        catch {
            $results.SyntaxValid = $false
        }
    }
    
    return $results
}

# Function to test startup monitor PHP script
function Test-StartupMonitor {
    Write-ColorOutput "[TEST] Testing startup monitor PHP script..." "Yellow"
    
    $monitorScript = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\bin\blackcnote-startup-monitor.php"
    
    $results = @{
        Exists = Test-Path $monitorScript
        Readable = $false
        Size = 0
        LastModified = $null
        SyntaxValid = $false
        CanExecute = $false
    }
    
    if ($results.Exists) {
        $results.Readable = (Get-Item $monitorScript).IsReadOnly -eq $false
        $results.Size = (Get-Item $monitorScript).Length
        $results.LastModified = (Get-Item $monitorScript).LastWriteTime
        
        # Test PHP syntax
        try {
            $phpOutput = php -l $monitorScript 2>&1
            $results.SyntaxValid = $LASTEXITCODE -eq 0
        }
        catch {
            $results.SyntaxValid = $false
        }
        
        # Test if can execute
        try {
            $testOutput = php $monitorScript --status 2>&1
            $results.CanExecute = $LASTEXITCODE -eq 0
        }
        catch {
            $results.CanExecute = $false
        }
    }
    
    return $results
}

# Function to test debug system plugin
function Test-DebugSystemPlugin {
    Write-ColorOutput "[TEST] Testing BlackCnote Debug System plugin..." "Yellow"
    
    $pluginDir = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\plugins\blackcnote-debug-system"
    
    $results = @{
        Exists = Test-Path $pluginDir
        MainFile = $false
        StartupMonitorClass = $false
        AdminView = $false
        Integration = $false
    }
    
    if ($results.Exists) {
        $mainFile = Join-Path $pluginDir "blackcnote-debug-system.php"
        $results.MainFile = Test-Path $mainFile
        
        $startupMonitorClass = Join-Path $pluginDir "includes\class-blackcnote-debug-startup-monitor.php"
        $results.StartupMonitorClass = Test-Path $startupMonitorClass
        
        $adminView = Join-Path $pluginDir "admin\views\startup-monitor-page.php"
        $results.AdminView = Test-Path $adminView
        
        # Check if integration is properly added
        if ($results.MainFile) {
            $mainContent = Get-Content $mainFile -Raw
            $results.Integration = $mainContent -match "BlackCnoteDebugStartupMonitor"
        }
    }
    
    return $results
}

# Function to test WordPress integration
function Test-WordPressIntegration {
    Write-ColorOutput "[TEST] Testing WordPress integration..." "Yellow"
    
    $results = @{
        WordPressAccessible = $false
        AdminAccessible = $false
        DebugPluginActive = $false
        StartupMonitorPage = $false
        RESTAPI = $false
    }
    
    # Test WordPress accessibility
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
        $results.WordPressAccessible = $response.StatusCode -eq 200
    }
    catch {
        $results.WordPressAccessible = $false
    }
    
    # Test WordPress admin
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-admin" -TimeoutSec 10 -UseBasicParsing
        $results.AdminAccessible = $response.StatusCode -eq 200
    }
    catch {
        $results.AdminAccessible = $false
    }
    
    # Test REST API
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8888/wp-json" -TimeoutSec 10 -UseBasicParsing
        $results.RESTAPI = $response.StatusCode -eq 200
    }
    catch {
        $results.RESTAPI = $false
    }
    
    return $results
}

# Function to test service health
function Test-ServiceHealth {
    Write-ColorOutput "[TEST] Testing service health..." "Yellow"
    
    $services = @(
        @{ Name = "WordPress"; Url = "http://localhost:8888"; Required = $true },
        @{ Name = "React App"; Url = "http://localhost:5174"; Required = $true },
        @{ Name = "phpMyAdmin"; Url = "http://localhost:8080"; Required = $true },
        @{ Name = "Redis Commander"; Url = "http://localhost:8081"; Required = $false },
        @{ Name = "MailHog"; Url = "http://localhost:8025"; Required = $false },
        @{ Name = "Browsersync"; Url = "http://localhost:3000"; Required = $false },
        @{ Name = "Metrics Exporter"; Url = "http://localhost:9091"; Required = $false }
    )
    
    $results = @{}
    
    foreach ($service in $services) {
        $result = @{
            Name = $service.Name
            Url = $service.Url
            Required = $service.Required
            Accessible = $false
            ResponseTime = 0
            StatusCode = 0
        }
        
        try {
            $startTime = Get-Date
            $response = Invoke-WebRequest -Uri $service.Url -TimeoutSec 5 -UseBasicParsing
            $endTime = Get-Date
            
            $result.Accessible = $true
            $result.StatusCode = $response.StatusCode
            $result.ResponseTime = ($endTime - $startTime).TotalMilliseconds
        }
        catch {
            $result.Accessible = $false
        }
        
        $results[$service.Name] = $result
    }
    
    return $results
}

# Function to test Docker containers
function Test-DockerContainers {
    Write-ColorOutput "[TEST] Testing Docker containers..." "Yellow"
    
    $containers = @(
        "blackcnote_wordpress",
        "blackcnote_mysql",
        "blackcnote_redis",
        "blackcnote_react",
        "blackcnote_phpmyadmin",
        "blackcnote_mailhog",
        "blackcnote_redis_commander",
        "blackcnote_browsersync",
        "blackcnote_file_watcher",
        "blackcnote_dev_tools",
        "blackcnote_debug",
        "blackcnote_debug_exporter"
    )
    
    $results = @{}
    
    foreach ($container in $containers) {
        $result = @{
            Name = $container
            Running = $false
            Status = "Unknown"
            Ports = @()
        }
        
        try {
            $output = docker ps --filter "name=$container" --format "{{.Names}}\t{{.Status}}\t{{.Ports}}" 2>$null
            
            if ($output -and $output.Trim()) {
                $result.Running = $true
                $lines = $output.Split("`n")
                foreach ($line in $lines) {
                    if ($line.Trim() -and $line.Contains($container)) {
                        $parts = $line.Split("`t")
                        $result.Status = $parts[1]
                        if ($parts.Length -gt 2) {
                            $result.Ports = $parts[2].Split(",")
                        }
                        break
                    }
                }
            }
        }
        catch {
            $result.Running = $false
            $result.Status = "Error checking container"
        }
        
        $results[$container] = $result
    }
    
    return $results
}

# Function to generate comprehensive report
function Generate-IntegrationReport {
    param($startupScript, $startupMonitor, $debugSystem, $wordPress, $services, $containers)
    
    $report = @{
        Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        TestResults = @{
            StartupScript = $startupScript
            StartupMonitor = $startupMonitor
            DebugSystem = $debugSystem
            WordPress = $wordPress
            Services = $services
            Containers = $containers
        }
        Summary = @{
            OverallStatus = "Unknown"
            CriticalIssues = 0
            Warnings = 0
            Recommendations = @()
        }
    }
    
    # Analyze results and generate summary
    $criticalIssues = 0
    $warnings = 0
    $recommendations = @()
    
    # Check startup script
    if (-not $startupScript.Exists) {
        $criticalIssues++
        $recommendations += "Startup script not found - critical issue"
    }
    if (-not $startupScript.SyntaxValid) {
        $criticalIssues++
        $recommendations += "Startup script has syntax errors - critical issue"
    }
    
    # Check startup monitor
    if (-not $startupMonitor.Exists) {
        $warnings++
        $recommendations += "Startup monitor script not found - integration incomplete"
    }
    if (-not $startupMonitor.SyntaxValid) {
        $criticalIssues++
        $recommendations += "Startup monitor has syntax errors - critical issue"
    }
    
    # Check debug system
    if (-not $debugSystem.Exists) {
        $criticalIssues++
        $recommendations += "Debug system plugin not found - critical issue"
    }
    if (-not $debugSystem.Integration) {
        $warnings++
        $recommendations += "Startup monitor not integrated with debug system"
    }
    
    # Check WordPress
    if (-not $wordPress.WordPressAccessible) {
        $criticalIssues++
        $recommendations += "WordPress not accessible - critical issue"
    }
    if (-not $wordPress.AdminAccessible) {
        $warnings++
        $recommendations += "WordPress admin not accessible"
    }
    
    # Check required services
    foreach ($service in $services.Values) {
        if ($service.Required -and -not $service.Accessible) {
            $criticalIssues++
            $recommendations += "Required service $($service.Name) not accessible - critical issue"
        } elseif (-not $service.Required -and -not $service.Accessible) {
            $warnings++
            $recommendations += "Optional service $($service.Name) not accessible"
        }
    }
    
    # Check required containers
    $requiredContainers = @("blackcnote_wordpress", "blackcnote_mysql", "blackcnote_redis")
    foreach ($container in $requiredContainers) {
        if ($containers[$container] -and -not $containers[$container].Running) {
            $criticalIssues++
            $recommendations += "Required container $container not running - critical issue"
        }
    }
    
    # Determine overall status
    if ($criticalIssues -eq 0) {
        $report.Summary.OverallStatus = "Healthy"
    } elseif ($criticalIssues -le 2) {
        $report.Summary.OverallStatus = "Degraded"
    } else {
        $report.Summary.OverallStatus = "Critical"
    }
    
    $report.Summary.CriticalIssues = $criticalIssues
    $report.Summary.Warnings = $warnings
    $report.Summary.Recommendations = $recommendations
    
    return $report
}

# Function to display test results
function Show-TestResults {
    param($report)
    
    Write-ColorOutput "`n=== BLACKCNOTE STARTUP MONITOR INTEGRATION TEST RESULTS ===" "Cyan"
    Write-ColorOutput "Generated at: $($report.Timestamp)" "White"
    Write-ColorOutput "Overall Status: $($report.Summary.OverallStatus)" $(if ($report.Summary.OverallStatus -eq "Healthy") { "Green" } elseif ($report.Summary.OverallStatus -eq "Degraded") { "Yellow" } else { "Red" })
    
    Write-ColorOutput "`n📊 SUMMARY" "Yellow"
    Write-ColorOutput "Critical Issues: $($report.Summary.CriticalIssues)" $(if ($report.Summary.CriticalIssues -eq 0) { "Green" } else { "Red" })
    Write-ColorOutput "Warnings: $($report.Summary.Warnings)" $(if ($report.Summary.Warnings -eq 0) { "Green" } else { "Yellow" })
    
    if ($report.Summary.Recommendations.Count -gt 0) {
        Write-ColorOutput "`n💡 RECOMMENDATIONS" "Yellow"
        foreach ($rec in $report.Summary.Recommendations) {
            Write-ColorOutput "  - $rec" "White"
        }
    }
    
    Write-ColorOutput "`n🔧 COMPONENT STATUS" "Yellow"
    
    # Startup Script
    $script = $report.TestResults.StartupScript
    Write-ColorOutput "Startup Script:" "White"
    Write-ColorOutput "  Exists: $(if ($script.Exists) { '[OK]' } else { '[FAILED]' })" $(if ($script.Exists) { "Green" } else { "Red" })
    Write-ColorOutput "  Syntax Valid: $(if ($script.SyntaxValid) { '[OK]' } else { '[FAILED]' })" $(if ($script.SyntaxValid) { "Green" } else { "Red" })
    
    # Startup Monitor
    $monitor = $report.TestResults.StartupMonitor
    Write-ColorOutput "Startup Monitor:" "White"
    Write-ColorOutput "  Exists: $(if ($monitor.Exists) { '[OK]' } else { '[FAILED]' })" $(if ($monitor.Exists) { "Green" } else { "Red" })
    Write-ColorOutput "  Syntax Valid: $(if ($monitor.SyntaxValid) { '[OK]' } else { '[FAILED]' })" $(if ($monitor.SyntaxValid) { "Green" } else { "Red" })
    Write-ColorOutput "  Can Execute: $(if ($monitor.CanExecute) { '[OK]' } else { '[FAILED]' })" $(if ($monitor.CanExecute) { "Green" } else { "Red" })
    
    # Debug System
    $debug = $report.TestResults.DebugSystem
    Write-ColorOutput "Debug System Plugin:" "White"
    Write-ColorOutput "  Exists: $(if ($debug.Exists) { '[OK]' } else { '[FAILED]' })" $(if ($debug.Exists) { "Green" } else { "Red" })
    Write-ColorOutput "  Integration: $(if ($debug.Integration) { '[OK]' } else { '[FAILED]' })" $(if ($debug.Integration) { "Green" } else { "Red" })
    
    # WordPress
    $wp = $report.TestResults.WordPress
    Write-ColorOutput "WordPress Integration:" "White"
    Write-ColorOutput "  Accessible: $(if ($wp.WordPressAccessible) { '[OK]' } else { '[FAILED]' })" $(if ($wp.WordPressAccessible) { "Green" } else { "Red" })
    Write-ColorOutput "  Admin Accessible: $(if ($wp.AdminAccessible) { '[OK]' } else { '[FAILED]' })" $(if ($wp.AdminAccessible) { "Green" } else { "Red" })
    Write-ColorOutput "  REST API: $(if ($wp.RESTAPI) { '[OK]' } else { '[FAILED]' })" $(if ($wp.RESTAPI) { "Green" } else { "Red" })
    
    # Services
    Write-ColorOutput "`n🌐 SERVICE STATUS" "Yellow"
    foreach ($service in $report.TestResults.Services.Values) {
        $status = if ($service.Accessible) { "[OK]" } else { "[FAILED]" }
        $color = if ($service.Accessible) { "Green" } else { $(if ($service.Required) { "Red" } else { "Yellow" }) }
        $required = if ($service.Required) { " (Required)" } else { " (Optional)" }
        Write-ColorOutput "  $status $($service.Name)$required" $color
    }
    
    # Containers
    Write-ColorOutput "`n🐳 CONTAINER STATUS" "Yellow"
    foreach ($container in $report.TestResults.Containers.Values) {
        $status = if ($container.Running) { "[RUNNING]" } else { "[STOPPED]" }
        $color = if ($container.Running) { "Green" } else { "Red" }
        Write-ColorOutput "  $status $($container.Name)" $color
    }
}

# Main execution
Write-ColorOutput "=== BlackCnote Startup Monitor Integration Test ===" "Cyan"
Write-ColorOutput "Starting comprehensive integration test..." "White"

# Run tests
$startupScript = Test-StartupScript
$startupMonitor = Test-StartupMonitor
$debugSystem = Test-DebugSystemPlugin

if (-not $SkipWordPress) {
    $wordPress = Test-WordPressIntegration
} else {
    $wordPress = @{ WordPressAccessible = $false; AdminAccessible = $false; RESTAPI = $false }
}

if (-not $SkipDocker) {
    $services = Test-ServiceHealth
    $containers = Test-DockerContainers
} else {
    $services = @{}
    $containers = @{}
}

# Generate report
$report = Generate-IntegrationReport -startupScript $startupScript -startupMonitor $startupMonitor -debugSystem $debugSystem -wordPress $wordPress -services $services -containers $containers

# Display results
Show-TestResults -report $report

# Save report if requested
if ($GenerateReport) {
    $reportFile = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\reports\startup-monitor-integration-test-$(Get-Date -Format 'yyyy-MM-dd-HHmmss').json"
    $reportDir = Split-Path $reportFile -Parent
    
    if (-not (Test-Path $reportDir)) {
        New-Item -ItemType Directory -Force -Path $reportDir | Out-Null
    }
    
    $report | ConvertTo-Json -Depth 10 | Out-File -FilePath $reportFile -Encoding UTF8
    Write-ColorOutput "`n📄 Report saved to: $reportFile" "Green"
}

Write-ColorOutput "`n✅ Integration test completed!" "Green" 