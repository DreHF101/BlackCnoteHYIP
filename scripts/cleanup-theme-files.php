<?php
/**
 * Cleanup Theme Files Script for BlackCnote
 * Removes backup files, test files, and other unnecessary files
 */

declare(strict_types=1);

echo "🧹 BlackCnote Theme Cleanup Script\n";
echo "==================================\n\n";

$theme_dir = 'blackcnote/wp-content/themes/blackcnote/';

// Files to remove (backups, tests, etc.)
$files_to_remove = [
    // Backup files
    'style.css.backup.2025-06-28-20-22-55',
    'functions.php.backup.2025-06-28-20-22-55',
    'header.php.backup.2025-06-28-20-22-55',
    'footer.php.backup.2025-06-28-20-22-55',
    'front-page.php.backup.2025-06-28-20-22-55',
    'index.php.backup.2025-06-28-20-22-55',
    
    // Test files
    'test-cursor-ai-monitor.php',
    'test-cursor-ai-monitor-2.php',
    'info.php'
];

// Directories to check for unnecessary files
$dirs_to_clean = [
    'inc/',
    'admin/',
    'assets/',
    'js/',
    'css/'
];

echo "1. Removing backup and test files...\n\n";

$removed_files = [];
$skipped_files = [];

foreach ($files_to_remove as $file) {
    $file_path = $theme_dir . $file;
    
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo "   ✅ Removed: $file\n";
            $removed_files[] = $file;
        } else {
            echo "   ❌ Failed to remove: $file\n";
        }
    } else {
        echo "   ⚠️  Not found: $file\n";
        $skipped_files[] = $file;
    }
}

echo "\n2. Cleaning directories for unnecessary files...\n\n";

foreach ($dirs_to_clean as $dir) {
    $dir_path = $theme_dir . $dir;
    
    if (is_dir($dir_path)) {
        $files = scandir($dir_path);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $file_path = $dir_path . $file;
                
                // Check for test files, backups, or temporary files
                if (preg_match('/\.(test|backup|tmp|temp|bak)$/i', $file) || 
                    preg_match('/^(test|debug|temp|tmp)/i', $file)) {
                    
                    if (is_file($file_path)) {
                        if (unlink($file_path)) {
                            echo "   ✅ Removed: $dir$file\n";
                            $removed_files[] = $dir . $file;
                        } else {
                            echo "   ❌ Failed to remove: $dir$file\n";
                        }
                    }
                }
            }
        }
    }
}

echo "\n3. Checking for duplicate template files...\n\n";

$template_files = [
    'template-hyip-dashboard.php' => 'template-blackcnote-dashboard.php',
    'template-hyip-plans.php' => 'template-blackcnote-plans.php',
    'template-hyip-transactions.php' => 'template-blackcnote-transactions.php'
];

foreach ($template_files as $old_template => $new_template) {
    $old_path = $theme_dir . $old_template;
    $new_path = $theme_dir . $new_template;
    
    if (file_exists($old_path) && file_exists($new_path)) {
        $old_size = filesize($old_path);
        $new_size = filesize($new_path);
        
        echo "   📄 $old_template (" . number_format($old_size) . " bytes)\n";
        echo "   📄 $new_template (" . number_format($new_size) . " bytes)\n";
        
        if ($old_size < $new_size) {
            echo "      → Keeping $new_template (larger, more complete)\n";
            if (unlink($old_path)) {
                echo "      ✅ Removed $old_template\n";
                $removed_files[] = $old_template;
            }
        } else {
            echo "      → Keeping $old_template (larger, more complete)\n";
            if (unlink($new_path)) {
                echo "      ✅ Removed $new_template\n";
                $removed_files[] = $new_template;
            }
        }
        echo "\n";
    }
}

echo "\n4. Verifying theme structure after cleanup...\n\n";

$required_files = [
    'style.css',
    'functions.php',
    'index.php',
    'header.php',
    'footer.php',
    'front-page.php'
];

foreach ($required_files as $file) {
    $file_path = $theme_dir . $file;
    if (file_exists($file_path)) {
        $size = filesize($file_path);
        echo "   ✅ $file: " . number_format($size) . " bytes\n";
    } else {
        echo "   ❌ $file: Missing!\n";
    }
}

echo "\n5. Checking for any remaining backup patterns...\n\n";

$remaining_files = scandir($theme_dir);
$backup_patterns = [];

foreach ($remaining_files as $file) {
    if ($file != '.' && $file != '..') {
        if (preg_match('/\.(backup|bak|old|tmp|temp)$/i', $file)) {
            $backup_patterns[] = $file;
        }
    }
}

if (!empty($backup_patterns)) {
    echo "   ⚠️  Found remaining backup patterns:\n";
    foreach ($backup_patterns as $file) {
        echo "      - $file\n";
    }
} else {
    echo "   ✅ No remaining backup patterns found\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 CLEANUP SUMMARY\n";
echo str_repeat("=", 60) . "\n";

echo "✅ FILES REMOVED:\n";
if (!empty($removed_files)) {
    foreach ($removed_files as $file) {
        echo "   - $file\n";
    }
} else {
    echo "   No files removed\n";
}

echo "\n⚠️  FILES SKIPPED (not found):\n";
if (!empty($skipped_files)) {
    foreach ($skipped_files as $file) {
        echo "   - $file\n";
    }
} else {
    echo "   No files skipped\n";
}

echo "\n📁 FINAL THEME STRUCTURE:\n";
$final_files = scandir($theme_dir);
foreach ($final_files as $file) {
    if ($file != '.' && $file != '..') {
        $file_path = $theme_dir . $file;
        if (is_dir($file_path)) {
            echo "   📁 $file/\n";
        } else {
            $size = filesize($file_path);
            echo "   📄 $file (" . number_format($size) . " bytes)\n";
        }
    }
}

echo "\n🎯 NEXT STEPS:\n";
echo "1. Test theme activation in WordPress admin\n";
echo "2. Verify all pages and templates work\n";
echo "3. Check admin settings functionality\n";
echo "4. Test frontend functionality\n";

echo "\n✅ Cleanup completed!\n";
?> 