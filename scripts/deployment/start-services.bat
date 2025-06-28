@echo off
echo BlackCnote Complete Debug Test with Services
echo ===========================================
echo.

echo Step 1: Starting XAMPP services...
cd /d "C:\xampp"
if exist "xampp-control.exe" (
    echo Starting XAMPP Control Panel...
    start "" "xampp-control.exe"
    timeout /t 5 /nobreak >nul
    
    echo Starting Apache...
    "C:\xampp\apache_start.bat"
    timeout /t 3 /nobreak >nul
    
    echo Starting MySQL...
    "C:\xampp\mysql_start.bat"
    timeout /t 5 /nobreak >nul
    
    echo ✓ XAMPP services started
) else (
    echo ✗ XAMPP not found at C:\xampp
    echo Please install XAMPP first
    pause
    exit /b 1
)

echo.
echo Step 2: Setting up database...
cd /d "C:\xampp\mysql\bin"
if exist "mysql.exe" (
    echo Creating BlackCnote database...
    mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS blackcnote CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql.exe -u root -e "CREATE USER IF NOT EXISTS 'blackcnote_user'@'localhost' IDENTIFIED BY 'blackcnote_password';"
    mysql.exe -u root -e "GRANT ALL PRIVILEGES ON blackcnote.* TO 'blackcnote_user'@'localhost';"
    mysql.exe -u root -e "FLUSH PRIVILEGES;"
    echo ✓ Database setup completed
) else (
    echo ✗ MySQL not found
    pause
    exit /b 1
)

echo.
echo Step 3: Testing database connection...
cd /d "%~dp0.."
php -r "
try {
    `$pdo = new PDO('mysql:host=localhost;dbname=blackcnote', 'blackcnote_user', 'blackcnote_password');
    echo '✓ Database connection successful\n';
} catch (PDOException `$e) {
    echo '✗ Database connection failed: ' . `$e->getMessage() . '\n';
    exit(1);
}
"

echo.
echo Step 4: Running comprehensive debug test...
php scripts\simple-debug-test.php

echo.
echo Step 5: Testing WordPress connection...
php scripts\test-wp-connection.php

echo.
echo Step 6: Testing debug plugin activation...
php hyiplab\tools\activate-debug-system.php

echo.
echo Step 7: Running security audit...
php scripts\standalone-security-audit.php

echo.
echo ========================================
echo Complete Debug Test Finished!
echo Check the results above for any issues.
echo ========================================
pause 