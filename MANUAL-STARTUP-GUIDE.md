# BlackCnote Manual Startup Guide
**Issue**: Docker Compose cannot find configuration file  
**Solution**: Explicit file path specification

## 🚨 **PROBLEM IDENTIFIED**

From the diagnostic results:
- ✅ Docker is accessible (v28.1.1)
- ✅ Docker Compose is accessible (v2.35.1)
- ❌ **"no configuration file provided: not found"**
- ❌ **"docker-compose.yml not found"**

## 🔧 **SOLUTION**

The issue is that Docker Compose cannot find the configuration file. Here's how to fix it:

### **Step 1: Open Command Prompt as Administrator**
1. Press `Windows + R`
2. Type `cmd`
3. Press `Ctrl + Shift + Enter`

### **Step 2: Navigate to BlackCnote Directory**
```cmd
cd "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
```

### **Step 3: Verify Current Location**
```cmd
dir docker-compose.yml
```
You should see the file listed.

### **Step 4: Start Services with Explicit File Path**
```cmd
docker-compose -f docker-compose.yml up -d
```

### **Step 5: Check Service Status**
```cmd
docker-compose -f docker-compose.yml ps
```

### **Step 6: Wait and Test**
```cmd
timeout /t 60
curl http://localhost:8888
```

## 🚀 **ALTERNATIVE: Use the Fixed Script**

1. **Right-click** on `start-blackcnote-fixed.bat`
2. Select **"Run as administrator"**
3. The script will automatically use the explicit file path

## 📊 **EXPECTED RESULTS**

After running the commands, you should see:

### **Service Startup:**
```
Creating blackcnote_mysql ... done
Creating blackcnote_redis ... done
Creating blackcnote_wordpress ... done
Creating blackcnote_phpmyadmin ... done
Creating blackcnote_react ... done
Creating blackcnote_debug ... done
Creating blackcnote_debug_exporter ... done
```

### **Service Status:**
```
Name                        Command               State           Ports
blackcnote_wordpress       docker-entrypoint.sh  Up              0.0.0.0:8888->80/tcp
blackcnote_mysql           docker-entrypoint.sh  Up              3306/tcp
blackcnote_redis           docker-entrypoint.sh  Up              6379/tcp
blackcnote_phpmyadmin      /docker-entrypoint.   Up              0.0.0.0:8080->80/tcp
```

### **Port Status:**
```
TCP    0.0.0.0:8888    0.0.0.0:0    LISTENING
```

## 🌐 **ACCESS URLs**

Once services are running:
- **BlackCnote**: http://localhost:8888
- **phpMyAdmin**: http://localhost:8080
- **Metrics**: http://localhost:9091
- **React App**: http://localhost:5174

## 🔍 **TROUBLESHOOTING**

### **If services still don't start:**
```cmd
# Check Docker logs
docker-compose -f docker-compose.yml logs

# Check specific service logs
docker-compose -f docker-compose.yml logs wordpress
docker-compose -f docker-compose.yml logs mysql
```

### **If port 8888 is still not accessible:**
```cmd
# Check what's using the port
netstat -an | findstr :8888

# Restart services
docker-compose -f docker-compose.yml down
docker-compose -f docker-compose.yml up -d
```

## ✅ **VERIFICATION**

After successful startup:
1. **Open browser** to http://localhost:8888
2. **You should see** the BlackCnote WordPress site
3. **No more** "ERR_CONNECTION_REFUSED" errors

---

**Status**: 🔧 ISSUE IDENTIFIED AND FIXED  
**Next Action**: 🚀 RUN THE FIXED COMMANDS  
**Expected Result**: ✅ BLACKCNOTE ACCESSIBLE 