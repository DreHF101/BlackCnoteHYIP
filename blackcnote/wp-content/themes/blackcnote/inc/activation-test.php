<?php
/**
 * BlackCnote Activation Test
 * Simple test to verify theme activation works
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Activation Test
 */
class BlackCnote_Activation_Test {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_test_menu']);
        add_action('wp_ajax_blackcnote_run_activation_test', [$this, 'run_activation_test']);
    }
    
    /**
     * Add test menu
     */
    public function add_test_menu() {
        add_management_page(
            'BlackCnote Activation Test',
            'BlackCnote Test',
            'manage_options',
            'blackcnote-activation-test',
            [$this, 'display_test_page']
        );
    }
    
    /**
     * Display test page
     */
    public function display_test_page() {
        ?>
        <div class="wrap">
            <h1>BlackCnote Activation Test</h1>
            
            <div class="notice notice-info">
                <p><strong>This tool will test the theme activation process and identify any issues.</strong></p>
            </div>
            
            <div class="card">
                <h2>Test Results</h2>
                <div id="test-results">
                    <p>Click the button below to run the activation test:</p>
                    <button id="run-test-btn" class="button button-primary">Run Activation Test</button>
                </div>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                $('#run-test-btn').on('click', function() {
                    var btn = $(this);
                    btn.prop('disabled', true).text('Testing...');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'blackcnote_run_activation_test',
                            nonce: '<?php echo wp_create_nonce('blackcnote_test_nonce'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#test-results').html('<div class="notice notice-success"><h3>Test Results:</h3><pre>' + JSON.stringify(response.data, null, 2) + '</pre></div>');
                            } else {
                                $('#test-results').html('<div class="notice notice-error"><p>Error: ' + response.data + '</p></div>');
                            }
                        },
                        error: function() {
                            $('#test-results').html('<div class="notice notice-error"><p>Ajax request failed.</p></div>');
                        },
                        complete: function() {
                            btn.prop('disabled', false).text('Run Activation Test');
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }
    
    /**
     * Run activation test
     */
    public function run_activation_test() {
        check_ajax_referer('blackcnote_test_nonce', 'nonce');
        
        $results = [
            'theme_files' => $this->test_theme_files(),
            'functions' => $this->test_functions(),
            'plugins' => $this->test_plugins(),
            'database' => $this->test_database(),
            'activation' => $this->test_activation()
        ];
        
        wp_send_json_success($results);
    }
    
    /**
     * Test theme files
     */
    private function test_theme_files() {
        $theme_dir = get_template_directory();
        $files = ['style.css', 'functions.php', 'index.php', 'header.php', 'footer.php'];
        
        $results = [];
        foreach ($files as $file) {
            $path = $theme_dir . '/' . $file;
            $results[$file] = [
                'exists' => file_exists($path),
                'readable' => is_readable($path),
                'size' => file_exists($path) ? filesize($path) : 0
            ];
        }
        
        return $results;
    }
    
    /**
     * Test functions
     */
    private function test_functions() {
        $results = [
            'blackcnote_theme_setup' => function_exists('blackcnote_theme_setup'),
            'blackcnote_theme_scripts' => function_exists('blackcnote_theme_scripts'),
            'blackcnote_should_render_wp_header_footer' => function_exists('blackcnote_should_render_wp_header_footer')
        ];
        
        return $results;
    }
    
    /**
     * Test plugins
     */
    private function test_plugins() {
        $plugins = [
            'hyiplab/hyiplab.php' => 'HYIPLab Plugin',
            'full-content-checker/full-content-checker.php' => 'Full Content Checker'
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
     * Test database
     */
    private function test_database() {
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
            $core_tables = ['posts', 'pages', 'options', 'users'];
            foreach ($core_tables as $table) {
                $results['tables'][$table] = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'") !== null;
            }
            
            // Check theme options
            $theme_options = ['blackcnote_theme_version'];
            foreach ($theme_options as $option) {
                $results['options'][$option] = get_option($option) !== false;
            }
            
        } catch (Exception $e) {
            $results['error'] = $e->getMessage();
        }
        
        return $results;
    }
    
    /**
     * Test activation
     */
    private function test_activation() {
        $results = [
            'theme_active' => wp_get_theme()->get_stylesheet() === 'blackcnote',
            'theme_name' => wp_get_theme()->get('Name'),
            'theme_version' => wp_get_theme()->get('Version'),
            'wp_debug' => defined('WP_DEBUG') && WP_DEBUG,
            'wp_debug_log' => defined('WP_DEBUG_LOG') && WP_DEBUG_LOG
        ];
        
        return $results;
    }
}

// Initialize the activation test
new BlackCnote_Activation_Test(); 