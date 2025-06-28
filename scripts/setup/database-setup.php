<?php
/**
 * BlackCnote Database Setup Script
 * 
 * This script initializes the database with all required tables
 * for the BlackCnote WordPress theme and HYIPLab plugin.
 */

declare(strict_types=1);

class BlackCnoteDatabaseSetup {
    private PDO $pdo;
    private array $errors = [];
    private array $success = [];

    public function __construct() {
        $this->connectToDatabase();
    }

    private function connectToDatabase(): void {
        try {
            $host = 'localhost';
            $port = 3306;
            $dbname = 'blackcnote';
            $username = 'root';
            $password = 'blackcnote_password';

            $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]);

            // Create database if it doesn't exist
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->pdo->exec("USE `$dbname`");
            
            $this->success[] = "Database connection established successfully";
        } catch (PDOException $e) {
            $this->errors[] = "Database connection failed: " . $e->getMessage();
            throw $e;
        }
    }

    public function setupWordPressTables(): void {
        try {
            // WordPress core tables
            $tables = [
                'wp_users' => "
                    CREATE TABLE IF NOT EXISTS `wp_users` (
                        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `user_login` varchar(60) NOT NULL DEFAULT '',
                        `user_pass` varchar(255) NOT NULL DEFAULT '',
                        `user_nicename` varchar(50) NOT NULL DEFAULT '',
                        `user_email` varchar(100) NOT NULL DEFAULT '',
                        `user_url` varchar(100) NOT NULL DEFAULT '',
                        `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                        `user_activation_key` varchar(255) NOT NULL DEFAULT '',
                        `user_status` int(11) NOT NULL DEFAULT '0',
                        `display_name` varchar(250) NOT NULL DEFAULT '',
                        PRIMARY KEY (`ID`),
                        KEY `user_login_key` (`user_login`),
                        KEY `user_nicename` (`user_nicename`),
                        KEY `user_email` (`user_email`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ",
                'wp_usermeta' => "
                    CREATE TABLE IF NOT EXISTS `wp_usermeta` (
                        `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                        `meta_key` varchar(255) DEFAULT NULL,
                        `meta_value` longtext,
                        PRIMARY KEY (`umeta_id`),
                        KEY `user_id` (`user_id`),
                        KEY `meta_key` (`meta_key`(191))
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ",
                'wp_options' => "
                    CREATE TABLE IF NOT EXISTS `wp_options` (
                        `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `option_name` varchar(191) NOT NULL DEFAULT '',
                        `option_value` longtext NOT NULL,
                        `autoload` varchar(20) NOT NULL DEFAULT 'yes',
                        PRIMARY KEY (`option_id`),
                        UNIQUE KEY `option_name` (`option_name`),
                        KEY `autoload` (`autoload`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ",
                'wp_posts' => "
                    CREATE TABLE IF NOT EXISTS `wp_posts` (
                        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `post_author` bigint(20) unsigned NOT NULL DEFAULT '0',
                        `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                        `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                        `post_content` longtext NOT NULL,
                        `post_title` text NOT NULL,
                        `post_excerpt` text NOT NULL,
                        `post_status` varchar(20) NOT NULL DEFAULT 'publish',
                        `comment_status` varchar(20) NOT NULL DEFAULT 'open',
                        `ping_status` varchar(20) NOT NULL DEFAULT 'open',
                        `post_password` varchar(255) NOT NULL DEFAULT '',
                        `post_name` varchar(200) NOT NULL DEFAULT '',
                        `to_ping` text NOT NULL,
                        `pinged` text NOT NULL,
                        `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                        `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                        `post_content_filtered` longtext NOT NULL,
                        `post_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
                        `guid` varchar(255) NOT NULL DEFAULT '',
                        `menu_order` int(11) NOT NULL DEFAULT '0',
                        `post_type` varchar(20) NOT NULL DEFAULT 'post',
                        `post_mime_type` varchar(100) NOT NULL DEFAULT '',
                        `comment_count` bigint(20) NOT NULL DEFAULT '0',
                        PRIMARY KEY (`ID`),
                        KEY `post_name` (`post_name`(191)),
                        KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
                        KEY `post_parent` (`post_parent`),
                        KEY `post_author` (`post_author`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                "
            ];

            foreach ($tables as $tableName => $sql) {
                $this->pdo->exec($sql);
                $this->success[] = "Created table: $tableName";
            }

        } catch (PDOException $e) {
            $this->errors[] = "Error creating WordPress tables: " . $e->getMessage();
        }
    }

    public function setupHYIPLabTables(): void {
        try {
            // HYIPLab plugin tables
            $tables = [
                'wp_hyiplab_investments' => "
                    CREATE TABLE IF NOT EXISTS `wp_hyiplab_investments` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `user_id` bigint(20) unsigned NOT NULL,
                        `plan_id` int(11) NOT NULL,
                        `amount` decimal(10,2) NOT NULL,
                        `roi_percentage` decimal(5,2) NOT NULL,
                        `duration_days` int(11) NOT NULL,
                        `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
                        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        KEY `user_id` (`user_id`),
                        KEY `plan_id` (`plan_id`),
                        KEY `status` (`status`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ",
                'wp_hyiplab_plans' => "
                    CREATE TABLE IF NOT EXISTS `wp_hyiplab_plans` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` varchar(255) NOT NULL,
                        `description` text,
                        `min_amount` decimal(10,2) NOT NULL,
                        `max_amount` decimal(10,2) NOT NULL,
                        `roi_percentage` decimal(5,2) NOT NULL,
                        `duration_days` int(11) NOT NULL,
                        `status` enum('active','inactive') NOT NULL DEFAULT 'active',
                        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ",
                'wp_hyiplab_transactions' => "
                    CREATE TABLE IF NOT EXISTS `wp_hyiplab_transactions` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `user_id` bigint(20) unsigned NOT NULL,
                        `investment_id` int(11) DEFAULT NULL,
                        `type` enum('deposit','withdrawal','roi','bonus') NOT NULL,
                        `amount` decimal(10,2) NOT NULL,
                        `status` enum('pending','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
                        `reference` varchar(255) DEFAULT NULL,
                        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        KEY `user_id` (`user_id`),
                        KEY `investment_id` (`investment_id`),
                        KEY `type` (`type`),
                        KEY `status` (`status`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                "
            ];

            foreach ($tables as $tableName => $sql) {
                $this->pdo->exec($sql);
                $this->success[] = "Created table: $tableName";
            }

        } catch (PDOException $e) {
            $this->errors[] = "Error creating HYIPLab tables: " . $e->getMessage();
        }
    }

    public function insertDefaultData(): void {
        try {
            // Insert default WordPress options
            $options = [
                'siteurl' => 'http://localhost:8888',
                'home' => 'http://localhost:8888',
                'blogname' => 'BlackCnote',
                'blogdescription' => 'Advanced Investment Platform',
                'users_can_register' => '1',
                'default_role' => 'subscriber',
                'timezone_string' => 'UTC',
                'date_format' => 'F j, Y',
                'time_format' => 'g:i a',
                'posts_per_page' => '10',
                'show_on_front' => 'posts',
                'page_on_front' => '0',
                'page_for_posts' => '0'
            ];

            foreach ($options as $option_name => $option_value) {
                $stmt = $this->pdo->prepare("INSERT IGNORE INTO wp_options (option_name, option_value, autoload) VALUES (?, ?, 'yes')");
                $stmt->execute([$option_name, $option_value]);
            }

            // Insert default HYIPLab plans
            $plans = [
                [
                    'name' => 'Starter Plan',
                    'description' => 'Perfect for beginners',
                    'min_amount' => 10.00,
                    'max_amount' => 1000.00,
                    'roi_percentage' => 2.50,
                    'duration_days' => 30
                ],
                [
                    'name' => 'Premium Plan',
                    'description' => 'For experienced investors',
                    'min_amount' => 100.00,
                    'max_amount' => 10000.00,
                    'roi_percentage' => 3.50,
                    'duration_days' => 60
                ],
                [
                    'name' => 'VIP Plan',
                    'description' => 'Exclusive high-yield investment',
                    'min_amount' => 1000.00,
                    'max_amount' => 100000.00,
                    'roi_percentage' => 5.00,
                    'duration_days' => 90
                ]
            ];

            foreach ($plans as $plan) {
                $stmt = $this->pdo->prepare("
                    INSERT IGNORE INTO wp_hyiplab_plans 
                    (name, description, min_amount, max_amount, roi_percentage, duration_days) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $plan['name'],
                    $plan['description'],
                    $plan['min_amount'],
                    $plan['max_amount'],
                    $plan['roi_percentage'],
                    $plan['duration_days']
                ]);
            }

            $this->success[] = "Default data inserted successfully";

        } catch (PDOException $e) {
            $this->errors[] = "Error inserting default data: " . $e->getMessage();
        }
    }

    public function run(): void {
        echo "🚀 BlackCnote Database Setup\n";
        echo "============================\n\n";

        try {
            $this->setupWordPressTables();
            $this->setupHYIPLabTables();
            $this->insertDefaultData();

            echo "✅ Setup completed successfully!\n\n";
            
            if (!empty($this->success)) {
                echo "Success messages:\n";
                foreach ($this->success as $msg) {
                    echo "  ✓ $msg\n";
                }
                echo "\n";
            }

            if (!empty($this->errors)) {
                echo "❌ Errors encountered:\n";
                foreach ($this->errors as $error) {
                    echo "  ✗ $error\n";
                }
                echo "\n";
            }

        } catch (Exception $e) {
            echo "❌ Fatal error: " . $e->getMessage() . "\n";
        }
    }
}

// Run the setup
if (php_sapi_name() === 'cli') {
    $setup = new BlackCnoteDatabaseSetup();
    $setup->run();
} 