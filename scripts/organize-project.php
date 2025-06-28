<?php
/**
 * BlackCnote Project Organization Script
 * 
 * This script organizes the entire BlackCnote project structure
 * to improve accessibility, maintainability, and development workflow.
 */

declare(strict_types=1);

class ProjectOrganizer {
    private array $moves = [];
    private array $errors = [];
    private array $warnings = [];

    public function organizeDocumentation(): void {
        echo "📚 Organizing Documentation...\n";
        
        $docMoves = [
            // Setup Documentation
            'docs/INSTALL.md' => 'docs/setup/INSTALLATION.md',
            'docs/INSTALLATION.md' => 'docs/setup/INSTALLATION.md',
            'docs/DOCKER-SETUP.md' => 'docs/setup/DOCKER-SETUP.md',
            'docs/CONFIGURATION_GUIDE.md' => 'docs/setup/CONFIGURATION-GUIDE.md',
            'docs/env.example' => 'docs/setup/ENVIRONMENT-EXAMPLE.md',
            
            // Development Documentation
            'docs/DEVELOPMENT.md' => 'docs/development/DEVELOPMENT-GUIDE.md',
            'docs/DEVELOPMENT-GUIDE.md' => 'docs/development/DEVELOPMENT-GUIDE.md',
            'docs/LOCAL-DEVELOPMENT-GUIDE.md' => 'docs/development/LOCAL-DEVELOPMENT.md',
            'docs/LIVE-EDITING-GUIDE.md' => 'docs/development/LIVE-EDITING.md',
            'docs/TEAM-TRAINING-GUIDE.md' => 'docs/development/TEAM-TRAINING.md',
            'docs/CODE-STRUCTURE-GUIDE.md' => 'docs/development/CODE-STRUCTURE.md',
            'docs/THEME-PLUGIN-INTEGRATION-GUIDE.md' => 'docs/development/THEME-PLUGIN-INTEGRATION.md',
            
            // Deployment Documentation
            'docs/DEPLOYMENT.md' => 'docs/deployment/DEPLOYMENT-GUIDE.md',
            'docs/blackcnote/DEPLOYMENT.md' => 'docs/deployment/DEPLOYMENT-GUIDE.md',
            'docs/blackcnote/IMPLEMENTATION-SUMMARY.md' => 'docs/deployment/IMPLEMENTATION-SUMMARY.md',
            
            // Troubleshooting Documentation
            'docs/TROUBLESHOOTING.md' => 'docs/troubleshooting/TROUBLESHOOTING.md',
            'docs/DOCKER-TROUBLESHOOTING.md' => 'docs/troubleshooting/DOCKER-TROUBLESHOOTING.md',
            'docs/DEBUG-PLUGIN-CONFLICT-ANALYSIS.md' => 'docs/troubleshooting/DEBUG-PLUGIN-CONFLICTS.md',
            'docs/DEBUG-PLUGIN-XAMPP-COMPATIBILITY.md' => 'docs/troubleshooting/DEBUG-PLUGIN-XAMPP.md',
            
            // Analysis and Review Documentation (keep in root docs)
            'docs/BLACKCNOTE-PATHWAY-ANALYSIS-REPORT.md' => 'docs/ANALYSIS-PATHWAY-REPORT.md',
            'docs/BLACKCNOTE-THEME-CONFLICT-ANALYSIS-REPORT.md' => 'docs/ANALYSIS-THEME-CONFLICTS.md',
            'docs/COMPATIBILITY-ANALYSIS-REPORT.md' => 'docs/ANALYSIS-COMPATIBILITY.md',
            'docs/SECURITY-REVIEW-REPORT.md' => 'docs/ANALYSIS-SECURITY.md',
            'docs/PERFORMANCE-ANALYSIS-REPORT.md' => 'docs/ANALYSIS-PERFORMANCE.md',
            'docs/FEATURE-VERIFICATION-REPORT.md' => 'docs/ANALYSIS-FEATURES.md',
            'docs/CODE-QUALITY-AUDIT.md' => 'docs/ANALYSIS-CODE-QUALITY.md',
        ];

        foreach ($docMoves as $from => $to) {
            if (file_exists($from)) {
                $this->moveFile($from, $to);
            }
        }
    }

    public function organizeScripts(): void {
        echo "🔧 Organizing Scripts...\n";
        
        $scriptMoves = [
            // Setup Scripts
            'scripts/setup-database.php' => 'scripts/setup/database-setup.php',
            'scripts/setup-complete-environment.ps1' => 'scripts/setup/environment-setup.ps1',
            'scripts/setup-database.sql' => 'scripts/setup/database-schema.sql',
            
            // Testing Scripts
            'scripts/test-docker-environment.php' => 'scripts/testing/docker-environment-test.php',
            'scripts/final-docker-test.php' => 'scripts/testing/final-docker-test.php',
            'scripts/final-debug-test.php' => 'scripts/testing/debug-test.php',
            'scripts/simple-debug-test.php' => 'scripts/testing/simple-debug-test.php',
            'scripts/standalone-debug-test.php' => 'scripts/testing/standalone-debug-test.php',
            'scripts/docker-health-check.ps1' => 'scripts/testing/docker-health-check.ps1',
            'scripts/docker-health-check.sh' => 'scripts/testing/docker-health-check.sh',
            
            // Deployment Scripts
            'scripts/run-complete-test.bat' => 'scripts/deployment/complete-test.bat',
            'scripts/start-services-and-test.bat' => 'scripts/deployment/start-services.bat',
            
            // Maintenance Scripts
            'scripts/fix-docker-issues.php' => 'scripts/maintenance/fix-docker-issues.php',
        ];

        foreach ($scriptMoves as $from => $to) {
            if (file_exists($from)) {
                $this->moveFile($from, $to);
            }
        }
    }

    public function organizeConfigurationFiles(): void {
        echo "⚙️ Organizing Configuration Files...\n";
        
        // Create config subdirectories if they don't exist
        $configDirs = ['config/docker', 'config/nginx', 'config/apache'];
        foreach ($configDirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
        
        $configMoves = [
            // Docker Configs
            'docker-compose.yml' => 'config/docker/docker-compose.yml',
            'docker-compose.prod.yml' => 'config/docker/docker-compose.prod.yml',
            'docker-compose.override.yml' => 'config/docker/docker-compose.override.yml',
            '.dockerignore' => 'config/docker/.dockerignore',
            'docker-setup.sh' => 'config/docker/setup.sh',
            'docker-commands.md' => 'config/docker/commands.md',
            
            // Nginx Configs (already in config/nginx/)
            // Apache Configs (already in config/apache/)
        ];

        foreach ($configMoves as $from => $to) {
            if (file_exists($from)) {
                $this->moveFile($from, $to);
            }
        }
    }

    public function organizeTools(): void {
        echo "🛠️ Organizing Tools...\n";
        
        // Create tools subdirectories
        $toolDirs = ['tools/debug', 'tools/analysis', 'tools/utilities'];
        foreach ($toolDirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
        
        $toolMoves = [
            // Debug Tools
            'blackcnote/debug-redirect.php' => 'tools/debug/redirect-debug.php',
            'blackcnote/phpinfo.php' => 'tools/debug/phpinfo.php',
            
            // Analysis Tools
            'blackcnote/core-integrity-check.php' => 'tools/analysis/core-integrity.php',
            'blackcnote/check-internal-urls.php' => 'tools/analysis/internal-urls.php',
            'blackcnote/check-fix-urls.php' => 'tools/analysis/url-fixes.php',
            'blackcnote/fix-wordpress-urls.php' => 'tools/analysis/wordpress-urls.php',
            'blackcnote/fix-db-urls.php' => 'tools/analysis/database-urls.php',
            'blackcnote/db-search-replace.php' => 'tools/analysis/database-search-replace.php',
            'blackcnote/deep-redirect-cleanup.php' => 'tools/analysis/redirect-cleanup.php',
            'blackcnote/update-urls-root.php' => 'tools/analysis/root-urls.php',
            'blackcnote/fresh-install.php' => 'tools/analysis/fresh-install.php',
            
            // Utility Tools
            'fix-wordpress-urls.php' => 'tools/utilities/fix-wordpress-urls.php',
            'check-internal-urls.php' => 'tools/utilities/check-internal-urls.php',
        ];

        foreach ($toolMoves as $from => $to) {
            if (file_exists($from)) {
                $this->moveFile($from, $to);
            }
        }
    }

    public function createProjectIndex(): void {
        echo "📋 Creating Project Index...\n";
        
        $index = '# BlackCnote Project Documentation Index

## 🚀 Quick Start
- [Installation Guide](setup/INSTALLATION.md) - Complete setup instructions
- [Docker Setup](setup/DOCKER-SETUP.md) - Docker environment configuration
- [Configuration Guide](setup/CONFIGURATION-GUIDE.md) - System configuration

## 🛠️ Development
- [Development Guide](development/DEVELOPMENT-GUIDE.md) - Development workflow
- [Local Development](development/LOCAL-DEVELOPMENT.md) - Local environment setup
- [Live Editing](development/LIVE-EDITING.md) - Real-time development features
- [Team Training](development/TEAM-TRAINING.md) - Team onboarding guide
- [Code Structure](development/CODE-STRUCTURE.md) - Project architecture
- [Theme-Plugin Integration](development/THEME-PLUGIN-INTEGRATION.md) - Integration guide

## 🚀 Deployment
- [Deployment Guide](deployment/DEPLOYMENT-GUIDE.md) - Production deployment
- [Implementation Summary](deployment/IMPLEMENTATION-SUMMARY.md) - Implementation details

## 🔧 Troubleshooting
- [Troubleshooting Guide](troubleshooting/TROUBLESHOOTING.md) - Common issues and solutions
- [Docker Troubleshooting](troubleshooting/DOCKER-TROUBLESHOOTING.md) - Docker-specific issues
- [Debug Plugin Conflicts](troubleshooting/DEBUG-PLUGIN-CONFLICTS.md) - Debug system issues
- [Debug Plugin XAMPP](troubleshooting/DEBUG-PLUGIN-XAMPP.md) - XAMPP compatibility

## 📊 Analysis & Reviews
- [Pathway Analysis](ANALYSIS-PATHWAY-REPORT.md) - Project pathway analysis
- [Theme Conflict Analysis](ANALYSIS-THEME-CONFLICTS.md) - Theme conflict resolution
- [Compatibility Analysis](ANALYSIS-COMPATIBILITY.md) - System compatibility
- [Security Review](ANALYSIS-SECURITY.md) - Security assessment
- [Performance Analysis](ANALYSIS-PERFORMANCE.md) - Performance optimization
- [Feature Verification](ANALYSIS-FEATURES.md) - Feature validation
- [Code Quality Audit](ANALYSIS-CODE-QUALITY.md) - Code quality assessment

## 📁 Project Structure
```
BlackCnote/
├── 📁 blackcnote/                    # WordPress Core
├── 📁 react-app/                     # React Frontend
├── 📁 hyiplab/                       # Investment Platform
├── 📁 docs/                          # Documentation
├── 📁 scripts/                       # Automation Scripts
├── 📁 config/                        # Configuration Files
├── 📁 tools/                         # Development Tools
├── 📁 db/                            # Database Files
└── 📁 logs/                          # Log Files
```

## 🔗 Quick Links
- [WordPress Admin](http://localhost:8888/wp-admin) - WordPress administration
- [React App](http://localhost:5174) - React development server
- [PHPMyAdmin](http://localhost:8080) - Database management
- [MailHog](http://localhost:8025) - Email testing
- [Redis Commander](http://localhost:8081) - Redis management

## 📞 Support
For technical support and questions, refer to the troubleshooting guides above.
';

        file_put_contents('docs/README.md', $index);
        $this->moves[] = "Created documentation index";
    }

    public function createMainREADME(): void {
        echo "📖 Creating Main README...\n";
        
        $readme = '# BlackCnote - Advanced Investment Platform

## 🎯 Overview
BlackCnote is a comprehensive investment platform built with WordPress, React, and modern web technologies. It provides a complete solution for managing investment plans, user portfolios, and financial transactions.

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
';

        file_put_contents('README.md', $readme);
        $this->moves[] = "Created main README";
    }

    public function cleanupDuplicateFiles(): void {
        echo "🧹 Cleaning up duplicate files...\n";
        
        $duplicates = [
            // Remove duplicate documentation
            'docs/README.md' => 'docs/README.md',
            'docs/READMENow.md' => null, // Remove duplicate
            'docs/README.txt' => null, // Remove duplicate
            
            // Remove test result files
            'final-test-results.txt' => null,
            'test-results.txt' => null,
            
            // Remove old configuration files
            'php.ini' => null,
            '.htaccess' => null,
            '.htaccess.disabled' => null,
        ];

        foreach ($duplicates as $file => $action) {
            if (file_exists($file)) {
                if ($action === null) {
                    unlink($file);
                    $this->moves[] = "Removed duplicate: $file";
                }
            }
        }
    }

    private function moveFile(string $from, string $to): void {
        try {
            // Create directory if it doesn't exist
            $dir = dirname($to);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Move the file
            if (rename($from, $to)) {
                $this->moves[] = "Moved: $from → $to";
            } else {
                $this->errors[] = "Failed to move: $from → $to";
            }
        } catch (Exception $e) {
            $this->errors[] = "Error moving $from: " . $e->getMessage();
        }
    }

    public function generateReport(): void {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🎯 BLACKCNOTE PROJECT ORGANIZATION COMPLETE\n";
        echo str_repeat("=", 60) . "\n\n";
        
        echo "✅ ORGANIZATION ACTIONS:\n";
        foreach ($this->moves as $move) {
            echo "  ✓ $move\n";
        }
        
        if (!empty($this->warnings)) {
            echo "\n⚠️ WARNINGS:\n";
            foreach ($this->warnings as $warning) {
                echo "  ⚠️ $warning\n";
            }
        }
        
        if (!empty($this->errors)) {
            echo "\n❌ ERRORS:\n";
            foreach ($this->errors as $error) {
                echo "  ✗ $error\n";
            }
        }
        
        echo "\n📁 NEW PROJECT STRUCTURE:\n";
        echo "  📁 docs/\n";
        echo "    📁 setup/ - Installation and setup guides\n";
        echo "    📁 development/ - Development guides and workflows\n";
        echo "    📁 deployment/ - Deployment and production guides\n";
        echo "    📁 troubleshooting/ - Troubleshooting guides\n";
        echo "    📄 README.md - Documentation index\n";
        echo "  📁 scripts/\n";
        echo "    📁 setup/ - Environment setup scripts\n";
        echo "    📁 testing/ - Test and validation scripts\n";
        echo "    📁 deployment/ - Deployment automation\n";
        echo "    📁 maintenance/ - Maintenance tools\n";
        echo "  📁 config/\n";
        echo "    📁 docker/ - Docker configurations\n";
        echo "    📁 nginx/ - Nginx configurations\n";
        echo "    📁 apache/ - Apache configurations\n";
        echo "  📁 tools/\n";
        echo "    📁 debug/ - Debugging utilities\n";
        echo "    📁 analysis/ - Code analysis tools\n";
        echo "    📁 utilities/ - General utilities\n";
        echo "  📄 README.md - Main project overview\n";
        
        echo "\n🎉 PROJECT ORGANIZATION COMPLETE!\n";
        echo "The BlackCnote project is now properly organized and ready for development.\n";
    }

    public function run(): void {
        echo "🚀 BlackCnote Project Organization\n";
        echo "==================================\n\n";
        
        $this->organizeDocumentation();
        $this->organizeScripts();
        $this->organizeConfigurationFiles();
        $this->organizeTools();
        $this->createProjectIndex();
        $this->createMainREADME();
        $this->cleanupDuplicateFiles();
        
        $this->generateReport();
    }
}

// Run the organizer
if (php_sapi_name() === 'cli') {
    $organizer = new ProjectOrganizer();
    $organizer->run();
} 