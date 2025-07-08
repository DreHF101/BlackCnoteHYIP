<?php
/**
 * BlackCnote Debug System Admin Class
 * Handles WordPress admin interface for the debug system
 *
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Interface Class
 */
class BlackCnoteDebugAdmin {
    
    private $debug_system;
    private $script_checker;
    
    /**
     * Constructor
     */
    public function __construct($debug_system) {
        $this->debug_system = $debug_system;
        $this->init_hooks();
        $this->load_script_checker();
    }
    
    /**
     * Load script checker integration
     */
    private function load_script_checker() {
        $script_checker_path = 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools\debug\script-checker-integration.php';
        if (file_exists($script_checker_path)) {
            require_once $script_checker_path;
            $this->script_checker = new BlackCnoteScriptChecker();
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_blackcnote_debug_clear_log', [$this, 'ajax_clear_log']);
        add_action('wp_ajax_blackcnote_debug_toggle', [$this, 'ajax_toggle_debug']);
        add_action('wp_ajax_blackcnote_debug_download_log', [$this, 'ajax_download_log']);
        add_action('wp_dashboard_setup', [ $this, 'add_dashboard_widget' ]);
        add_action('admin_enqueue_scripts', [ $this, 'enqueue_dashboard_assets' ]);
        
        // Script checker AJAX handlers
        add_action('wp_ajax_blackcnote_script_check', [$this, 'ajax_script_check']);
        add_action('wp_ajax_blackcnote_get_script_results', [$this, 'ajax_get_script_results']);
        add_action('wp_ajax_blackcnote_get_script_log', [$this, 'ajax_get_script_log']);
        
        // Admin notices for script checker alerts
        add_action('admin_notices', [$this, 'display_script_checker_notices']);
        add_action('wp_ajax_blackcnote_dismiss_script_alert', [$this, 'ajax_dismiss_script_alert']);
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_scripts($hook) {
        // Only load on our pages
        if (strpos($hook, 'blackcnote-debug') === false) {
            return;
        }
        
        wp_enqueue_script(
            'blackcnote-debug-admin',
            BLACKCNOTE_DEBUG_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            BLACKCNOTE_DEBUG_VERSION,
            true
        );
        
        wp_enqueue_script(
            'blackcnote-script-checker-admin',
            'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools\debug\script-checker-admin.js',
            ['jquery'],
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'blackcnote-debug-admin',
            BLACKCNOTE_DEBUG_PLUGIN_URL . 'assets/css/admin.css',
            [],
            BLACKCNOTE_DEBUG_VERSION
        );
        
        // Add inline styles for admin notices
        wp_add_inline_style('blackcnote-debug-admin', $this->get_admin_notice_styles());
        
        // Localize script for AJAX
        wp_localize_script('blackcnote-debug-admin', 'blackcnoteDebug', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('blackcnote_debug_nonce'),
            'strings' => [
                'confirm_clear_log' => __('Are you sure you want to clear the debug log?', 'blackcnote-debug'),
                'confirm_toggle_debug' => __('Are you sure you want to toggle debug mode?', 'blackcnote-debug'),
                'loading' => __('Loading...', 'blackcnote-debug'),
                'error' => __('Error occurred', 'blackcnote-debug'),
                'success' => __('Operation completed successfully', 'blackcnote-debug')
            ]
        ]);
    }
    
    /**
     * Get admin notice styles
     */
    private function get_admin_notice_styles() {
        return '
        .blackcnote-script-alert {
            border-left: 4px solid #dc3232;
            padding: 12px;
            margin: 15px 0;
            background: #fff;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            transition: all 0.2s ease-in-out;
        }
        
        .blackcnote-script-alert.notice-warning {
            border-left-color: #ffb900;
        }
        
        .blackcnote-script-alert:hover {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            transform: translateY(-1px);
        }
        
        .blackcnote-script-alert .notice-content {
            display: flex;
            flex-direction: column;
        }
        
        .blackcnote-script-alert .notice-header {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .blackcnote-script-alert .notice-header .dashicons {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .blackcnote-script-alert .notice-header .notice-time {
            margin-left: auto;
            color: #666;
            font-size: 12px;
        }
        
        .blackcnote-script-alert .notice-body {
            margin-left: 26px;
        }
        
        .blackcnote-script-alert .notice-body p {
            margin: 0 0 12px 0;
            font-size: 13px;
            line-height: 1.4;
        }
        
        .blackcnote-script-alert .notice-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .blackcnote-script-alert .notice-actions .button {
            font-size: 12px;
            padding: 4px 12px;
            height: auto;
            line-height: 1.4;
            transition: all 0.2s ease-in-out;
        }
        
        .blackcnote-script-alert .dismiss-alert {
            background: #f1f1f1;
            border-color: #ddd;
            color: #555;
        }
        
        .blackcnote-script-alert .dismiss-alert:hover {
            background: #e5e5e5;
            border-color: #ccc;
            color: #333;
        }
        
        .blackcnote-script-alert .dismiss-alert.dismissing {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        @media (max-width: 782px) {
            .blackcnote-script-alert .notice-actions {
                flex-direction: column;
            }
            
            .blackcnote-script-alert .notice-actions .button {
                width: 100%;
                text-align: center;
            }
        }
        
        /* Animation for new alerts */
        .blackcnote-script-alert {
            animation: slideInDown 0.3s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        ';
    }
    
    /**
     * Add dashboard widget
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'blackcnote_debug_widget',
            __('BlackCnote Debug System', 'blackcnote-debug'),
            [$this, 'dashboard_widget_content']
        );
    }
    
    /**
     * Dashboard widget content
     */
    public function dashboard_widget_content() {
        if ($this->script_checker) {
            echo $this->script_checker->generateDashboardWidget();
        } else {
            echo '<p>' . __('Script checker not available', 'blackcnote-debug') . '</p>';
        }
    }
    
    /**
     * Enqueue dashboard assets
     */
    public function enqueue_dashboard_assets($hook) {
        if ($hook === 'index.php') {
            wp_enqueue_script(
                'blackcnote-script-checker-admin',
                'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools\debug\script-checker-admin.js',
                ['jquery'],
                '1.0.0',
                true
            );
            
            wp_localize_script('blackcnote-script-checker-admin', 'blackcnoteScriptChecker', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('blackcnote_script_checker_nonce')
            ]);
        }
        
        // Add script for admin notices on all admin pages
        wp_enqueue_script(
            'blackcnote-admin-notices',
            BLACKCNOTE_DEBUG_PLUGIN_URL . 'assets/js/admin-notices.js',
            ['jquery'],
            BLACKCNOTE_DEBUG_VERSION,
            true
        );
        
        wp_localize_script('blackcnote-admin-notices', 'blackcnoteAdminNotices', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('blackcnote_script_checker_nonce'),
            'strings' => [
                'dismissing' => __('Dismissing...', 'blackcnote-debug'),
                'dismissed' => __('Alert dismissed', 'blackcnote-debug'),
                'error' => __('Error dismissing alert', 'blackcnote-debug')
            ]
        ]);
    }
    
    /**
     * AJAX handler for script check
     */
    public function ajax_script_check() {
        check_ajax_referer('blackcnote_script_checker_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        if (!$this->script_checker) {
            wp_send_json_error('Script checker not available');
        }
        
        $fix_emojis = isset($_POST['fix_emojis']) && $_POST['fix_emojis'] === 'true';
        $result = $this->script_checker->runCheck($fix_emojis);
        
        wp_send_json([
            'success' => $result['success'],
            'message' => $result['message'],
            'exitCode' => $result['exitCode']
        ]);
    }
    
    /**
     * AJAX handler for getting script results
     */
    public function ajax_get_script_results() {
        check_ajax_referer('blackcnote_script_checker_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        if (!$this->script_checker) {
            wp_send_json_error('Script checker not available');
        }
        
        $results = $this->script_checker->getResults();
        wp_send_json($results);
    }
    
    /**
     * AJAX handler for getting script log
     */
    public function ajax_get_script_log() {
        check_ajax_referer('blackcnote_script_checker_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        if (!$this->script_checker) {
            wp_send_json_error('Script checker not available');
        }
        
        $log_content = $this->script_checker->getLogContent();
        wp_send_json($log_content);
    }
    
    /**
     * AJAX handler for clearing debug log
     */
    public function ajax_clear_log() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $result = $this->debug_system->clear_log();
        
        if ($result) {
            wp_send_json_success(__('Debug log cleared successfully', 'blackcnote-debug'));
        } else {
            wp_send_json_error(__('Failed to clear debug log', 'blackcnote-debug'));
        }
    }
    
    /**
     * AJAX handler for toggling debug mode
     */
    public function ajax_toggle_debug() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $enabled = $this->debug_system->toggle_debug_mode();
        
        wp_send_json_success([
            'enabled' => $enabled,
            'message' => $enabled ? __('Debug mode enabled', 'blackcnote-debug') : __('Debug mode disabled', 'blackcnote-debug')
        ]);
    }
    
    /**
     * AJAX handler for downloading debug log
     */
    public function ajax_download_log() {
        check_ajax_referer('blackcnote_debug_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $log_content = $this->debug_system->get_log_content();
        
        if ($log_content === false) {
            wp_die(__('No log content available', 'blackcnote-debug'));
        }
        
        $filename = 'blackcnote-debug-' . date('Y-m-d-H-i-s') . '.log';
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($log_content));
        
        echo $log_content;
        exit;
    }
    
    /**
     * Display script checker admin notices
     */
    public function display_script_checker_notices() {
        if (!$this->script_checker) {
            return;
        }
        
        // Check if user has dismissed the alert
        $dismissed_until = get_user_meta(get_current_user_id(), 'blackcnote_script_alert_dismissed_until', true);
        if ($dismissed_until && time() < $dismissed_until) {
            return;
        }
        
        $results = $this->script_checker->getResults();
        
        if (!$results['summary']) {
            return;
        }
        
        $summary = $results['summary'];
        $status = $summary['OverallStatus'] ?? 'unknown';
        
        // Only show notices for ERROR or WARNING status
        if ($status === 'PASS') {
            return;
        }
        
        $error_count = $summary['ErrorFiles'] ?? 0;
        $warning_count = $summary['WarningFiles'] ?? 0;
        $total_issues = $error_count + $warning_count;
        
        // Determine notice type and message
        if ($status === 'ERROR') {
            $notice_class = 'notice-error';
            $icon = 'dashicons-dismiss';
            $title = __('Script Checker Critical Alert', 'blackcnote-debug');
            $message = sprintf(
                __('%d critical errors and %d warnings detected in BlackCnote scripts. This requires immediate attention.', 'blackcnote-debug'),
                $error_count,
                $warning_count
            );
        } else {
            $notice_class = 'notice-warning';
            $icon = 'dashicons-warning';
            $title = __('Script Checker Warning', 'blackcnote-debug');
            $message = sprintf(
                __('%d warnings detected in BlackCnote scripts. Review recommended.', 'blackcnote-debug'),
                $warning_count
            );
        }
        
        // Get the script checker page URL
        $script_page_url = admin_url('admin.php?page=blackcnote-debug-scripts');
        
        // Build the notice HTML
        $notice_html = sprintf(
            '<div class="notice %s is-dismissible blackcnote-script-alert" data-alert-id="script-checker-%s">
                <div class="notice-content">
                    <div class="notice-header">
                        <span class="dashicons %s"></span>
                        <strong>%s</strong>
                        <span class="notice-time">%s</span>
                    </div>
                    <div class="notice-body">
                        <p>%s</p>
                        <div class="notice-actions">
                            <a href="%s" class="button button-primary">%s</a>
                            <button type="button" class="button button-secondary dismiss-alert" data-dismiss-hours="24">%s</button>
                            <button type="button" class="button button-secondary dismiss-alert" data-dismiss-hours="168">%s</button>
                        </div>
                    </div>
                </div>
            </div>',
            esc_attr($notice_class),
            esc_attr($status),
            esc_attr($icon),
            esc_html($title),
            esc_html($summary['Timestamp'] ?? ''),
            esc_html($message),
            esc_url($script_page_url),
            esc_html__('View Details', 'blackcnote-debug'),
            esc_html__('Dismiss for 24 hours', 'blackcnote-debug'),
            esc_html__('Dismiss for 1 week', 'blackcnote-debug')
        );
        
        echo $notice_html;
    }
    
    /**
     * AJAX handler for dismissing script alerts
     */
    public function ajax_dismiss_script_alert() {
        check_ajax_referer('blackcnote_script_checker_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $dismiss_hours = intval($_POST['dismiss_hours'] ?? 24);
        $dismiss_until = time() + ($dismiss_hours * 3600);
        
        update_user_meta(get_current_user_id(), 'blackcnote_script_alert_dismissed_until', $dismiss_until);
        
        wp_send_json_success([
            'message' => sprintf(__('Alert dismissed for %d hours', 'blackcnote-debug'), $dismiss_hours),
            'dismissed_until' => $dismiss_until
        ]);
    }
} 