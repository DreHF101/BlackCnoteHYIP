# BlackCnote Canonical Pathways Enforcement

## 🚨 **CRITICAL - ENFORCEMENT POLICY** 🚨

**This document enforces the use of canonical pathways for ALL BlackCnote development, deployment, and documentation. Violation of these pathways will result in loading issues and system failures.**

---

## **📋 Enforcement Rules**

### **1. MANDATORY CANONICAL PATHS**

**ALL BlackCnote development MUST use these exact paths:**

```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\                    # PROJECT ROOT
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\         # WORDPRESS INSTALLATION
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\ # WORDPRESS CONTENT
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\ # THEME FILES
```

### **2. MANDATORY SERVICE URLS**

**ALL BlackCnote services MUST use these exact URLs:**

```
http://localhost:8888    # WordPress Frontend
http://localhost:8888/wp-admin/    # WordPress Admin
http://localhost:5174    # React App
http://localhost:8080    # phpMyAdmin
http://localhost:8081    # Redis Commander
http://localhost:8025    # MailHog
http://localhost:3000    # Browsersync
http://localhost:9229    # Dev Tools
```

### **3. PROHIBITED PATHS**

**NEVER use these deprecated or invalid paths:**

- ❌ `wordpress/wp-content/`
- ❌ `wp-content/` (root level)
- ❌ Any other `wp-content` directories outside `blackcnote/`
- ❌ Hardcoded paths that don't match canonical structure

---

## **🔧 Enforcement Mechanisms**

### **1. Automated Path Verification**

```powershell
# Run this script to verify canonical paths
scripts\verify-canonical-paths.ps1

# Expected output: All paths must be ✅ Valid
```

### **2. Docker Configuration Enforcement**

```yaml
# Docker Compose MUST use these volume mappings
volumes:
  - "C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote:/var/www/html:delegated"
  - "./scripts:/var/www/html/scripts:delegated"
  - "./logs:/var/www/html/logs:delegated"
```

### **3. WordPress Configuration Enforcement**

```php
// WordPress MUST use these constants
define('BLACKCNOTE_THEME_DIR', get_template_directory());
define('BLACKCNOTE_THEME_URI', get_template_directory_uri());
define('BLACKCNOTE_ASSETS_URI', BLACKCNOTE_THEME_URI . '/assets');

// File includes MUST use WordPress functions
require_once BLACKCNOTE_THEME_DIR . '/inc/blackcnote-react-loader.php';
```

### **4. Service URL Enforcement**

```php
// Service URLs MUST be defined as constants
define('BLACKCNOTE_WORDPRESS_URL', 'http://localhost:8888');
define('BLACKCNOTE_REACT_URL', 'http://localhost:5174');
define('BLACKCNOTE_PHPMYADMIN_URL', 'http://localhost:8080');
define('BLACKCNOTE_MAILHOG_URL', 'http://localhost:8025');
define('BLACKCNOTE_REDIS_COMMANDER_URL', 'http://localhost:8081');
define('BLACKCNOTE_BROWSERSYNC_URL', 'http://localhost:3000');
define('BLACKCNOTE_DEV_TOOLS_URL', 'http://localhost:9229');
```

---

## **🚫 Violation Consequences**

### **1. Development Issues**
- ❌ File not found errors
- ❌ Theme loading failures
- ❌ Plugin conflicts
- ❌ Asset loading issues
- ❌ Database connection problems

### **2. Deployment Issues**
- ❌ Production failures
- ❌ Service unavailability
- ❌ Path resolution errors
- ❌ Container startup failures

### **3. Maintenance Issues**
- ❌ Debug system failures
- ❌ Log file access problems
- ❌ Backup/restore failures
- ❌ Update conflicts

---

## **✅ Compliance Checklist**

### **Before Development**
- [ ] Confirm working in `blackcnote/wp-content/themes/blackcnote/`
- [ ] Verify Docker containers use canonical paths
- [ ] Check all includes use WordPress functions
- [ ] Ensure service URLs match canonical list
- [ ] Run path verification script

### **Before Committing**
- [ ] No hardcoded paths to deprecated directories
- [ ] All includes use WordPress functions or canonical paths
- [ ] Documentation references correct paths
- [ ] Scripts use canonical paths
- [ ] All service URLs use canonical localhost pathways

### **Before Deployment**
- [ ] Verify packaging scripts use canonical paths
- [ ] Check Docker volume mappings
- [ ] Confirm WordPress configuration points to correct directories
- [ ] Test that all features work with canonical paths
- [ ] Verify all service connections are maintained

---

## **🔍 Verification Scripts**

### **1. Path Verification**
```powershell
# Verify all canonical paths exist
scripts\verify-canonical-paths.ps1
```

### **2. Service Verification**
```powershell
# Verify all services are accessible
scripts\test-service-connectivity.ps1
```

### **3. Docker Verification**
```powershell
# Verify Docker configuration
scripts\verify-docker-config.ps1
```

### **4. WordPress Verification**
```powershell
# Verify WordPress configuration
scripts\verify-wordpress-config.ps1
```

---

## **📚 Documentation Requirements**

### **1. All Documentation MUST Include**
- Canonical path references
- Correct service URLs
- WordPress function usage examples
- Docker configuration examples

### **2. Code Comments MUST Include**
- Path explanations
- Service URL references
- WordPress function documentation
- Dependency information

### **3. Scripts MUST Include**
- Canonical path validation
- Service URL verification
- Error handling for path issues
- Clear documentation of path usage

---

## **🛠️ Enforcement Tools**

### **1. Automated Enforcement**
- Path verification scripts
- Service connectivity tests
- Docker configuration validation
- WordPress configuration checks

### **2. Manual Enforcement**
- Code review requirements
- Documentation review
- Testing procedures
- Deployment validation

### **3. Monitoring Enforcement**
- Debug system monitoring
- Log file analysis
- Service health checks
- Path usage tracking

---

## **🚨 Emergency Procedures**

### **1. Path Violation Detection**
1. **Identify the violation**: Check error logs and debug output
2. **Locate the source**: Find where non-canonical paths are used
3. **Correct the paths**: Update to use canonical paths
4. **Test the fix**: Verify functionality with canonical paths
5. **Document the change**: Update documentation and comments

### **2. Service URL Violation Detection**
1. **Identify the violation**: Check service connectivity
2. **Locate the source**: Find where non-canonical URLs are used
3. **Correct the URLs**: Update to use canonical URLs
4. **Test the fix**: Verify service connectivity
5. **Document the change**: Update documentation and constants

### **3. Docker Configuration Violation**
1. **Identify the violation**: Check Docker volume mappings
2. **Locate the source**: Find incorrect volume configurations
3. **Correct the mappings**: Update to use canonical paths
4. **Test the fix**: Verify container functionality
5. **Document the change**: Update Docker configuration files

---

## **📋 Compliance Reporting**

### **1. Daily Compliance Check**
```powershell
# Run daily compliance check
scripts\daily-compliance-check.ps1
```

### **2. Weekly Compliance Report**
```powershell
# Generate weekly compliance report
scripts\weekly-compliance-report.ps1
```

### **3. Monthly Compliance Audit**
```powershell
# Perform monthly compliance audit
scripts\monthly-compliance-audit.ps1
```

---

## **🎯 Success Metrics**

### **1. Path Compliance**
- ✅ 100% canonical path usage
- ✅ 0% deprecated path references
- ✅ 100% WordPress function usage
- ✅ 0% hardcoded path violations

### **2. Service Compliance**
- ✅ 100% canonical URL usage
- ✅ 100% service accessibility
- ✅ 0% URL hardcoding violations
- ✅ 100% service health status

### **3. Configuration Compliance**
- ✅ 100% Docker configuration compliance
- ✅ 100% WordPress configuration compliance
- ✅ 0% configuration violations
- ✅ 100% deployment success rate

---

## **📞 Support and Escalation**

### **1. Path Issues**
- **Contact**: BlackCnote Development Team
- **Procedure**: Run verification scripts and check canonical paths
- **Resolution**: Update to use canonical paths

### **2. Service Issues**
- **Contact**: BlackCnote DevOps Team
- **Procedure**: Check service connectivity and canonical URLs
- **Resolution**: Update to use canonical URLs

### **3. Configuration Issues**
- **Contact**: BlackCnote Infrastructure Team
- **Procedure**: Verify Docker and WordPress configuration
- **Resolution**: Update to use canonical configuration

---

## **📝 Change Management**

### **1. Path Changes**
- **Requirement**: Must maintain canonical structure
- **Approval**: BlackCnote Development Team
- **Documentation**: Update all relevant documentation
- **Testing**: Verify all functionality with new paths

### **2. Service Changes**
- **Requirement**: Must maintain canonical URLs
- **Approval**: BlackCnote DevOps Team
- **Documentation**: Update service registry
- **Testing**: Verify all service connectivity

### **3. Configuration Changes**
- **Requirement**: Must maintain canonical configuration
- **Approval**: BlackCnote Infrastructure Team
- **Documentation**: Update configuration files
- **Testing**: Verify all deployment scenarios

---

**Last Updated**: December 2024  
**Version**: 2.0  
**Status**: ✅ **ACTIVE ENFORCEMENT - ALL CANONICAL PATHWAYS MANDATORY** 