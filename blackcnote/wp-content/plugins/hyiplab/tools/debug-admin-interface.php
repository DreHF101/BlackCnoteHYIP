<?php
/**
 * BlackCnote Debug System Admin Interface
 * Provides a comprehensive admin dashboard for debug system management
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class BlackCnoteDebugAdminInterface {
    
    private static $instance = null;
    private $debug_system = null;
    private $page_slug = 'blackcnote-debug';
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->debug_system = BlackCnoteOptimizedDebugSystem::getInstance();
        $this->setupHooks();
    }
    
    private function setupHooks() {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_ajax_blackcnote_debug_action', [$this, 'handleAjaxAction']);
        add_action('admin_init', [$this, 'handleFormSubmission']);
    }
    
    public function addAdminMenu() {
        add_management_page(
            'BlackCnote Debug System',
            'BC Debug',
            'manage_options',
            $this->page_slug,
            [$this, 'renderAdminPage']
        );
    }
    
    public function enqueueAdminAssets($hook) {
        if ($hook !== 'tools_page_' . $this->page_slug) {
            return;
        }
        
        wp_enqueue_style(
            'blackcnote-debug-admin',
            plugin_dir_url(__FILE__) . 'assets/css/debug-admin.css',
            [],
            '1.0.0'
        );
        
        wp_enqueue_script(
            'blackcnote-debug-admin',
            plugin_dir_url(__FILE__) . 'assets/js/debug-admin.js',
            ['jquery'],
            '1.0.0',
            true
        );
        
        wp_localize_script('blackcnote-debug-admin', 'blackcnoteDebug', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('blackcnote_debug_nonce'),
            'strings' => [
                'confirmClearLog' => 'Are you sure you want to clear the debug log?',
                'confirmRotateLog' => 'Are you sure you want to rotate the debug log?',
                'loading' => 'Loading...',
                'error' => 'An error occurred',
                'success' => 'Operation completed successfully'
            ]
        ]);
    }
    
    public function renderAdminPage() {
        $current_tab = $_GET['tab'] ?? 'dashboard';
        $config = $this->debug_system->getConfig();
        
        ?>
        <div class="wrap">
            <h1>BlackCnote Debug System</h1>
            
            <nav class="nav-tab-wrapper">
                <a href="?page=<?php echo $this->page_slug; ?>&tab=dashboard" 
                   class="nav-tab <?php echo $current_tab === 'dashboard' ? 'nav-tab-active' : ''; ?>">
                    Dashboard
                </a>
                <a href="?page=<?php echo $this->page_slug; ?>&tab=configuration" 
                   class="nav-tab <?php echo $current_tab === 'configuration' ? 'nav-tab-active' : ''; ?>">
                    Configuration
                </a>
                <a href="?page=<?php echo $this->page_slug; ?>&tab=logs" 
                   class="nav-tab <?php echo $current_tab === 'logs' ? 'nav-tab-active' : ''; ?>">
                    Logs
                </a>
                <a href="?page=<?php echo $this->page_slug; ?>&tab=performance" 
                   class="nav-tab <?php echo $current_tab === 'performance' ? 'nav-tab-active' : ''; ?>">
                    Performance
                </a>
                <a href="?page=<?php echo $this->page_slug; ?>&tab=system" 
                   class="nav-tab <?php echo $current_tab === 'system' ? 'nav-tab-active' : ''; ?>">
                    System Info
                </a>
            </nav>
            
            <div class="tab-content">
                <?php
                switch ($current_tab) {
                    case 'dashboard':
                        $this->renderDashboardTab();
                        break;
                    case 'configuration':
                        $this->renderConfigurationTab($config);
                        break;
                    case 'logs':
                        $this->renderLogsTab();
                        break;
                    case 'performance':
                        $this->renderPerformanceTab();
                        break;
                    case 'system':
                        $this->renderSystemTab();
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    private function renderDashboardTab() {
        $log_file = $this->debug_system->getLogFilePath();
        $log_size = $this->debug_system->getLogFileSize();
        $config = $this->debug_system->getConfig();
        $memory_usage = memory_get_usage();
        $peak_memory = memory_get_peak_usage();
        
        ?>
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Debug System Status</h3>
                <div class="status-indicator <?php echo $config->get('enabled') ? 'status-enabled' : 'status-disabled'; ?>">
                    <?php echo $config->get('enabled') ? 'Enabled' : 'Disabled'; ?>
                </div>
                <p>Current log level: <strong><?php echo $config->get('log_level'); ?></strong></p>
            </div>
            
            <div class="dashboard-card">
                <h3>Log File</h3>
                <p>Path: <code><?php echo $log_file; ?></code></p>
                <p>Size: <strong><?php echo $this->formatBytes($log_size); ?></strong></p>
                <div class="button-group">
                    <button class="button" onclick="viewLog()">View Log</button>
                    <button class="button" onclick="clearLog()">Clear Log</button>
                    <button class="button" onclick="rotateLog()">Rotate Log</button>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h3>Memory Usage</h3>
                <p>Current: <strong><?php echo $this->formatBytes($memory_usage); ?></strong></p>
                <p>Peak: <strong><?php echo $this->formatBytes($peak_memory); ?></strong></p>
                <p>Limit: <strong><?php echo ini_get('memory_limit'); ?></strong></p>
            </div>
            
            <div class="dashboard-card">
                <h3>Active Modules</h3>
                <ul>
                    <?php
                    $modules = ['core', 'react', 'theme', 'performance', 'security'];
                    foreach ($modules as $module) {
                        $enabled = $config->get($module . '_debugging', false);
                        echo '<li>' . ucfirst($module) . ': <span class="' . ($enabled ? 'status-enabled' : 'status-disabled') . '">' . ($enabled ? 'Active' : 'Inactive') . '</span></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
        
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="button-group">
                <button class="button button-primary" onclick="toggleDebug()">
                    <?php echo $config->get('enabled') ? 'Disable Debug' : 'Enable Debug'; ?>
                </button>
                <button class="button" onclick="testLogging()">Test Logging</button>
                <button class="button" onclick="exportLog()">Export Log</button>
                <button class="button" onclick="refreshStats()">Refresh Stats</button>
            </div>
        </div>
        <?php
    }
    
    private function renderConfigurationTab($config) {
        $options = $config->getAll();
        
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('blackcnote_debug_config', 'debug_nonce'); ?>
            <input type="hidden" name="action" value="update_debug_config">
            
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Debug System</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enabled" value="1" 
                                   <?php checked($options['enabled']); ?>>
                            Enable comprehensive debugging
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Log Level</th>
                    <td>
                        <select name="log_level">
                            <option value="DEBUG" <?php selected($options['log_level'], 'DEBUG'); ?>>Debug</option>
                            <option value="INFO" <?php selected($options['log_level'], 'INFO'); ?>>Info</option>
                            <option value="WARNING" <?php selected($options['log_level'], 'WARNING'); ?>>Warning</option>
                            <option value="ERROR" <?php selected($options['log_level'], 'ERROR'); ?>>Error</option>
                            <option value="FATAL" <?php selected($options['log_level'], 'FATAL'); ?>>Fatal</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Buffer Size</th>
                    <td>
                        <input type="number" name="buffer_size" value="<?php echo $options['buffer_size']; ?>" min="10" max="1000">
                        <p class="description">Number of log entries to buffer before writing to file</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Max Log Size</th>
                    <td>
                        <input type="number" name="max_log_size" value="<?php echo $options['max_log_size']; ?>" min="1048576" max="104857600">
                        <p class="description">Maximum log file size in bytes (1MB - 100MB)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Log Rotation</th>
                    <td>
                        <label>
                            <input type="checkbox" name="log_rotation" value="1" 
                                   <?php checked($options['log_rotation']); ?>>
                            Automatically rotate log files when size limit is reached
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">React Debugging</th>
                    <td>
                        <label>
                            <input type="checkbox" name="react_debugging" value="1" 
                                   <?php checked($options['react_debugging']); ?>>
                            Enable React application debugging
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Theme Debugging</th>
                    <td>
                        <label>
                            <input type="checkbox" name="theme_debugging" value="1" 
                                   <?php checked($options['theme_debugging']); ?>>
                            Enable theme-specific debugging
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Performance Monitoring</th>
                    <td>
                        <label>
                            <input type="checkbox" name="performance_monitoring" value="1" 
                                   <?php checked($options['performance_monitoring']); ?>>
                            Enable performance monitoring and logging
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Security Monitoring</th>
                    <td>
                        <label>
                            <input type="checkbox" name="security_monitoring" value="1" 
                                   <?php checked($options['security_monitoring']); ?>>
                            Enable security monitoring and logging
                        </label>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Save Configuration'); ?>
        </form>
        <?php
    }
    
    private function renderLogsTab() {
        $log_file = $this->debug_system->getLogFilePath();
        $log_content = '';
        
        if (file_exists($log_file)) {
            $log_content = file_get_contents($log_file);
        }
        
        ?>
        <div class="log-controls">
            <div class="button-group">
                <button class="button" onclick="refreshLog()">Refresh</button>
                <button class="button" onclick="clearLog()">Clear Log</button>
                <button class="button" onclick="downloadLog()">Download</button>
                <button class="button" onclick="searchLog()">Search</button>
            </div>
            
            <div class="log-filters">
                <select id="log-level-filter">
                    <option value="">All Levels</option>
                    <option value="DEBUG">Debug</option>
                    <option value="INFO">Info</option>
                    <option value="WARNING">Warning</option>
                    <option value="ERROR">Error</option>
                    <option value="FATAL">Fatal</option>
                </select>
                
                <input type="text" id="log-search" placeholder="Search log entries...">
            </div>
        </div>
        
        <div class="log-viewer">
            <textarea id="log-content" readonly><?php echo esc_textarea($log_content); ?></textarea>
        </div>
        
        <script>
            // Auto-scroll to bottom
            document.getElementById('log-content').scrollTop = document.getElementById('log-content').scrollHeight;
        </script>
        <?php
    }
    
    private function renderPerformanceTab() {
        $monitor = $this->debug_system->getPerformanceMonitor();
        
        ?>
        <div class="performance-grid">
            <div class="performance-card">
                <h3>Memory Usage</h3>
                <div class="metric">
                    <span class="metric-label">Current:</span>
                    <span class="metric-value"><?php echo $this->formatBytes(memory_get_usage()); ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Peak:</span>
                    <span class="metric-value"><?php echo $this->formatBytes(memory_get_peak_usage()); ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Limit:</span>
                    <span class="metric-value"><?php echo ini_get('memory_limit'); ?></span>
                </div>
            </div>
            
            <div class="performance-card">
                <h3>Execution Time</h3>
                <div class="metric">
                    <span class="metric-label">Page Load:</span>
                    <span class="metric-value"><?php echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3); ?>s</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Max Execution:</span>
                    <span class="metric-value"><?php echo ini_get('max_execution_time'); ?>s</span>
                </div>
            </div>
            
            <div class="performance-card">
                <h3>Database</h3>
                <div class="metric">
                    <span class="metric-label">Queries:</span>
                    <span class="metric-value"><?php echo get_num_queries(); ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Query Time:</span>
                    <span class="metric-value"><?php echo round(timer_stop(), 3); ?>s</span>
                </div>
            </div>
        </div>
        
        <div class="performance-actions">
            <h3>Performance Testing</h3>
            <div class="button-group">
                <button class="button" onclick="runPerformanceTest()">Run Performance Test</button>
                <button class="button" onclick="clearPerformanceData()">Clear Performance Data</button>
                <button class="button" onclick="exportPerformanceReport()">Export Report</button>
            </div>
        </div>
        <?php
    }
    
    private function renderSystemTab() {
        ?>
        <div class="system-info">
            <h3>WordPress Information</h3>
            <table class="widefat">
                <tr>
                    <td><strong>WordPress Version:</strong></td>
                    <td><?php echo get_bloginfo('version'); ?></td>
                </tr>
                <tr>
                    <td><strong>PHP Version:</strong></td>
                    <td><?php echo PHP_VERSION; ?></td>
                </tr>
                <tr>
                    <td><strong>MySQL Version:</strong></td>
                    <td><?php echo $this->getMySQLVersion(); ?></td>
                </tr>
                <tr>
                    <td><strong>Server Software:</strong></td>
                    <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                </tr>
                <tr>
                    <td><strong>Document Root:</strong></td>
                    <td><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></td>
                </tr>
            </table>
            
            <h3>Plugin Information</h3>
            <table class="widefat">
                <tr>
                    <td><strong>Active Plugins:</strong></td>
                    <td><?php echo count(get_option('active_plugins')); ?></td>
                </tr>
                <tr>
                    <td><strong>Must-Use Plugins:</strong></td>
                    <td><?php echo count(get_mu_plugins()); ?></td>
                </tr>
                <tr>
                    <td><strong>Theme:</strong></td>
                    <td><?php echo wp_get_theme()->get('Name'); ?></td>
                </tr>
            </table>
            
            <h3>Debug System Information</h3>
            <table class="widefat">
                <tr>
                    <td><strong>Debug System Version:</strong></td>
                    <td>1.0.0</td>
                </tr>
                <tr>
                    <td><strong>Log File Path:</strong></td>
                    <td><?php echo $this->debug_system->getLogFilePath(); ?></td>
                </tr>
                <tr>
                    <td><strong>Log File Size:</strong></td>
                    <td><?php echo $this->formatBytes($this->debug_system->getLogFileSize()); ?></td>
                </tr>
                <tr>
                    <td><strong>Configuration:</strong></td>
                    <td><?php echo $this->debug_system->getConfig()->get('enabled') ? 'Enabled' : 'Disabled'; ?></td>
                </tr>
            </table>
        </div>
        <?php
    }
    
    public function handleFormSubmission() {
        if (!isset($_POST['action']) || $_POST['action'] !== 'update_debug_config') {
            return;
        }
        
        if (!wp_verify_nonce($_POST['debug_nonce'], 'blackcnote_debug_config')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $config = $this->debug_system->getConfig();
        
        // Update configuration
        $config->set('enabled', isset($_POST['enabled']));
        $config->set('log_level', sanitize_text_field($_POST['log_level']));
        $config->set('buffer_size', intval($_POST['buffer_size']));
        $config->set('max_log_size', intval($_POST['max_log_size']));
        $config->set('log_rotation', isset($_POST['log_rotation']));
        $config->set('react_debugging', isset($_POST['react_debugging']));
        $config->set('theme_debugging', isset($_POST['theme_debugging']));
        $config->set('performance_monitoring', isset($_POST['performance_monitoring']));
        $config->set('security_monitoring', isset($_POST['security_monitoring']));
        
        // Update debug system settings
        $this->debug_system->setDebugEnabled($config->get('enabled'));
        $this->debug_system->setLogLevel($config->get('log_level'));
        
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success"><p>Debug configuration updated successfully.</p></div>';
        });
    }
    
    public function handleAjaxAction() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $action = $_POST['debug_action'] ?? '';
        
        switch ($action) {
            case 'toggle_debug':
                $enabled = $_POST['enabled'] === 'true';
                $this->debug_system->setDebugEnabled($enabled);
                wp_send_json_success(['enabled' => $enabled]);
                break;
                
            case 'clear_log':
                $this->debug_system->clearLog();
                wp_send_json_success('Log cleared successfully');
                break;
                
            case 'rotate_log':
                $this->debug_system->checkLogRotation();
                wp_send_json_success('Log rotated successfully');
                break;
                
            case 'test_logging':
                blackcnote_opt_log('Test log entry from admin interface', 'INFO', [
                    'source' => 'admin_interface',
                    'timestamp' => current_time('mysql')
                ]);
                wp_send_json_success('Test log entry created');
                break;
                
            case 'get_log_content':
                $log_file = $this->debug_system->getLogFilePath();
                $content = file_exists($log_file) ? file_get_contents($log_file) : '';
                wp_send_json_success(['content' => $content]);
                break;
                
            default:
                wp_send_json_error('Unknown action');
        }
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function getMySQLVersion() {
        global $wpdb;
        return $wpdb->db_version();
    }
}

// Initialize the admin interface
BlackCnoteDebugAdminInterface::getInstance();
?> 