# BlackCnote Theme Packaging Script

# Set paths
$themeDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$zipFile = Join-Path $themeDir "blackcnote-theme.zip"

# Files to include
$files = @(
    "style.css",
    "index.php",
    "functions.php",
    "header.php",
    "footer.php",
    "sidebar.php",
    "page.php",
    "single.php",
    "archive.php",
    "search.php",
    "404.php",
    "screenshot.png",
    "README.md",
    "CHANGELOG.md",
    "LICENSE.txt",
    "template-blackcnote-dashboard.php",
    "template-blackcnote-plans.php",
    "template-blackcnote-transactions.php",
    "blackcnotelab/dashboard.php",
    "assets/css/blackcnote-theme.css",
    "assets/js/blackcnote-theme.js",
    "languages/blackcnote-theme.pot",
    "tests/test-blackcnote-theme.php"
)

# Directories to include
$directories = @(
    "assets",
    "inc",
    "template-parts",
    "languages"
)

# Create zip file
if (Test-Path $zipFile) {
    Remove-Item $zipFile
}

Add-Type -AssemblyName System.IO.Compression.FileSystem
$compressionLevel = [System.IO.Compression.CompressionLevel]::Optimal

# Create zip archive
$zip = [System.IO.Compression.ZipFile]::Open($zipFile, [System.IO.Compression.ZipArchiveMode]::Create)

# Add files
foreach ($file in $files) {
    $filePath = Join-Path $themeDir $file
    if (Test-Path $filePath) {
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $filePath, $file, $compressionLevel)
    }
}

# Add directories
foreach ($dir in $directories) {
    $dirPath = Join-Path $themeDir $dir
    if (Test-Path $dirPath) {
        Get-ChildItem -Path $dirPath -Recurse | ForEach-Object {
            $relativePath = $_.FullName.Substring($themeDir.Length + 1)
            if ($_.PSIsContainer) {
                $zip.CreateEntry($relativePath + "\")
            } else {
                [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $_.FullName, $relativePath, $compressionLevel)
            }
        }
    }
}

# Close zip file
$zip.Dispose()

Write-Host "Theme packaged successfully: $zipFile" 