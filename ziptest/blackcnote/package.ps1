# BlackCnote Theme Package Script
# This script creates a zip package of the BlackCnote theme

# Create temporary directory
$tempDir = Join-Path $PSScriptRoot "..\temp\blackcnote-theme"
New-Item -ItemType Directory -Force -Path $tempDir | Out-Null

# Copy theme files
Get-ChildItem -Path $PSScriptRoot -Exclude "package.ps1","screenshot.txt" | Copy-Item -Destination $tempDir -Recurse -Force

# Create zip file
$zipPath = Join-Path $PSScriptRoot "..\temp\blackcnote-theme.zip"
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}
Compress-Archive -Path $tempDir -DestinationPath $zipPath

# Move zip to themes directory
$themesDir = Join-Path $PSScriptRoot "..\themes"
if (-not (Test-Path $themesDir)) {
    New-Item -ItemType Directory -Force -Path $themesDir | Out-Null
}
Move-Item -Path $zipPath -Destination $themesDir -Force

# Clean up
Remove-Item -Path $tempDir -Recurse -Force

Write-Host "Theme packaged successfully as blackcnote-theme.zip" 