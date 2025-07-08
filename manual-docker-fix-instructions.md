# BlackCnote Manual Docker Engine Fix Instructions

## 🚨 **CRITICAL: MANUAL INTERVENTION REQUIRED** 🚨

**Status**: Docker Desktop is installed but the engine is not starting properly. The `docker-desktop` WSL2 distro is missing.

---

## **🔧 MANUAL FIX STEPS**

### **Step 1: Open Docker Desktop Manually**
1. **Open Windows Start Menu**
2. **Search for "Docker Desktop"**
3. **Right-click and select "Run as administrator"**
4. **Wait for Docker Desktop to fully load (3-5 minutes)**

### **Step 2: Check Docker Desktop Settings**
1. **In Docker Desktop, go to Settings (gear icon)**
2. **Go to "General" tab**
3. **Ensure "Use the WSL 2 based engine" is checked**
4. **Go to "Resources" > "WSL Integration"**
5. **Enable integration with your Ubuntu distribution**
6. **Click "Apply & Restart"**

### **Step 3: Wait for Full Initialization**
- **Docker Desktop will create the missing `docker-desktop` WSL2 distro**
- **This process can take 5-10 minutes**
- **You'll see a progress indicator in Docker Desktop**

### **Step 4: Verify Docker Engine**
Once Docker Desktop shows "Docker Desktop is running", open PowerShell and run:
```powershell
docker info
```

You should see both Client and Server information without errors.

### **Step 5: Start BlackCnote Services**
After Docker engine is working, run:
```powershell
cd "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
docker-compose -f config/docker/docker-compose.yml up -d
```

---

## **🌐 SERVICE URLs TO VERIFY**

Once Docker is working, these services will be accessible:

| Service | URL | Purpose |
|---------|-----|---------|
| **WordPress** | http://localhost:8888 | Main BlackCnote site |
| **WordPress Admin** | http://localhost:8888/wp-admin/ | WordPress administration |
| **React App** | http://localhost:5174 | React development server |
| **phpMyAdmin** | http://localhost:8080 | Database management |
| **MailHog** | http://localhost:8025 | Email testing |
| **Redis Commander** | http://localhost:8081 | Cache management |
| **Browsersync** | http://localhost:3000 | Live reloading |

---

## **📋 VERIFICATION CHECKLIST**

After completing the manual steps:

- [ ] Docker Desktop shows "Docker Desktop is running"
- [ ] `docker info` command works without errors
- [ ] `docker ps` shows running containers
- [ ] All service URLs are accessible in browser
- [ ] WordPress loads at http://localhost:8888
- [ ] React app loads at http://localhost:5174
- [ ] phpMyAdmin loads at http://localhost:8080

---

## **🔍 TROUBLESHOOTING**

### **If Docker Desktop Won't Start:**
1. **Check Windows Services**
   - Open Services (services.msc)
   - Look for "Docker Desktop Service"
   - Ensure it's running

2. **Check WSL2 Status**
   ```powershell
   wsl --list --verbose
   ```
   - Should show Ubuntu and docker-desktop distributions

3. **Reset Docker Desktop**
   - In Docker Desktop settings
   - Go to "Troubleshoot"
   - Click "Reset to factory defaults"

### **If WSL2 Issues:**
1. **Update WSL2**
   ```powershell
   wsl --update
   ```

2. **Set WSL2 as Default**
   ```powershell
   wsl --set-default-version 2
   ```

3. **Reinstall Ubuntu Distribution**
   ```powershell
   wsl --unregister Ubuntu
   wsl --install -d Ubuntu
   ```

---

## **📞 SUPPORT**

If issues persist:

1. **Check Docker Desktop logs**
   - In Docker Desktop, go to Troubleshoot
   - View logs for error details

2. **Check Windows Event Viewer**
   - Look for Docker-related errors

3. **Verify System Requirements**
   - Windows 10/11 Pro, Enterprise, or Education
   - WSL2 enabled
   - Virtualization enabled in BIOS

---

## **🎯 EXPECTED OUTCOME**

After successful manual intervention:
- ✅ Docker engine fully operational
- ✅ All BlackCnote containers running
- ✅ All services accessible via browser
- ✅ Full development environment ready
- ✅ Live editing and hot reloading working

**Last Updated**: December 28, 2024
**Status**: Manual intervention required for Docker engine fix 