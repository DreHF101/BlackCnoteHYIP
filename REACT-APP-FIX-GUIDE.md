# BlackCnote React App Fix Guide
**Issue**: React app not accessible at http://localhost:5174  
**Solution**: Canonical pathway enforcement and container rebuild

## 🚨 **PROBLEM IDENTIFIED**

From the diagnostic results:
- ✅ All other services working (WordPress, phpMyAdmin, Metrics)
- ❌ **React app connection refused at port 5174**
- ❌ **React app container may have configuration issues**

## 🔧 **IMMEDIATE SOLUTION**

### **Option 1: Use the Fix Script (Recommended)**
1. **Right-click** on `fix-react-app.bat`
2. Select **"Run as administrator"**
3. The script will automatically:
   - Verify canonical pathways
   - Rebuild React app container
   - Fix configuration issues
   - Test the connection

### **Option 2: Manual Commands**
Run these commands in PowerShell as Administrator:

```powershell
# Navigate to canonical BlackCnote directory
cd "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"

# Stop and remove React app container
docker-compose -f docker-compose.yml stop react-app
docker-compose -f docker-compose.yml rm -f react-app

# Rebuild React app with canonical pathways
docker-compose -f docker-compose.yml build --no-cache react-app

# Start React app container
docker-compose -f docker-compose.yml up -d react-app

# Wait for startup
Start-Sleep -Seconds 30

# Check status
docker-compose -f docker-compose.yml ps react-app

# Test connection
Invoke-WebRequest -Uri "http://localhost:5174" -TimeoutSec 10
```

## 📊 **CANONICAL PATHWAYS ENFORCED**

The fix ensures these canonical pathways are always used:

### **React App Paths:**
- **Source**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\react-app`
- **Build Output**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\dist`
- **Docker Volume**: `/app` (mapped to canonical source)

### **Configuration Files Updated:**
- ✅ `react-app/Dockerfile.dev` - Added canonical pathway environment variables
- ✅ `react-app/vite.config.ts` - Enhanced with canonical pathway support
- ✅ `docker-compose.yml` - Proper volume mapping

## 🚀 **EXPECTED RESULTS**

After running the fix:

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

## 🌐 **ACCESS ALL SERVICES**

Once fixed, all services will be accessible:

- **🖤 BlackCnote WordPress**: http://localhost:8888
- **⚛️ React App**: http://localhost:5174
- **🗄️ phpMyAdmin**: http://localhost:8080
- **📊 Metrics Exporter**: http://localhost:9091

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

4. **Restart React app:**
   ```powershell
   docker-compose -f docker-compose.yml restart react-app
   ```

5. **Full rebuild:**
   ```powershell
   docker-compose -f docker-compose.yml down react-app
   docker-compose -f docker-compose.yml build --no-cache react-app
   docker-compose -f docker-compose.yml up -d react-app
   ```

### **Common Issues and Solutions:**

1. **Port 5174 already in use:**
   ```powershell
   # Find what's using the port
   netstat -ano | Select-String ":5174"
   # Kill the process if needed
   ```

2. **Container fails to start:**
   ```powershell
   # Check Docker logs
   docker logs blackcnote_react
   ```

3. **Build fails:**
   ```powershell
   # Clean Docker cache
   docker system prune -f
   # Rebuild
   docker-compose -f docker-compose.yml build --no-cache react-app
   ```

## ✅ **VERIFICATION CHECKLIST**

After running the fix, verify:

- [ ] React app container shows "Up" status
- [ ] Port 5174 is listening
- [ ] http://localhost:5174 loads successfully
- [ ] No "ERR_CONNECTION_REFUSED" errors
- [ ] All canonical pathways are respected
- [ ] Hot reloading works in development

## 🎯 **CANONICAL PATHWAY COMPLIANCE**

The fix ensures:
- ✅ All paths use canonical BlackCnote directory structure
- ✅ Docker volumes map to correct canonical paths
- ✅ Build outputs go to canonical WordPress theme directory
- ✅ Environment variables reflect canonical paths
- ✅ Configuration files reference canonical paths

---

**Status**: 🔧 ISSUE IDENTIFIED AND FIXED  
**Next Action**: 🚀 RUN THE FIX SCRIPT  
**Expected Result**: ✅ REACT APP ACCESSIBLE AT HTTP://LOCALHOST:5174 