# BlackCnote Complete Environment Setup Script
# This script sets up the entire development environment

Write-Host "BlackCnote Complete Environment Setup" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
Write-Host ""

# Check if running as administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")
if (-not $isAdmin) {
    Write-Host "This script requires administrator privileges. Please run as administrator." -ForegroundColor Red
    exit 1
}

# Function to check if a service exists
function Test-ServiceExists {
    param([string]$ServiceName)
    return (Get-Service -Name $ServiceName -ErrorAction SilentlyContinue) -ne $null
}

# Function to start service if it exists
function Start-ServiceIfExists {
    param([string]$ServiceName, [string]$DisplayName)
    if (Test-ServiceExists $ServiceName) {
        Write-Host "Starting $DisplayName..." -ForegroundColor Yellow
        try {
            Start-Service -Name $ServiceName
            Write-Host "✓ $DisplayName started successfully" -ForegroundColor Green
        } catch {
            Write-Host "✗ Failed to start $DisplayName: $($_.Exception.Message)" -ForegroundColor Red
        }
    } else {
        Write-Host "⚠ $DisplayName service not found" -ForegroundColor Yellow
    }
}

# Function to check if XAMPP is installed
function Test-XAMPPInstalled {
    $xamppPaths = @(
        "C:\xampp",
        "C:\Program Files\xampp",
        "C:\Program Files (x86)\xampp"
    )
    
    foreach ($path in $xamppPaths) {
        if (Test-Path $path) {
            return $path
        }
    }
    return $null
}

# Function to start XAMPP services
function Start-XAMPPServices {
    $xamppPath = Test-XAMPPInstalled
    if ($xamppPath) {
        Write-Host "XAMPP found at: $xamppPath" -ForegroundColor Green
        
        # Start Apache
        $apacheExe = Join-Path $xamppPath "apache\bin\httpd.exe"
        if (Test-Path $apacheExe) {
            Write-Host "Starting Apache..." -ForegroundColor Yellow
            try {
                Start-Process -FilePath $apacheExe -ArgumentList "-k start" -NoNewWindow -Wait
                Write-Host "✓ Apache started successfully" -ForegroundColor Green
            } catch {
                Write-Host "✗ Failed to start Apache: $($_.Exception.Message)" -ForegroundColor Red
            }
        }
        
        # Start MySQL
        $mysqlExe = Join-Path $xamppPath "mysql\bin\mysqld.exe"
        if (Test-Path $mysqlExe) {
            Write-Host "Starting MySQL..." -ForegroundColor Yellow
            try {
                Start-Process -FilePath $mysqlExe -ArgumentList "--console" -NoNewWindow
                Start-Sleep -Seconds 5
                Write-Host "✓ MySQL started successfully" -ForegroundColor Green
            } catch {
                Write-Host "✗ Failed to start MySQL: $($_.Exception.Message)" -ForegroundColor Red
            }
        }
    } else {
        Write-Host "XAMPP not found. Please install XAMPP first." -ForegroundColor Red
        return $false
    }
    return $true
}

# Function to create database and user
function Setup-Database {
    Write-Host "Setting up database..." -ForegroundColor Yellow
    
    # Create MySQL command file
    $sqlCommands = @"
CREATE DATABASE IF NOT EXISTS blackcnote CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'blackcnote_user'@'localhost' IDENTIFIED BY 'blackcnote_password';
GRANT ALL PRIVILEGES ON blackcnote.* TO 'blackcnote_user'@'localhost';
FLUSH PRIVILEGES;
"@
    
    $sqlFile = Join-Path $PSScriptRoot "setup_database.sql"
    $sqlCommands | Out-File -FilePath $sqlFile -Encoding UTF8
    
    # Execute MySQL commands
    $xamppPath = Test-XAMPPInstalled
    if ($xamppPath) {
        $mysqlExe = Join-Path $xamppPath "mysql\bin\mysql.exe"
        if (Test-Path $mysqlExe) {
            try {
                & $mysqlExe -u root -p -e "source $sqlFile"
                Write-Host "✓ Database setup completed" -ForegroundColor Green
            } catch {
                Write-Host "✗ Database setup failed: $($_.Exception.Message)" -ForegroundColor Red
            }
        }
    }
    
    # Clean up
    if (Test-Path $sqlFile) {
        Remove-Item $sqlFile
    }
}

# Function to update WordPress configuration
function Update-WordPressConfig {
    Write-Host "Updating WordPress configuration..." -ForegroundColor Yellow
    
    $wpConfigPath = Join-Path $PSScriptRoot "..\blackcnote\wp-config.php"
    if (Test-Path $wpConfigPath) {
        $content = Get-Content $wpConfigPath -Raw
        
        # Update database settings
        $content = $content -replace "define\(\s*'DB_NAME',\s*'[^']*'\s*\);", "define( 'DB_NAME', 'blackcnote' );"
        $content = $content -replace "define\(\s*'DB_USER',\s*'[^']*'\s*\);", "define( 'DB_USER', 'blackcnote_user' );"
        $content = $content -replace "define\(\s*'DB_PASSWORD',\s*'[^']*'\s*\);", "define( 'DB_PASSWORD', 'blackcnote_password' );"
        $content = $content -replace "define\(\s*'DB_HOST',\s*'[^']*'\s*\);", "define( 'DB_HOST', 'localhost' );"
        
        # Enable debug mode
        $content = $content -replace "define\(\s*'WP_DEBUG',\s*false\s*\);", "define( 'WP_DEBUG', true );"
        $content = $content -replace "define\(\s*'WP_DEBUG_LOG',\s*false\s*\);", "define( 'WP_DEBUG_LOG', true );"
        $content = $content -replace "define\(\s*'WP_DEBUG_DISPLAY',\s*true\s*\);", "define( 'WP_DEBUG_DISPLAY', false );"
        
        Set-Content -Path $wpConfigPath -Value $content -Encoding UTF8
        Write-Host "✓ WordPress configuration updated" -ForegroundColor Green
    } else {
        Write-Host "✗ WordPress configuration file not found" -ForegroundColor Red
    }
}

# Function to create logs directory
function Create-LogsDirectory {
    Write-Host "Creating logs directory..." -ForegroundColor Yellow
    
    $logsPath = Join-Path $PSScriptRoot "..\blackcnote\wp-content\logs"
    if (-not (Test-Path $logsPath)) {
        New-Item -ItemType Directory -Path $logsPath -Force | Out-Null
        Write-Host "✓ Logs directory created" -ForegroundColor Green
    } else {
        Write-Host "✓ Logs directory already exists" -ForegroundColor Green
    }
}

# Function to test database connection
function Test-DatabaseConnection {
    Write-Host "Testing database connection..." -ForegroundColor Yellow
    
    $testScript = @"
<?php
try {
    `$pdo = new PDO('mysql:host=localhost;dbname=blackcnote', 'blackcnote_user', 'blackcnote_password');
    echo "Database connection successful\n";
} catch (PDOException `$e) {
    echo "Database connection failed: " . `$e->getMessage() . "\n";
}
?>
"@
    
    $testFile = Join-Path $PSScriptRoot "test_db.php"
    $testScript | Out-File -FilePath $testFile -Encoding UTF8
    
    try {
        $result = & php $testFile
        Write-Host $result -ForegroundColor Green
    } catch {
        Write-Host "✗ Database test failed: $($_.Exception.Message)" -ForegroundColor Red
    }
    
    # Clean up
    if (Test-Path $testFile) {
        Remove-Item $testFile
    }
}

# Main execution
Write-Host "Step 1: Starting XAMPP services..." -ForegroundColor Cyan
Start-XAMPPServices

Write-Host "`nStep 2: Setting up database..." -ForegroundColor Cyan
Setup-Database

Write-Host "`nStep 3: Updating WordPress configuration..." -ForegroundColor Cyan
Update-WordPressConfig

Write-Host "`nStep 4: Creating logs directory..." -ForegroundColor Cyan
Create-LogsDirectory

Write-Host "`nStep 5: Testing database connection..." -ForegroundColor Cyan
Test-DatabaseConnection

Write-Host "`nEnvironment setup completed!" -ForegroundColor Green
Write-Host "You can now test the BlackCnote Debug Plugin." -ForegroundColor Green 