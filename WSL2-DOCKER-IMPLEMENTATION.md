# WSL2 Docker Implementation for BlackCnote

## **🚀 WSL2 BACKEND IMPLEMENTATION**

### **Why WSL2 for Docker?**
- **Better Performance:** WSL2 provides near-native Linux performance on Windows
- **Volume Sync Fix:** Resolves Docker Desktop volume mounting issues on Windows
- **File System Performance:** Significantly faster file I/O operations
- **Linux Compatibility:** Full Linux kernel support for Docker containers
- **Memory Management:** Better memory allocation and management

---

## **STEP-BY-STEP WSL2 IMPLEMENTATION**

### **Step 1: Enable WSL2 Features**

Open PowerShell as Administrator and run:

```powershell
# Enable WSL feature
dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart

# Enable Virtual Machine feature
dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart

# Restart your computer
Restart-Computer
```

### **Step 2: Install WSL2 Kernel Update**

After restart, download and install the WSL2 Linux kernel update:
- Go to: https://aka.ms/wsl2kernel
- Download and install the package

### **Step 3: Set WSL2 as Default**

```powershell
# Set WSL2 as default version
wsl --set-default-version 2
```

### **Step 4: Install Ubuntu Distribution**

```powershell
# Install Ubuntu from Microsoft Store or via command line
wsl --install -d Ubuntu
```

### **Step 5: Configure Docker Desktop for WSL2**

1. **Open Docker Desktop**
2. **Go to Settings > General**
3. **Check "Use the WSL 2 based engine"**
4. **Go to Settings > Resources > WSL Integration**
5. **Enable integration with your Ubuntu distribution**
6. **Click "Apply & Restart"**

### **Step 6: Move Project to WSL2 File System**

For optimal performance, move your project to the WSL2 file system:

```bash
# In WSL2 Ubuntu terminal
cd /home/your-username
mkdir blackcnote
cd blackcnote

# Copy your project files from Windows to WSL2
cp -r /mnt/c/Users/CASH\ AMERICA\ PAWN/Desktop/BlackCnote/* .
```

### **Step 7: Update Docker Compose for WSL2**

Update your Docker Compose configuration for WSL2:

```yaml
version: '3.8'
services:
  wordpress:
    image: wordpress:6.8-apache
    container_name: blackcnote-wordpress
    environment:
      WORDPRESS_DB_HOST: mysql
      WORDPRESS_DB_NAME: blackcnote
      WORDPRESS_DB_USER: root
      WORDPRESS_DB_PASSWORD: blackcnote_password
      WORDPRESS_DEBUG: 1
      WP_HOME: http://localhost:8888
      WP_SITEURL: http://localhost:8888
      WP_CONTENT_URL: http://localhost:8888/wp-content
      WP_DEBUG: true
      WP_DEBUG_LOG: true
      WP_DEBUG_DISPLAY: false
      SCRIPT_DEBUG: true
      SAVEQUERIES: true
      WP_CACHE: false
      FS_METHOD: direct
      WP_MEMORY_LIMIT: 256M
      WP_MAX_MEMORY_LIMIT: 512M
    volumes:
      # WSL2 optimized volume mounts
      - "./blackcnote:/var/www/html:cached"
      - "./scripts:/var/www/html/scripts:cached"
      - "./logs:/var/www/html/logs:cached"
    depends_on:
      - mysql
      - redis
    networks:
      - blackcnote-network
    restart: unless-stopped
```

---

## **WSL2 OPTIMIZATION CONFIGURATIONS**

### **WSL2 Configuration File**

Create `.wslconfig` in your Windows user directory (`C:\Users\CASH AMERICA PAWN\.wslconfig`):

```ini
[wsl2]
# Memory allocation
memory=8GB

# CPU allocation
processors=4

# Swap allocation
swap=2GB

# File system performance
localhostForwarding=true

# Kernel command line
kernelCommandLine=cgroup_enable=1 cgroup_memory=1 cgroup_enable=1 swapaccount=1

# Performance optimizations
pageReporting=false
nestedVirtualization=true
```

### **Docker Desktop WSL2 Settings**

In Docker Desktop Settings > Resources > WSL Integration:

```yaml
# Enable WSL Integration
Enable integration with my default WSL distro: ✅
Enable integration with additional distros: ✅ Ubuntu

# Resource allocation
Memory: 6GB
Swap: 1GB
Disk image size: 64GB
```

---

## **PERFORMANCE OPTIMIZATIONS**

### **File System Performance**

For maximum performance, use the WSL2 file system instead of Windows file system:

```bash
# Check current file system location
pwd

# If in /mnt/c/ (Windows file system), move to WSL2 file system
# WSL2 file system: /home/username/
# Windows file system: /mnt/c/Users/...
```

### **Volume Mount Optimizations**

Use appropriate volume mount options:

```yaml
volumes:
  # For read-heavy workloads
  - "./data:/var/www/html:ro"
  
  # For development (read-write with caching)
  - "./blackcnote:/var/www/html:cached"
  
  # For performance-critical applications
  - "./cache:/var/cache:delegated"
```

---

## **TROUBLESHOOTING WSL2**

### **Common Issues and Solutions**

#### **Issue 1: WSL2 Not Starting**
```powershell
# Reset WSL
wsl --shutdown
wsl --unregister Ubuntu
wsl --install -d Ubuntu
```

#### **Issue 2: Docker Not Working in WSL2**
```bash
# Check Docker service
sudo service docker status

# Start Docker service
sudo service docker start

# Add user to docker group
sudo usermod -aG docker $USER
```

#### **Issue 3: Volume Mount Issues**
```bash
# Check file permissions
ls -la /var/www/html

# Fix permissions
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
```

#### **Issue 4: Performance Issues**
```bash
# Check WSL2 memory usage
free -h

# Check disk space
df -h

# Monitor system resources
htop
```

---

## **MIGRATION STEPS**

### **Step 1: Backup Current Setup**
```bash
# Backup your current project
cp -r /mnt/c/Users/CASH\ AMERICA\ PAWN/Desktop/BlackCnote /mnt/c/Users/CASH\ AMERICA\ PAWN/Desktop/BlackCnote-backup
```

### **Step 2: Stop Current Containers**
```bash
docker-compose -f config/docker/docker-compose.yml down
```

### **Step 3: Enable WSL2 Backend**
Follow the WSL2 implementation steps above.

### **Step 4: Move Project to WSL2**
```bash
# In WSL2 terminal
cd /home/your-username
mkdir blackcnote
cp -r /mnt/c/Users/CASH\ AMERICA\ PAWN/Desktop/BlackCnote/* ./blackcnote/
```

### **Step 5: Start Containers with WSL2**
```bash
cd blackcnote
docker-compose -f config/docker/docker-compose.yml up -d
```

---

## **VERIFICATION CHECKLIST**

### **✅ WSL2 Setup**
- [ ] WSL2 features enabled
- [ ] Ubuntu distribution installed
- [ ] WSL2 set as default
- [ ] Docker Desktop configured for WSL2

### **✅ Project Migration**
- [ ] Project moved to WSL2 file system
- [ ] Docker Compose updated for WSL2
- [ ] Volume mounts optimized

### **✅ Performance Verification**
- [ ] File operations are faster
- [ ] Volume sync works correctly
- [ ] Live editing functions properly
- [ ] No more 403 Forbidden errors

### **✅ WordPress Functionality**
- [ ] WordPress accessible at http://localhost:8888
- [ ] Admin panel working at http://localhost:8888/wp-admin/
- [ ] BlackCnote theme available and activatable
- [ ] All development tools accessible

---

## **BENEFITS AFTER WSL2 IMPLEMENTATION**

1. **Volume Sync Fixed:** No more missing files in containers
2. **Better Performance:** Faster file I/O and container operations
3. **Live Editing:** Real-time file changes reflected in containers
4. **Linux Compatibility:** Full Linux kernel support
5. **Memory Efficiency:** Better resource management
6. **Development Workflow:** Smoother development experience

---

## **NEXT STEPS**

After WSL2 implementation:

1. **Test WordPress accessibility**
2. **Activate BlackCnote theme**
3. **Verify live editing functionality**
4. **Begin development with full performance**
5. **Monitor system resources and performance**

---

## **SUPPORT**

If you encounter issues:

1. **Check WSL2 status:** `wsl --status`
2. **Check Docker Desktop logs**
3. **Verify WSL2 integration settings**
4. **Check file permissions in containers**
5. **Monitor system resources**

The WSL2 backend will resolve your Docker volume sync issues and provide a much better development experience for your BlackCnote project. 