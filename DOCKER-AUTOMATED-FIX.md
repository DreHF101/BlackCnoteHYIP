# BlackCnote Docker Automated Fix System

## 🚀 **Overview**

The BlackCnote Docker Automated Fix System provides comprehensive troubleshooting and repair for Docker Desktop issues on Windows. This system automatically detects, diagnoses, and fixes common Docker problems including:

- WSL2 docker-desktop distro startup issues
- Docker daemon communication problems
- Missing Docker pipes
- Docker CLI connectivity issues

## 📁 **Files Created**

### **1. `fix-docker-automated.ps1`**
- **Purpose**: Main PowerShell script with comprehensive Docker diagnostics and repair
- **Features**:
  - Docker CLI availability testing
  - WSL2 status checking and repair
  - Docker daemon connection testing
  - Docker pipe verification
  - Automatic Docker Desktop restart
  - BlackCnote debug system integration
  - Structured logging

### **2. `fix-docker-automated.bat`**
- **Purpose**: Batch wrapper for easy execution
- **Features**:
  - Administrator privilege checking
  - PowerShell execution policy bypass
  - Error handling and exit code reporting
  - User-friendly output formatting

## 🛠️ **What the Automated Fix Does**

### **Step 1: Docker CLI Testing**
```powershell
# Tests if Docker CLI is available and working
Test-DockerCLI
```
- Verifies Docker CLI installation
- Tests `docker --version` command
- Reports CLI version and status

### **Step 2: WSL2 Status Check**
```powershell
# Checks if docker-desktop WSL2 distro is running
Test-WSL2Status
```
- Lists all WSL2 distros
- Identifies docker-desktop distro status
- Reports running/stopped state

### **Step 3: WSL2 Distro Startup**
```powershell
# Starts docker-desktop WSL2 distro if stopped
Start-DockerDesktopWSL
```
- Automatically starts docker-desktop distro
- Waits for startup completion
- Verifies successful startup

### **Step 4: Docker Daemon Testing**
```powershell
# Tests connection to Docker daemon
Test-DockerDaemon
```
- Attempts `docker version` command
- Uses timeout to prevent hanging
- Reports daemon connectivity status

### **Step 5: Docker Pipe Verification**
```powershell
# Checks for Docker named pipes
Test-DockerPipes
```
- Scans for docker-related pipes
- Identifies missing pipes
- Reports pipe availability

### **Step 6: Docker Desktop Restart (if needed)**
```powershell
# Force restarts Docker Desktop
Restart-DockerDesktop
```
- Stops all Docker processes
- Starts Docker Desktop fresh
- Waits for full startup

## 🚀 **Usage Instructions**

### **Quick Fix**
1. **Right-click** `fix-docker-automated.bat`
2. Select **"Run as administrator"**
3. **Wait** for the automated process to complete
4. **Review** the results and log files

### **PowerShell Direct Execution**
```powershell
# Run with default settings
.\fix-docker-automated.ps1

# Run with verbose output
.\fix-docker-automated.ps1 -Verbose

# Run without logging
.\fix-docker-automated.ps1 -NoLog

# Force Docker Desktop restart
.\fix-docker-automated.ps1 -Force
```

### **Command Line Options**
- `-Verbose`: Enable detailed output
- `-NoLog`: Disable logging to files
- `-Force`: Force Docker Desktop restart even if WSL2 starts

## 📊 **Exit Codes**

| Exit Code | Meaning | Action Required |
|-----------|---------|-----------------|
| 0 | **SUCCESS** | Docker is fully functional |
| 1 | **WARNING** | CLI works, daemon connection failed | Manual Docker Desktop restart |
| 2 | **ERROR** | Docker CLI not working | Reinstall Docker Desktop |
| 4 | **CRITICAL** | Script execution error | Check logs for details |

## 📋 **Log Files**

### **Docker Automated Fix Log**
- **Location**: `logs\docker-automated-fix.log`
- **Format**: JSON structured logging
- **Content**: Step-by-step execution details

### **BlackCnote Debug Log**
- **Location**: `blackcnote\wp-content\logs\blackcnote-debug.log`
- **Format**: JSON structured logging
- **Content**: Integration with BlackCnote debug system

## 🔍 **Troubleshooting**

### **Script Won't Run**
1. **Check PowerShell Execution Policy**:
   ```powershell
   Get-ExecutionPolicy
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   ```

2. **Run as Administrator**:
   - Right-click batch file
   - Select "Run as administrator"

### **WSL2 Issues**
1. **Check WSL2 Installation**:
   ```powershell
   wsl --version
   ```

2. **Update WSL2**:
   ```powershell
   wsl --update
   ```

### **Docker Desktop Issues**
1. **Check Docker Desktop Status**:
   - Open Docker Desktop
   - Check Troubleshoot menu
   - Review diagnostic logs

2. **Factory Reset**:
   - Docker Desktop → Troubleshoot → Reset to factory defaults

## 🔧 **Advanced Configuration**

### **Custom Timeouts**
Edit the PowerShell script to modify timeouts:
```powershell
# WSL2 startup timeout (default: 25 seconds)
Start-Sleep -Seconds 25

# Docker daemon test timeout (default: 30 seconds)
$result = Wait-Job -Job $job -Timeout 30
```

### **Additional Diagnostics**
Add custom diagnostic functions:
```powershell
function Test-CustomDiagnostic {
    # Add your custom diagnostic logic here
    Write-BlackCnoteLog 'Custom diagnostic completed' 'INFO'
}
```

## 📚 **Integration with BlackCnote**

### **Debug System Integration**
The automated fix integrates with BlackCnote's debug system:
- Logs all actions to BlackCnote debug log
- Uses structured JSON logging
- Maintains component separation

### **Path Recognition**
Automatically recognizes BlackCnote project structure:
- Uses correct paths for log files
- Integrates with existing BlackCnote directories
- Maintains project organization

## 🚨 **Common Issues and Solutions**

### **Issue: "docker-desktop WSL2 distro won't start"**
**Solution**: 
1. Run `wsl --shutdown`
2. Restart Docker Desktop
3. Run automated fix again

### **Issue: "Docker daemon connection timeout"**
**Solution**:
1. Check Windows Defender/Antivirus exclusions
2. Verify WSL2 backend is enabled in Docker Desktop
3. Run automated fix with `-Force` flag

### **Issue: "No Docker pipes found"**
**Solution**:
1. Ensure Docker Desktop is running
2. Check WSL2 integration
3. Restart Docker Desktop completely

## 🔒 **Security Considerations**

### **Administrator Privileges**
- Script may require elevated privileges for WSL2 operations
- Docker Desktop operations often need admin rights
- WSL2 management requires system-level access

### **Execution Policy**
- Script temporarily bypasses execution policy for current process
- No permanent system changes to execution policy
- Safe for enterprise environments

## 📈 **Performance Monitoring**

### **Execution Time**
- Typical execution: 1-3 minutes
- WSL2 startup: 10-30 seconds
- Docker daemon test: 5-15 seconds

### **Resource Usage**
- Minimal CPU usage during execution
- Temporary memory allocation for job management
- No persistent resource consumption

## 🤝 **Support and Maintenance**

### **Log Analysis**
Review log files for detailed diagnostics:
```powershell
Get-Content "logs\docker-automated-fix.log" | ConvertFrom-Json
```

### **Script Updates**
- Check for script updates regularly
- Monitor Docker Desktop version compatibility
- Update WSL2 integration as needed

---

**Last Updated**: June 27, 2025  
**Version**: 1.0.0  
**Compatibility**: Windows 10/11, Docker Desktop 4.0+  
**BlackCnote Integration**: Full debug system integration 