@echo off
echo ========================================
echo BlackCnote WordPress Fix Verification
echo ========================================
echo.

echo [1/6] Checking container status...
docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo.
echo [2/6] Testing WordPress accessibility...
docker exec blackcnote-wordpress curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost

echo.
echo [3/6] Checking wp-config.php file...
docker exec blackcnote-wordpress ls -la /var/www/html/wp-config.php

echo.
echo [4/6] Testing database connection...
docker exec blackcnote-mysql mysql -u root -pblackcnote_password -e "USE blackcnote; SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema='blackcnote';"

echo.
echo [5/6] Checking WordPress tables...
docker exec blackcnote-mysql mysql -u root -pblackcnote_password -e "USE blackcnote; SHOW TABLES;" | findstr "wp_"

echo.
echo [6/6] Checking debug log...
docker exec blackcnote-wordpress cat /var/www/html/wp-content/debug.log

echo.
echo ========================================
echo Verification Complete!
echo ========================================
echo.
echo Expected Results:
echo - WordPress should return HTTP 301 (redirect)
echo - wp-config.php should be a file (not directory)
echo - Database should show 15+ tables
echo - Debug log should be empty or show no errors
echo.
pause 