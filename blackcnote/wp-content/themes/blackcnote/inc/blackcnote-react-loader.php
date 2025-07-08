<?php
declare(strict_types=1);

/**
 * BlackCnote React App Loader
 * Handles React app initialization and WordPress integration
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize React app when DOM is ready
 */
function blackcnote_init_react_app(): void {
    // Only initialize on frontend pages, not admin
    if (is_admin()) {
        return;
    }
    
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if React app container exists
        const rootElement = document.getElementById('root');
        if (rootElement) {
            console.log('BlackCnote: React container found, initializing app...');
            
            // Remove loading message after a short delay
            setTimeout(function() {
                const loadingElement = rootElement.querySelector('.react-loading');
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }
            }, 1000);
            
            // Initialize React app
            if (window.blackCnoteApiSettings) {
                console.log('BlackCnote: API settings found, React app should load');
                console.log('BlackCnote: Development mode:', window.blackCnoteApiSettings.isDevelopment);
                console.log('BlackCnote: Dev server URL:', window.blackCnoteApiSettings.devServerUrl);
            } else {
                console.error('BlackCnote: API settings not found - React app may not load properly');
            }
        } else {
            console.warn('BlackCnote: React container not found');
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'blackcnote_init_react_app', 999);

/**
 * Add React app container to WordPress templates
 */
function blackcnote_add_react_container(): void {
    ?>
    <div id="root" class="blackcnote-react-app">
        <div class="react-loading">
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <p class="loading-text">Loading BlackCnote...</p>
            <div class="loading-status">
                <small>Connecting to React development server...</small>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Automatically output React container on frontend pages only
 */
function blackcnote_output_react_container(): void {
    // Only output on frontend pages, never on admin pages
    if (!is_admin() && (is_front_page() || is_home() || is_page() || is_single() || is_archive())) {
        blackcnote_add_react_container();
    }
}
add_action('wp_head', 'blackcnote_output_react_container', 1);

/**
 * Add React app styles only on frontend pages
 */
function blackcnote_add_react_styles(): void {
    // Only add styles on frontend pages
    if (is_admin()) {
        return;
    }
    
    ?>
    <style>
    .blackcnote-react-app {
        min-height: 100vh;
        width: 100%;
        position: relative;
    }
    
    .react-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        padding: 20px;
    }
    
    .loading-spinner {
        margin-bottom: 20px;
    }
    
    .loading-text {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 10px;
    }
    
    .loading-status {
        opacity: 0.8;
        font-size: 14px;
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
    
    /* Hide WordPress admin bar when React app is loading */
    .blackcnote-react-app .react-loading ~ #wpadminbar {
        display: none;
    }
    
    /* Ensure React app takes full width */
    .blackcnote-react-app #root {
        width: 100%;
        min-height: 100vh;
    }
    </style>
    <?php
}
add_action('wp_head', 'blackcnote_add_react_styles'); 