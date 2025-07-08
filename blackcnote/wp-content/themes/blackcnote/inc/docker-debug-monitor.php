<?php
/**
 * BlackCnote Docker Debug Monitor
 * 
 * Docker-aware debug monitoring system for BlackCnote
 * Detects Docker services and provides accurate status information
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BlackCnote_Docker_Debug_Monitor {
    
    private $docker_services = [
        'wordpress' => [
            'url' => 'http://localhost:8888',
            'port' => 8888,
            'container' => 'blackcnote-wordpress',
            'required' => true
        ],
        'mysql' => [
            'url' => 'mysql://localhost:3306',
            'port' => 3306,
            'container' => 'blackcnote-mysql',
            'required' => true
        ],
        'redis' => [
            'url' => 'redis://localhost:6379',
            'port' => 6379,
            'container' => 'blackcnote-redis',
            'required' => true
        ],
        'phpmyadmin' => [
            'url' => 'http://localhost:8080',
            'port' => 8080,
            'container' => 'blackcnote-phpmyadmin',
            'required' => false
        ],
        'react' => [
            'url' => 'http://localhost:5174',
            'port' => 5174,
            'container' => 'blackcnote-react',
            'required' => false
        ],
        'mailhog' => [
            'url' => 'http://localhost:8025',
            'port' => 8025,
            'container' => 'blackcnote-mailhog',
            'required' => false
        ],
        'dev_tools' => [
            'url' => 'http://localhost:9229',
            'port' => 9229,
            'container' => 'blackcnote-dev-tools',
            'required' => false
        ]
    ];
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_debug_menu']);
        add_action('wp_ajax_check_docker_services', [$this, 'check_docker_services']);
        add_action('wp_ajax_nopriv_check_docker_services', [$this, 'check_docker_services']);
    }
    
    /**
     * Add debug menu to WordPress admin
     */
    public function add_debug_menu() {
        add_submenu_page(
            'tools.php',
            'Docker Debug Monitor',
            'Docker Debug',
            'manage_options',
            'blackcnote-docker-debug',
            [$this, 'render_debug_page']
        );
    }
    
    /**
     * Render debug monitor page
     */
    public function render_debug_page() {
        $services_status = $this->check_all_services();
        ?>
        <div class="wrap">
            <h1>🐳 BlackCnote Docker Debug Monitor</h1>
            
            <div class="notice notice-info">
                <p><strong>Environment:</strong> Docker (Not XAMPP)</p>
                <p><strong>Project Root:</strong> <?php echo esc_html(ABSPATH); ?></p>
                <p><strong>Theme Directory:</strong> <?php echo esc_html(get_template_directory()); ?></p>
            </div>
            
            <div class="card">
                <h2>📊 Service Status</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>URL</th>
                            <th>Port</th>
                            <th>Status</th>
                            <th>Response Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services_status as $service => $status): ?>
                        <tr>
                            <td><strong><?php echo esc_html(ucfirst($service)); ?></strong></td>
                            <td><code><?php echo esc_html($status['url']); ?></code></td>
                            <td><?php echo esc_html($status['port']); ?></td>
                            <td>
                                <?php if ($status['status'] === 'running'): ?>
                                    <span class="dashicons dashicons-yes-alt" style="color: green;"></span> Running
                                <?php elseif ($status['status'] === 'error'): ?>
                                    <span class="dashicons dashicons-dismiss" style="color: red;"></span> Error
                                <?php else: ?>
                                    <span class="dashicons dashicons-warning" style="color: orange;"></span> Unknown
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($status['response_time']); ?>ms</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card">
                <h2>🔧 System Information</h2>
                <table class="form-table">
                    <tr>
                        <th>WordPress Version</th>
                        <td><?php echo esc_html(get_bloginfo('version')); ?></td>
                    </tr>
                    <tr>
                        <th>PHP Version</th>
                        <td><?php echo esc_html(PHP_VERSION); ?></td>
                    </tr>
                    <tr>
                        <th>MySQL Version</th>
                        <td><?php echo esc_html($this->get_mysql_version()); ?></td>
                    </tr>
                    <tr>
                        <th>Memory Limit</th>
                        <td><?php echo esc_html(WP_MEMORY_LIMIT); ?></td>
                    </tr>
                    <tr>
                        <th>Max Memory Limit</th>
                        <td><?php echo esc_html(WP_MAX_MEMORY_LIMIT); ?></td>
                    </tr>
                    <tr>
                        <th>Debug Mode</th>
                        <td><?php echo WP_DEBUG ? 'Enabled' : 'Disabled'; ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="card">
                <h2>🚨 Issues & Recommendations</h2>
                <?php $issues = $this->get_issues_and_recommendations($services_status); ?>
                <?php if (empty($issues)): ?>
                    <p class="notice notice-success">✅ No issues detected. All services are running properly.</p>
                <?php else: ?>
                    <?php foreach ($issues as $issue): ?>
                        <div class="notice notice-<?php echo esc_attr($issue['type']); ?>">
                            <p><strong><?php echo esc_html($issue['title']); ?></strong></p>
                            <p><?php echo esc_html($issue['description']); ?></p>
                            <?php if (!empty($issue['solution'])): ?>
                                <p><strong>Solution:</strong> <?php echo esc_html($issue['solution']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h2>🔄 Manual Refresh</h2>
                <button type="button" class="button button-primary" onclick="location.reload();">
                    Refresh Status
                </button>
                <button type="button" class="button" onclick="checkDockerServices();">
                    Check Services
                </button>
            </div>
        </div>
        
        <script>
        function checkDockerServices() {
            jQuery.post(ajaxurl, {
                action: 'check_docker_services',
                nonce: '<?php echo wp_create_nonce('docker_debug_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error checking services: ' + response.data);
                }
            });
        }
        </script>
        <?php
    }
    
    /**
     * Check all Docker services
     */
    public function check_all_services() {
        $status = [];
        
        foreach ($this->docker_services as $service => $config) {
            $start_time = microtime(true);
            $response = $this->check_service($config['url'], $config['port']);
            $end_time = microtime(true);
            
            $status[$service] = [
                'url' => $config['url'],
                'port' => $config['port'],
                'status' => $response['status'],
                'response_time' => round(($end_time - $start_time) * 1000, 2),
                'error' => $response['error'] ?? null
            ];
        }
        
        return $status;
    }
    
    /**
     * Check individual service
     */
    private function check_service($url, $port) {
        // Check if port is accessible
        $connection = @fsockopen('localhost', $port, $errno, $errstr, 5);
        
        if ($connection) {
            fclose($connection);
            
            // For HTTP services, try to get a response
            if (strpos($url, 'http') === 0) {
                $response = wp_remote_get($url, [
                    'timeout' => 5,
                    'sslverify' => false
                ]);
                
                if (is_wp_error($response)) {
                    return [
                        'status' => 'error',
                        'error' => $response->get_error_message()
                    ];
                }
                
                $status_code = wp_remote_retrieve_response_code($response);
                if ($status_code >= 200 && $status_code < 500) {
                    return ['status' => 'running'];
                } else {
                    return [
                        'status' => 'error',
                        'error' => "HTTP $status_code"
                    ];
                }
            }
            
            return ['status' => 'running'];
        }
        
        return [
            'status' => 'error',
            'error' => "Port $port not accessible"
        ];
    }
    
    /**
     * Get MySQL version
     */
    private function get_mysql_version() {
        global $wpdb;
        $version = $wpdb->get_var("SELECT VERSION()");
        return $version ?: 'Unknown';
    }
    
    /**
     * Get issues and recommendations
     */
    private function get_issues_and_recommendations($services_status) {
        $issues = [];
        
        // Check required services
        foreach ($this->docker_services as $service => $config) {
            if ($config['required'] && $services_status[$service]['status'] !== 'running') {
                $issues[] = [
                    'type' => 'error',
                    'title' => ucfirst($service) . ' Service Down',
                    'description' => "Required service $service is not running on port {$config['port']}.",
                    'solution' => "Run: docker-compose up -d $service"
                ];
            }
        }
        
        // Check React service for development
        if ($services_status['react']['status'] !== 'running') {
            $issues[] = [
                'type' => 'warning',
                'title' => 'React Development Server Not Running',
                'description' => 'React development server is not accessible. Live editing may not work.',
                'solution' => 'Run: docker-compose up -d react-app'
            ];
        }
        
        // Check for high response times
        foreach ($services_status as $service => $status) {
            if ($status['response_time'] > 2000) { // 2 seconds
                $issues[] = [
                    'type' => 'warning',
                    'title' => ucfirst($service) . ' Slow Response',
                    'description' => "Service $service is responding slowly ({$status['response_time']}ms).",
                    'solution' => 'Check container resources: docker stats'
                ];
            }
        }
        
        return $issues;
    }
    
    /**
     * AJAX handler for service checking
     */
    public function check_docker_services() {
        check_ajax_referer('docker_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $services_status = $this->check_all_services();
        wp_send_json_success($services_status);
    }
}

// Initialize the Docker debug monitor
new BlackCnote_Docker_Debug_Monitor(); 