@echo off
echo Creating BlackCnote Theme Package...
echo.

REM Remove existing zip file if it exists
if exist "BlackCnote-Theme-Complete.zip" del "BlackCnote-Theme-Complete.zip"

REM Create zip file using PowerShell
powershell -Command "Add-Type -AssemblyName System.IO.Compression.FileSystem; [System.IO.Compression.ZipFile]::CreateFromDirectory('blackcnote\wp-content\themes\blackcnote', 'BlackCnote-Theme-Complete.zip')"

if exist "BlackCnote-Theme-Complete.zip" (
    echo.
    echo ✅ BlackCnote Theme Package Created Successfully!
    echo 📦 File: BlackCnote-Theme-Complete.zip
    for %%A in ("BlackCnote-Theme-Complete.zip") do echo 📏 Size: %%~zA bytes
) else (
    echo ❌ Failed to create zip file
)

echo.
pause 