<?php
/**
 * BlackCnote Performance Optimizer
 * 
 * Optimizes WordPress performance for the BlackCnote theme
 * Reduces server load and improves response times
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BlackCnote_Performance_Optimizer {
    
    public function __construct() {
        add_action('init', [$this, 'init_performance_optimizations']);
        add_action('wp_enqueue_scripts', [$this, 'optimize_scripts'], 999);
        add_action('wp_head', [$this, 'add_performance_headers'], 1);
        add_filter('wp_headers', [$this, 'add_cache_headers']);
        add_action('wp_footer', [$this, 'add_performance_metrics']);
    }
    
    /**
     * Initialize performance optimizations
     */
    public function init_performance_optimizations() {
        // Disable unnecessary WordPress features
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        
        // Disable emoji scripts
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        
        // Optimize database queries
        add_filter('posts_where', [$this, 'optimize_posts_where']);
        add_filter('posts_orderby', [$this, 'optimize_posts_orderby']);
        
        // Enable object caching if available
        if (function_exists('wp_cache_add')) {
            add_action('wp_footer', [$this, 'cache_page_content']);
        }
    }
    
    /**
     * Optimize script loading
     */
    public function optimize_scripts() {
        // Defer non-critical JavaScript
        add_filter('script_loader_tag', [$this, 'defer_parsing_of_js'], 10, 3);
        
        // Remove unnecessary scripts
        if (!is_admin()) {
            wp_deregister_script('wp-embed');
        }
    }
    
    /**
     * Add performance headers
     */
    public function add_performance_headers() {
        if (!is_admin()) {
            // Add resource hints for faster loading
            echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
            echo '<link rel="dns-prefetch" href="//ajax.googleapis.com">' . "\n";
            echo '<link rel="preconnect" href="//fonts.googleapis.com">' . "\n";
            echo '<link rel="preconnect" href="//fonts.gstatic.com" crossorigin>' . "\n";
        }
    }
    
    /**
     * Add cache headers
     */
    public function add_cache_headers($headers) {
        if (!is_admin() && !is_user_logged_in()) {
            $headers['Cache-Control'] = 'public, max-age=3600';
            $headers['Expires'] = gmdate('D, d M Y H:i:s \G\M\T', time() + 3600);
        }
        return $headers;
    }
    
    /**
     * Defer JavaScript parsing
     */
    public function defer_parsing_of_js($tag, $handle, $src) {
        if (is_admin()) {
            return $tag;
        }
        
        // List of scripts to defer
        $defer_scripts = ['jquery', 'blackcnote-main', 'blackcnote-theme'];
        
        if (in_array($handle, $defer_scripts)) {
            return str_replace(' src', ' defer src', $tag);
        }
        
        return $tag;
    }
    
    /**
     * Optimize database queries
     */
    public function optimize_posts_where($where) {
        // Remove the problematic index hint that was causing SQL syntax errors
        // The USE INDEX hint was malformed and causing database errors
        // We'll rely on MySQL's query optimizer instead
        
        // Only perform safe optimizations
        if (!is_admin() && strpos($where, 'wp_posts') !== false) {
            // Remove any existing malformed index hints
            $where = str_replace('wp_posts USE INDEX (type_status_date)', 'wp_posts', $where);
            
            // Add safe optimizations only
            if (strpos($where, 'post_type') !== false && strpos($where, 'post_status') !== false) {
                // Let MySQL's query optimizer handle index selection
                // This is safer than forcing specific indexes
            }
        }
        return $where;
    }
    
    /**
     * Optimize ORDER BY clauses
     */
    public function optimize_posts_orderby($orderby) {
        // Only optimize if not in admin and not already optimized
        if (!is_admin() && strpos($orderby, 'wp_posts.post_date') !== false) {
            // Remove duplicate DESC if present
            $orderby = str_replace('wp_posts.post_date DESC DESC', 'wp_posts.post_date DESC', $orderby);
            // Add DESC if not present
            if (strpos($orderby, 'wp_posts.post_date DESC') === false) {
                $orderby = str_replace('wp_posts.post_date', 'wp_posts.post_date DESC', $orderby);
            }
        }
        return $orderby;
    }
    
    /**
     * Cache page content
     */
    public function cache_page_content() {
        if (!is_user_logged_in() && !is_admin()) {
            $cache_key = 'page_content_' . md5($_SERVER['REQUEST_URI']);
            $cached_content = wp_cache_get($cache_key, 'blackcnote');
            
            if ($cached_content === false) {
                ob_start();
                // Capture the page content
                wp_cache_set($cache_key, ob_get_contents(), 'blackcnote', 3600);
                ob_end_flush();
            } else {
                echo $cached_content;
            }
        }
    }
    
    /**
     * Add performance metrics
     */
    public function add_performance_metrics() {
        if (current_user_can('manage_options')) {
            $memory_usage = memory_get_usage(true);
            $peak_memory = memory_get_peak_usage(true);
            $load_time = timer_stop();
            
            echo '<!-- Performance Metrics: ';
            echo 'Memory: ' . round($memory_usage / 1024 / 1024, 2) . 'MB, ';
            echo 'Peak: ' . round($peak_memory / 1024 / 1024, 2) . 'MB, ';
            echo 'Load Time: ' . round($load_time, 3) . 's';
            echo ' -->' . "\n";
        }
    }
    
    /**
     * Get performance statistics
     */
    public static function get_performance_stats() {
        global $wpdb;
        
        $stats = [
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'load_time' => timer_stop(),
            'database_queries' => get_num_queries(),
            'cache_hits' => wp_cache_get('cache_hits', 'blackcnote') ?: 0,
            'cache_misses' => wp_cache_get('cache_misses', 'blackcnote') ?: 0
        ];
        
        return $stats;
    }
}

// Initialize the performance optimizer
new BlackCnote_Performance_Optimizer(); 