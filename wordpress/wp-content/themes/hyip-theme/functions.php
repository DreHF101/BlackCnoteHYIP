<?php
/**
 * HYIP Theme functions and definitions
 *
 * @package HYIP_Theme
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define theme constants
 */
define('HYIP_THEME_VERSION', '1.0.0');
define('HYIP_THEME_DIR', get_template_directory());
define('HYIP_THEME_URI', get_template_directory_uri());

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @since 1.0.0
 * @return void
 */
function hyip_theme_setup(): void {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus([
        'primary' => esc_html__('Primary Menu', 'hyip-theme'),
        'footer' => esc_html__('Footer Menu', 'hyip-theme'),
    ]);

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for core custom logo
    add_theme_support('custom-logo', [
        'height' => 250,
        'width' => 250,
        'flex-width' => true,
        'flex-height' => true,
    ]);
}
add_action('after_setup_theme', 'hyip_theme_setup');

/**
 * Enqueues scripts and styles.
 *
 * @since 1.0.0
 * @return void
 */
function hyip_theme_scripts(): void {
    // Enqueue Bootstrap 5 CSS
    wp_enqueue_style(
        'bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
        [],
        '5.3.0'
    );

    // Enqueue theme stylesheet
    wp_enqueue_style(
        'hyip-theme-style',
        get_stylesheet_uri(),
        ['bootstrap'],
        HYIP_THEME_VERSION
    );

    // Enqueue Bootstrap 5 JS Bundle
    wp_enqueue_script(
        'bootstrap-bundle',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
        [],
        '5.3.0',
        true
    );

    // Enqueue theme script
    wp_enqueue_script(
        'hyip-theme-script',
        get_template_directory_uri() . '/js/theme.js',
        ['bootstrap-bundle'],
        HYIP_THEME_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script('hyip-theme-script', 'hyipTheme', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('hyip_theme_nonce'),
    ]);
}

/**
 * Registers custom post types for the theme.
 *
 * @since 1.0.0
 * @return void
 */
function hyip_theme_register_post_types(): void {
    register_post_type('hyip_plan', [
        'labels' => [
            'name' => __('Investment Plans', 'hyip-theme'),
            'singular_name' => __('Investment Plan', 'hyip-theme'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_icon' => 'dashicons-chart-line',
        'show_in_rest' => true,
    ]);
}

/**
 * Creates necessary database tables for the theme.
 *
 * @since 1.0.0
 * @return void
 * @throws Exception If table creation fails
 */
function hyip_theme_create_tables(): void {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $tables = [
        'hyiplab_plans' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}hyiplab_plans (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            amount decimal(20,2) NOT NULL,
            duration int NOT NULL,
            roi decimal(5,2) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;",
        
        'hyiplab_investments' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}hyiplab_investments (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            plan_id bigint(20) NOT NULL,
            amount decimal(20,2) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            returns decimal(20,2) DEFAULT 0,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY plan_id (plan_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;",
        
        'hyip_error_logs' => "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}hyip_error_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            log_entry longtext NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY created_at (created_at)
        ) $charset_collate;"
    ];
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    foreach ($tables as $table_name => $sql) {
        try {
            dbDelta($sql);
            
            // Verify table creation
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table_name}'");
            if (!$table_exists) {
                throw new Exception("Failed to create table {$table_name}");
            }
        } catch (Exception $e) {
            hyip_theme_log_error("Error creating table {$table_name}: " . $e->getMessage());
        }
    }
}

/**
 * Verifies database structure and integrity.
 *
 * @since 1.0.0
 * @return array Array of verification results
 */
function hyip_theme_verify_database(): array {
    global $wpdb;
    
    $tables = [
        'hyiplab_plans',
        'hyiplab_investments',
        'hyip_error_logs'
    ];
    
    foreach ($tables as $table) {
        try {
            $table_name = $wpdb->prefix . $table;
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");
            
            if (!$table_exists) {
                throw new Exception("Table {$table} does not exist");
            }
            
            // Check for required columns
            $columns = $wpdb->get_results("SHOW COLUMNS FROM {$table_name}");
            if (empty($columns)) {
                throw new Exception("Table {$table} has no columns");
            }
            
            // Verify indexes
            $indexes = $wpdb->get_results("SHOW INDEX FROM {$table_name}");
            if (empty($indexes)) {
                throw new Exception("Table {$table} has no indexes");
            }
        } catch (Exception $e) {
            hyip_theme_log_error("Database verification failed for {$table}: " . $e->getMessage());
        }
    }

    return [
        'hyiplab_plans' => $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}hyiplab_plans'"),
        'hyiplab_investments' => $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}hyiplab_investments'"),
        'hyip_error_logs' => $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}hyip_error_logs'")
    ];
}

/**
 * Wraps database operations in a transaction.
 *
 * @since 1.0.0
 * @param callable $operation The database operation to perform
 * @param array $params Parameters for the operation
 * @return mixed Result of the operation
 * @throws Exception If the operation fails
 */
function hyip_theme_secure_db_operation(callable $operation, array $params = []): mixed {
    try {
        global $wpdb;
        
        // Start transaction
        $wpdb->query('START TRANSACTION');
        
        // Execute operation
        $result = $operation($params);
        
        // Check for errors
        if ($wpdb->last_error) {
            throw new Exception($wpdb->last_error);
        }
        
        // Commit transaction
        $wpdb->query('COMMIT');
        
        return $result;
    } catch (Exception $e) {
        // Rollback transaction
        $wpdb->query('ROLLBACK');
        
        // Log error
        hyip_theme_log_error('Database operation failed: ' . $e->getMessage(), [
            'operation' => debug_backtrace()[1]['function'] ?? 'unknown',
            'params' => $params
        ]);
        
        return false;
    }
}

/**
 * Sanitize database input
 */
function hyip_theme_sanitize_db_input(mixed $input, string $type = 'string'): mixed {
    if (is_array($input)) {
        return array_map(fn($value) => hyip_theme_sanitize_db_input($value, $type), $input);
    }
    
    switch ($type) {
        case 'int':
            return absint($input);
        case 'float':
            return floatval($input);
        case 'bool':
            return (bool) $input;
        case 'date':
            return sanitize_text_field($input);
        case 'email':
            return sanitize_email($input);
        case 'url':
            return esc_url_raw($input);
        case 'html':
            return wp_kses_post($input);
        default:
            return sanitize_text_field($input);
    }
}

/**
 * Sanitize user input
 */
function hyip_theme_sanitize_user_input(mixed $input, string $type = 'string'): mixed {
    if (is_array($input)) {
        return array_map(fn($value) => hyip_theme_sanitize_user_input($value, $type), $input);
    }
    
    switch ($type) {
        case 'int':
            return absint($input);
        case 'float':
            return floatval($input);
        case 'bool':
            return (bool) $input;
        case 'date':
            return sanitize_text_field($input);
        case 'email':
            return sanitize_email($input);
        case 'url':
            return esc_url_raw($input);
        case 'html':
            return wp_kses_post($input);
        case 'textarea':
            return sanitize_textarea_field($input);
        case 'title':
            return sanitize_title($input);
        case 'key':
            return sanitize_key($input);
        default:
            return sanitize_text_field($input);
    }
}

/**
 * Verify nonce for AJAX requests
 */
function hyip_theme_verify_nonce(string $nonce, string $action): bool {
    if (!wp_verify_nonce($nonce, $action)) {
        hyip_theme_log_error('Invalid nonce verification', [
            'action' => $action,
            'ip' => hyip_theme_get_client_ip()
        ]);
        return false;
    }
    return true;
}

/**
 * Get client IP address
 */
function hyip_theme_get_client_ip(): string {
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    }
    
    return $ip;
}

/**
 * Prepare and execute a secure database query
 */
function hyip_theme_prepare_query(string $query, array $args = []): mixed {
    global $wpdb;
    
    try {
        if (empty($args)) {
            return $wpdb->query($query);
        }
        
        $prepared_query = $wpdb->prepare($query, $args);
        if ($prepared_query === false) {
            throw new Exception('Query preparation failed');
        }
        
        return $wpdb->query($prepared_query);
    } catch (Exception $e) {
        hyip_theme_log_error('Query preparation failed: ' . $e->getMessage(), [
            'query' => $query,
            'args' => $args
        ]);
        return false;
    }
}

/**
 * Get a single row securely
 */
function hyip_theme_get_row(string $query, array $args = [], string $output = OBJECT): mixed {
    global $wpdb;
    
    try {
        if (empty($args)) {
            return $wpdb->get_row($query, $output);
        }
        
        $prepared_query = $wpdb->prepare($query, $args);
        if ($prepared_query === false) {
            throw new Exception('Query preparation failed');
        }
        
        return $wpdb->get_row($prepared_query, $output);
    } catch (Exception $e) {
        hyip_theme_log_error('Get row failed: ' . $e->getMessage(), [
            'query' => $query,
            'args' => $args
        ]);
        return false;
    }
}

/**
 * Get multiple rows securely
 */
function hyip_theme_get_results(string $query, array $args = [], string $output = OBJECT): mixed {
    global $wpdb;
    
    try {
        if (empty($args)) {
            return $wpdb->get_results($query, $output);
        }
        
        $prepared_query = $wpdb->prepare($query, $args);
        if ($prepared_query === false) {
            throw new Exception('Query preparation failed');
        }
        
        return $wpdb->get_results($prepared_query, $output);
    } catch (Exception $e) {
        hyip_theme_log_error('Get results failed: ' . $e->getMessage(), [
            'query' => $query,
            'args' => $args
        ]);
        return false;
    }
}

/**
 * Insert data securely
 */
function hyip_theme_insert(string $table, array $data, array $format = []): int|false {
    global $wpdb;
    
    try {
        $result = $wpdb->insert($table, $data, $format);
        if ($result === false) {
            throw new Exception($wpdb->last_error);
        }
        return $wpdb->insert_id;
    } catch (Exception $e) {
        hyip_theme_log_error('Insert failed: ' . $e->getMessage(), [
            'table' => $table,
            'data' => $data
        ]);
        return false;
    }
}

/**
 * Update data securely
 */
function hyip_theme_update(string $table, array $data, array $where, array $format = [], array $where_format = []): int|false {
    global $wpdb;
    
    try {
        $result = $wpdb->update($table, $data, $where, $format, $where_format);
        if ($result === false) {
            throw new Exception($wpdb->last_error);
        }
        return $result;
    } catch (Exception $e) {
        hyip_theme_log_error('Update failed: ' . $e->getMessage(), [
            'table' => $table,
            'data' => $data,
            'where' => $where
        ]);
        return false;
    }
}

/**
 * Logs errors to the database and WordPress debug log.
 *
 * @since 1.0.0
 * @param string $message Error message
 * @param array $context Additional context data
 * @return void
 */
function hyip_theme_log_error(string $message, array $context = []): void {
    if (!is_string($message) || empty($message)) {
        return;
    }

    try {
        global $wpdb;
        
        $log_entry = [
            'message' => $message,
            'context' => $context,
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'ip' => hyip_theme_get_client_ip(),
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'hyip_error_logs',
            [
                'log_entry' => wp_json_encode($log_entry),
                'created_at' => current_time('mysql')
            ],
            ['%s', '%s']
        );
        
        if ($result === false) {
            throw new Exception($wpdb->last_error);
        }
        
        // Also log to WordPress debug log if enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                '[HYIP Theme] %s | Context: %s',
                $message,
                wp_json_encode($context)
            ));
        }
    } catch (Exception $e) {
        // Fallback to WordPress debug log
        error_log(sprintf(
            '[HYIP Theme] Error logging failed: %s | Original message: %s',
            $e->getMessage(),
            $message
        ));
    }
}

/**
 * Cleans up old error logs.
 *
 * @since 1.0.0
 * @return void
 */
function hyip_theme_cleanup_error_logs(): void {
    try {
        global $wpdb;
        
        $retention_days = absint(get_option('hyip_theme_log_retention_days', 30));
        if ($retention_days <= 0) {
            return;
        }
        
        $result = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}hyip_error_logs 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $retention_days
        ));
        
        if ($result === false) {
            throw new Exception($wpdb->last_error);
        }
        
        // Optimize table after deletion
        $wpdb->query("OPTIMIZE TABLE {$wpdb->prefix}hyip_error_logs");
    } catch (Exception $e) {
        error_log('[HYIP Theme] Error cleaning up logs: ' . $e->getMessage());
    }
}
add_action('hyip_theme_daily_cleanup', 'hyip_theme_cleanup_error_logs');

/**
 * Schedules cleanup task.
 *
 * @since 1.0.0
 * @return void
 */
function hyip_theme_schedule_cleanup(): void {
    if (!wp_next_scheduled('hyip_theme_daily_cleanup')) {
        wp_schedule_event(time(), 'daily', 'hyip_theme_daily_cleanup');
    }
}
add_action('init', 'hyip_theme_schedule_cleanup');

/**
 * Handles exceptions with optional rethrow in debug mode.
 *
 * @since 1.0.0
 * @param Throwable $e The exception to handle
 * @param bool $rethrow Whether to rethrow the exception in debug mode
 * @return void
 */
function hyip_theme_handle_exception(Throwable $e, bool $rethrow = false): void {
    $context = [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
        'code' => $e->getCode(),
    ];
    
    hyip_theme_log_error($e->getMessage(), $context);
    
    if ($rethrow && defined('WP_DEBUG') && WP_DEBUG) {
        throw $e;
    }
}

/**
 * Registers a custom error handler.
 *
 * @since 1.0.0
 * @return void
 */
function hyip_theme_register_error_handler(): void {
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $context = [
            'file' => $errfile,
            'line' => $errline,
            'type' => $errno,
        ];
        
        hyip_theme_log_error($errstr, $context);
        
        return true;
    });
}
add_action('init', 'hyip_theme_register_error_handler');

/**
 * Handles fatal errors.
 *
 * @since 1.0.0
 * @return void
 */
function hyip_theme_handle_fatal_error(): void {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        hyip_theme_log_error('Fatal error occurred', [
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line'],
            'type' => $error['type']
        ]);
    }
}
register_shutdown_function('hyip_theme_handle_fatal_error');