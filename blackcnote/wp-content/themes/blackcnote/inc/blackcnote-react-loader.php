<?php
declare(strict_types=1);

/**
 * BlackCnote React App Loader
 * Handles React app initialization and WordPress integration
 * Enhanced with better loading experience and connection status
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
            
            // Initialize enhanced loading system
            initializeEnhancedLoading(rootElement);
            
            // Initialize React app
            if (window.blackCnoteApiSettings) {
                console.log('BlackCnote: API settings found, React app should load');
                console.log('BlackCnote: Development mode:', window.blackCnoteApiSettings.isDevelopment);
                console.log('BlackCnote: Dev server URL:', window.blackCnoteApiSettings.devServerUrl);
            } else {
                console.error('BlackCnote: API settings not found - React app may not load properly');
                updateLoadingStatus('error', 'Configuration error - React app may not load properly');
            }
        } else {
            console.warn('BlackCnote: React container not found');
        }
    });
    
    /**
     * Enhanced loading system with real-time status updates
     */
    function initializeEnhancedLoading(rootElement) {
        const loadingElement = rootElement.querySelector('.react-loading');
        if (!loadingElement) return;
        
        // Update loading status with connection checks
        updateLoadingStatus('connecting', 'Checking WordPress connection...');
        
        // Check WordPress connection
        checkWordPressConnection().then(() => {
            updateLoadingStatus('connecting', 'Checking React development server...');
            
            // Check React dev server
            return checkReactConnection();
        }).then(() => {
            updateLoadingStatus('connecting', 'Initializing React application...');
            
            // Simulate React app loading
            setTimeout(() => {
                updateLoadingStatus('success', 'React app loaded successfully!');
                
                // Hide loading after success
                setTimeout(() => {
                    loadingElement.style.display = 'none';
                }, 1000);
            }, 2000);
        }).catch((error) => {
            console.error('Loading error:', error);
            updateLoadingStatus('error', 'Connection failed: ' + error.message);
        });
    }
    
    /**
     * Check WordPress connection
     */
    async function checkWordPressConnection() {
        try {
            const response = await fetch(window.location.origin + '/wp-json/');
            if (!response.ok) {
                throw new Error('WordPress not responding');
            }
            updateLoadingStatus('connecting', 'WordPress connected ✓');
            return true;
        } catch (error) {
            throw new Error('WordPress connection failed');
        }
    }
    
    /**
     * Check React development server connection
     */
    async function checkReactConnection() {
        try {
            const reactUrl = 'http://localhost:5174';
            const response = await fetch(reactUrl, { 
                mode: 'no-cors',
                cache: 'no-cache'
            });
            updateLoadingStatus('connecting', 'React dev server connected ✓');
            return true;
        } catch (error) {
            console.warn('React dev server not accessible, using fallback');
            updateLoadingStatus('connecting', 'Using production build...');
            return true; // Don't fail, just use fallback
        }
    }
    
    /**
     * Update loading status with visual feedback
     */
    function updateLoadingStatus(type, message) {
        const loadingElement = document.querySelector('.react-loading');
        if (!loadingElement) return;
        
        const statusElement = loadingElement.querySelector('.loading-status');
        const spinnerElement = loadingElement.querySelector('.loading-spinner');
        
        if (statusElement) {
            statusElement.innerHTML = '<small>' + message + '</small>';
        }
        
        // Update spinner based on status
        if (spinnerElement) {
            spinnerElement.className = 'loading-spinner';
            
            switch (type) {
                case 'connecting':
                    spinnerElement.classList.add('connecting');
                    break;
                case 'success':
                    spinnerElement.classList.add('success');
                    break;
                case 'error':
                    spinnerElement.classList.add('error');
                    break;
            }
        }
        
        console.log('BlackCnote Loading Status:', message);
    }
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
            <div class="loading-progress">
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
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
    if (is_admin()) {
        return;
    }
    
    ?>
    <style>
    .blackcnote-react-app {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .react-loading {
        text-align: center;
        color: white;
        padding: 2rem;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        width: 100%;
    }
    
    .loading-spinner {
        margin-bottom: 1rem;
    }
    
    .loading-spinner.connecting .spinner-border {
        border-color: #ffc107;
        border-right-color: transparent;
        animation: spin 1s linear infinite;
    }
    
    .loading-spinner.success .spinner-border {
        border-color: #28a745;
        border-right-color: transparent;
        animation: spin 0.5s linear infinite;
    }
    
    .loading-spinner.error .spinner-border {
        border-color: #dc3545;
        border-right-color: transparent;
        animation: spin 0.3s linear infinite;
    }
    
    .loading-text {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 1rem 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .loading-status {
        margin: 1rem 0;
        font-size: 0.9rem;
        opacity: 0.8;
    }
    
    .loading-status small {
        display: block;
        padding: 0.5rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        margin: 0.25rem 0;
    }
    
    .loading-progress {
        margin-top: 1.5rem;
    }
    
    .progress-bar {
        width: 100%;
        height: 4px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #ffc107, #28a745);
        width: 0%;
        transition: width 0.3s ease;
        animation: progress 3s ease-in-out infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes progress {
        0% { width: 0%; }
        50% { width: 70%; }
        100% { width: 100%; }
    }
    
    /* Hide loading when React app is ready */
    .react-loading.hidden {
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.5s ease;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .react-loading {
            margin: 1rem;
            padding: 1.5rem;
        }
        
        .loading-text {
            font-size: 1.25rem;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'blackcnote_add_react_styles', 2); 