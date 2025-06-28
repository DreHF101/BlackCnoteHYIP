# Docker Desktop WSL2 Distro Fix Guide

## 🚨 **The Problem**

The error "docker-desktop distro not found" occurs when Docker Desktop's WSL2 integration is broken. This prevents Docker from working properly.

## 🔍 **Diagnosis**

From your output, we can see:
- ✅ WSL2 is working (Ubuntu and docker-desktop distros exist)
- ✅ docker-desktop distro shows as "Running" 
- ❌ But Docker Desktop process detection fails
- ❌ Docker connection fails

## 🔧 **Solution Steps**

### **Step 1: Manual Docker Desktop Settings Check**

1. **Open Docker Desktop manually**
2. **Go to Settings** (gear icon)
3. **Click on "General"**
4. **Ensure these settings are correct:**
   - ✅ **Use WSL 2 based engine** (CHECKED)
   - ✅ **Use the WSL 2 based engine** (CHECKED)
   - ✅ **Enable integration with my default WSL distro** (CHECKED)

### **Step 2: Restart Docker Desktop Properly**

1. **Close Docker Desktop completely**
2. **Wait 30 seconds**
3. **Start Docker Desktop manually**
4. **Wait for it to fully initialize** (2-3 minutes)

### **Step 3: Verify WSL2 Integration**

Run these commands in PowerShell as Administrator:

```powershell
# Check WSL2 status
wsl --list --verbose

# Should show:
#   NAME              STATE           VERSION
# * Ubuntu            Stopped         2
#   docker-desktop    Running         2
```

### **Step 4: Test Docker Connection**

```powershell
# Test Docker connection
docker info

# Should return Docker daemon information, not connection errors
```

## 🛠️ **Automated Fix Script**

Run the focused fix script:

```bash
# Right-click and "Run as administrator"
fix-docker-desktop-distro.bat
```

## 🔄 **If Still Not Working**

### **Nuclear Option: Complete Docker Desktop Reset**

1. **Stop Docker Desktop completely**
2. **Remove Docker Desktop settings:**
   ```cmd
   rmdir /s /q "%APPDATA%\Docker"
   rmdir /s /q "%LOCALAPPDATA%\Docker"
   ```
3. **Restart your computer**
4. **Start Docker Desktop fresh**
5. **Wait for full initialization**

### **Alternative: Reinstall Docker Desktop**

1. **Uninstall Docker Desktop**
2. **Restart computer**
3. **Download latest Docker Desktop**
4. **Install with WSL2 backend**
5. **Start and wait for initialization**

## 🎯 **Expected Results**

After successful fix:
- ✅ `wsl --list --verbose` shows docker-desktop as "Running"
- ✅ `docker info` returns daemon information
- ✅ Docker commands work properly
- ✅ BlackCnote Docker containers can start

## 🔍 **Troubleshooting**

### **If docker-desktop distro is missing:**
```powershell
# Reinstall Docker Desktop with WSL2 backend
# This will recreate the docker-desktop distro
```

### **If WSL2 is not working:**
```powershell
# Enable WSL2
wsl --install
# Restart computer
```

### **If Docker Desktop won't start:**
1. Check Windows Defender exclusions
2. Check antivirus software
3. Verify Windows features are enabled:
   - WSL2
   - Virtual Machine Platform
   - Hyper-V (if applicable)

## 📋 **Verification Commands**

After fix, run these to verify:

```powershell
# Check WSL2
wsl --list --verbose

# Check Docker
docker info

# Test Docker functionality
docker run hello-world

# Check Docker Desktop process
Get-Process -Name "Docker Desktop"
```

## 🎉 **Success Indicators**

- ✅ Docker Desktop shows as running in Task Manager
- ✅ `docker info` returns server information
- ✅ `wsl --list --verbose` shows docker-desktop as "Running"
- ✅ Docker commands execute without connection errors

---

**Last Updated**: June 27, 2025  
**Version**: 1.0.0  
**Compatibility**: Windows 10/11 with Docker Desktop 