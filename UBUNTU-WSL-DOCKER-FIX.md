# Ubuntu WSL 2 & Docker Integration Fix

## 🚨 **Current Issues**
1. **Ubuntu distro file not found** - WSL 2 distribution issues
2. **Docker API connectivity** - Docker Desktop can't connect to engine
3. **WSL 2 integration** - Docker Desktop not properly integrated with WSL 2

## 🔧 **Step-by-Step Fix**

### **Step 1: Reset WSL 2 Completely**

```powershell
# Run as Administrator
wsl --shutdown
wsl --unregister Ubuntu
wsl --unregister docker-desktop
wsl --unregister docker-desktop-data
```

### **Step 2: Reinstall Ubuntu**

```powershell
# Install Ubuntu from Microsoft Store or download
wsl --install -d Ubuntu
```

### **Step 3: Configure WSL 2**

```powershell
# Set WSL 2 as default
wsl --set-default-version 2

# Set Ubuntu to use WSL 2
wsl --set-version Ubuntu 2
```

### **Step 4: Reset Docker Desktop**

1. **Close Docker Desktop completely**
2. **Delete Docker settings** (optional - nuclear option):
   ```powershell
   Remove-Item "$env:USERPROFILE\AppData\Roaming\Docker" -Recurse -Force
   Remove-Item "$env:USERPROFILE\AppData\Local\Docker" -Recurse -Force
   ```
3. **Restart Docker Desktop**

### **Step 5: Configure Docker Desktop WSL Integration**

1. **Open Docker Desktop**
2. **Go to Settings → Resources → WSL Integration**
3. **Enable integration for:**
   - ✅ Ubuntu
   - ✅ docker-desktop
4. **Click "Apply & Restart"**

### **Step 6: Test Integration**

```powershell
# Test WSL
wsl --list --verbose

# Test Docker
docker version

# Test from Ubuntu
wsl -d Ubuntu
docker version
```

## 🚀 **Alternative: Quick Fix**

If the above doesn't work, try this quick fix:

### **Option A: Docker Desktop Factory Reset**

1. **Open Docker Desktop**
2. **Settings → Troubleshoot**
3. **Click "Reset to factory defaults"**
4. **Restart Docker Desktop**

### **Option B: WSL 2 Reset Only**

```powershell
# Run as Administrator
wsl --shutdown
wsl --unregister docker-desktop
wsl --unregister docker-desktop-data
# Restart Docker Desktop
```

## 📊 **Current Status Check**

Let's verify what's working:

```powershell
# Check WSL status
wsl --list --verbose

# Check Docker status
docker version

# Check Ubuntu access
wsl -d Ubuntu -- echo "Ubuntu is working"
```

## 🎯 **Immediate Workaround**

While fixing Docker, the **BlackCnote Debug System continues to work perfectly**:

```powershell
# Check debug system status
Get-Content logs/blackcnote-debug.log -Tail 5

# Test metrics exporter
php bin/blackcnote-metrics-exporter.php

# Start debug daemon
php bin/blackcnote-debug-daemon.php
```

## ✅ **Expected Results**

After successful fix:
- ✅ Ubuntu WSL 2 distribution working
- ✅ Docker Desktop API accessible
- ✅ `docker version` shows both client and server
- ✅ `docker ps` works
- ✅ Full BlackCnote stack can be deployed

## 🚨 **If Issues Persist**

### **Nuclear Option: Complete Reset**

```powershell
# 1. Uninstall Docker Desktop
# 2. Uninstall WSL 2
# 3. Restart Windows
# 4. Reinstall WSL 2
# 5. Reinstall Docker Desktop
```

### **Alternative: Use WSL 1**

```powershell
# Set Ubuntu to WSL 1 (less features but more stable)
wsl --set-version Ubuntu 1
```

## 📋 **Quick Commands**

```powershell
# Check WSL status
wsl --list --verbose

# Check Docker status
docker version

# Test Ubuntu
wsl -d Ubuntu -- uname -a

# Check debug system
Get-Content logs/blackcnote-debug.log -Tail 3
```

## 🎉 **Success Indicators**

- ✅ `wsl --list --verbose` shows Ubuntu and docker-desktop as Running
- ✅ `docker version` shows both client and server information
- ✅ `docker ps` returns container list (even if empty)
- ✅ No "file not found" or "pipe" errors

**Status**: 🔧 **FIXING WSL/DOCKER** | ✅ **DEBUG SYSTEM WORKING** 