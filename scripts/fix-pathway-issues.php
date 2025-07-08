<?php
/**
 * Fix Pathway Issues Script for BlackCnote
 * Moves theme files from WordPress root to canonical theme directory
 */

declare(strict_types=1);

echo "🔧 BlackCnote Pathway Fix Script\n";
echo "===============================\n\n";

// Define canonical paths
$wp_root = 'blackcnote/';
$theme_dir = 'blackcnote/wp-content/themes/blackcnote/';

// Ensure theme directory exists
if (!is_dir($theme_dir)) {
    mkdir($theme_dir, 0755, true);
    echo "✅ Created theme directory: $theme_dir\n";
}

// Files to move from WordPress root to theme directory
$files_to_move = [
    // Core theme files (use WordPress root versions as they're more complete)
    'style.css' => 'Main theme stylesheet',
    'functions.php' => 'Theme functions file',
    'index.php' => 'Main template file',
    'header.php' => 'Header template',
    'footer.php' => 'Footer template',
    'front-page.php' => 'Front page template',
    'page.php' => 'Page template',
    
    // Template files
    'template-hyip-dashboard.php' => 'Dashboard template',
    'template-hyip-plans.php' => 'Plans template',
    'template-hyip-transactions.php' => 'Transactions template',
    
    // Theme assets
    'screenshot.png' => 'Theme screenshot',
    'blackcnote-demo-content.xml' => 'Demo content file',
    
    // Logo files
    'BLACKCNOTE Logo (1).png' => 'Theme logo',
    'BLACKCNOTE Logo (2).png' => 'Theme logo',
    'BLACKCNOTE Logo (3).png' => 'Theme logo',
    'BLACKCNOTE logo (4).png' => 'Theme logo'
];

// Directories to move
$dirs_to_move = [
    'assets' => 'Theme assets directory',
    'inc' => 'Theme includes directory',
    'languages' => 'Theme languages directory',
    'template-parts' => 'Template parts directory'
];

echo "1. Moving theme files from WordPress root to theme directory...\n\n";

$moved_files = [];
$skipped_files = [];
$errors = [];

foreach ($files_to_move as $file => $description) {
    $source = $wp_root . $file;
    $destination = $theme_dir . $file;
    
    if (file_exists($source)) {
        // Check if destination exists and compare content
        if (file_exists($destination)) {
            $source_content = file_get_contents($source);
            $dest_content = file_get_contents($destination);
            
            if ($source_content !== $dest_content) {
                echo "   ⚠️  $file: Different content found, backing up and replacing...\n";
                // Backup existing file
                $backup = $destination . '.backup.' . date('Y-m-d-H-i-s');
                copy($destination, $backup);
                echo "      📄 Backup created: $backup\n";
            } else {
                echo "   ✅ $file: Identical content, skipping...\n";
                $skipped_files[] = $file;
                continue;
            }
        }
        
        // Move the file
        if (copy($source, $destination)) {
            echo "   ✅ $file: Moved successfully ($description)\n";
            $moved_files[] = $file;
            
            // Remove from source if copy was successful
            unlink($source);
            echo "      🗑️  Removed from source\n";
        } else {
            echo "   ❌ $file: Failed to move\n";
            $errors[] = $file;
        }
    } else {
        echo "   ⚠️  $file: Source file not found\n";
        $skipped_files[] = $file;
    }
}

echo "\n2. Moving theme directories...\n\n";

foreach ($dirs_to_move as $dir => $description) {
    $source = $wp_root . $dir;
    $destination = $theme_dir . $dir;
    
    if (is_dir($source)) {
        if (is_dir($destination)) {
            echo "   ⚠️  $dir: Destination directory exists, merging...\n";
            // Merge directories
            $files = scandir($source);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $src_file = $source . '/' . $file;
                    $dest_file = $destination . '/' . $file;
                    
                    if (is_dir($src_file)) {
                        if (!is_dir($dest_file)) {
                            mkdir($dest_file, 0755, true);
                        }
                        // Recursively copy directory contents
                        copyDirectory($src_file, $dest_file);
                    } else {
                        if (!file_exists($dest_file) || filesize($src_file) !== filesize($dest_file)) {
                            copy($src_file, $dest_file);
                        }
                    }
                }
            }
            echo "      ✅ Merged successfully\n";
        } else {
            // Move entire directory
            if (rename($source, $destination)) {
                echo "   ✅ $dir: Moved successfully ($description)\n";
                $moved_files[] = $dir;
            } else {
                echo "   ❌ $dir: Failed to move\n";
                $errors[] = $dir;
            }
        }
    } else {
        echo "   ⚠️  $dir: Source directory not found\n";
        $skipped_files[] = $dir;
    }
}

echo "\n3. Cleaning up theme directory...\n\n";

// Remove test files from theme directory
$test_files = [
    'test-cursor-ai-monitor.php',
    'test-cursor-ai-monitor-2.php',
    'info.php'
];

foreach ($test_files as $test_file) {
    $test_path = $theme_dir . $test_file;
    if (file_exists($test_path)) {
        unlink($test_path);
        echo "   🗑️  Removed test file: $test_file\n";
    }
}

// Ensure proper theme structure
$required_files = [
    'style.css',
    'functions.php',
    'index.php',
    'header.php',
    'footer.php'
];

echo "\n4. Verifying theme structure...\n\n";

foreach ($required_files as $file) {
    $file_path = $theme_dir . $file;
    if (file_exists($file_path)) {
        $size = filesize($file_path);
        echo "   ✅ $file: " . number_format($size) . " bytes\n";
    } else {
        echo "   ❌ $file: Missing!\n";
        $errors[] = "Missing required file: $file";
    }
}

echo "\n5. Updating theme files for canonical paths...\n\n";

// Update functions.php to use correct paths
$functions_file = $theme_dir . 'functions.php';
if (file_exists($functions_file)) {
    $content = file_get_contents($functions_file);
    
    // Update any hardcoded paths
    $content = str_replace(
        'get_template_directory() . \'/inc/',
        'BLACKCNOTE_THEME_DIR . \'/inc/',
        $content
    );
    
    $content = str_replace(
        'get_template_directory_uri() . \'/assets/',
        'BLACKCNOTE_THEME_URI . \'/assets/',
        $content
    );
    
    file_put_contents($functions_file, $content);
    echo "   ✅ Updated functions.php with canonical paths\n";
}

// Update style.css header
$style_file = $theme_dir . 'style.css';
if (file_exists($style_file)) {
    $content = file_get_contents($style_file);
    
    // Ensure proper theme header
    if (strpos($content, 'Theme Name: BlackCnote') === false) {
        $header = "/*
Theme Name: BlackCnote
Theme URI: https://blackcnote.com
Author: BlackCnote Team
Author URI: https://blackcnote.com
Description: A modern, responsive WordPress theme for investment platforms with React integration.
Version: 1.0.0
Requires at least: 5.8
Requires PHP: 7.4
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: blackcnote
Tags: investment, bootstrap, responsive, custom-background, custom-logo, custom-menu, featured-images, threaded-comments
*/\n\n";
        
        // Remove existing header if present
        $content = preg_replace('/\/\*[\s\S]*?\*\//', '', $content, 1);
        $content = $header . $content;
        
        file_put_contents($style_file, $content);
        echo "   ✅ Updated style.css header\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 PATHWAY FIX SUMMARY\n";
echo str_repeat("=", 60) . "\n";

echo "✅ SUCCESSFULLY MOVED:\n";
if (!empty($moved_files)) {
    foreach ($moved_files as $file) {
        echo "   - $file\n";
    }
} else {
    echo "   No files moved\n";
}

echo "\n⚠️  SKIPPED (already in place or not found):\n";
if (!empty($skipped_files)) {
    foreach ($skipped_files as $file) {
        echo "   - $file\n";
    }
} else {
    echo "   No files skipped\n";
}

echo "\n❌ ERRORS:\n";
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "   - $error\n";
    }
} else {
    echo "   No errors encountered\n";
}

echo "\n📁 FINAL THEME STRUCTURE:\n";
if (is_dir($theme_dir)) {
    $files = scandir($theme_dir);
    foreach ($files as $file) {
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
}

echo "\n🎯 NEXT STEPS:\n";
echo "1. Verify theme activation in WordPress admin\n";
echo "2. Test all theme functionality\n";
echo "3. Check that all assets load correctly\n";
echo "4. Verify canonical paths are working\n";

echo "\n✅ Pathway fix completed!\n";

/**
 * Helper function to copy directory recursively
 */
function copyDirectory($src, $dst) {
    if (!is_dir($dst)) {
        mkdir($dst, 0755, true);
    }
    
    $files = scandir($src);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $src_file = $src . '/' . $file;
            $dst_file = $dst . '/' . $file;
            
            if (is_dir($src_file)) {
                copyDirectory($src_file, $dst_file);
            } else {
                copy($src_file, $dst_file);
            }
        }
    }
}
?> 