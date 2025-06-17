# WordPress Local Environment Setup Script

# Set error action preference
$ErrorActionPreference = "Stop"

# Define paths
$rootDir = Get-Location
$wpDir = Join-Path $rootDir "wordpress"
$wpContentDir = Join-Path $wpDir "wp-content"
$themesDir = Join-Path $wpContentDir "themes"
$blackcnoteThemeDir = Join-Path $themesDir "blackcnote-theme"

# Create WordPress directory if it doesn't exist
if (-not (Test-Path $wpDir)) {
    New-Item -ItemType Directory -Path $wpDir | Out-Null
    Write-Host "Created WordPress directory"
}

# Download WordPress
$wpZip = Join-Path $rootDir "wordpress.zip"
if (-not (Test-Path $wpZip)) {
    Write-Host "Downloading WordPress..."
    Invoke-WebRequest -Uri "https://wordpress.org/latest.zip" -OutFile $wpZip
}

# Extract WordPress
Write-Host "Extracting WordPress..."
Expand-Archive -Path $wpZip -DestinationPath $rootDir -Force

# Create wp-config.php
$configContent = @"
<?php
define('DB_NAME', 'blackcnote_local');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

\$table_prefix = 'wp_';

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
"@

Set-Content -Path (Join-Path $wpDir "wp-config.php") -Value $configContent

# Install BlackCnote Theme
Write-Host "Installing BlackCnote Theme..."
if (-not (Test-Path $blackcnoteThemeDir)) {
    New-Item -ItemType Directory -Path $blackcnoteThemeDir | Out-Null
}

# Copy theme files
Get-ChildItem -Path $rootDir -Exclude @("wordpress", "wordpress.zip", "setup-local.ps1", "*.zip") | 
    Copy-Item -Destination $blackcnoteThemeDir -Recurse -Force

# Create .htaccess
$htaccessContent = @"
# BEGIN WordPress
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
# END WordPress
"@

Set-Content -Path (Join-Path $wpDir ".htaccess") -Value $htaccessContent

Write-Host "Local WordPress environment setup complete!"
Write-Host "Next steps:"
Write-Host "1. Create a MySQL database named 'blackcnote_local'"
Write-Host "2. Start your local web server (Apache/XAMPP)"
Write-Host "3. Visit http://localhost/wordpress to complete the installation"
Write-Host "4. Activate the BlackCnote Theme in WordPress admin" 