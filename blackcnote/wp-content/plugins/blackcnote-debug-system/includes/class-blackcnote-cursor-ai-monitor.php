<?php
/**
 * BlackCnote Cursor AI Monitor
 * Ensures all AI-assisted changes follow canonical pathways and project rules
 *
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cursor AI Monitor Class
 * Monitors and validates all AI-assisted changes to ensure compliance with BlackCnote rules
 */
class BlackCnoteCursorAIMonitor {
    
    private $debug_system;
    private $canonical_paths;
    private $cursor_rules;
    private $validation_results = [];
    private $monitoring_enabled = true;
    
    /**
     * Constructor
     */
    public function __construct($debug_system) {
        $this->debug_system = $debug_system;
        $this->initializeCanonicalPaths();
        $this->initializeCursorRules();
        $this->setupHooks();
        
        if ($this->debug_system && method_exists($this->debug_system, 'log')) {
            $this->debug_system->log('BlackCnote Cursor AI Monitor initialized', 'SYSTEM', [
                'canonical_paths' => $this->canonical_paths,
                'rules_count' => count($this->cursor_rules)
            ]);
        } else {
            error_log('[BlackCnote Debug] Cursor AI Monitor initialized, but debug_system is null or missing log() method.');
        }
    }
    
    /**
     * Initialize canonical pathways
     */
    private function initializeCanonicalPaths() {
        $this->canonical_paths = [
            'project_root' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote',
            'blackcnote_theme' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote',
            'wp_content' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote\\wp-content',
            'theme_files' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote\\wp-content\\themes\\blackcnote',
            'plugins' => 'C:\\Users\\CASH AMERICA PAWN\\Desktop\\BlackCnote\\blackcnote\\wp-content\\plugins',
            'github_repo' => 'https://github.com/DreHF101/BlackCnoteHYIP.git',
            'service_urls' => [
                'wordpress' => 'http://localhost:8888',
                'react' => 'http://localhost:5174',
                'phpmyadmin' => 'http://localhost:8080',
                'mailhog' => 'http://localhost:8025',
                'redis_commander' => 'http://localhost:8081',
                'browsersync' => 'http://localhost:3000'
            ]
        ];
    }
    
    /**
     * Initialize Cursor AI rules and regulations
     */
    private function initializeCursorRules() {
        $this->cursor_rules = [
            'path_validation' => [
                'enabled' => true,
                'description' => 'All file operations must use canonical BlackCnote paths',
                'validation_function' => 'validateCanonicalPaths'
            ],
            'wordpress_standards' => [
                'enabled' => true,
                'description' => 'All WordPress code must follow WordPress coding standards',
                'validation_function' => 'validateWordPressStandards'
            ],
            'php_best_practices' => [
                'enabled' => true,
                'description' => 'All PHP code must follow PHP 7.4+ best practices',
                'validation_function' => 'validatePHPBestPractices'
            ],
            'security_validation' => [
                'enabled' => true,
                'description' => 'All code must pass security validation checks',
                'validation_function' => 'validateSecurity'
            ],
            'documentation_requirements' => [
                'enabled' => true,
                'description' => 'All changes must include proper documentation',
                'validation_function' => 'validateDocumentation'
            ],
            'git_integration' => [
                'enabled' => true,
                'description' => 'All changes must be properly committed to Git',
                'validation_function' => 'validateGitIntegration'
            ],
            'docker_compatibility' => [
                'enabled' => true,
                'description' => 'All changes must be compatible with Docker environment',
                'validation_function' => 'validateDockerCompatibility'
            ],
            'react_integration' => [
                'enabled' => true,
                'description' => 'All React changes must maintain integration with WordPress',
                'validation_function' => 'validateReactIntegration'
            ]
        ];
    }
    
    /**
     * Setup WordPress hooks
     */
    private function setupHooks() {
        // Monitor file changes
        add_action('wp_loaded', [$this, 'monitorFileChanges']);
        
        // Monitor database changes
        add_action('wp_insert_post', [$this, 'monitorPostChanges']);
        add_action('wp_update_post', [$this, 'monitorPostChanges']);
        add_action('wp_delete_post', [$this, 'monitorPostChanges']);
        
        // Monitor plugin and theme changes
        add_action('activated_plugin', [$this, 'monitorPluginChanges']);
        add_action('deactivated_plugin', [$this, 'monitorPluginChanges']);
        add_action('switch_theme', [$this, 'monitorThemeChanges']);
        
        // Monitor admin actions
        add_action('admin_init', [$this, 'monitorAdminActions']);
        
        // Monitor AJAX requests
        add_action('wp_ajax_blackcnote_cursor_validation', [$this, 'handleCursorValidation']);
        
        // Add admin menu
        add_action('admin_menu', [$this, 'addAdminMenu']);
    }
    
    /**
     * Validate canonical paths in file operations
     */
    public function validateCanonicalPaths($file_path, $operation = 'read') {
        $validation_result = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'recommendations' => []
        ];
        
        // Check if path uses canonical structure
        $canonical_base = $this->canonical_paths['project_root'];
        $blackcnote_base = $this->canonical_paths['blackcnote_theme'];
        
        if (strpos($file_path, $canonical_base) === false) {
            $validation_result['valid'] = false;
            $validation_result['errors'][] = "File path does not use canonical BlackCnote project root: {$canonical_base}";
        }
        
        // Check for deprecated paths
        $deprecated_paths = [
            'wordpress/wp-content/',
            'wp-content/',
            'wp-admin/',
            'wp-includes/'
        ];
        
        foreach ($deprecated_paths as $deprecated) {
            if (strpos($file_path, $deprecated) !== false) {
                $validation_result['warnings'][] = "File uses deprecated path: {$deprecated}";
                $validation_result['recommendations'][] = "Use canonical path: {$blackcnote_base}/wp-content/";
            }
        }
        
        // Log validation result
        $this->debug_system->log('Cursor AI path validation', 'INFO', [
            'file_path' => $file_path,
            'operation' => $operation,
            'validation_result' => $validation_result
        ]);
        
        return $validation_result;
    }
    
    /**
     * Validate WordPress coding standards
     */
    public function validateWordPressStandards($code, $file_path) {
        $validation_result = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'recommendations' => []
        ];
        
        // Check for WordPress coding standards
        $wordpress_checks = [
            'strict_types' => 'declare(strict_types=1);',
            'abspath_check' => 'if (!defined(\'ABSPATH\'))',
            'wp_functions' => ['wp_enqueue_script', 'wp_enqueue_style', 'add_action', 'add_filter'],
            'security_functions' => ['wp_verify_nonce', 'sanitize_text_field', 'esc_html']
        ];
        
        // Check for strict types declaration
        if (strpos($code, $wordpress_checks['strict_types']) === false) {
            $validation_result['warnings'][] = 'Missing strict types declaration';
            $validation_result['recommendations'][] = 'Add declare(strict_types=1); at the top of PHP files';
        }
        
        // Check for ABSPATH check
        if (strpos($code, $wordpress_checks['abspath_check']) === false) {
            $validation_result['warnings'][] = 'Missing ABSPATH check for security';
            $validation_result['recommendations'][] = 'Add if (!defined(\'ABSPATH\')) { exit; }';
        }
        
        // Check for WordPress functions usage
        $wp_functions_found = 0;
        foreach ($wordpress_checks['wp_functions'] as $function) {
            if (strpos($code, $function) !== false) {
                $wp_functions_found++;
            }
        }
        
        if ($wp_functions_found === 0) {
            $validation_result['warnings'][] = 'No WordPress functions detected';
            $validation_result['recommendations'][] = 'Use WordPress built-in functions when possible';
        }
        
        $this->debug_system->log('Cursor AI WordPress standards validation', 'INFO', [
            'file_path' => $file_path,
            'validation_result' => $validation_result
        ]);
        
        return $validation_result;
    }
    
    /**
     * Validate PHP best practices
     */
    public function validatePHPBestPractices($code, $file_path) {
        $validation_result = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'recommendations' => []
        ];
        
        // Check for PHP 7.4+ features
        $php_features = [
            'typed_properties' => 'private string $',
            'arrow_functions' => 'fn(',
            'null_coalescing' => '??',
            'typed_parameters' => 'function test(string $'
        ];
        
        $features_found = 0;
        foreach ($php_features as $feature => $pattern) {
            if (strpos($code, $pattern) !== false) {
                $features_found++;
            }
        }
        
        if ($features_found === 0) {
            $validation_result['recommendations'][] = 'Consider using PHP 7.4+ features for better code quality';
        }
        
        // Check for OOP practices
        if (strpos($code, 'class ') !== false) {
            $validation_result['valid'] = true;
        } else {
            $validation_result['warnings'][] = 'Consider using object-oriented programming for better modularity';
        }
        
        $this->debug_system->log('Cursor AI PHP best practices validation', 'INFO', [
            'file_path' => $file_path,
            'validation_result' => $validation_result
        ]);
        
        return $validation_result;
    }
    
    /**
     * Validate security practices
     */
    public function validateSecurity($code, $file_path) {
        $validation_result = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'recommendations' => []
        ];
        
        // Security checks
        $security_issues = [
            'sql_injection' => ['mysql_query', 'mysqli_query', 'SELECT * FROM'],
            'xss_vulnerability' => ['echo $_GET', 'echo $_POST', 'echo $_REQUEST'],
            'file_inclusion' => ['include $_GET', 'require $_POST'],
            'command_injection' => ['shell_exec', 'system', 'exec']
        ];
        
        foreach ($security_issues as $issue => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($code, $pattern) !== false) {
                    $validation_result['errors'][] = "Potential {$issue} vulnerability detected";
                    $validation_result['valid'] = false;
                }
            }
        }
        
        // Security best practices
        $security_best_practices = [
            'nonce_verification' => 'wp_verify_nonce',
            'input_sanitization' => 'sanitize_',
            'output_escaping' => 'esc_',
            'prepared_statements' => '$wpdb->prepare'
        ];
        
        $practices_found = 0;
        foreach ($security_best_practices as $practice => $pattern) {
            if (strpos($code, $pattern) !== false) {
                $practices_found++;
            }
        }
        
        if ($practices_found === 0) {
            $validation_result['warnings'][] = 'No security best practices detected';
            $validation_result['recommendations'][] = 'Implement proper input sanitization and output escaping';
        }
        
        $this->debug_system->log('Cursor AI security validation', 'INFO', [
            'file_path' => $file_path,
            'validation_result' => $validation_result
        ]);
        
        return $validation_result;
    }
    
    /**
     * Monitor file changes
     */
    public function monitorFileChanges() {
        $monitored_directories = [
            $this->canonical_paths['theme_files'],
            $this->canonical_paths['plugins'],
            $this->canonical_paths['wp_content']
        ];
        
        foreach ($monitored_directories as $directory) {
            if (is_dir($directory)) {
                $this->scanDirectoryForChanges($directory);
            }
        }
    }
    
    /**
     * Scan directory for changes
     */
    private function scanDirectoryForChanges($directory) {
        $files = glob($directory . '/**/*.php', GLOB_BRACE);
        
        foreach ($files as $file) {
            $file_hash = md5_file($file);
            $stored_hash = get_option('blackcnote_file_hash_' . md5($file), '');
            
            if ($file_hash !== $stored_hash) {
                $this->validateFileChanges($file, $file_hash);
                update_option('blackcnote_file_hash_' . md5($file), $file_hash);
            }
        }
    }
    
    /**
     * Validate file changes
     */
    private function validateFileChanges($file_path, $file_hash) {
        $file_content = file_get_contents($file_path);
        
        // Run all validations
        $validations = [
            'paths' => $this->validateCanonicalPaths($file_path, 'modified'),
            'wordpress' => $this->validateWordPressStandards($file_content, $file_path),
            'php' => $this->validatePHPBestPractices($file_content, $file_path),
            'security' => $this->validateSecurity($file_content, $file_path)
        ];
        
        $this->validation_results[$file_path] = [
            'timestamp' => current_time('mysql'),
            'hash' => $file_hash,
            'validations' => $validations
        ];
        
        $this->debug_system->log('Cursor AI file change validation', 'INFO', [
            'file_path' => $file_path,
            'validations' => $validations
        ]);
    }
    
    /**
     * Handle Cursor AI validation AJAX request
     */
    public function handleCursorValidation() {
        check_ajax_referer('blackcnote_cursor_validation', 'nonce');
        
        $action = sanitize_text_field($_POST['action_type'] ?? '');
        $file_path = sanitize_text_field($_POST['file_path'] ?? '');
        $code_content = wp_kses_post($_POST['code_content'] ?? '');
        
        $validation_result = [];
        
        switch ($action) {
            case 'validate_path':
                $validation_result = $this->validateCanonicalPaths($file_path);
                break;
            case 'validate_wordpress':
                $validation_result = $this->validateWordPressStandards($code_content, $file_path);
                break;
            case 'validate_php':
                $validation_result = $this->validatePHPBestPractices($code_content, $file_path);
                break;
            case 'validate_security':
                $validation_result = $this->validateSecurity($code_content, $file_path);
                break;
            case 'validate_all':
                $validation_result = [
                    'paths' => $this->validateCanonicalPaths($file_path),
                    'wordpress' => $this->validateWordPressStandards($code_content, $file_path),
                    'php' => $this->validatePHPBestPractices($code_content, $file_path),
                    'security' => $this->validateSecurity($code_content, $file_path)
                ];
                break;
        }
        
        wp_send_json_success($validation_result);
    }
    
    /**
     * Add admin menu
     */
    public function addAdminMenu() {
        add_submenu_page(
            'blackcnote-debug',
            'Cursor AI Monitor',
            'Cursor AI Monitor',
            'manage_options',
            'blackcnote-cursor-monitor',
            [$this, 'adminPage']
        );
    }
    
    /**
     * Admin page
     */
    public function adminPage() {
        ?>
        <div class="wrap">
            <h1>BlackCnote Cursor AI Monitor</h1>
            <p>Monitor and validate all AI-assisted changes to ensure compliance with BlackCnote rules.</p>
            
            <h2>Canonical Pathways</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Path Type</th>
                        <th>Canonical Path</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->canonical_paths as $type => $path): ?>
                        <tr>
                            <td><?php echo esc_html(ucfirst(str_replace('_', ' ', $type))); ?></td>
                            <td><code><?php echo esc_html($path); ?></code></td>
                            <td>
                                <?php if (is_dir($path) || filter_var($path, FILTER_VALIDATE_URL)): ?>
                                    <span class="dashicons dashicons-yes-alt" style="color: green;"></span> Valid
                                <?php else: ?>
                                    <span class="dashicons dashicons-warning" style="color: orange;"></span> Check Required
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <h2>Validation Rules</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Rule</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->cursor_rules as $rule => $config): ?>
                        <tr>
                            <td><?php echo esc_html(ucfirst(str_replace('_', ' ', $rule))); ?></td>
                            <td><?php echo esc_html($config['description']); ?></td>
                            <td>
                                <?php if ($config['enabled']): ?>
                                    <span class="dashicons dashicons-yes-alt" style="color: green;"></span> Active
                                <?php else: ?>
                                    <span class="dashicons dashicons-no-alt" style="color: red;"></span> Disabled
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <h2>Recent Validations</h2>
            <div id="blackcnote-cursor-validations">
                <?php $this->displayRecentValidations(); ?>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Auto-refresh validations every 30 seconds
            setInterval(function() {
                $('#blackcnote-cursor-validations').load(window.location.href + ' #blackcnote-cursor-validations');
            }, 30000);
        });
        </script>
        <?php
    }
    
    /**
     * Display recent validations
     */
    private function displayRecentValidations() {
        if (empty($this->validation_results)) {
            echo '<p>No recent validations found.</p>';
            return;
        }
        
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>File</th><th>Timestamp</th><th>Validation Results</th></tr></thead>';
        echo '<tbody>';
        
        foreach (array_slice($this->validation_results, -10) as $file => $result) {
            echo '<tr>';
            echo '<td><code>' . esc_html($file) . '</code></td>';
            echo '<td>' . esc_html($result['timestamp']) . '</td>';
            echo '<td>';
            
            foreach ($result['validations'] as $type => $validation) {
                $status = $validation['valid'] ? 'Valid' : 'Issues Found';
                $color = $validation['valid'] ? 'green' : 'red';
                echo '<span style="color: ' . $color . ';">' . esc_html(ucfirst($type)) . ': ' . esc_html($status) . '</span><br>';
            }
            
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    }
    
    /**
     * Get validation results
     */
    public function getValidationResults() {
        return $this->validation_results;
    }
    
    /**
     * Get canonical paths
     */
    public function getCanonicalPaths() {
        return $this->canonical_paths;
    }
    
    /**
     * Get cursor rules
     */
    public function getCursorRules() {
        return $this->cursor_rules;
    }

    /**
     * Monitor plugin changes
     */
    public function monitorPluginChanges($plugin) {
        if ($this->debug_system && method_exists($this->debug_system, 'log')) {
            $this->debug_system->log('Plugin change detected', 'INFO', [
                'plugin' => $plugin,
                'action' => current_filter()
            ]);
        }
    }

    /**
     * Monitor post changes
     */
    public function monitorPostChanges($post_id) {
        if ($this->debug_system && method_exists($this->debug_system, 'log')) {
            $this->debug_system->log('Post change detected', 'INFO', [
                'post_id' => $post_id,
                'action' => current_filter()
            ]);
        }
    }

    /**
     * Monitor theme changes
     */
    public function monitorThemeChanges($new_theme) {
        if ($this->debug_system && method_exists($this->debug_system, 'log')) {
            $this->debug_system->log('Theme change detected', 'INFO', [
                'new_theme' => $new_theme,
                'action' => current_filter()
            ]);
        }
    }

    /**
     * Monitor admin actions
     */
    public function monitorAdminActions() {
        if ($this->debug_system && method_exists($this->debug_system, 'log')) {
            $this->debug_system->log('Admin action detected', 'INFO', [
                'current_screen' => get_current_screen() ? get_current_screen()->id : 'unknown',
                'action' => current_filter()
            ]);
        }
    }
} 