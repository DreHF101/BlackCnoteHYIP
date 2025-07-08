# Docker Engine Quick Fix Guide

## 🚨 **Current Issue**
Docker Desktop is running but the Docker engine is not responding. This is a common WSL2 integration issue.

## ⚡ **Quick Fix (5 minutes)**

### **Step 1: Open Docker Desktop Settings**
1. **Right-click Docker Desktop icon** in system tray
2. **Click "Settings"**
3. **Or open Docker Desktop and go to Settings**

### **Step 2: Check WSL2 Backend**
1. **Go to "General" tab**
2. **Make sure "Use the WSL 2 based engine" is CHECKED**
3. **If not checked, check it and click "Apply & Restart"**

### **Step 3: Enable WSL Integration**
1. **Go to "Resources" → "WSL Integration"**
2. **Enable integration for:**
   - ✅ **Ubuntu** (turn ON)
   - ✅ **docker-desktop** (should be ON)
3. **Click "Apply & Restart"**

### **Step 4: Test the Fix**
```powershell
# Test Docker connection
docker info

# Should return Docker daemon information, not connection errors
```

## 🔧 **If Quick Fix Doesn't Work**

### **Option A: Docker Desktop Factory Reset**
1. **Open Docker Desktop Settings**
2. **Go to "Troubleshoot"**
3. **Click "Reset to factory defaults"**
4. **Restart Docker Desktop**

### **Option B: WSL2 Reset**
```powershell
# Run as Administrator
wsl --shutdown
wsl --unregister docker-desktop
wsl --unregister docker-desktop-data
# Restart Docker Desktop
```

### **Option C: Automated Fix Script**
```powershell
# Right-click and "Run as administrator"
.\fix-docker-engine-complete.ps1
```

## 📊 **Current Status**
- ✅ **Docker Desktop**: Running (multiple processes)
- ✅ **WSL2**: Working (Ubuntu and docker-desktop distros running)
- ❌ **Docker Engine**: Not responding
- ✅ **Debug System**: Fully operational

## 🎯 **Expected Results After Fix**
```powershell
# Should show both client and server
docker version

# Should work from Ubuntu
wsl -d Ubuntu -- docker version

# Should show containers
docker ps

# Should work for BlackCnote
docker-compose up -d
```

## 🚀 **Immediate Workaround**
While fixing Docker, continue development with the debug system:
```powershell
# Check debug system
Get-Content logs/blackcnote-debug.log -Tail 5

# Test metrics
php bin/blackcnote-metrics-exporter.php

# Start daemon
php bin/blackcnote-debug-daemon.php
```

## 🔍 **Troubleshooting**

### **If WSL Integration Option is Missing**
1. **Close Docker Desktop completely**
2. **Open Docker Desktop**
3. **Go to Settings → General**
4. **Make sure "Use WSL 2 based engine" is checked**
5. **Go to Settings → Resources → WSL Integration**
6. **Enable Ubuntu integration**

### **If Docker Desktop Won't Start**
1. **Check Windows Defender exclusions**
2. **Check antivirus software**
3. **Verify Windows features are enabled:**
   - WSL2
   - Virtual Machine Platform
   - Hyper-V (if applicable)

### **If WSL2 is Not Working**
```powershell
# Enable WSL2
wsl --install
# Restart computer
```

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

## 🆘 **If Still Not Working**

1. **Try the automated fix script**: `.\fix-docker-engine-complete.ps1`
2. **Check Docker Desktop logs** in Settings → Troubleshoot
3. **Verify WSL2 integration settings**
4. **Check file permissions in containers**
5. **Monitor system resources**

---

**Last Updated**: December 2024  
**Version**: 1.0.0  
**Compatibility**: Windows 10/11 with Docker Desktop 