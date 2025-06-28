@echo off
echo Creating HYIPLab Plugin Zip...
echo.

REM Run the PowerShell script
powershell -ExecutionPolicy Bypass -File "scripts\create-hyiplab-plugin-zip.ps1"

echo.
echo Press any key to exit...
pause >nul
