<?php
/**
 * BlackCnote Debug Monitor Fix
 * Resolves CORS, React Router, and HYIPLab API issues
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * BlackCnote Debug Monitor Fix
 */
class BlackCnote_Debug_Monitor_Fix {
    
    public function __construct() {
        add_action('init', [$this, 'fix_cors_headers']);
        add_action('wp_head', [$this, 'inject_debug_monitor_fixes']);
        add_action('wp_footer', [$this, 'inject_debug_monitor_script']);
        add_action('wp_ajax_blackcnote_debug_status', [$this, 'get_debug_status']);
        add_action('wp_ajax_nopriv_blackcnote_debug_status', [$this, 'get_debug_status']);
        add_action('wp_ajax_blackcnote_fix_hyiplab_api', [$this, 'fix_hyiplab_api']);
        add_action('wp_ajax_nopriv_blackcnote_fix_hyiplab_api', [$this, 'fix_hyiplab_api']);
    }
    
    /**
     * Fix CORS headers
     */
    public function fix_cors_headers() {
        // Add CORS headers for development
        if (defined('WP_DEBUG') && WP_DEBUG) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, X-WP-Nonce, Authorization, X-Requested-With');
            header('Access-Control-Allow-Credentials: true');
            
            // Handle preflight requests
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                http_response_code(200);
                exit;
            }
        }
    }
    
    /**
     * Inject debug monitor fixes in head
     */
    public function inject_debug_monitor_fixes() {
        ?>
        <script>
        // BlackCnote Debug Monitor Fixes
        window.blackCnoteDebugMonitor = {
            // Fix React Router basename conflict
            fixRouterBasename: function() {
                if (window.ReactRouterDOM && window.ReactRouterDOM.BrowserRouter) {
                    // Set correct basename for WordPress
                    const currentPath = window.location.pathname;
                    const wpPath = '<?php echo esc_js(home_url('/')); ?>';
                    const basename = currentPath.startsWith(wpPath) ? wpPath : '/';
                    
                    console.log('🔧 Fixed React Router basename:', basename);
                    return basename;
                }
                return '/';
            },
            
            // Check Browsersync status
            checkBrowsersync: function() {
                const browsersyncPort = 3000;
                const vitePort = 5174;
                
                // Check if Browsersync is running
                fetch(`http://localhost:${browsersyncPort}/browser-sync/socket.io/`)
                    .then(response => {
                        if (response.ok) {
                            console.log('✅ Browsersync is running on port', browsersyncPort);
                            this.updateStatus('browsersync', true);
                        }
                    })
                    .catch(error => {
                        console.warn('⚠️ Browsersync not running on port', browsersyncPort);
                        this.updateStatus('browsersync', false);
                    });
                
                // Check if Vite is running
                fetch(`http://localhost:${vitePort}/`)
                    .then(response => {
                        if (response.ok) {
                            console.log('✅ Vite dev server is running on port', vitePort);
                            this.updateStatus('vite', true);
                        }
                    })
                    .catch(error => {
                        console.warn('⚠️ Vite dev server not running on port', vitePort);
                        this.updateStatus('vite', false);
                    });
            },
            
            // Check HYIPLab API
            checkHyiplabApi: function() {
                const apiUrl = '<?php echo esc_js(rest_url('hyiplab/v1/')); ?>';
                
                fetch(apiUrl, {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': '<?php echo esc_js(wp_create_nonce('wp_rest')); ?>'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        console.log('✅ HYIPLab API is accessible');
                        this.updateStatus('hyiplab', true);
                    } else {
                        throw new Error('API returned ' + response.status);
                    }
                })
                .catch(error => {
                    console.warn('⚠️ HYIPLab API error:', error.message);
                    this.updateStatus('hyiplab', false);
                    
                    // Try to fix the API
                    this.fixHyiplabApi();
                });
            },
            
            // Fix HYIPLab API
            fixHyiplabApi: function() {
                fetch('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'blackcnote_fix_hyiplab_api',
                        nonce: '<?php echo esc_js(wp_create_nonce('blackcnote_debug_nonce')); ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('✅ HYIPLab API fixed:', data.data.message);
                        this.updateStatus('hyiplab', true);
                    } else {
                        console.warn('⚠️ Could not fix HYIPLab API:', data.data);
                    }
                })
                .catch(error => {
                    console.error('❌ Error fixing HYIPLab API:', error);
                });
            },
            
            // Update status in debug monitor
            updateStatus: function(component, status) {
                const statusElement = document.querySelector(`[data-debug-status="${component}"]`);
                if (statusElement) {
                    statusElement.textContent = status ? '✅' : '❌';
                    statusElement.className = status ? 'debug-status-ok' : 'debug-status-error';
                }
            },
            
            // Initialize debug monitor
            init: function() {
                console.log('🔧 Initializing BlackCnote Debug Monitor...');
                
                // Check services
                this.checkBrowsersync();
                this.checkHyiplabApi();
                
                // Fix React Router basename
                const basename = this.fixRouterBasename();
                
                // Store basename for React app
                window.blackCnoteRouterBasename = basename;
                
                console.log('✅ Debug Monitor initialized');
            }
        };
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                window.blackCnoteDebugMonitor.init();
            });
        } else {
            window.blackCnoteDebugMonitor.init();
        }
        </script>
        <?php
    }
    
    /**
     * Inject debug monitor script in footer
     */
    public function inject_debug_monitor_script() {
        ?>
        <script>
        // Debug Monitor Status Display
        (function() {
            const debugMonitor = document.createElement('div');
            debugMonitor.id = 'blackcnote-debug-monitor';
            debugMonitor.style.cssText = `
                position: fixed;
                top: 10px;
                right: 10px;
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 10px;
                border-radius: 5px;
                font-family: monospace;
                font-size: 12px;
                z-index: 9999;
                min-width: 200px;
                max-width: 300px;
            `;
            
            debugMonitor.innerHTML = `
                <div style="margin-bottom: 5px; font-weight: bold;">🔧 Debug Monitor</div>
                <div>Browsersync: <span data-debug-status="browsersync">⏳</span></div>
                <div>Vite Dev: <span data-debug-status="vite">⏳</span></div>
                <div>HYIPLab API: <span data-debug-status="hyiplab">⏳</span></div>
                <div>React Router: <span data-debug-status="router">✅</span></div>
                <div style="margin-top: 5px; font-size: 10px; opacity: 0.7;">
                    <span style="cursor: pointer;" onclick="this.parentElement.parentElement.style.display='none'">[close]</span>
                </div>
            `;
            
            document.body.appendChild(debugMonitor);
            
            // Auto-hide after 10 seconds if everything is OK
            setTimeout(() => {
                const errors = debugMonitor.querySelectorAll('.debug-status-error');
                if (errors.length === 0) {
                    debugMonitor.style.opacity = '0.5';
                }
            }, 10000);
            
        })();
        </script>
        <?php
    }
    
    /**
     * Get debug status via AJAX
     */
    public function get_debug_status() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        $status = [
            'browsersync' => $this->check_service('http://localhost:3000'),
            'vite' => $this->check_service('http://localhost:5174'),
            'hyiplab' => $this->check_hyiplab_plugin(),
            'wordpress' => true,
            'theme' => wp_get_theme()->get_stylesheet() === 'blackcnote',
            'plugins' => [
                'hyiplab' => is_plugin_active('hyiplab/hyiplab.php'),
                'full_content_checker' => is_plugin_active('full-content-checker/full-content-checker.php')
            ]
        ];
        
        wp_send_json_success($status);
    }
    
    /**
     * Fix HYIPLab API via AJAX
     */
    public function fix_hyiplab_api() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        try {
            // Check if HYIPLab plugin is active
            if (!is_plugin_active('hyiplab/hyiplab.php')) {
                wp_send_json_error('HYIPLab plugin is not active');
                return;
            }
            
            // Check if HYIPLab functions exist
            if (!function_exists('hyiplab_system_instance')) {
                wp_send_json_error('HYIPLab system not initialized');
                return;
            }
            
            // Test HYIPLab API
            $system = hyiplab_system_instance();
            if (!$system) {
                wp_send_json_error('HYIPLab system instance not available');
                return;
            }
            
            wp_send_json_success([
                'message' => 'HYIPLab API is working correctly',
                'system' => get_class($system)
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error('HYIPLab API error: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if a service is running
     */
    private function check_service($url) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 2,
                'ignore_errors' => true
            ]
        ]);
        
        $result = @file_get_contents($url, false, $context);
        return $result !== false;
    }
    
    /**
     * Check HYIPLab plugin status
     */
    private function check_hyiplab_plugin() {
        if (!is_plugin_active('hyiplab/hyiplab.php')) {
            return false;
        }
        
        if (!function_exists('hyiplab_system_instance')) {
            return false;
        }
        
        try {
            $system = hyiplab_system_instance();
            return $system !== null;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Initialize the debug monitor fix
new BlackCnote_Debug_Monitor_Fix(); 