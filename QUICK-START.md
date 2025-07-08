# BlackCnote Quick Start Guide

## 🚀 **Automated Development Environment**

### **One-Command Start**
From the project root directory, simply run:
```bash
npm run dev:full
```

This will automatically:
- ✅ Navigate to the correct directory
- ✅ Check dependencies
- ✅ Start React development server
- ✅ Start Browsersync for live reloading

### **Alternative Commands**

#### **From Root Directory:**
```bash
# Start full development environment (recommended)
npm run dev:full

# Start just React development
npm run dev:react

# Start just WordPress/Docker services
npm run dev:wordpress

# Quick start (same as dev:full)
npm start
```

#### **From React App Directory:**
```bash
cd react-app

# Start full development environment
npm run dev:full

# Start just React dev server
npm run dev

# Start just Browsersync
npm run dev:sync

# Start both React and Browsersync
npm run dev:both
```

### **Development URLs**

Once started, access your development environment at:

| Service | URL | Description |
|---------|-----|-------------|
| **React App** | http://localhost:5174 | Main React development server |
| **Browsersync** | http://localhost:3000 | Live reloading proxy |
| **WordPress** | http://localhost:8888 | WordPress frontend |
| **WordPress Admin** | http://localhost:8888/wp-admin/ | WordPress admin panel |
| **phpMyAdmin** | http://localhost:8080 | Database management |
| **Redis Commander** | http://localhost:8081 | Cache management |
| **MailHog** | http://localhost:8025 | Email testing |
| **Debug Metrics** | http://localhost:9091 | System metrics |

### **File Structure**

```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\
├── react-app/                    # React development
│   ├── src/                     # React source code
│   ├── package.json             # React dependencies
│   └── npm scripts              # Development commands
├── blackcnote/                  # WordPress installation
│   ├── wp-content/themes/blackcnote/  # BlackCnote theme
│   └── wp-admin/                # WordPress admin
├── start-dev-simple.ps1         # Automated startup script
├── start-dev.bat                # Batch file for easy execution
└── package.json                 # Root package.json with shortcuts
```

### **Troubleshooting**

#### **If you get a port error or lagging server:**
- Run `npx kill-port 5174 3000 3001` to free up ports
- Or manually check: `netstat -ano | findstr :5174` and `taskkill /PID [PID] /F`
- If you see Browsersync running on 3002 or 3003, it means 3000/3001 were busy
- If you get a permission error, try running your terminal as administrator
- If all else fails, reboot your machine

#### **If Docker/WordPress is slow or lagging:**
- Open Docker Desktop → Settings → Resources
- Allocate at least 2 CPUs and 4GB RAM for smooth performance
- Restart Docker after changing resources

#### **If you get "Missing script" error:**
- Make sure you're in the correct directory
- Run `npm run dev:full` from the project root
- The script will automatically navigate to the right location

#### **If React server won't start:**
- Check if port 5174 is already in use
- Run `netstat -ano | findstr :5174` to see what's using it
- Kill the process if needed: `taskkill /PID [PID] /F`

#### **If dependencies are missing:**
- The script will automatically install them
- Or manually run: `cd react-app && npm install`

#### **If Docker services aren't running:**
- Start them with: `npm run dev:wordpress`
- Or manually: `docker compose up -d`

### **Development Workflow**

1. **Start Development Environment:**
   ```bash
   npm run dev:full
   ```

2. **Edit React Code:**
   - Files in `react-app/src/` will hot reload
   - Changes appear instantly in browser

3. **Edit WordPress Theme:**
   - Files in `blackcnote/wp-content/themes/blackcnote/`
   - Changes appear on WordPress site

4. **Stop Development:**
   - Press `Ctrl+C` in the terminal
   - Or close the terminal window

### **Available Scripts Summary**

| Command | Description | Location |
|---------|-------------|----------|
| `npm run dev:full` | Start full dev environment | Root |
| `npm run dev:react` | Start just React | Root |
| `npm run dev:wordpress` | Start just WordPress | Root |
| `npm start` | Quick start (same as dev:full) | Root |
| `npm run dev:full` | Start full dev environment | react-app/ |
| `npm run dev` | Start React dev server | react-app/ |
| `npm run dev:sync` | Start Browsersync | react-app/ |
| `npm run dev:both` | Start React + Browsersync | react-app/ |

### **Performance Tips**

- **Use `npm run dev:full`** for the best development experience
- **Keep Docker containers running** for WordPress development
- **Use Browsersync** for cross-device testing
- **Monitor debug metrics** at http://localhost:9091

---

**🎉 Your BlackCnote development environment is now fully automated!**

Just run `npm run dev:full` from the project root and start coding! 