# BlackCnote Production Health Check Script
# Monitors all services and provides detailed status reports

param(
    [switch]$Detailed = $false,
    [switch]$Continuous = $false,
    [int]$Interval = 30
)

Write-Host "🏥 BlackCnote Production Health Check" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green

# Configuration
$Services = @(
    @{ Name = "WordPress"; URL = "http://localhost:8888"; Port = 8888 },
    @{ Name = "WordPress Admin"; URL = "http://localhost:8888/wp-admin"; Port = 8888 },
    @{ Name = "React App"; URL = "http://localhost:3001"; Port = 3001 },
    @{ Name = "Prometheus"; URL = "http://localhost:9090"; Port = 9090 },
    @{ Name = "Grafana"; URL = "http://localhost:3000"; Port = 3000 },
    @{ Name = "AlertManager"; URL = "http://localhost:9093"; Port = 9093 }
)

$DockerServices = @("wordpress", "mysql", "redis", "nginx-proxy", "prometheus", "grafana", "alertmanager")

# Function to check if port is open
function Test-Port {
    param([int]$Port)
    try {
        $connection = New-Object System.Net.Sockets.TcpClient
        $connection.Connect("localhost", $Port)
        $connection.Close()
        return $true
    }
    catch {
        return $false
    }
}

# Function to check HTTP response
function Test-HTTPResponse {
    param([string]$URL, [int]$Timeout = 10)
    try {
        $response = Invoke-WebRequest -Uri $URL -TimeoutSec $Timeout -UseBasicParsing
        return @{
            Status = $response.StatusCode
            Time = $response.BaseResponse.ResponseTime
            Success = $true
        }
    }
    catch {
        return @{
            Status = $_.Exception.Response.StatusCode.value__
            Time = 0
            Success = $false
            Error = $_.Exception.Message
        }
    }
}

# Function to check Docker container health
function Test-DockerHealth {
    param([string]$ServiceName)
    try {
        $health = docker inspect --format='{{.State.Health.Status}}' "blackcnote_${ServiceName}_1" 2>$null
        if ($health -eq "healthy") {
            return @{ Status = "Healthy"; Success = $true }
        }
        elseif ($health -eq "unhealthy") {
            return @{ Status = "Unhealthy"; Success = $false }
        }
        else {
            return @{ Status = "Starting"; Success = $false }
        }
    }
    catch {
        return @{ Status = "Not Running"; Success = $false }
    }
}

# Function to check system resources
function Get-SystemResources {
    $cpu = Get-Counter '\Processor(_Total)\% Processor Time' | Select-Object -ExpandProperty CounterSamples | Select-Object -ExpandProperty CookedValue
    $memory = Get-Counter '\Memory\Available MBytes' | Select-Object -ExpandProperty CounterSamples | Select-Object -ExpandProperty CookedValue
    $disk = Get-WmiObject -Class Win32_LogicalDisk -Filter "DeviceID='C:'" | Select-Object @{Name="FreeGB";Expression={[math]::Round($_.FreeSpace/1GB,2)}}, @{Name="TotalGB";Expression={[math]::Round($_.Size/1GB,2)}}
    
    return @{
        CPU = [math]::Round($cpu, 2)
        MemoryAvailable = [math]::Round($memory, 2)
        DiskFree = $disk.FreeGB
        DiskTotal = $disk.TotalGB
    }
}

# Function to display service status
function Show-ServiceStatus {
    param([hashtable]$Service, [hashtable]$Response)
    
    $statusColor = if ($Response.Success) { "Green" } else { "Red" }
    $statusIcon = if ($Response.Success) { "[OK]" } else { "[ERROR]" }
    
    Write-Host "  $statusIcon $($Service.Name)" -ForegroundColor $statusColor
    if ($Detailed) {
        Write-Host "    URL: $($Service.URL)" -ForegroundColor Gray
        Write-Host "    Port: $($Service.Port)" -ForegroundColor Gray
        if ($Response.Success) {
            Write-Host "    Status: $($Response.Status)" -ForegroundColor Green
            Write-Host "    Response Time: $($Response.Time)ms" -ForegroundColor Green
        }
        else {
            Write-Host "    Error: $($Response.Error)" -ForegroundColor Red
        }
    }
}

# Function to display Docker status
function Show-DockerStatus {
    param([string]$ServiceName, [hashtable]$Health)
    
    $statusColor = if ($Health.Success) { "Green" } else { "Red" }
    $statusIcon = if ($Health.Success) { "[OK]" } else { "[ERROR]" }
    
    Write-Host "  $statusIcon $ServiceName" -ForegroundColor $statusColor
    if ($Detailed) {
        Write-Host "    Status: $($Health.Status)" -ForegroundColor Gray
    }
}

# Function to display system resources
function Show-SystemResources {
    param([hashtable]$Resources)
    
    Write-Host "📊 System Resources:" -ForegroundColor Cyan
    Write-Host "  CPU Usage: $($Resources.CPU)%" -ForegroundColor $(if ($Resources.CPU -gt 80) { "Red" } elseif ($Resources.CPU -gt 60) { "Yellow" } else { "Green" })
    Write-Host "  Available Memory: $($Resources.MemoryAvailable) MB" -ForegroundColor $(if ($Resources.MemoryAvailable -lt 1000) { "Red" } elseif ($Resources.MemoryAvailable -lt 2000) { "Yellow" } else { "Green" })
    Write-Host "  Disk Space: $($Resources.DiskFree) GB / $($Resources.DiskTotal) GB" -ForegroundColor $(if ($Resources.DiskFree -lt 10) { "Red" } elseif ($Resources.DiskFree -lt 20) { "Yellow" } else { "Green" })
}

# Function to run health check
function Start-HealthCheck {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host "`n🕐 Health Check at $timestamp" -ForegroundColor Yellow
    
    # Check HTTP services
    Write-Host "`n🌐 HTTP Services:" -ForegroundColor Cyan
    foreach ($service in $Services) {
        $response = Test-HTTPResponse -URL $service.URL
        Show-ServiceStatus -Service $service -Response $response
    }
    
    # Check Docker containers
    Write-Host "`n🐳 Docker Services:" -ForegroundColor Cyan
    foreach ($service in $DockerServices) {
        $health = Test-DockerHealth -ServiceName $service
        Show-DockerStatus -ServiceName $service -Health $health
    }
    
    # Check system resources
    $resources = Get-SystemResources
    Show-SystemResources -Resources $resources
    
    # Summary
    $httpSuccess = ($Services | ForEach-Object { Test-HTTPResponse -URL $_.URL } | Where-Object { $_.Success }).Count
    $dockerSuccess = ($DockerServices | ForEach-Object { Test-DockerHealth -ServiceName $_ } | Where-Object { $_.Success }).Count
    
    Write-Host "`n📈 Summary:" -ForegroundColor Cyan
    Write-Host "  HTTP Services: $httpSuccess/$($Services.Count) healthy" -ForegroundColor $(if ($httpSuccess -eq $Services.Count) { "Green" } else { "Red" })
    Write-Host "  Docker Services: $dockerSuccess/$($DockerServices.Count) healthy" -ForegroundColor $(if ($dockerSuccess -eq $DockerServices.Count) { "Green" } else { "Red" })
    
    return @{
        HTTPHealthy = $httpSuccess
        HTTPTotal = $Services.Count
        DockerHealthy = $dockerSuccess
        DockerTotal = $DockerServices.Count
        AllHealthy = ($httpSuccess -eq $Services.Count) -and ($dockerSuccess -eq $DockerServices.Count)
    }
}

# Main execution
if ($Continuous) {
    Write-Host "🔄 Starting continuous health monitoring (Press Ctrl+C to stop)" -ForegroundColor Yellow
    Write-Host "Interval: $Interval seconds" -ForegroundColor Yellow
    
    while ($true) {
        try {
            $result = Start-HealthCheck
            if ($result.AllHealthy) {
                Write-Host "`n[SUCCESS] All services are healthy!" -ForegroundColor Green
            }
            else {
                Write-Host "`n[WARNING] Some services are unhealthy" -ForegroundColor Yellow
            }
            
            Start-Sleep -Seconds $Interval
        }
        catch {
            Write-Host "`n[ERROR] Health check failed: $($_.Exception.Message)" -ForegroundColor Red
            Start-Sleep -Seconds $Interval
        }
    }
}
else {
    $result = Start-HealthCheck
    
    if ($result.AllHealthy) {
        Write-Host "`n[SUCCESS] All services are healthy!" -ForegroundColor Green
        exit 0
    }
    else {
        Write-Host "`n[WARNING] Some services are unhealthy" -ForegroundColor Yellow
        exit 1
    }
} 