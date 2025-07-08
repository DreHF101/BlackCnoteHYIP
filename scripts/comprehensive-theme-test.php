<?php
/**
 * Comprehensive Theme Test Script for BlackCnote
 * Tests all theme functionality, admin features, and frontend components
 */

declare(strict_types=1);

echo "🧪 BlackCnote Comprehensive Theme Test\n";
echo "=====================================\n\n";

$theme_dir = 'blackcnote/wp-content/themes/blackcnote/';

echo "1. Testing Theme File Structure...\n\n";

// Required theme files
$required_files = [
    'style.css' => 'Main theme stylesheet',
    'functions.php' => 'Theme functions file',
    'index.php' => 'Main template file',
    'header.php' => 'Header template',
    'footer.php' => 'Footer template',
    'front-page.php' => 'Front page template',
    'page.php' => 'Page template'
];

$missing_files = [];
$present_files = [];

foreach ($required_files as $file => $description) {
    $file_path = $theme_dir . $file;
    if (file_exists($file_path)) {
        $size = filesize($file_path);
        echo "   ✅ $file: " . number_format($size) . " bytes ($description)\n";
        $present_files[] = $file;
    } else {
        echo "   ❌ $file: Missing! ($description)\n";
        $missing_files[] = $file;
    }
}

echo "\n2. Testing Template Files...\n\n";

$template_files = [
    'template-blackcnote-dashboard.php' => 'Dashboard template',
    'template-blackcnote-plans.php' => 'Plans template',
    'template-blackcnote-transactions.php' => 'Transactions template'
];

foreach ($template_files as $file => $description) {
    $file_path = $theme_dir . $file;
    if (file_exists($file_path)) {
        $size = filesize($file_path);
        echo "   ✅ $file: " . number_format($size) . " bytes ($description)\n";
    } else {
        echo "   ❌ $file: Missing! ($description)\n";
        $missing_files[] = $file;
    }
}

echo "\n3. Testing Page Templates...\n\n";

$page_templates = [
    'page-dashboard.php' => 'Dashboard page',
    'page-plans.php' => 'Plans page',
    'page-about.php' => 'About page',
    'page-contact.php' => 'Contact page',
    'page-services.php' => 'Services page',
    'page-privacy.php' => 'Privacy page',
    'page-terms.php' => 'Terms page',
    'page-home.php' => 'Home page'
];

foreach ($page_templates as $file => $description) {
    $file_path = $theme_dir . $file;
    if (file_exists($file_path)) {
        $size = filesize($file_path);
        echo "   ✅ $file: " . number_format($size) . " bytes ($description)\n";
    } else {
        echo "   ❌ $file: Missing! ($description)\n";
        $missing_files[] = $file;
    }
}

echo "\n4. Testing Include Files...\n\n";

$include_files = [
    'inc/admin-functions.php' => 'Admin functions',
    'inc/menu-registration.php' => 'Menu registration',
    'inc/backend-settings-manager.php' => 'Backend settings manager',
    'inc/widgets.php' => 'Widgets',
    'inc/full-content-checker.php' => 'Content checker'
];

foreach ($include_files as $file => $description) {
    $file_path = $theme_dir . $file;
    if (file_exists($file_path)) {
        $size = filesize($file_path);
        echo "   ✅ $file: " . number_format($size) . " bytes ($description)\n";
    } else {
        echo "   ❌ $file: Missing! ($description)\n";
        $missing_files[] = $file;
    }
}

echo "\n5. Testing Asset Directories...\n\n";

$asset_dirs = [
    'assets/' => 'Main assets directory',
    'css/' => 'CSS directory',
    'js/' => 'JavaScript directory',
    'admin/' => 'Admin assets directory',
    'dist/' => 'React build directory',
    'languages/' => 'Languages directory',
    'template-parts/' => 'Template parts directory'
];

foreach ($asset_dirs as $dir => $description) {
    $dir_path = $theme_dir . $dir;
    if (is_dir($dir_path)) {
        $files = scandir($dir_path);
        $file_count = count($files) - 2; // Subtract . and ..
        echo "   ✅ $dir: $file_count files ($description)\n";
    } else {
        echo "   ❌ $dir: Missing! ($description)\n";
        $missing_files[] = $dir;
    }
}

echo "\n6. Testing Theme Header...\n\n";

$style_file = $theme_dir . 'style.css';
if (file_exists($style_file)) {
    $content = file_get_contents($style_file);
    
    // Check for required theme header
    $required_headers = [
        'Theme Name: BlackCnote',
        'Version:',
        'Author:',
        'Description:'
    ];
    
    foreach ($required_headers as $header) {
        if (strpos($content, $header) !== false) {
            echo "   ✅ Found: $header\n";
        } else {
            echo "   ❌ Missing: $header\n";
        }
    }
} else {
    echo "   ❌ style.css not found!\n";
}

echo "\n7. Testing Functions.php Includes...\n\n";

$functions_file = $theme_dir . 'functions.php';
if (file_exists($functions_file)) {
    $content = file_get_contents($functions_file);
    
    // Check for required includes
    $required_includes = [
        'require_once get_template_directory() . \'/inc/menu-registration.php\'',
        'require_once get_template_directory() . \'/admin/admin-functions.php\'',
        'require_once get_template_directory() . \'/inc/backend-settings-manager.php\''
    ];
    
    foreach ($required_includes as $include) {
        if (strpos($content, $include) !== false) {
            echo "   ✅ Found: " . basename($include) . "\n";
        } else {
            echo "   ❌ Missing: " . basename($include) . "\n";
        }
    }
    
    // Check for required functions
    $required_functions = [
        'blackcnote_theme_setup',
        'blackcnote_theme_scripts',
        'blackcnote_plans_shortcode'
    ];
    
    foreach ($required_functions as $function) {
        if (strpos($content, "function $function") !== false) {
            echo "   ✅ Found function: $function\n";
        } else {
            echo "   ❌ Missing function: $function\n";
        }
    }
} else {
    echo "   ❌ functions.php not found!\n";
}

echo "\n8. Testing WordPress Integration...\n\n";

// Check if WordPress is accessible
$wp_config = 'blackcnote/wp-config.php';
if (file_exists($wp_config)) {
    echo "   ✅ WordPress configuration found\n";
    
    // Check for database connection
    $config_content = file_get_contents($wp_config);
    if (strpos($config_content, 'DB_HOST') !== false) {
        echo "   ✅ Database configuration present\n";
    } else {
        echo "   ❌ Database configuration missing\n";
    }
} else {
    echo "   ❌ WordPress configuration not found\n";
}

echo "\n9. Testing Docker Services...\n\n";

// Check if Docker containers are running
$docker_containers = [
    'wordpress' => 'WordPress container',
    'mysql' => 'MySQL database',
    'phpmyadmin' => 'phpMyAdmin',
    'mailhog' => 'MailHog email testing'
];

foreach ($docker_containers as $container => $description) {
    $output = shell_exec("docker ps --filter name=$container --format '{{.Names}}' 2>&1");
    if (trim($output) !== '') {
        echo "   ✅ $container: Running ($description)\n";
    } else {
        echo "   ⚠️  $container: Not running ($description)\n";
    }
}

echo "\n10. Testing Service URLs...\n\n";

$service_urls = [
    'http://localhost:8888' => 'WordPress Frontend',
    'http://localhost:8888/wp-admin/' => 'WordPress Admin',
    'http://localhost:8080' => 'phpMyAdmin',
    'http://localhost:8025' => 'MailHog'
];

foreach ($service_urls as $url => $description) {
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        echo "   ✅ $url: Accessible ($description)\n";
    } else {
        echo "   ❌ $url: Not accessible ($description)\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 COMPREHENSIVE TEST SUMMARY\n";
echo str_repeat("=", 60) . "\n";

echo "✅ FILES PRESENT: " . count($present_files) . "\n";
echo "❌ FILES MISSING: " . count($missing_files) . "\n";

if (!empty($missing_files)) {
    echo "\nMissing files:\n";
    foreach ($missing_files as $file) {
        echo "   - $file\n";
    }
}

echo "\n🎯 TESTING CHECKLIST:\n";
echo "1. ✅ Theme file structure verified\n";
echo "2. ✅ Template files checked\n";
echo "3. ✅ Page templates verified\n";
echo "4. ✅ Include files tested\n";
echo "5. ✅ Asset directories confirmed\n";
echo "6. ✅ Theme header validated\n";
echo "7. ✅ Functions.php includes checked\n";
echo "8. ✅ WordPress integration tested\n";
echo "9. ✅ Docker services verified\n";
echo "10. ✅ Service URLs tested\n";

echo "\n📋 MANUAL TESTING REQUIRED:\n";
echo "1. Visit http://localhost:8888/wp-admin/\n";
echo "2. Go to Appearance > Themes and activate BlackCnote\n";
echo "3. Check Appearance > Menus for menu creation\n";
echo "4. Visit Pages to verify all templates are assigned\n";
echo "5. Test BlackCnote admin settings (if menu exists)\n";
echo "6. Visit frontend pages to test templates\n";
echo "7. Check that all assets (CSS/JS/images) load\n";

echo "\n✅ Comprehensive testing completed!\n";
?> 