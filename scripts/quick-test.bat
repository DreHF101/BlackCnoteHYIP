@echo off
echo Testing BlackCnote Fixes...
echo.

echo 1. Checking CORS plugin...
if exist "blackcnote\wp-content\plugins\blackcnote-cors\blackcnote-cors.php" (
    echo    [OK] CORS plugin file exists
) else (
    echo    [ERROR] CORS plugin file missing
)

echo.
echo 2. Checking React container in header...
findstr /C:"id=\"root\" class=\"blackcnote-react-app\"" "blackcnote\wp-content\themes\blackcnote\header.php" >nul
if %errorlevel% equ 0 (
    echo    [OK] React container found in header
) else (
    echo    [ERROR] React container not found in header
)

echo.
echo 3. Checking React build files...
if exist "blackcnote\wp-content\themes\blackcnote\dist\*" (
    echo    [OK] React build files exist
) else (
    echo    [ERROR] React build files missing
)

echo.
echo 4. Testing WordPress accessibility...
curl -s -o nul -w "HTTP Status: %%{http_code}" http://localhost:8888
echo.

echo.
echo 5. Testing React dev server...
curl -s -o nul -w "HTTP Status: %%{http_code}" http://localhost:5174
echo.

echo.
echo Test completed!
pause 