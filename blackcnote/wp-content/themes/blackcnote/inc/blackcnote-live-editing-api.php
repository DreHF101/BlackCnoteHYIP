<?php
/**
 * BlackCnote Live Editing REST API
 * Comprehensive real-time synchronization between WordPress, React, and GitHub
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Live Editing REST API Controller
 */
class BlackCnote_Live_Editing_API {
    
    private $namespace = 'blackcnote/v1';
    private $debug_system;
    private $request_count = 0;
    private $max_requests = 10; // Prevent infinite loops
    
    public function __construct() {
        // Initialize debug system if available
        if (class_exists('BlackCnoteDebugSystemCore')) {
            $this->debug_system = new BlackCnoteDebugSystemCore([
                'base_path' => dirname(dirname(dirname(dirname(__FILE__)))),
                'log_file' => dirname(dirname(dirname(dirname(__FILE__)))) . '/logs/blackcnote-live-editing.log'
            ]);
        } else {
            // Fallback: simple logging without debug system
            $this->debug_system = null;
        }
        
        // Add rate limiting to prevent server overload
        add_action('init', [$this, 'check_rate_limit']);
        
        add_action('rest_api_init', [$this, 'register_routes']);
        add_action('wp_ajax_blackcnote_live_edit', [$this, 'handle_ajax_live_edit']);
        add_action('wp_ajax_nopriv_blackcnote_live_edit', [$this, 'handle_ajax_live_edit']);
    }
    
    /**
     * Check rate limiting to prevent server overload
     */
    public function check_rate_limit() {
        $this->request_count++;
        
        // If too many requests, disable live editing temporarily
        if ($this->request_count > $this->max_requests) {
            $this->log('Rate limit exceeded, disabling live editing temporarily', 'WARNING');
            return false;
        }
        
        return true;
    }
    
    /**
     * Safe logging method that handles null debug system
     */
    private function log($message, $level = 'INFO', $context = []) {
        if ($this->debug_system) {
            $this->debug_system->log($message, $level, $context);
        } else {
            // Simple fallback logging
            error_log("BlackCnote Live Editing API [{$level}]: {$message}");
        }
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Check if rate limit is exceeded
        if (!$this->check_rate_limit()) {
            return;
        }
        
        // Content Management
        register_rest_route($this->namespace, '/content/(?P<id>[a-zA-Z0-9_-]+)', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_content'],
                'permission_callback' => [$this, 'check_permissions'],
                'args' => [
                    'id' => [
                        'validate_callback' => 'sanitize_text_field',
                        'required' => true
                    ]
                ]
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update_content'],
                'permission_callback' => [$this, 'check_permissions'],
                'args' => [
                    'id' => [
                        'validate_callback' => 'sanitize_text_field',
                        'required' => true
                    ],
                    'content' => [
                        'validate_callback' => 'wp_kses_post',
                        'required' => true
                    ],
                    'type' => [
                        'validate_callback' => 'sanitize_text_field',
                        'default' => 'content'
                    ]
                ]
            ]
        ]);
        
        // Style Management
        register_rest_route($this->namespace, '/styles', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_styles'],
                'permission_callback' => [$this, 'check_permissions']
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update_styles'],
                'permission_callback' => [$this, 'check_permissions'],
                'args' => [
                    'styles' => [
                        'validate_callback' => [$this, 'validate_styles'],
                        'required' => true
                    ]
                ]
            ]
        ]);
        
        // Component Management
        register_rest_route($this->namespace, '/components', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_components'],
                'permission_callback' => [$this, 'check_permissions']
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update_component'],
                'permission_callback' => [$this, 'check_permissions'],
                'args' => [
                    'name' => [
                        'validate_callback' => 'sanitize_text_field',
                        'required' => true
                    ],
                    'data' => [
                        'validate_callback' => [$this, 'validate_component_data'],
                        'required' => true
                    ]
                ]
            ]
        ]);
        
        // Git Operations
        register_rest_route($this->namespace, '/github/status', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_git_status'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        register_rest_route($this->namespace, '/github/commit', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'git_commit'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'message' => [
                    'validate_callback' => 'sanitize_text_field',
                    'required' => true
                ]
            ]
        ]);
        
        register_rest_route($this->namespace, '/github/push', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'git_push'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        register_rest_route($this->namespace, '/github/sync', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'git_sync'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'message' => [
                    'validate_callback' => 'sanitize_text_field',
                    'required' => true
                ]
            ]
        ]);
        
        // Development Operations
        register_rest_route($this->namespace, '/dev/clear-cache', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'clear_cache'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        register_rest_route($this->namespace, '/dev/restart-services', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'restart_services'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        register_rest_route($this->namespace, '/dev/build-react', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'build_react'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        register_rest_route($this->namespace, '/dev/docker-status', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_docker_status'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        // File Watching
        register_rest_route($this->namespace, '/files/changes', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_file_changes'],
            'permission_callback' => [$this, 'check_permissions']
        ]);
        
        // Health Check
        register_rest_route($this->namespace, '/health', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_health'],
            'permission_callback' => '__return_true'
        ]);
    }
    
    /**
     * Check permissions for API access
     */
    public function check_permissions($request) {
        // Allow in development mode
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return true;
        }
        
        // Check for valid nonce
        $nonce = $request->get_header('X-WP-Nonce');
        if (!$nonce || !wp_verify_nonce($nonce, 'wp_rest')) {
            return false;
        }
        
        // Check user capabilities
        return current_user_can('edit_posts');
    }
    
    /**
     * Get content by ID
     */
    public function get_content($request) {
        $id = $request->get_param('id');
        
        // Try to get from options first
        $content = get_option("blackcnote_content_{$id}");
        
        if (!$content) {
            // Try to get from post
            $post = get_page_by_path($id);
            if ($post) {
                $content = [
                    'id' => $id,
                    'title' => $post->post_title,
                    'content' => $post->post_content,
                    'type' => 'post',
                    'modified' => $post->post_modified
                ];
            } else {
                return new WP_Error('not_found', 'Content not found', ['status' => 404]);
            }
        }
        
        $this->log("Content retrieved: {$id}", 'INFO');
        
        return rest_ensure_response($content);
    }
    
    /**
     * Update content
     */
    public function update_content($request) {
        $id = $request->get_param('id');
        $content = $request->get_param('content');
        $type = $request->get_param('type');
        
        // Update in options
        $content_data = [
            'id' => $id,
            'content' => $content,
            'type' => $type,
            'modified' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ];
        
        update_option("blackcnote_content_{$id}", $content_data);
        
        // If it's a post, update the post
        $post = get_page_by_path($id);
        if ($post) {
            wp_update_post([
                'ID' => $post->ID,
                'post_content' => $content
            ]);
        }
        
        // Trigger file change event
        do_action('blackcnote_content_updated', $id, $content_data);
        
        $this->log("Content updated: {$id}", 'INFO', $content_data);
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Content updated successfully',
            'data' => $content_data
        ]);
    }
    
    /**
     * Get styles
     */
    public function get_styles($request) {
        $styles = get_option('blackcnote_live_styles', []);
        
        return rest_ensure_response($styles);
    }
    
    /**
     * Update styles
     */
    public function update_styles($request) {
        $styles = $request->get_param('styles');
        
        // Validate and sanitize styles
        $sanitized_styles = [];
        foreach ($styles as $property => $value) {
            $sanitized_styles[sanitize_text_field($property)] = sanitize_text_field($value);
        }
        
        update_option('blackcnote_live_styles', $sanitized_styles);
        
        // Generate CSS custom properties
        $css = ':root {';
        foreach ($sanitized_styles as $property => $value) {
            $css .= "\n  --{$property}: {$value};";
        }
        $css .= "\n}";
        
        // Write to theme CSS file
        $theme_dir = get_template_directory();
        $css_file = $theme_dir . '/assets/css/live-styles.css';
        
        if (!is_dir(dirname($css_file))) {
            wp_mkdir_p(dirname($css_file));
        }
        
        file_put_contents($css_file, $css);
        
        $this->log("Styles updated", 'INFO', $sanitized_styles);
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Styles updated successfully',
            'data' => $sanitized_styles
        ]);
    }
    
    /**
     * Get components
     */
    public function get_components($request) {
        $components = get_option('blackcnote_live_components', []);
        
        return rest_ensure_response($components);
    }
    
    /**
     * Update component
     */
    public function update_component($request) {
        $name = $request->get_param('name');
        $data = $request->get_param('data');
        
        $components = get_option('blackcnote_live_components', []);
        $components[$name] = [
            'name' => $name,
            'data' => $data,
            'modified' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ];
        
        update_option('blackcnote_live_components', $components);
        
        $this->log("Component updated: {$name}", 'INFO', $data);
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Component updated successfully',
            'data' => $components[$name]
        ]);
    }
    
    /**
     * Get Git status
     */
    public function get_git_status($request) {
        $project_root = dirname(dirname(dirname(dirname(__FILE__))));
        
        if (!is_dir($project_root . '/.git')) {
            return rest_ensure_response([
                'is_repo' => false,
                'message' => 'Not a Git repository'
            ]);
        }
        
        $status = [];
        
        // Get current branch
        $branch = trim(shell_exec("cd {$project_root} && git branch --show-current 2>/dev/null"));
        $status['branch'] = $branch ?: 'unknown';
        
        // Get status
        $git_status = shell_exec("cd {$project_root} && git status --porcelain 2>/dev/null");
        $status['has_changes'] = !empty($git_status);
        $status['changes'] = $git_status ? explode("\n", trim($git_status)) : [];
        
        // Get last commit
        $last_commit = shell_exec("cd {$project_root} && git log -1 --oneline 2>/dev/null");
        $status['last_commit'] = trim($last_commit);
        
        $status['is_repo'] = true;
        
        return rest_ensure_response($status);
    }
    
    /**
     * Git commit
     */
    public function git_commit($request) {
        $message = $request->get_param('message');
        $project_root = dirname(dirname(dirname(dirname(__FILE__))));
        
        $output = shell_exec("cd {$project_root} && git add . && git commit -m " . escapeshellarg($message) . " 2>&1");
        
        $this->log("Git commit: {$message}", 'INFO', ['output' => $output]);
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Changes committed successfully',
            'output' => $output
        ]);
    }
    
    /**
     * Git push
     */
    public function git_push($request) {
        $project_root = dirname(dirname(dirname(dirname(__FILE__))));
        
        $output = shell_exec("cd {$project_root} && git push 2>&1");
        
        $this->log("Git push completed", 'INFO', ['output' => $output]);
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Changes pushed successfully',
            'output' => $output
        ]);
    }
    
    /**
     * Git sync (commit + push)
     */
    public function git_sync($request) {
        $message = $request->get_param('message');
        $project_root = dirname(dirname(dirname(dirname(__FILE__))));
        
        $commit_output = shell_exec("cd {$project_root} && git add . && git commit -m " . escapeshellarg($message) . " 2>&1");
        $push_output = shell_exec("cd {$project_root} && git push 2>&1");
        
        $this->log("Git sync: {$message}", 'INFO', [
            'commit_output' => $commit_output,
            'push_output' => $push_output
        ]);
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Changes synced successfully',
            'commit_output' => $commit_output,
            'push_output' => $push_output
        ]);
    }
    
    /**
     * Clear cache
     */
    public function clear_cache($request) {
        // Clear WordPress cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear transients
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");
        
        // Clear theme cache
        $theme_dir = get_template_directory();
        $cache_dir = $theme_dir . '/cache';
        if (is_dir($cache_dir)) {
            $this->delete_directory($cache_dir);
        }
        
        $this->log("Cache cleared", 'INFO');
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }
    
    /**
     * Restart services
     */
    public function restart_services($request) {
        $project_root = dirname(dirname(dirname(dirname(__FILE__))));
        
        // Restart Docker services
        $output = shell_exec("cd {$project_root} && docker-compose restart 2>&1");
        
        $this->log("Services restarted", 'INFO', ['output' => $output]);
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Services restarted successfully',
            'output' => $output
        ]);
    }
    
    /**
     * Build React app
     */
    public function build_react($request) {
        $project_root = dirname(dirname(dirname(dirname(__FILE__))));
        $react_app_dir = $project_root . '/react-app';
        
        if (!is_dir($react_app_dir)) {
            return new WP_Error('not_found', 'React app directory not found', ['status' => 404]);
        }
        
        $output = shell_exec("cd {$react_app_dir} && npm run build 2>&1");
        
        $this->log("React build completed", 'INFO', ['output' => $output]);
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'React app built successfully',
            'output' => $output
        ]);
    }
    
    /**
     * Get Docker status
     */
    public function get_docker_status($request) {
        $project_root = dirname(dirname(dirname(dirname(__FILE__))));
        
        $output = shell_exec("cd {$project_root} && docker-compose ps 2>&1");
        
        return rest_ensure_response([
            'success' => true,
            'output' => $output
        ]);
    }
    
    /**
     * Get file changes
     */
    public function get_file_changes($request) {
        $changes = get_option('blackcnote_file_changes', []);
        
        return rest_ensure_response($changes);
    }
    
    /**
     * Get health status
     */
    public function get_health($request) {
        $services = [
            'wordpress' => [
                'url' => 'http://localhost:8888',
                'status' => $this->check_service('http://localhost:8888')
            ],
            'react' => [
                'url' => 'http://localhost:5174',
                'status' => $this->check_service('http://localhost:5174')
            ],
            'browsersync' => [
                'url' => 'http://localhost:3000',
                'status' => $this->check_service('http://localhost:3000')
            ],
            'phpmyadmin' => [
                'url' => 'http://localhost:8080',
                'status' => $this->check_service('http://localhost:8080')
            ],
            'mailhog' => [
                'url' => 'http://localhost:8025',
                'status' => $this->check_service('http://localhost:8025')
            ]
        ];
        
        return rest_ensure_response([
            'status' => 'healthy',
            'services' => $services,
            'timestamp' => current_time('mysql')
        ]);
    }
    
    /**
     * Handle AJAX live edit requests
     */
    public function handle_ajax_live_edit() {
        check_ajax_referer('blackcnote_live_edit', 'nonce');
        
        $action = sanitize_text_field($_POST['action_type']);
        $data = $_POST['data'] ?? [];
        
        switch ($action) {
            case 'update_content':
                $result = $this->update_content_ajax($data);
                break;
            case 'update_styles':
                $result = $this->update_styles_ajax($data);
                break;
            case 'update_component':
                $result = $this->update_component_ajax($data);
                break;
            default:
                $result = ['success' => false, 'message' => 'Invalid action'];
        }
        
        wp_send_json($result);
    }
    
    /**
     * Update content via AJAX
     */
    private function update_content_ajax($data) {
        $id = sanitize_text_field($data['id']);
        $content = wp_kses_post($data['content']);
        
        $content_data = [
            'id' => $id,
            'content' => $content,
            'type' => 'content',
            'modified' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ];
        
        update_option("blackcnote_content_{$id}", $content_data);
        
        do_action('blackcnote_content_updated', $id, $content_data);
        
        return ['success' => true, 'message' => 'Content updated successfully'];
    }
    
    /**
     * Update styles via AJAX
     */
    private function update_styles_ajax($data) {
        $styles = $data['styles'] ?? [];
        
        $sanitized_styles = [];
        foreach ($styles as $property => $value) {
            $sanitized_styles[sanitize_text_field($property)] = sanitize_text_field($value);
        }
        
        update_option('blackcnote_live_styles', $sanitized_styles);
        
        return ['success' => true, 'message' => 'Styles updated successfully'];
    }
    
    /**
     * Update component via AJAX
     */
    private function update_component_ajax($data) {
        $name = sanitize_text_field($data['name']);
        $component_data = $data['data'] ?? [];
        
        $components = get_option('blackcnote_live_components', []);
        $components[$name] = [
            'name' => $name,
            'data' => $component_data,
            'modified' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ];
        
        update_option('blackcnote_live_components', $components);
        
        return ['success' => true, 'message' => 'Component updated successfully'];
    }
    
    /**
     * Validate styles
     */
    public function validate_styles($styles) {
        if (!is_array($styles)) {
            return false;
        }
        
        foreach ($styles as $property => $value) {
            if (!is_string($property) || !is_string($value)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate component data
     */
    public function validate_component_data($data) {
        return is_array($data);
    }
    
    /**
     * Check if service is accessible
     */
    private function check_service($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $http_code >= 200 && $http_code < 400;
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
        
        rmdir($dir);
    }
}

// Initialize the API
new BlackCnote_Live_Editing_API(); 