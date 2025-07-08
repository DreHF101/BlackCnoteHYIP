# BlackCnote - Advanced Investment Platform Theme

## 🎯 Overview
BlackCnote is a comprehensive investment platform theme built with WordPress, React, and modern web technologies. This repository contains the essential files needed to import and deploy the BlackCnote theme across different platforms.

## ✨ Features
- **Investment Management**: Create and manage investment plans
- **User Portfolios**: Track user investments and returns
- **Payment Processing**: Integrated payment gateways
- **Real-time Updates**: Live investment tracking
- **Admin Dashboard**: Comprehensive administration tools
- **Mobile Responsive**: Works on all devices
- **Security Focused**: Enterprise-grade security
- **Cross-Platform**: Works on Windows, macOS, and Linux

## 🚀 Quick Start

### Prerequisites
- Docker Desktop
- Git
- Node.js 18+ (for development)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/blackcnote.git
   cd blackcnote
   ```

2. **Start the development environment**
   ```bash
   # Windows
   npm run dev:full
   
   # Or manually
   docker-compose up -d
   ```

3. **Access the platform**
   - WordPress: http://localhost:8888
   - React App: http://localhost:5174
   - PHPMyAdmin: http://localhost:8080

## 📁 Repository Structure

```
blackcnote/
├── 📁 blackcnote/wp-content/themes/blackcnote/  # WordPress Theme
│   ├── 📁 assets/                               # Theme assets
│   ├── 📁 inc/                                  # Theme includes
│   ├── 📁 template-parts/                       # Template parts
│   ├── 📁 admin/                                # Admin functionality
│   ├── functions.php                            # Theme functions
│   ├── style.css                                # Theme stylesheet
│   └── index.php                                # Main template
├── 📁 react-app/                                # React Frontend
│   ├── 📁 src/                                  # React source code
│   ├── 📁 public/                               # Public assets
│   ├── package.json                             # Dependencies
│   └── vite.config.ts                           # Build configuration
├── 📁 hyiplab/                                  # Investment Platform
│   ├── 📁 app/                                  # Core application
│   ├── 📁 routes/                               # Routing
│   ├── 📁 views/                                # Views/templates
│   └── composer.json                            # PHP dependencies
├── 📁 config/                                   # Configuration files
│   └── 📁 docker/                               # Docker configurations
├── docker-compose.yml                           # Docker services
├── package.json                                 # Project dependencies
└── README.md                                    # This file
```

## 🛠️ Technology Stack

### Backend
- **WordPress 6.8+**: Content management system
- **PHP 8.2+**: Server-side scripting
- **MySQL 8.0**: Database
- **Redis**: Caching and sessions

### Frontend
- **React 18**: User interface framework
- **TypeScript**: Type-safe JavaScript
- **Vite**: Build tool and dev server
- **Tailwind CSS**: Utility-first CSS framework

### Investment Platform
- **HYIPLab**: Custom investment management system
- **Payment Gateways**: Integrated payment processing
- **Real-time Updates**: Live investment tracking

### Development Tools
- **Docker**: Containerization
- **Docker Compose**: Multi-container orchestration
- **Hot Reloading**: Live development experience
- **Browsersync**: Cross-device testing

## 🔧 Development

### Available Scripts

```bash
# Start full development environment
npm run dev:full

# Start only React development server
npm run dev:react

# Start only WordPress/Docker services
npm run dev:wordpress

# Stop all services
npm run stop

# Build React app for production
npm run build:react

# Clean and reinstall dependencies
npm run clean
```

### Development Workflow

1. **Start Development Environment**
   ```bash
   npm run dev:full
   ```

2. **Make Changes**
   - Edit WordPress theme files in `blackcnote/wp-content/themes/blackcnote/`
   - Edit React components in `react-app/src/`
   - Edit investment platform in `hyiplab/app/`

3. **View Changes**
   - WordPress changes are live-reloaded
   - React changes are hot-reloaded
   - All changes are reflected immediately

## 🚀 Deployment

### Production Deployment

1. **Build the React app**
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

### Docker Production

```bash
# Build production images
docker-compose -f docker-compose.prod.yml build

# Deploy to production
docker-compose -f docker-compose.prod.yml up -d
```

## 📚 Documentation

- [Development Guide](docs/development/DEVELOPMENT-GUIDE.md)
- [Deployment Guide](docs/deployment/DEPLOYMENT-GUIDE.md)
- [Theme Customization](docs/theme/CUSTOMIZATION.md)
- [API Documentation](docs/api/README.md)
- [Troubleshooting](docs/troubleshooting/TROUBLESHOOTING.md)

## 🔒 Security

- WordPress security best practices
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- CSRF protection
- Secure file permissions
- Regular security updates

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

- Follow WordPress coding standards for PHP
- Follow React/TypeScript best practices
- Write meaningful commit messages
- Include tests for new features
- Update documentation as needed

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: [docs/README.md](docs/README.md)
- **Issues**: [GitHub Issues](https://github.com/your-org/blackcnote/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-org/blackcnote/discussions)

## 🙏 Acknowledgments

- WordPress community for the excellent CMS
- React team for the amazing frontend framework
- Docker team for containerization technology
- All contributors who have helped improve this project

---

**BlackCnote** - Advanced Investment Platform Theme

*Built with ❤️ for the investment community*
