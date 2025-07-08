<?php
/**
 * BlackCnote Diagnostic Tool
 * Helps identify and fix theme and plugin issues
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Diagnostic Tool
 */
class BlackCnote_Diagnostic_Tool {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_diagnostic_menu']);
        add_action('wp_ajax_blackcnote_fix_issues', [$this, 'fix_issues']);
    }
    
    /**
     * Add diagnostic menu
     */
    public function add_diagnostic_menu() {
        add_management_page(
            'BlackCnote Diagnostic Tool',
            'BlackCnote Diagnostic',
            'manage_options',
            'blackcnote-diagnostic',
            [$this, 'display_diagnostic_page']
        );
    }
    
    /**
     * Display diagnostic page
     */
    public function display_diagnostic_page() {
        $diagnostics = $this->run_diagnostics();
        ?>
        <div class="wrap">
            <h1>BlackCnote Diagnostic Tool</h1>
            
            <div class="notice notice-info">
                <p><strong>This tool will help identify and fix issues with the BlackCnote theme and plugins.</strong></p>
            </div>
            
            <div class="card">
                <h2>System Diagnostics</h2>
                <?php $this->display_diagnostic_results($diagnostics); ?>
            </div>
            
            <div class="card">
                <h2>Fix Issues</h2>
                <p>Click the button below to automatically fix common issues:</p>
                <button id="fix-issues-btn" class="button button-primary">Fix Issues</button>
                <div id="fix-results"></div>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                $('#fix-issues-btn').on('click', function() {
                    var btn = $(this);
                    btn.prop('disabled', true).text('Fixing...');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'blackcnote_fix_issues',
                            nonce: '<?php echo wp_create_nonce('blackcnote_fix_nonce'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#fix-results').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                                location.reload();
                            } else {
                                $('#fix-results').html('<div class="notice notice-error"><p>Error: ' + response.data + '</p></div>');
                            }
                        },
                        error: function() {
                            $('#fix-results').html('<div class="notice notice-error"><p>Ajax request failed.</p></div>');
                        },
                        complete: function() {
                            btn.prop('disabled', false).text('Fix Issues');
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }
    
    /**
     * Run diagnostics
     */
    private function run_diagnostics() {
        return [
            'theme' => $this->check_theme(),
            'plugins' => $this->check_plugins(),
            'files' => $this->check_files(),
            'database' => $this->check_database(),
            'permissions' => $this->check_permissions(),
            'react' => $this->check_react()
        ];
    }
    
    /**
     * Check theme
     */
    private function check_theme() {
        $theme = wp_get_theme();
        $active_theme = wp_get_theme()->get_stylesheet();
        
        return [
            'active' => $active_theme === 'blackcnote',
            'name' => $theme->get('Name'),
            'version' => $theme->get('Version'),
            'author' => $theme->get('Author'),
            'description' => $theme->get('Description')
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
     * Check files
     */
    private function check_files() {
        $theme_dir = get_template_directory();
        $files = [
            'style.css' => 'Theme stylesheet',
            'functions.php' => 'Theme functions',
            'index.php' => 'Main template',
            'header.php' => 'Header template',
            'footer.php' => 'Footer template',
            'inc/blackcnote-live-editing-api.php' => 'Live Editing API',
            'inc/theme-activation-fix.php' => 'Theme Activation Fix'
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
     * Check React
     */
    private function check_react() {
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
     * Display diagnostic results
     */
    private function display_diagnostic_results($diagnostics) {
        foreach ($diagnostics as $section => $data) {
            echo '<h3>' . ucfirst($section) . '</h3>';
            echo '<table class="widefat">';
            echo '<thead><tr><th>Item</th><th>Status</th><th>Details</th></tr></thead>';
            echo '<tbody>';
            
            switch ($section) {
                case 'theme':
                    $this->display_theme_results($data);
                    break;
                case 'plugins':
                    $this->display_plugin_results($data);
                    break;
                case 'files':
                    $this->display_file_results($data);
                    break;
                case 'database':
                    $this->display_database_results($data);
                    break;
                case 'permissions':
                    $this->display_permission_results($data);
                    break;
                case 'react':
                    $this->display_react_results($data);
                    break;
            }
            
            echo '</tbody></table><br>';
        }
    }
    
    /**
     * Display theme results
     */
    private function display_theme_results($data) {
        $status = $data['active'] ? 'Active' : 'Inactive';
        $status_class = $data['active'] ? 'success' : 'error';
        
        echo '<tr>';
        echo '<td>Theme Status</td>';
        echo '<td><span class="dashicons dashicons-' . $status_class . '"></span> ' . $status . '</td>';
        echo '<td>' . $data['name'] . ' v' . $data['version'] . '</td>';
        echo '</tr>';
    }
    
    /**
     * Display plugin results
     */
    private function display_plugin_results($data) {
        foreach ($data as $plugin => $info) {
            $status = $info['active'] ? 'Active' : ($info['installed'] ? 'Inactive' : 'Not Installed');
            $status_class = $info['active'] ? 'success' : ($info['installed'] ? 'warning' : 'error');
            
            echo '<tr>';
            echo '<td>' . $info['name'] . '</td>';
            echo '<td><span class="dashicons dashicons-' . $status_class . '"></span> ' . $status . '</td>';
            echo '<td>' . $plugin . '</td>';
            echo '</tr>';
        }
    }
    
    /**
     * Display file results
     */
    private function display_file_results($data) {
        foreach ($data as $file => $info) {
            $status = $info['exists'] ? 'Exists' : 'Missing';
            $status_class = $info['exists'] ? 'success' : 'error';
            
            echo '<tr>';
            echo '<td>' . $info['description'] . '</td>';
            echo '<td><span class="dashicons dashicons-' . $status_class . '"></span> ' . $status . '</td>';
            echo '<td>' . $file . ' (' . $info['size'] . ' bytes)</td>';
            echo '</tr>';
        }
    }
    
    /**
     * Display database results
     */
    private function display_database_results($data) {
        $status = $data['connection'] ? 'Connected' : 'Error';
        $status_class = $data['connection'] ? 'success' : 'error';
        
        echo '<tr>';
        echo '<td>Database Connection</td>';
        echo '<td><span class="dashicons dashicons-' . $status_class . '"></span> ' . $status . '</td>';
        echo '<td>' . (isset($data['error']) ? $data['error'] : 'OK') . '</td>';
        echo '</tr>';
    }
    
    /**
     * Display permission results
     */
    private function display_permission_results($data) {
        foreach ($data as $permission => $writable) {
            $status = $writable ? 'Writable' : 'Not Writable';
            $status_class = $writable ? 'success' : 'error';
            
            echo '<tr>';
            echo '<td>' . ucfirst(str_replace('_', ' ', $permission)) . '</td>';
            echo '<td><span class="dashicons dashicons-' . $status_class . '"></span> ' . $status . '</td>';
            echo '<td>' . $permission . '</td>';
            echo '</tr>';
        }
    }
    
    /**
     * Display React results
     */
    private function display_react_results($data) {
        $status = $data['dist_exists'] ? 'Available' : 'Not Available';
        $status_class = $data['dist_exists'] ? 'success' : 'warning';
        
        echo '<tr>';
        echo '<td>React Assets</td>';
        echo '<td><span class="dashicons dashicons-' . $status_class . '"></span> ' . $status . '</td>';
        echo '<td>' . count($data['css_files']) . ' CSS, ' . count($data['js_files']) . ' JS files</td>';
        echo '</tr>';
    }
    
    /**
     * Fix issues
     */
    public function fix_issues() {
        check_ajax_referer('blackcnote_fix_nonce', 'nonce');
        
        $fixes = [];
        
        try {
            // Fix theme activation
            $fixes[] = $this->fix_theme_activation();
            
            // Fix plugin issues
            $fixes[] = $this->fix_plugin_issues();
            
            // Fix file permissions
            $fixes[] = $this->fix_permissions();
            
            // Clear caches
            $fixes[] = $this->clear_caches();
            
            wp_send_json_success([
                'message' => 'Issues fixed successfully: ' . implode(', ', $fixes)
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error('Error fixing issues: ' . $e->getMessage());
        }
    }
    
    /**
     * Fix theme activation
     */
    private function fix_theme_activation() {
        // Set theme options
        $options = [
            'blackcnote_theme_version' => '1.0.0',
            'blackcnote_react_enabled' => true,
            'blackcnote_live_editing_enabled' => true,
            'blackcnote_wp_header_footer_enabled' => false
        ];
        
        foreach ($options as $option => $value) {
            update_option($option, $value);
        }
        
        return 'Theme options updated';
    }
    
    /**
     * Fix plugin issues
     */
    private function fix_plugin_issues() {
        // Deactivate problematic plugins temporarily
        $plugins = ['hyiplab/hyiplab.php', 'full-content-checker/full-content-checker.php'];
        
        foreach ($plugins as $plugin) {
            if (is_plugin_active($plugin)) {
                deactivate_plugins($plugin);
            }
        }
        
        return 'Plugins temporarily deactivated for troubleshooting';
    }
    
    /**
     * Fix permissions
     */
    private function fix_permissions() {
        $theme_dir = get_template_directory();
        
        // Try to make theme directory writable
        if (!is_writable($theme_dir)) {
            chmod($theme_dir, 0755);
        }
        
        return 'Permissions checked';
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
        
        return 'Caches cleared';
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

// Initialize the diagnostic tool
new BlackCnote_Diagnostic_Tool(); 