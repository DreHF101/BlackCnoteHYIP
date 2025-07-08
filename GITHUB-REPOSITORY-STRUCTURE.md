# BlackCnote GitHub Repository Structure

## 🎯 Overview
This document outlines the cleaned GitHub repository structure for the BlackCnote investment platform theme. The repository contains only essential files needed for importing and deploying the theme across different platforms.

## 📁 Repository Structure

```
blackcnote/
├── 📁 blackcnote/wp-content/themes/blackcnote/  # WordPress Theme (ESSENTIAL)
│   ├── 📁 assets/                               # Theme assets (CSS, JS, images)
│   ├── 📁 inc/                                  # Theme includes and functions
│   ├── 📁 template-parts/                       # Template parts and components
│   ├── 📁 admin/                                # Admin functionality
│   ├── 📁 css/                                  # Theme stylesheets
│   ├── 📁 js/                                   # Theme JavaScript
│   ├── 📁 languages/                            # Translation files
│   ├── functions.php                            # Theme functions (ESSENTIAL)
│   ├── style.css                                # Theme stylesheet (ESSENTIAL)
│   ├── index.php                                # Main template (ESSENTIAL)
│   ├── header.php                               # Header template
│   ├── footer.php                               # Footer template
│   ├── front-page.php                           # Front page template
│   ├── page.php                                 # Page template
│   ├── single.php                               # Single post template
│   ├── archive.php                              # Archive template
│   └── [other template files]                   # Additional templates
├── 📁 react-app/                                # React Frontend (ESSENTIAL)
│   ├── 📁 src/                                  # React source code
│   │   ├── 📁 components/                       # React components
│   │   ├── 📁 pages/                            # React pages
│   │   ├── 📁 api/                              # API integration
│   │   ├── 📁 hooks/                            # Custom React hooks
│   │   ├── 📁 services/                         # Service layer
│   │   ├── 📁 types/                            # TypeScript types
│   │   ├── 📁 utils/                            # Utility functions
│   │   ├── 📁 config/                           # Configuration
│   │   ├── App.tsx                              # Main React app
│   │   ├── main.tsx                             # React entry point
│   │   └── index.css                            # Global styles
│   ├── 📁 public/                               # Public assets
│   ├── package.json                             # Dependencies (ESSENTIAL)
│   ├── package-lock.json                        # Lock file (ESSENTIAL)
│   ├── vite.config.ts                           # Build configuration (ESSENTIAL)
│   ├── tailwind.config.js                       # Tailwind config (ESSENTIAL)
│   ├── tsconfig.json                            # TypeScript config (ESSENTIAL)
│   ├── tsconfig.app.json                        # TypeScript app config
│   ├── tsconfig.node.json                       # TypeScript node config
│   ├── postcss.config.js                        # PostCSS config
│   ├── eslint.config.js                         # ESLint config
│   └── index.html                               # HTML entry point
├── 📁 hyiplab/                                  # Investment Platform (ESSENTIAL)
│   ├── 📁 app/                                  # Core application
│   │   ├── 📁 BackOffice/                       # Admin functionality
│   │   ├── 📁 Controllers/                      # Controllers
│   │   ├── 📁 Models/                           # Data models
│   │   ├── 📁 Services/                         # Business logic
│   │   ├── 📁 Helpers/                          # Helper functions
│   │   ├── 📁 Middleware/                       # Middleware
│   │   └── 📁 Database/                         # Database layer
│   ├── 📁 routes/                               # Routing configuration
│   ├── 📁 views/                                # View templates
│   ├── 📁 assets/                               # Platform assets
│   ├── 📁 languages/                            # Translations
│   ├── composer.json                            # PHP dependencies (ESSENTIAL)
│   ├── hyiplab.php                              # Main entry point
│   └── dashboard.php                            # Dashboard entry point
├── 📁 config/                                   # Configuration Files (ESSENTIAL)
│   └── 📁 docker/                               # Docker configurations
│       ├── wordpress.Dockerfile                 # WordPress Dockerfile
│       └── [other Docker configs]               # Additional Docker files
├── docker-compose.yml                           # Docker services (ESSENTIAL)
├── package.json                                 # Project dependencies (ESSENTIAL)
├── start-dev-simple.ps1                         # Development startup script
├── stop-dev-simple.ps1                          # Development stop script
├── .gitignore                                   # Git ignore rules (ESSENTIAL)
├── README.md                                    # Project documentation (ESSENTIAL)
└── GITHUB-REPOSITORY-STRUCTURE.md               # This file
```

## ✅ Files Included (Essential)

### WordPress Theme
- **Complete theme directory**: `blackcnote/wp-content/themes/blackcnote/`
- **All template files**: PHP templates for pages, posts, archives
- **Theme functions**: `functions.php` with all custom functionality
- **Stylesheets**: `style.css` and CSS assets
- **JavaScript**: Theme-specific JS files
- **Assets**: Images, fonts, and other theme assets

### React Frontend
- **Source code**: Complete `react-app/src/` directory
- **Configuration**: All build and development configs
- **Dependencies**: `package.json` and `package-lock.json`
- **Public assets**: Static files in `react-app/public/`

### Investment Platform (HYIPLab)
- **Core application**: Complete `hyiplab/app/` directory
- **Routing**: Route definitions in `hyiplab/routes/`
- **Views**: Template files in `hyiplab/views/`
- **Dependencies**: `composer.json` for PHP dependencies

### Configuration
- **Docker setup**: `docker-compose.yml` and Docker configurations
- **Project config**: Root `package.json` for project dependencies
- **Development scripts**: Startup and stop scripts

### Documentation
- **README.md**: Comprehensive project documentation
- **Repository structure**: This documentation file

## ❌ Files Excluded (Non-Essential)

### WordPress Core
- `wp-admin/` - WordPress admin files (install separately)
- `wp-includes/` - WordPress core files (install separately)
- `wp-*.php` - WordPress core PHP files
- `xmlrpc.php` - WordPress XML-RPC
- `readme.html` - WordPress readme
- `license.txt` - WordPress license
- `wp-config*.php` - WordPress configuration files
- `.htaccess` - WordPress .htaccess

### Dependencies
- `node_modules/` - Node.js dependencies (install with npm)
- `vendor/` - PHP dependencies (install with composer)
- `composer.lock` - Composer lock file
- `composer.phar` - Composer executable

### Build Outputs
- `dist/` - Build output directories
- `build/` - Build output directories
- `.cache/` - Cache directories
- `.vite/` - Vite cache

### Development Files
- `logs/` - Log files
- `tmp/` - Temporary files
- `backups/` - Backup files
- `tools/` - Development tools
- `bin/` - Binary files
- Test files (`test-*.php`, `*.test.js`, etc.)

### Documentation
- `BLACKCNOTE-*.md` - Internal documentation files
- `ERROR-*.md` - Error documentation
- `FINAL-*.md` - Final documentation
- `DOCKER-*.md` - Docker documentation
- `REACT-*.md` - React documentation
- `MANUAL-*.md` - Manual documentation

### Binary Files
- `*.png`, `*.jpg`, `*.gif`, `*.svg` - Image files
- `*.pdf` - PDF files
- `*.zip`, `*.tar.gz` - Archive files

### Environment Files
- `*.env` - Environment configuration files
- `*.pem`, `*.key`, `*.crt` - SSL certificates

## 🚀 Usage Instructions

### For Theme Import
1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/blackcnote.git
   cd blackcnote
   ```

2. **Install dependencies**
   ```bash
   # Install Node.js dependencies
   npm install
   cd react-app && npm install && cd ..
   
   # Install PHP dependencies
   cd hyiplab && composer install && cd ..
   ```

3. **Start development environment**
   ```bash
   npm run dev:full
   ```

### For Production Deployment
1. **Build React app**
   ```bash
   npm run build:react
   ```

2. **Deploy WordPress theme**
   - Upload `blackcnote/wp-content/themes/blackcnote/` to your WordPress installation
   - Activate the theme in WordPress admin

3. **Deploy HYIPLab platform**
   - Upload `hyiplab/` to your server
   - Run `composer install` in the hyiplab directory
   - Configure database connections

## 📊 Repository Statistics

After cleanup, the repository contains:
- **Essential files only**: ~500-800 files
- **Total size**: ~50-100 MB (vs. 1GB+ before cleanup)
- **Cross-platform compatible**: Works on Windows, macOS, and Linux
- **Easy to import**: Minimal setup required
- **Production ready**: All necessary files included

## 🔧 Maintenance

### Adding New Files
When adding new files to the repository:
1. Ensure they are essential for theme functionality
2. Update this documentation if needed
3. Test cross-platform compatibility
4. Update `.gitignore` if necessary

### Updating Dependencies
When updating dependencies:
1. Update `package.json` and `composer.json`
2. Test the build process
3. Update documentation if needed
4. Commit lock files for reproducible builds

## 📞 Support

For questions about the repository structure:
- Check the [README.md](README.md) for general information
- Review the [Development Guide](docs/development/DEVELOPMENT-GUIDE.md)
- Open an issue on GitHub for specific questions

---

**BlackCnote GitHub Repository** - Clean, Essential, Cross-Platform 