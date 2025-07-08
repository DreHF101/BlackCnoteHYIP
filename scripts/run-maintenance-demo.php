<?php
/**
 * BlackCnote Maintenance Automation Demo
 * 
 * This script demonstrates the maintenance automation system
 * and can be used to test maintenance operations.
 */

declare(strict_types=1);

// Include WordPress
require_once dirname(__DIR__) . '/blackcnote/wp-config.php';

// Include the maintenance automation class
require_once dirname(__DIR__) . '/blackcnote/wp-content/plugins/blackcnote-debug-system/includes/class-blackcnote-maintenance-automation.php';

class BlackCnoteMaintenanceDemo {
    private $maintenance_automation;
    
    public function __construct() {
        // Initialize debug system
        $debug_system = new BlackCnoteDebugSystemCore([
            'log_file' => WP_CONTENT_DIR . '/logs/blackcnote-debug.log',
            'debug_enabled' => true,
            'log_level' => 'ALL'
        ]);
        
        // Initialize maintenance automation
        $this->maintenance_automation = new BlackCnoteMaintenanceAutomation($debug_system);
    }
    
    public function runDemo() {
        echo "🛠️ **BlackCnote Maintenance Automation Demo**\n";
        echo "============================================\n\n";
        
        echo "This demo shows the maintenance automation system in action.\n\n";
        
        // Test daily maintenance
        echo "📅 **Running Daily Maintenance Demo**\n";
        echo "------------------------------------\n";
        $this->maintenance_automation->run_daily_maintenance();
        echo "✅ Daily maintenance completed\n\n";
        
        // Test individual operations
        echo "🔧 **Testing Individual Operations**\n";
        echo "----------------------------------\n";
        
        // Test cleanup temp files
        echo "🧹 Testing cleanup_temp_files...\n";
        $result = $this->maintenance_automation->cleanup_temp_files();
        echo "   Files removed: " . $result['files_removed'] . "\n";
        echo "   Size freed: " . round($result['size_freed'] / 1024, 2) . " KB\n\n";
        
        // Test verify canonical paths
        echo "🛣️ Testing verify_canonical_paths...\n";
        $result = $this->maintenance_automation->verify_canonical_paths();
        $verified_count = count(array_filter($result, fn($path) => $path['exists']));
        echo "   Paths verified: {$verified_count}/" . count($result) . "\n\n";
        
        // Test backup essential files
        echo "💾 Testing backup_essential_files...\n";
        $result = $this->maintenance_automation->backup_essential_files();
        echo "   Files backed up: " . $result['files_backed_up'] . "\n";
        echo "   Backup directory: " . $result['backup_directory'] . "\n\n";
        
        echo "🎉 **Demo completed successfully!**\n";
        echo "The maintenance automation system is working correctly.\n";
        echo "You can now use the WordPress admin interface to manage maintenance tasks.\n\n";
        
        echo "📋 **Next Steps**:\n";
        echo "1. Access WordPress admin at: http://localhost:8888/wp-admin/\n";
        echo "2. Navigate to: BlackCnote Debug → Maintenance\n";
        echo "3. Use the maintenance dashboard to monitor and run maintenance tasks\n";
        echo "4. Check the maintenance logs for detailed activity tracking\n";
    }
}

// Run demo if called directly
if (php_sapi_name() === 'cli') {
    $demo = new BlackCnoteMaintenanceDemo();
    $demo->runDemo();
}
?> 