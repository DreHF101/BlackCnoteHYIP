<?php
/**
 * BlackCnote Synchronization Test
 * Verifies all services, features, and integrations are working correctly
 */

declare(strict_types=1);

class BlackCnoteSynchronizationTest {
    private $results = [];
    private $services = [
        'wordpress' => 'http://localhost:8888',
        'react' => 'http://localhost:5174',
        'phpmyadmin' => 'http://localhost:8080',
        'mailhog' => 'http://localhost:8025',
        'redis_commander' => 'http://localhost:8081',
        'browsersync' => 'http://localhost:3000'
    ];
    
    public function run() {
        echo "=== BLACKCNOTE SYNCHRONIZATION TEST ===\n\n";
        
        $this->testDockerServices();
        $this->testFileStructure();
        $this->testThemeFiles();
        $this->testReactIntegration();
        $this->testWordPressIntegration();
        $this->testLiveEditing();
        $this->testDatabaseConnection();
        $this->testGitIntegration();
        
        $this->generateFinalReport();
    }
    
    private function testDockerServices() {
        echo "1. DOCKER SERVICES VERIFICATION:\n";
        echo "===============================\n";
        
        $runningServices = [
            'blackcnote-wordpress' => 'WordPress',
            'blackcnote-react' => 'React App',
            'blackcnote-mysql' => 'MySQL Database',
            'blackcnote-redis' => 'Redis Cache',
            'blackcnote-phpmyadmin' => 'phpMyAdmin',
            'blackcnote-mailhog' => 'MailHog',
            'blackcnote-redis-commander' => 'Redis Commander',
            'blackcnote-browsersync' => 'Browsersync',
            'blackcnote-file-watcher' => 'File Watcher',
            'blackcnote-dev-tools' => 'Dev Tools'
        ];
        
        foreach ($runningServices as $container => $service) {
            $output = shell_exec("docker ps --filter name=$container --format '{{.Names}}' 2>/dev/null");
            if ($output && trim($output) === $container) {
                echo "✅ $service ($container) - RUNNING\n";
                $this->results['docker_services'][$service] = true;
            } else {
                echo "❌ $service ($container) - NOT RUNNING\n";
                $this->results['docker_services'][$service] = false;
            }
        }
        echo "\n";
    }
    
    private function testFileStructure() {
        echo "2. FILE STRUCTURE VERIFICATION:\n";
        echo "===============================\n";
        
        $requiredPaths = [
            'blackcnote/wp-content/themes/blackcnote/' => 'Theme Directory',
            'blackcnote/wp-content/themes/blackcnote/style.css' => 'Theme Style',
            'blackcnote/wp-content/themes/blackcnote/functions.php' => 'Theme Functions',
            'blackcnote/wp-content/themes/blackcnote/header.php' => 'Theme Header',
            'blackcnote/wp-content/themes/blackcnote/footer.php' => 'Theme Footer',
            'blackcnote/wp-content/themes/blackcnote/index.php' => 'Theme Index',
            'blackcnote/wp-content/themes/blackcnote/assets/css/blackcnote-theme.css' => 'Enhanced CSS',
            'blackcnote/wp-content/themes/blackcnote/assets/js/blackcnote-theme.js' => 'Enhanced JS',
            'react-app/src/App.tsx' => 'React App',
            'react-app/src/main.tsx' => 'React Entry',
            'config/docker/docker-compose.yml' => 'Docker Config',
            '.gitignore' => 'Git Ignore'
        ];
        
        foreach ($requiredPaths as $path => $description) {
            if (file_exists($path)) {
                echo "✅ $description - FOUND\n";
                $this->results['file_structure'][$description] = true;
            } else {
                echo "❌ $description - NOT FOUND\n";
                $this->results['file_structure'][$description] = false;
            }
        }
        echo "\n";
    }
    
    private function testThemeFiles() {
        echo "3. THEME FILES VERIFICATION:\n";
        echo "============================\n";
        
        $themeFiles = [
            'blackcnote/wp-content/themes/blackcnote/inc/menu-registration.php' => 'Menu Registration',
            'blackcnote/wp-content/themes/blackcnote/inc/admin-functions.php' => 'Admin Functions',
            'blackcnote/wp-content/themes/blackcnote/inc/backend-settings-manager.php' => 'Backend Settings',
            'blackcnote/wp-content/themes/blackcnote/inc/widgets.php' => 'Widgets',
            'blackcnote/wp-content/themes/blackcnote/inc/full-content-checker.php' => 'Content Checker',
            'blackcnote/wp-content/themes/blackcnote/template-parts/dashboard.php' => 'Dashboard Template',
            'blackcnote/wp-content/themes/blackcnote/template-parts/home-cta.php' => 'Home CTA Template',
            'blackcnote/wp-content/themes/blackcnote/template-parts/home-features.php' => 'Home Features Template'
        ];
        
        foreach ($themeFiles as $file => $description) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if (strlen($content) > 100) {
                    echo "✅ $description - FOUND (Content: " . strlen($content) . " bytes)\n";
                    $this->results['theme_files'][$description] = true;
                } else {
                    echo "⚠️ $description - FOUND (Empty/Minimal)\n";
                    $this->results['theme_files'][$description] = false;
                }
            } else {
                echo "❌ $description - NOT FOUND\n";
                $this->results['theme_files'][$description] = false;
            }
        }
        echo "\n";
    }
    
    private function testReactIntegration() {
        echo "4. REACT INTEGRATION VERIFICATION:\n";
        echo "==================================\n";
        
        $reactFiles = [
            'react-app/src/App.tsx' => 'Main App Component',
            'react-app/src/main.tsx' => 'App Entry Point',
            'react-app/src/components/Header.tsx' => 'Header Component',
            'react-app/src/components/Footer.tsx' => 'Footer Component',
            'react-app/src/pages/Home.tsx' => 'Home Page',
            'react-app/src/pages/About.tsx' => 'About Page',
            'react-app/src/pages/Calculator.tsx' => 'Calculator Page',
            'react-app/src/pages/Contact.tsx' => 'Contact Page',
            'react-app/package.json' => 'Package Config',
            'react-app/vite.config.ts' => 'Vite Config'
        ];
        
        foreach ($reactFiles as $file => $description) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if (strlen($content) > 50) {
                    echo "✅ $description - FOUND (Content: " . strlen($content) . " bytes)\n";
                    $this->results['react_integration'][$description] = true;
                } else {
                    echo "⚠️ $description - FOUND (Empty/Minimal)\n";
                    $this->results['react_integration'][$description] = false;
                }
            } else {
                echo "❌ $description - NOT FOUND\n";
                $this->results['react_integration'][$description] = false;
            }
        }
        echo "\n";
    }
    
    private function testWordPressIntegration() {
        echo "5. WORDPRESS INTEGRATION VERIFICATION:\n";
        echo "======================================\n";
        
        // Test WordPress core files
        $wpFiles = [
            'blackcnote/wp-config.php' => 'WordPress Config',
            'blackcnote/wp-blog-header.php' => 'Blog Header',
            'blackcnote/wp-load.php' => 'WordPress Loader',
            'blackcnote/index.php' => 'WordPress Index'
        ];
        
        foreach ($wpFiles as $file => $description) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if (strlen($content) > 50) {
                    echo "✅ $description - FOUND (Content: " . strlen($content) . " bytes)\n";
                    $this->results['wordpress_integration'][$description] = true;
                } else {
                    echo "⚠️ $description - FOUND (Empty/Minimal)\n";
                    $this->results['wordpress_integration'][$description] = false;
                }
            } else {
                echo "❌ $description - NOT FOUND\n";
                $this->results['wordpress_integration'][$description] = false;
            }
        }
        
        // Test theme activation
        if (file_exists('blackcnote/wp-content/themes/blackcnote/style.css')) {
            $themeHeader = file_get_contents('blackcnote/wp-content/themes/blackcnote/style.css');
            if (strpos($themeHeader, 'Theme Name: BlackCnote') !== false) {
                echo "✅ BlackCnote Theme - PROPERLY CONFIGURED\n";
                $this->results['wordpress_integration']['Theme Configuration'] = true;
            } else {
                echo "❌ BlackCnote Theme - NOT PROPERLY CONFIGURED\n";
                $this->results['wordpress_integration']['Theme Configuration'] = false;
            }
        }
        echo "\n";
    }
    
    private function testLiveEditing() {
        echo "6. LIVE EDITING VERIFICATION:\n";
        echo "=============================\n";
        
        $liveEditingFiles = [
            'react-app/bs-config.js' => 'Browsersync Config',
            'react-app/vite.config.ts' => 'Vite Config',
            'react-app/package.json' => 'Package Dependencies',
            'config/nginx/blackcnote-docker.conf' => 'Nginx Config'
        ];
        
        foreach ($liveEditingFiles as $file => $description) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if (strlen($content) > 50) {
                    echo "✅ $description - FOUND (Content: " . strlen($content) . " bytes)\n";
                    $this->results['live_editing'][$description] = true;
                } else {
                    echo "⚠️ $description - FOUND (Empty/Minimal)\n";
                    $this->results['live_editing'][$description] = false;
                }
            } else {
                echo "❌ $description - NOT FOUND\n";
                $this->results['live_editing'][$description] = false;
            }
        }
        
        // Check for live editing features in theme
        if (file_exists('blackcnote/wp-content/themes/blackcnote/assets/js/blackcnote-theme.js')) {
            $jsContent = file_get_contents('blackcnote/wp-content/themes/blackcnote/assets/js/blackcnote-theme.js');
            if (strpos($jsContent, 'live-editing') !== false || strpos($jsContent, 'browsersync') !== false) {
                echo "✅ Live Editing JavaScript - CONFIGURED\n";
                $this->results['live_editing']['JavaScript Integration'] = true;
            } else {
                echo "⚠️ Live Editing JavaScript - NOT CONFIGURED\n";
                $this->results['live_editing']['JavaScript Integration'] = false;
            }
        }
        echo "\n";
    }
    
    private function testDatabaseConnection() {
        echo "7. DATABASE CONNECTION VERIFICATION:\n";
        echo "====================================\n";
        
        // Test MySQL container
        $mysqlOutput = shell_exec("docker exec blackcnote-mysql mysql -u root -proot -e 'SELECT 1;' 2>/dev/null");
        if ($mysqlOutput && strpos($mysqlOutput, '1') !== false) {
            echo "✅ MySQL Database - CONNECTED\n";
            $this->results['database']['MySQL'] = true;
        } else {
            echo "❌ MySQL Database - NOT CONNECTED\n";
            $this->results['database']['MySQL'] = false;
        }
        
        // Test Redis container
        $redisOutput = shell_exec("docker exec blackcnote-redis redis-cli ping 2>/dev/null");
        if ($redisOutput && trim($redisOutput) === 'PONG') {
            echo "✅ Redis Cache - CONNECTED\n";
            $this->results['database']['Redis'] = true;
        } else {
            echo "❌ Redis Cache - NOT CONNECTED\n";
            $this->results['database']['Redis'] = false;
        }
        
        // Test phpMyAdmin
        $phpmyadminOutput = shell_exec("curl -s -o /dev/null -w '%{http_code}' http://localhost:8080 2>/dev/null");
        if ($phpmyadminOutput && $phpmyadminOutput === '200') {
            echo "✅ phpMyAdmin - ACCESSIBLE\n";
            $this->results['database']['phpMyAdmin'] = true;
        } else {
            echo "❌ phpMyAdmin - NOT ACCESSIBLE\n";
            $this->results['database']['phpMyAdmin'] = false;
        }
        echo "\n";
    }
    
    private function testGitIntegration() {
        echo "8. GIT INTEGRATION VERIFICATION:\n";
        echo "================================\n";
        
        // Check if git repository exists
        if (is_dir('.git')) {
            echo "✅ Git Repository - INITIALIZED\n";
            $this->results['git']['Repository'] = true;
            
            // Check remote origin
            $remoteOutput = shell_exec("git remote -v 2>/dev/null");
            if ($remoteOutput && strpos($remoteOutput, 'origin') !== false) {
                echo "✅ Git Remote - CONFIGURED\n";
                $this->results['git']['Remote'] = true;
            } else {
                echo "❌ Git Remote - NOT CONFIGURED\n";
                $this->results['git']['Remote'] = false;
            }
            
            // Check .gitignore
            if (file_exists('.gitignore')) {
                echo "✅ Git Ignore - CONFIGURED\n";
                $this->results['git']['GitIgnore'] = true;
            } else {
                echo "❌ Git Ignore - NOT CONFIGURED\n";
                $this->results['git']['GitIgnore'] = false;
            }
        } else {
            echo "❌ Git Repository - NOT INITIALIZED\n";
            $this->results['git']['Repository'] = false;
        }
        echo "\n";
    }
    
    private function generateFinalReport() {
        echo "=== FINAL SYNCHRONIZATION REPORT ===\n";
        echo "====================================\n\n";
        
        $totalTests = 0;
        $passedTests = 0;
        
        foreach ($this->results as $category => $tests) {
            $categoryTotal = count($tests);
            $categoryPassed = count(array_filter($tests));
            $totalTests += $categoryTotal;
            $passedTests += $categoryPassed;
            
            $percentage = ($categoryPassed / $categoryTotal) * 100;
            echo strtoupper(str_replace('_', ' ', $category)) . ": " . number_format($percentage, 1) . "% ($categoryPassed/$categoryTotal)\n";
        }
        
        $overallPercentage = ($passedTests / $totalTests) * 100;
        echo "\nOVERALL SYNCHRONIZATION: " . number_format($overallPercentage, 1) . "% ($passedTests/$totalTests)\n";
        
        if ($overallPercentage >= 95) {
            echo "🏆 PERFECT: All systems synchronized and operational!\n";
        } elseif ($overallPercentage >= 90) {
            echo "✅ EXCELLENT: Most systems synchronized and operational!\n";
        } elseif ($overallPercentage >= 80) {
            echo "👍 GOOD: Good synchronization with minor issues!\n";
        } elseif ($overallPercentage >= 70) {
            echo "⚠️ FAIR: Some synchronization issues detected!\n";
        } else {
            echo "❌ NEEDS WORK: Significant synchronization issues!\n";
        }
        
        echo "\n=== SERVICE STATUS ===\n";
        echo "WordPress: " . ($this->results['docker_services']['WordPress'] ?? false ? '✅ RUNNING' : '❌ STOPPED') . "\n";
        echo "React App: " . ($this->results['docker_services']['React App'] ?? false ? '✅ RUNNING' : '❌ STOPPED') . "\n";
        echo "MySQL: " . ($this->results['docker_services']['MySQL Database'] ?? false ? '✅ RUNNING' : '❌ STOPPED') . "\n";
        echo "Redis: " . ($this->results['docker_services']['Redis Cache'] ?? false ? '✅ RUNNING' : '❌ STOPPED') . "\n";
        echo "phpMyAdmin: " . ($this->results['docker_services']['phpMyAdmin'] ?? false ? '✅ RUNNING' : '❌ STOPPED') . "\n";
        echo "MailHog: " . ($this->results['docker_services']['MailHog'] ?? false ? '✅ RUNNING' : '❌ STOPPED') . "\n";
        echo "Browsersync: " . ($this->results['docker_services']['Browsersync'] ?? false ? '✅ RUNNING' : '❌ STOPPED') . "\n";
        
        echo "\n=== INTEGRATION STATUS ===\n";
        echo "Theme Files: " . (isset($this->results['theme_files']) && count(array_filter($this->results['theme_files'])) > 5 ? '✅ COMPLETE' : '❌ INCOMPLETE') . "\n";
        echo "React Integration: " . (isset($this->results['react_integration']) && count(array_filter($this->results['react_integration'])) > 7 ? '✅ COMPLETE' : '❌ INCOMPLETE') . "\n";
        echo "WordPress Integration: " . (isset($this->results['wordpress_integration']) && count(array_filter($this->results['wordpress_integration'])) > 3 ? '✅ COMPLETE' : '❌ INCOMPLETE') . "\n";
        echo "Live Editing: " . (isset($this->results['live_editing']) && count(array_filter($this->results['live_editing'])) > 3 ? '✅ CONFIGURED' : '❌ NOT CONFIGURED') . "\n";
        echo "Database: " . (isset($this->results['database']) && count(array_filter($this->results['database'])) > 2 ? '✅ CONNECTED' : '❌ CONNECTED') . "\n";
        echo "Git: " . (isset($this->results['git']) && count(array_filter($this->results['git'])) > 1 ? '✅ CONFIGURED' : '❌ NOT CONFIGURED') . "\n";
        
        echo "\n=== SYNCHRONIZATION TEST COMPLETE ===\n";
    }
}

// Run the synchronization test
$test = new BlackCnoteSynchronizationTest();
$test->run(); 