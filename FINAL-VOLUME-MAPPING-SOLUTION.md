# BlackCnote Volume Mapping - Final Solution

## Issue Summary
The Docker Desktop/WSL2 volume mapping has been experiencing issues where WordPress core files are not properly mounted into containers, resulting in 500 errors and missing files like `wp-blog-header.php`.

## Root Cause
Docker Desktop's WSL2 integration has inconsistent volume mapping behavior, especially when running Docker Compose from within WSL2 while trying to mount WSL2 filesystem paths.

## Solutions

### Solution 1: Windows-Based Docker Compose (Recommended)
Use the Windows filesystem directly instead of WSL2 for volume mapping:

```powershell
# Run from Windows PowerShell in the project root
docker-compose -f docker-compose-windows.yml up -d
```

**Advantages:**
- Most reliable volume mapping
- Works consistently across different Docker Desktop versions
- No WSL2 integration issues

### Solution 2: WSL2 with Windows Path Format
Use Windows path format from within WSL2:

```bash
# Run from WSL2
docker-compose -f config/docker/docker-compose.yml up -d
```

**Configuration:**
- Uses `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote:/var/www/html`
- Requires Docker Desktop to have access to Windows filesystem

### Solution 3: Automated Troubleshooting
Use the enhanced automation script that tries multiple approaches:

```powershell
powershell -ExecutionPolicy Bypass -File automate-ubuntu-setup.ps1
```

**Features:**
- Tests WSL2 volume mapping first
- Falls back to Windows Docker Compose if WSL2 fails
- Provides comprehensive error reporting
- Automatic troubleshooting guidance

## File Structure

### Main Files
- `docker-compose-windows.yml` - Windows-optimized configuration
- `config/docker/docker-compose.yml` - WSL2 configuration (updated)
- `automate-ubuntu-setup.ps1` - Enhanced automation script
- `setup-blackcnote-wsl2.sh` - Ubuntu setup script
- `test-volume-mapping.sh` - Volume mapping diagnostic tool

### Key Changes Made
1. **Multiple volume mapping formats** tested and implemented
2. **Fallback mechanisms** for different Docker Desktop configurations
3. **Comprehensive testing** to identify working configurations
4. **Automated troubleshooting** with multiple solution attempts

## Usage Instructions

### For Windows Users (Recommended)
```powershell
# Navigate to project directory
cd "C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote"

# Use Windows Docker Compose
docker-compose -f docker-compose-windows.yml up -d

# Check status
docker-compose -f docker-compose-windows.yml ps

# Access WordPress
# http://localhost:8888
```

### For WSL2 Users
```bash
# Navigate to project directory
cd ~/blackcnote

# Use WSL2 Docker Compose
docker-compose -f config/docker/docker-compose.yml up -d

# Check status
docker-compose -f config/docker/docker-compose.yml ps

# Access WordPress
# http://localhost:8888
```

### For Automated Setup
```powershell
# Run the comprehensive automation script
powershell -ExecutionPolicy Bypass -File automate-ubuntu-setup.ps1
```

## Troubleshooting

### If WordPress Returns 500 Error
1. **Check volume mapping:**
   ```bash
   docker exec blackcnote-wordpress ls -la /var/www/html/wp-blog-header.php
   ```

2. **If file is missing:**
   - Try Windows Docker Compose: `docker-compose -f docker-compose-windows.yml up -d`
   - Restart Docker Desktop
   - Check Docker Desktop WSL2 integration settings

3. **If still not working:**
   - Use the automation script which tries multiple approaches
   - Check Docker Desktop logs for WSL2 integration issues

### If Containers Won't Start
1. **Check Docker Desktop status:**
   ```powershell
   docker info
   ```

2. **Verify WSL2 integration:**
   - Open Docker Desktop settings
   - Ensure "Use the WSL 2 based engine" is enabled
   - Ensure "Enable integration with my default WSL distro" is checked

3. **Restart services:**
   ```powershell
   # Restart Docker Desktop
   # Then restart WSL2
   wsl --shutdown
   wsl
   ```

## Service URLs
- **WordPress:** http://localhost:8888
- **WordPress Admin:** http://localhost:8888/wp-admin/
- **phpMyAdmin:** http://localhost:8080
- **MailHog:** http://localhost:8025
- **Redis Commander:** http://localhost:8081
- **React App:** http://localhost:5174
- **Browsersync:** http://localhost:3000

## Performance Notes
- Windows filesystem volume mapping may be slower than WSL2
- For development, consider using the Windows approach for reliability
- For production, use the optimized WSL2 configuration once volume mapping is confirmed working

## Support
If volume mapping issues persist:
1. Update Docker Desktop to the latest version
2. Ensure WSL2 is properly configured
3. Try running Docker commands from Windows PowerShell instead of WSL2
4. Use the Windows Docker Compose file as the primary solution 