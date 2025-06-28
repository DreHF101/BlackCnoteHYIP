@echo off
REM BlackCnote Theme Packaging Script
REM ================================================
REM CANONICAL PATHWAYS - DO NOT CHANGE
REM Project Root: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
REM Theme Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote
REM WP-Content Directory: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
REM Theme Files: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote
REM ================================================
echo Creating BlackCnote Theme Package...
echo Using correct pathway: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote
echo.

REM Remove existing zip file if it exists
if exist "BlackCnote-Theme-Complete.zip" del "BlackCnote-Theme-Complete.zip"

REM Create zip file using PowerShell with correct path
powershell -Command "Add-Type -AssemblyName System.IO.Compression.FileSystem; [System.IO.Compression.ZipFile]::CreateFromDirectory('C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote', 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\BlackCnote-Theme-Complete.zip')"

if exist "BlackCnote-Theme-Complete.zip" (
    echo.
    echo ✅ BlackCnote Theme Package Created Successfully!
    echo 📦 File: BlackCnote-Theme-Complete.zip
    echo 📍 Location: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\
    for %%A in ("BlackCnote-Theme-Complete.zip") do echo 📏 Size: %%~zA bytes
) else (
    echo ❌ Failed to create zip file
    echo Please check the file path and permissions
)

echo.
pause 