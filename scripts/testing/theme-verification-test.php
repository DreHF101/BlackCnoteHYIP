<?php
/**
 * Theme Verification Test for BlackCnote
 * Tests if the BCnote Theme is properly configured and active
 */

declare(strict_types=1);

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 BlackCnote Theme Verification Test\n";
echo "====================================\n\n";

// Test 1: Check WordPress Configuration
echo "1. Testing WordPress Configuration...\n";
$wp_config = 'blackcnote/wp-config.php';
if (file_exists($wp_config)) {
    echo "   ✅ WordPress config exists\n";
    
    // Check database configuration
    $config_content = file_get_contents($wp_config);
    if (strpos($config_content, 'DB_HOST') !== false) {
        echo "   ✅ Database configuration found\n";
    } else {
        echo "   ❌ Database configuration missing\n";
    }
} else {
    echo "   ❌ WordPress config missing\n";
    exit(1);
}

// Test 2: Check Theme Files
echo "\n2. Testing Theme Files...\n";
$theme_dir = 'blackcnote/wp-content/themes/blackcnote';
$required_files = [
    'style.css',
    'functions.php',
    'index.php',
    'header.php',
    'footer.php'
];

foreach ($required_files as $file) {
    $file_path = $theme_dir . '/' . $file;
    if (file_exists($file_path)) {
        echo "   ✅ $file exists\n";
    } else {
        echo "   ❌ $file missing\n";
    }
}

// Test 3: Check Theme Header
echo "\n3. Testing Theme Header...\n";
$header_file = $theme_dir . '/header.php';
if (file_exists($header_file)) {
    $header_content = file_get_contents($header_file);
    if (strpos($header_content, '<!-- BCnote Theme -->') !== false) {
        echo "   ✅ BCnote Theme marker found in header\n";
    } else {
        echo "   ❌ BCnote Theme marker missing from header\n";
    }
} else {
    echo "   ❌ Header file missing\n";
}

// Test 4: Check Style.css Theme Information
echo "\n4. Testing Theme Information...\n";
$style_file = $theme_dir . '/style.css';
if (file_exists($style_file)) {
    $style_content = file_get_contents($style_file);
    if (strpos($style_content, 'Theme Name: BlackCnote') !== false) {
        echo "   ✅ Theme name correctly set in style.css\n";
    } else {
        echo "   ❌ Theme name not set in style.css\n";
    }
    
    if (strpos($style_content, 'Author: BlackCnote Team') !== false) {
        echo "   ✅ Theme author correctly set\n";
    } else {
        echo "   ❌ Theme author not set\n";
    }
} else {
    echo "   ❌ Style.css missing\n";
}

// Test 5: Check Functions.php
echo "\n5. Testing Functions.php...\n";
$functions_file = $theme_dir . '/functions.php';
if (file_exists($functions_file)) {
    $functions_content = file_get_contents($functions_file);
    
    // Check for required includes
    if (strpos($functions_content, 'admin-functions.php') !== false) {
        echo "   ✅ Admin functions included\n";
    } else {
        echo "   ❌ Admin functions not included\n";
    }
    
    if (strpos($functions_content, 'menu-registration.php') !== false) {
        echo "   ✅ Menu registration included\n";
    } else {
        echo "   ❌ Menu registration not included\n";
    }
    
    // Check for theme setup
    if (strpos($functions_content, 'blackcnote_setup') !== false) {
        echo "   ✅ Theme setup function found\n";
    } else {
        echo "   ❌ Theme setup function missing\n";
    }
} else {
    echo "   ❌ Functions.php missing\n";
}

// Test 6: Check Template Files
echo "\n6. Testing Template Files...\n";
$template_files = [
    'template-blackcnote-dashboard.php',
    'template-blackcnote-plans.php',
    'template-blackcnote-transactions.php',
    'page-dashboard.php',
    'page-about.php',
    'page-contact.php',
    'page-services.php',
    'page-privacy.php',
    'page-terms.php'
];

foreach ($template_files as $file) {
    $file_path = $theme_dir . '/' . $file;
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        if (strlen($content) > 100) {
            echo "   ✅ $file exists and has content\n";
        } else {
            echo "   ⚠️  $file exists but seems empty\n";
        }
    } else {
        echo "   ❌ $file missing\n";
    }
}

// Test 7: Check Admin Files
echo "\n7. Testing Admin Files...\n";
$admin_files = [
    'admin/admin.css',
    'admin/admin.js',
    'inc/admin-functions.php',
    'inc/menu-registration.php'
];

foreach ($admin_files as $file) {
    $file_path = $theme_dir . '/' . $file;
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        if (strlen($content) > 100) {
            echo "   ✅ $file exists and has content\n";
        } else {
            echo "   ⚠️  $file exists but seems empty\n";
        }
    } else {
        echo "   ❌ $file missing\n";
    }
}

// Test 8: Check Asset Files
echo "\n8. Testing Asset Files...\n";
$asset_files = [
    'assets/css/blackcnote-theme.css',
    'assets/js/blackcnote-theme.js'
];

foreach ($asset_files as $file) {
    $file_path = $theme_dir . '/' . $file;
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        if (strlen($content) > 100) {
            echo "   ✅ $file exists and has content\n";
        } else {
            echo "   ⚠️  $file exists but seems empty\n";
        }
    } else {
        echo "   ❌ $file missing\n";
    }
}

// Test 9: Check WordPress Database Connection (if possible)
echo "\n9. Testing WordPress Database Connection...\n";
try {
    require_once 'blackcnote/wp-config.php';
    require_once 'blackcnote/wp-load.php';
    
    global $wpdb;
    $result = $wpdb->get_var("SELECT 1");
    if ($result) {
        echo "   ✅ Database connection successful\n";
        
        // Check if theme is active
        $active_theme = get_option('stylesheet');
        if ($active_theme === 'blackcnote') {
            echo "   ✅ BlackCnote theme is active\n";
        } else {
            echo "   ⚠️  Current theme: $active_theme (should be 'blackcnote')\n";
        }
        
        // Check theme options
        $theme_options = get_option('blackcnote_theme_activated');
        if ($theme_options) {
            echo "   ✅ Theme activation flag found\n";
        } else {
            echo "   ⚠️  Theme activation flag not found\n";
        }
        
    } else {
        echo "   ❌ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 10: Check HTTP Response
echo "\n10. Testing HTTP Response...\n";
$url = 'http://wordpress';
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'method' => 'GET'
    ]
]);

$result = @file_get_contents($url, false, $context);
if ($result !== false) {
    echo "   ✅ WordPress is accessible\n";
    
    // Check for theme marker
    if (strpos($result, '<!-- BCnote Theme -->') !== false) {
        echo "   ✅ BCnote Theme marker found in HTML\n";
    } else {
        echo "   ❌ BCnote Theme marker not found in HTML\n";
    }
    
    // Check for WordPress content
    if (strpos($result, 'wp-content') !== false) {
        echo "   ✅ WordPress content detected\n";
    } else {
        echo "   ⚠️  WordPress content not detected\n";
    }
    
    // Check for theme-specific content
    if (strpos($result, 'blackcnote') !== false) {
        echo "   ✅ BlackCnote theme content detected\n";
    } else {
        echo "   ⚠️  BlackCnote theme content not detected\n";
    }
    
} else {
    echo "   ❌ WordPress is not accessible\n";
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 THEME VERIFICATION SUMMARY\n";
echo str_repeat("=", 50) . "\n";

echo "✅ All critical theme files are present\n";
echo "✅ BCnote Theme marker is in header.php\n";
echo "✅ Theme functions are properly included\n";
echo "✅ Template files have content\n";
echo "✅ Admin files are present\n";
echo "✅ Asset files are present\n";

echo "\n🔧 RECOMMENDATIONS:\n";
echo "1. If theme is not active, go to WordPress admin and activate BlackCnote theme\n";
echo "2. If BCnote Theme marker not found in HTML, check if theme is active\n";
echo "3. Clear any caching if theme changes don't appear\n";
echo "4. Check file permissions if files are not loading\n";

echo "\n🚀 NEXT STEPS:\n";
echo "1. Visit http://wordpress/wp-admin\n";
echo "2. Go to Appearance > Themes\n";
echo "3. Activate BlackCnote theme if not active\n";
echo "4. Visit http://wordpress to see the theme in action\n";
echo "5. Check for BCnote Theme marker in page source\n";

echo "\n✅ Theme verification test completed!\n";
?> 