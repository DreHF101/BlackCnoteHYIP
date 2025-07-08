# BlackCnote GitHub Integration & Live Editing Guide

## Overview

The BlackCnote project features a comprehensive live editing system that integrates WordPress, React, and GitHub for seamless development and deployment. This system enables real-time synchronization between all components while maintaining version control through GitHub.

## 🏗️ Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   WordPress     │    │   React App     │    │     GitHub      │
│                 │    │                 │    │                 │
│ ┌─────────────┐ │    │ ┌─────────────┐ │    │ ┌─────────────┐ │
│ │ Live Editor │◄┼────┼►│ Sync API    │ │    │ │ Repository  │ │
│ └─────────────┘ │    │ └─────────────┘ │    │ └─────────────┘ │
│ ┌─────────────┐ │    │ ┌─────────────┐ │    │ ┌─────────────┐ │
│ │ Git Ops     │◄┼────┼►│ File Watch  │ │    │ │ Webhooks    │ │
│ └─────────────┘ │    │ └─────────────┘ │    │ └─────────────┘ │
│ ┌─────────────┐ │    │ ┌─────────────┐ │    │ ┌─────────────┐ │
│ │ REST API    │◄┼────┼►│ Dev Tools   │ │    │ │ Actions     │ │
│ └─────────────┘ │    │ └─────────────┘ │    │ └─────────────┘ │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## 🚀 Quick Start

### 1. Repository Setup

```bash
# Clone the repository
git clone https://github.com/DreHF101/BlackCnoteHYIP.git
cd BlackCnoteHYIP

# Checkout main branch
git checkout main

# Install dependencies
npm install
```

### 2. Development Environment

```bash
# Start WordPress (Docker)
docker-compose up -d

# Start React development server
cd react-app
npm run dev

# Start live editing system
# (Automatically enabled in development mode)
```

### 3. Live Editing

1. **WordPress Side**: Edit content directly in the browser
2. **React Side**: Edit components in your IDE
3. **Automatic Sync**: Changes sync in real-time
4. **Git Integration**: Commit and push changes

## 📁 Project Structure

```
BlackCnote/
├── blackcnote/                    # WordPress installation
│   ├── wp-content/
│   │   └── themes/
│   │       └── blackcnote/        # BlackCnote theme
│   │           ├── functions.php  # Enhanced with live editing
│   │           ├── js/
│   │           │   └── live-editor.js
│   │           └── css/
│   │               └── live-editor.css
│   └── wp-config.php
├── react-app/                     # React application
│   ├── src/
│   │   └── api/
│   │       └── sync.js           # React sync API
│   └── package.json
├── scripts/
│   └── deploy-to-github.ps1      # Deployment script
└── docs/
    └── GITHUB-INTEGRATION-GUIDE.md
```

## 🔧 Live Editing Features

### WordPress Live Editor

#### Content Editing
```javascript
// Edit content directly in the browser
$('[data-live-edit]').on('click', function() {
    // Enable inline editing
    $(this).attr('contenteditable', 'true');
    $(this).addClass('editing');
});

// Auto-save on blur
$('[data-live-edit]').on('blur', function() {
    const content = $(this).html();
    const id = $(this).data('edit-id');
    
    // Send to WordPress
    blackcnoteSync.sendContentChange(id, content);
});
```

#### Style Editing
```javascript
// Edit styles in real-time
$('[data-style-edit]').on('change', function() {
    const property = $(this).data('style-property');
    const value = $(this).val();
    
    // Update CSS custom properties
    document.documentElement.style.setProperty(`--${property}`, value);
    
    // Sync with WordPress
    blackcnoteSync.sendStyleChange(property, { [property]: value });
});
```

#### Component Editing
```javascript
// Edit React components from WordPress
$('[data-component-edit]').on('click', function() {
    const componentName = $(this).data('component');
    
    // Show component editor
    showComponentEditor(componentName);
});
```

### React Sync API

#### Initialize Sync
```javascript
import { BlackCnoteSyncAPI } from './api/sync';

// Initialize sync API
const sync = new BlackCnoteSyncAPI();

// Listen for WordPress changes
sync.on('wordpress-change', (change) => {
    switch (change.type) {
        case 'content':
            updateContent(change.id, change.content);
            break;
        case 'style':
            updateStyles(change.styles);
            break;
        case 'component':
            updateComponent(change.name, change.data);
            break;
    }
});
```

#### Send Changes to WordPress
```javascript
// Send content changes
sync.sendContentChange('hero-title', 'New Hero Title');

// Send style changes
sync.sendStyleChange('primary-color', { '--primary-color': '#0073aa' });

// Send component changes
sync.sendComponentChange('HeroSection', { title: 'New Title' });
```

#### Git Operations
```javascript
// Commit changes
await sync.gitCommit('Update hero section');

// Push to GitHub
await sync.gitPush();

// Sync (commit + push)
await sync.gitSync('Live edit: Update styles');

// Deploy to production
await sync.gitDeploy();
```

## 🔄 Git Integration

### Automatic Git Operations

The system automatically handles Git operations:

1. **File Changes**: Monitored in real-time
2. **Auto Commit**: Changes committed automatically
3. **Auto Push**: Changes pushed to GitHub
4. **Deployment**: Production deployment via tags

### Manual Git Operations

```powershell
# Sync changes with GitHub
.\scripts\deploy-to-github.ps1

# Build React app
.\scripts\deploy-to-github.ps1 build

# Deploy to production
.\scripts\deploy-to-github.ps1 deploy

# Run tests
.\scripts\deploy-to-github.ps1 test
```

### Git Workflow

```bash
# Development workflow
1. Make changes in WordPress or React
2. Changes sync automatically
3. System commits changes
4. Changes pushed to GitHub
5. GitHub Actions trigger deployment

# Production deployment
1. Create deployment tag
2. Push tag to GitHub
3. GitHub Actions deploy to production
4. Monitor deployment status
```

## 🛠️ Development Tools

### WordPress Admin Tools

#### Development Toolbar
- **Service Status**: WordPress, React, Docker, Git
- **Quick Actions**: Sync, Clear Cache, Restart Services
- **Build Tools**: Build React, Deploy

#### Live Editor Interface
- **Inline Editing**: Click to edit content
- **Style Controls**: Real-time style editing
- **Component Editor**: Edit React components
- **File Watching**: Monitor file changes

### React Development Tools

#### Sync API Dashboard
```javascript
// Monitor sync status
sync.on('sync-complete', (data) => {
    console.log('Sync completed:', data.timestamp);
});

// Monitor service status
sync.on('service-status', (status) => {
    console.log(`${status.service}: ${status.status ? '🟢' : '🔴'}`);
});
```

#### Development Indicators
- **Sync Status**: Real-time sync indicators
- **Service Status**: Service health monitoring
- **File Changes**: File change notifications
- **Error Handling**: Comprehensive error reporting

## 🔌 API Endpoints

### WordPress REST API

#### Content Management
```
GET  /wp-json/blackcnote/v1/content/{id}
POST /wp-json/blackcnote/v1/content/{id}
PUT  /wp-json/blackcnote/v1/content/{id}
```

#### Style Management
```
GET  /wp-json/blackcnote/v1/styles
POST /wp-json/blackcnote/v1/styles
PUT  /wp-json/blackcnote/v1/styles
```

#### Component Management
```
GET  /wp-json/blackcnote/v1/components
POST /wp-json/blackcnote/v1/components
PUT  /wp-json/blackcnote/v1/components
```

#### Git Operations
```
GET  /wp-json/blackcnote/v1/github/status
POST /wp-json/blackcnote/v1/github/commit
POST /wp-json/blackcnote/v1/github/push
POST /wp-json/blackcnote/v1/github/sync
POST /wp-json/blackcnote/v1/github/deploy
```

#### Development Operations
```
POST /wp-json/blackcnote/v1/dev/clear-cache
POST /wp-json/blackcnote/v1/dev/restart-services
POST /wp-json/blackcnote/v1/dev/build-react
GET  /wp-json/blackcnote/v1/dev/docker-status
```

### React Sync API

#### Event System
```javascript
// Listen for events
sync.on('wordpress-change', handler);
sync.on('file-change', handler);
sync.on('git-status', handler);
sync.on('service-status', handler);
sync.on('sync-complete', handler);
sync.on('sync-error', handler);
```

#### Methods
```javascript
// Content operations
sync.sendContentChange(id, content, type);
sync.sendStyleChange(id, styles, type);
sync.sendComponentChange(name, data);

// Git operations
sync.gitCommit(message);
sync.gitPush();
sync.gitSync(message);
sync.gitDeploy();

// Development operations
sync.clearCache();
sync.restartServices();
sync.buildReact();
```

## 🚀 Deployment

### Development Deployment

```bash
# 1. Start development environment
docker-compose up -d

# 2. Start React development
cd react-app && npm run dev

# 3. Enable live editing
# (Automatically enabled in development mode)

# 4. Make changes
# (Changes sync automatically)

# 5. Commit and push
git add .
git commit -m "Live edit: Update content"
git push origin main
```

### Production Deployment

```bash
# 1. Build React app
npm run build

# 2. Run tests
npm test

# 3. Deploy to production
.\scripts\deploy-to-github.ps1 deploy

# 4. Monitor deployment
# (Check GitHub Actions for status)
```

### GitHub Actions Workflow

```yaml
name: Deploy BlackCnote

on:
  push:
    tags:
      - 'deploy-*'

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup Node.js
        uses: actions/setup-node@v2
        with:
          node-version: '18'
          
      - name: Install dependencies
        run: npm install
        
      - name: Build React app
        run: npm run build
        
      - name: Run tests
        run: npm test
        
      - name: Deploy to production
        run: |
          # Deploy to production server
          # (Configure based on your hosting)
```

## 🔧 Configuration

### WordPress Configuration

#### wp-config.php
```php
// Live editing configuration
define('BLACKCNOTE_LIVE_EDITING', true);
define('BLACKCNOTE_GITHUB_REPO', 'https://github.com/DreHF101/BlackCnoteHYIP.git');
define('BLACKCNOTE_AUTO_COMMIT', true);
define('BLACKCNOTE_AUTO_PUSH', false);

// Development settings
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('SCRIPT_DEBUG', true);
```

#### Theme Configuration
```php
// functions.php
function blackcnote_live_editing_config() {
    return array(
        'enabled' => true,
        'auto_save' => true,
        'file_watching' => true,
        'git_integration' => true,
        'react_sync' => true
    );
}
```

### React Configuration

#### sync.js Configuration
```javascript
const config = {
    wordpressUrl: 'http://localhost:8888',
    reactUrl: 'http://localhost:5174',
    githubRepo: 'https://github.com/DreHF101/BlackCnoteHYIP.git',
    syncInterval: 1000,
    autoSave: true,
    fileWatching: true,
    dockerMode: true,
    websocketEnabled: false
};
```

#### package.json Scripts
```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "test": "vitest",
    "deploy": "powershell -File ../scripts/deploy-to-github.ps1 deploy"
  }
}
```

## 🐛 Troubleshooting

### Common Issues

#### WordPress Not Responding
```bash
# Check WordPress status
curl http://localhost:8888/wp-json/

# Check Docker containers
docker-compose ps

# Restart WordPress
docker-compose restart wordpress
```

#### React Sync Not Working
```bash
# Check React dev server
curl http://localhost:5174

# Check sync API
curl http://localhost:8888/wp-json/blackcnote/v1/content/test

# Restart React dev server
cd react-app && npm run dev
```

#### Git Operations Failing
```bash
# Check Git status
git status

# Check remote configuration
git remote -v

# Reset Git state
git reset --hard HEAD
git clean -fd
```

#### File Watching Not Working
```bash
# Check file permissions
ls -la

# Restart file watchers
# (Restart development servers)
```

### Debug Mode

#### Enable Debug Logging
```php
// WordPress debug
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Check debug log
tail -f wp-content/debug.log
```

#### React Debug Mode
```javascript
// Enable debug logging
const sync = new BlackCnoteSyncAPI({
    debug: true,
    logLevel: 'verbose'
});

// Monitor all events
sync.on('*', (event, data) => {
    console.log('Event:', event, data);
});
```

## 📚 Best Practices

### Development Workflow

1. **Always work on feature branches**
   ```bash
   git checkout -b feature/new-feature
   # Make changes
   git commit -m "Add new feature"
   git push origin feature/new-feature
   ```

2. **Use descriptive commit messages**
   ```bash
   git commit -m "Live edit: Update hero section styling"
   git commit -m "Fix: Resolve React component sync issue"
   ```

3. **Test changes before committing**
   ```bash
   npm test
   # Fix any issues
   git add .
   git commit -m "Fix: Resolve test failures"
   ```

### Performance Optimization

1. **Debounce sync operations**
   ```javascript
   // Debounce content changes
   const debouncedSync = debounce((id, content) => {
       sync.sendContentChange(id, content);
   }, 500);
   ```

2. **Batch multiple changes**
   ```javascript
   // Batch style changes
   const styleChanges = {};
   elements.forEach(element => {
       styleChanges[element.id] = element.style;
   });
   sync.sendStyleChange('batch', styleChanges);
   ```

3. **Use WebSocket for real-time sync**
   ```javascript
   // Enable WebSocket for better performance
   const sync = new BlackCnoteSyncAPI({
       websocketEnabled: true
   });
   ```

### Security Considerations

1. **Validate all inputs**
   ```php
   // WordPress side
   $content = wp_kses_post($_POST['content']);
   $id = sanitize_text_field($_POST['id']);
   ```

2. **Check permissions**
   ```php
   // Check user capabilities
   if (!current_user_can('edit_posts')) {
       wp_die('Insufficient permissions');
   }
   ```

3. **Use nonces for security**
   ```php
   // Verify nonce
   check_ajax_referer('blackcnote_live_edit', 'nonce');
   ```

## 🔮 Future Enhancements

### Planned Features

1. **Advanced Git Integration**
   - Branch management
   - Pull request automation
   - Code review integration

2. **Enhanced Live Editing**
   - Visual page builder
   - Component library
   - Template system

3. **Performance Improvements**
   - WebSocket optimization
   - Caching strategies
   - Lazy loading

4. **Development Tools**
   - Debug console
   - Performance monitoring
   - Error tracking

### Contributing

1. **Fork the repository**
2. **Create a feature branch**
3. **Make your changes**
4. **Add tests**
5. **Submit a pull request**

## 📞 Support

### Getting Help

- **Documentation**: Check this guide and inline comments
- **Issues**: Report bugs on GitHub
- **Discussions**: Use GitHub Discussions for questions
- **Email**: Contact the development team

### Resources

- [WordPress Developer Documentation](https://developer.wordpress.org/)
- [React Documentation](https://react.dev/)
- [GitHub API Documentation](https://docs.github.com/en/rest)
- [Docker Documentation](https://docs.docker.com/)

---

**BlackCnote Live Editing System** - Seamless WordPress/React/GitHub Integration 