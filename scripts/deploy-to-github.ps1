# BlackCnote GitHub Deployment Script
# Comprehensive deployment script for BlackCnote project

param(
    [string]$Action = "sync",
    [string]$Message = "",
    [switch]$Force,
    [switch]$Deploy,
    [switch]$Build,
    [switch]$Test
)

# Configuration
$Config = @{
    RepoUrl = "https://github.com/DreHF101/BlackCnoteHYIP.git"
    Branch = "main"
    ProjectRoot = Split-Path -Parent $PSScriptRoot
    WordPressDir = "blackcnote"
    ReactDir = "react-app"
    ThemeDir = "blackcnote/wp-content/themes/blackcnote"
    ExcludeFiles = @(
        "wp-config.php",
        ".env",
        "node_modules/",
        "vendor/",
        "*.log",
        ".git/",
        ".vscode/",
        "*.tmp",
        "*.cache"
    )
}

# Colors for output
$Colors = @{
    Success = "Green"
    Error = "Red"
    Warning = "Yellow"
    Info = "Cyan"
    Debug = "Gray"
}

function Write-ColorOutput {
    param(
        [string]$Message,
        [string]$Color = "White"
    )
    Write-Host $Message -ForegroundColor $Colors[$Color]
}

function Test-Prerequisites {
    Write-ColorOutput "Checking prerequisites..." "Info"
    
    # Check if git is available
    try {
        $gitVersion = git --version
        Write-ColorOutput "✓ Git: $gitVersion" "Success"
    } catch {
        Write-ColorOutput "✗ Git not found. Please install Git." "Error"
        exit 1
    }
    
    # Check if we're in a git repository
    if (-not (Test-Path ".git")) {
        Write-ColorOutput "✗ Not in a git repository. Please run this script from the project root." "Error"
        exit 1
    }
    
    # Check if we're on the correct branch
    $currentBranch = git branch --show-current
    if ($currentBranch -ne $Config.Branch) {
        Write-ColorOutput "⚠ Current branch is '$currentBranch', expected '$($Config.Branch)'" "Warning"
        if (-not $Force) {
            $response = Read-Host "Continue anyway? (y/N)"
            if ($response -ne "y" -and $response -ne "Y") {
                exit 1
            }
        }
    }
    
    Write-ColorOutput "✓ Prerequisites check completed" "Success"
}

function Get-GitStatus {
    Write-ColorOutput "Checking Git status..." "Info"
    
    $status = @{
        HasChanges = $false
        StagedFiles = @()
        UnstagedFiles = @()
        UntrackedFiles = @()
        Branch = git branch --show-current
        LastCommit = git log -1 --format="%h - %s (%cr)" 2>$null
    }
    
    # Get staged files
    $staged = git diff --cached --name-only 2>$null
    if ($staged) {
        $status.StagedFiles = $staged
        $status.HasChanges = $true
    }
    
    # Get unstaged files
    $unstaged = git diff --name-only 2>$null
    if ($unstaged) {
        $status.UnstagedFiles = $unstaged
        $status.HasChanges = $true
    }
    
    # Get untracked files
    $untracked = git ls-files --others --exclude-standard 2>$null
    if ($untracked) {
        $status.UntrackedFiles = $untracked
        $status.HasChanges = $true
    }
    
    return $status
}

function Show-GitStatus {
    param([hashtable]$Status)
    
    Write-ColorOutput "Git Status:" "Info"
    Write-ColorOutput "  Branch: $($Status.Branch)" "Info"
    Write-ColorOutput "  Last Commit: $($Status.LastCommit)" "Info"
    
    if ($Status.HasChanges) {
        Write-ColorOutput "  Changes detected:" "Warning"
        
        if ($Status.StagedFiles.Count -gt 0) {
            Write-ColorOutput "    Staged files ($($Status.StagedFiles.Count)):" "Info"
            $Status.StagedFiles | ForEach-Object { Write-ColorOutput "      $_" "Debug" }
        }
        
        if ($Status.UnstagedFiles.Count -gt 0) {
            Write-ColorOutput "    Unstaged files ($($Status.UnstagedFiles.Count)):" "Info"
            $Status.UnstagedFiles | ForEach-Object { Write-ColorOutput "      $_" "Debug" }
        }
        
        if ($Status.UntrackedFiles.Count -gt 0) {
            Write-ColorOutput "    Untracked files ($($Status.UntrackedFiles.Count)):" "Info"
            $Status.UntrackedFiles | ForEach-Object { Write-ColorOutput "      $_" "Debug" }
        }
    } else {
        Write-ColorOutput "  No changes detected" "Success"
    }
}

function Add-Changes {
    Write-ColorOutput "Adding changes to Git..." "Info"
    
    # Add all changes
    git add .
    
    if ($LASTEXITCODE -eq 0) {
        Write-ColorOutput "✓ Changes added successfully" "Success"
    } else {
        Write-ColorOutput "✗ Failed to add changes" "Error"
        exit 1
    }
}

function Commit-Changes {
    param([string]$Message)
    
    if (-not $Message) {
        $Message = "BlackCnote Live Edit: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
    }
    
    Write-ColorOutput "Committing changes..." "Info"
    Write-ColorOutput "  Message: $Message" "Debug"
    
    git commit -m $Message
    
    if ($LASTEXITCODE -eq 0) {
        Write-ColorOutput "✓ Changes committed successfully" "Success"
    } else {
        Write-ColorOutput "✗ Failed to commit changes" "Error"
        exit 1
    }
}

function Push-Changes {
    Write-ColorOutput "Pushing changes to GitHub..." "Info"
    
    git push origin $Config.Branch
    
    if ($LASTEXITCODE -eq 0) {
        Write-ColorOutput "✓ Changes pushed successfully" "Success"
    } else {
        Write-ColorOutput "✗ Failed to push changes" "Error"
        exit 1
    }
}

function Pull-Changes {
    Write-ColorOutput "Pulling changes from GitHub..." "Info"
    
    git pull origin $Config.Branch
    
    if ($LASTEXITCODE -eq 0) {
        Write-ColorOutput "✓ Changes pulled successfully" "Success"
    } else {
        Write-ColorOutput "✗ Failed to pull changes" "Error"
        exit 1
    }
}

function Sync-WithGitHub {
    Write-ColorOutput "Syncing with GitHub..." "Info"
    
    $status = Get-GitStatus
    Show-GitStatus $status
    
    if ($status.HasChanges) {
        if (-not $Force) {
            $response = Read-Host "Commit and push changes? (Y/n)"
            if ($response -eq "n" -or $response -eq "N") {
                Write-ColorOutput "Sync cancelled" "Warning"
                return
            }
        }
        
        Add-Changes
        Commit-Changes $Message
        Push-Changes
    } else {
        Write-ColorOutput "No changes to sync" "Info"
    }
    
    Write-ColorOutput "✓ Sync completed" "Success"
}

function Build-ReactApp {
    Write-ColorOutput "Building React app..." "Info"
    
    $reactDir = Join-Path $Config.ProjectRoot $Config.ReactDir
    
    if (-not (Test-Path $reactDir)) {
        Write-ColorOutput "✗ React app directory not found: $reactDir" "Error"
        return $false
    }
    
    # Change to React directory
    Push-Location $reactDir
    
    try {
        # Install dependencies if needed
        if (-not (Test-Path "node_modules")) {
            Write-ColorOutput "Installing React dependencies..." "Info"
            npm install
            if ($LASTEXITCODE -ne 0) {
                Write-ColorOutput "✗ Failed to install dependencies" "Error"
                return $false
            }
        }
        
        # Build the app
        Write-ColorOutput "Building React app..." "Info"
        npm run build
        
        if ($LASTEXITCODE -eq 0) {
            Write-ColorOutput "✓ React app built successfully" "Success"
            
            # Copy build to theme
            $buildDir = Join-Path $reactDir "dist"
            $themeAssets = Join-Path $Config.ProjectRoot $Config.ThemeDir "assets"
            
            if (Test-Path $buildDir) {
                if (Test-Path $themeAssets) {
                    Remove-Item $themeAssets -Recurse -Force
                }
                Copy-Item $buildDir $themeAssets -Recurse -Force
                Write-ColorOutput "✓ Build copied to theme assets" "Success"
            }
            
            return $true
        } else {
            Write-ColorOutput "✗ Failed to build React app" "Error"
            return $false
        }
    } finally {
        Pop-Location
    }
}

function Test-Project {
    Write-ColorOutput "Running tests..." "Info"
    
    # Test WordPress
    Write-ColorOutput "Testing WordPress..." "Info"
    $wpTestResult = Test-WordPress
    if ($wpTestResult) {
        Write-ColorOutput "✓ WordPress tests passed" "Success"
    } else {
        Write-ColorOutput "✗ WordPress tests failed" "Error"
    }
    
    # Test React
    Write-ColorOutput "Testing React..." "Info"
    $reactTestResult = Test-React
    if ($reactTestResult) {
        Write-ColorOutput "✓ React tests passed" "Success"
    } else {
        Write-ColorOutput "✗ React tests failed" "Error"
    }
    
    return $wpTestResult -and $reactTestResult
}

function Test-WordPress {
    # Basic WordPress file structure test
    $wpFiles = @(
        "wp-config.php",
        "wp-content/themes/blackcnote/style.css",
        "wp-content/themes/blackcnote/functions.php"
    )
    
    foreach ($file in $wpFiles) {
        $filePath = Join-Path $Config.ProjectRoot $Config.WordPressDir $file
        if (-not (Test-Path $filePath)) {
            Write-ColorOutput "  ✗ Missing: $file" "Error"
            return $false
        }
    }
    
    return $true
}

function Test-React {
    $reactDir = Join-Path $Config.ProjectRoot $Config.ReactDir
    
    if (-not (Test-Path $reactDir)) {
        return $false
    }
    
    Push-Location $reactDir
    
    try {
        # Run React tests if available
        if (Test-Path "package.json") {
            $packageJson = Get-Content "package.json" | ConvertFrom-Json
            if ($packageJson.scripts.test) {
                npm test -- --watchAll=false
                return $LASTEXITCODE -eq 0
            }
        }
        
        # Basic structure test
        $reactFiles = @(
            "package.json",
            "src/App.tsx",
            "src/main.tsx"
        )
        
        foreach ($file in $reactFiles) {
            if (-not (Test-Path $file)) {
                return $false
            }
        }
        
        return $true
    } finally {
        Pop-Location
    }
}

function Deploy-ToProduction {
    Write-ColorOutput "Deploying to production..." "Info"
    
    # Check if we're on main branch
    $currentBranch = git branch --show-current
    if ($currentBranch -ne $Config.Branch) {
        Write-ColorOutput "✗ Deployment only allowed from $($Config.Branch) branch" "Error"
        exit 1
    }
    
    # Build React app
    if (-not (Build-ReactApp)) {
        Write-ColorOutput "✗ React build failed, deployment cancelled" "Error"
        exit 1
    }
    
    # Run tests
    if (-not (Test-Project)) {
        Write-ColorOutput "✗ Tests failed, deployment cancelled" "Error"
        exit 1
    }
    
    # Create deployment tag
    $tag = "deploy-$(Get-Date -Format 'yyyy-MM-dd-HH-mm-ss')"
    Write-ColorOutput "Creating deployment tag: $tag" "Info"
    
    git tag $tag
    if ($LASTEXITCODE -ne 0) {
        Write-ColorOutput "✗ Failed to create tag" "Error"
        exit 1
    }
    
    # Push tag
    git push origin $tag
    if ($LASTEXITCODE -ne 0) {
        Write-ColorOutput "✗ Failed to push tag" "Error"
        exit 1
    }
    
    Write-ColorOutput "✓ Deployment tag created: $tag" "Success"
    Write-ColorOutput "Deployment triggered successfully" "Success"
}

function Show-Help {
    Write-ColorOutput "BlackCnote GitHub Deployment Script" "Info"
    Write-ColorOutput "Usage: .\deploy-to-github.ps1 [Action] [Options]" "Info"
    Write-ColorOutput ""
    Write-ColorOutput "Actions:" "Info"
    Write-ColorOutput "  sync     - Sync changes with GitHub (default)" "Info"
    Write-ColorOutput "  pull     - Pull changes from GitHub" "Info"
    Write-ColorOutput "  build    - Build React app" "Info"
    Write-ColorOutput "  test     - Run tests" "Info"
    Write-ColorOutput "  deploy   - Deploy to production" "Info"
    Write-ColorOutput ""
    Write-ColorOutput "Options:" "Info"
    Write-ColorOutput "  -Message <string> - Commit message" "Info"
    Write-ColorOutput "  -Force            - Skip confirmations" "Info"
    Write-ColorOutput "  -Deploy           - Deploy to production" "Info"
    Write-ColorOutput "  -Build            - Build React app" "Info"
    Write-ColorOutput "  -Test             - Run tests" "Info"
    Write-ColorOutput ""
    Write-ColorOutput "Examples:" "Info"
    Write-ColorOutput "  .\deploy-to-github.ps1" "Info"
    Write-ColorOutput "  .\deploy-to-github.ps1 sync -Message 'Update styles'" "Info"
    Write-ColorOutput "  .\deploy-to-github.ps1 build" "Info"
    Write-ColorOutput "  .\deploy-to-github.ps1 deploy -Force" "Info"
}

# Main script execution
try {
    Write-ColorOutput "BlackCnote GitHub Deployment Script" "Info"
    Write-ColorOutput "=====================================" "Info"
    
    # Parse parameters
    if ($args -contains "-h" -or $args -contains "--help" -or $args -contains "-?") {
        Show-Help
        exit 0
    }
    
    # Test prerequisites
    Test-Prerequisites
    
    # Execute action
    switch ($Action.ToLower()) {
        "sync" {
            Sync-WithGitHub
        }
        "pull" {
            Pull-Changes
        }
        "build" {
            Build-ReactApp
        }
        "test" {
            Test-Project
        }
        "deploy" {
            Deploy-ToProduction
        }
        default {
            Write-ColorOutput "Unknown action: $Action" "Error"
            Show-Help
            exit 1
        }
    }
    
    Write-ColorOutput "Script completed successfully" "Success"
    
} catch {
    Write-ColorOutput "Script failed: $($_.Exception.Message)" "Error"
    exit 1
} 