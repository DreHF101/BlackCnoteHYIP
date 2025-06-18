# HYIP Theme Packaging Script
# This script prepares the theme for WordPress installation

# Set error action preference
$ErrorActionPreference = "Stop"

# Define paths
$themeDir = Get-Location
$tempDir = Join-Path $themeDir "temp"
$zipFile = Join-Path $themeDir "hyip-theme.zip"

# Create temporary directory
if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Files and directories to include
$includeFiles = @(
    "style.css",
    "index.php",
    "functions.php",
    "header.php",
    "footer.php",
    "screenshot.png",
    "README.md",
    "LICENSE.txt",
    "CHANGELOG.md",
    "single.php",
    "page.php",
    "archive.php",
    "search.php",
    "comments.php",
    "sidebar.php",
    "template-hyip-dashboard.php",
    "template-hyip-plans.php",
    "template-hyip-transactions.php"
)

$includeDirs = @(
    "assets",
    "inc",
    "js",
    "languages",
    "template-parts"
)

# Copy files
foreach ($file in $includeFiles) {
    if (Test-Path (Join-Path $themeDir $file)) {
        Copy-Item (Join-Path $themeDir $file) -Destination $tempDir
    } else {
        Write-Warning "File not found: $file"
    }
}

# Copy directories
foreach ($dir in $includeDirs) {
    if (Test-Path (Join-Path $themeDir $dir)) {
        Copy-Item (Join-Path $themeDir $dir) -Destination $tempDir -Recurse
    } else {
        Write-Warning "Directory not found: $dir"
    }
}

# Remove unnecessary files
$excludeFiles = @(
    "package.ps1",
    "validate.php",
    "create-screenshot.ps1",
    "screenshot.txt",
    ".git",
    ".gitignore",
    "node_modules",
    "dist",
    "tests"
)

foreach ($file in $excludeFiles) {
    $path = Join-Path $tempDir $file
    if (Test-Path $path) {
        Remove-Item $path -Recurse -Force
    }
}

# Create ZIP file
if (Test-Path $zipFile) {
    Remove-Item $zipFile -Force
}

Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory($tempDir, $zipFile)

# Clean up
Remove-Item $tempDir -Recurse -Force

# Verify ZIP file
$zipSize = (Get-Item $zipFile).Length
$maxSize = 50MB

if ($zipSize -gt $maxSize) {
    Write-Warning "Warning: ZIP file size ($($zipSize/1MB) MB) exceeds WordPress.com limit (50 MB)"
} else {
    Write-Host "Success: Theme packaged successfully. Size: $($zipSize/1MB) MB"
}

Write-Host "Theme package created at: $zipFile" 