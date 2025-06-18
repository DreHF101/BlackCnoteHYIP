# BlackCnote Theme Packaging Script (Filtered by Extension)
# This script creates a ZIP file of the theme for distribution, only including files with correct extensions.

# Set error action preference
$ErrorActionPreference = "Stop"

# Define paths
$themeDir = $PSScriptRoot
$zipFile = Join-Path $themeDir "blackcnote.zip"

# Extensions and files to include
$includeExtensions = @("*.php", "*.css", "*.js", "*.md", "*.txt", "*.pot", "*.png")
$includeFiles = @("screenshot.png") # Add any single files without extension if needed

# Files/directories to exclude
$excludePatterns = @(".git", ".gitignore", "package.ps1", "package.sh", "*.log", "*.zip")

# Remove existing ZIP file if it exists
if (Test-Path $zipFile) {
    Remove-Item -Path $zipFile -Force
}

# Gather all files to include
$filesToZip = @()
foreach ($ext in $includeExtensions) {
    $filesToZip += Get-ChildItem -Path $themeDir -Recurse -Include $ext -File | Where-Object {
        $excludePatterns -notcontains $_.Name -and
        ($_.FullName -notmatch '\\.git(\\|$)')
    }
}
foreach ($file in $includeFiles) {
    $found = Get-ChildItem -Path $themeDir -Recurse -Include $file -File
    if ($found) { $filesToZip += $found }
}

# Add all directories (to preserve structure)
$dirsToZip = Get-ChildItem -Path $themeDir -Recurse -Directory | Where-Object {
    $excludePatterns -notcontains $_.Name -and
    ($_.FullName -notmatch '\\.git(\\|$)')
}

# Prepare temp directory for zipping
$tempDir = Join-Path $env:TEMP "blackcnote-zip-temp"
if (Test-Path $tempDir) { Remove-Item -Path $tempDir -Recurse -Force }
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Copy files and directories to temp
foreach ($dir in $dirsToZip) {
    $relPath = $dir.FullName.Substring($themeDir.Length).TrimStart('\')
    $targetDir = Join-Path $tempDir $relPath
    New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
}
foreach ($file in $filesToZip) {
    # Calculate relative path including filename and extension
    $relPath = $file.FullName.Substring($themeDir.Length).TrimStart('\','/')
    $targetFile = Join-Path $tempDir $relPath
    $targetDir = Split-Path $targetFile -Parent
    if (!(Test-Path $targetDir)) {
        New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
    }
    Copy-Item -Path $file.FullName -Destination $targetFile -Force
}

# Create ZIP from temp directory
Compress-Archive -Path "$tempDir\*" -DestinationPath $zipFile -Force

# Validate theme files (moved up before cleanup)
$requiredFiles = @(
    "style.css",
    "functions.php",
    "index.php",
    "header.php",
    "footer.php",
    "template-blackcnote-dashboard.php",
    "template-blackcnote-plans.php",
    "template-blackcnote-transactions.php",
    "blackcnotelab/dashboard.php",
    "assets/css/blackcnote-theme.css",
    "assets/js/blackcnote-theme.js",
    "languages/blackcnote-theme.pot",
    "tests/test-blackcnote-theme.php",
    "README.md",
    "CHANGELOG.md",
    "LICENSE.txt",
    "screenshot.png",
    "screenshot.txt",
    "validate.php"
)

$missingFiles = @()
foreach ($file in $requiredFiles) {
    $filePath = Join-Path $tempDir $file
    if (-not (Test-Path $filePath)) {
        $missingFiles += $file
    }
}

if ($missingFiles.Count -gt 0) {
    Write-Warning "Missing required files:"
    $missingFiles | ForEach-Object {
        Write-Warning "- $_"
    }
    exit 1
}

# Clean up temp directory (moved down)
Remove-Item -Path $tempDir -Recurse -Force

Write-Host "Theme packaged successfully: $zipFile"
Write-Host "`nTheme packaging completed successfully!"

# Create temporary directory
New-Item -ItemType Directory -Force -Path "../temp/blackcnote"

# Copy theme files
Copy-Item -Path "*" -Destination "../temp/blackcnote/" -Recurse

# Remove development files
Remove-Item -Path "../temp/blackcnote/package.ps1" -Force
Remove-Item -Path "../temp/blackcnote/screenshot.txt" -Force

# Create zip file
Set-Location "../temp"
Compress-Archive -Path "blackcnote" -DestinationPath "blackcnote.zip" -Force

# Move zip to themes directory
Move-Item -Path "blackcnote.zip" -Destination "../themes/" -Force

# Clean up
Remove-Item -Path "blackcnote" -Recurse -Force
Set-Location "../themes/blackcnote"

Write-Host "Theme packaged successfully as blackcnote.zip" 