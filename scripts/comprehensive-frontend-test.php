<?php
/**
 * Comprehensive Frontend Features Test
 * Tests all frontend components, features, and functionality
 * 
 * @package BlackCnote
 * @version 2.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    require_once('blackcnote/wp-config.php');
    require_once('blackcnote/wp-load.php');
}

echo "=== BLACKCNOTE COMPREHENSIVE FRONTEND FEATURES TEST ===\n\n";

// Test 1: Core Template Files
echo "1. TESTING CORE TEMPLATE FILES:\n";
$core_templates = [
    'header.php',
    'footer.php', 
    'index.php',
    'front-page.php',
    'page.php',
    'style.css',
    'functions.php'
];

$missing_templates = [];
foreach ($core_templates as $template) {
    $template_path = get_template_directory() . '/' . $template;
    if (file_exists($template_path)) {
        echo "✅ {$template} - EXISTS\n";
    } else {
        echo "❌ {$template} - MISSING\n";
        $missing_templates[] = $template;
    }
}

if (empty($missing_templates)) {
    echo "✅ All core template files present\n";
} else {
    echo "❌ Missing core templates: " . implode(', ', $missing_templates) . "\n";
}

// Test 2: Template Parts
echo "\n2. TESTING TEMPLATE PARTS:\n";
$template_parts = [
    'home-hero.php',
    'home-features.php', 
    'home-plans.php',
    'home-stats.php',
    'home-cta.php',
    'dashboard.php',
    'plans.php',
    'transactions.php',
    'transactions-table.php'
];

$missing_parts = [];
foreach ($template_parts as $part) {
    $part_path = get_template_directory() . '/template-parts/' . $part;
    if (file_exists($part_path)) {
        echo "✅ {$part} - EXISTS\n";
    } else {
        echo "❌ {$part} - MISSING\n";
        $missing_parts[] = $part;
    }
}

if (empty($missing_parts)) {
    echo "✅ All template parts present\n";
} else {
    echo "❌ Missing template parts: " . implode(', ', $missing_parts) . "\n";
}

// Test 3: CSS Files
echo "\n3. TESTING CSS FILES:\n";
$css_files = [
    'style.css',
    'assets/css/blackcnote-theme.css',
    'assets/css/hyip-theme.css',
    'inc/backend-settings.css',
    'css/widgets.css'
];

$missing_css = [];
foreach ($css_files as $css) {
    $css_path = get_template_directory() . '/' . $css;
    if (file_exists($css_path)) {
        $content = file_get_contents($css_path);
        $size = strlen($content);
        echo "✅ {$css} - EXISTS ({$size} bytes)\n";
    } else {
        echo "❌ {$css} - MISSING\n";
        $missing_css[] = $css;
    }
}

if (empty($missing_css)) {
    echo "✅ All CSS files present\n";
} else {
    echo "❌ Missing CSS files: " . implode(', ', $missing_css) . "\n";
}

// Test 4: JavaScript Files
echo "\n4. TESTING JAVASCRIPT FILES:\n";
$js_files = [
    'assets/js/blackcnote-theme.js',
    'assets/js/hyip-theme.js',
    'inc/backend-settings.js',
    'admin/admin.js'
];

$missing_js = [];
foreach ($js_files as $js) {
    $js_path = get_template_directory() . '/' . $js;
    if (file_exists($js_path)) {
        $content = file_get_contents($js_path);
        $size = strlen($content);
        echo "✅ {$js} - EXISTS ({$size} bytes)\n";
    } else {
        echo "❌ {$js} - MISSING\n";
        $missing_js[] = $js;
    }
}

if (empty($missing_js)) {
    echo "✅ All JavaScript files present\n";
} else {
    echo "❌ Missing JavaScript files: " . implode(', ', $missing_js) . "\n";
}

// Test 5: Page Templates
echo "\n5. TESTING PAGE TEMPLATES:\n";
$page_templates = [
    'page-dashboard.php',
    'page-plans.php',
    'page-about.php',
    'page-contact.php',
    'page-services.php',
    'page-terms.php',
    'page-privacy.php',
    'page-home.php',
    'template-blackcnote-dashboard.php',
    'template-blackcnote-plans.php',
    'template-blackcnote-transactions.php'
];

$missing_pages = [];
foreach ($page_templates as $page) {
    $page_path = get_template_directory() . '/' . $page;
    if (file_exists($page_path)) {
        echo "✅ {$page} - EXISTS\n";
    } else {
        echo "❌ {$page} - MISSING\n";
        $missing_pages[] = $page;
    }
}

if (empty($missing_pages)) {
    echo "✅ All page templates present\n";
} else {
    echo "❌ Missing page templates: " . implode(', ', $missing_pages) . "\n";
}

// Test 6: Frontend Features in CSS
echo "\n6. TESTING FRONTEND FEATURES IN CSS:\n";
$main_css = get_template_directory() . '/assets/css/blackcnote-theme.css';
if (file_exists($main_css)) {
    $css_content = file_get_contents($main_css);
    
    $features = [
        'responsive design' => ['@media', 'max-width', 'min-width'],
        'buttons' => ['.btn', 'btn-primary', 'btn-secondary'],
        'forms' => ['.form-control', '.form-group', 'input'],
        'cards' => ['.card', 'card-header', 'card-body'],
        'dashboard' => ['.dashboard', 'portfolio-stats', 'stat-card'],
        'investment plans' => ['.plans-grid', '.plan-card', '.plan-features'],
        'activity list' => ['.activity-list', '.activity-item'],
        'animations' => ['transition', 'transform', 'hover'],
        'mobile menu' => ['.navbar', '.navbar-toggler', '.navbar-collapse'],
        'typography' => ['font-family', 'font-size', 'line-height']
    ];
    
    foreach ($features as $feature => $selectors) {
        $found = 0;
        foreach ($selectors as $selector) {
            if (strpos($css_content, $selector) !== false) {
                $found++;
            }
        }
        if ($found > 0) {
            echo "✅ {$feature} - FOUND ({$found} selectors)\n";
        } else {
            echo "❌ {$feature} - NOT FOUND\n";
        }
    }
}

// Test 7: JavaScript Functionality
echo "\n7. TESTING JAVASCRIPT FUNCTIONALITY:\n";
$main_js = get_template_directory() . '/assets/js/blackcnote-theme.js';
if (file_exists($main_js)) {
    $js_content = file_get_contents($main_js);
    
    $js_features = [
        'investment calculator' => ['calculator', 'calculate', 'investment-amount'],
        'portfolio management' => ['portfolio', 'loadPortfolioData'],
        'investment plans' => ['investmentPlans', 'selectPlan'],
        'transactions' => ['transactions', 'loadTransactions'],
        'mobile menu' => ['mobileMenu', 'toggle'],
        'form validation' => ['formValidation', 'validate'],
        'smooth scrolling' => ['smoothScroll', 'scrollTo'],
        'live editing' => ['liveEditing', 'init'],
        'utility functions' => ['utils', 'formatCurrency', 'showMessage']
    ];
    
    foreach ($js_features as $feature => $functions) {
        $found = 0;
        foreach ($functions as $func) {
            if (strpos($js_content, $func) !== false) {
                $found++;
            }
        }
        if ($found > 0) {
            echo "✅ {$feature} - FOUND ({$found} functions)\n";
        } else {
            echo "❌ {$feature} - NOT FOUND\n";
        }
    }
}

// Test 8: Bootstrap Integration
echo "\n8. TESTING BOOTSTRAP INTEGRATION:\n";
$functions_content = file_get_contents(get_template_directory() . '/functions.php');
if (strpos($functions_content, 'bootstrap') !== false) {
    echo "✅ Bootstrap CSS enqueued\n";
} else {
    echo "❌ Bootstrap CSS not found\n";
}

if (strpos($functions_content, 'bootstrap.bundle.min.js') !== false) {
    echo "✅ Bootstrap JS enqueued\n";
} else {
    echo "❌ Bootstrap JS not found\n";
}

// Test 9: Responsive Design
echo "\n9. TESTING RESPONSIVE DESIGN:\n";
$responsive_selectors = [
    '@media (max-width: 768px)',
    '@media (max-width: 480px)',
    'container',
    'row',
    'col-',
    'd-flex',
    'd-none',
    'd-block'
];

$responsive_found = 0;
foreach ($responsive_selectors as $selector) {
    if (strpos($css_content, $selector) !== false) {
        $responsive_found++;
    }
}

if ($responsive_found >= 5) {
    echo "✅ Responsive design implemented ({$responsive_found} selectors)\n";
} else {
    echo "❌ Responsive design incomplete ({$responsive_found} selectors)\n";
}

// Test 10: Interactive Features
echo "\n10. TESTING INTERACTIVE FEATURES:\n";
$interactive_features = [
    'hover effects' => ['hover', ':hover'],
    'transitions' => ['transition', 'ease'],
    'animations' => ['animation', 'transform'],
    'click handlers' => ['click', 'onclick'],
    'form submission' => ['submit', 'preventDefault'],
    'AJAX calls' => ['ajax', '$.ajax'],
    'DOM manipulation' => ['document.getElementById', 'querySelector']
];

$interactive_found = 0;
foreach ($interactive_features as $feature => $selectors) {
    $found = 0;
    foreach ($selectors as $selector) {
        if (strpos($js_content, $selector) !== false) {
            $found++;
        }
    }
    if ($found > 0) {
        echo "✅ {$feature} - FOUND\n";
        $interactive_found++;
    } else {
        echo "❌ {$feature} - NOT FOUND\n";
    }
}

// Test 11: Investment Platform Features
echo "\n11. TESTING INVESTMENT PLATFORM FEATURES:\n";
$investment_features = [
    'investment calculator' => ['calculator', 'calculate', 'investment-amount'],
    'portfolio dashboard' => ['dashboard', 'portfolio', 'stat-card'],
    'investment plans' => ['plans', 'plan-card', 'plan-features'],
    'transactions' => ['transactions', 'transaction', 'activity-item'],
    'profit calculation' => ['profit', 'return', 'roi'],
    'user authentication' => ['login', 'register', 'user'],
    'payment processing' => ['payment', 'withdrawal', 'deposit']
];

$investment_found = 0;
foreach ($investment_features as $feature => $selectors) {
    $found = 0;
    foreach ($selectors as $selector) {
        if (strpos($css_content . $js_content, $selector) !== false) {
            $found++;
        }
    }
    if ($found > 0) {
        echo "✅ {$feature} - FOUND\n";
        $investment_found++;
    } else {
        echo "❌ {$feature} - NOT FOUND\n";
    }
}

// Test 12: Accessibility Features
echo "\n12. TESTING ACCESSIBILITY FEATURES:\n";
$accessibility_features = [
    'screen reader text' => ['screen-reader-text', 'sr-only'],
    'skip links' => ['skip-link', 'skip to content'],
    'alt text' => ['alt=', 'alt text'],
    'ARIA labels' => ['aria-label', 'aria-labelledby'],
    'focus states' => [':focus', 'focus'],
    'semantic HTML' => ['<nav>', '<main>', '<section>', '<article>']
];

$accessibility_found = 0;
foreach ($accessibility_features as $feature => $selectors) {
    $found = 0;
    foreach ($selectors as $selector) {
        if (strpos($css_content . $js_content, $selector) !== false) {
            $found++;
        }
    }
    if ($found > 0) {
        echo "✅ {$feature} - FOUND\n";
        $accessibility_found++;
    } else {
        echo "❌ {$feature} - NOT FOUND\n";
    }
}

// Test 13: Performance Features
echo "\n13. TESTING PERFORMANCE FEATURES:\n";
$performance_features = [
    'CSS optimization' => ['minified', '.min.css'],
    'JS optimization' => ['minified', '.min.js'],
    'image optimization' => ['img-fluid', 'lazy-load'],
    'caching' => ['cache', 'transient'],
    'compression' => ['gzip', 'deflate']
];

$performance_found = 0;
foreach ($performance_features as $feature => $selectors) {
    $found = 0;
    foreach ($selectors as $selector) {
        if (strpos($css_content . $js_content, $selector) !== false) {
            $found++;
        }
    }
    if ($found > 0) {
        echo "✅ {$feature} - FOUND\n";
        $performance_found++;
    } else {
        echo "❌ {$feature} - NOT FOUND\n";
    }
}

// Test 14: Security Features
echo "\n14. TESTING SECURITY FEATURES:\n";
$security_features = [
    'nonce verification' => ['nonce', 'wp_verify_nonce'],
    'data sanitization' => ['sanitize', 'esc_html', 'esc_attr'],
    'SQL injection prevention' => ['prepare', '$wpdb->prepare'],
    'XSS prevention' => ['wp_kses', 'esc_html'],
    'CSRF protection' => ['nonce', 'csrf']
];

$security_found = 0;
foreach ($security_features as $feature => $selectors) {
    $found = 0;
    foreach ($selectors as $selector) {
        if (strpos($functions_content, $selector) !== false) {
            $found++;
        }
    }
    if ($found > 0) {
        echo "✅ {$feature} - FOUND\n";
        $security_found++;
    } else {
        echo "❌ {$feature} - NOT FOUND\n";
    }
}

// Test 15: WordPress Integration
echo "\n15. TESTING WORDPRESS INTEGRATION:\n";
$wp_features = [
    'theme setup' => ['add_theme_support', 'after_setup_theme'],
    'enqueue scripts' => ['wp_enqueue_script', 'wp_enqueue_style'],
    'navigation menus' => ['register_nav_menus', 'wp_nav_menu'],
    'widgets' => ['register_sidebar', 'dynamic_sidebar'],
    'customizer' => ['add_theme_mod', 'get_theme_mod'],
    'template hierarchy' => ['get_template_part', 'get_header', 'get_footer']
];

$wp_found = 0;
foreach ($wp_features as $feature => $functions) {
    $found = 0;
    foreach ($functions as $func) {
        if (strpos($functions_content, $func) !== false) {
            $found++;
        }
    }
    if ($found > 0) {
        echo "✅ {$feature} - FOUND\n";
        $wp_found++;
    } else {
        echo "❌ {$feature} - NOT FOUND\n";
    }
}

// Summary
echo "\n=== FRONTEND FEATURES SUMMARY ===\n";
$total_tests = 15;
$passed_tests = 0;

if (empty($missing_templates)) $passed_tests++;
if (empty($missing_parts)) $passed_tests++;
if (empty($missing_css)) $passed_tests++;
if (empty($missing_js)) $passed_tests++;
if (empty($missing_pages)) $passed_tests++;
if ($responsive_found >= 5) $passed_tests++;
if ($interactive_found >= 5) $passed_tests++;
if ($investment_found >= 5) $passed_tests++;
if ($accessibility_found >= 3) $passed_tests++;
if ($performance_found >= 2) $passed_tests++;
if ($security_found >= 3) $passed_tests++;
if ($wp_found >= 5) $passed_tests++;

echo "Tests Passed: {$passed_tests}/{$total_tests}\n";
$percentage = round(($passed_tests / $total_tests) * 100, 1);
echo "Success Rate: {$percentage}%\n";

if ($percentage >= 90) {
    echo "🎉 EXCELLENT: Frontend features are comprehensive and ready!\n";
} elseif ($percentage >= 75) {
    echo "✅ GOOD: Frontend features are mostly complete!\n";
} elseif ($percentage >= 60) {
    echo "⚠️  FAIR: Frontend features need some improvements!\n";
} else {
    echo "❌ POOR: Frontend features need significant work!\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
if (!empty($missing_templates)) {
    echo "- Create missing core template files\n";
}
if (!empty($missing_parts)) {
    echo "- Create missing template parts\n";
}
if ($responsive_found < 5) {
    echo "- Enhance responsive design implementation\n";
}
if ($interactive_found < 5) {
    echo "- Add more interactive JavaScript features\n";
}
if ($accessibility_found < 3) {
    echo "- Improve accessibility features\n";
}
if ($security_found < 3) {
    echo "- Enhance security measures\n";
}

echo "\n=== FRONTEND FEATURES TEST COMPLETE ===\n";
?> 