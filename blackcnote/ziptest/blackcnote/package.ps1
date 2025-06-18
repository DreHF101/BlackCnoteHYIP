# HYIP Theme Packaging Script
# This script creates a ZIP file of the theme for distribution

# Set error action preference
$ErrorActionPreference = "Stop"

# Define paths
$themeDir = $PSScriptRoot
$zipFile = Join-Path $themeDir "hyip-theme.zip"

# Files to exclude
$excludeFiles = @(
    ".git",
    ".gitignore",
    "package.ps1",
    "node_modules",
    "*.log",
    "*.zip"
)

# Create temporary directory
$tempDir = Join-Path $env:TEMP "hyip-theme-temp"
if (Test-Path $tempDir) {
    Remove-Item -Path $tempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Copy theme files to temporary directory
Get-ChildItem -Path $themeDir -Exclude $excludeFiles | Copy-Item -Destination $tempDir -Recurse

# Remove existing ZIP file if it exists
if (Test-Path $zipFile) {
    Remove-Item -Path $zipFile -Force
}

# Create ZIP file
Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory($tempDir, $zipFile)

# Clean up temporary directory
Remove-Item -Path $tempDir -Recurse -Force

# Verify ZIP file
if (Test-Path $zipFile) {
    Write-Host "Theme packaged successfully: $zipFile"
    
    # List contents of ZIP file
    Write-Host "`nContents of ZIP file:"
    $shell = New-Object -ComObject Shell.Application
    $zip = $shell.NameSpace($zipFile)
    $zip.Items() | ForEach-Object {
        Write-Host $_.Name
    }
} else {
    Write-Error "Failed to create ZIP file"
    exit 1
}

# Validate theme files
$requiredFiles = @(
    "style.css",
    "functions.php",
    "index.php",
    "header.php",
    "footer.php",
    "template-hyip-dashboard.php",
    "template-hyip-plans.php",
    "template-hyip-transactions.php",
    "hyiplab/dashboard.php",
    "assets/css/hyip-theme.css",
    "assets/js/hyip-theme.js",
    "languages/hyip-theme.pot",
    "tests/test-hyip-theme.php",
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

# Check file permissions
Get-ChildItem -Path $tempDir -Recurse | ForEach-Object {
    if ($_.IsFile) {
        $permissions = (Get-Acl $_.FullName).Access
        $hasReadPermission = $permissions | Where-Object {
            $_.FileSystemRights -match "Read" -and
            $_.IdentityReference -eq "Everyone"
        }
        if (-not $hasReadPermission) {
            Write-Warning "File may have incorrect permissions: $($_.FullName)"
        }
    }
}

# Check for PHP syntax errors
$phpFiles = Get-ChildItem -Path $tempDir -Filter "*.php" -Recurse
foreach ($file in $phpFiles) {
    $output = php -l $file.FullName 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Error "PHP syntax error in $($file.FullName):"
        Write-Error $output
        exit 1
    }
}

# Check for WordPress coding standards
if (Get-Command phpcs -ErrorAction SilentlyContinue) {
    $phpcsOutput = phpcs --standard=WordPress $tempDir
    if ($LASTEXITCODE -ne 0) {
        Write-Warning "WordPress coding standards violations:"
        Write-Warning $phpcsOutput
    }
}

Write-Host "`nTheme packaging completed successfully!" 