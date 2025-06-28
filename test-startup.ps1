# BlackCnote Startup Test Script
Write-Host "=== BlackCnote Startup Test ===" -ForegroundColor Cyan
Write-Host "Testing startup system..." -ForegroundColor White

# Test 1: Check if running as administrator
$currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
$principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
$isAdmin = $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

Write-Host "[Test 1] Administrator privileges: $isAdmin" -ForegroundColor $(if($isAdmin){"Green"}else{"Red"})

# Test 2: Check project directory
$projectRoot = "$PSScriptRoot"
Write-Host "[Test 2] Project root: $projectRoot" -ForegroundColor Green

# Test 3: Check if Docker is available
try {
    $dockerVersion = docker --version 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[Test 3] Docker: Available" -ForegroundColor Green
    } else {
        Write-Host "[Test 3] Docker: Not available" -ForegroundColor Red
    }
} catch {
    Write-Host "[Test 3] Docker: Error - $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Check if docker-compose is available
try {
    $composeVersion = docker-compose --version 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[Test 4] Docker Compose: Available" -ForegroundColor Green
    } else {
        Write-Host "[Test 4] Docker Compose: Not available" -ForegroundColor Red
    }
} catch {
    Write-Host "[Test 4] Docker Compose: Error - $($_.Exception.Message)" -ForegroundColor Red
}

# Test 5: Check if WSL2 is available
try {
    $wslVersion = wsl --version 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[Test 5] WSL2: Available" -ForegroundColor Green
    } else {
        Write-Host "[Test 5] WSL2: Not available" -ForegroundColor Red
    }
} catch {
    Write-Host "[Test 5] WSL2: Error - $($_.Exception.Message)" -ForegroundColor Red
}

# Test 6: Check if React app directory exists
$reactAppPath = Join-Path $projectRoot "react-app"
if (Test-Path $reactAppPath) {
    Write-Host "[Test 6] React app directory: Exists" -ForegroundColor Green
} else {
    Write-Host "[Test 6] React app directory: Missing" -ForegroundColor Red
}

# Test 7: Check if WordPress directory exists
$wpPath = Join-Path $projectRoot "blackcnote"
if (Test-Path $wpPath) {
    Write-Host "[Test 7] WordPress directory: Exists" -ForegroundColor Green
} else {
    Write-Host "[Test 7] WordPress directory: Missing" -ForegroundColor Red
}

# Test 8: Check if docker-compose.yml exists
$composeFile = Join-Path $projectRoot "docker-compose.yml"
if (Test-Path $composeFile) {
    Write-Host "[Test 8] Docker Compose file: Exists" -ForegroundColor Green
} else {
    Write-Host "[Test 8] Docker Compose file: Missing" -ForegroundColor Red
}

Write-Host "`n=== Test Complete ===" -ForegroundColor Cyan 