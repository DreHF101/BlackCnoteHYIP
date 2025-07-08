# BlackCnote Unified Startup System

## Overview

The BlackCnote Unified Startup System is a comprehensive solution that addresses all previous startup issues and provides a single, reliable way to start all BlackCnote services. This system replaces all conflicting startup scripts with a unified approach that ensures proper Docker engine management, React build processes, and service coordination.

## 🚨 **CRITICAL - REPLACES ALL PREVIOUS STARTUP SCRIPTS**

**The unified startup system replaces ALL previous startup scripts to eliminate conflicts and ensure reliable operation.**

---

## **🔧 What Was Fixed**

### **Previous Issues Resolved**

1. **Multiple Terminals Opening**
   - **Problem**: Multiple conflicting startup scripts running simultaneously
   - **Solution**: Single unified script with proper process management

2. **Notepad Opening Instead of Execution**
   - **Problem**: File association issues with startup scripts
   - **Solution**: Proper batch file wrapper with administrator privileges

3. **Docker Engine Issues**
   - **Problem**: Inconsistent Docker Desktop management
   - **Solution**: Enhanced Docker startup with proper daemon waiting

4. **React Build Failures**
   - **Problem**: Inconsistent build process and file copying
   - **Solution**: Robust React build with error handling and verification

5. **Service Coordination Issues**
   - **Problem**: Services starting in wrong order or failing
   - **Solution**: Proper service dependency management and health checks

---

## **📁 New Unified Startup Files**

### **Primary Startup Scripts**

| File | Purpose | Usage |
|------|---------|-------|
| `start-blackcnote-unified.ps1` | Main PowerShell startup script | Advanced users, automation |
| `start-blackcnote-unified.bat` | Windows batch wrapper | Simple startup, right-click execution |
| `cleanup-startup-scripts.ps1` | Cleanup conflicting scripts | One-time cleanup |
| `test-unified-startup.ps1` | Comprehensive testing | Verify system functionality |

### **Canonical Paths**

```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\
├── start-blackcnote-unified.ps1          # Main PowerShell script
├── start-blackcnote-unified.bat          # Batch wrapper
├── cleanup-startup-scripts.ps1           # Cleanup script
├── test-unified-startup.ps1              # Test script
└── docker-compose.yml                    # Docker configuration
```

---

## **🚀 Quick Start**

### **Option 1: Simple Startup (Recommended)**
```bash
# Right-click and "Run as administrator"
start-blackcnote-unified.bat
```

### **Option 2: PowerShell Startup**
```powershell
# Run as Administrator
.\start-blackcnote-unified.ps1
```

### **Option 3: Advanced Options**
```powershell
# Skip React build
.\start-blackcnote-unified.ps1 -SkipReact

# Force rebuild everything
.\start-blackcnote-unified.ps1 -ForceRebuild

# Quiet mode (no browser launch)
.\start-blackcnote-unified.ps1 -Quiet -NoBrowser

# Debug mode
.\start-blackcnote-unified.ps1 -Debug
```

---

## **🔧 System Requirements**

### **Prerequisites**
- Windows 10/11 with WSL2
- Docker Desktop (latest version)
- Administrator privileges
- Node.js 18+ (for React builds)

### **Canonical Service URLs**
- **WordPress**: `http://localhost:8888`
- **WordPress Admin**: `http://localhost:8888/wp-admin`
- **React App**: `http://localhost:5174`
- **phpMyAdmin**: `http://localhost:8080`
- **Redis Commander**: `http://localhost:8081`
- **MailHog**: `http://localhost:8025`
- **Metrics**: `http://localhost:9091`
- **Health Check**: `http://localhost:8888/health`

---

## **🛠️ Installation and Setup**

### **Step 1: Clean Up Old Scripts**
```powershell
# Run as Administrator
.\cleanup-startup-scripts.ps1 -Backup
```

### **Step 2: Test the System**
```powershell
# Run as Administrator
.\test-unified-startup.ps1 -FullTest
```

### **Step 3: Start BlackCnote**
```bash
# Right-click and "Run as administrator"
start-blackcnote-unified.bat
```

---

## **📋 Startup Process**

### **Phase 1: System Preparation**
1. **Administrator Check**: Verify elevated privileges
2. **Project Root**: Set canonical project directory
3. **WSL2 Setup**: Enable and configure WSL2 (if needed)
4. **Port Cleanup**: Free all required ports

### **Phase 2: Docker Management**
1. **Docker Desktop**: Start with enhanced configuration
2. **Daemon Wait**: Wait for Docker daemon to be ready
3. **Container Cleanup**: Stop existing containers
4. **Service Startup**: Start all Docker services

### **Phase 3: React Build**
1. **Dependencies**: Install npm packages
2. **Build Process**: Compile React application
3. **File Copy**: Copy build to WordPress theme
4. **Verification**: Verify build success

### **Phase 4: Service Health**
1. **Health Checks**: Verify all services are responding
2. **Browser Launch**: Open services in browser (optional)
3. **Status Report**: Display service URLs and status

---

## **⚙️ Configuration Options**

### **PowerShell Script Parameters**

| Parameter | Description | Default |
|-----------|-------------|---------|
| `-SkipWSL2` | Skip WSL2 setup and configuration | `false` |
| `-SkipDocker` | Skip Docker Desktop startup | `false` |
| `-SkipReact` | Skip React app build process | `false` |
| `-ForceRebuild` | Force clean rebuild of all components | `false` |
| `-Quiet` | Suppress verbose output | `false` |
| `-NoBrowser` | Don't open services in browser | `false` |
| `-Debug` | Enable debug output | `false` |

### **Environment Variables**
```yaml
# Docker Configuration
DOCKER_BUILDKIT: 1
COMPOSE_DOCKER_CLI_BUILD: 1

# React Configuration
NODE_ENV: development
CHOKIDAR_USEPOLLING: true

# WordPress Configuration
WP_DEBUG: true
WP_DEBUG_LOG: true
```

---

## **🔍 Troubleshooting**

### **Common Issues**

#### **1. Docker Desktop Won't Start**
```powershell
# Check Docker Desktop status
Get-Process -Name "Docker Desktop" -ErrorAction SilentlyContinue

# Manual Docker start
Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -WindowStyle Minimized
```

#### **2. Port Already in Use**
```powershell
# Check what's using a port
netstat -ano | findstr :8888

# Kill process using port
Stop-Process -Id <PID> -Force
```

#### **3. React Build Fails**
```powershell
# Clean React app
cd react-app
Remove-Item -Recurse -Force node_modules, package-lock.json, dist, .vite
npm install
npm run build
```

#### **4. Services Not Responding**
```powershell
# Check Docker containers
docker-compose ps

# View service logs
docker-compose logs wordpress
docker-compose logs react-app
```

### **Debug Mode**
```powershell
# Run with debug output
.\start-blackcnote-unified.ps1 -Debug

# Check detailed logs
docker-compose logs -f
```

---

## **🧪 Testing**

### **Comprehensive Test Suite**
```powershell
# Run all tests
.\test-unified-startup.ps1 -FullTest

# Run specific tests
.\test-unified-startup.ps1 -SkipDocker -SkipReact
```

### **Test Coverage**
- ✅ File permissions
- ✅ Port availability
- ✅ Startup scripts
- ✅ Docker Compose configuration
- ✅ Docker functionality
- ✅ React build process
- ✅ Full integration test

---

## **📊 Performance Optimization**

### **Docker Optimizations**
```yaml
# docker-compose.yml optimizations
services:
  wordpress:
    volumes:
      - ./blackcnote:/var/www/html:cached
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

### **React Build Optimizations**
```json
// package.json optimizations
{
  "scripts": {
    "build": "tsc && vite build",
    "build:optimized": "npm run optimize:build && npm run build"
  }
}
```

---

## **🔒 Security Features**

### **Administrator Privileges**
- All startup scripts require administrator privileges
- Proper privilege checking and error handling
- Secure file operations with proper permissions

### **Network Security**
- Services bound to localhost only
- No external network access by default
- Proper firewall configuration

### **Docker Security**
- Non-root user containers
- Read-only file systems where possible
- Security scanning and vulnerability checks

---

## **📝 Logging and Monitoring**

### **Log Locations**
- **WordPress Logs**: `logs/wordpress/`
- **MySQL Logs**: `logs/mysql/`
- **Redis Logs**: `logs/redis/`
- **Docker Logs**: `docker-compose logs <service>`

### **Health Monitoring**
```powershell
# Check service health
curl -f http://localhost:8888/health

# Monitor Docker containers
docker-compose ps

# View real-time logs
docker-compose logs -f
```

---

## **🔄 Maintenance**

### **Regular Maintenance**
```powershell
# Clean up old containers and images
docker system prune -f

# Update dependencies
cd react-app && npm update

# Backup database
docker exec blackcnote_mysql mysqldump -u root -p blackcnote > backup.sql
```

### **System Updates**
```powershell
# Update Docker Desktop
# Download latest version from docker.com

# Update WSL2
wsl --update

# Update Node.js
# Download latest LTS version from nodejs.org
```

---

## **📚 Additional Resources**

### **Documentation**
- **Docker Setup**: `DOCKER-SETUP.md`
- **Canonical Paths**: `BLACKCNOTE-CANONICAL-PATHS.md`
- **Windows Startup**: `BLACKCNOTE-WINDOWS-STARTUP-SYSTEM.md`

### **External Resources**
- **Docker Documentation**: https://docs.docker.com/
- **WSL2 Documentation**: https://docs.microsoft.com/en-us/windows/wsl/
- **WordPress Development**: https://developer.wordpress.org/
- **React Development**: https://react.dev/

---

## **🤝 Support**

### **Getting Help**
1. **Check this documentation** for common issues
2. **Run the test suite**: `.\test-unified-startup.ps1 -FullTest`
3. **Enable debug mode**: `.\start-blackcnote-unified.ps1 -Debug`
4. **Check service logs**: `docker-compose logs <service>`

### **Reporting Issues**
When reporting issues, please include:
- Windows version and build
- Docker Desktop version
- Node.js version
- Complete error messages
- Debug output from startup script

---

## **📋 Migration Guide**

### **From Old Startup System**
1. **Backup old scripts**: `.\cleanup-startup-scripts.ps1 -Backup`
2. **Remove old scripts**: `.\cleanup-startup-scripts.ps1`
3. **Test new system**: `.\test-unified-startup.ps1 -FullTest`
4. **Start with new system**: `.\start-blackcnote-unified.bat`

### **Backward Compatibility**
- All existing Docker configurations remain compatible
- WordPress data and settings are preserved
- React app code and configuration unchanged
- Database and uploads remain intact

---

**Last Updated**: December 2024  
**Version**: 2.0.0 - Unified Engine  
**Status**: ✅ **ACTIVE - REPLACES ALL PREVIOUS STARTUP SCRIPTS** 