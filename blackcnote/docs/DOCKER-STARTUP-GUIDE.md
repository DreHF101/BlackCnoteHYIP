# BlackCnote Docker Startup Guide

## Overview

This guide covers the enhanced Docker startup process for BlackCnote, including troubleshooting and best practices.

## Quick Start

### Option 1: Batch File (Recommended)
```bash
# Run the batch file (no notepad popup)
automate-docker-startup.bat
```

### Option 2: PowerShell Script
```bash
# Run the PowerShell script directly
powershell -ExecutionPolicy Bypass -File scripts\automate-docker-startup.ps1
```

## Enhanced Features

### Version 2.0.0 Improvements

1. **Docker Settings Configuration**
   - Automatic Docker Desktop settings configuration
   - Optimized daemon configuration
   - Enhanced performance settings

2. **Error Handling**
   - Fixed concurrent map writes error
   - Retry logic for service startup
   - Graceful process management

3. **No Notepad Popup**
   - Batch file wrapper prevents notepad popup
   - Proper PowerShell execution

4. **Comprehensive Diagnostics**
   - System resource monitoring
   - Network connectivity checks
   - WSL2 status verification

## Script Parameters

### PowerShell Script Options
```powershell
# Basic startup
.\scripts\automate-docker-startup.ps1

# Force restart Docker Desktop
.\scripts\automate-docker-startup.ps1 -ForceRestart

# Reset WSL2 (use with caution)
.\scripts\automate-docker-startup.ps1 -ResetWSL

# Quiet mode (minimal output)
.\scripts\automate-docker-startup.ps1 -Quiet

# Verbose mode (detailed output)
.\scripts\automate-docker-startup.ps1 -Verbose
```

### Batch File Options
```bash
# Basic startup
automate-docker-startup.bat

# With PowerShell parameters
automate-docker-startup.bat -ForceRestart
automate-docker-startup.bat -ResetWSL
```

## Troubleshooting

### Common Issues

#### 1. Concurrent Map Writes Error
**Symptoms**: `fatal error: concurrent map writes`
**Solution**: The enhanced script automatically handles this by:
- Using `docker compose` instead of `docker-compose`
- Cleaning Docker system before startup
- Implementing retry logic

#### 2. Docker Desktop Not Starting
**Symptoms**: Docker processes not found
**Solution**:
```bash
# Run with force restart
automate-docker-startup.bat -ForceRestart
```

#### 3. WSL2 Issues
**Symptoms**: WSL not responding or Docker engine not connecting
**Solution**:
```bash
# Reset WSL2 (use with caution)
automate-docker-startup.bat -ResetWSL
```

### Diagnostic Tools

#### Docker Troubleshooting Script
```powershell
# Run comprehensive diagnostics
.\scripts\docker-troubleshoot.ps1

# Run diagnostics with automatic fixes
.\scripts\docker-troubleshoot.ps1 -FixAll

# Verbose diagnostic report
.\scripts\docker-troubleshoot.ps1 -Verbose
```

#### Manual Checks
```powershell
# Check Docker status
docker info

# Check WSL2 status
wsl --status

# Check Docker processes
Get-Process -Name "*docker*"
```

## Service URLs

After successful startup, services are available at:

- **WordPress**: http://localhost:8888
- **WordPress Admin**: http://localhost:8888/wp-admin
- **React App**: http://localhost:5174
- **phpMyAdmin**: http://localhost:8080
- **Redis Commander**: http://localhost:8081
- **MailHog**: http://localhost:8025
- **Metrics**: http://localhost:9091
- **Health Check**: http://localhost:8888/health

## Docker Configuration

### Automatic Settings

The script automatically configures:

1. **Docker Desktop Settings** (`%APPDATA%\Docker\settings.json`)
   - BuildKit enabled
   - Kubernetes disabled
   - Docker Compose V2 enabled
   - Optimized concurrent operations

2. **Docker Daemon Config** (`%APPDATA%\Docker\daemon.json`)
   - Overlay2 storage driver
   - JSON file logging
   - Optimized memory and CPU settings
   - DNS configuration

### Manual Configuration

If you need to manually configure Docker:

1. **Docker Desktop Settings**
   - Open Docker Desktop
   - Go to Settings > General
   - Enable "Use the WSL 2 based engine"
   - Go to Settings > Resources > WSL Integration
   - Enable integration with your WSL distro

2. **WSL2 Configuration**
   ```bash
   # Set WSL2 as default
   wsl --set-default-version 2
   
   # Update WSL
   wsl --update
   ```

## Performance Optimization

### System Requirements
- **RAM**: Minimum 4GB, Recommended 8GB+
- **Disk Space**: Minimum 20GB free space
- **Windows**: Windows 10/11 Pro, Enterprise, or Education
- **Virtualization**: Enabled in BIOS

### Performance Tips
1. **Close unnecessary applications** before starting Docker
2. **Run as Administrator** for best performance
3. **Allocate sufficient resources** in Docker Desktop settings
4. **Use SSD storage** for better I/O performance

## Logs and Debugging

### Docker Logs
```bash
# View all container logs
docker compose logs

# View specific service logs
docker compose logs wordpress
docker compose logs mysql
docker compose logs redis

# Follow logs in real-time
docker compose logs -f
```

### System Logs
```bash
# Windows Event Viewer
eventvwr.msc

# Docker Desktop logs
%APPDATA%\Docker\log.txt
```

### Debug Mode
```powershell
# Run startup with verbose logging
.\scripts\automate-docker-startup.ps1 -Verbose
```

## Troubleshooting Checklist

### Before Running Script
- [ ] Docker Desktop installed and updated
- [ ] WSL2 enabled and updated
- [ ] Virtualization enabled in BIOS
- [ ] Sufficient system resources available
- [ ] Running as Administrator (recommended)

### If Startup Fails
- [ ] Check Docker Desktop is running
- [ ] Verify WSL2 status
- [ ] Check system resources
- [ ] Review error logs
- [ ] Try force restart option
- [ ] Run diagnostic script

### If Services Don't Start
- [ ] Check Docker engine is responding
- [ ] Verify network connectivity
- [ ] Check port availability
- [ ] Review container logs
- [ ] Try resetting WSL2

## Support

### Getting Help
1. Run the diagnostic script: `.\scripts\docker-troubleshoot.ps1 -Verbose`
2. Check the logs: `docker compose logs`
3. Review this guide for common solutions
4. Check Windows Event Viewer for system errors

### Common Commands
```bash
# Stop all services
docker compose down

# Remove all containers and volumes
docker compose down -v

# Clean Docker system
docker system prune -a

# Restart Docker Desktop
Stop-Process -Name "*docker*" -Force
Start-Process "${env:ProgramFiles}\Docker\Docker\Docker Desktop.exe"
```

## Version History

### v2.0.0 (Current)
- Enhanced Docker settings configuration
- Fixed concurrent map writes error
- Added retry logic for service startup
- Improved error handling and diagnostics
- Removed notepad popup issue
- Added comprehensive troubleshooting tools

### v1.0.0
- Basic Docker Desktop automation
- WSL2 integration
- Service startup and monitoring 