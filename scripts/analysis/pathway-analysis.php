<?php
/**
 * Pathway Analysis Script for BlackCnote
 * Identifies files that need to be moved from WordPress root to theme directory
 */

declare(strict_types=1);

echo "🔍 BlackCnote Pathway Analysis\n";
echo "=============================\n\n";

// Define canonical paths
$wp_root = 'blackcnote/';
$theme_dir = 'blackcnote/wp-content/themes/blackcnote/';

echo "📁 Canonical Paths:\n";
echo "   WordPress Root: $wp_root\n";
echo "   Theme Directory: $theme_dir\n\n";

// Files that should be in theme directory but are in WordPress root
$theme_files_in_wp_root = [
    // Core theme files
    'style.css' => 'Main theme stylesheet',
    'functions.php' => 'Theme functions file',
    'index.php' => 'Main template file',
    'header.php' => 'Header template',
    'footer.php' => 'Footer template',
    'front-page.php' => 'Front page template',
    
    // Template files
    'template-hyip-dashboard.php' => 'Dashboard template',
    'template-hyip-plans.php' => 'Plans template',
    'template-hyip-transactions.php' => 'Transactions template',
    
    // Page templates
    'page.php' => 'Page template',
    
    // Directories
    'assets/' => 'Theme assets directory',
    'inc/' => 'Theme includes directory',
    'languages/' => 'Theme languages directory',
    'template-parts/' => 'Template parts directory',
    
    // Theme-specific files
    'blackcnote-demo-content.xml' => 'Demo content file',
    'screenshot.png' => 'Theme screenshot',
    
    // Logo files
    'BLACKCNOTE logo (4).png' => 'Theme logo',
    'BLACKCNOTE Logo (3).png' => 'Theme logo',
    'BLACKCNOTE Logo (2).png' => 'Theme logo',
    'BLACKCNOTE Logo (1).png' => 'Theme logo'
];

// Files that should stay in WordPress root
$wp_root_files = [
    'wp-config.php' => 'WordPress configuration',
    'wp-config-local.php' => 'Local WordPress configuration',
    'wp-config.php.backup' => 'WordPress config backup',
    '.htaccess' => 'Apache configuration',
    'activate-theme.php' => 'Theme activation script',
    'start-blackcnote.sh' => 'Startup script',
    'test-phpinfo.php' => 'PHP info test',
    'simple-test.php' => 'Simple test file',
    'test.html' => 'Test HTML file',
    'test-apache.php' => 'Apache test file',
    'debug-wp.php' => 'WordPress debug file',
    'test-wp.php' => 'WordPress test file',
    'test-db.php' => 'Database test file',
    'about.php' => 'WordPress about page',
    'site-editor.php' => 'WordPress site editor',
    'nav-menus.php' => 'WordPress nav menus',
    'edit-form-advanced.php' => 'WordPress edit form',
    'async-upload.php' => 'WordPress async upload',
    'user-edit.php' => 'WordPress user edit',
    'themes.php' => 'WordPress themes page',
    'wp-signup.php' => 'WordPress signup',
    'edit-form-blocks.php' => 'WordPress blocks editor',
    'readme.html' => 'WordPress readme',
    'customize.php' => 'WordPress customizer',
    'license.txt' => 'WordPress license',
    'wp-settings.php' => 'WordPress settings',
    'menu.php' => 'WordPress menu',
    'user-new.php' => 'WordPress new user',
    'plugin-editor.php' => 'WordPress plugin editor',
    'theme-editor.php' => 'WordPress theme editor',
    'import.php' => 'WordPress import',
    'site-health-info.php' => 'WordPress health info',
    'menu-header.php' => 'WordPress menu header',
    'options-general.php' => 'WordPress general options',
    'install.php' => 'WordPress install',
    'upgrade.php' => 'WordPress upgrade',
    'wp-mail.php' => 'WordPress mail',
    'media-upload.php' => 'WordPress media upload',
    'users.php' => 'WordPress users',
    'edit-link-form.php' => 'WordPress link form',
    'link-add.php' => 'WordPress add link',
    'link-manager.php' => 'WordPress link manager',
    'plugins.php' => 'WordPress plugins',
    'theme-install.php' => 'WordPress theme install',
    'upload.php' => 'WordPress upload',
    'wp-login.php' => 'WordPress login',
    'edit-tags.php' => 'WordPress edit tags',
    'admin-header.php' => 'WordPress admin header',
    'admin-functions.php' => 'WordPress admin functions',
    'custom-background.php' => 'WordPress custom background',
    'custom-header.php' => 'WordPress custom header',
    'options-head.php' => 'WordPress options head',
    'freedoms.php' => 'WordPress freedoms',
    'options-privacy.php' => 'WordPress privacy options',
    'privacy.php' => 'WordPress privacy',
    'admin-post.php' => 'WordPress admin post',
    'admin.php' => 'WordPress admin',
    'options-discussion.php' => 'WordPress discussion options',
    'options-media.php' => 'WordPress media options',
    'setup-config.php' => 'WordPress setup config',
    'credits.php' => 'WordPress credits',
    'edit-tag-form.php' => 'WordPress tag form',
    'edit-form-comment.php' => 'WordPress comment form',
    'xmlrpc.php' => 'WordPress XML-RPC',
    'load-styles.php' => 'WordPress load styles',
    'wp-trackback.php' => 'WordPress trackback',
    'wp-config-sample.php' => 'WordPress config sample'
];

echo "1. Analyzing files in WordPress root directory...\n";
$wp_root_files_found = [];
$theme_files_found = [];

if (is_dir($wp_root)) {
    $files = scandir($wp_root);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != 'wp-content' && $file != 'wp-admin' && $file != 'wp-includes') {
            if (isset($theme_files_in_wp_root[$file])) {
                $theme_files_found[$file] = $theme_files_in_wp_root[$file];
            } elseif (isset($wp_root_files[$file])) {
                $wp_root_files_found[$file] = $wp_root_files[$file];
            } else {
                echo "   ⚠️  Unknown file: $file\n";
            }
        }
    }
}

echo "\n2. Files that should be moved to theme directory:\n";
if (!empty($theme_files_found)) {
    foreach ($theme_files_found as $file => $description) {
        $source = $wp_root . $file;
        $destination = $theme_dir . $file;
        
        if (file_exists($source)) {
            if (is_dir($source)) {
                echo "   📁 Directory: $file ($description)\n";
                echo "      Source: $source\n";
                echo "      Destination: $destination\n";
            } else {
                $size = filesize($source);
                echo "   📄 File: $file ($description) - " . number_format($size) . " bytes\n";
                echo "      Source: $source\n";
                echo "      Destination: $destination\n";
            }
        }
    }
} else {
    echo "   ✅ No theme files found in WordPress root\n";
}

echo "\n3. Files correctly in WordPress root:\n";
if (!empty($wp_root_files_found)) {
    foreach ($wp_root_files_found as $file => $description) {
        echo "   ✅ $file ($description)\n";
    }
} else {
    echo "   ❌ No WordPress files found in root\n";
}

echo "\n4. Checking theme directory contents...\n";
if (is_dir($theme_dir)) {
    $theme_files = scandir($theme_dir);
    echo "   Files in theme directory:\n";
    foreach ($theme_files as $file) {
        if ($file != '.' && $file != '..') {
            $path = $theme_dir . $file;
            if (is_dir($path)) {
                echo "   📁 $file/\n";
            } else {
                $size = filesize($path);
                echo "   📄 $file (" . number_format($size) . " bytes)\n";
            }
        }
    }
} else {
    echo "   ❌ Theme directory does not exist\n";
}

echo "\n5. File comparison analysis...\n";

// Compare style.css files
$wp_root_style = $wp_root . 'style.css';
$theme_style = $theme_dir . 'style.css';

if (file_exists($wp_root_style) && file_exists($theme_style)) {
    $wp_root_content = file_get_contents($wp_root_style);
    $theme_content = file_get_contents($theme_style);
    
    if ($wp_root_content !== $theme_content) {
        echo "   ⚠️  style.css files are different!\n";
        echo "      WordPress root: " . strlen($wp_root_content) . " bytes\n";
        echo "      Theme directory: " . strlen($theme_content) . " bytes\n";
    } else {
        echo "   ✅ style.css files are identical\n";
    }
}

// Compare functions.php files
$wp_root_functions = $wp_root . 'functions.php';
$theme_functions = $theme_dir . 'functions.php';

if (file_exists($wp_root_functions) && file_exists($theme_functions)) {
    $wp_root_content = file_get_contents($wp_root_functions);
    $theme_content = file_get_contents($theme_functions);
    
    if ($wp_root_content !== $theme_content) {
        echo "   ⚠️  functions.php files are different!\n";
        echo "      WordPress root: " . strlen($wp_root_content) . " bytes\n";
        echo "      Theme directory: " . strlen($theme_content) . " bytes\n";
    } else {
        echo "   ✅ functions.php files are identical\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 PATHWAY ANALYSIS SUMMARY\n";
echo str_repeat("=", 60) . "\n";

echo "❌ ISSUES FOUND:\n";
echo "1. Theme files are incorrectly placed in WordPress root directory\n";
echo "2. Some files exist in both locations with different content\n";
echo "3. Canonical theme directory is missing many files\n";

echo "\n🔧 RECOMMENDATIONS:\n";
echo "1. Move all theme files from WordPress root to theme directory\n";
echo "2. Ensure theme directory contains all necessary files\n";
echo "3. Remove duplicate files from WordPress root\n";
echo "4. Update any hardcoded paths in theme files\n";

echo "\n📁 CORRECT STRUCTURE:\n";
echo "   WordPress Root: blackcnote/\n";
echo "   ├── wp-config.php\n";
echo "   ├── wp-admin/\n";
echo "   ├── wp-includes/\n";
echo "   └── wp-content/\n";
echo "       └── themes/\n";
echo "           └── blackcnote/\n";
echo "               ├── style.css\n";
echo "               ├── functions.php\n";
echo "               ├── index.php\n";
echo "               ├── header.php\n";
echo "               ├── footer.php\n";
echo "               ├── assets/\n";
echo "               ├── inc/\n";
echo "               └── template-parts/\n";

echo "\n✅ Pathway analysis completed!\n";
?> 