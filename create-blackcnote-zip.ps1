# BlackCnote Theme Packaging Script
# ================================================
# CANONICAL PATHWAYS - DO NOT CHANGE
# Project Root: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
# Theme Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote
# WP-Content Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
# Theme Files: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote
# ================================================
# Using correct pathway: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote

Write-Host "[START] Creating BlackCnote Theme Package..." -ForegroundColor Green
Write-Host "Using correct pathway: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Green

# Set correct paths
$ThemePath = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote"
$OutputFile = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\BlackCnote-Theme-Complete.zip"

# Check if theme path exists
if (-not (Test-Path $ThemePath)) {
    Write-Host "[ERROR] Theme path not found: $ThemePath" -ForegroundColor Red
    exit 1
}

Write-Host "[OK] Theme path found: $ThemePath" -ForegroundColor Green

# Remove existing zip file if it exists
if (Test-Path $OutputFile) {
    Remove-Item $OutputFile -Force
    Write-Host "🧹 Removed existing zip file" -ForegroundColor Yellow
}

# Create zip file
try {
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    [System.IO.Compression.ZipFile]::CreateFromDirectory($ThemePath, $OutputFile)
    Write-Host "[OK] Zip file created successfully!" -ForegroundColor Green
} catch {
    Write-Host "[ERROR] Error creating zip file: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Verify zip file was created
if (Test-Path $OutputFile) {
    $FileSize = (Get-Item $OutputFile).Length
    $FileSizeMB = [math]::Round($FileSize / 1MB, 2)
    
    Write-Host "`n[PACKAGE] Package Details:" -ForegroundColor Green
    Write-Host "==================" -ForegroundColor Green
    Write-Host "[FOLDER] File: $OutputFile" -ForegroundColor Cyan
    Write-Host "[SIZE] Size: $FileSizeMB MB" -ForegroundColor Cyan
    
    # Count files in zip
    $Zip = [System.IO.Compression.ZipFile]::OpenRead($OutputFile)
    $FileCount = $Zip.Entries.Count
    $Zip.Dispose()
    
    Write-Host "[INFO] Files: $FileCount" -ForegroundColor Cyan
    
    Write-Host "`n[SUCCESS] BlackCnote Theme Package Complete!" -ForegroundColor Green
    Write-Host "Ready for deployment!" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Zip file was not created" -ForegroundColor Red
    exit 1
} 