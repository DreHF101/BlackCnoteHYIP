<?php
/**
 * Comprehensive Page Check for BlackCnote Theme
 * Checks for all missing pages, templates, and functionality
 * 
 * @package BlackCnote
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== BlackCnote Comprehensive Page Check ===\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";

// Define project paths
$projectRoot = dirname(dirname(__DIR__));
$themePath = $projectRoot . '/blackcnote/wp-content/themes/blackcnote';
$incPath = $themePath . '/inc';
$adminPath = $themePath . '/admin';
$assetsPath = $themePath . '/assets';

echo "Project Root: $projectRoot\n";
echo "Theme Path: $themePath\n\n";

// Test 1: Check if theme directory exists
echo "1. Theme Directory Check:\n";
if (is_dir($themePath)) {
    echo "   ✅ Theme directory exists\n";
} else {
    echo "   ❌ Theme directory missing: $themePath\n";
    exit(1);
}

// Test 2: Check required page templates
echo "\n2. Page Template Check:\n";
$requiredPages = [
    'page-about.php' => 'About Us Page',
    'page-services.php' => 'Services Page', 
    'page-contact.php' => 'Contact Page',
    'page-privacy.php' => 'Privacy Policy Page',
    'page-terms.php' => 'Terms of Service Page',
    'page-plans.php' => 'Investment Plans Page',
    'page-dashboard.php' => 'Dashboard Page',
    'page-home.php' => 'Home Page',
    'front-page.php' => 'Front Page',
    'index.php' => 'Index Page',
    'header.php' => 'Header Template',
    'footer.php' => 'Footer Template',
    'functions.php' => 'Functions File',
    'style.css' => 'Main Stylesheet'
];

$missingPages = [];
foreach ($requiredPages as $file => $description) {
    $filePath = $themePath . '/' . $file;
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        if ($size > 100) {
            echo "   ✅ $description ($file) - " . number_format($size) . " bytes\n";
        } else {
            echo "   ⚠️  $description ($file) - Empty or too small (" . number_format($size) . " bytes)\n";
            $missingPages[] = $file;
        }
    } else {
        echo "   ❌ $description ($file) - Missing\n";
        $missingPages[] = $file;
    }
}

// Test 3: Check template files
echo "\n3. Template File Check:\n";
$requiredTemplates = [
    'template-blackcnote-dashboard.php' => 'Dashboard Template',
    'template-blackcnote-plans.php' => 'Plans Template',
    'template-blackcnote-transactions.php' => 'Transactions Template'
];

foreach ($requiredTemplates as $file => $description) {
    $filePath = $themePath . '/' . $file;
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        if ($size > 100) {
            echo "   ✅ $description ($file) - " . number_format($size) . " bytes\n";
        } else {
            echo "   ⚠️  $description ($file) - Empty or too small (" . number_format($size) . " bytes)\n";
            $missingPages[] = $file;
        }
    } else {
        echo "   ❌ $description ($file) - Missing\n";
        $missingPages[] = $file;
    }
}

// Test 4: Check include files
echo "\n4. Include Files Check:\n";
$requiredIncludes = [
    'admin-functions.php' => 'Admin Functions',
    'template-functions.php' => 'Template Functions',
    'template-tags.php' => 'Template Tags',
    'menu-registration.php' => 'Menu Registration'
];

foreach ($requiredIncludes as $file => $description) {
    $filePath = $incPath . '/' . $file;
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        echo "   ✅ $description ($file) - " . number_format($size) . " bytes\n";
    } else {
        echo "   ❌ $description ($file) - Missing\n";
        $missingPages[] = $file;
    }
}

// Test 5: Check admin files
echo "\n5. Admin Files Check:\n";
$requiredAdminFiles = [
    'admin.css' => 'Admin CSS',
    'admin.js' => 'Admin JavaScript'
];

foreach ($requiredAdminFiles as $file => $description) {
    $filePath = $adminPath . '/' . $file;
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        echo "   ✅ $description ($file) - " . number_format($size) . " bytes\n";
    } else {
        echo "   ❌ $description ($file) - Missing\n";
        $missingPages[] = $file;
    }
}

// Test 6: Check assets
echo "\n6. Assets Check:\n";
$requiredAssets = [
    'css/blackcnote-theme.css' => 'Main Theme CSS',
    'js/blackcnote-theme.js' => 'Main Theme JS',
    'img/header-logo.png' => 'Header Logo',
    'img/hero-logo.png' => 'Hero Logo'
];

foreach ($requiredAssets as $file => $description) {
    $filePath = $assetsPath . '/' . $file;
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        echo "   ✅ $description ($file) - " . number_format($size) . " bytes\n";
    } else {
        echo "   ❌ $description ($file) - Missing\n";
        $missingPages[] = $file;
    }
}

// Test 7: Check functions.php content
echo "\n7. Functions.php Content Check:\n";
$functionsPath = $themePath . '/functions.php';
if (file_exists($functionsPath)) {
    $content = file_get_contents($functionsPath);
    
    $requiredFunctions = [
        'blackcnote_setup' => 'Theme Setup Function',
        'blackcnote_enqueue_scripts' => 'Script Enqueue Function',
        'blackcnote_create_default_pages' => 'Page Creation Function',
        'blackcnote_admin_functions' => 'Admin Functions Include',
        'blackcnote_live_editing_api' => 'Live Editing API'
    ];
    
    foreach ($requiredFunctions as $function => $description) {
        if (strpos($content, $function) !== false) {
            echo "   ✅ $description ($function)\n";
        } else {
            echo "   ❌ $description ($function) - Missing\n";
        }
    }
    
    // Check for required includes
    $requiredIncludes = [
        'admin-functions.php' => 'Admin Functions Include',
        'template-functions.php' => 'Template Functions Include',
        'template-tags.php' => 'Template Tags Include'
    ];
    
    foreach ($requiredIncludes as $include => $description) {
        if (strpos($content, $include) !== false) {
            echo "   ✅ $description ($include)\n";
        } else {
            echo "   ❌ $description ($include) - Missing\n";
        }
    }
} else {
    echo "   ❌ functions.php missing\n";
}

// Test 8: Check page content quality
echo "\n8. Page Content Quality Check:\n";
$pagesToCheck = [
    'page-about.php' => 'About Us',
    'page-services.php' => 'Services',
    'page-contact.php' => 'Contact',
    'page-privacy.php' => 'Privacy Policy',
    'page-terms.php' => 'Terms of Service',
    'page-plans.php' => 'Investment Plans',
    'page-dashboard.php' => 'Dashboard'
];

foreach ($pagesToCheck as $file => $description) {
    $filePath = $themePath . '/' . $file;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $size = strlen($content);
        
        if ($size > 1000) {
            echo "   ✅ $description - Good content (" . number_format($size) . " chars)\n";
        } elseif ($size > 100) {
            echo "   ⚠️  $description - Minimal content (" . number_format($size) . " chars)\n";
        } else {
            echo "   ❌ $description - Empty or missing content (" . number_format($size) . " chars)\n";
        }
    } else {
        echo "   ❌ $description - File missing\n";
    }
}

// Test 9: Check for blank pages
echo "\n9. Blank Page Detection:\n";
$blankPages = [];
foreach ($pagesToCheck as $file => $description) {
    $filePath = $themePath . '/' . $file;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $content = trim($content);
        
        // Check if page has meaningful content
        if (strlen($content) < 500 || 
            strpos($content, 'default-content') === false && 
            strpos($content, 'the_content()') === false) {
            $blankPages[] = $file;
            echo "   ⚠️  $description ($file) - May be blank or incomplete\n";
        } else {
            echo "   ✅ $description ($file) - Has content\n";
        }
    }
}

// Test 10: Check startup scripts
echo "\n10. Startup Scripts Check:\n";
$startupScripts = [
    'start-blackcnote-complete.ps1' => 'Complete PowerShell Startup',
    'start-blackcnote.bat' => 'Windows Batch Startup',
    'start-blackcnote.sh' => 'Linux/WSL Startup'
];

foreach ($startupScripts as $file => $description) {
    $filePath = $projectRoot . '/' . $file;
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        echo "   ✅ $description ($file) - " . number_format($size) . " bytes\n";
    } else {
        echo "   ❌ $description ($file) - Missing\n";
    }
}

// Test 11: Check Docker configuration
echo "\n11. Docker Configuration Check:\n";
$dockerFiles = [
    'config/docker/docker-compose.yml' => 'Docker Compose',
    'config/docker/docker-compose.prod.yml' => 'Production Docker Compose',
    'config/nginx/blackcnote-docker.conf' => 'Nginx Configuration'
];

foreach ($dockerFiles as $file => $description) {
    $filePath = $projectRoot . '/' . $file;
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        echo "   ✅ $description ($file) - " . number_format($size) . " bytes\n";
    } else {
        echo "   ❌ $description ($file) - Missing\n";
    }
}

// Test 12: Check React integration
echo "\n12. React Integration Check:\n";
$reactFiles = [
    'react-app/package.json' => 'React Package.json',
    'react-app/src/App.tsx' => 'React App Component',
    'react-app/src/services/LiveSyncService.ts' => 'Live Sync Service',
    'react-app/src/hooks/useLiveEditing.ts' => 'Live Editing Hook'
];

foreach ($reactFiles as $file => $description) {
    $filePath = $projectRoot . '/' . $file;
    if (file_exists($filePath)) {
        $size = filesize($filePath);
        echo "   ✅ $description ($file) - " . number_format($size) . " bytes\n";
    } else {
        echo "   ❌ $description ($file) - Missing\n";
    }
}

// Summary
echo "\n=== SUMMARY ===\n";
if (empty($missingPages) && empty($blankPages)) {
    echo "✅ All pages and templates are present and functional!\n";
} else {
    if (!empty($missingPages)) {
        echo "❌ Missing files:\n";
        foreach ($missingPages as $file) {
            echo "   - $file\n";
        }
    }
    
    if (!empty($blankPages)) {
        echo "⚠️  Potentially blank or incomplete pages:\n";
        foreach ($blankPages as $file) {
            echo "   - $file\n";
        }
    }
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Start Docker environment: .\\start-blackcnote-complete.ps1\n";
echo "2. Activate BlackCnote theme in WordPress admin\n";
echo "3. Visit all pages to verify functionality\n";
echo "4. Test admin settings at: http://localhost:8888/wp-admin/admin.php?page=blackcnote-settings\n";
echo "5. Test live editing features\n";

echo "\nCompleted at: " . date('Y-m-d H:i:s') . "\n";
echo "=== END OF COMPREHENSIVE PAGE CHECK ===\n"; 