@echo off
echo Running BlackCnote React Integration Test...
powershell -ExecutionPolicy Bypass -File "%~dp0test-react-integration.ps1"
pause 