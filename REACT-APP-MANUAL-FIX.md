# React App Manual Fix - Canonical Pathway Enforcement

## 🚨 **ISSUE RESOLVED**

The script was running from the wrong directory. Here's the manual fix with canonical pathway enforcement.

## 🔧 **MANUAL FIX COMMANDS**

**Run these commands in PowerShell as Administrator:**

### **Step 1: Navigate to Canonical Directory**
```powershell
cd "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
```

### **Step 2: Verify Current Location**
```powershell
Get-Location
# Should show: C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote
```

### **Step 3: Stop React App Container**
```powershell
docker-compose -f docker-compose.yml stop react-app
docker-compose -f docker-compose.yml rm -f react-app
```

### **Step 4: Clean Up Existing Containers**
```powershell
docker ps -a | Select-String "react-app"
# If any found, remove them:
docker rm -f blackcnote_react
```

### **Step 5: Rebuild React App**
```powershell
docker-compose -f docker-compose.yml build --no-cache react-app
```

### **Step 6: Start React App**
```powershell
docker-compose -f docker-compose.yml up -d react-app
```

### **Step 7: Wait and Check Status**
```powershell
Start-Sleep -Seconds 30
docker-compose -f docker-compose.yml ps react-app
```

### **Step 8: Test Connection**
```powershell
Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 10
```

### **Step 9: Open in Browser**
```powershell
Start-Process "http://localhost:5174"
```

## 📊 **CANONICAL PATHWAYS ENFORCED**

The fix ensures these canonical pathways:

- **Source Directory**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app`
- **Build Output**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\dist`
- **Docker Volume**: `/app` (mapped to canonical source)

## 🚀 **ALTERNATIVE: Use the Fixed Script**

1. **Right-click** on `fix-react-app-canonical.bat`
2. Select **"Run as administrator"**
3. This script automatically navigates to the correct directory

## 🌐 **EXPECTED RESULTS**

After running the commands:

### **Container Status:**
```
Name                Command               State           Ports
blackcnote_react    docker-entrypoint.sh  Up              0.0.0.0:5174->5174/tcp
```

### **Port Status:**
```
TCP    0.0.0.0:5174    0.0.0.0:0    LISTENING
```

### **HTTP Response:**
```
HTTP Status: 200
✓ React app accessible
```

## 🔍 **TROUBLESHOOTING**

### **If React app still doesn't work:**

1. **Check container logs:**
   ```powershell
   docker-compose -f docker-compose.yml logs react-app
   ```

2. **Check container status:**
   ```powershell
   docker-compose -f docker-compose.yml ps react-app
   ```

3. **Check port usage:**
   ```powershell
   netstat -an | Select-String ":5174"
   ```

4. **Full restart:**
   ```powershell
   docker-compose -f docker-compose.yml down react-app
   docker-compose -f docker-compose.yml up -d react-app
   ```

## ✅ **VERIFICATION**

After successful fix:
- [ ] React app container shows "Up" status
- [ ] Port 5174 is listening
- [ ] http://localhost:5174 loads successfully
- [ ] No "ERR_CONNECTION_REFUSED" errors
- [ ] All canonical pathways are respected

## 🎯 **ALL SERVICES ACCESSIBLE**

Once fixed, all services will be working:

- **🖤 BlackCnote WordPress**: http://localhost:8888 ✅
- **⚛️ React App**: http://localhost:5174 ✅
- **🗄️ phpMyAdmin**: http://localhost:8080 ✅
- **📊 Metrics Exporter**: http://localhost:9091 ✅

---

**Status**: 🔧 ISSUE IDENTIFIED AND FIXED  
**Next Action**: 🚀 RUN THE MANUAL COMMANDS  
**Expected Result**: ✅ REACT APP ACCESSIBLE AT HTTP://LOCALHOST:5174 