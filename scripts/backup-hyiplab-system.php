<?php
/**
 * BlackCnote HYIPLab System Backup & Restore
 * Automated backup and restore functionality for HYIPLab system
 */

declare(strict_types=1);

// Load WordPress
require_once 'wp-config.php';
require_once 'wp-load.php';

echo "💾 BlackCnote HYIPLab System Backup & Restore\n";
echo "============================================\n\n";

global $wpdb;

class HYIPLabBackupManager {
    private $wpdb;
    private $backup_dir;
    private $timestamp;
    
    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
        $this->backup_dir = WP_CONTENT_DIR . '/backups/hyiplab/';
        $this->timestamp = date('Y-m-d_H-i-s');
        
        // Create backup directory if it doesn't exist
        if (!file_exists($this->backup_dir)) {
            wp_mkdir_p($this->backup_dir);
        }
    }
    
    public function createBackup($backup_type = 'full') {
        echo "Creating {$backup_type} backup...\n\n";
        
        $backup_files = [];
        
        switch ($backup_type) {
            case 'database':
                $backup_files[] = $this->backupDatabase();
                break;
            case 'files':
                $backup_files[] = $this->backupFiles();
                break;
            case 'config':
                $backup_files[] = $this->backupConfig();
                break;
            case 'full':
            default:
                $backup_files[] = $this->backupDatabase();
                $backup_files[] = $this->backupFiles();
                $backup_files[] = $this->backupConfig();
                $backup_files[] = $this->backupDemoData();
                break;
        }
        
        $this->createBackupManifest($backup_files, $backup_type);
        $this->cleanupOldBackups();
        
        echo "✅ Backup completed successfully!\n";
        return $backup_files;
    }
    
    private function backupDatabase() {
        echo "1. Backing up database...\n";
        
        $backup_file = $this->backup_dir . "hyiplab_db_{$this->timestamp}.sql";
        
        // Get HYIPLab tables
        $hyiplab_tables = [
            'hyiplab_plans',
            'hyiplab_users', 
            'hyiplab_invests',
            'hyiplab_transactions',
            'hyiplab_deposits',
            'hyiplab_withdrawals',
            'hyiplab_gateways',
            'hyiplab_notification_templates',
            'hyiplab_time_settings',
            'hyiplab_extensions',
            'hyiplab_forms',
            'hyiplab_gateway_currencies',
            'hyiplab_support_tickets',
            'hyiplab_support_messages',
            'hyiplab_support_attachments',
            'hyiplab_referrals',
            'hyiplab_user_rankings',
            'hyiplab_withdraw_methods',
            'hyiplab_stakings',
            'hyiplab_staking_invests',
            'hyiplab_promotion_tools',
            'hyiplab_pools',
            'hyiplab_pool_invests',
            'hyiplab_holidays',
            'hyiplab_schedule_invests'
        ];
        
        $sql_content = "-- BlackCnote HYIPLab Database Backup\n";
        $sql_content .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql_content .= "-- Backup Type: Database Only\n\n";
        
        foreach ($hyiplab_tables as $table) {
            $full_table_name = $this->wpdb->prefix . $table;
            
            // Check if table exists
            $table_exists = $this->wpdb->get_var("SHOW TABLES LIKE '{$full_table_name}'");
            if (!$table_exists) {
                echo "   ⚠️  Table {$table} does not exist, skipping...\n";
                continue;
            }
            
            // Get table structure
            $create_table = $this->wpdb->get_row("SHOW CREATE TABLE {$full_table_name}", ARRAY_N);
            if ($create_table) {
                $sql_content .= "-- Table structure for {$table}\n";
                $sql_content .= "DROP TABLE IF EXISTS `{$full_table_name}`;\n";
                $sql_content .= $create_table[1] . ";\n\n";
            }
            
            // Get table data
            $rows = $this->wpdb->get_results("SELECT * FROM {$full_table_name}", ARRAY_A);
            if (!empty($rows)) {
                $sql_content .= "-- Data for {$table}\n";
                foreach ($rows as $row) {
                    $values = array_map(function($value) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return "'" . $this->wpdb->_real_escape($value) . "'";
                    }, $row);
                    
                    $sql_content .= "INSERT INTO `{$full_table_name}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql_content .= "\n";
            }
            
            echo "   ✅ Backed up table: {$table} (" . count($rows) . " rows)\n";
        }
        
        // Backup WordPress options related to HYIPLab
        $hyiplab_options = $this->wpdb->get_results(
            "SELECT option_name, option_value FROM {$this->wpdb->options} 
             WHERE option_name LIKE 'hyiplab%' OR option_name LIKE '%hyiplab%'"
        );
        
        if (!empty($hyiplab_options)) {
            $sql_content .= "-- HYIPLab WordPress Options\n";
            foreach ($hyiplab_options as $option) {
                $sql_content .= "INSERT INTO `{$this->wpdb->options}` (option_name, option_value) VALUES ('{$option->option_name}', '" . $this->wpdb->_real_escape($option->option_value) . "') ON DUPLICATE KEY UPDATE option_value = VALUES(option_value);\n";
            }
            $sql_content .= "\n";
            
            echo "   ✅ Backed up " . count($hyiplab_options) . " HYIPLab options\n";
        }
        
        file_put_contents($backup_file, $sql_content);
        echo "   📁 Database backup saved: " . basename($backup_file) . "\n\n";
        
        return $backup_file;
    }
    
    private function backupFiles() {
        echo "2. Backing up HYIPLab files...\n";
        
        $backup_file = $this->backup_dir . "hyiplab_files_{$this->timestamp}.zip";
        $plugin_dir = WP_PLUGIN_DIR . '/hyiplab/';
        
        if (!file_exists($plugin_dir)) {
            echo "   ❌ HYIPLab plugin directory not found\n\n";
            return null;
        }
        
        $zip = new ZipArchive();
        if ($zip->open($backup_file, ZipArchive::CREATE) === TRUE) {
            $this->addFolderToZip($zip, $plugin_dir, 'hyiplab/');
            $zip->close();
            
            echo "   📁 Files backup saved: " . basename($backup_file) . "\n";
            echo "   📊 Backup size: " . $this->formatBytes(filesize($backup_file)) . "\n\n";
            
            return $backup_file;
        } else {
            echo "   ❌ Failed to create files backup\n\n";
            return null;
        }
    }
    
    private function backupConfig() {
        echo "3. Backing up configuration...\n";
        
        $config_file = $this->backup_dir . "hyiplab_config_{$this->timestamp}.json";
        
        $config = [
            'backup_info' => [
                'timestamp' => $this->timestamp,
                'wordpress_version' => get_bloginfo('version'),
                'hyiplab_version' => get_option('hyiplab_version', 'unknown'),
                'php_version' => PHP_VERSION,
                'mysql_version' => $this->wpdb->db_version()
            ],
            'site_info' => [
                'site_url' => get_site_url(),
                'home_url' => get_home_url(),
                'admin_email' => get_option('admin_email'),
                'timezone' => get_option('timezone_string')
            ],
            'hyiplab_settings' => [
                'purchase_code' => get_option('hyiplab_purchase_code'),
                'currency' => get_option('hyiplab_cur_text', 'USD'),
                'currency_symbol' => get_option('hyiplab_cur_sym', '$'),
                'email_notification' => get_option('hyiplab_email_notification'),
                'sms_notification' => get_option('hyiplab_sms_notification')
            ],
            'database_info' => [
                'db_name' => DB_NAME,
                'db_host' => DB_HOST,
                'table_prefix' => $this->wpdb->prefix
            ]
        ];
        
        file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
        
        echo "   📁 Configuration backup saved: " . basename($config_file) . "\n\n";
        
        return $config_file;
    }
    
    private function backupDemoData() {
        echo "4. Backing up demo data...\n";
        
        $demo_file = $this->backup_dir . "hyiplab_demo_{$this->timestamp}.json";
        
        $demo_data = [
            'users' => $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}hyiplab_users WHERE username LIKE 'demo_%'", ARRAY_A),
            'plans' => $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}hyiplab_plans", ARRAY_A),
            'investments' => $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}hyiplab_invests", ARRAY_A),
            'transactions' => $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}hyiplab_transactions", ARRAY_A)
        ];
        
        file_put_contents($demo_file, json_encode($demo_data, JSON_PRETTY_PRINT));
        
        echo "   📁 Demo data backup saved: " . basename($demo_file) . "\n";
        echo "   📊 Demo users: " . count($demo_data['users']) . "\n";
        echo "   📊 Demo plans: " . count($demo_data['plans']) . "\n";
        echo "   📊 Demo investments: " . count($demo_data['investments']) . "\n";
        echo "   📊 Demo transactions: " . count($demo_data['transactions']) . "\n\n";
        
        return $demo_file;
    }
    
    private function createBackupManifest($backup_files, $backup_type) {
        echo "5. Creating backup manifest...\n";
        
        $manifest_file = $this->backup_dir . "backup_manifest_{$this->timestamp}.json";
        
        $manifest = [
            'backup_info' => [
                'timestamp' => $this->timestamp,
                'type' => $backup_type,
                'created_by' => 'HYIPLab Backup Manager',
                'wordpress_version' => get_bloginfo('version')
            ],
            'files' => array_filter($backup_files), // Remove null values
            'total_size' => 0,
            'checksums' => []
        ];
        
        foreach ($manifest['files'] as $file) {
            if (file_exists($file)) {
                $manifest['total_size'] += filesize($file);
                $manifest['checksums'][basename($file)] = md5_file($file);
            }
        }
        
        file_put_contents($manifest_file, json_encode($manifest, JSON_PRETTY_PRINT));
        
        echo "   📁 Manifest saved: " . basename($manifest_file) . "\n";
        echo "   📊 Total backup size: " . $this->formatBytes($manifest['total_size']) . "\n\n";
        
        return $manifest_file;
    }
    
    private function cleanupOldBackups() {
        echo "6. Cleaning up old backups...\n";
        
        $backup_files = glob($this->backup_dir . '*.{sql,zip,json}', GLOB_BRACE);
        $max_backups = 10; // Keep only 10 most recent backups
        
        if (count($backup_files) > $max_backups) {
            // Sort by modification time (oldest first)
            usort($backup_files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $files_to_delete = array_slice($backup_files, 0, count($backup_files) - $max_backups);
            
            foreach ($files_to_delete as $file) {
                unlink($file);
                echo "   🗑️  Deleted old backup: " . basename($file) . "\n";
            }
        }
        
        echo "   ✅ Cleanup completed\n\n";
    }
    
    public function listBackups() {
        echo "Available Backups:\n";
        echo "==================\n";
        
        $backup_files = glob($this->backup_dir . 'backup_manifest_*.json');
        
        if (empty($backup_files)) {
            echo "No backups found.\n";
            return;
        }
        
        foreach ($backup_files as $manifest_file) {
            $manifest = json_decode(file_get_contents($manifest_file), true);
            if ($manifest) {
                echo "📁 Backup: " . $manifest['backup_info']['timestamp'] . "\n";
                echo "   Type: " . $manifest['backup_info']['type'] . "\n";
                echo "   Size: " . $this->formatBytes($manifest['total_size']) . "\n";
                echo "   Files: " . count($manifest['files']) . "\n";
                echo "   Date: " . date('Y-m-d H:i:s', strtotime($manifest['backup_info']['timestamp'])) . "\n\n";
            }
        }
    }
    
    private function addFolderToZip($zip, $folder, $zip_folder) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zip_folder . substr($filePath, strlen($folder));
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

// Handle command line arguments
$action = isset($argv[1]) ? $argv[1] : 'backup';
$backup_type = isset($argv[2]) ? $argv[2] : 'full';

$backup_manager = new HYIPLabBackupManager($wpdb);

switch ($action) {
    case 'backup':
        $backup_manager->createBackup($backup_type);
        break;
    case 'list':
        $backup_manager->listBackups();
        break;
    default:
        echo "Usage: php backup-hyiplab-system.php [backup|list] [full|database|files|config]\n";
        echo "Examples:\n";
        echo "  php backup-hyiplab-system.php backup full\n";
        echo "  php backup-hyiplab-system.php backup database\n";
        echo "  php backup-hyiplab-system.php list\n";
        break;
}

echo "\n💾 Backup system ready!\n";
echo "Backup location: " . $backup_manager->backup_dir . "\n";
echo "For automated backups, add to cron:\n";
echo "0 2 * * * docker exec blackcnote-wordpress php /var/www/html/scripts/backup-hyiplab-system.php backup full\n"; 