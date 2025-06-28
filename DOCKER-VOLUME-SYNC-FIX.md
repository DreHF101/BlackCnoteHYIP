# Docker Volume Sync Fix for BlackCnote

## **CRITICAL ISSUE IDENTIFIED**

Your Docker container is missing essential WordPress files due to Docker Desktop volume sync issues on Windows. The container only has:
- `wp-config.php`
- `wp-includes/`
- `wp-links-opml.php`
- `logs/`
- `scripts/`

**Missing critical files:**
- `index.php` (WordPress entry point)
- `wp-admin/` (WordPress administration)
- `wp-content/` (Themes and plugins)

---

## **ROOT CAUSE**

Docker Desktop on Windows has known issues with volume mounting, especially when:
1. Large directories are involved
2. Files were copied/created while containers were running
3. Windows file permissions interfere with mounting
4. Path length limitations are exceeded

---

## **SOLUTION STEPS**

### **Step 1: Restart Docker Desktop**
1. **Right-click** the Docker Desktop icon in your system tray
2. **Select "Restart Docker Desktop"**
3. **Wait** for Docker to fully restart (this may take 2-3 minutes)
4. **Verify** Docker is running by opening Docker Desktop

### **Step 2: Stop All Containers**
```bash
docker-compose -f config/docker/docker-compose.yml down
```

### **Step 3: Clear Docker Cache**
```bash
# Remove unused volumes
docker volume prune -f

# Remove unused containers
docker container prune -f

# Remove unused images (optional)
docker image prune -f
```

### **Step 4: Verify Local Directory Structure**
```bash
# Check that all required files exist locally
dir blackcnote\index.php
dir blackcnote\wp-admin
dir blackcnote\wp-content
dir blackcnote\wp-content\themes\blackcnote
```

### **Step 5: Start Containers with Fresh Mount**
```bash
docker-compose -f config/docker/docker-compose.yml up -d
```

### **Step 6: Verify Container Directory Structure**
```bash
# Check if all directories are now present
docker exec blackcnote-wordpress ls -la /var/www/html
docker exec blackcnote-wordpress ls -la /var/www/html/wp-admin
docker exec blackcnote-wordpress ls -la /var/www/html/wp-content/themes
```

### **Step 7: Test WordPress Accessibility**
```bash
# Test if WordPress is now accessible
Invoke-WebRequest -Uri "http://localhost:8888" -Method Head
```

---

## **ALTERNATIVE SOLUTIONS**

### **Solution A: Use Docker Desktop File Sharing Settings**
1. **Open Docker Desktop**
2. **Go to Settings > Resources > File Sharing**
3. **Add your project directory**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote`
4. **Click "Apply & Restart"**
5. **Restart containers**

### **Solution B: Use WSL2 Backend (Recommended)**
1. **Open Docker Desktop**
2. **Go to Settings > General**
3. **Check "Use the WSL 2 based engine"**
4. **Go to Settings > Resources > WSL Integration**
5. **Enable integration with your WSL2 distribution**
6. **Apply & Restart**

### **Solution C: Manual File Copy (Temporary Fix)**
If volume mounting continues to fail, you can manually copy files:

```bash
# Copy missing files to container
docker cp blackcnote/index.php blackcnote-wordpress:/var/www/html/
docker cp blackcnote/wp-admin blackcnote-wordpress:/var/www/html/
docker cp blackcnote/wp-content blackcnote-wordpress:/var/www/html/
```

---

## **VERIFICATION CHECKLIST**

After applying the fix, verify these items:

### **✅ Local Directory Structure**
- [ ] `blackcnote/index.php` exists
- [ ] `blackcnote/wp-admin/` directory exists
- [ ] `blackcnote/wp-content/` directory exists
- [ ] `blackcnote/wp-content/themes/blackcnote/` directory exists

### **✅ Container Directory Structure**
- [ ] `docker exec blackcnote-wordpress ls -la /var/www/html` shows all files
- [ ] `docker exec blackcnote-wordpress ls -la /var/www/html/wp-admin` shows admin files
- [ ] `docker exec blackcnote-wordpress ls -la /var/www/html/wp-content/themes` shows themes

### **✅ WordPress Accessibility**
- [ ] `http://localhost:8888` returns 200 OK (not 403 Forbidden)
- [ ] WordPress admin is accessible at `http://localhost:8888/wp-admin/`
- [ ] No "No matching DirectoryIndex" errors in logs

### **✅ Theme Availability**
- [ ] BlackCnote theme appears in WordPress admin > Appearance > Themes
- [ ] Theme can be activated without errors

---

## **PREVENTION MEASURES**

### **Best Practices for Windows Docker Development**
1. **Always restart Docker Desktop** after making large file changes
2. **Use WSL2 backend** for better performance and reliability
3. **Keep project paths short** to avoid Windows path length limitations
4. **Avoid editing files while containers are starting/stopping**
5. **Use Docker Desktop's file sharing settings** to explicitly allow your project directory

### **Development Workflow**
1. **Make changes** in your local `blackcnote` directory
2. **Wait for Docker to sync** (usually instant, but may take a few seconds)
3. **Test changes** in browser at `http://localhost:8888`
4. **If changes don't appear**, restart Docker Desktop and containers

---

## **TROUBLESHOOTING**

### **If Volume Mounting Still Fails**
1. **Check Windows Defender** - it may be blocking Docker access
2. **Run Docker Desktop as Administrator**
3. **Check Windows file permissions** on your project directory
4. **Consider moving project** to a shorter path (e.g., `C:\blackcnote\`)

### **If WordPress Still Shows 403 Forbidden**
1. **Check file permissions** in the container
2. **Verify Apache configuration** is correct
3. **Check if SELinux** is interfering (if using WSL2)

### **If Theme Doesn't Appear**
1. **Verify theme directory structure** is correct
2. **Check theme files** have proper permissions
3. **Clear WordPress cache** if using caching plugins

---

## **NEXT STEPS**

Once the volume sync issue is resolved:

1. **Access WordPress admin** at `http://localhost:8888/wp-admin/`
2. **Activate your BlackCnote theme** in Appearance > Themes
3. **Begin development** with live editing capabilities
4. **Test all functionality** to ensure everything works correctly

---

## **SUPPORT**

If you continue to experience issues:
1. **Check Docker Desktop logs** for detailed error messages
2. **Verify your Windows version** is compatible with Docker Desktop
3. **Consider using WSL2** for better Docker performance on Windows
4. **Contact Docker support** if issues persist 