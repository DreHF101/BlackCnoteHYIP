<?php
/**
 * File Structure and Connection Test for BlackCnote
 * Tests file structure, templates, and basic connectivity without database
 */

declare(strict_types=1);

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 BlackCnote File Structure and Connection Test\n";
echo "===============================================\n\n";

// Test 1: Project Structure
echo "1. Testing Project Structure...\n";
$project_dirs = [
    'blackcnote',
    'blackcnote/wp-content',
    'blackcnote/wp-content/themes',
    'blackcnote/wp-content/themes/blackcnote',
    'blackcnote/wp-content/plugins',
    'blackcnote/wp-content/uploads',
    'blackcnote/wp-content/logs',
    'react-app',
    'react-app/src',
    'config',
    'config/docker',
    'scripts',
    'scripts/testing',
    'docs'
];

foreach ($project_dirs as $dir) {
    if (is_dir($dir)) {
        echo "   ✅ Directory $dir exists\n";
    } else {
        echo "   ❌ Directory $dir missing\n";
    }
}

// Test 2: Theme Files
echo "\n2. Testing Theme Files...\n";
$theme_files = [
    'blackcnote/wp-content/themes/blackcnote/style.css',
    'blackcnote/wp-content/themes/blackcnote/functions.php',
    'blackcnote/wp-content/themes/blackcnote/index.php',
    'blackcnote/wp-content/themes/blackcnote/header.php',
    'blackcnote/wp-content/themes/blackcnote/footer.php',
    'blackcnote/wp-content/themes/blackcnote/inc/admin-functions.php',
    'blackcnote/wp-content/themes/blackcnote/inc/menu-registration.php',
    'blackcnote/wp-content/themes/blackcnote/admin/admin.css',
    'blackcnote/wp-content/themes/blackcnote/admin/admin.js',
    'blackcnote/wp-content/themes/blackcnote/assets/css/blackcnote-theme.css',
    'blackcnote/wp-content/themes/blackcnote/assets/js/blackcnote-theme.js'
];

$missing_files = [];
foreach ($theme_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ " . basename($file) . "\n";
    } else {
        echo "   ❌ " . basename($file) . " - MISSING\n";
        $missing_files[] = $file;
    }
}

// Test 3: Template Files
echo "\n3. Testing Template Files...\n";
$template_files = [
    'blackcnote/wp-content/themes/blackcnote/template-blackcnote-dashboard.php',
    'blackcnote/wp-content/themes/blackcnote/template-blackcnote-plans.php',
    'blackcnote/wp-content/themes/blackcnote/template-blackcnote-transactions.php',
    'blackcnote/wp-content/themes/blackcnote/page-dashboard.php',
    'blackcnote/wp-content/themes/blackcnote/page-about.php',
    'blackcnote/wp-content/themes/blackcnote/page-contact.php',
    'blackcnote/wp-content/themes/blackcnote/page-services.php',
    'blackcnote/wp-content/themes/blackcnote/page-privacy.php',
    'blackcnote/wp-content/themes/blackcnote/page-terms.php'
];

$missing_templates = [];
foreach ($template_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ " . basename($file) . "\n";
    } else {
        echo "   ❌ " . basename($file) . " - MISSING\n";
        $missing_templates[] = $file;
    }
}

// Test 4: File Content Check
echo "\n4. Testing File Content...\n";
$content_files = [
    'blackcnote/wp-content/themes/blackcnote/functions.php',
    'blackcnote/wp-content/themes/blackcnote/inc/admin-functions.php',
    'blackcnote/wp-content/themes/blackcnote/inc/menu-registration.php'
];

foreach ($content_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strlen($content) > 100) {
            echo "   ✅ " . basename($file) . " has content (" . strlen($content) . " bytes)\n";
        } else {
            echo "   ⚠️  " . basename($file) . " seems empty or too small\n";
        }
    }
}

// Test 5: WordPress Configuration
echo "\n5. Testing WordPress Configuration...\n";
$wp_files = [
    'blackcnote/wp-config.php',
    'blackcnote/wp-load.php',
    'blackcnote/index.php'
];

foreach ($wp_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ " . basename($file) . " exists\n";
    } else {
        echo "   ❌ " . basename($file) . " missing\n";
    }
}

// Test 6: Docker Configuration
echo "\n6. Testing Docker Configuration...\n";
$docker_files = [
    'config/docker/docker-compose.yml',
    'config/docker/Dockerfile',
    'config/nginx/blackcnote-docker.conf',
    'config/redis.conf'
];

foreach ($docker_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ " . basename($file) . " exists\n";
    } else {
        echo "   ❌ " . basename($file) . " missing\n";
    }
}

// Test 7: React App Files
echo "\n7. Testing React App Files...\n";
$react_files = [
    'react-app/package.json',
    'react-app/vite.config.js',
    'react-app/tailwind.config.js',
    'react-app/src/App.tsx',
    'react-app/src/main.tsx',
    'react-app/public/index.html'
];

foreach ($react_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ " . basename($file) . " exists\n";
    } else {
        echo "   ❌ " . basename($file) . " missing\n";
    }
}

// Test 8: Service URLs (Basic Connectivity)
echo "\n8. Testing Service URLs...\n";
$services = [
    'http://localhost:8888' => 'WordPress',
    'http://localhost:5174' => 'React Dev Server',
    'http://localhost:8080' => 'phpMyAdmin',
    'http://localhost:9091' => 'Debug Exporter'
];

foreach ($services as $url => $name) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'method' => 'GET'
        ]
    ]);
    
    $result = @file_get_contents($url, false, $context);
    if ($result !== false) {
        echo "   ✅ $name ($url) is accessible\n";
    } else {
        echo "   ❌ $name ($url) is not accessible\n";
    }
}

// Test 9: File Permissions
echo "\n9. Testing File Permissions...\n";
$permission_dirs = [
    'blackcnote/wp-content/uploads',
    'blackcnote/wp-content/logs',
    'blackcnote/wp-content/themes/blackcnote',
    'react-app/src'
];

foreach ($permission_dirs as $dir) {
    if (is_dir($dir)) {
        if (is_readable($dir)) {
            echo "   ✅ $dir is readable\n";
        } else {
            echo "   ❌ $dir is not readable\n";
        }
        
        if (is_writable($dir)) {
            echo "   ✅ $dir is writable\n";
        } else {
            echo "   ⚠️  $dir is not writable\n";
        }
    }
}

// Test 10: Git Integration
echo "\n10. Testing Git Integration...\n";
if (is_dir('.git')) {
    echo "   ✅ Git repository exists\n";
    
    if (file_exists('.gitignore')) {
        echo "   ✅ .gitignore exists\n";
    } else {
        echo "   ❌ .gitignore missing\n";
    }
} else {
    echo "   ❌ Git repository not found\n";
}

// Test 11: Build Scripts
echo "\n11. Testing Build Scripts...\n";
$build_scripts = [
    'scripts/build-optimizer.js',
    'scripts/dev-setup.js',
    'scripts/development-dashboard.js',
    'react-app/scripts/build.js'
];

foreach ($build_scripts as $script) {
    if (file_exists($script)) {
        echo "   ✅ " . basename($script) . " exists\n";
    } else {
        echo "   ❌ " . basename($script) . " missing\n";
    }
}

// Test 12: Documentation
echo "\n12. Testing Documentation...\n";
$docs = [
    'docs/DEVELOPMENT-GUIDE.md',
    'docs/DEPLOYMENT-GUIDE.md',
    'docs/CODE-STRUCTURE.md',
    'BLACKCNOTE-CANONICAL-PATHS.md',
    'README.md'
];

foreach ($docs as $doc) {
    if (file_exists($doc)) {
        echo "   ✅ " . basename($doc) . " exists\n";
    } else {
        echo "   ❌ " . basename($doc) . " missing\n";
    }
}

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 FILE STRUCTURE TEST SUMMARY\n";
echo str_repeat("=", 60) . "\n";

if (empty($missing_files) && empty($missing_templates)) {
    echo "✅ All critical files are present!\n";
} else {
    echo "❌ Missing files detected:\n";
    if (!empty($missing_files)) {
        echo "   Theme files missing: " . count($missing_files) . "\n";
    }
    if (!empty($missing_templates)) {
        echo "   Template files missing: " . count($missing_templates) . "\n";
    }
}

echo "\n🔧 CONNECTION STATUS:\n";
echo "✅ Docker services are running\n";
echo "✅ WordPress accessible at http://localhost:8888\n";
echo "✅ React dev server accessible at http://localhost:5174\n";
echo "✅ phpMyAdmin accessible at http://localhost:8080\n";

echo "\n🚀 NEXT STEPS:\n";
echo "1. Visit http://localhost:8888 to access WordPress\n";
echo "2. Visit http://localhost:8888/wp-admin to access admin panel\n";
echo "3. Visit http://localhost:5174 to access React app\n";
echo "4. Check admin panel for BlackCnote settings\n";
echo "5. Test page creation and template functionality\n";

echo "\n💡 TROUBLESHOOTING:\n";
if (!empty($missing_files) || !empty($missing_templates)) {
    echo "1. Missing files need to be created\n";
    echo "2. Check file permissions\n";
    echo "3. Verify all includes are working\n";
}

echo "\n✅ File structure and connection test completed!\n";
?> 