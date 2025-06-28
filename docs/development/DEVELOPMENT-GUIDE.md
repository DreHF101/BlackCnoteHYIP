# BlackCnote Theme Development Guide

## 🚀 Quick Start

### Daily Development Workflow

1. **Start the development environment:**
   ```powershell
   .\scripts\quick-start.ps1
   ```

2. **Or use the full workflow script:**
   ```powershell
   .\scripts\dev-workflow.ps1 start
   ```

3. **Access your development environment:**
   - **WordPress**: http://localhost:8888
   - **wp-admin**: http://localhost:8888/wp-admin/
   - **React Dev Server**: http://localhost:5174
   - **PHPMyAdmin**: http://localhost:8080

## 🛠️ Development Workflow

### React Development (Local)

The React app runs locally for the best development experience:

```powershell
# Start React development server
cd react-app
npm run dev
```

**Benefits of local React development:**
- ⚡ Instant hot reloading
- 🔍 Better debugging capabilities
- 📦 Direct package management
- 🎯 Faster iteration cycles

### WordPress Integration

The React app builds directly into the WordPress theme:

```powershell
# Build React app for production
cd react-app
npm run build
```

**Build output:** `react-app/blackcnote/dist/` → WordPress theme assets

### Development Scripts

| Command | Description |
|---------|-------------|
| `.\scripts\quick-start.ps1` | Start essential services for daily development |
| `.\scripts\dev-workflow.ps1 start` | Start all services (WordPress + React) |
| `.\scripts\dev-workflow.ps1 dev` | Start React development server only |
| `.\scripts\dev-workflow.ps1 build` | Build React app for production |
| `.\scripts\dev-workflow.ps1 preview` | Build and preview React app |
| `.\scripts\dev-workflow.ps1 status` | Show status of all services |
| `.\scripts\dev-workflow.ps1 stop` | Stop all services |
| `.\scripts\dev-workflow.ps1 clean` | Clean build files |

## 📁 Project Structure

```
BlackCnote/
├── blackcnote/                 # WordPress theme directory
│   ├── wp-content/
│   │   ├── themes/
│   │   │   └── blackcnote/     # Theme files
│   │   ├── plugins/
│   │   └── uploads/
│   └── dist/                   # Built React assets
├── react-app/                  # React development
│   ├── src/                    # React source code
│   ├── public/                 # Static assets
│   └── blackcnote/dist/        # Build output
├── scripts/                    # Development scripts
├── docs/                       # Documentation
└── docker-compose.yml          # Docker services
```

## 🎨 Theme Development Workflow

### 1. React Component Development

1. **Edit React components** in `react-app/src/`
2. **See changes instantly** at http://localhost:5174
3. **Test functionality** in the React dev environment
4. **Build for WordPress** when ready

### 2. WordPress Integration

1. **Build React app:**
   ```powershell
   cd react-app
   npm run build
   ```

2. **Assets are automatically copied** to `blackcnote/dist/`
3. **WordPress serves the built files** from the theme directory
4. **Test in WordPress** at http://localhost:8888

### 3. WordPress Theme Development

1. **Edit PHP files** in `blackcnote/wp-content/themes/blackcnote/`
2. **Edit WordPress templates** and functions
3. **Test WordPress functionality** at http://localhost:8888
4. **Use wp-admin** at http://localhost:8888/wp-admin/

## 🔧 Development Tools

### Available Services

| Service | URL | Purpose |
|---------|-----|---------|
| WordPress | http://localhost:8888 | Main WordPress site |
| wp-admin | http://localhost:8888/wp-admin/ | WordPress administration |
| React Dev | http://localhost:5174 | React development server |
| PHPMyAdmin | http://localhost:8080 | Database management |
| Redis Commander | http://localhost:8081 | Redis cache management |
| MailHog | http://localhost:8025 | Email testing |

### Development Commands

```powershell
# React Development
cd react-app
npm run dev          # Start development server
npm run build        # Build for production
npm run preview      # Preview built app

# WordPress Development
# Edit files in blackcnote/wp-content/themes/blackcnote/
# Changes are reflected immediately

# Database Management
# Access PHPMyAdmin at http://localhost:8080
# Or use WP-CLI in the WordPress container
```

## 🚀 Deployment Workflow

### 1. Development Phase
- Use local React development for fast iteration
- Test in WordPress environment
- Build and preview regularly

### 2. Production Build
```powershell
cd react-app
npm run build
```

### 3. WordPress Integration
- Built assets are in `blackcnote/dist/`
- WordPress theme serves these assets
- Test complete functionality

### 4. Deployment
- Deploy WordPress theme files
- Include built React assets
- Configure production environment

## 🐛 Troubleshooting

### Common Issues

**React dev server not starting:**
```powershell
cd react-app
npm install
npm run dev
```

**WordPress not accessible:**
```powershell
docker-compose restart wordpress
```

**Build errors:**
```powershell
cd react-app
npm run clean
npm install
npm run build
```

**Port conflicts:**
- Check if ports 5174, 8888, 8080 are available
- Stop conflicting services

### Service Status

Check service status:
```powershell
.\scripts\dev-workflow.ps1 status
```

View logs:
```powershell
.\scripts\dev-workflow.ps1 logs
```

## 📚 Best Practices

### React Development
1. **Use TypeScript** for better type safety
2. **Follow component structure** in `src/components/`
3. **Test components** in React dev environment first
4. **Build regularly** to catch integration issues

### WordPress Development
1. **Follow WordPress coding standards**
2. **Use WordPress hooks and filters**
3. **Test in WordPress environment**
4. **Keep theme files organized**

### Workflow Tips
1. **Start with React development** for UI components
2. **Build and test in WordPress** regularly
3. **Use version control** for both React and WordPress code
4. **Document custom functionality**

## 🎯 Next Steps

1. **Start development:**
   ```powershell
   .\scripts\quick-start.ps1
   ```

2. **Explore the React app structure:**
   - `react-app/src/` - React components
   - `react-app/src/components/` - Reusable components
   - `react-app/src/pages/` - Page components

3. **Explore the WordPress theme:**
   - `blackcnote/wp-content/themes/blackcnote/` - Theme files
   - `blackcnote/wp-content/plugins/` - Custom plugins

4. **Begin development:**
   - Edit React components for UI
   - Build and test in WordPress
   - Iterate and improve

Happy coding! 🎨 