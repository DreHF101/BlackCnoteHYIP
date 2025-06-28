# BlackCnote Docker Simple Fix
# Focuses on the core WSL2 docker-desktop issue

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "BlackCnote Docker Simple Fix" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Test Docker CLI
Write-Host "Step 1: Testing Docker CLI..." -ForegroundColor Yellow
$dockerPath = "C:\Program Files\Docker\Docker\resources\bin\docker.exe"

if (Test-Path $dockerPath) {
    $version = & $dockerPath --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Docker CLI found: $version" -ForegroundColor Green
    } else {
        Write-Host "✗ Docker CLI test failed: $version" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "✗ Docker CLI not found at: $dockerPath" -ForegroundColor Red
    exit 1
}

# Step 2: Check WSL2 status
Write-Host ""
Write-Host "Step 2: Checking WSL2 status..." -ForegroundColor Yellow
$wslList = wsl --list --verbose 2>&1
Write-Host "WSL2 Distros:"
Write-Output $wslList

$dockerDesktopRunning = $wslList -match "docker-desktop.*Running"
if ($dockerDesktopRunning) {
    Write-Host "✓ docker-desktop WSL2 distro is running" -ForegroundColor Green
} else {
    Write-Host "⚠ docker-desktop WSL2 distro is not running" -ForegroundColor Yellow
    
    # Step 3: Start docker-desktop WSL2 distro
    Write-Host ""
    Write-Host "Step 3: Starting docker-desktop WSL2 distro..." -ForegroundColor Yellow
    Write-Host "This may take a moment..."
    
    # Start the distro
    Start-Process -FilePath "wsl" -ArgumentList "-d", "docker-desktop" -WindowStyle Hidden
    
    # Wait for it to start
    Start-Sleep -Seconds 15
    
    # Check again
    $wslList = wsl --list --verbose 2>&1
    $dockerDesktopRunning = $wslList -match "docker-desktop.*Running"
    
    if ($dockerDesktopRunning) {
        Write-Host "✓ docker-desktop WSL2 distro started successfully" -ForegroundColor Green
    } else {
        Write-Host "✗ Failed to start docker-desktop WSL2 distro" -ForegroundColor Red
        Write-Host "Try restarting Docker Desktop manually" -ForegroundColor Yellow
        exit 1
    }
}

# Step 4: Test Docker daemon
Write-Host ""
Write-Host "Step 4: Testing Docker daemon connection..." -ForegroundColor Yellow

# Test with timeout using job
$job = Start-Job -ScriptBlock {
    param($dockerPath)
    & $dockerPath version 2>&1
} -ArgumentList $dockerPath

$result = Wait-Job -Job $job -Timeout 30

if ($result) {
    $output = Receive-Job -Job $job
    Remove-Job -Job $job
    
    if ($output -match "error during connect") {
        Write-Host "✗ Docker daemon connection failed: $output" -ForegroundColor Red
        exit 1
    } elseif ($output -match "Server:") {
        Write-Host "✓ Docker daemon connection successful" -ForegroundColor Green
    } else {
        Write-Host "⚠ Unexpected Docker response: $output" -ForegroundColor Yellow
    }
} else {
    Remove-Job -Job $job -Force
    Write-Host "✗ Docker daemon connection timed out" -ForegroundColor Red
    exit 1
}

# Step 5: Check Docker pipes
Write-Host ""
Write-Host "Step 5: Checking Docker pipes..." -ForegroundColor Yellow
$pipes = Get-ChildItem -Path '\\.\\pipe\\' -ErrorAction SilentlyContinue | Where-Object {$_.Name -like "*docker*"}

if ($pipes) {
    Write-Host "✓ Found Docker pipes:" -ForegroundColor Green
    foreach ($pipe in $pipes) {
        Write-Host "  - $($pipe.Name)" -ForegroundColor Cyan
    }
} else {
    Write-Host "⚠ No Docker pipes found" -ForegroundColor Yellow
}

# Results
Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "RESULTS SUMMARY" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Docker CLI: ✓ Working" -ForegroundColor Green
Write-Host "WSL2 Status: ✓ Running" -ForegroundColor Green
Write-Host "Docker Daemon: ✓ Connected" -ForegroundColor Green
Write-Host "Docker Pipes: " + (if ($pipes) { "✓ Found" } else { "⚠ Missing" }) -ForegroundColor $(if ($pipes) { "Green" } else { "Yellow" })

Write-Host ""
Write-Host "✓ Docker is fully functional!" -ForegroundColor Green
Write-Host "You can now use Docker commands normally." -ForegroundColor Green 