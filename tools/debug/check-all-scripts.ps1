# BlackCnote Script Integrity Checker
# Comprehensive script validation for all file types
# Integrated with BlackCnote Debug System

param(
    [string]$Root = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote",
    [string]$LogFile = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\logs\script-check.log",
    [string]$JsonLogFile = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\logs\script-check.json",
    [switch]$Verbose,
    [switch]$FixEmojis,
    [switch]$EmailReport
)

# Set error action preference
$ErrorActionPreference = "Continue"

# Create logs directory if it doesn't exist
$logsDir = Split-Path $LogFile -Parent
if (-not (Test-Path $logsDir)) {
    New-Item -ItemType Directory -Path $logsDir -Force | Out-Null
}

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    if ($Verbose) {
        Write-Host $Message -ForegroundColor $Color
    }
}

# Function to check bracket balance
function Test-BracketBalance {
    param([string]$Content, [string]$FileType)
    
    $errors = @()
    
    switch ($FileType) {
        "powershell" {
            # Check PowerShell brackets
            $openBraces = ($Content -split '' | Where-Object { $_ -eq '{' }).Count
            $closeBraces = ($Content -split '' | Where-Object { $_ -eq '}' }).Count
            if ($openBraces -ne $closeBraces) {
                $errors += "Unbalanced braces: $openBraces open, $closeBraces close"
            }
            
            # Check parentheses
            $openParens = ($Content -split '' | Where-Object { $_ -eq '(' }).Count
            $closeParens = ($Content -split '' | Where-Object { $_ -eq ')' }).Count
            if ($openParens -ne $closeParens) {
                $errors += "Unbalanced parentheses: $openParens open, $closeParens close"
            }
            
            # Check square brackets
            $openBrackets = ($Content -split '' | Where-Object { $_ -eq '[' }).Count
            $closeBrackets = ($Content -split '' | Where-Object { $_ -eq ']' }).Count
            if ($openBrackets -ne $closeBrackets) {
                $errors += "Unbalanced brackets: $openBrackets open, $closeBrackets close"
            }
        }
        "batch" {
            # Check batch parentheses
            $openParens = ($Content -split '' | Where-Object { $_ -eq '(' }).Count
            $closeParens = ($Content -split '' | Where-Object { $_ -eq ')' }).Count
            if ($openParens -ne $closeParens) {
                $errors += "Unbalanced parentheses: $openParens open, $closeParens close"
            }
        }
        "php" {
            # Check PHP brackets and parentheses
            $openBraces = ($Content -split '' | Where-Object { $_ -eq '{' }).Count
            $closeBraces = ($Content -split '' | Where-Object { $_ -eq '}' }).Count
            if ($openBraces -ne $closeBraces) {
                $errors += "Unbalanced braces: $openBraces open, $closeBraces close"
            }
            
            $openParens = ($Content -split '' | Where-Object { $_ -eq '(' }).Count
            $closeParens = ($Content -split '' | Where-Object { $_ -eq ')' }).Count
            if ($openParens -ne $closeParens) {
                $errors += "Unbalanced parentheses: $openParens open, $closeParens close"
            }
        }
    }
    
    return $errors
}

# Function to check for Unicode/emoji characters
function Test-UnicodeCharacters {
    param([string]$Content)
    
    $issues = @()
    
    # Emoji pattern (Unicode ranges for emojis)
    $emojiPattern = '[\uD800-\uDBFF][\uDC00-\uDFFF]|[\u2600-\u27BF]|[\u2300-\u23FF]|[\u2000-\u206F]|[\u2100-\u214F]'
    
    # Non-ASCII pattern (excluding common programming characters)
    $nonAsciiPattern = '[^\x00-\x7F]'
    
    $lines = $Content -split "`n"
    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i]
        $lineNumber = $i + 1
        
        if ($line -match $emojiPattern) {
            $issues += "Line $lineNumber`: Emoji detected: $($line.Trim())"
        }
        elseif ($line -match $nonAsciiPattern) {
            # Check if it's a legitimate non-ASCII character (comments, strings, etc.)
            $cleanLine = $line -replace '".*?"', '' -replace "'.*?'", '' -replace '//.*$', '' -replace '#.*$', ''
            if ($cleanLine -match $nonAsciiPattern) {
                $issues += "Line $lineNumber`: Non-ASCII character detected: $($line.Trim())"
            }
        }
    }
    
    return $issues
}

# Function to check PowerShell syntax
function Test-PowerShellSyntax {
    param([string]$FilePath)
    
    try {
        $null = [System.Management.Automation.PSParser]::Tokenize((Get-Content $FilePath -Raw), [ref]$null)
        return $true
    }
    catch {
        return $false
    }
}

# Function to check batch syntax
function Test-BatchSyntax {
    param([string]$FilePath)
    
    try {
        # Basic batch syntax check
        $content = Get-Content $FilePath -Raw
        if ($content -match 'if\s+.*\s+\([^)]*$') {
            return $false
        }
        return $true
    }
    catch {
        return $false
    }
}

# Function to check PHP syntax
function Test-PHPSyntax {
    param([string]$FilePath)
    
    try {
        $output = php -l $FilePath 2>&1
        return $output -match "No syntax errors"
    }
    catch {
        return $false
    }
}

# Function to fix emoji issues
function Fix-EmojiIssues {
    param([string]$FilePath, [string]$FileType)
    
    $content = Get-Content $FilePath -Raw
    $originalContent = $content
    $fixed = $false
    
    # Replace common emojis with text equivalents
    $replacements = @{
        '✅' = '[OK]'
        '❌' = '[ERROR]'
        '⚠️' = '[WARNING]'
        '🚀' = '[START]'
        '🔧' = '[TOOL]'
        '📁' = '[FOLDER]'
        '🛠️' = '[BUILD]'
        '🚨' = '[ALERT]'
        '💡' = '[TIP]'
        '🎯' = '[TARGET]'
        '🔍' = '[SEARCH]'
        '📋' = '[INFO]'
        '🔒' = '[SECURITY]'
        '📚' = '[DOCS]'
        '🤝' = '[SUPPORT]'
        '📦' = '[PACKAGE]'
        '📏' = '[SIZE]'
        '🎉' = '[SUCCESS]'
    }
    
    foreach ($emoji in $replacements.Keys) {
        if ($content -match $emoji) {
            $content = $content -replace $emoji, $replacements[$emoji]
            $fixed = $true
        }
    }
    
    if ($fixed) {
        Set-Content -Path $FilePath -Value $content -Encoding UTF8
        return $true
    }
    
    return $false
}

# Main execution
Write-ColorOutput "=== BlackCnote Script Integrity Checker ===" "Cyan"
Write-ColorOutput "Starting comprehensive script validation..." "White"
Write-ColorOutput "Root directory: $Root" "Yellow"
Write-ColorOutput "Log file: $LogFile" "Yellow"
Write-ColorOutput ""

# Define file types to check
$fileTypes = @{
    "*.ps1" = "powershell"
    "*.bat" = "batch"
    "*.cmd" = "batch"
    "*.sh" = "shell"
    "*.php" = "php"
    "*.js" = "javascript"
    "*.py" = "python"
}

$results = @()
$totalFiles = 0
$errorFiles = 0
$warningFiles = 0

foreach ($pattern in $fileTypes.Keys) {
    $fileType = $fileTypes[$pattern]
    Write-ColorOutput "Checking $fileType files..." "Yellow"
    
    $files = Get-ChildItem -Path $Root -Recurse -Include $pattern -ErrorAction SilentlyContinue
    
    foreach ($file in $files) {
        $totalFiles++
        Write-ColorOutput "  Checking: $($file.Name)" "White"
        
        $entry = @{
            File = $file.FullName
            FileType = $fileType
            Name = $file.Name
            Size = $file.Length
            LastModified = $file.LastWriteTime
            Syntax = "OK"
            BracketBalance = "OK"
            UnicodeIssues = @()
            Errors = @()
            Warnings = @()
            Status = "PASS"
        }
        
        try {
            $content = Get-Content $file.FullName -Raw -ErrorAction Stop
            
            # Check for Unicode/emoji issues
            $unicodeIssues = Test-UnicodeCharacters -Content $content
            if ($unicodeIssues.Count -gt 0) {
                $entry.UnicodeIssues = $unicodeIssues
                $entry.Warnings += "Unicode/emoji characters detected"
                $entry.Status = "WARNING"
                $warningFiles++
                
                # Fix emoji issues if requested
                if ($FixEmojis) {
                    if (Fix-EmojiIssues -FilePath $file.FullName -FileType $fileType) {
                        $entry.Warnings += "Emoji issues fixed automatically"
                    }
                }
            }
            
            # Check bracket balance
            $bracketErrors = Test-BracketBalance -Content $content -FileType $fileType
            if ($bracketErrors.Count -gt 0) {
                $entry.BracketBalance = "ERROR"
                $entry.Errors += $bracketErrors
                $entry.Status = "ERROR"
                $errorFiles++
            }
            
            # Check syntax based on file type
            switch ($fileType) {
                "powershell" {
                    if (-not (Test-PowerShellSyntax -FilePath $file.FullName)) {
                        $entry.Syntax = "ERROR"
                        $entry.Errors += "PowerShell syntax error"
                        $entry.Status = "ERROR"
                        $errorFiles++
                    }
                }
                "batch" {
                    if (-not (Test-BatchSyntax -FilePath $file.FullName)) {
                        $entry.Syntax = "ERROR"
                        $entry.Errors += "Batch syntax error"
                        $entry.Status = "ERROR"
                        $errorFiles++
                    }
                }
                "php" {
                    if (-not (Test-PHPSyntax -FilePath $file.FullName)) {
                        $entry.Syntax = "ERROR"
                        $entry.Errors += "PHP syntax error"
                        $entry.Status = "ERROR"
                        $errorFiles++
                    }
                }
            }
            
        }
        catch {
            $entry.Errors += "Failed to read file: $($_.Exception.Message)"
            $entry.Status = "ERROR"
            $errorFiles++
        }
        
        $results += [PSCustomObject]$entry
    }
}

# Generate summary
$summary = @{
    Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    TotalFiles = $totalFiles
    ErrorFiles = $errorFiles
    WarningFiles = $warningFiles
    PassFiles = $totalFiles - $errorFiles - $warningFiles
    OverallStatus = if ($errorFiles -gt 0) { "ERROR" } elseif ($warningFiles -gt 0) { "WARNING" } else { "PASS" }
}

# Write detailed results to log file
$logContent = @"
=== BlackCnote Script Integrity Check Report ===
Generated: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
Root Directory: $Root

SUMMARY:
- Total Files Checked: $($summary.TotalFiles)
- Files with Errors: $($summary.ErrorFiles)
- Files with Warnings: $($summary.WarningFiles)
- Files Passing: $($summary.PassFiles)
- Overall Status: $($summary.OverallStatus)

DETAILED RESULTS:
"@

$results | Format-Table -Property Name, FileType, Status, Syntax, BracketBalance, @{Name="UnicodeIssues";Expression={$_.UnicodeIssues.Count}} | Out-String | ForEach-Object { $logContent += $_ }

$logContent += "`n`nDETAILED ISSUES:`n"
foreach ($result in $results | Where-Object { $_.Status -ne "PASS" }) {
    $logContent += "`nFile: $($result.Name)`n"
    $logContent += "Type: $($result.FileType)`n"
    $logContent += "Status: $($result.Status)`n"
    if ($result.Errors.Count -gt 0) {
        $logContent += "Errors:`n"
        foreach ($error in $result.Errors) {
            $logContent += "  - $error`n"
        }
    }
    if ($result.Warnings.Count -gt 0) {
        $logContent += "Warnings:`n"
        foreach ($warning in $result.Warnings) {
            $logContent += "  - $warning`n"
        }
    }
    if ($result.UnicodeIssues.Count -gt 0) {
        $logContent += "Unicode Issues:`n"
        foreach ($issue in $result.UnicodeIssues) {
            $logContent += "  - $issue`n"
        }
    }
    $logContent += "`n"
}

Set-Content -Path $LogFile -Value $logContent -Encoding UTF8

# Write JSON results for programmatic access
$jsonData = @{
    Summary = $summary
    Results = $results
}
$jsonData | ConvertTo-Json -Depth 10 | Set-Content -Path $JsonLogFile -Encoding UTF8

# Display summary
Write-ColorOutput "`n=== CHECK COMPLETE ===" "Cyan"
Write-ColorOutput "Total Files: $($summary.TotalFiles)" "White"
Write-ColorOutput "Errors: $($summary.ErrorFiles)" "Red"
Write-ColorOutput "Warnings: $($summary.WarningFiles)" "Yellow"
Write-ColorOutput "Passed: $($summary.PassFiles)" "Green"
Write-ColorOutput "Overall Status: $($summary.OverallStatus)" $(if ($summary.OverallStatus -eq "PASS") { "Green" } elseif ($summary.OverallStatus -eq "WARNING") { "Yellow" } else { "Red" })
Write-ColorOutput ""
Write-ColorOutput "Detailed report written to: $LogFile" "Cyan"
Write-ColorOutput "JSON data written to: $JsonLogFile" "Cyan"

# Return exit code based on results
if ($errorFiles -gt 0) {
    exit 1
} elseif ($warningFiles -gt 0) {
    exit 2
} else {
    exit 0
} 