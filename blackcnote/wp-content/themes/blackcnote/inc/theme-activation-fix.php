<?php
/**
 * BlackCnote Theme Activation Fix
 * Handles theme activation issues and provides diagnostics
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Theme Activation Handler
 */
class BlackCnote_Theme_Activation_Fix {
    
    public function __construct() {
        add_action('after_switch_theme', [$this, 'handle_theme_activation']);
        add_action('admin_notices', [$this, 'display_activation_notices']);
        add_action('wp_ajax_blackcnote_diagnostic_check', [$this, 'run_diagnostic_check']);
    }
    
    /**
     * Handle theme activation
     */
    public function handle_theme_activation() {
        try {
            // Check for required files
            $this->check_required_files();
            
            // Create required pages
            $this->create_required_pages();
            
            // Set up theme options
            $this->setup_theme_options();
            
            // Clear any caches
            $this->clear_caches();
            
            // Log successful activation
            error_log('BlackCnote Theme activated successfully');
            
        } catch (Exception $e) {
            error_log('BlackCnote Theme Activation Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Check for required files
     */
    private function check_required_files() {
        $required_files = [
            'style.css',
            'functions.php',
            'index.php',
            'header.php',
            'footer.php'
        ];
        
        $missing_files = [];
        foreach ($required_files as $file) {
            if (!file_exists(get_template_directory() . '/' . $file)) {
                $missing_files[] = $file;
            }
        }
        
        if (!empty($missing_files)) {
            throw new Exception('Missing required theme files: ' . implode(', ', $missing_files));
        }
    }
    
    /**
     * Create required pages
     */
    private function create_required_pages() {
        $pages = [
            'home' => [
                'title' => 'Home',
                'content' => '[blackcnote_dashboard]',
                'template' => 'front-page.php'
            ],
            'dashboard' => [
                'title' => 'Dashboard',
                'content' => '[blackcnote_dashboard]',
                'template' => 'page-dashboard.php'
            ],
            'investment-plans' => [
                'title' => 'Investment Plans',
                'content' => '[blackcnote_plans]',
                'template' => 'page-plans.php'
            ],
            'transactions' => [
                'title' => 'Transactions',
                'content' => '[blackcnote_transactions]',
                'template' => 'page-transactions.php'
            ]
        ];
        
        foreach ($pages as $slug => $page_data) {
            $existing_page = get_page_by_path($slug);
            if (!$existing_page) {
                $page_id = wp_insert_post([
                    'post_title' => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_name' => $slug
                ]);
                
                if ($page_id && !is_wp_error($page_id)) {
                    update_post_meta($page_id, '_wp_page_template', $page_data['template']);
                }
            }
        }
    }
    
    /**
     * Set up theme options
     */
    private function setup_theme_options() {
        // Set default theme options
        $default_options = [
            'blackcnote_theme_version' => '1.0.0',
            'blackcnote_react_enabled' => true,
            'blackcnote_live_editing_enabled' => true,
            'blackcnote_wp_header_footer_enabled' => false
        ];
        
        foreach ($default_options as $option => $value) {
            if (get_option($option) === false) {
                add_option($option, $value);
            }
        }
    }
    
    /**
     * Clear caches
     */
    private function clear_caches() {
        // Clear WordPress cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear theme cache
        $cache_dir = get_template_directory() . '/cache';
        if (is_dir($cache_dir)) {
            $this->delete_directory($cache_dir);
        }
    }
    
    /**
     * Display activation notices
     */
    public function display_activation_notices() {
        if (get_transient('blackcnote_theme_activated')) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>BlackCnote Theme Activated Successfully!</strong></p>
                <p>The theme has been activated and all required pages have been created.</p>
                <p><a href="<?php echo admin_url('admin-ajax.php?action=blackcnote_diagnostic_check'); ?>" class="button button-primary">Run Diagnostic Check</a></p>
            </div>
            <?php
            delete_transient('blackcnote_theme_activated');
        }
    }
    
    /**
     * Run diagnostic check
     */
    public function run_diagnostic_check() {
        check_ajax_referer('blackcnote_diagnostic_nonce', 'nonce');
        
        $diagnostics = [
            'theme_files' => $this->check_theme_files(),
            'database' => $this->check_database(),
            'permissions' => $this->check_permissions(),
            'plugins' => $this->check_plugins(),
            'react_assets' => $this->check_react_assets()
        ];
        
        wp_send_json_success($diagnostics);
    }
    
    /**
     * Check theme files
     */
    private function check_theme_files() {
        $theme_dir = get_template_directory();
        $files = [
            'style.css' => 'Theme stylesheet',
            'functions.php' => 'Theme functions',
            'index.php' => 'Main template',
            'header.php' => 'Header template',
            'footer.php' => 'Footer template'
        ];
        
        $results = [];
        foreach ($files as $file => $description) {
            $path = $theme_dir . '/' . $file;
            $results[$file] = [
                'exists' => file_exists($path),
                'readable' => is_readable($path),
                'size' => file_exists($path) ? filesize($path) : 0,
                'description' => $description
            ];
        }
        
        return $results;
    }
    
    /**
     * Check database
     */
    private function check_database() {
        global $wpdb;
        
        $results = [
            'connection' => false,
            'tables' => [],
            'options' => []
        ];
        
        try {
            $wpdb->query("SELECT 1");
            $results['connection'] = true;
            
            // Check core tables
            $core_tables = ['posts', 'pages', 'options', 'users', 'usermeta'];
            foreach ($core_tables as $table) {
                $results['tables'][$table] = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'") !== null;
            }
            
            // Check theme options
            $theme_options = [
                'blackcnote_theme_version',
                'blackcnote_react_enabled',
                'blackcnote_live_editing_enabled'
            ];
            
            foreach ($theme_options as $option) {
                $results['options'][$option] = get_option($option) !== false;
            }
            
        } catch (Exception $e) {
            $results['error'] = $e->getMessage();
        }
        
        return $results;
    }
    
    /**
     * Check permissions
     */
    private function check_permissions() {
        $theme_dir = get_template_directory();
        
        return [
            'theme_dir_writable' => is_writable($theme_dir),
            'wp_content_writable' => is_writable(WP_CONTENT_DIR),
            'uploads_writable' => is_writable(wp_upload_dir()['basedir'])
        ];
    }
    
    /**
     * Check plugins
     */
    private function check_plugins() {
        $plugins = [
            'hyiplab/hyiplab.php' => 'HYIPLab Plugin',
            'full-content-checker/full-content-checker.php' => 'Full Content Checker',
            'blackcnote-cors/blackcnote-cors.php' => 'BlackCnote CORS'
        ];
        
        $results = [];
        foreach ($plugins as $plugin => $name) {
            $results[$plugin] = [
                'active' => is_plugin_active($plugin),
                'installed' => file_exists(WP_PLUGIN_DIR . '/' . $plugin),
                'name' => $name
            ];
        }
        
        return $results;
    }
    
    /**
     * Check React assets
     */
    private function check_react_assets() {
        $react_dir = get_template_directory() . '/dist';
        
        return [
            'dist_exists' => is_dir($react_dir),
            'index_html_exists' => file_exists($react_dir . '/index.html'),
            'assets_dir_exists' => is_dir($react_dir . '/assets'),
            'css_files' => $this->get_react_files($react_dir, 'css'),
            'js_files' => $this->get_react_files($react_dir, 'js')
        ];
    }
    
    /**
     * Get React files
     */
    private function get_react_files($dir, $ext) {
        if (!is_dir($dir . '/assets')) {
            return [];
        }
        
        $files = [];
        $pattern = $dir . '/assets/*.' . $ext;
        foreach (glob($pattern) as $file) {
            $files[] = basename($file);
        }
        
        return $files;
    }
    
    /**
     * Delete directory recursively
     */
    private function delete_directory($dir) {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->delete_directory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
}

// Initialize the activation fix
new BlackCnote_Theme_Activation_Fix(); 