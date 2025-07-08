# BlackCnote Headers Already Sent Fix Script
# Diagnoses and fixes "headers already sent" errors
# Version: 1.0.0

param(
    [switch]$Fix,
    [switch]$Verbose,
    [switch]$CheckOnly
)

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { Write-ColorOutput $args "Green" }
function Write-Warning { Write-ColorOutput $args "Yellow" }
function Write-Error { Write-ColorOutput $args "Red" }
function Write-Info { Write-ColorOutput $args "Cyan" }

# Set project root
$projectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
Set-Location $projectRoot

Write-Info "=========================================="
Write-Info "BLACKCNOTE HEADERS ALREADY SENT FIX SCRIPT"
Write-Info "=========================================="
Write-Info "Starting diagnosis at: $(Get-Date)"
Write-Info ""

# Function to check file for BOM and whitespace issues
function Test-PHPFile {
    param([string]$FilePath)
    
    if (-not (Test-Path $FilePath)) {
        return @{ Status = "Missing"; Issues = @("File does not exist") }
    }
    
    $content = Get-Content $FilePath -Raw -Encoding UTF8
    $issues = @()
    
    # Check for BOM
    $bom = [System.Text.Encoding]::UTF8.GetPreamble()
    $fileBytes = [System.IO.File]::ReadAllBytes($FilePath)
    if ($fileBytes.Length -ge 3 -and $fileBytes[0] -eq $bom[0] -and $fileBytes[1] -eq $bom[1] -and $fileBytes[2] -eq $bom[2]) {
        $issues += "BOM detected at start of file"
    }
    
    # Check for whitespace before <?php
    if ($content -match '^\s+<\?php') {
        $issues += "Whitespace before <?php tag"
    }
    
    # Check for whitespace after closing ?>
    if ($content -match '\?>\s*$') {
        $issues += "Whitespace after closing ?> tag"
    }
    
    # Check for echo/print before headers
    $lines = Get-Content $FilePath
    $phpStarted = $false
    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i]
        if ($line -match '^\s*<\?php') {
            $phpStarted = $true
        }
        if ($phpStarted -and $line -match '^\s*(echo|print|var_dump|print_r)\s') {
            $issues += "Output statement found at line $($i + 1): $($line.Trim())"
        }
    }
    
    # Check for HTML before <?php
    if ($content -match '^[^<]*<\?php') {
        $issues += "HTML or text before <?php tag"
    }
    
    if ($issues.Count -eq 0) {
        return @{ Status = "Clean"; Issues = @() }
    } else {
        return @{ Status = "Issues"; Issues = $issues }
    }
}

# Function to fix PHP file issues
function Fix-PHPFile {
    param([string]$FilePath)
    
    Write-Info "Fixing: $FilePath"
    
    # Read file content
    $content = Get-Content $FilePath -Raw -Encoding UTF8
    
    # Remove BOM if present
    $bom = [System.Text.Encoding]::UTF8.GetPreamble()
    $fileBytes = [System.IO.File]::ReadAllBytes($FilePath)
    if ($fileBytes.Length -ge 3 -and $fileBytes[0] -eq $bom[0] -and $fileBytes[1] -eq $bom[1] -and $fileBytes[2] -eq $bom[2]) {
        $content = $content.Substring(3)
        Write-Warning "  - Removed BOM"
    }
    
    # Remove whitespace before <?php
    $content = $content -replace '^\s+<\?php', '<?php'
    
    # Remove closing ?> tag and whitespace after it
    $content = $content -replace '\?>\s*$', ''
    
    # Remove HTML/text before <?php
    $content = $content -replace '^[^<]*<\?php', '<?php'
    
    # Write back to file
    [System.IO.File]::WriteAllText($FilePath, $content, [System.Text.Encoding]::UTF8)
    
    Write-Success "  - File fixed"
}

# Files to check (in order of importance)
$filesToCheck = @(
    "blackcnote\wp-content\themes\blackcnote\functions.php",
    "blackcnote\wp-content\themes\blackcnote\inc\blackcnote-react-loader.php",
    "blackcnote\wp-content\themes\blackcnote\inc\menu-registration.php",
    "blackcnote\wp-content\themes\blackcnote\inc\full-content-checker.php",
    "blackcnote\wp-content\plugins\blackcnote-cors\blackcnote-cors.php",
    "blackcnote\wp-content\plugins\blackcnote-hyiplab-api\blackcnote-hyiplab-api.php",
    "blackcnote\wp-content\themes\blackcnote\header.php",
    "blackcnote\wp-content\themes\blackcnote\footer.php",
    "blackcnote\wp-content\themes\blackcnote\index.php"
)

Write-Info "Step 1: Checking PHP files for output issues..."
Write-Info ""

$issuesFound = @()

foreach ($file in $filesToCheck) {
    $result = Test-PHPFile $file
    Write-Info "Checking: $file"
    
    if ($result.Status -eq "Clean") {
        Write-Success "  ✅ Clean"
    } elseif ($result.Status -eq "Missing") {
        Write-Warning "  ⚠️  Missing"
    } else {
        Write-Error "  ❌ Issues found:"
        foreach ($issue in $result.Issues) {
            Write-Error "    - $issue"
        }
        $issuesFound += @{ File = $file; Issues = $result.Issues }
    }
}

Write-Info ""
Write-Info "Step 2: Summary"
Write-Info "================"

if ($issuesFound.Count -eq 0) {
    Write-Success "✅ No issues found in PHP files"
} else {
    Write-Warning "⚠️  Found $($issuesFound.Count) files with issues:"
    foreach ($issue in $issuesFound) {
        Write-Warning "  - $($issue.File)"
        foreach ($problem in $issue.Issues) {
            Write-Warning "    * $problem"
        }
    }
}

# Fix issues if requested
if ($Fix -and $issuesFound.Count -gt 0) {
    Write-Info ""
    Write-Info "Step 3: Fixing issues..."
    Write-Info "========================"
    
    foreach ($issue in $issuesFound) {
        Fix-PHPFile $issue.File
    }
    
    Write-Info ""
    Write-Success "✅ All issues have been fixed!"
}

# Test WordPress frontend
Write-Info ""
Write-Info "Step 4: Testing WordPress frontend..."
Write-Info "====================================="

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8888" -TimeoutSec 10 -UseBasicParsing
    Write-Success "✅ WordPress frontend is accessible (HTTP $($response.StatusCode))"
    
    # Check for React app container
    if ($response.Content -match "blackcnote-react-app") {
        Write-Success "✅ React app container found in frontend"
    } else {
        Write-Warning "⚠️  React app container not found in frontend"
    }
    
    # Check for error messages
    if ($response.Content -match "Warning:|Error:|Fatal error:") {
        Write-Error "❌ PHP errors found in frontend:"
        $errors = $response.Content -split "`n" | Where-Object { $_ -match "Warning:|Error:|Fatal error:" }
        foreach ($error in $errors[0..4]) { # Show first 5 errors
            Write-Error "  $($error.Trim())"
        }
    } else {
        Write-Success "✅ No PHP errors found in frontend"
    }
    
} catch {
    Write-Error "❌ WordPress frontend is not accessible: $($_.Exception.Message)"
}

# Check Docker containers
Write-Info ""
Write-Info "Step 5: Checking Docker containers..."
Write-Info "===================================="

try {
    $containers = docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
    Write-Success "✅ Docker containers status:"
    Write-Info $containers
} catch {
    Write-Error "❌ Docker is not running or accessible"
}

# Final recommendations
Write-Info ""
Write-Info "Step 6: Recommendations"
Write-Info "========================"

if ($issuesFound.Count -gt 0 -and -not $Fix) {
    Write-Warning "⚠️  Run this script with -Fix flag to automatically fix the issues:"
    Write-Info "   .\scripts\fix-headers-already-sent.ps1 -Fix"
}

Write-Info "🔧 Additional troubleshooting steps:"
Write-Info "   1. Clear browser cache and reload the page"
Write-Info "   2. Check WordPress debug log: blackcnote\wp-content\debug.log"
Write-Info "   3. Restart Docker containers: docker-compose restart"
Write-Info "   4. Check for any output in wp-config.php"

Write-Info ""
Write-Info "=========================================="
Write-Info "DIAGNOSIS COMPLETE"
Write-Info "==========================================" 