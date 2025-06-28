# BlackCnote Directory Structure (EXCLUSIVE WP-CONTENT USAGE)

## 🚩 **EXCLUSIVE WordPress Content Directory**

**All WordPress content for the BlackCnote project is served exclusively from:**

```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content
```

- This is the **only** valid wp-content directory for themes, plugins, uploads, and customizations.
- All Docker, Nginx, and WordPress configuration is mapped to this directory.
- Do **not** use any other wp-content or WordPress directory (such as ./wordpress/wp-content).

## 📦 **Docker & Deployment**
- The Docker Compose configuration mounts `./blackcnote` as `/var/www/html` in the WordPress container.
- The `wp-config.php` used is `./blackcnote/wp-config.php`.
- Nginx and all monitoring tools reference only the `blackcnote/wp-content` directory.

## 🛠️ **Development**
- Place all custom themes in `blackcnote/wp-content/themes/`
- Place all custom plugins in `blackcnote/wp-content/plugins/`
- All uploads and media are stored in `blackcnote/wp-content/uploads/`

## 📝 **Testing & Debugging**
- The debug system and metrics exporter monitor the `blackcnote/wp-content` directory exclusively.
- Any changes to this directory are logged and monitored in real time.

## ❌ **Deprecated/Unused**
- The `wordpress/` directory and any other `wp-content` directories are **not used** and should be ignored for all development and deployment purposes.

## ✅ **Summary**
- **Always use:** `blackcnote/wp-content` for all WordPress content.
- **Never use:** `wordpress/wp-content` or any other path.

---

**This is a core architectural decision for the BlackCnote project. All future development, deployment, and documentation must adhere to this exclusive directory structure.**

## **CRITICAL DIRECTORY STRUCTURE**

### **Project Root Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\
```
This is your **main project directory** containing all BlackCnote files, Docker configurations, and development tools.

### **WordPress Installation Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\
```
This is your **WordPress installation directory** that contains the complete WordPress core files and your custom theme.

### **Theme Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\
```
This is your **custom BlackCnote theme directory** containing all theme files.

---

## **Docker Volume Mapping Configuration**

### **Current Docker Compose Configuration**
```yaml
volumes:
  # Live editing - WordPress core files (main mount)
  - "../blackcnote:/var/www/html"
  # Development tools and scripts
  - "./scripts:/var/www/html/scripts:delegated"
  # Logs for debugging
  - "./logs:/var/www/html/logs:delegated"
```

### **What This Means**
- Your local `blackcnote` directory is mounted to `/var/www/html` in the Docker container
- This enables **live editing** - any changes you make to files in your local `blackcnote` directory are immediately reflected in the running WordPress container
- The `scripts` and `logs` directories are also mounted for development tools

---

## **Required WordPress Core Files**

Your `blackcnote` directory **MUST** contain these essential WordPress files:

### **Core Files**
- `index.php` - WordPress entry point
- `wp-config.php` - WordPress configuration
- `wp-load.php` - WordPress loader
- `wp-settings.php` - WordPress settings
- `wp-links-opml.php` - Links OPML

### **Core Directories**
- `wp-admin/` - WordPress administration files
- `wp-includes/` - WordPress core includes
- `wp-content/` - Themes, plugins, and uploads

### **Theme Directory**
- `wp-content/themes/blackcnote/` - Your custom BlackCnote theme

---

## **Verification Steps**

### **1. Check Local Directory Structure**
```bash
# Verify all required directories exist
dir blackcnote\wp-admin
dir blackcnote\wp-content
dir blackcnote\wp-includes
dir blackcnote\wp-content\themes\blackcnote
```

### **2. Check Container Directory Structure**
```bash
# Verify Docker container has all directories
docker exec blackcnote-wordpress ls -la /var/www/html
docker exec blackcnote-wordpress ls -la /var/www/html/wp-admin
docker exec blackcnote-wordpress ls -la /var/www/html/wp-content/themes
```

### **3. Test WordPress Accessibility**
```bash
# Test if WordPress is accessible
Invoke-WebRequest -Uri "http://localhost:8888" -Method Head
```

---

## **Troubleshooting Docker Volume Sync Issues**

### **Common Issues on Windows**
1. **Missing directories in container**: Docker Desktop on Windows sometimes fails to sync large directories
2. **Permission issues**: Windows file permissions can interfere with Docker volume mounting
3. **Path length issues**: Windows has path length limitations

### **Solutions**

#### **Solution 1: Restart Docker Desktop**
1. Right-click Docker Desktop icon in system tray
2. Select "Restart Docker Desktop"
3. Wait for Docker to fully restart
4. Restart containers:
   ```bash
   docker-compose -f config/docker/docker-compose.yml down
   docker-compose -f config/docker/docker-compose.yml up -d
   ```

#### **Solution 2: Force Volume Refresh**
1. Stop all containers
2. Remove Docker volumes:
   ```bash
   docker volume prune
   ```
3. Restart containers

#### **Solution 3: Check File Sharing Settings**
1. Open Docker Desktop
2. Go to Settings > Resources > File Sharing
3. Ensure your project directory is in the shared paths
4. Apply & Restart

---

## **Live Editing Workflow**

### **How to Edit Your Theme**
1. **Edit files in**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\`
2. **Changes are automatically reflected** in the running WordPress container
3. **View changes at**: `http://localhost:8888`

### **How to Edit WordPress Core Files**
1. **Edit files in**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\`
2. **Changes are automatically reflected** in the running WordPress container
3. **Be careful** - editing core files can break WordPress functionality

### **How to Edit Plugins**
1. **Edit files in**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\plugins\`
2. **Changes are automatically reflected** in the running WordPress container

---

## **Development Tools**

### **Available Services**
- **WordPress**: `http://localhost:8888`
- **WordPress Admin**: `http://localhost:8888/wp-admin/`
- **phpMyAdmin**: `http://localhost:8080`
- **React Dev Server**: `http://localhost:5174`
- **MailHog**: `http://localhost:8025`
- **Redis Commander**: `http://localhost:8081`
- **Browsersync**: `http://localhost:3000`

### **Useful Commands**
```bash
# View container logs
docker logs blackcnote-wordpress

# Access WordPress container shell
docker exec -it blackcnote-wordpress bash

# Check container status
docker-compose -f config/docker/docker-compose.yml ps

# Restart specific service
docker-compose -f config/docker/docker-compose.yml restart wordpress
```

---

## **Important Notes**

1. **Never edit files directly in the Docker container** - always edit in your local directory
2. **The `blackcnote` directory is your single source of truth** for all WordPress files
3. **Docker Desktop on Windows** may have sync issues - restart Docker Desktop if you encounter problems
4. **Always backup your work** before making major changes
5. **Test changes in the browser** at `http://localhost:8888` after making edits

---

## **Next Steps**

1. **Verify your directory structure** matches the requirements above
2. **Restart Docker Desktop** if you're experiencing sync issues
3. **Test WordPress accessibility** at `http://localhost:8888`
4. **Activate your BlackCnote theme** in WordPress admin
5. **Begin development** with live editing capabilities 