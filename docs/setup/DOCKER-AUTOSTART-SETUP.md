# Docker Autostart Setup for BlackCnote

## Overview
This document describes the automated Docker startup configuration for the BlackCnote project.

## Files Created

### 1. Project Root Batch File
- **File**: `start-docker-env.bat`
- **Location**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\start-docker-env.bat`
- **Purpose**: Manual startup script for the Docker environment

### 2. Windows Startup Script
- **File**: `BlackCnote-Docker-Startup.bat`
- **Location**: `C:\Users\CASH AMERICA PAWN\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\`
- **Purpose**: Automatic startup when Windows boots

## What the Script Does

1. **Docker Status Check**: Verifies Docker Desktop is running
2. **Directory Navigation**: Changes to the Docker Compose directory
3. **Clean Shutdown**: Stops any existing containers
4. **Service Startup**: Starts all Docker services in detached mode
5. **Status Verification**: Displays running service status
6. **Information Display**: Shows available service URLs

## Services Started

- **WordPress**: http://localhost:8888
- **React App**: http://localhost:5174
- **PHPMyAdmin**: http://localhost:8080
- **Redis Commander**: http://localhost:8081
- **MailHog**: http://localhost:8025

## Usage

### Manual Startup
```bash
# From project root
.\start-docker-env.bat
```

### Automatic Startup
The script will automatically run when Windows starts up.

## Verification

To verify the startup script is properly configured:

```powershell
# Check if startup script exists
Test-Path "$env:APPDATA\Microsoft\Windows\Start Menu\Programs\Startup\BlackCnote-Docker-Startup.bat"

# View startup script details
Get-ChildItem "$env:APPDATA\Microsoft\Windows\Start Menu\Programs\Startup\BlackCnote-Docker-Startup.bat"
```

## Troubleshooting

### Docker Not Running
If you see "Docker is not running" error:
1. Start Docker Desktop manually
2. Wait for Docker to fully initialize
3. Run the startup script again

### Permission Issues
If you encounter permission errors:
1. Run PowerShell as Administrator
2. Re-copy the startup script to the startup folder

### Service Startup Failures
If services fail to start:
1. Check Docker Desktop status
2. Ensure no other services are using the required ports
3. Run `docker-compose logs` to view error details

## Manual Commands

### Start Services
```bash
cd config/docker
docker-compose up -d
```

### Stop Services
```bash
cd config/docker
docker-compose down
```

### View Logs
```bash
cd config/docker
docker-compose logs -f
```

### Check Status
```bash
cd config/docker
docker-compose ps
```

## Configuration Details

- **Docker Compose File**: `config/docker/docker-compose.yml`
- **Environment File**: `config/docker/.env`
- **Startup Delay**: 10 seconds for service initialization
- **Display Time**: 5 seconds to show results

## Security Notes

- The startup script runs with current user permissions
- No elevated privileges required for normal operation
- Docker Desktop must be installed and configured
- Windows startup folder location is user-specific

## Maintenance

### Update Startup Script
To update the startup script:
1. Modify `start-docker-env.bat` in project root
2. Copy the updated file to the startup folder:
   ```powershell
   Copy-Item "start-docker-env.bat" "$env:APPDATA\Microsoft\Windows\Start Menu\Programs\Startup\BlackCnote-Docker-Startup.bat" -Force
   ```

### Remove from Startup
To remove automatic startup:
```powershell
Remove-Item "$env:APPDATA\Microsoft\Windows\Start Menu\Programs\Startup\BlackCnote-Docker-Startup.bat"
```

## Created
- **Date**: 2025-06-25
- **Version**: 1.0
- **Status**: Active and configured 