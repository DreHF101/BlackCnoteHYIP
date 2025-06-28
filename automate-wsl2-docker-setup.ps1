# BlackCnote WSL2 + Docker Automation Script
# Run as Administrator in PowerShell

Write-Host "=== BlackCnote WSL2 + Docker Automation Script ===" -ForegroundColor Cyan

# 1. Enable WSL2 and Virtual Machine Platform
Write-Host "[1/7] Enabling WSL2 and Virtual Machine Platform features..." -ForegroundColor Yellow
wslFeature = (Get-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux).State
vmFeature = (Get-WindowsOptionalFeature -Online -FeatureName VirtualMachinePlatform).State
if ($wslFeature -ne 'Enabled') {
    Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux -NoRestart -All
}
if ($vmFeature -ne 'Enabled') {
    Enable-WindowsOptionalFeature -Online -FeatureName VirtualMachinePlatform -NoRestart -All
}

# 2. Prompt for reboot if needed
if ($wslFeature -ne 'Enabled' -or $vmFeature -ne 'Enabled') {
    Write-Host "[!] System reboot required. Please reboot, then re-run this script." -ForegroundColor Red
    pause
    exit
}

# 3. Install WSL2 Kernel Update
Write-Host "[2/7] Checking for WSL2 kernel update..." -ForegroundColor Yellow
$kernelUrl = "https://wslstorestorage.blob.core.windows.net/wslblob/wsl_update_x64.msi"
$kernelPath = "$env:TEMP\wsl_update_x64.msi"
if (!(Test-Path "$env:windir\system32\lxss\tools\kernel")) {
    Write-Host "Downloading WSL2 kernel update..." -ForegroundColor Yellow
    Invoke-WebRequest -Uri $kernelUrl -OutFile $kernelPath
    Start-Process msiexec.exe -Wait -ArgumentList "/i $kernelPath /quiet"
}

# 4. Set WSL2 as default
Write-Host "[3/7] Setting WSL2 as default version..." -ForegroundColor Yellow
wsl --set-default-version 2

# 5. Install Ubuntu if not present
Write-Host "[4/7] Checking for Ubuntu installation..." -ForegroundColor Yellow
$distros = wsl --list --online | Select-String -Pattern "Ubuntu"
$installed = wsl --list --verbose | Select-String -Pattern "Ubuntu"
if (-not $installed) {
    Write-Host "Installing Ubuntu..." -ForegroundColor Yellow
    wsl --install -d Ubuntu
    Write-Host "Please complete Ubuntu setup in the new terminal window, then re-run this script." -ForegroundColor Red
    pause
    exit
}

# 6. Copy BlackCnote project to WSL2 home directory
Write-Host "[5/7] Copying BlackCnote project to WSL2 home directory..." -ForegroundColor Yellow
$wslHome = "/home/$(wsl -u root echo $USER)"
wsl mkdir -p $wslHome/blackcnote
wsl rm -rf $wslHome/blackcnote/*
wsl cp -r /mnt/c/Users/CASH\ AMERICA\ PAWN/Desktop/BlackCnote/* $wslHome/blackcnote/

# 7. Launch Docker Compose from WSL2
Write-Host "[6/7] Launching Docker Compose from WSL2..." -ForegroundColor Yellow
wsl bash -c "cd ~/blackcnote && docker-compose -f config/docker/docker-compose.yml up -d"

Write-Host "[7/7] All done! Test your site at http://localhost:8888" -ForegroundColor Green
Write-Host "If you encounter issues, see WSL2-DOCKER-IMPLEMENTATION.md for troubleshooting." -ForegroundColor Cyan 