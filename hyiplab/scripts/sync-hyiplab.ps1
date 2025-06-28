# HYIPLab Plugin Sync Script (XAMPP Development)
# ================================================
# CANONICAL PATHWAYS - DO NOT CHANGE
# Project Root: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
# Theme Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote
# WP-Content Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
# ================================================
# NOTE: This script is for XAMPP development only
# For Docker development, use the canonical blackcnote/wp-content directory directly

param(
    [switch]$Watch,
    [switch]$OneTime
)

$sourcePath = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\hyiplab"
$targetPath = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\plugins\hyiplab"

Write-Host "HYIPLab Plugin Sync Script (XAMPP Development)" -ForegroundColor Green
Write-Host "Source: $sourcePath" -ForegroundColor Yellow
Write-Host "Target: $targetPath" -ForegroundColor Yellow
Write-Host ""

# Function to sync files
function Sync-HyiplabPlugin {
    Write-Host "Syncing HYIPLab plugin..." -ForegroundColor Cyan
    
    # Remove existing target if it exists
    if (Test-Path $targetPath) {
        Write-Host "Removing existing plugin directory..." -ForegroundColor Yellow
        Remove-Item $targetPath -Recurse -Force
    }
    
    # Copy all files from source to target
    Write-Host "Copying files..." -ForegroundColor Yellow
    Copy-Item $sourcePath $targetPath -Recurse -Force
    
    Write-Host "Sync completed successfully!" -ForegroundColor Green
    Write-Host "Plugin is now available at: $targetPath" -ForegroundColor Green
    Write-Host ""
}

# Function to watch for changes
function Watch-HyiplabPlugin {
    Write-Host "Starting file watcher..." -ForegroundColor Cyan
    Write-Host "Press Ctrl+C to stop watching" -ForegroundColor Yellow
    Write-Host ""
    
    $watcher = New-Object System.IO.FileSystemWatcher
    $watcher.Path = $sourcePath
    $watcher.IncludeSubdirectories = $true
    $watcher.EnableRaisingEvents = $true
    
    $action = {
        $path = $Event.SourceEventArgs.FullPath
        $changeType = $Event.SourceEventArgs.ChangeType
        $timeStamp = (Get-Date).ToString('yyyy-MM-dd HH:mm:ss')
        Write-Host "[$timeStamp] $changeType : $path" -ForegroundColor Magenta
        
        # Debounce the sync to avoid multiple rapid syncs
        Start-Sleep -Seconds 1
        Sync-HyiplabPlugin
    }
    
    # Register event handlers
    Register-ObjectEvent $watcher "Created" -Action $action
    Register-ObjectEvent $watcher "Changed" -Action $action
    Register-ObjectEvent $watcher "Deleted" -Action $action
    Register-ObjectEvent $watcher "Renamed" -Action $action
    
    # Keep the script running
    try {
        while ($true) {
            Start-Sleep -Seconds 1
        }
    }
    catch {
        Write-Host "Stopping file watcher..." -ForegroundColor Yellow
        Unregister-Event -SourceIdentifier $watcher.Created
        Unregister-Event -SourceIdentifier $watcher.Changed
        Unregister-Event -SourceIdentifier $watcher.Deleted
        Unregister-Event -SourceIdentifier $watcher.Renamed
        $watcher.EnableRaisingEvents = $false
        $watcher.Dispose()
    }
}

# Main execution
if ($OneTime) {
    Sync-HyiplabPlugin
}
elseif ($Watch) {
    Sync-HyiplabPlugin
    Watch-HyiplabPlugin
}
else {
    Write-Host "Usage:" -ForegroundColor White
    Write-Host "  .\sync-hyiplab.ps1 -OneTime    # Sync once" -ForegroundColor Gray
    Write-Host "  .\sync-hyiplab.ps1 -Watch      # Sync and watch for changes" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Examples:" -ForegroundColor White
    Write-Host "  .\sync-hyiplab.ps1 -OneTime" -ForegroundColor Gray
    Write-Host "  .\sync-hyiplab.ps1 -Watch" -ForegroundColor Gray
} 