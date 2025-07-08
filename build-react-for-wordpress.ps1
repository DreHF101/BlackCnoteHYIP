# BlackCnote React Build and WordPress Integration Script
# Creates seamless integration between React app and WordPress

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "BLACKCNOTE REACT BUILD & WORDPRESS INTEGRATION" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Function to write colored output
function Write-ColorOutput {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success { Write-ColorOutput $args "Green" }
function Write-Warning { Write-ColorOutput $args "Yellow" }
function Write-Error { Write-ColorOutput $args "Red" }
function Write-Info { Write-ColorOutput $args "Cyan" }

# Step 1: Build React app for production
Write-Info "Step 1: Building React app for production..."

# Navigate to React app directory
Set-Location "react-app"

# Install dependencies if needed
if (!(Test-Path "node_modules")) {
    Write-Info "Installing React app dependencies..."
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Failed to install React app dependencies"
        exit 1
    }
}

# Build React app for production
Write-Info "Building React app..."
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Error "Failed to build React app"
    exit 1
}

Write-Success "React app built successfully!"

# Step 2: Copy built files to WordPress theme
Write-Info "Step 2: Copying built files to WordPress theme..."

# Create dist directory in WordPress theme if it doesn't exist
$themeDistPath = "../blackcnote/wp-content/themes/blackcnote/dist"
if (!(Test-Path $themeDistPath)) {
    New-Item -ItemType Directory -Path $themeDistPath -Force | Out-Null
}

# Copy all built files to WordPress theme
Write-Info "Copying dist files to WordPress theme..."
Copy-Item -Path "dist/*" -Destination $themeDistPath -Recurse -Force

Write-Success "Built files copied to WordPress theme!"

# Step 3: Update WordPress theme functions.php for seamless integration
Write-Info "Step 3: Updating WordPress theme for seamless React integration..."

# Navigate back to project root
Set-Location ".."

# Create a new functions.php with proper React integration
$functionsContent = @'
<?php
/**
 * BlackCnote Theme Functions
 * Seamless React App Integration
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('BLACKCNOTE_THEME_VERSION', '1.0.0');
define('BLACKCNOTE_THEME_DIR', get_template_directory());
define('BLACKCNOTE_THEME_URI', get_template_directory_uri());

/**
 * Theme setup
 */
function blackcnote_setup(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ]);
    add_theme_support('custom-logo', [
        'height' => 250,
        'width' => 250,
        'flex-width' => true,
        'flex-height' => true,
    ]);
}
add_action('after_setup_theme', 'blackcnote_setup');

/**
 * Enqueue React app assets with seamless integration
 */
function blackcnote_enqueue_react_app(): void {
    if (is_admin()) {
        return; // Don't load on admin pages
    }

    $dist_path = BLACKCNOTE_THEME_DIR . '/dist';
    $dist_uri = BLACKCNOTE_THEME_URI . '/dist';

    // Check if React app is built
    if (!file_exists($dist_path . '/index.html')) {
        error_log('BlackCnote: React app not built. Run npm run build in react-app directory.');
        return;
    }

    // Enqueue React CSS
    $css_files = glob($dist_path . '/assets/*.css');
    foreach ($css_files as $css_file) {
        $filename = basename($css_file);
        wp_enqueue_style(
            'blackcnote-react-' . md5($filename),
            $dist_uri . '/assets/' . $filename,
            [],
            BLACKCNOTE_THEME_VERSION
        );
    }

    // Enqueue React JS
    $js_files = glob($dist_path . '/assets/*.js');
    $main_js_handle = null;
    
    foreach ($js_files as $js_file) {
        $filename = basename($js_file);
        $handle = 'blackcnote-react-' . md5($filename);
        
        wp_enqueue_script(
            $handle,
            $dist_uri . '/assets/' . $filename,
            ['jquery'],
            BLACKCNOTE_THEME_VERSION,
            true
        );
        
        // Track main JS file for config injection
        if (strpos($filename, 'main') !== false || strpos($filename, 'index') !== false) {
            $main_js_handle = $handle;
        }
    }

    // Inject WordPress configuration for React app
    if ($main_js_handle) {
        $user = wp_get_current_user();
        $config = [
            'homeUrl' => home_url(),
            'isDevelopment' => false,
            'apiUrl' => home_url('/wp-json/blackcnote/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'isLoggedIn' => is_user_logged_in(),
            'userId' => is_user_logged_in() ? $user->ID : 0,
            'baseUrl' => home_url(),
            'themeUrl' => BLACKCNOTE_THEME_URI,
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'environment' => 'production',
            'themeActive' => true,
            'pluginActive' => function_exists('hyiplab_system_instance'),
            'wpHeaderFooterDisabled' => false,
        ];

        wp_add_inline_script(
            $main_js_handle,
            'window.blackCnoteApiSettings = ' . wp_json_encode($config) . ';',
            'before'
        );
    }

    error_log('BlackCnote: React app loaded from dist directory');
}
add_action('wp_enqueue_scripts', 'blackcnote_enqueue_react_app', 100);

/**
 * Custom shortcode for investment plans
 */
function blackcnote_investment_plans_shortcode($atts): string {
    $atts = shortcode_atts([
        'limit' => 10,
        'featured' => false
    ], $atts);

    global $wpdb;
    
    $where_clause = "status = 1";
    if ($atts['featured']) {
        $where_clause .= " AND featured = 1";
    }
    
    $plans = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}hyiplab_plans WHERE {$where_clause} ORDER BY id ASC LIMIT %d",
        $atts['limit']
    ));

    if (empty($plans)) {
        return '<p>No investment plans available.</p>';
    }

    $output = '<div class="investment-plans">';
    foreach ($plans as $plan) {
        $output .= sprintf(
            '<div class="plan">
                <h3>%s</h3>
                <p>Min: $%s | Max: $%s</p>
                <p>Return: %s%% | Duration: %s days</p>
            </div>',
            esc_html($plan->name),
            number_format($plan->min_investment, 2),
            number_format($plan->max_investment, 2),
            $plan->return_rate,
            $plan->duration_days
        );
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('investment_plans', 'blackcnote_investment_plans_shortcode');

/**
 * Load theme includes
 */
require_once BLACKCNOTE_THEME_DIR . '/inc/blackcnote-react-loader.php';
require_once BLACKCNOTE_THEME_DIR . '/inc/menu-registration.php';
require_once BLACKCNOTE_THEME_DIR . '/inc/full-content-checker.php';
?>
'@

# Write the new functions.php
$functionsPath = "blackcnote/wp-content/themes/blackcnote/functions.php"
$functionsContent | Out-File -FilePath $functionsPath -Encoding UTF8

Write-Success "WordPress theme functions.php updated!"

# Step 4: Create React loader include file
Write-Info "Step 4: Creating React loader include file..."

$reactLoaderContent = @'
<?php
/**
 * BlackCnote React App Loader
 * Handles React app initialization and WordPress integration
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize React app when DOM is ready
 */
function blackcnote_init_react_app(): void {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if React app container exists
        const rootElement = document.getElementById('root');
        if (rootElement) {
            // Remove loading message
            const loadingElement = rootElement.querySelector('.react-loading');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
            
            // Initialize React app
            if (window.blackCnoteApiSettings) {
                console.log('BlackCnote: Initializing React app with WordPress integration');
            } else {
                console.error('BlackCnote: API settings not found');
            }
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
        </div>
    </div>
    <?php
}

/**
 * Add React app styles
 */
function blackcnote_add_react_styles(): void {
    ?>
    <style>
    .blackcnote-react-app {
        min-height: 100vh;
        width: 100%;
    }
    
    .react-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .loading-spinner {
        margin-bottom: 20px;
    }
    
    .loading-text {
        font-size: 18px;
        font-weight: 500;
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
    </style>
    <?php
}
add_action('wp_head', 'blackcnote_add_react_styles');
?>
'@

# Create the React loader include file
$incDir = "blackcnote/wp-content/themes/blackcnote/inc"
if (!(Test-Path $incDir)) {
    New-Item -ItemType Directory -Path $incDir -Force | Out-Null
}

$reactLoaderPath = "$incDir/blackcnote-react-loader.php"
$reactLoaderContent | Out-File -FilePath $reactLoaderPath -Encoding UTF8

Write-Success "React loader include file created!"

# Step 5: Update WordPress templates to use React container
Write-Info "Step 5: Updating WordPress templates..."

# Update index.php
$indexContent = @'
<?php
/**
 * The main template file - React App Integration
 * 
 * This template serves as a shell for the React app.
 * All content rendering is handled by React components.
 *
 * @package BlackCnote
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- BlackCnote React App Container -->
<?php blackcnote_add_react_container(); ?>

<!-- React App Fallback (if JavaScript is disabled) -->
<noscript>
    <div class="react-fallback">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1>Welcome to BlackCnote</h1>
                    <p>This application requires JavaScript to function properly.</p>
                    <p>Please enable JavaScript in your browser to continue.</p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                        Refresh Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</noscript>

<?php get_footer(); ?>
'@

$indexPath = "blackcnote/wp-content/themes/blackcnote/index.php"
$indexContent | Out-File -FilePath $indexPath -Encoding UTF8

Write-Success "WordPress templates updated!"

# Step 6: Restart WordPress container to apply changes
Write-Info "Step 6: Restarting WordPress container..."

docker restart blackcnote-wordpress
if ($LASTEXITCODE -eq 0) {
    Write-Success "WordPress container restarted successfully!"
} else {
    Write-Warning "Failed to restart WordPress container. Please restart manually."
}

# Step 7: Test the integration
Write-Info "Step 7: Testing integration..."

Start-Sleep -Seconds 5

# Test WordPress frontend
$response = Invoke-WebRequest -Uri "http://localhost:8888" -UseBasicParsing -TimeoutSec 10
if ($response.StatusCode -eq 200) {
    Write-Success "WordPress frontend is accessible!"
    
    # Check if React app is loaded
    if ($response.Content -match "blackcnote-react-app") {
        Write-Success "React app container found in WordPress frontend!"
    } else {
        Write-Warning "React app container not found in WordPress frontend"
    }
} else {
    Write-Error "WordPress frontend not accessible"
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "INTEGRATION COMPLETED!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ React app built for production" -ForegroundColor Green
Write-Host "✅ Files copied to WordPress theme" -ForegroundColor Green
Write-Host "✅ WordPress theme updated" -ForegroundColor Green
Write-Host "✅ Seamless integration configured" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Access your seamless BlackCnote application:" -ForegroundColor Cyan
Write-Host "   WordPress Frontend: http://localhost:8888" -ForegroundColor White
Write-Host "   WordPress Admin: http://localhost:8888/wp-admin/" -ForegroundColor White
Write-Host ""
Write-Host "The React app is now seamlessly integrated with WordPress!" -ForegroundColor Green
Write-Host "No more separate servers or loading issues." -ForegroundColor Green 