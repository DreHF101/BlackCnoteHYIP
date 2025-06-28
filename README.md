# BlackCnote - Advanced Investment Platform

## 🎯 Overview
BlackCnote is a comprehensive investment platform built with WordPress, React, and modern web technologies. It provides a complete solution for managing investment plans, user portfolios, and financial transactions.

## 📍 **CANONICAL DIRECTORY PATHWAYS** ⚠️ **CRITICAL**

**All BlackCnote development, deployment, and documentation MUST use these canonical pathways:**

### **BlackCnote Project Root Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\
```
- **Purpose**: Main BlackCnote project root directory
- **Contains**: All project files, Docker configs, scripts, docs, themes, plugins
- **Usage**: Primary development and deployment directory for the entire BlackCnote project
- **Status**: ✅ **CANONICAL PROJECT ROOT**

### **Theme Directory (WordPress Installation)**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\
```

### **WordPress Content Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\
```

### **Theme Files Directory**
```
C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\blackcnote\wp-content\themes\blackcnote\
```

**⚠️ IMPORTANT:**
- **NEVER** use `wordpress/wp-content` or any other directory
- **ALWAYS** use the `blackcnote/wp-content` directory for all WordPress content
- All Docker containers, scripts, and tools are configured to use these canonical paths
- The debug system and monitoring tools exclusively watch these directories
- **ALWAYS** start development from the main project root: `C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\`

**For detailed directory structure information, see:** [BLACKCNOTE-DIRECTORY-STRUCTURE.md](BLACKCNOTE-DIRECTORY-STRUCTURE.md)  
**For complete canonical path documentation, see:** [BLACKCNOTE-CANONICAL-PATHS.md](BLACKCNOTE-CANONICAL-PATHS.md)

## ✨ Features
- **Investment Management**: Create and manage investment plans
- **User Portfolios**: Track user investments and returns
- **Payment Processing**: Integrated payment gateways
- **Real-time Updates**: Live investment tracking
- **Admin Dashboard**: Comprehensive administration tools
- **Mobile Responsive**: Works on all devices
- **Security Focused**: Enterprise-grade security

## 🚀 Quick Start

### Prerequisites
- Docker Desktop
- Git
- Node.js (for development)

### Installation
1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/blackcnote.git
   cd blackcnote
   ```

2. **Start the environment**
   ```bash
   # Windows
   start-blackcnote.bat
   
   # Or manually
   docker-compose up -d
   ```

3. **Access the platform**
   - WordPress: http://localhost:8888
   - React App: http://localhost:5174
   - PHPMyAdmin: http://localhost:8080

## 🛠️ Technology Stack
- **Backend**: WordPress 6.8, PHP 8.2, MySQL 8.0
- **Frontend**: React 18, TypeScript, Vite
- **Investment Platform**: HYIPLab (custom plugin)
- **Containerization**: Docker, Docker Compose
- **Caching**: Redis
- **Email Testing**: MailHog
- **Development**: Hot reloading, Live editing

## 📁 Project Structure
```
BlackCnote/
├── 📁 blackcnote/                    # WordPress Core
│   ├── 📁 wp-content/
│   │   ├── 📁 themes/blackcnote/     # Main theme
│   │   └── 📁 plugins/blackcnote-hyiplab/  # Investment plugin
├── 📁 react-app/                     # React Frontend
├── 📁 hyiplab/                       # Investment Platform
├── 📁 docs/                          # Documentation
├── 📁 scripts/                       # Automation Scripts
└── 📁 config/                        # Configuration Files
```

## 🔧 Development

### Scripts
- **Setup**: `scripts/setup/` - Environment setup scripts
- **Testing**: `scripts/testing/` - Test and validation scripts
- **Deployment**: `scripts/deployment/` - Deployment automation
- **Maintenance**: `scripts/maintenance/` - Maintenance tools

### Tools
- **Debug**: `tools/debug/` - Debugging utilities
- **Analysis**: `tools/analysis/` - Code analysis tools
- **Utilities**: `tools/utilities/` - General utilities

## 📚 Documentation
- [Complete Documentation](docs/README.md) - Full documentation index
- [Project Root Directory](BLACKCNOTE-PROJECT-ROOT.md) - Canonical project root information
- [Canonical Paths](BLACKCNOTE-CANONICAL-PATHS.md) - All canonical directory pathways
- [Directory Structure](BLACKCNOTE-DIRECTORY-STRUCTURE.md) - Complete project structure
- [Development Guide](docs/development/DEVELOPMENT-GUIDE.md) - Development workflow
- [Deployment Guide](docs/deployment/DEPLOYMENT-GUIDE.md) - Production deployment
- [Troubleshooting](docs/troubleshooting/TROUBLESHOOTING.md) - Common issues

## 🔒 Security
- WordPress security best practices
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- CSRF protection
- Secure file permissions

## 🚀 Deployment
See [Deployment Guide](docs/deployment/DEPLOYMENT-GUIDE.md) for production deployment instructions.

## 🤝 Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License
This project is licensed under the MIT License - see [LICENSE.txt](docs/LICENSE.txt) for details.

## 📞 Support
- Documentation: [docs/README.md](docs/README.md)
- Troubleshooting: [docs/troubleshooting/TROUBLESHOOTING.md](docs/troubleshooting/TROUBLESHOOTING.md)
- Issues: GitHub Issues

---

**BlackCnote** - Advanced Investment Platform
