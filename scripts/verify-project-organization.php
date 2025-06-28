<?php
/**
 * BlackCnote Project Organization Verification Script
 * 
 * This script verifies that all project components are properly organized,
 * accessible, and ready for development and production use.
 */

declare(strict_types=1);

class ProjectVerifier {
    private array $results = [];
    private array $errors = [];
    private array $warnings = [];

    public function verifyDirectoryStructure(): void {
        echo "📁 Verifying Directory Structure...\n";
        
        $requiredDirs = [
            'blackcnote',
            'blackcnote/wp-content',
            'blackcnote/wp-content/themes',
            'blackcnote/wp-content/themes/blackcnote',
            'blackcnote/wp-content/plugins',
            'blackcnote/wp-content/plugins/blackcnote-hyiplab',
            'blackcnote/wp-content/uploads',
            'react-app',
            'react-app/src',
            'hyiplab',
            'hyiplab/app',
            'hyiplab/views',
            'docs',
            'docs/setup',
            'docs/development',
            'docs/deployment',
            'docs/troubleshooting',
            'scripts',
            'scripts/setup',
            'scripts/testing',
            'scripts/deployment',
            'scripts/maintenance',
            'config',
            'config/docker',
            'config/nginx',
            'config/apache',
            'tools',
            'tools/debug',
            'tools/analysis',
            'tools/utilities',
            'db',
            'logs',
            'dist',
            'public'
        ];

        foreach ($requiredDirs as $dir) {
            if (is_dir($dir)) {
                $this->results[] = "✅ Directory exists: $dir";
            } else {
                $this->errors[] = "❌ Missing directory: $dir";
            }
        }
    }

    public function verifyCoreFiles(): void {
        echo "📄 Verifying Core Files...\n";
        
        $coreFiles = [
            // WordPress Core
            'blackcnote/wp-config.php',
            'blackcnote/index.php',
            'blackcnote/.htaccess',
            
            // React App
            'react-app/package.json',
            'react-app/vite.config.ts',
            'react-app/index.html',
            
            // HYIPLab
            'hyiplab/composer.json',
            'hyiplab/index.php',
            
            // Docker
            'config/docker/docker-compose.yml',
            'config/docker/docker-compose.prod.yml',
            
            // Documentation
            'README.md',
            'docs/README.md',
            
            // Scripts
            'scripts/setup/database-setup.php',
            'scripts/testing/docker-environment-test.php',
            'scripts/maintenance/fix-docker-issues.php',
            
            // Configuration
            'config/nginx/blackcnote.conf',
            'config/apache/000-default.conf',
            
            // Database
            'db/blackcnote.sql',
            
            // Git
            '.gitignore'
        ];

        foreach ($coreFiles as $file) {
            if (file_exists($file)) {
                $this->results[] = "✅ File exists: $file";
            } else {
                $this->errors[] = "❌ Missing file: $file";
            }
        }
    }

    public function verifyWordPressComponents(): void {
        echo "🔧 Verifying WordPress Components...\n";
        
        // Check theme files
        $themeFiles = [
            'blackcnote/wp-content/themes/blackcnote/style.css',
            'blackcnote/wp-content/themes/blackcnote/index.php',
            'blackcnote/wp-content/themes/blackcnote/functions.php',
            'blackcnote/wp-content/themes/blackcnote/header.php',
            'blackcnote/wp-content/themes/blackcnote/footer.php'
        ];

        foreach ($themeFiles as $file) {
            if (file_exists($file)) {
                $this->results[] = "✅ Theme file: $file";
            } else {
                $this->warnings[] = "⚠️ Missing theme file: $file";
            }
        }

        // Check plugin files
        $pluginFiles = [
            'blackcnote/wp-content/plugins/blackcnote-hyiplab/blackcnote-hyiplab.php',
            'blackcnote/wp-content/plugins/blackcnote-hyiplab/readme.txt'
        ];

        foreach ($pluginFiles as $file) {
            if (file_exists($file)) {
                $this->results[] = "✅ Plugin file: $file";
            } else {
                $this->warnings[] = "⚠️ Missing plugin file: $file";
            }
        }
    }

    public function verifyReactComponents(): void {
        echo "⚛️ Verifying React Components...\n";
        
        $reactFiles = [
            'react-app/src/App.tsx',
            'react-app/src/main.tsx',
            'react-app/src/index.css',
            'react-app/public/index.html'
        ];

        foreach ($reactFiles as $file) {
            if (file_exists($file)) {
                $this->results[] = "✅ React file: $file";
            } else {
                $this->warnings[] = "⚠️ Missing React file: $file";
            }
        }

        // Check package.json dependencies
        if (file_exists('react-app/package.json')) {
            $packageJson = json_decode(file_get_contents('react-app/package.json'), true);
            if ($packageJson && isset($packageJson['dependencies'])) {
                $requiredDeps = ['react', 'react-dom', 'react-router-dom'];
                foreach ($requiredDeps as $dep) {
                    if (isset($packageJson['dependencies'][$dep])) {
                        $this->results[] = "✅ React dependency: $dep";
                    } else {
                        $this->warnings[] = "⚠️ Missing React dependency: $dep";
                    }
                }
            }
        }
    }

    public function verifyHYIPLabComponents(): void {
        echo "💰 Verifying HYIPLab Components...\n";
        
        $hyiplabFiles = [
            'hyiplab/app/Controllers/',
            'hyiplab/app/Models/',
            'hyiplab/app/Services/',
            'hyiplab/views/admin/',
            'hyiplab/views/user/',
            'hyiplab/routes/',
            'hyiplab/assets/'
        ];

        foreach ($hyiplabFiles as $dir) {
            if (is_dir($dir)) {
                $this->results[] = "✅ HYIPLab directory: $dir";
            } else {
                $this->warnings[] = "⚠️ Missing HYIPLab directory: $dir";
            }
        }
    }

    public function verifyDocumentation(): void {
        echo "📚 Verifying Documentation...\n";
        
        $docFiles = [
            'docs/setup/INSTALLATION.md',
            'docs/setup/DOCKER-SETUP.md',
            'docs/setup/CONFIGURATION-GUIDE.md',
            'docs/development/DEVELOPMENT-GUIDE.md',
            'docs/development/LOCAL-DEVELOPMENT.md',
            'docs/deployment/DEPLOYMENT-GUIDE.md',
            'docs/troubleshooting/TROUBLESHOOTING.md',
            'docs/troubleshooting/DOCKER-TROUBLESHOOTING.md'
        ];

        foreach ($docFiles as $file) {
            if (file_exists($file)) {
                $this->results[] = "✅ Documentation: $file";
            } else {
                $this->warnings[] = "⚠️ Missing documentation: $file";
            }
        }
    }

    public function verifyScripts(): void {
        echo "🔧 Verifying Scripts...\n";
        
        $scriptFiles = [
            'scripts/setup/database-setup.php',
            'scripts/testing/docker-environment-test.php',
            'scripts/testing/docker-health-check.ps1',
            'scripts/maintenance/fix-docker-issues.php'
        ];

        foreach ($scriptFiles as $file) {
            if (file_exists($file)) {
                $this->results[] = "✅ Script: $file";
            } else {
                $this->warnings[] = "⚠️ Missing script: $file";
            }
        }
    }

    public function verifyConfiguration(): void {
        echo "⚙️ Verifying Configuration...\n";
        
        $configFiles = [
            'config/docker/docker-compose.yml',
            'config/nginx/blackcnote.conf',
            'config/apache/000-default.conf'
        ];

        foreach ($configFiles as $file) {
            if (file_exists($file)) {
                $this->results[] = "✅ Configuration: $file";
            } else {
                $this->warnings[] = "⚠️ Missing configuration: $file";
            }
        }
    }

    public function verifyDockerEnvironment(): void {
        echo "🐳 Verifying Docker Environment...\n";
        
        // Check if Docker is available
        $dockerVersion = shell_exec('docker --version 2>&1');
        if ($dockerVersion && strpos($dockerVersion, 'Docker version') !== false) {
            $this->results[] = "✅ Docker is available";
        } else {
            $this->warnings[] = "⚠️ Docker may not be available";
        }

        // Check if docker-compose is available
        $dockerComposeVersion = shell_exec('docker-compose --version 2>&1');
        if ($dockerComposeVersion && strpos($dockerComposeVersion, 'docker-compose version') !== false) {
            $this->results[] = "✅ Docker Compose is available";
        } else {
            $this->warnings[] = "⚠️ Docker Compose may not be available";
        }

        // Check if docker-compose.yml is valid
        if (file_exists('config/docker/docker-compose.yml')) {
            $yamlContent = file_get_contents('config/docker/docker-compose.yml');
            if (strpos($yamlContent, 'version:') !== false && strpos($yamlContent, 'services:') !== false) {
                $this->results[] = "✅ Docker Compose file is valid";
            } else {
                $this->errors[] = "❌ Docker Compose file appears invalid";
            }
        }
    }

    public function verifyDatabaseSetup(): void {
        echo "🗄️ Verifying Database Setup...\n";
        
        if (file_exists('db/blackcnote.sql')) {
            $sqlContent = file_get_contents('db/blackcnote.sql');
            if (strpos($sqlContent, 'CREATE TABLE') !== false) {
                $this->results[] = "✅ Database schema file exists";
            } else {
                $this->warnings[] = "⚠️ Database schema file may be empty";
            }
        } else {
            $this->warnings[] = "⚠️ Database schema file missing";
        }

        if (file_exists('scripts/setup/database-setup.php')) {
            $this->results[] = "✅ Database setup script exists";
        } else {
            $this->warnings[] = "⚠️ Database setup script missing";
        }
    }

    public function verifySecurity(): void {
        echo "🔒 Verifying Security...\n";
        
        // Check .gitignore
        if (file_exists('.gitignore')) {
            $gitignore = file_get_contents('.gitignore');
            $securityItems = ['wp-config.php', '.env', 'logs/', 'uploads/'];
            foreach ($securityItems as $item) {
                if (strpos($gitignore, $item) !== false) {
                    $this->results[] = "✅ Security item in .gitignore: $item";
                } else {
                    $this->warnings[] = "⚠️ Security item not in .gitignore: $item";
                }
            }
        }

        // Check WordPress security
        if (file_exists('blackcnote/wp-config.php')) {
            $wpConfig = file_get_contents('blackcnote/wp-config.php');
            if (strpos($wpConfig, 'WP_DEBUG') !== false) {
                $this->results[] = "✅ WordPress debug configuration present";
            }
            if (strpos($wpConfig, 'DB_PASSWORD') !== false) {
                $this->results[] = "✅ Database password configuration present";
            }
        }
    }

    public function generateReport(): void {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🎯 BLACKCNOTE PROJECT VERIFICATION REPORT\n";
        echo str_repeat("=", 60) . "\n\n";
        
        $totalChecks = count($this->results) + count($this->errors) + count($this->warnings);
        
        echo "📊 VERIFICATION SUMMARY:\n";
        echo "   Total Checks: $totalChecks\n";
        echo "   ✅ Passed: " . count($this->results) . "\n";
        echo "   ❌ Failed: " . count($this->errors) . "\n";
        echo "   ⚠️ Warnings: " . count($this->warnings) . "\n\n";
        
        if (!empty($this->results)) {
            echo "✅ SUCCESSES:\n";
            foreach ($this->results as $result) {
                echo "   $result\n";
            }
            echo "\n";
        }
        
        if (!empty($this->warnings)) {
            echo "⚠️ WARNINGS:\n";
            foreach ($this->warnings as $warning) {
                echo "   $warning\n";
            }
            echo "\n";
        }
        
        if (!empty($this->errors)) {
            echo "❌ ERRORS:\n";
            foreach ($this->errors as $error) {
                echo "   $error\n";
            }
            echo "\n";
        }
        
        echo "🎯 PROJECT STATUS:\n";
        if (empty($this->errors)) {
            echo "   🎉 EXCELLENT - Project is well-organized and ready!\n";
        } elseif (count($this->errors) < 5) {
            echo "   ✅ GOOD - Minor issues need attention\n";
        } else {
            echo "   ⚠️ NEEDS WORK - Several issues need to be resolved\n";
        }
        
        echo "\n📋 NEXT STEPS:\n";
        if (!empty($this->errors)) {
            echo "   1. Fix the errors listed above\n";
        }
        if (!empty($this->warnings)) {
            echo "   2. Address the warnings for optimal setup\n";
        }
        echo "   3. Run the project: start-blackcnote.bat\n";
        echo "   4. Test all functionality\n";
        echo "   5. Review documentation: docs/README.md\n";
        
        echo "\n" . str_repeat("=", 60) . "\n";
    }

    public function run(): void {
        echo "🔍 BlackCnote Project Verification\n";
        echo "==================================\n\n";
        
        $this->verifyDirectoryStructure();
        $this->verifyCoreFiles();
        $this->verifyWordPressComponents();
        $this->verifyReactComponents();
        $this->verifyHYIPLabComponents();
        $this->verifyDocumentation();
        $this->verifyScripts();
        $this->verifyConfiguration();
        $this->verifyDockerEnvironment();
        $this->verifyDatabaseSetup();
        $this->verifySecurity();
        
        $this->generateReport();
    }
}

// Run the verifier
if (php_sapi_name() === 'cli') {
    $verifier = new ProjectVerifier();
    $verifier->run();
} 