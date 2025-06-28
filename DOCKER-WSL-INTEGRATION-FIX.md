# Docker WSL Integration Fix

## 🚨 **Exact Issue Identified**

The error message is clear:
```
The command 'docker' could not be found in this WSL 2 distro.
We recommend to activate the WSL integration in Docker Desktop settings.
```

## ✅ **Quick Fix (5 minutes)**

### **Step 1: Open Docker Desktop Settings**

1. **Right-click Docker Desktop icon** in system tray
2. **Click "Settings"**
3. **Or open Docker Desktop and go to Settings**

### **Step 2: Enable WSL Integration**

1. **Go to "Resources" → "WSL Integration"**
2. **Enable integration for:**
   - ✅ **Ubuntu** (turn ON)
   - ✅ **docker-desktop** (should be ON)
3. **Click "Apply & Restart"**

### **Step 3: Test the Fix**

```powershell
# Test from Windows
docker version

# Test from Ubuntu
wsl -d Ubuntu -- docker version
```

## 🔧 **If WSL Integration Option is Missing**

### **Alternative Fix:**

1. **Close Docker Desktop completely**
2. **Open Docker Desktop**
3. **Go to Settings → General**
4. **Make sure "Use the WSL 2 based engine" is checked**
5. **Go to Settings → Resources → WSL Integration**
6. **Enable Ubuntu integration**

## 📊 **Current Status**

- ✅ **Ubuntu WSL 2**: Working and accessible
- ✅ **WSL 2**: Properly installed
- ❌ **Docker WSL Integration**: Not enabled
- ❌ **Docker API**: Not accessible from Windows
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

## 📋 **Verification Commands**

After enabling WSL integration:

```powershell
# 1. Test Docker from Windows
docker version

# 2. Test Docker from Ubuntu
wsl -d Ubuntu -- docker version

# 3. Test Docker Compose
docker-compose --version

# 4. Test BlackCnote stack
docker-compose ps
```

## 🎉 **Success Indicators**

- ✅ `docker version` shows both client and server
- ✅ `wsl -d Ubuntu -- docker version` works
- ✅ No "command not found" errors
- ✅ No "pipe" or "file not found" errors
- ✅ Docker Desktop shows green/healthy status

## 🚨 **If Still Not Working**

### **Nuclear Option:**
1. **Docker Desktop Settings → Troubleshoot**
2. **Click "Reset to factory defaults"**
3. **Restart Docker Desktop**
4. **Re-enable WSL integration**

### **Alternative:**
1. **Uninstall Docker Desktop**
2. **Restart Windows**
3. **Reinstall Docker Desktop**
4. **Enable WSL integration during setup**

## ✅ **Conclusion**

The issue is **specifically the WSL integration not being enabled** in Docker Desktop settings. This is a common configuration issue that's easily fixed.

**Status**: 🔧 **WSL INTEGRATION NEEDED** | ✅ **DEBUG SYSTEM WORKING** 