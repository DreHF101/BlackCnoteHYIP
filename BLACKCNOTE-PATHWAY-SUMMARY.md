# BlackCnote Pathway Summary

## 🎯 **CANONICAL PATHWAYS - FINAL SUMMARY**

**This document provides a complete summary of all canonical pathways for the BlackCnote project. All development, deployment, and documentation MUST use these pathways.**

---

## **🏠 Primary Canonical Pathways**

### **1. BlackCnote Project Root Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\
```
- **Status**: ✅ **CANONICAL PROJECT ROOT**
- **Purpose**: Main development and deployment directory
- **Usage**: All Docker operations, script execution, and development work

### **2. BlackCnote Theme Directory (WordPress Installation)**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\
```
- **Status**: ✅ **CANONICAL WORDPRESS INSTALLATION**
- **Purpose**: Complete WordPress installation
- **Usage**: WordPress core files, configuration, and administration

### **3. WordPress Content Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\
```
- **Status**: ✅ **CANONICAL WP-CONTENT DIRECTORY**
- **Purpose**: All WordPress content (themes, plugins, uploads)
- **Usage**: EXCLUSIVE wp-content directory for BlackCnote

### **4. BlackCnote Theme Files Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\
```
- **Status**: ✅ **CANONICAL THEME DIRECTORY**
- **Purpose**: Custom BlackCnote theme files
- **Usage**: Theme development and customization

---

## **📋 Quick Reference Table**

| **Component** | **Canonical Path** | **Status** |
|---------------|-------------------|------------|
| **Project Root** | `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\` | ✅ **CANONICAL** |
| **WordPress Installation** | `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\` | ✅ **CANONICAL** |
| **WordPress Content** | `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\` | ✅ **CANONICAL** |
| **Theme Files** | `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\` | ✅ **CANONICAL** |
| **Plugins** | `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\plugins\` | ✅ **CANONICAL** |
| **Uploads** | `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\uploads\` | ✅ **CANONICAL** |
| **Logs** | `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\logs\` | ✅ **CANONICAL** |

---

## **🚫 DEPRECATED/INVALID PATHS**

**NEVER use these paths for BlackCnote development:**

- ❌ `wordpress/wp-content/` - Legacy directory, not used
- ❌ `wp-content/` (root level) - Not the canonical directory
- ❌ Any other `wp-content` directories outside the `blackcnote/` folder
- ❌ Any paths that don't start with the canonical project root

---

## **✅ CORRECT USAGE EXAMPLES**

### **Development Scripts**
```powershell
# ✅ CORRECT - Use canonical project root
$ProjectRoot = "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"
$ThemePath = "$ProjectRoot\blackcnote\wp-content\themes\blackcnote"
$OutputFile = "$ProjectRoot\BlackCnote-Theme-Complete.zip"
```

### **Docker Configuration**
```yaml
# ✅ CORRECT - Volume mapping from canonical paths
volumes:
  - "C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote:/var/www/html:delegated"
  - "./scripts:/var/www/html/scripts:delegated"
  - "./logs:/var/www/html/logs:delegated"
```

### **WordPress Functions**
```php
// ✅ CORRECT - Use WordPress functions
define('BLACKCNOTE_THEME_DIR', get_template_directory());
define('BLACKCNOTE_THEME_URI', get_template_directory_uri());
require_once BLACKCNOTE_THEME_DIR . '/inc/blackcnote-react-loader.php';
```

---

## **🔧 Configuration Files Status**

| **File** | **Location** | **Status** | **Canonical Paths** |
|----------|--------------|------------|-------------------|
| `docker-compose.yml` | `config/docker/` | ✅ **UPDATED** | ✅ **DOCUMENTED** |
| `create-blackcnote-zip.ps1` | Project Root | ✅ **UPDATED** | ✅ **DOCUMENTED** |
| `create-blackcnote-zip.bat` | Project Root | ✅ **UPDATED** | ✅ **DOCUMENTED** |
| `blackcnote-debug-daemon.php` | `bin/` | ✅ **UPDATED** | ✅ **DOCUMENTED** |
| `README.md` | Project Root | ✅ **UPDATED** | ✅ **DOCUMENTED** |

---

## **📚 Documentation Files**

| **Document** | **Purpose** | **Status** |
|--------------|-------------|------------|
| `BLACKCNOTE-CANONICAL-PATHS.md` | Complete canonical path documentation | ✅ **CREATED** |
| `BLACKCNOTE-PROJECT-ROOT.md` | Project root directory information | ✅ **CREATED** |
| `BLACKCNOTE-DIRECTORY-STRUCTURE.md` | Complete project structure | ✅ **EXISTS** |
| `README.md` | Main project overview | ✅ **UPDATED** |

---

## **🚀 Development Workflow**

### **Starting Development**
```bash
# 1. Navigate to canonical project root
cd "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"

# 2. Start Docker environment
docker-compose -f config/docker/docker-compose.yml up -d

# 3. Access development environment
# WordPress: http://localhost:8888
# React App: http://localhost:5174
# PHPMyAdmin: http://localhost:8080
```

### **Running Scripts**
```bash
# All scripts run from canonical project root
.\scripts\setup\environment-setup.ps1
.\scripts\deployment\deploy-production.ps1
.\bin\blackcnote-debug-daemon.php
```

### **Docker Operations**
```bash
# All Docker operations from canonical project root
docker-compose -f config/docker/docker-compose.yml up -d
docker-compose -f config/docker/docker-compose.yml down
docker-compose -f config/docker/docker-compose.yml logs
```

---

## **📋 Verification Checklist**

### **Before Development**
- [ ] Confirm you're in canonical project root: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\`
- [ ] Verify Docker containers are using canonical paths
- [ ] Check that debug system is monitoring correct directory
- [ ] Ensure all scripts reference canonical paths

### **Before Deployment**
- [ ] Verify packaging scripts use canonical paths
- [ ] Check Docker volume mappings
- [ ] Confirm WordPress configuration points to correct directories
- [ ] Test that all features work with canonical paths

### **Before Committing**
- [ ] No hardcoded paths to deprecated directories
- [ ] All includes use WordPress functions or canonical paths
- [ ] Documentation references correct paths
- [ ] Scripts use canonical paths

---

## **🎯 Key Principles**

### **1. Canonical Project Root**
- **ALWAYS** start development from the canonical project root
- **NEVER** work from subdirectories without returning to root
- **ALWAYS** run scripts from the canonical project root

### **2. Canonical WordPress Paths**
- **ALWAYS** use `blackcnote/wp-content` for WordPress content
- **NEVER** use `wordpress/wp-content` or any other directory
- **ALWAYS** use WordPress functions for path resolution

### **3. Canonical Documentation**
- **ALWAYS** reference canonical paths in documentation
- **NEVER** use non-canonical paths in guides or tutorials
- **ALWAYS** update documentation when paths change

---

## **📞 Support & Troubleshooting**

### **Path-Related Issues**
1. **Check this summary** for correct canonical paths
2. **Verify Docker configuration** in `config/docker/docker-compose.yml`
3. **Review debug logs** in `blackcnote/wp-content/logs/`
4. **Check script documentation** for path references

### **Common Solutions**
- **Scripts not found**: Ensure you're in canonical project root
- **Docker errors**: Verify you're running from canonical project root
- **Path issues**: Check canonical path documentation
- **Configuration errors**: Verify file locations relative to canonical paths

---

## **✅ Final Status**

**All BlackCnote project components now use canonical pathways:**

- ✅ **Project Root**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\`
- ✅ **WordPress Installation**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\`
- ✅ **WordPress Content**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\`
- ✅ **Theme Files**: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\`
- ✅ **Documentation**: All canonical paths documented
- ✅ **Scripts**: All scripts use canonical paths
- ✅ **Docker**: All containers use canonical paths
- ✅ **Debug System**: Monitors canonical paths exclusively
- ✅ **XAMPP Scripts**: Updated to use canonical paths
- ✅ **Development Tools**: All tools use canonical paths

**Pathway Review Status**: ✅ **COMPLETE - 100% CANONICAL COMPLIANCE**

---

**Last Updated**: December 2024  
**Version**: 1.0  
**Status**: ✅ **COMPLETE - ALL CANONICAL PATHWAYS ESTABLISHED AND VERIFIED** 