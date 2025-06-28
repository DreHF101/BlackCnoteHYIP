# BlackCnote Windows Startup System

## Overview

The BlackCnote Windows Startup System provides comprehensive automation for starting all BlackCnote services on Windows, including WSL2, Docker, React, and WordPress. This system ensures that all components are properly initialized and ready for development.

## 🚀 Quick Start

### Option 1: Batch File (Recommended)
```bash
# Right-click and "Run as administrator"
start-blackcnote.bat
```

### Option 2: PowerShell Script
```powershell
# Run as Administrator
.\start-blackcnote-complete.ps1
```

### Option 3: Manual Docker Compose
```bash
docker-compose up -d --build
```

## 📁 Startup Scripts

### 1. `start-blackcnote-complete.ps1`
**Main PowerShell startup script** that integrates all components:

- **WSL2 Setup**: Enables and configures Windows Subsystem for Linux
- **Docker Management**: Starts Docker Desktop and verifies readiness
- **React Build**: Installs dependencies and builds React application
- **Service Startup**: Launches all Docker Compose services
- **Health Monitoring**: Verifies all services are running

**Parameters:**
- `-SkipWSL2`: Skip WSL2 setup and configuration
- `-SkipDocker`: Skip Docker Desktop startup
- `-SkipReact`: Skip React app build process
- `-ForceRebuild`: Force clean rebuild of all components
- `-Quiet`: Suppress verbose output

### 2. `start-blackcnote.bat`
**Windows batch file** that launches the PowerShell script with administrator privileges:

- Automatic administrator privilege detection
- User-friendly interface
- Browser launch option
- Error handling and status reporting

### 3. `start-blackcnote.sh`
**WSL2/Linux startup script** for running BlackCnote in WSL2 environment:

- Optimized for WSL2 performance
- File synchronization between Windows and WSL2
- Linux-specific optimizations

## 🔧 Service Integration

### WSL2 Integration
```powershell
# Enable WSL2 features
Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux
Enable-WindowsOptionalFeature -Online -FeatureName VirtualMachinePlatform

# Set WSL2 as default
wsl --set-default-version 2

# Install Ubuntu if needed
wsl --install -d Ubuntu --no-launch
```

### Docker Integration
```powershell
# Start Docker Desktop
Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized

# Wait for Docker readiness
docker info
```

### React Integration
```powershell
# Install dependencies
npm install --silent

# Build application
npm run build --silent

# Copy to WordPress theme
Copy-Item -Path "dist\*" -Destination "blackcnote\wp-content\themes\blackcnote\dist" -Recurse -Force
```

## 🌐 Service URLs

| Service | URL | Port | Description |
|---------|-----|------|-------------|
| **WordPress Frontend** | http://localhost:8888 | 8888 | Main WordPress site |
| **WordPress Admin** | http://localhost:8888/wp-admin | 8888 | WordPress administration |
| **React Development** | http://localhost:5174 | 5174 | React app with hot reload |
| **phpMyAdmin** | http://localhost:8080 | 8080 | Database management |
| **Metrics Exporter** | http://localhost:9091 | 9091 | Prometheus metrics |
| **Health Check** | http://localhost:8888/health | 8888 | Service health status |

## 🔄 Auto-Start Configuration

### Windows Task Scheduler Setup
```powershell
# Create auto-start task (run as Administrator)
$action = New-ScheduledTaskAction -Execute "PowerShell.exe" -Argument "-ExecutionPolicy Bypass -File `"$PSScriptRoot\start-blackcnote-complete.ps1`" -Quiet"
$trigger = New-ScheduledTaskTrigger -AtStartup
$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable
$principal = New-ScheduledTaskPrincipal -UserId "$env:USERDOMAIN\$env:USERNAME" -LogonType Interactive -RunLevel Highest

Register-ScheduledTask -TaskName "BlackCnote Auto-Start" -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Description "Automatically starts BlackCnote services on Windows boot" -Force
```

### Startup Folder Method
```powershell
# Create shortcut in Windows startup folder
$startupFolder = "$env:APPDATA\Microsoft\Windows\Start Menu\Programs\Startup"
$shortcutPath = Join-Path $startupFolder "BlackCnote.lnk"
$targetPath = Join-Path $PSScriptRoot "start-blackcnote.bat"

$WshShell = New-Object -comObject WScript.Shell
$Shortcut = $WshShell.CreateShortcut($shortcutPath)
$Shortcut.TargetPath = $targetPath
$Shortcut.WorkingDirectory = $PSScriptRoot
$Shortcut.Save()
```

## 🛠️ Troubleshooting

### Common Issues

#### 1. Port Already in Use
```powershell
# Check what's using a port
netstat -ano | findstr :8888

# Kill process using port
Stop-Process -Id <PID> -Force
```

#### 2. Docker Not Starting
```powershell
# Check Docker Desktop status
Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue

# Start Docker Desktop manually
Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe"
```

#### 3. WSL2 Issues
```powershell
# Restart WSL2
wsl --shutdown
wsl -d Ubuntu -e echo "WSL2 restarted"

# Update WSL2 kernel
wsl --update
```

#### 4. React Build Failures
```powershell
# Clean React app
Remove-Item -Recurse -Force react-app\node_modules, react-app\package-lock.json, react-app\dist, react-app\.vite

# Reinstall dependencies
cd react-app
npm install
npm run build
```

### Debug Mode
```powershell
# Run with verbose output
.\start-blackcnote-complete.ps1 -Verbose

# Check service status
docker-compose ps
docker-compose logs wordpress
docker-compose logs react-app
```

## 📊 Performance Optimization

### Docker Optimizations
```yaml
# docker-compose.yml optimizations
services:
  wordpress:
    volumes:
      - ./blackcnote:/var/www/html:cached  # Use cached mount for better performance
    environment:
      WP_MEMORY_LIMIT: 256M
      WP_MAX_MEMORY_LIMIT: 512M

  mysql:
    environment:
      MYSQL_INNODB_BUFFER_POOL_SIZE: 512M
      MYSQL_MAX_CONNECTIONS: 100

  redis:
    command: >
      redis-server
      --maxmemory 256mb
      --maxmemory-policy allkeys-lru
```

### WSL2 Performance
```bash
# .wslconfig optimizations
[wsl2]
memory=4GB
processors=4
swap=0
localhostForwarding=true
```

## 🔒 Security Considerations

### Administrator Privileges
- All startup scripts require administrator privileges for full functionality
- WSL2 and Docker features require elevated permissions
- Task Scheduler tasks run with highest privileges

### Network Security
- Services are bound to localhost only
- No external network access by default
- Firewall rules may need adjustment for Docker

### File Permissions
```powershell
# Set proper permissions for WordPress files
icacls "blackcnote\wp-content" /grant "Everyone:(OI)(CI)F" /T
icacls "logs" /grant "Everyone:(OI)(CI)F" /T
```

## 📝 Logging and Monitoring

### Log Locations
- **WordPress Logs**: `logs/wordpress/`
- **MySQL Logs**: `logs/mysql/`
- **Redis Logs**: `logs/redis/`
- **Docker Logs**: `docker-compose logs <service>`

### Health Monitoring
```powershell
# Check all services
$services = @(
    @{Name="WordPress"; Url="http://localhost:8888"},
    @{Name="React App"; Url="http://localhost:5174"},
    @{Name="phpMyAdmin"; Url="http://localhost:8080"}
)

foreach ($service in $services) {
    try {
        $response = Invoke-WebRequest -Uri $service.Url -TimeoutSec 5
        Write-Host "[$($service.Name)] ✅ Ready" -ForegroundColor Green
    }
    catch {
        Write-Host "[$($service.Name)] ❌ Not responding" -ForegroundColor Red
    }
}
```

## 🚀 Deployment

### Production Deployment
```powershell
# Production build
.\start-blackcnote-complete.ps1 -ForceRebuild -Quiet

# Use production Docker Compose
docker-compose -f docker-compose.prod.yml up -d
```

### Development Deployment
```powershell
# Development mode with hot reload
.\start-blackcnote-complete.ps1 -SkipReact
cd react-app
npm run dev
```

## 📚 Additional Resources

- **Docker Documentation**: https://docs.docker.com/
- **WSL2 Documentation**: https://docs.microsoft.com/en-us/windows/wsl/
- **WordPress Development**: https://developer.wordpress.org/
- **React Development**: https://react.dev/

## 🤝 Support

For issues and questions:
1. Check the troubleshooting section above
2. Review Docker and WSL2 logs
3. Verify all prerequisites are installed
4. Ensure administrator privileges are available

---

**Last Updated**: June 27, 2025  
**Version**: 1.0.0  
**Compatibility**: Windows 10/11 with WSL2 and Docker Desktop 